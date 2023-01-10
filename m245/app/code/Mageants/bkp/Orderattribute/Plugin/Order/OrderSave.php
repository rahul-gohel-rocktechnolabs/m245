<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order;

class OrderSave
{
    /**
     * @var \Mageants\Orderattribute\Model\OrderAttributesManagement
     */
    protected $orderAttributesManagement;
    /**
     * @var \Magento\Framework\App\State
     */
    public $state;

    /**
     * OrderSave constructor.
     * @param \Mageants\Orderattribute\Model\OrderAttributesManagement $orderAttributesManagement
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Mageants\Orderattribute\Model\OrderAttributesManagement $orderAttributesManagement,
        \Magento\Framework\App\State $state
    ) {
        $this->orderAttributesManagement = $orderAttributesManagement;
        $this->state = $state;
    }

    /**
     * After save
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        if ($this->state->getAreaCode() !== \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $this->orderAttributesManagement->saveOrderAttributes($order, null);
        }

        return $order;
    }
}
