<?php
namespace Mageants\DeliveryDate\Observer\Checkout;

use Magento\Framework\Event\ObserverInterface;

class AfterAddtoCart implements ObserverInterface
{
    /**
     * @var helper
     */
    protected $helper;
    /**
     * @var request
     */
    protected $request;
    
    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteCollection
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteCollection,
        \Mageants\DeliveryDate\Helper\Data $helper
    ) {
        $this->quote = $checkoutSession->getQuote();
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->quoteCollection = $quoteCollection;
        $this->helper = $helper;
    }

    /**
     * After Add To cart function
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $this->request->getPost();
        $item_id = 0;
        $product= [];
        $itemData =[];
        if ($this->helper->getPluginDisplayAt() == 3) {
            if (isset($data['del-date']) && isset($data['delivery_timeslot'])) {
                $quote = $this->quoteCollection->create();
                $items = $this->quote->getAllItems();
                foreach ($items as $item) {
                    if ($item->getId() > $item_id) {
                        $item_id = $item->getId();
                    }
                }
                foreach ($quote->addFieldToFilter('item_id', $item_id)->load()->getItems() as $quoteData) {
                    $quoteData->setData('delivery_date', $data['del-date']);
                    $quoteData->setData('delivery_timeslot', $data['delivery_timeslot']);
                    if (isset($data['delivery_comment'])) {
                        $quoteData->setData('delivery_comment', $data['delivery_comment']);
                    }
                    $quoteData->save();
                }
            }
        }
    }
}
