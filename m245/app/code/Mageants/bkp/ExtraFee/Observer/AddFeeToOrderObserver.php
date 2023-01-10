<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;

class AddFeeToOrderObserver implements ObserverInterface
{
    /**
     * @var CookieManagerInterface
     */
    public $cookiemanagerinterface;
    /**
     *
     * @param CookieManagerInterface $cookiemanagerinterface
     */
    public function __construct(
        CookieManagerInterface $cookiemanagerinterface
    ) {
        $this->cookiemanagerinterface = $cookiemanagerinterface;
    }

    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $value=$this->cookiemanagerinterface->getCookie("orderExtrafeeAmount");
        $fname=$this->cookiemanagerinterface->getCookie("orderExtraFeeLabel");
        
        $quote = $observer->getQuote();
        $ExtrafeeFee = $quote->getFee();
        if (!$ExtrafeeFee) {
            return $this;
        }
        //Set fee data to order
        $order = $observer->getOrder();
        $order->setData('fee', $ExtrafeeFee);
        $order->setData('grand_total', $order->getGrandTotal());
        $this->cookiemanagerinterface->deleteCookie(
            "orderExtraFeeLabel"
        );
        return $this;
    }
}
