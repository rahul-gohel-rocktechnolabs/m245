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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Session;

class CustomPrice implements ObserverInterface
{
    const MAX_QTY = 'sample_section/sample_general/max_qty';
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     * @param \Magento\Framework\App\Request\Http $request
     */
    
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession
    ) {
        $this->_productloader = $_productloader;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
      /*echo "called";
      exit;*/
        $item = $observer->getEvent()->getData('quote_item');
      
        $quote = $item->getQuote();
      
        $max_qty = $this->_scopeConfig->getValue(self::MAX_QTY, ScopeInterface::SCOPE_STORE);
    
        $total_samp_qty = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('\Magento\Catalog\Helper\Product\Configuration');
        if ($quote->getItems()) {
            foreach ($quote->getItems() as $loop_item) {
                $loop_request = $loop_item->getBuyRequest();
        
                if (isset($loop_request['sampleorder'])) {
                    $options=$helper->getCustomOptions($loop_item);
                    foreach ($options as $option) {
                        if ($option['label']=="Sample Order") {
                            $total_samp_qty +=$loop_item->getQty();
                        }
                    }
                  //$total_samp_qty +=$loop_item->getQty();
                }
            }
        }
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        $request=$this->request->getPost();

        if (isset($request['sample-price'])) {
            if ($request['isSample']=="1") {
                if ($this->_checkoutSession->getCloseFreeSample() == 0) {
                    $additionalOptions[] = [
                    'label' => __("Sample Order"),
                    'value' => ' - '.__("Yes"),
                    ];
                    $product_item = $this->_productloader->create()->load($item->getProduct()->getId());
                    if ($product_item->getOfferSampleCost()) {
                        $price =  $product_item->getOfferSampleCost();
                    } else {
                        $price = 0;
                    }
                    $item->setCustomPrice($price);
                    $item->setType('Sample');
                    $item->setOriginalCustomPrice($price);
                    $item->getProduct()->setIsSuperMode(true);
                }
            }
        }
    }
}
