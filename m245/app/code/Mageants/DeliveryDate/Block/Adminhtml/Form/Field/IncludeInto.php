<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Block\Adminhtml\Form\Field;

class IncludeInto implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var datearray
     */
    public $datearr;
    /**
     * Option function
     *
     * @return array
     */
    public function toOptionArray()
    {
        $arr =  [
            1 => "Print Copy of Order Confirmation",
            2 => "Order Confirmation E-mail",
            3 => "Invoice E-mail",
            4 => "Shipment E-mail",
            5 => "Invoice PDF",
            6 => "Shipment PDF(Packing Slip)",
        ];
        $datefieldArr = [];
        foreach ($arr as $key => $value) {
            $datefieldArr[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $datefieldArr;
    }
}
