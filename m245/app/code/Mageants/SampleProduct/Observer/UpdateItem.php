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

class UpdateItem implements ObserverInterface
{
    const MAX_QTY = 'sample_section/sample_general/max_qty';
    /**
     * @var RequestInterface
     */
    protected $_request;
     
    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_productloader = $_productloader;
        $this->messageManager = $messageManager;
    }
 
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request=$this->_request->getPost();
        if (isset($request['sampleorder'])) {
            $quote = $this->_checkoutSession->getQuote();
            $max_qty = $this->_scopeConfig->getValue(self::MAX_QTY, ScopeInterface::SCOPE_STORE);
            $customprice=$observer->getItem();
            $curr_qty=$customprice->getQty();
            $total_samp_qty = 0;
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objectManager->create('\Magento\Catalog\Helper\Product\Configuration');
            if ($quote->getItems()) {
                foreach ($quote->getItems() as $loop_item) {
                    $options=$helper->getCustomOptions($loop_item);
                    foreach ($options as $option) {
                        if ($option['label']=="Sample Order" && isset($loop_request['sampleorder'])) {
                            $total_samp_qty +=$loop_item->getQty();
                        }
                    }
                }
            }
                
            if (($total_samp_qty+$curr_qty) <= $max_qty) {
                $additionalOptions[] = [
                    'label' => __("Sample Order"),
                    'value' => ' - '.__("Yes"),
                ];
                $price =$this->_request->getParam('sample-price');
                $customprice->setCustomPrice($price);
                $customprice->setOriginalCustomPrice($price);
                $product=$customprice->getProduct();
                $product->addCustomOption('additional_options', serialize($additionalOptions));
                $options_set=$product->getCustomOption("additional_options");
                $customprice->addOption($options_set);
                $customprice->saveItemOptions();
                $customprice->save();
            } else {
                $this->messageManager->addNoticeMessage("You have crossed max Limit of sample Order.");
            }
        }
    }
}
