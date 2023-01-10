<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;

class AfterOrder implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface CookieManagerInterface
     */
    private $cookieManager;

    /**
     *
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        CookieManagerInterface $cookieManager
    ) {
        $this->cookieManager = $cookieManager;
    }
    /**
     * Remove cookie after place order
     *
     * @param object $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if (isset(${'_COOKIE'}['shippingExtraFeeLabel'])) {
            unset(${'_COOKIE'}['shippingExtraFeeLabel']);
        }
        $this->cookieManager->deleteCookie(
            "shippingExtraFeeLabel"
        );
        $this->cookieManager->deleteCookie(
            "shippingExtrafeeIds"
        );
        $this->cookieManager->deleteCookie(
            "orderExtrafeeAmount"
        );
        $this->cookieManager->deleteCookie(
            "orderExtraFeeLabel"
        );
    }
}
