<?php
namespace Mageants\SampleProduct\Observer;
  
use Magento\Framework\Event\ObserverInterface;
  
class OrderItemAdditionalOptions implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $quote = $observer->getQuote();
            $order = $observer->getOrder();
            $quoteItems = [];
 
            // Map Quote Item with Quote Item Id
            foreach ($quote->getAllVisibleItems() as $quoteItem) {
                $quoteItems[$quoteItem->getId()] = $quoteItem;
            }
 
            foreach ($order->getAllVisibleItems() as $orderItem) {
                $quoteItemId = $orderItem->getQuoteItemId();
                $quoteItem = $quoteItems[$quoteItemId];
                $additionalOptions = $quoteItem->getOptionByCode('additional_options');
 
                 
                if (count($additionalOptions) > 0) {
                    // Get Order Item's other options
                    $options = $orderItem->getProductOptions();
                    // Set additional options to Order Item
                    $options['additional_options'] = unserialize($additionalOptions->getValue());
                    $orderItem->setProductOptions($options);
                }
            }
        } catch (\Exception $e) {
            // catch error if any
        }
    }
}
