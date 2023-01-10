<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveDeliveryDateToOrderObserver implements ObserverInterface
{
    /**
     * Constructor function
     *
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     */
    public function __construct(\Magento\Quote\Model\QuoteFactory $quoteFactory)
    {
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Save Delivery Date function
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quote = $this->quoteFactory->create()->load($order->getQuoteId());

        if (!empty($quote->getDeliveryDate())) {
            $order->setDeliveryDate($quote->getDeliveryDate());
        }
        if (!empty($quote->getDeliveryTimeslot())) {
            $order->setDeliveryTimeslot($quote->getDeliveryTimeslot());
        }
        if (!empty($quote->getDeliveryComment())) {
            $order->setDeliveryComment($quote->getDeliveryComment());
        }
        return $this;
    }
}
