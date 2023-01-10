<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Block\Adminhtml\Grid\Renderer;

/**
 * Delete class for delete customer price in Grid
 */
class Delete extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
     /**
      * @var $storeManager
      */
    protected $storeManager;

    /**
     * Construct
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Render
     *
     * @param  \Magento\Framework\DataObject $row
     * @return [type]
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $id = $row->getData('id');
        $url = $this->getBaseUrl().'/pricepercustomer/product/deletecustomerprice/';

        return '<a href="#" onclick="deleteProductCustomerPrice('.$id.',\''.$url.'\'); return false;">Delete</a>';
    }

    /**
     * Get BaseUrl
     *
     * @return Url
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
