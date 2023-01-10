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
use \Magento\Framework\Controller\ResultFactory;

class UpdateProduct implements ObserverInterface
{
    const MAX_QTY = 'sample_section/sample_general/max_qty';
    /**
     * @var RequestInterface
     */
    protected $_request;
    const TYPE_REDIRECT = 'redirect';
     
    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Action\Context $contextredirect,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_productloader = $_productloader;
        $this->messageManager = $messageManager;
        $this->_resultFactory = $context->getResultFactory();
        $this->_redirect = $contextredirect->getRedirect();
    }
 
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $request=$this->_request->getPost();
        $quote = $this->_checkoutSession->getQuote();
        $max_qty = $this->_scopeConfig->getValue(self::MAX_QTY, ScopeInterface::SCOPE_STORE);
        $quote_Item=[];
        $total_samp_qty = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('\Magento\Catalog\Helper\Product\Configuration');

        $productIdData = [];
        if ($request['cart']) {
            foreach ($request['cart'] as $key => $cartqty) {
                $productData = $objectManager->get('Magento\Quote\Model\Quote\Item')->load($key);
                
                if (array_key_exists($productData->getSku(), $productIdData)) {
                    $productIdData[$productData->getSku()] =  (int)$cartqty['qty'] + $productIdData[$productData->getSku()];
                } else {
                    $productIdData[$productData->getSku()] = (int)$cartqty['qty'];
                }
            }
        }
        
        if (!empty($productIdData)) {
            foreach ($productIdData as $key => $qty) {
                $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');

                $productData = $productRepository->get($key);
                
                $productId = $productData->getId();

                $stockItem = $objectManager->create('\Magento\CatalogInventory\Api\StockRegistryInterface');

                $stockProduct = $stockItem->getStockItem($productId);

                $productQty = (int)$stockProduct->getQty();
            
                if ($qty > $productQty) {
                    $itemsName = $productData->getName();
                    throw new LocalizedException(__('We don\'t have as many "%1" as you requested.', $itemsName));
                }
            }
        }
        
        if ($quote->getItems()) {
            foreach ($quote->getItems() as $loop_item) {
                $options=$helper->getCustomOptions($loop_item);
                foreach ($options as $option) {
                    if ($option['label']=="Sample Order") {
                        $quote_Item[$loop_item->getId()]=$loop_item->getQty();
                        $total_samp_qty +=$loop_item->getQty();
                    }
                }
            }
        }

        $Infos=$observer->getInfo();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('\Magento\Catalog\Helper\Product\Configuration');
        foreach ($Infos->toArray() as $item_id => $Info) {
            $item = $this->_checkoutSession->getQuote()->getItemById($item_id);

            $options=$helper->getCustomOptions($item);
            
            foreach ($options as $option) {
                if ($option['label']=="Sample Order") {
                    $total_samp_qty += (($item->getQty() - $Info['qty'])* (-1));
                }
            }
        }
        if ($total_samp_qty > $max_qty) {
            $this->messageManager->addNotice(__('You can not order more then %1 Sample Product.', $max_qty));
            throw new LocalizedException(__('You can not order more then %1 Sample Product.', $max_qty));
        }
    }
}
