<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Block\Adminhtml\Grid\Renderer;

/**
 * Customerspecialprice class for display customer special price in Grid
 */
class Customerspecialprice extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Construct
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
     * @return special price
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $customerspecialprice = '';

        $entity_id = $row->getData('entity_id');
        $customerspecialprice = $this->getCustomerSpecialPrice($entity_id);

        return '<input name="custom_specialprice-'.$entity_id.'" 
        class="input-text custom_specialprice" value="'.$customerspecialprice.'" tabindex="1000">';
    }

    /**
     * Get Customerspecialprice
     *
     * @param  entityid $entity_id
     * @return price
     */
    public function getCustomerSpecialPrice($entity_id)
    {
        $customer_id = $this->getRequest()->getParam('id');

        $data = $this->customerpricefactory->create()->getCollection();
        $data->addFieldToSelect('special_price');
        $data->addFieldToFilter('product_id', $entity_id);
        $data->addFieldToFilter('customer_id', $customer_id);
        $price_data = $data->getData();
        $price = '';
        foreach ($price_data as $key => $value) {
            $price = $value['special_price'];
        }

        return $price;
    }
}
