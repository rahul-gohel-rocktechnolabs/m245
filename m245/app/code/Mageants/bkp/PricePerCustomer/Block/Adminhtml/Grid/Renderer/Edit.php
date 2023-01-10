<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Block\Adminhtml\Grid\Renderer;

/**
 * Edit class for edit customer price in Grid
 */
class Edit extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Contruct
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
     * @return Price
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $customer_id = $row->getData('customer_id');
        $product_id = $this->getRequest()->getParam('id');
        $customer_email = $row->getData('customer_email');
        $customer_price = $row->getData('customer_price');
        $special_price = $row->getData('special_price');

        $price_id = $this->getPriceId($customer_id, $product_id);

        return '<a href="javascript:;" onclick="editProductCustomerPrice('.$price_id.','.$customer_id.',\''.trim($customer_email) ?? ''.'\',\''.trim($customer_price) ?? ''.'\',\''.trim($special_price) ?? ''.'\');">Edit</a>';
    }

    /**
     * Get PriceId
     *
     * @param  customerid $customer_id
     * @param  productid $product_id
     * @return Customer Price
     */
    public function getPriceId($customer_id, $product_id)
    {
        $data = $this->customerpricefactory->create()->getCollection();
        $data->addFieldToSelect('*');
        $data->addFieldToFilter('product_id', $product_id);
        $data->addFieldToFilter('customer_id', $customer_id);
        $customer_price = $data->getData();

        return $customer_price[0]['id'];
    }
}
