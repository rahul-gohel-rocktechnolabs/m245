<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Model\order\pdf;

class Shipment extends \Magento\Sales\Model\Order\Pdf\Shipment
{
    /**
     * @var storeManager
     */
    protected $_storeManager;

    /**
     * @var localeResolver
     */
    protected $_localeResolver;

    /**
     * @var deliveryHelper
     */
    public $_deliveryHelper;

    /**
     * @var OrderSaveFactory
     */
    public $OrderSaveFactory;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sales\Model\Order\Pdf\Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Mageants\DeliveryDate\Helper\Data $deliveryhelper
     * @param \Mageants\DeliveryDate\Model\OrderSaveFactory $OrderSaveFactory
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Mageants\DeliveryDate\Helper\Data $deliveryhelper,
        \Mageants\DeliveryDate\Model\OrderSaveFactory $OrderSaveFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_deliveryHelper = $deliveryhelper;
        $this->OrderSaveFactory = $OrderSaveFactory;
        $this->_productRepository = $productRepository;
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
            $appEmulation
        );
    }

    /**
     * Draw table header for product items
     *
     * @param  \Zend_Pdf_Page $page
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
        $lines[0][] = ['text' => __('Products'), 'feed' => 100];

        $lines[0][] = ['text' => __('Qty'), 'feed' => 35];

        $lines[0][] = ['text' => __('SKU'), 'feed' => 565, 'align' => 'right'];

        $lineBlock = ['lines' => $lines, 'height' => 10];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param \Magento\Sales\Model\Order\Shipment[] $shipments
     * @return \Zend_Pdf
     */
    public function getPdf($shipments = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                $this->_localeResolver->emulate($shipment->getStoreId());
                $this->_storeManager->setCurrentStore($shipment->getStoreId());
            }
            $page = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Packing Slip # ') . $shipment->getIncrementId());
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }

        $this->insertConditions($page, $order);

        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            $this->_localeResolver->revert();
        }
        return $pdf;
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

    /**
     * Insert Condition function
     *
     * @param mixed $page
     * @param mixed $order
     * @return mixed
     */
    public function insertConditions($page, $order)
    {
        $displayAt = '';
        $orderRecord = $this->OrderSaveFactory->create();
        $orderCollection = $orderRecord->getCollection();
        $orderIdData = $orderCollection->addFieldToFilter('order_id', $order->getId());
        foreach ($orderIdData as $key => $value) {
            $displayAt = $value['configuration_display_at'];
        }
        $includeinto=$this->_deliveryHelper->getPluginIncludeInto($order->getStoreId());
        if (strpos($includeinto, '6') !== false) {
            $orderData=$order->getData();
            if ($displayAt ==3) {
                $this->_setFontBold($page, 10);
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $this->y, 570, $this->y - 15);
                $this->y -= 10;
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

                $lines[0][] = ['text' => __('Delivery Information'),'feed'=>35];

                $lineBlock = ['lines' => $lines, 'height' => 5];

                $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

                $this->y -= 25;
                $dateDeliveryData = [];
                $deliverydate = json_decode($orderData['delivery_date']);
                $deliverytime = json_decode($orderData['delivery_timeslot']);
                $deliverycomment = json_decode($orderData['delivery_comment']);
                foreach ($deliverydate as $key => $productId) {
                    $product = $this->_productRepository->getById($deliverydate[$key]->item_id);
                    $dateDeliveryData[$key]['productId'] =  $product->getId();
                    $dateDeliveryData[$key]['productName'] =  $product->getName();
                    $dateDeliveryData[$key]['deliverydate'] = $deliverydate[$key]->delivery_date;
                    $dateDeliveryData[$key]['deliverytime'] = $deliverytime[$key]->delivery_timeslot;
                    $dateDeliveryData[$key]['deliverycomment'] = $deliverycomment[$key]->delivery_comment;
                }
                foreach ($dateDeliveryData as $key => $value) {
                    $page->drawText("Product Name", 35, $this->y, 'UTF-8');
                    $page->drawText(": ".$dateDeliveryData[$key]['productName'], 130, $this->y, 'UTF-8');
                    $page->drawText("Delivery Date", 35, $this->y-15, 'UTF-8');
                    $page->drawText(": ".$dateDeliveryData[$key]['deliverydate'], 130, $this->y-15, 'UTF-8');
                    $page->drawText("Delivery Timeslot", 35, $this->y-30, 'UTF-8');
                    $page->drawText(": ".$dateDeliveryData[$key]['deliverytime'], 130, $this->y-30, 'UTF-8');
                    if ($dateDeliveryData[$key]['deliverycomment'] != null) {
                        $page->drawText("Delivery Comment", 35, $this->y-45, 'UTF-8');
                        $page->drawText(": ".$dateDeliveryData[$key]['deliverycomment'], 130, $this->y-45, 'UTF-8');
                    }
                    $this->y -= 70;
                }
            } else {
                $this->_setFontBold($page, 10);
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $this->y, 570, $this->y - 15);
                $this->y -= 10;
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

                $lines[0][] = ['text' => __('Delivery Information'),'feed'=>35];

                $lineBlock = ['lines' => $lines, 'height' => 5];

                $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

                $this->y -= 25;
                $page->drawText("Delivery Date", 35, $this->y, 'UTF-8');
                $page->drawText(": ".$orderData['delivery_date'], 130, $this->y, 'UTF-8');
                $page->drawText("Delivery Timeslot", 35, $this->y-15, 'UTF-8');
                $page->drawText(": ".$orderData['delivery_timeslot'], 130, $this->y-15, 'UTF-8');
                if ($orderData['delivery_comment'] != null) {
                    $page->drawText("Delivery Comment", 35, $this->y-30, 'UTF-8');
                    $page->drawText(": ".$orderData['delivery_comment'], 130, $this->y-30, 'UTF-8');
                }
            }
        }
    }
}
