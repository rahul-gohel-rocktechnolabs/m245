<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel\RelationDetails;

use \Mageants\Orderattribute\Block\Checkout\LayoutProcessor;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @method \Mageants\Orderattribute\Api\Data\RelationDetailInterface[] getItems
 */
class Collection extends AbstractCollection
{
    /**
     * Get by relation
     *
     * @param int $relationId
     * @return $this
     */
    public function getByRelation($relationId)
    {
        $this->addFieldToFilter('relation_id', $relationId);
        return $this;
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(
            \Mageants\Orderattribute\Model\RelationDetails::class,
            \Mageants\Orderattribute\Model\ResourceModel\RelationDetails::class
        );
    }

    /**
     * Join EAV attribute codes
     *
     * @return $this
     */
    public function joinDependAttributeCode()
    {
        $this->getSelect()->joinInner(
            ['dependent' => $this->getTable('eav_attribute')],
            'main_table.dependent_attribute_id = dependent.attribute_id',
            ['dependent.attribute_code as dependent_attribute_code']
        )->joinInner(
            ['parent' => $this->getTable('eav_attribute')],
            'main_table.attribute_id = parent.attribute_id',
            ['parent.attribute_code as parent_attribute_code']
        );

        return $this;
    }

    /**
     * Prepare relations for attribute
     *
     * @param int $attributeId
     * @return array
     */
    public function getAttributeRelations($attributeId)
    {
        return $this->addFieldToFilter('main_table.attribute_id', $attributeId)
            ->joinDependAttributeCode()
            ->toRelationArray();
    }

    /**
     * To relation array
     *
     * Return array with keys:
     *   [attribute_name] - element name of parent attribute
     *   [dependent_name] - element name of depend attribute
     *   [option_value]   - value which Parent should have to show Depend
     *
     * @return array
     */
    public function toRelationArray()
    {
        $relations = [];
        foreach ($this->getItems() as $relation) {
            $relations[] = [
                'option_value' => $relation->getOptionId(),
                'attribute_name' => LayoutProcessor::getAttributeName($relation->getData('parent_attribute_code')),
                'dependent_name' => LayoutProcessor::getAttributeName($relation->getData('dependent_attribute_code')),
            ];
        }
        return $relations;
    }
}
