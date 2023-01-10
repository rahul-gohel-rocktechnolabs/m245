<?php
/**
 * @category   Mageants SampleProduct
 * @package    Mageants_SampleProduct
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\SampleProduct\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;

class CustomerLoginAfterCheck implements ObserverInterface
{
    const MAX_QTY = 'sample_section/sample_general/max_qty';
    const ENABLE = 'sample_section/sample_general/sample_enable';
   
    public function __construct(
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url
    ) {
        
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_messageManager = $messageManager;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
    }
 
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $moduleStatus = $this->_scopeConfig->getValue(self::ENABLE, ScopeInterface::SCOPE_STORE);
        if ($moduleStatus==1) {
            $quote = $this->_checkoutSession->getQuote();
            $max_qty = $this->_scopeConfig->getValue(self::MAX_QTY, ScopeInterface::SCOPE_STORE);
            $total_samp_qty = 0;
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objectManager->create('\Magento\Catalog\Helper\Product\Configuration');
            if ($quote->getItems()) {
                foreach ($quote->getItems() as $loop_item) {
                    $options=$helper->getCustomOptions($loop_item);
                    foreach ($options as $option) {
                        if ($option['label']=="Sample Order") {
                            $total_samp_qty +=$loop_item->getQty();
                        }
                    }
                }
            }
            
            if ($total_samp_qty > $max_qty) {
                $cartUrl = $this->_url->getUrl('checkout/cart/index');
                $redirect = $this->_responseFactory->create()->setRedirect($cartUrl)->sendResponse();
                $this->_messageManager->addNotice(__('You can not order more then %1 Sample Product.', $max_qty));
                exit;
            }
        }
    }
}
