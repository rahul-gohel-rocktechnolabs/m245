<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\Order\Pdf;

class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{

    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    protected $attributeValue;

    /**
     * @var \Mageants\Orderattribute\Helper\Config
     */
    protected $config;

    /**
     * Invoice constructor.
     *
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
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Mageants\Orderattribute\Model\Order\Attribute\Value $attributeValue
     * @param \Mageants\Orderattribute\Helper\Config $config
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
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Mageants\Orderattribute\Model\Order\Attribute\Value $attributeValue,
        \Mageants\Orderattribute\Helper\Config $config,
        array $data = []
    ) {
        $this->attributeValue = $attributeValue;
        $this->_localeResolver = $localeResolver;
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
            $appEmulation,
            $data
        );
        $this->config = $config;
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
        /*
         * create pdf and styles
         */
        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            $page = $this->_createPage($invoice);
            $order = $invoice->getOrder();
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
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
     * Create Page
     *
     * @param array|Collection $invoice
     * @return object
     */
    protected function _createPage($invoice)
    {

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

        /*add order attributes to the pdf*/
        $orderAttributesList = $this->getOrderAttributeList(
            $order->getId(),
            $order->getStoreId()
        );
        if ($this->config->getPdfInvoice() && count($orderAttributesList) > 0) {
            $this->drawAttributesHeader($page);
            $this->drawAttributesList($page, $orderAttributesList);
        }

        return $page;
    }

    /**
     * Draw attributes header
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function drawAttributesHeader(\Zend_Pdf_Page $page)
    {
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Order Attributes'), 'feed' => 35];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Draw attributes list
     *
     * @param \Zend_Pdf_Page $page
     * @param array $list
     */
    protected function drawAttributesList($page, $list)
    {
        foreach ($list as $label => $value) {
            if (is_array($value)) {
                $page->drawText($label . ': ', 35, $this->y, 'UTF-8');
                foreach ($value as $str) {
                    $this->y -= 15;
                    $page->drawText($str, 50, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            } else {
                $page->drawText($label . ': ', 35, $this->y, 'UTF-8');
                $this->y -= 15;
                $page->drawText($value, 50, $this->y, 'UTF-8');
                $this->y -= 15;
            }
        }

        $this->y -= 10;
    }

    /**
     * Get Order Attribute List
     *
     * @param string $orderId
     * @param string $storeId
     * @return array
     */
    protected function getOrderAttributeList($orderId, $storeId)
    {
        if ($this->getData('attributes_list' . $orderId . '_' . $storeId)) {
            return $this->getData('attributes_list' . $orderId . '_' . $storeId);
        }
        $this->attributeValue->loadByOrderId($orderId);
        $attributesList = $this->attributeValue->getOrderAttributeValuesForPdf($storeId);
        $this->setData('attributes_list' . $orderId . '_' . $storeId, $attributesList);
        return $attributesList;
    }
}
