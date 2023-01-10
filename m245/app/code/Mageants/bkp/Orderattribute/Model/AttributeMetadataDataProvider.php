<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model;

use Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\Collection;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

class AttributeMetadataDataProvider
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory
     */
    private $orderAttributesCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * Initialize data provider with data source
     *
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory $attrFormCollectionFactory
     * @param \Magento\Store\Model\StoreManager $storeManager
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory $attrFormCollectionFactory,
        \Magento\Store\Model\StoreManager $storeManager
    ) {
        $this->eavConfig = $eavConfig;
        $this->orderAttributesCollectionFactory = $attrFormCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Get attribute model for a given entity type and code
     *
     * @param string $entityType
     * @param string $attributeCode
     * @return false|AbstractAttribute
     */
    public function getAttribute($entityType, $attributeCode)
    {
        return $this->eavConfig->getAttribute($entityType, $attributeCode);
    }

    /**
     * Load Attributes For Edit Form By Store Id
     *
     * @param int $storeId
     * @return Collection
     */
    public function loadAttributesForEditFormByStoreId($storeId)
    {
        $attributesCollection = $this->loadAttributesCollection();
        $attributesCollection->addStoreFilter($storeId);
        return $attributesCollection;
    }

    /**
     * Load Attributes For Edit Form By Store Id
     *
     * @param int $storeId
     * @return Collection
     */
    public function loadAttributesForCreateOrderFormByStoreId($storeId)
    {
        $attributesCollection = $this->loadAttributesBackendCollection();
        $attributesCollection->addStoreFilter($storeId);
        return $attributesCollection;
    }

    /**
     * Load attributes for pdf
     *
     * @param int $storeId
     * @return Collection
     */
    public function loadAttributesForPdf($storeId)
    {
        $attributesCollection = $this->loadAttributesCollection();
        $attributesCollection->addStoreFilter($storeId);
        $attributesCollection->addFieldToFilter('include_pdf', 1);
        return $attributesCollection;
    }

    /**
     * Load attributes for print html
     *
     * @param int $storeId
     * @return Collection
     */
    public function loadAttributesForPrintHtml($storeId)
    {
        $attributesCollection = $this->loadAttributesCollection();
        $attributesCollection->addStoreFilter($storeId);
        $attributesCollection->addFieldToFilter('include_html_print_order', 1);
        return $attributesCollection;
    }

    /**
     * Load attributes frontend collection
     *
     * @param int $storeId
     * @return Collection
     */
    public function loadAttributesFrontendCollection($storeId)
    {
        $attributesCollection = $this->loadAttributesCollection();
        $this->addShippingMethodsToSelect($attributesCollection);
        $attributesCollection->addStoreFilter($storeId);
        $attributesCollection->addFieldToFilter('is_visible_on_front', 1);

        return $attributesCollection;
    }

    /**
     * Load attributes for api
     *
     * @param int $storeId
     * @return Collection
     */
    public function loadAttributesForApi($storeId)
    {
        $attributesCollection = $this->loadAttributesCollection();
        $this->addShippingMethodsToSelect($attributesCollection);
        $attributesCollection->addStoreFilter($storeId);
        $attributesCollection->addFieldToFilter('include_api', 1);

        return $attributesCollection;
    }

    /**
     * Add shipping methods to select
     *
     * @param Collection $collection
     * @return Collection
     */
    public function addShippingMethodsToSelect($collection)
    {
        $collection->getSelect()->joinLeft(
            ['sm' => $collection->getTable('mageants_orderattribute_shipping_methods')],
            'main_table.attribute_id = sm.attribute_id',
            'GROUP_CONCAT(sm.shipping_method) as shipping_methods'
        );
        $collection->addAttributeGrouping();

        return $collection;
    }

    /**
     * Add relations to select
     *
     * @param Collection $collection
     * @return Collection
     */
    public function addRelationsToSelect($collection)
    {
        $collection->getSelect()->joinLeft(
            ['relation' => $collection->getTable('mageants_orderattribute_attributes_relation_details')],
            'main_table.attribute_id = sm.attribute_id',
            'GROUP_CONCAT(sm.shipping_method) as shipping_methods'
        );
        $collection->addAttributeGrouping();

        return $collection;
    }

    /**
     * Load attributes for order grid
     *
     * @return Collection
     */
    public function loadAttributesForOrderGrid()
    {
        $attributesCollection = $this->loadAttributesCollection();
        $attributesCollection->addFieldToFilter('is_used_in_grid', 1);
        return $attributesCollection;
    }

    /**
     * Load attributes backend collection
     *
     * @return Collection
     */
    public function loadAttributesBackendCollection()
    {
        $attributesCollection = $this->loadAttributesCollection();
        $attributesCollection->addFieldToFilter('is_visible_on_back', 1);

        return $attributesCollection;
    }

    /**
     * Load attributes with default value collection
     *
     * @return Collection
     */
    public function loadAttributesWithDefaultValueCollection()
    {
        $attributesCollection = $this->loadAttributesCollection();
        $attributesCollection->addFieldToFilter('apply_default', 1);

        return $attributesCollection;
    }

    /**
     * Load attributes collection
     *
     * @return Collection
     */
    public function loadAttributesCollection()
    {
        /**
         * @var Collection $attributesCollection
         */
        $attributesCollection = $this->orderAttributesCollectionFactory->create();
        $attributesCollection->addFieldToFilter('is_user_defined', 1);
        $attributesCollection->setOrder('sorting_order', 'ASC');

        return $attributesCollection;
    }

    /**
     * Load Attributes For Edit Form By Store Id
     *
     * @param int $storeId
     * @return Collection
     */
    public function countOrderAttributes()
    {
        $attributesCollection = $this->loadAttributesCollection();

        return $attributesCollection->getSize();
    }

    /**
     * Get all attribute codes for a given entity type and attribute set
     *
     * @param string $entityType
     * @param int $attributeSetId
     * @param string|null $storeId
     * @return array Attribute codes
     */
    public function getAllAttributeCodes($entityType, $attributeSetId = 0, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $object = new \Magento\Framework\DataObject(
            [
                'store_id' => $storeId,
                'attribute_set_id' => $attributeSetId,
            ]
        );
        return $this->eavConfig->getEntityAttributeCodes($entityType, $object);
    }
}
