<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template\Context;
use Magento\Sales\Model\Order;

class Attributes extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Mageants\Orderattribute\Helper\Config
     */
    protected $config;

    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    protected $orderAttributeValue;

    /**
     * @param Context $context
     * @param \Mageants\Orderattribute\Model\Order\Attribute\Value $orderAttributeValue
     * @param \Mageants\Orderattribute\Helper\Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Mageants\Orderattribute\Model\Order\Attribute\Value $orderAttributeValue,
        \Mageants\Orderattribute\Helper\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->orderAttributeValue = $orderAttributeValue;
    }

    /**
     * Get list
     *
     * @return object
     */
    public function getList()
    {
        $orderModel = $this->getOrder();
        $this->orderAttributeValue->loadByOrderId($orderModel->getId());

        $list = $this->orderAttributeValue->getOrderAttributeValues(
            $orderModel->getStoreId()
        );

        return $list;
    }

    /**
     * Get order attribute edit link
     *
     * @param string $label
     * @return string
     */
    public function getOrderAttributeEditLink($label = '')
    {
        $link = '';
        if ($this->isAllowedToEdit() && $this->isOrderViewPage()) {
            $label = $label ?: __('Edit');
            $url = $this->getOrderAttributeEditUrl();
            $link = sprintf('<a href="%s">%s</a>', $url, $label);
        }

        return $link;
    }

    /**
     * Get order attribute edit url
     *
     * @return string
     */
    public function getOrderAttributeEditUrl()
    {
        return $this->getUrl(
            'mgorderattribute/order_attributes/edit',
            ['order_id' => $this->getOrder()->getId()]
        );
    }

    /**
     * Is allowed to edit
     *
     * @return bool
     */
    public function isAllowedToEdit()
    {
        return $this->_authorization->isAllowed('Mageants_Orderattribute::attribute_value_edit');
    }

    /**
     * Get order
     *
     * @return Order
     */
    protected function getOrder()
    {
        if (!$this->hasData('order_entity')) {
            $this->setData('order_entity', $this->getParentBlock()->getOrder());
        }
        return $this->getData('order_entity');
    }

    /**
     * Is order view page
     *
     * @return boolean
     */
    public function isOrderViewPage()
    {
        return (boolean) $this->getOrderInfoArea() == 'order';
    }

    /**
     * Is shipment view page
     *
     * @return bool
     */
    public function isShipmentViewPage()
    {
        return (boolean) $this->getOrderInfoArea() == 'shipment';
    }

    /**
     * Is invoice view page
     *
     * @param object $order
     * @return bool
     */
    public function isInvoiceViewPage()
    {
        return (boolean) $this->getOrderInfoArea() == 'invoice';
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
        ? nl2br(htmlentities(
            preg_replace('/\$/', '\\\$', $value),
            ENT_COMPAT,
            "UTF-8"
        ))
        : __('-- no value--');
        return $value;
    }
}
