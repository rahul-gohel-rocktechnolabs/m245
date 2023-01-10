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

class AttributeOptionsProvider implements \Magento\Framework\Data\OptionSourceInterface
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
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    private $optionCollection;

    /**
     * @var ParentAttributeProvider
     */
    private $attributeProvider;

    /**
     * AttributeOptionsProvider constructor.
     *
     * @param \Magento\Framework\Registry                                                $coreRegistry
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $collectionFactory
     * @param ParentAttributeProvider                                                    $attributeProvider
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $collectionFactory,
        ParentAttributeProvider $attributeProvider
    ) {
        $this->optionCollection = $collectionFactory->create();
        $this->coreRegistry = $coreRegistry;
        $this->attributeProvider = $attributeProvider;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            if (!$this->getSelectedParentAttribute()) {
                return $this->options = [];
            }
            $this->options = $this->optionCollection
                ->setAttributeFilter($this->getSelectedParentAttribute())
            /* join default option labels */
                ->setStoreFilter(0, false)
                ->toOptionArray();
        }

        return $this->options;
    }

    /**
     * Get selected Attribute ID for load Options
     *
     * @return int|false
     */
    public function getSelectedParentAttribute()
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
     * Force set attribute ID
     *
     * @param string $attributeId
     * @return $this
     */
    public function setParentAttributeId($attributeId)
    {
        $this->parentAttributeId = $attributeId;
        return $this;
    }
}
