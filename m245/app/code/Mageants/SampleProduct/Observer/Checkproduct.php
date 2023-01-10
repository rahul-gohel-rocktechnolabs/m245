<?php
namespace Mageants\SampleProduct\Observer;
  
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Checkout\Model\Session;

class Checkproduct implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    protected $resultRedirectFactory;

    protected $_checkoutSession;

    protected $stockRegistry;

    protected $_redirect;
  
    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        Session $checkoutSession,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->_messageManager = $messageManager;
        $this->resultRedirectFactory = $resultFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->stockRegistry = $stockRegistry;
        $this->_redirect = $url;
    }
  
    /**
     * add to cart event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if ($observer->getRequest()->getParam('isSample') == "1") {
            if ($observer->getRequest()->getParam('qty')) {
                $curr_qty = $observer->getRequest()->getParam('qty');
            } else {
                $curr_qty = 1;
            }
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $quote = $this->_checkoutSession->getQuote();
                $productSku = '';
            if (isset($quote) && !empty($quote->getItems())) {
                $postproductId = (int)$observer->getRequest()->getParam('product');
                foreach ($quote->getItems() as $item) {
                    if ($postproductId == $item->getProduct()->getId()) {
                        $productSku = $item->getProduct()->getSku();
                        $totalItemQty = $curr_qty;
                       // $itemsName = $item->getName();
                    }
                }
                    
                foreach ($quote->getItems() as $Qitem) {
                    if ($Qitem->getProduct()->getSKu() == $productSku) {
                        $totalItemQty = $totalItemQty + $Qitem->getQty();
                    }
                }
                if ($productSku) {
                    $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');

                    $productData = $productRepository->get($productSku);
                    $productId = $productData->getId();

                    $stockItem = $this->stockRegistry->getStockItem($productId);
                    $productQty = $stockItem->getQty();
                        
                    if ($totalItemQty > $productQty) {
                        $this->_messageManager->addError(__('We don\'t have as many "%1" as you requested.', $productData->getName()));
                        $resultRedirect = $this->resultRedirectFactory->create(ResultFactory::TYPE_REDIRECT);
                        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                        $observer->getRequest()->setParam('product', false);
                        return $this;
                    }
                }
            }
        } else {
            return $this;
        }
    }
}
