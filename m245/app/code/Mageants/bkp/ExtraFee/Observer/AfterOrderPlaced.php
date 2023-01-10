<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Observer;

use \Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Mageants\ExtraFee\Helper\Data;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Mageants\ExtraFee\Model\ExtraFee;
use Magento\Framework\Pricing\Helper\Data as Pricedata;

class AfterOrderPlaced implements ObserverInterface
{
    public const ORDER_EXTRAFEE_AMOUNT = 'orderExtrafeeAmount';
    public const ORDER_EXTRAFEE_LABEL = 'orderExtraFeeLabel';
    public const SHIPPING_EXTRAFEE_IDS = 'shippingExtrafeeIds';
    public const SHIPPING_EXTRAFEE_LABEL = 'shippingExtraFeeLabel';
    public const CODFEE = 'codFee';
    public const MANDATORY_SHIPPING_EXTRAFEE = 'mandatoryShippingExtraFee';
    public const MANDATORY_SHIPPING_AMOUNT = 'mandatoryShippingAmount';
    public const MANDATORY_ORDER_EXTRAFEE = 'mandatoryOrderExtraFee';
    public const MANDATORY_ORDER_EXTRAFEE_IDS = 'mandatoryOrderExtraFeeIdsStr';
    public const ORDER_EXTRAFEE_IDS = 'orderExtrafeeId';
    
    /**
     * After Order use class
     *
     * @param CookieManagerInterface $cookieManager
     * @param Data $helperdata
     * @param SessionManagerInterface $sessionManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param ExtraFee $model
     * @param Pricedata $priceHelper
     */

    public function __construct(
        CookieManagerInterface $cookieManager,
        Data $helperdata,
        SessionManagerInterface $sessionManager,
        CookieMetadataFactory $cookieMetadataFactory,
        ExtraFee $model,
        Pricedata $priceHelper
    ) {
        $this->cookieManager = $cookieManager;
        $this->helperdata = $helperdata;
        $this->sessionManager = $sessionManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->model = $model;
        $this->priceHelper = $priceHelper;
    }

    /**
     * Remove cookie after place order
     *
     * @param object $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderFeeIds = $this->helperdata->getOrderFeeIds();
        $shippingExtrafeeIds = $this->cookieManager->getCookie('shippingExtrafeeIds');
        $mandatoryShippingAmount = $this->cookieManager->getCookie('mandatoryShippingAmount');
        $extraFeeComment = $this->cookieManager->getCookie('extraFeeComment');
        $order->setShippingAmount($order->getShippingAmount());
        $feesIds = explode(',', $orderFeeIds);
        $efeeids = [];
        $feePrice=0;
        $price=0;
        foreach ($feesIds as $feesId) {
            if ($feesId) {
                $ExtraFeeData=$this->model->load($feesId);
                if ($ExtraFeeData->getType()=='Percentage') {
                    $amount=($price*$ExtraFeeData->getAmount())/100;
                    $feePrice = $feePrice+$amount;
                } else {
                    $feePrice =  $feePrice+ $ExtraFeeData->getAmount();
                }
                $efeeids[] = $feesId.':'.$feePrice;
            }
        }
        $categorylable=$this->helperdata->getCategoryFeeLabels();
        $productlable=$this->helperdata->getProductFeeLabels();
        $efeeidsStr = implode(',', $efeeids);
        $order->setData('efeeids', $feePrice);
        $order->setData('categoryfeelable', $categorylable['catelable']);
        $order->setData('productfeelable', $productlable['prdlable']);
        $order->setData('categoryfeeapplyprdid', $categorylable['prdid']);
        $order->setData('productfeeapplyprdid', $productlable['prdid']);
        $order->setData('extrafeecomment', $extraFeeComment);
        $order->save();
            
        $this->cookieManager->deleteCookie(self::ORDER_EXTRAFEE_AMOUNT, $this->cookieMetadata());
        $this->cookieManager->deleteCookie(self::ORDER_EXTRAFEE_LABEL, $this->cookieMetadata());
        $this->cookieManager->deleteCookie(self::SHIPPING_EXTRAFEE_IDS, $this->cookieMetadata());
        $this->cookieManager->deleteCookie(self::SHIPPING_EXTRAFEE_LABEL, $this->cookieMetadata());
        $this->cookieManager->deleteCookie(self::CODFEE, $this->cookieMetadata());
        $this->cookieManager->deleteCookie(self::MANDATORY_SHIPPING_EXTRAFEE, $this->cookieMetadata());
        $this->cookieManager->deleteCookie(self::MANDATORY_SHIPPING_AMOUNT, $this->cookieMetadata());
        $this->cookieManager->deleteCookie(self::MANDATORY_ORDER_EXTRAFEE, $this->cookieMetadata());
        $this->cookieManager->deleteCookie(self::MANDATORY_ORDER_EXTRAFEE_IDS, $this->cookieMetadata());
        $this->cookieManager->deleteCookie(self::ORDER_EXTRAFEE_IDS, $this->cookieMetadata());
        $this->cookieManager->deleteCookie('extraFeeComment', $this->cookieMetadata());
        $this->cookieManager->deleteCookie('categoryExtraFeeLabel', $this->cookieMetadata());
    }

    /**
     * To get the cookie related data
     */
    public function cookieMetadata()
    {
        $cookiedata = $this->cookieMetadataFactory
                        ->createCookieMetadata()
                        ->setPath($this->sessionManager->getCookiePath())
                        ->setDomain($this->sessionManager->getCookieDomain());
        return $cookiedata;
    }
}
