<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Block\Adminhtml\Grid\Renderer;

/**
 * Customerprice class for display customer price in Grid
 */
class Customerprice extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Contruct
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerpricefactory = $customerpricefactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Render
     *
     * @param  \Magento\Framework\DataObject $row
     * @return [type]
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $customerprice = '';

        $entity_id = $row->getData('entity_id');
        $customerprice = $this->getCustomerPrice($entity_id);

        return '<input name="custom_price-'.$entity_id.'" 
        class="input-text custom_price" value="'.$customerprice.'" tabindex="1000">';
    }

    /**
     * Get CustomerPrice
     *
     * @param  entityid $entity_id
     * @return Price
     */
    public function getCustomerPrice($entity_id)
    {
        $customer_id = $this->getRequest()->getParam('id');

        $data = $this->customerpricefactory->create()->getCollection();
        $data->addFieldToSelect('customer_price');
        $data->addFieldToFilter('product_id', $entity_id);
        $data->addFieldToFilter('customer_id', $customer_id);
        $price_data = $data->getData();
        $price = '';
        foreach ($price_data as $key => $value) {
            $price = $value['customer_price'];
        }

        return $price;
    }
}
