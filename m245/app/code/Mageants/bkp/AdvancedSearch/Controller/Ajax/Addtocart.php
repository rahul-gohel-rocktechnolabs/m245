<?php

/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\AdvancedSearch\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;

class Addtocart extends \Magento\Framework\App\Action\Action
{

    /**
     * @var Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;
    /**
     * @var Magento\Checkout\Model\Cart
     */
    protected $cart;
    /**
     * @var Magento\Catalog\Model\Product
     */
    protected $product;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productfactory;

    /**
     * Get Document
     *
     * @param Context $context
     * @param FormKey $formKey
     * @param Cart $cart
     * @param Product $product
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Mageants\AdvancedSearch\Helper\Data $helperData
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Catalog\Model\ProductFactory $productfactory
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        Cart $cart,
        Product $product,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Mageants\AdvancedSearch\Helper\Data $helperData,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Catalog\Model\ProductFactory $productfactory
    ) {
            $this->formKey = $formKey;
            $this->cart = $cart;
            $this->product = $product;
            $this->resultJsonFactory = $resultJsonFactory;
            $this->helperData        = $helperData;
            $this->_urlInterface = $urlInterface;
            $this->productfactory = $productfactory;
            parent::__construct($context);
    }
    /**
     * For Add to cart
     */
    public function execute()
    {
       
        $result = $this->resultJsonFactory->create();
        $productId  = $this->getRequest()->getParams();
        $product = $this->product->load($productId);
        $producttype =  $product->getTypeId();
        if ($producttype == "simple") {
            $params = [
                    'form_key' => $this->formKey->getFormKey(),
                    'product' => $productId,
                    'qty'   =>1
                ];
            $product = $this->product->load($productId);
            $abc =  $product->getTypeId();
            $this->cart->addProduct($product, $params);
            $this->cart->save();
            $checkoutpage = $this->helperData->shippingcart();
            $checkout = $this->_urlInterface->getUrl('checkout/cart');
           
            if ($checkoutpage == 1) {
                return $result->setData(['success' => true,'value'=>$checkout]);
            }

        } else {
            //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            //$_product = $objectManager->get(\Magento\Catalog\Model\ProductFactory::class)->create()->load($productId);
            $_product = $this->productfactory->create()->load($productId);
            
              $url =  $_product->getProductUrl();
              return $result->setData(['success' => true,'value'=>$url]);
        }
    }
}
