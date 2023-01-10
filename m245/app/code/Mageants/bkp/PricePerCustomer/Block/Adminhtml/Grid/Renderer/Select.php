<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Block\Adminhtml\Grid\Renderer;

/**
 * Select class for select customer price in Grid
 */
class Select extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Construct
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerpricefactory = $customerpricefactory;
    }

    /**
     * Render
     *
     * @param  \Magento\Framework\DataObject $row
     * @return customer
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $customer_id = $row->getData('entity_id');
        $customer_email = $row->getData('email');

        return '<a href="javascript:;" 
        onclick="selectAddCustomer('.$customer_id.',\''.trim($customer_email).'\');">Select</a>';
    }
}
