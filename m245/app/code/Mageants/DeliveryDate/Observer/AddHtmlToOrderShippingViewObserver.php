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
use Magento\Framework\Serialize\SerializerInterface;

class AddHtmlToOrderShippingViewObserver implements ObserverInterface
{
    /**
     * @var helper
     */
    protected $helper;
    /**
     * @var OrderSaveFactory
     */
    public $OrderSaveFactory;
    /**
     * @var serializer
     */
    public $serializer;

    /**
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     * @param SerializerInterface $serializer
     * @param \Mageants\DeliveryDate\Model\OrderSaveFactory $OrderSaveFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\View\Element\Template $template
     */
    public function __construct(
        \Mageants\DeliveryDate\Helper\Data $helper,
        SerializerInterface $serializer,
        \Mageants\DeliveryDate\Model\OrderSaveFactory $OrderSaveFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\View\Element\Template $template
    ) {
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->OrderSaveFactory = $OrderSaveFactory;
        $this->timezone = $timezone;
        $this->template = $template;
    }

    /**
     * Observer Function
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if ($observer->getElementName() == 'order_shipping_view') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
            $displayAt = '';
            $dateFormat = $this->helper->getCustomDateFormat($order->getId());
            $orderRecord = $this->OrderSaveFactory->create();
            $orderCollection = $orderRecord->getCollection();
            $orderIdData = $orderCollection->addFieldToFilter('order_id', $order->getId());
            $formattedDate = '';
            foreach ($orderIdData as $key => $value) {
                $displayAt = $value['configuration_display_at'];
            }
            if ($displayAt == 3) {
                $deliveryDateBlock = $this->template;
                $deliverydate = $this->serializer->unserialize($order->getDeliveryDate());
                $deliverytime = $this->serializer->unserialize($order->getDeliveryTimeslot());
                $deliverycomment = $this->serializer->unserialize($order->getDeliveryComment());
                if ($order->getDeliveryStatus() != null) {
                    $deliverystatus = $this->serializer->unserialize($order->getDeliveryStatus());
                }
                $dateDeliveryData = [];

                foreach ($deliverydate as $key => $productId) {
                    $localeDate = $this->timezone;
                    if ($deliverydate[$key]['delivery_date'] != '0000-00-00 00:00:00') {
                        if ($dateFormat == 'dd-mm-yy' || $dateFormat == 'yy-mm-dd') {
                            $formattedDate = $localeDate->formatDateTime(
                                $deliverydate[$key]['delivery_date'],
                                \IntlDateFormatter::MEDIUM,
                                \IntlDateFormatter::MEDIUM,
                                null,
                                $localeDate->getConfigTimezone(
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                    $order->getStore()->getCode()
                                )
                            );
                        } else {
                            $formattedDate = $localeDate->formatDateTime(
                                str_replace('-', '/', $deliverydate[$key]['delivery_date']),
                                \IntlDateFormatter::MEDIUM,
                                \IntlDateFormatter::MEDIUM,
                                null,
                                $localeDate->getConfigTimezone(
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                    $order->getStore()->getCode()
                                )
                            );
                        }
                        $deliverydate[$key]['delivery_date'] = $formattedDate;
                    } else {
                        $deliverydate[$key]['delivery_date'] = __('N/A');
                    }
                    $deliveryDateBlock->setDeliveryDate($deliverydate[$key]['delivery_date']);
                    $deliveryDateBlock->setDeliveryTimeslot($deliverytime[$key]['delivery_timeslot']);
                    $deliveryDateBlock->setDeliveryComment($deliverycomment[$key]['delivery_comment']);
                    if (isset($deliverystatus)) {
                        $deliveryDateBlock->setDeliveryStatus($deliverystatus[$key]['delivery_status']);
                    }
                }
            } else {
                $localeDate = $this->timezone;
                if ($order->getDeliveryDate() != '0000-00-00 00:00:00') {
                    if ($dateFormat == 'dd-mm-yy' || $dateFormat == 'yy-mm-dd') {
                        $formattedDate = $localeDate->formatDateTime(
                            $order->getDeliveryDate(),
                            \IntlDateFormatter::MEDIUM,
                            \IntlDateFormatter::MEDIUM,
                            null,
                            $localeDate->getConfigTimezone(
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                $order->getStore()->getCode()
                            )
                        );
                    } else {
                        $formattedDate = $localeDate->formatDateTime(
                            $order->getDeliveryDate(),
                            \IntlDateFormatter::MEDIUM,
                            \IntlDateFormatter::MEDIUM,
                            null,
                            $localeDate->getConfigTimezone(
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                $order->getStore()->getCode()
                            )
                        );
                    }
                } else {
                    $formattedDate = __('N/A');
                }
                $deliveryDateBlock = $this->template;
                $deliveryDateBlock->setDeliveryDate($formattedDate);
                $deliveryDateBlock->setDeliveryTimeslot($order->getDeliveryTimeslot());
                if ($order->getDeliveryComment() != null) {
                    $deliveryDateBlock->setDeliveryComment($order->getDeliveryComment());
                }
            }
        }
    }
}
