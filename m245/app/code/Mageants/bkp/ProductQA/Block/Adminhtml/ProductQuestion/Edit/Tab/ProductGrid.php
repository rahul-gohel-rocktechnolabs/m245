<?php
 /**
  * @category  Mageants BannerSlider
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Block\Adminhtml\ProductQuestion\Edit\Tab;

class ProductGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    
    /**
     * @var  array
     */
    protected $question_id = false;
    /**
     * @var  array
     */
    protected $_prod_ids;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var  \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;
    /**
     *
     * @param \Magento\Backend\Block\Template\Context                        $context                  [description]
     * @param \Magento\Backend\Helper\Data                                   $backendHelper            [description]
     * @param \Magento\Framework\Registry                                    $registry                 [description]
     * @param \Magento\Framework\ObjectManagerInterface                      $objectManager            [description]
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory [description]
     * @param array                                                          $data                     [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        if (!isset($data['product_ids'])) {
            $data['product_ids'] = [];
        }
        
        if (isset($data['question_id'])) {
            $this->question_id = $data['question_id'];
        }
        
        $this->setSelectedProductsIds($data['product_ids']);
        
        $this->productCollectionFactory = $productCollectionFactory;
        
        $this->_objectManager = $objectManager;
        
        $this->registry = $registry;
        
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        
        $this->setId('productsGrid');
        
        $this->setDefaultSort('entity_id');
        
        $this->setDefaultDir('DESC');
        
        $this->setSaveParametersInSession(true);
        
        $this->setUseAjax(true);
    }

    /**
     * Add Column Filter To Collection
     *
     * @param int|string $column
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
     * Prepare collection
     */
    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create();
        
        $collection->addAttributeToSelect('name');
        
        $collection->addAttributeToSelect('sku');
        
        $collection->addAttributeToSelect('price');
        
        if (count($this->getSelectedProducts())) {
            $collection->addIdFilter($this->getSelectedProducts());
        }
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    /**
     * PrepareColumns
     */
    protected function _prepareColumns()
    {
        
        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'radio',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->getSelectedProducts(),
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '50px',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsgrid', ['_current' => true]);
    }

    /**
     * Get RowUrl
     *
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
   /**
    * Set SelectedProductsIds
    *
    * @param [type] $ids
    */
    public function setSelectedProductsIds($ids)
    {
        $this->_prod_ids = $ids;
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        $selected = $this->_prod_ids;

        if (!is_array($selected)) {
            $selected = [];
        }
        
        return array_filter($selected);
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return true;
    }
}
