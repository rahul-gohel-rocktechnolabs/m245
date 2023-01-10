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
 * Searchgrid class for search Customer data from Grid
 */
class Searchgrid extends Extended
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
     * [$_customer description]
     * @var [type]
     */
    protected $_customer;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param Currency $currency
     * @param StoreManager $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customers
     * @param CustomerPriceCollection $customerpriceFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $registry,
        Currency $currency,
        StoreManager $storeManager,
        \Magento\Customer\Model\CustomerFactory $customers,
        CustomerPriceCollection $customerpriceFactory,
        array $data = []
    ) {
        $this->currency = $currency;
        $this->_storeManager = $storeManager;
        $this->registry = $registry;
        $this->_customer = $customers;
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
        $this->setId('searchgrid');
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
        $data = $this->_customer->create()->getCollection();
        $data->addFieldToSelect("*");
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
            'entity_id',
            [
                'header' => __('Customer ID'),
                'type' => 'text',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'email',
            [
                'header' => __('Customer Email'),
                'type' => 'text',
                'index' => 'email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'firstname',
            [
                'header' => __('First Name'),
                'type' => 'text',
                'index' => 'firstname',
                'header_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'lastname',
            [
                'header' => __('Last Name'),
                'type' => 'text',
                'index' => 'lastname',
                'header_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'edit',
            [
                'header' => __('Select'),
                'type' => 'text',
                'renderer'  => \Mageants\PricePerCustomer\Block\Adminhtml\Grid\Renderer\Select::class,
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
        return $this->getUrl('pricepercustomer/index/searchgrid', ['_current' => true]);
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
        return $this->getUrl('pricepercustomer/index/searchgrid', ['_current' => true]);
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
