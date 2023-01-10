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

class AddHtmlToOrderShippingBlockObserver implements ObserverInterface
{
    /**
     * @var helper
     */
    protected $helper;
    /**
     * @var serializer
     */
    public $serializer;

    /**
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     * @param SerializerInterface $serializer
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\View\Element\Template $template
     */
    public function __construct(
        \Mageants\DeliveryDate\Helper\Data $helper,
        SerializerInterface $serializer,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\View\Element\Template $template
    ) {
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->timezone = $timezone;
        $this->template = $template;
    }

    /**
     * Observer function
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if ($observer->getElementName() == 'sales.order.info') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
            $dateFormat = $this->helper->getCustomDateFormat($order->getId());
            $displayAt = $this->helper->getCustomModelData($order->getId());
            if ($displayAt == 3) {
                $deliveryDateBlock = $this->template;
                $deliverydate = $this->serializer->unserialize($order->getDeliveryDate());
                $deliverytime = $this->serializer->unserialize($order->getDeliveryTimeslot());
                $deliverycomment = $this->serializer->unserialize($order->getDeliveryComment());
                $dateDeliveryData = [];
                $formattedDate = '';
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
                            str_replace('-', '/', $order->getDeliveryDate()),
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
                $deliveryDateBlock =$this->template;
                $deliveryDateBlock->setDeliveryDate($formattedDate);
                $deliveryDateBlock->setDeliveryTimeslot($order->getDeliveryTimeslot());
                $deliveryDateBlock->setDeliveryComment($order->getDeliveryComment());
            }
        }
    }
}
