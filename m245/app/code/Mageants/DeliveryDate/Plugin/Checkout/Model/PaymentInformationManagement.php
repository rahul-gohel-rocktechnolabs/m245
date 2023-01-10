<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Plugin\Checkout\Model;

class PaymentInformationManagement
{

    /**
     * @var quoteRepository
     */
    protected $quoteRepository;

    /**
     * Constructor function
     *
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->cookieManager = $cookieManager;
    }

    /**
     * Before Save Payment function
     *
     * @param mixed $cartId
     * @return void
     */
    public function beforesavePaymentInformationAndPlaceOrder(
        $cartId
    ) {
        $quote = $this->quoteRepository->getActive($cartId);
        $deliveyDate = $this->cookieManager->getCookie('delivery_date');
        $deliveyComment = $this->cookieManager->getCookie('delivery_comment');
        $deliveyTime = $this->cookieManager->getCookie('delivery_timeslot');
        $quote->setDeliveryDate($deliveyDate);
        $quote->setDeliveryComment($deliveyComment);
        $quote->setDeliveryTimeslot($deliveyTime);
    }
}
