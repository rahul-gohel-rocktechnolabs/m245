<?php
/**
 * @category Mageants FastOrder
 * @package Mageants_FastOrder
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FastOrder\Controller\Index;

use Magento\Framework\App\Action\Context;

class Option extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;
    
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $_configurableProduct;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
       
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;
    
    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;
    
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var  \Magento\Catalog\Model\Product
     */
    protected $catalogModelProduct;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    
   /**
    * @param Context $context
    * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
    * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct
    * @param \Magento\Catalog\Model\ProductFactory $productloader
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
    * @param \Magento\Framework\Locale\FormatInterface $localeFormat
    * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    * @param \Magento\Catalog\Model\Product $catalogModelProduct
    * @param \Magento\Framework\Registry $registry
    */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Product $catalogModelProduct,
        \Magento\Framework\Registry $registry
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_productloader = $productloader;
        $this->_configurableProduct = $configurableProduct;
        $this->_storeManager = $storeManager;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_localeFormat = $localeFormat;
        $this->priceCurrency = $priceCurrency;
        $this->catalogModelProduct = $catalogModelProduct;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }
    
    /**
     * Return json string
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('productId');
        $sortOrder = $this->getRequest()->getParam('sortOrder');

        $product = $this->_productloader->create()->load($productId);
        $layout = $this->_view->getLayout();

        if ($product->getTypeId()=='configurable') {
            $productAttributes = $this->_configurableProduct->getConfigurableAttributesAsArray($product);
            
            $block1 = $layout->createBlock(\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable::class);
            $block1->setProduct($product);

            $block3 = $layout->createBlock(\Magento\Swatches\Block\Product\Renderer\Configurable::class);
            $block3->setProduct($product);
            
            $block = $layout->createBlock(\Magento\Framework\View\Element\Template::class);
            $block->setProductAttributes($productAttributes);
            $block->setSortOrder($sortOrder);
            $block->setProduct($product);
            $block->setStoreMediaUrl($this->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA));
            $block->setJsonConfig($block1->getJsonConfig());
            $block->setJsonSwatchConfig($block3->getJsonSwatchConfig());
            $block->setTemplate('Mageants_FastOrder::option.phtml');
            $html = $block->toHtml();
        } else {
            $storeId = $this->_storeManager->getStore()->getId();
            $productId = $this->getRequest()->getParam('productId');
            $sortOrder = $this->getRequest()->getParam('sortOrder');
            $product = $this->catalogModelProduct->setStoreId($storeId)->load($productId);
            $resultPage = $this->resultPageFactory->create();
            $this->registry->unregister('current_product');
            $this->registry->register('current_product', $product);
            $html = $resultPage->getLayout()
                    ->createBlock(
                        \Magento\Framework\View\Element\Template::class,
                        '',
                        ['data' => [
                        'sort_order' => $sortOrder
                        ]
                        ]
                    )
                    ->setTemplate('Mageants_FastOrder::option.phtml')
                    ->setProduct($product)
                    ->setStoreMediaUrl($this->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA))
                    ->toHtml();
        }

        $productOption = ['popup_option'=>$html];
        $this->getResponse()->setBody(json_encode($productOption));
    }

    /**
     * Retrieve current store
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }
}
