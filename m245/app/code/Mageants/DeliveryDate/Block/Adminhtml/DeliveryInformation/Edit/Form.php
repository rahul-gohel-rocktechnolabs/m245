<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Block\Adminhtml\DeliveryInformation\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Sales\Model\Order $salesorder
     * @param \Magento\Catalog\Model\Product $product
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Mageants\DeliveryDate\Helper\Data $helper,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Sales\Model\Order $salesorder,
        \Magento\Catalog\Model\Product $product,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_systemStore = $systemStore;
        $this->_helper = $helper;
        $this->timezone = $timezone;
        $this->salesorder = $salesorder;
        $this->product = $product;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        $displayAt = $this->_helper->getCustomModelData($id);
        if ($displayAt == 3) {
            $dateFormat = $this->_helper->getCustomDateFormat($id);
            parent::_prepareForm();
            /** @var \Magento\Framework\Data\Form $form */
            $productID = $this->getRequest()->getParam('productid');
            $order = $this->salesorder->load($id)->getData();

            $deliverydate = json_decode($order['delivery_date']);
            $deliverytime = json_decode($order['delivery_timeslot']);
            $deliverycomment = json_decode($order['delivery_comment']);
            if ($order['delivery_status'] != null) {
                $deliverystatus = json_decode($order['delivery_status']);
            }
            $dateDeliveryData = [];
            foreach ($deliverydate as $key => $productId) {
                $product = $this->product->load($deliverydate[$key]->item_id);
                $dateDeliveryData[$key]['productId'] =  $product->getId();
                $dateDeliveryData[$key]['deliverydate'] = $deliverydate[$key]->delivery_date;
                $dateDeliveryData[$key]['deliverytime'] = $deliverytime[$key]->delivery_timeslot;
                $dateDeliveryData[$key]['deliverycomment'] = $deliverycomment[$key]->delivery_comment;
            }

            foreach ($dateDeliveryData as $key => $value) {
                if ($dateDeliveryData[$key]['productId'] == $productID) {
                    $form = $this->_formFactory->create(
                        ['data' =>
                        [
                            'id' => 'edit_form',
                            'action' => $this->getUrl(
                                'delivery/information/save',
                                ['id' => $id]
                            ), 'method' => 'post',
                            'enctype' => 'multipart/form-data'
                        ]]
                    );

                    $fieldset = $form->addFieldset(
                        'base_fieldset',
                        ['legend' => __('Delivery Date Information'), 'class' => '']
                    );

                    $fieldset->addField(
                        'product_id',
                        'hidden',
                        ['name' => 'product_id', 'value' => $productID]
                    );
                    $localeDate = $this->timezone;
                    if ($dateFormat == 'dd-mm-yy') {
                        $formatDate = 'd-MM-Y';
                        $formattedDate = $localeDate->date(new \DateTime($dateDeliveryData[$key]['deliverydate']))
                            ->format('d-m-Y');
                    } elseif ($dateFormat == 'mm-dd-yy') {
                        $formatDate = 'MM-d-Y';
                        $formattedDate = $localeDate
                            ->date(new \DateTime(str_replace('-', '/', $dateDeliveryData[$key]['deliverydate'])))
                            ->format('M-d-Y');
                    } else {
                        $formatDate = 'Y-MM-d';
                        $formattedDate = $localeDate->date(new \DateTime($dateDeliveryData[$key]['deliverydate']))
                            ->format('Y-m-d');
                    }
                    $fieldset->addField(
                        'delivery_date',
                        'date',
                        [
                            'name' => 'delivery_date',
                            'value' => $formattedDate,
                            'date_format' => 'Y-MM-d',
                            'label' => __('Delivery Date'),
                            'title' => __('Delivery Date'),
                            'required' => true
                        ]
                    );
                    $dayNames = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
                    $timestamp = strtotime($dateDeliveryData[$key]['deliverydate']);

                    $day = strtolower(date('l', $timestamp));
                    $slots = ($this->_helper->getTimeSlot()) ? $this->_helper->getTimeSlot() : [];

                    $fieldset->addField(
                        'delivery_timeslot',
                        'select',
                        [
                            'name' => 'delivery_timeslot',
                            'id' => 'delivery_timeslot',
                            'label' => __('Delivery Time Slot'),
                            'title' => __('Delivery Time Slot'),
                            'required' => true,
                            'values' => [
                                $dateDeliveryData[$key]['deliverytime'] => $dateDeliveryData[$key]['deliverytime']
                            ]
                        ]
                    );
                    $fieldset->addField(
                        'delivery_status',
                        'hidden',
                        [
                            'name' => 'delivery_status',
                            'id' => 'delivery_status',
                            'label' => __('Delivery Status'),
                            'title' => __('Delivery Status'),
                            'required' => false,
                            'values' => ["pending" => "Pending", "delivered" => "Delivered"]
                        ]
                    );
                    $configComment = $this->_helper->getPluginArrivalComment();
                    if ($configComment) {
                        $field =  $fieldset->addField(
                            'delivery_comment',
                            'textarea',
                            [
                                'name' => 'delivery_comment',
                                'label' => __('Shipping Arrival Comment'),
                                'title' => __('Shipping Arrival Comment'),
                                'required' => true,
                                'value' => $dateDeliveryData[$key]['deliverycomment']
                            ]
                        );
                        $field->setAfterElementHtml('
                            <input type="hidden" id="dayslotsslots" value=' . json_encode($slots) . ' />
                            <script>
                        //<![CDATA[
                            require(["jquery","mdate"], function ($) {
                               
                            });
                         
                        //]]>
                        </script>');
                    }
                }
            }
            $form->setUseContainer(true);
            $this->setForm($form);

            return parent::_prepareForm();
        } else {
            parent::_prepareForm();
            /** @var \Magento\Framework\Data\Form $form */
            $id = $this->getRequest()->getParam('id');
            $dateFormat = $this->_helper->getCustomDateFormat($id);
            $order = $this->salesorder->load($id)->getData();

            $form = $this->_formFactory->create(
                ['data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'delivery/information/save',
                        ['id' => $id]
                    ),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]]
            );

            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Delivery Date Information'), 'class' => '']
            );
            $localeDate = $this->timezone;
            if ($dateFormat == 'dd-mm-yy') {
                $formatDate = 'd-MM-Y';
                $order['delivery_date'];
            } elseif ($dateFormat == 'mm-dd-yy') {
                $formatDate = 'MM-d-Y';
                $order['delivery_date'];
            } else {
                $formatDate = 'Y-MM-d';
                $order['delivery_date'];
            }
            $fieldset->addField(
                'delivery_date',
                'date',
                [
                    'name' => 'delivery_date',
                    'value' => $order['delivery_date'],
                    'date_format' => 'Y-MM-d',
                    'label' => __('Delivery Date'),
                    'title' => __('Delivery Date'),
                    'required' => true,
                    'style' => 'width:250px'
                ]
            );

            //Timeslot array
            $timeslotval = $this->_helper->getTimeSlot();
            $timeslotvalArr = [];

            $dayNames = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
            $timestamp = strtotime($order['delivery_date']);
            $day = strtolower(date('l', $timestamp));

            if (count($timeslotval[$day]) > -1) {
                foreach ($timeslotval[$day] as $key => $value) {
                    $timeslotvalArr[] = [
                        'label' => $value['start_hour'] . ':' . $value['start_min']
                            . ' - ' . $value['end_hour'] . ':' . $value['end_min'],
                        'value' => $value['start_hour'] . ':' . $value['start_min']
                            . ' - ' . $value['end_hour'] . ':' . $value['end_min']
                    ];
                }
            } else {
                $timeslotvalArr[] = [];
            }
            $slots = ($this->_helper->getTimeSlot()) ? $this->_helper->getTimeSlot() : [];

            $fieldset->addField(
                'delivery_timeslot',
                'select',
                [
                    'name' => 'delivery_timeslot',
                    'id' => 'delivery_timeslot',
                    'label' => __('Delivery Time Slot'),
                    'title' => __('Delivery Time Slot'),
                    'required' => true,
                    'values' => $timeslotvalArr,
                    'style' => 'width:250px'
                ]
            );
            $configComment = $this->_helper->getPluginArrivalComment();
            if ($configComment) {
                $field =  $fieldset->addField(
                    'delivery_comment',
                    'textarea',
                    [
                        'name' => 'delivery_comment',
                        'label' => __('Shipping Arrival Comment'),
                        'title' => __('Shipping Arrival Comment'),
                        'required' => true,
                        'style' => 'height:80px;width:250px;'
                    ]
                );
                $field->setAfterElementHtml('
                    <input type="hidden" id="dayslotsslots" value=' . json_encode($slots) . ' />
                    <script>
                //<![CDATA[
                    require(["jquery","mdate"], function ($) {
                       
                    });
                 
                //]]>
                </script>');
            }
            if (!empty($order)) {
                $form->setValues($order);
            }
            $form->setUseContainer(true);
            $this->setForm($form);
            return parent::_prepareForm();
        }
    }
}
