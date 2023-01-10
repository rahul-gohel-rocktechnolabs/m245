<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageants\ExtraFee\Model\Order\Pdf;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\Payment\Helper\Data;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Sales\Model\Order\Pdf\Total\Factory;
use Magento\Sales\Model\Order\Pdf\ItemsFactory;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use \Magento\Framework\Translate\Inline\StateInterface;
use \Magento\Sales\Model\Order\Address\Renderer;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\Locale\ResolverInterface;
use \Magento\Framework\Pricing\Helper\Data as pricedata;
use Mageants\Extrafee\Helper\Data as helperdata;

class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;
    /**
     * @var helperdata
     */
    protected $_dataHelper;

     /**
      * @var pricedata
      */
    protected $priceHelper;

    /**
     *
     * @param Data $paymentData
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @param Config $pdfConfig
     * @param Factory $pdfTotalFactory
     * @param ItemsFactory $pdfItemsFactory
     * @param TimezoneInterface $localeDate
     * @param StateInterface $inlineTranslation
     * @param Renderer $addressRenderer
     * @param StoreManagerInterface $storeManager
     * @param ResolverInterface $localeResolver
     * @param helperdata $dataHelper
     * @param pricedata $priceHelper
     */
    public function __construct(
        Data $paymentData,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        Config $pdfConfig,
        Factory $pdfTotalFactory,
        ItemsFactory $pdfItemsFactory,
        TimezoneInterface $localeDate,
        StateInterface $inlineTranslation,
        Renderer $addressRenderer,
        StoreManagerInterface $storeManager,
        ResolverInterface $localeResolver,
        helperdata $dataHelper,
        pricedata $priceHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_dataHelper = $dataHelper;
        $this->priceHelper = $priceHelper;
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $storeManager,
            $localeResolver
            // $data
        );
    }

    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Products'), 'feed' => 35];

        $lines[0][] = ['text' => __('SKU'), 'feed' => 290, 'align' => 'right'];

        $lines[0][] = ['text' => __('Qty'), 'feed' => 435, 'align' => 'right'];

        $lines[0][] = ['text' => __('Price'), 'feed' => 360, 'align' => 'right'];

        $lines[0][] = ['text' => __('Tax'), 'feed' => 495, 'align' => 'right'];

        $lines[0][] = ['text' => __('Subtotal'), 'feed' => 565, 'align' => 'right'];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($invoices = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                
                $lineBlock = ['lines' => [], 'height' => 15];
                
                $ordpid=$item->getOrderItem()->getProductId();
                $catpids=[];
                $prdpids=[];
                
                if ($order->getCategoryfeeapplyprdid()) {
                    $catpids = explode(',', $order->getCategoryfeeapplyprdid());
                }
                if ($order->getProductfeeapplyprdid()) {
                    $prdpids = explode(',', $order->getProductfeeapplyprdid());
                }
                if (count($catpids)>0) {
                    if (in_array($ordpid, $catpids)) {
                        if ($order->getCategoryfeelable()) {
                            $lineBlock['lines'][] = [
                                [
                                    'text' => $order->getCategoryfeelable(),
                                    'feed' => 35,
                                    'align' => 'left',
                                    'font_size' => 10,
                                    'font' => 'normal',
                                ],
                            ];
                        }
                    }
                }
                if (count($prdpids)>0) {
                    if (in_array($ordpid, $prdpids)) {
                        if ($order->getProductfeelable()) {
                            $lineBlock['lines'][] = [
                                [
                                    'text' => $order->getProductfeelable(),
                                    'feed' => 35,
                                    'align' => 'left',
                                    'font_size' => 10,
                                    'font' => 'normal',
                                ],
                            ];
                        }
                    }
                }
                
                $page = $this->drawLineBlocks($page, [$lineBlock]);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                $this->_localeResolver->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }
    /**
     * Insert all Extra Fee Total
     *
     * @param object $page
     * @param object $source
     */
    protected function insertTotals($page, $source)
    {
        $order = $source->getOrder();
        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 15];
        $lineBlock['lines'][] = [
            [
                'text' => $this->_dataHelper->getFeeLabel().':',
                'feed' => 475,
                'align' => 'right',
                'font_size' => 10,
                'font' => 'bold',
            ],
            [
                'text' => $this->priceHelper->currency($order->getfee(), true, false),
                'feed' => 565,
                'align' => 'right',
                'font_size' => 10,
                'font' => 'bold'
            ],
        ];
        $extraFeeLen = strlen($order->getExtrafeecomment());
        $this->y -= 20;
        if ($extraFeeLen > 75) {
            $extrafeeText = wordwrap($order->getExtrafeecomment(), 75, "\n");
            $page = $this->drawLineBlocks($page, [$lineBlock]);
            foreach (explode("\n", $extrafeeText) as $textLine) {
                if ($textLine!=='') {
                        $page->drawText(strip_tags(ltrim($textLine)), 150, $this->y, 'UTF-8');
                        $this->y -= 15;
                }
            }
        } else {
            $lineBlock['lines'][] = [
                [
                    'text' => '('.$order->getExtrafeecomment().')',
                    'feed' => 475,
                    'align' => 'right',
                    'font_size' => 10,
                    'font' => 'bold',
                ]
            ];
        }
        foreach ($totals as $total) {
            $total->setOrder($order)->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = [
                        [
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                        ],
                        [
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ],
                    ];
                }
            }
        }
        
        //$this->y -= 20;
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        return $page;
    }
    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return \Zend_Pdf_Page
     */
    public function newPage(array $settings = [])
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(\Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
}
