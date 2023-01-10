<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;
use \Magento\Checkout\Model\Session;

class UpdatePaypalTotal implements ObserverInterface
{
    /**
     *
     * @var Session
     */
    public $checkout;

    /**
     *
     * @param Session $checkout
     */
    public function __construct(Session $checkout)
    {
        $this->checkout = $checkout;
    }
    /**
     * To Add Extra fee in paypal
     *
     * @param object $observer
     */
    public function execute(Observer $observer)
    {
        $cart = $observer->getEvent()->getCart();
        $quote = $this->checkout->getQuote();
        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $fee = $address->getFee();
        $cart->addCustomItem('Total Extra Fee', 1, $fee, 'total_extrafee');
        return $this;
    }
}
