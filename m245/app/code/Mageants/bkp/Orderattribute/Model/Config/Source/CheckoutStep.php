<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CheckoutStep implements ArrayInterface
{
    public const SHIPPING_STEP = 2;
    public const PAYMENT_STEP = 3;

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->toArray() as $stepId => $label) {
            $optionArray[] = ['value' => $stepId, 'label' => $label];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::SHIPPING_STEP => __('Shipping'),
            self::PAYMENT_STEP => __('Review & Payments'),
        ];
    }
}
