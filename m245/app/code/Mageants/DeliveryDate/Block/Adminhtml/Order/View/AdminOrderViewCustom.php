<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\DeliveryDate\Block\Adminhtml\Order\View;

use Magento\Framework\Registry;

class AdminOrderViewCustom extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
}
