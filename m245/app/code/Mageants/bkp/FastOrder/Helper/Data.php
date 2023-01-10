<?php
/**
 * @category Mageants FastOrder
 * @package Mageants_FastOrder
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FastOrder\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var  \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
     /**
      * @var  \Magento\Framework\Json\EncoderInterface
      */
    protected $jsonEncoder;
    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $localeFormat;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;
    /**
     * @var  \Magento\Framework\DataObject
     */
    protected $responseObject;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Framework\DataObject $responseObject
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\DataObject $responseObject
    ) {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $context->getScopeConfig();
        $this->localeFormat = $localeFormat;
        $this->imageHelper = $imageHelper;
        $this->priceCurrency = $priceCurrency;
        $this->eventManager = $context->getEventManager();
        $this->jsonEncoder = $jsonEncoder;
        $this->customerSession = $customerSession;
        $this->responseObject = $responseObject;
    }
    
    /**
     * Get Store Config Value
     *
     * @param int|null $configPath
     * @return string
     */
    public function getFastOrderConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get Store
     *
     * @return int|string
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }
    /**
     * Get Fomat Price
     *
     * @return int|string
     */
    public function getFomatPrice()
    {
        $config = $this->localeFormat->getPriceFormat();
        return $this->jsonEncoder->encode($config);
    }
    /**
     * Get Json Config Price
     *
     * @param int|null $product
     * @return int|string
     */

    public function getJsonConfigPrice($product)
    {
        if (!$product->hasOptions()) {
            $config = [
                'productId' => $product->getId(),
                'priceFormat' => $this->localeFormat->getPriceFormat()
                ];
            return $this->jsonEncoder->encode($config);
        }

        $tierPrices = [];
        $tierPricesList = $product->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPrices[] = $this->priceCurrency->convert($tierPrice['price']->getValue());
        }
        $config = [
            'productId' => $product->getId(),
            'priceFormat' => $this->localeFormat->getPriceFormat(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->priceCurrency->convert(
                        $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue()
                    ),
                    'adjustments' => []
                ],
                'basePrice' => [
                    'amount' => $this->priceCurrency->convert(
                        $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount()
                    ),
                    'adjustments' => []
                ],
                'finalPrice' => [
                    'amount' => $this->priceCurrency->convert(
                        $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue()
                    ),
                    'adjustments' => []
                ]
            ],
            'idSuffix' => '_clone',
            'tierPrices' => $tierPrices
        ];

        $this->eventManager->dispatch('catalog_product_view_config', ['response_object' => $this->responseObject]);
        if (is_array($this->responseObject->getAdditionalOptions())) {
            foreach ($this->responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return $this->jsonEncoder->encode($config);
    }
}
