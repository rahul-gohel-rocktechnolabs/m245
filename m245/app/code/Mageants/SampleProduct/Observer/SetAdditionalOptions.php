<?php
/**
 * @category   Mageants SampleProduct
 * @package    Mageants_SampleProduct
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\SampleProduct\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Session;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use \Magento\Framework\Controller\ResultFactory;

class SetAdditionalOptions implements ObserverInterface
{
    const MAX_QTY = 'sample_section/sample_general/max_qty';
    /**
     * @var RequestInterface
     */
    protected $_request;
    protected $_url;
    protected $_responseFactory;
    private $serializer;
    const TYPE_REDIRECT = 'redirect';
    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        SerializerInterface $serializer,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Action\Context $contextredirect,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->_request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_productloader = $_productloader;
        $this->serializer = $serializer;
        $this->stockRegistry = $stockRegistry;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
         $this->_messageManager = $messageManager;
         $this->_resultFactory = $context->getResultFactory();
         $this->_redirect = $contextredirect->getRedirect();
    }
 
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Check and set information according to your need
        if ($this->_request->getFullActionName() == 'checkout_cart_add' || $this->_request->getFullActionName() == 'checkout_cart_updateItemOptions') {
            if ($this->_request->getParam('isSample') == "1") {
                if ($this->_request->getParam('qty')) {
                    $curr_qty = $this->_request->getParam('qty');
                } else {
                    $curr_qty = 1;
                }
                $item_id = $this->_request->getParam('id');
                $item_qty=0;
                $quote = $this->_checkoutSession->getQuote();
                $order = $observer->getOrder();
                $max_qty = $this->_scopeConfig->getValue(self::MAX_QTY, ScopeInterface::SCOPE_STORE);
    
                $total_samp_qty = 0;
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $helper = $objectManager->create('\Magento\Catalog\Helper\Product\Configuration');
                if ($quote->getItems()) {
                    foreach ($quote->getItems() as $loop_item) {
                        $options=$helper->getCustomOptions($loop_item);
                        foreach ($options as $option) {
                            if ($option['label']=="Sample Order") {
                                if ($loop_item->getId()==$item_id) {
                                    $item_qty=$loop_item->getQty();
                                }
                                $total_samp_qty +=$loop_item->getQty();
                            }
                        }
                    }
                }

                $additionalOptions = [];
                $total_samp_qty += $curr_qty;
                if ($total_samp_qty <= $max_qty && $curr_qty <= $max_qty) {
                    $additionalOptions[] = [
                        'label' => __("Sample Order"),
                        'value' => ' - '.__("Yes"),
                    ];
                    $this->_checkoutSession->setCloseFreeSample(0);
                    $product = $observer->getProduct();
                    $observer->getProduct()->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
                } else {
                    $this->_messageManager->addNotice(__('You can not order more then %1 Sample Product.', $max_qty));
                    $resultRedirect = $this->resultFactory->create(self::TYPE_REDIRECT);
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
            } else {
                $_product = $observer->getProduct();
                if ($_product->getTypeId() == "simple") {
                    $total_qty = 0;
                    $productQty = 0;
                    $curr_qty = $this->_request->getParam('qty');
                    $quote = $this->_checkoutSession->getQuote();
                    $stockItem = $this->stockRegistry->getStockItem($_product->getId());
                    $productQty = $stockItem->getQty();

                    $product = $this->_productloader->create()->load($_product->getId());
                        
                    if (isset($quote) && !empty($quote->getItems())) {
                        foreach ($quote->getItems() as $loop_item) {
                            if ($product->getSku() == $loop_item->getSku()) {
                                $total_qty += $loop_item->getQty();
                            }
                        }
                    }
                    
                    $total_qty += $curr_qty;

                    if ($productQty>0) {
                        if ($total_qty <= $productQty && $curr_qty <= $productQty) {
                        } else {
                            throw new LocalizedException(__('We don\'t have as many "%1" as you requested.', $product->getName()));
                        }
                    }
                }
            }
        }
    }
}
