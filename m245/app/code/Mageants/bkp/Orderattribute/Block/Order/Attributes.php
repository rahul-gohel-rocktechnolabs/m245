<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Order;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;

class Attributes extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    protected $orderValue;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param Template\Context $context
     * @param \Mageants\Orderattribute\Model\Order\Attribute\Value $orderValue
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Mageants\Orderattribute\Model\Order\Attribute\Value $orderValue,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderValue = $orderValue;
        $this->_coreRegistry = $registry;
    }

    /**
     * Get list
     *
     * @return object
     */
    public function getList()
    {
        $orderModel = $this->getOrder();
        if (!$orderModel) {
            return [];
        }
        $this->orderValue->loadByOrderId($orderModel->getId());

        $list = $this->orderValue->getOrderAttributeValues(
            $orderModel->getStoreId()
        );

        return $list;
    }

    /**
     * Has data in list
     *
     * @param array $orderAttributesList
     * @return string
     */
    public function hasDataInList($orderAttributesList)
    {
        if (!$orderAttributesList) {
            return false;
        }
        foreach ($orderAttributesList as $value) {
            if ('' !== $value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get order
     *
     * @return Order
     */
    protected function getOrder()
    {
        if (!$this->hasData('order_entity')) {
            $order = $this->_coreRegistry->registry('current_order');

            if (!$order && $this->getParentBlock()) {
                $order = $this->getParentBlock()->getOrder();
            }

            $this->setData('order_entity', $order);
        }
        return $this->getData('order_entity');
    }

    /**
     * Prepare attribute value for displaying
     *
     * @param string $value
     * @return string
     */
    public function prepareAttributeValueForDisplaying($value)
    {
        $value = $value
        ? nl2br(htmlentities(preg_replace('/\$/', '\\\$', $value), ENT_COMPAT, "UTF-8"))
        : __('-- no value--');
        return $value;
    }
}
