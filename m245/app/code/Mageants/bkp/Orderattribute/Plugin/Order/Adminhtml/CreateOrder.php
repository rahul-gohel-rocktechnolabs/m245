<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order\Adminhtml;

class CreateOrder
{
    /**
     * @var \Mageants\Orderattribute\Model\OrderAttributesManagement
     */
    protected $attributesManagement;

    /**
     * @param \Mageants\Orderattribute\Model\OrderAttributesManagement $attributesManagement
     */
    public function __construct(
        \Mageants\Orderattribute\Model\OrderAttributesManagement $attributesManagement
    ) {
        $this->attributesManagement = $attributesManagement;
    }

    /**
     * After create order
     *
     * @param \Magento\Sales\Model\AdminOrder\Create $subject
     * @param \Magento\Sales\Model\Order $result
     * @return \Magento\Sales\Model\Order
     */
    public function afterCreateOrder(
        \Magento\Sales\Model\AdminOrder\Create $subject,
        \Magento\Sales\Model\Order $result
    ) {
        $orderAttributeData = $subject->getData('attributes');
        if (!empty($orderAttributeData)) {
            $this->attributesManagement->saveOrderAttributes($result, $orderAttributeData);
        }
        return $result;
    }
}
