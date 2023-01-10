<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin;

class Order
{
    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    protected $orderAttributeValue;

    /**
     * @param \Mageants\Orderattribute\Model\Order\Attribute\Value $orderAttributeValue
     */
    public function __construct(
        \Mageants\Orderattribute\Model\Order\Attribute\Value $orderAttributeValue
    ) {
        $this->orderAttributeValue = $orderAttributeValue;
    }

    /**
     * Before get data
     *
     * @param \Magento\Sales\Model\Order $subject
     * @param string $key
     * @param int $index
     * @return string
     */
    public function beforeGetData(\Magento\Sales\Model\Order $subject, $key = '', $index = null)
    {
        $whiteList = [
            '',
            'increment_id',
            'mageants_order_attributes',
            'store_id',
            'entity_id',
            'items',
            'customer',
            'items_count',
        ];
        if (in_array($key, $whiteList)) {
            return [$key, $index];
        }

        $orderAttributes = $subject->getMageantsOrderAttributes();
        if ($orderAttributes == null) {
            $this->orderAttributeValue->loadByOrderId($subject->getId());

            $orderAttributes = $this->orderAttributeValue->getAttributes($subject->getStoreId());
            $subject->setMageantsOrderAttributes($orderAttributes);
        }

        if (array_key_exists($key, $orderAttributes)) {
            $attribute = $orderAttributes[$key];
            $subject->setData($key, $attribute->getValueOutput());
        }

        return [$key, $index];
    }
}
