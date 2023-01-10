<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\Relation;

use Mageants\Orderattribute\Controller\RegistryConstants;
use Mageants\Orderattribute\Model\Relation;

class DependentAttributeProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var null|array
     */
    protected $options = null;

    /**
     * @var null|int
     */
    protected $parentAttributeId = null;

    /**
     * @var null|int[]
     */
    protected $excludedAttributeIds = null;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var $attributeProvider
     */
    private $attributeProvider;

    /**
     * @var \Mageants\Orderattribute\Model\AttributeMetadataDataProvider
     */
    private $attributeMetadataProvider;

    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\Repository
     */
    private $repository;
    /**
     * @var \Mageants\Orderattribute\Model\ResourceModel\RelationDetails\CollectionFactory
     */
    private $relationCollectionFactory;

    /**
     * DependentAttributeProvider constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataProvider
     * @param ParentAttributeProvider $attributeProvider
     * @param \Mageants\Orderattribute\Model\Order\Attribute\Repository $repository
     * @param \Mageants\Orderattribute\Model\ResourceModel\RelationDetails\CollectionFactory $relationCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataProvider,
        ParentAttributeProvider $attributeProvider,
        \Mageants\Orderattribute\Model\Order\Attribute\Repository $repository,
        \Mageants\Orderattribute\Model\ResourceModel\RelationDetails\CollectionFactory $relationCollectionFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->attributeProvider = $attributeProvider;
        $this->attributeMetadataProvider = $attributeMetadataProvider;
        $this->repository = $repository;
        $this->relationCollectionFactory = $relationCollectionFactory;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            if (!$this->getParentAttributeId()) {
                return $this->options;
            }
            $parentAttribute = $this->repository->get($this->getParentAttributeId());

            $collection = $this->attributeMetadataProvider
                ->loadAttributesCollection()
                ->addFieldToFilter('main_table.attribute_id', ['nin' => $this->getExcludedIds()])
                ->addFieldToFilter('additional_table.checkout_step', $parentAttribute->getCheckoutStep());

            foreach ($collection as $attribute) {
                $label = $attribute->getFrontendLabel();
                if (!$attribute->getIsVisibleOnFront()) {
                    $label .= ' - ' . __('Not Visible');
                }
                $this->options[] = [
                    'value' => $attribute->getAttributeId(),
                    'label' => $label,
                ];
            }
        }

        return $this->options;
    }

    /**
     * Get parent attribute id
     *
     * Get Parent Attribute ID
     * Dependent attribute should not be like parent attribute
     *
     * @return int|false
     */
    protected function getParentAttributeId()
    {
        if ($this->parentAttributeId === null) {
            /** @var Relation $relation */
            $relation = $this->coreRegistry->registry(RegistryConstants::CURRENT_RELATION_ID);
            if ($relation instanceof Relation && $relation->getAttributeId()) {
                $this->parentAttributeId = $relation->getAttributeId();
            } else {
                $this->parentAttributeId = false;
                // If relation new then take first attribute from dropdown "Parent Attribute"
                $attribute = $this->attributeProvider->getDefaultSelected();
                if ($attribute) {
                    $this->parentAttributeId = $attribute['value'];
                }
            }
        }
        return $this->parentAttributeId;
    }

    /**
     * Get excluded ids
     *
     * Return Excluded Attribute IDs which can't be as Dependent attribute for this relation.
     * Exclude attributes which already have relations as parent for avoid loop
     *
     * @return int[]|null
     */
    protected function getExcludedIds()
    {
        if ($this->excludedAttributeIds === null) {
            $parentId = $this->getParentAttributeId();
            /** @var \Mageants\Orderattribute\Model\ResourceModel\RelationDetails\Collection $collection */
            $collection = $this->relationCollectionFactory->create();
            $collection->addFieldToFilter('dependent_attribute_id', $parentId);
            $this->excludedAttributeIds = array_unique($collection->getColumnValues('attribute_id'));
            $this->excludedAttributeIds[] = $parentId;
        }

        return $this->excludedAttributeIds;
    }

    /**
     * Force set attribute ID
     *
     * @param int $parentAttributeId
     * @return $this
     */
    public function setParentAttributeId($parentAttributeId)
    {
        $this->parentAttributeId = $parentAttributeId;
        return $this;
    }
}
