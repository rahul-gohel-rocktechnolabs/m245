<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Block\Adminhtml\Grid;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\StoreManagerInterface as storeManager;
use Mageants\PricePerCustomer\Model\ResourceModel\PricePerCustomer\Collection as CustomerPriceCollection;

/**
 * Grid class for fetch Customer Price data for Grid
 */
class Grid extends Extended
{
    /**
     * [$registry description]
     * @var [type]
     */
    protected $registry;

    /**
     * [$currency description]
     * @var [type]
     */
    protected $currency;

    /**
     * [$storeManager description]
     * @var [type]
     */
    protected $storeManager;

    /**
     * [$customerpriceFactory description]
     * @var [type]
     */
    protected $customerpriceFactory;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param Currency $currency
     * @param StoreManager $storeManager
     * @param CustomerPriceCollection $customerpriceFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $registry,
        Currency $currency,
        StoreManager $storeManager,
        CustomerPriceCollection $customerpriceFactory,
        array $data = []
    ) {
        $this->currency = $currency;
        $this->_storeManager = $storeManager;
        $this->registry = $registry;
        $this ->customerpriceFactory = $customerpriceFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Contruct
     *
     * @return construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customerPriceGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Get CurrentProduct
     *
     * @return Product
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Collection
     *
     * @return Collection
     */
    protected function _prepareCollection()
    {
        $id = $this->getRequest()->getParam('id');
        $data = $this->customerpriceFactory;
        $data->addFieldToSelect('*');
        $data->addFieldToFilter('product_id', $id);
        $this->setCollection($data);
        return parent::_prepareCollection();
    }
    
    /**
     * Prepare Columns
     *
     * @return Columns
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'customer_id',
            [
                'header' => __('Customer ID'),
                'type' => 'text',
                'index' => 'customer_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'customer_email',
            [
                'header' => __('Customer Email'),
                'type' => 'text',
                'index' => 'customer_email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'customer_price',
            [
                'header' => __('Customer Price'),
                'type' => 'text',
                'index' => 'customer_price',
                'header_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'special_price',
            [
                'header' => __('Special Price'),
                'type' => 'text',
                'index' => 'special_price',
                'header_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'text',
                'renderer'  => \Mageants\PricePerCustomer\Block\Adminhtml\Grid\Renderer\Edit::class,
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
        $this->addColumn(
            'delete',
            [
                'header' => __('Delete'),
                'type' => 'text',
                'renderer'  => \Mageants\PricePerCustomer\Block\Adminhtml\Grid\Renderer\Delete::class,
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
        return parent::_prepareColumns();
    }
    /**
     * Get GridUrl
     *
     * @return Url
     */
    public function getGridUrl()
    {
        return $this->getUrl('pricepercustomer/index/grid', ['_current' => true]);
    }

    /**
     * Get CustomerData
     *
     * @return id
     */
    public function getcustomerdata()
    {
        return 'id';
    }

    /**
     * Get TabUrl
     *
     * @return Url
     */
    public function getTabUrl()
    {
        return $this->getUrl('pricepercustomer/index/customerpricegrid', ['_current' => true]);
    }

    /**
     * Get RowUrl
     *
     * @param  row $row
     * @return url
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * Can ShowTab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * IsHidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return true;
    }
    
    /**
     * IsAjaxLoaded
     *
     * @return boolean
     */
    public function isAjaxLoaded()
    {
        return true;
    }

    /**
     * Get TabLabel
     *
     * @return Customer Price
     */
    public function getTabLabel()
    {
        return __('Customer Price');
    }

    /**
     * Get TabTitle
     *
     * @return Customer Price
     */
    public function getTabTitle()
    {
        return __('Customer Price');
    }

    /**
     * Get BaseUrl
     *
     * @return Url
     */
    public function getBaseUrl()
    {
        return $this->getUrl();
    }
}
