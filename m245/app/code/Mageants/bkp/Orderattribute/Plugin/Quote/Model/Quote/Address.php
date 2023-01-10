<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Quote\Model\Quote;

class Address
{
    /**
     * After get custom attributes
     *
     * @param \Magento\Quote\Model\Quote\Address $object
     * @param array $result
     * @return array
     */
    public function afterGetCustomAttributes($object, $result)
    {
        $attributes = $object->getOrderAttributes();
        if ($attributes && is_array($attributes)) {
            $result = array_merge($result, $attributes);
        }
        return $result;
    }
}
