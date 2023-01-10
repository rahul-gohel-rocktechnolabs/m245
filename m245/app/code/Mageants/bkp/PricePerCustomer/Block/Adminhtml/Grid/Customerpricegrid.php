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
 * Customerpricegrid class for fetch Customer Price data for Grid
 */
class Customerpricegrid extends Extended
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
     * Construct
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param Currency $currency
     * @param StoreManager $storeManager
     * @param CustomerPriceCollection $customerpriceFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $registry,
        Currency $currency,
        StoreManager $storeManager,
        CustomerPriceCollection $customerpriceFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->currency = $currency;
        $this->_storeManager = $storeManager;
        $this->registry = $registry;
        $this ->customerpriceFactory = $customerpriceFactory;
        $this ->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Contruct
     *
     * @return Construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customerprice_grid_product_price');
        $this->setUseAjax(true);
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_product' => 1]);
        }
    }

    /**
     * ColumnFilterCollection
     *
     * @param column $column
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Get Current Product
     *
     * @return Current Product
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
    
    /**
     * Prepare Collection
     *
     * @return Collection
     */
    protected function _prepareCollection()
    {
        $id = $this->getRequest()->getParam('id');
        $product = $this->productCollectionFactory->create();
        $product->addFieldToSelect('*');
        $this->setCollection($product);
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
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'column_css_class' => 'col-id',
                'values' => $this->_getSelectedProducts(),
                //'field_name' => 'selectedproducts[]'
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'text',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'type' => 'text',
                'index' => 'name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'type_id',
            [
                'header' => __('Type'),
                'type' => 'text',
                'index' => 'type_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'type' => 'text',
                'index' => 'sku',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => true,
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'price',
                'index' => 'price',
                'header_css_class' => 'col-id',
                'sortable'  => true,
                'currency_code' => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
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
                'currency_code' => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
            ]
        );
        $this->addColumn(
            'customer_price',
            [
                'header' => __('Customer Price'),
                'type' => 'input',
                'index' => 'action',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => true,
                'renderer'  => \Mageants\PricePerCustomer\Block\Adminhtml\Grid\Renderer\Customerprice::class,
            ]
        );
        $this->addColumn(
            'customer_special_price',
            [
                'header' => __('Customer Special Price'),
                'type' => 'input',
                'index' => 'action',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => true,
                'renderer'  => \Mageants\PricePerCustomer\Block\Adminhtml\Grid\Renderer\Customerspecialprice::class,
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Get SelectedProducts
     *
     * @return Products
     */
    protected function _getSelectedProducts()
    {
        $customer_id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPost();
        $product_price_id =  [];
        $data = $this->customerpriceFactory;
        $data->addFieldToSelect('product_id');
        $data->addFieldToFilter('customer_id', $customer_id);
        $product_data = $data->getData();
        $pro_ids = '';
        foreach ($product_data as $key => $value) {
            $product_price_id[] = $value['product_id'];
        }
        return $this->getRequest()->getPost('selected', $product_price_id);
    }

    /**
     * Get GridUrl
     *
     * @return url
     */
    public function getGridUrl()
    {
        return $this->getUrl('pricepercustomer/index/customerproductprice', ['_current' => true]);
    }

    /**
     * Get Customerdata
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
     * @param row $row
     * @return Url
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * ShowTab
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
     * Get TabTable
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
