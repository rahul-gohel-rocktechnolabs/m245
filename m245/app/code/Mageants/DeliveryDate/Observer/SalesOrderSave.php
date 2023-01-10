<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderSave implements ObserverInterface
{
    /**
     * Constructor
     *
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
    ) {
        $this->cookieManager = $cookieManager;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Observer function
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getOrder();
            $quote = $this->quoteFactory->create()->load($order->getQuoteId());
            $deliveyDate = $this->cookieManager->getCookie(
                'delivery_date'
            );
            $deliveyTime = $this->cookieManager->getCookie(
                'delivery_timeslot'
            );
            $deliveyCom = $this->cookieManager->getCookie(
                'delivery_comment'
            );
            $quote->setDeliveryDate($deliveyDate);
            $quote->setDeliveryTimeslot($deliveyTime);

            $order->setDeliveryDate($deliveyDate);
            $order->setDeliveryTimeslot($deliveyTime);

            if (!empty($deliveyCom)) {
                $quote->setDeliveryComment($deliveyCom);
                $order->setDeliveryComment($deliveyCom);
            }
            return $this;
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
