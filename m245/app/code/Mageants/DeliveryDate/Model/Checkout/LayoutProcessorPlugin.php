<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Model\Checkout;

use Magento\Framework\Serialize\SerializerInterface;

class LayoutProcessorPlugin
{
    /**
     * @var deliveryhelper
     */
    public $deliveryhelper;

    /**
     * @var timeslot
     */
    public $timeslot;
    /**
     * @var array
     */
    public $timeslotArr = [];

    /**
     * @var pluginStatus
     */
    public $pluginStatus;

    /**
     * @var displayat
     */
    public $displayat;

    /**
     * @var serializer
     */
    public $serializer;

    /**
     * @param \Mageants\DeliveryDate\Helper\Data $deliveryhelper
     * @param \Magento\Customer\Model\SessionFactory $sessionFactory
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        \Mageants\DeliveryDate\Helper\Data $deliveryhelper,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        SerializerInterface $serializer = null
    ) {
        $this->serializer = $serializer;
        $this->session = $sessionFactory;
        $this->_deliveryhelper = $deliveryhelper;
    }

    /**
     * Get Layout Processor function
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return void
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $this->_pluginStatus = $this->_deliveryhelper->getPluginEnable();
        $this->_displayat = $this->_deliveryhelper->getPluginDisplayAt();
        $customerSession = $this->session->create();
        if ($this->_pluginStatus == 1) {
            if ($this->_displayat == 0) {
                if ($customerSession->isLoggedIn()) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                    ['children']['shippingAddress']['children']['before-form']['children']['orderdeliveryDate']
                        = [
                            'component' => 'Mageants_DeliveryDate/js/view/delivery-date-block',
                            'config' => [
                                'template' => 'Mageants_DeliveryDate/delivery-date-block',
                                'id' => 'delivery_date'
                            ],
                            'label' => 'Delivery Date Information',
                        ];
                } else {
                    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['shipping-address-fieldset']['children']['orderdeliveryDate']
                        = [
                            'component' => 'Mageants_DeliveryDate/js/view/delivery-date-block',
                            'config' => [
                                'template' => 'Mageants_DeliveryDate/delivery-date-block',
                                'id' => 'delivery_date'
                            ],
                            'validation' => [
                                'required-entry' => true,
                            ],
                            'label' => 'Delivery Date Information',
                        ];
                }
            } elseif ($this->_displayat == 1) {
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['before-shipping-method-form']['children']['orderdeliveryDate']
                    = [
                        'component' => 'Mageants_DeliveryDate/js/view/delivery-date-block',
                        'config' => [
                            'template' => 'Mageants_DeliveryDate/delivery-date-block',
                            'id' => 'delivery_date'
                        ],
                        'validation' => [
                            'required-entry' => true,
                        ],
                    ];
            } elseif ($this->_displayat == 2) {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['beforeMethods']['children']['orderdeliveryDate']
                    = [
                        'component' => 'Mageants_DeliveryDate/js/view/delivery-date-block',
                        'config' => [
                            'template' => 'Mageants_DeliveryDate/delivery-date-block',
                            'id' => 'delivery_date'
                        ],
                        'validation' => [
                            'required-entry' => true,
                        ],
                    ];
            } elseif ($this->_displayat == 3) {
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['before-shipping-method-form']['children']['orderdeliveryDate']
                    = [
                        'component' => 'Mageants_DeliveryDate/js/view/delivery-date-block-product',
                        'config' => [
                            'template' => 'Mageants_DeliveryDate/delivery-date-block-product',
                            'id' => 'delivery_date'
                        ],
                        'validation' => [
                            'required-entry' => true,
                        ],
                    ];
            }
            $jsLayout['components']['checkout']['children']['sidebar']['children']['orderdeliveryDate']
                = [
                    'component' => 'Mageants_DeliveryDate/js/view/delivery-sidebar-view',
                    'config' => [
                        'displayArea' => 'shipping-information'
                    ]
                ];
        }
        return $jsLayout;
    }
}
