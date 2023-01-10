<?php

namespace Mageants\DeliveryDate\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Mageants\DeliveryDate\Helper\Data as HelperData;
use Magento\Tax\Helper\Data as TaxHelperData;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\GiftMessage\Helper\Message;

class ViewModel implements ArgumentInterface
{
    /**
     * @var helperData
     */
    private $helperData;
    /**
     * @var taxHelperData
     */
    private $taxHelperData;

    /**
     * @var shippingHelper
     */
    private $shippingHelper;

    /**
     * @var message
     */
    private $message;

    /**
     * @param HelperData $helperData
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param TaxHelperData $taxHelperData
     * @param ShippingHelper $shippingHelper
     * @param Message $message
     */
    public function __construct(
        HelperData $helperData,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        TaxHelperData $taxHelperData,
        ShippingHelper $shippingHelper,
        Message $message
    ) {
        $this->helperData = $helperData;
        $this->urlBuilder = $urlBuilder;
        $this->productRepository = $productRepository;
        $this->taxHelperData = $taxHelperData;
        $this->shippingHelper = $shippingHelper;
        $this->message = $message;
    }
    /**
     * Get Display Position function
     *
     * @param int $orderId
     * @return string
     */
    public function getDisplayPosition($orderId)
    {
        return $this->helperData->getCustomModelData($orderId);
    }

    /**
     * Get Plugin Enable/Disable function
     *
     * @return bool
     */
    public function getPluginEnable()
    {
        return $this->helperData->getPluginEnable();
    }

    /**
     * Return Url function
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->urlBuilder;
    }

    /**
     * Get Product By Id function
     *
     * @return object
     */
    public function productData()
    {
        return  $this->productRepository;
    }

    /**
     * Get Tax Model function
     *
     * @return object
     */
    public function getTaxModel()
    {
        return $this->taxHelperData;
    }

    /**
     * Get shipping Model function
     *
     * @return object
     */
    public function getShippingModel()
    {
        return $this->shippingHelper;
    }

    /**
     * Get Include Into function
     *
     * @param mixed $storeid
     * @return string
     */
    public function getPluginIncludeInto($storeid)
    {
        return $this->helperData->getPluginIncludeInto($storeid);
    }
    
    /**
     * Gift Message function
     *
     * @return object
     */
    public function giftMessage()
    {
        return $this->message;
    }
    
    /**
     * Helper Object function
     *
     * @return void
     */
    public function helperFunction()
    {
        return $this->helperData;
    }
}
