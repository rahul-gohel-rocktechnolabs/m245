<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutOrderSave implements ObserverInterface
{
    /**
     * @var order
     */
    public $order;
    /**
     * @var logger
     */
    protected $logger;
    /**
     * @var orderSave
     */
    public $orderSave;
    /**
     * @var helper
     */
    public $helper;
    /**
     * @var coreSession
     */
    public $coreSession;
    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Mageants\DeliveryDate\Model\OrderSave $orderSave
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Mageants\DeliveryDate\Model\OrderSave $orderSave,
        \Mageants\DeliveryDate\Helper\Data $helper,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->order = $order;
        $this->orderSave = $orderSave;
        $this->helper = $helper;
        $this->coreSession = $coreSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Checkout Observer function
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return mixed
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $dateFormat = $this->helper->getPluginDateFormat();
        if ($order->getData('delivery_date')) {
            $product_data = [];
            if ($this->helper->getPluginDisplayAt() == 3) {
                $delivery_date = json_decode($order->getData('delivery_date'));
                foreach ($delivery_date as $key => $value) {
                    $product_data[$key]['item_id'] = $delivery_date[$key]->item_id;
                    $product_data[$key]['delivery_status'] = "pending";
                }
            } else {
                $delivery_date = $order->getData('delivery_date');
            }
            $orderid = $order->getId();
            $displayat = $this->helper->getPluginDisplayAt();
            $this->orderSave->setOrderId($orderid);
            $this->orderSave->setConfigurationDisplayAt($displayat);
            $this->orderSave->setDateFormat($dateFormat);
            $this->orderSave->save();
            try {
                $deliveryStatus = 'pending';
                $displayAt = $this->helper->getCustomModelData($order->getId());
                if ($displayAt == 3) {
                    $order->setData('delivery_status', json_encode($product_data));
                    $order->save();
                }
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $metadata->setPath('/');

        // $this->cookieManager->deleteCookie(
        //     'delivery_date',
        //     $metadata
        // );
        // $this->cookieManager->deleteCookie(
        //     'delivery_timeslot',
        //     $metadata
        // );
        // $this->cookieManager->deleteCookie(
        //     'delivery_comment',
        //     $metadata
        // );
    }
}
