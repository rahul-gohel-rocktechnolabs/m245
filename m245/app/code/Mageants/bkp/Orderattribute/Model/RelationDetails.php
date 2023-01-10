<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model;

use Mageants\Orderattribute\Api\Data\RelationDetailInterface;

class RelationDetails extends \Magento\Framework\Model\AbstractModel implements RelationDetailInterface
{
    /**
     * Construct
     */
    public function _construct()
    {
        $this->_init(\Mageants\Orderattribute\Model\ResourceModel\RelationDetails::class);
    }

    /**
     * Returns EAV Attribute ID
     *
     * @return int
     */
    public function getAttributeId()
    {
        return $this->_getData(self::ATTRIBUTE_ID);
    }

    /**
     * Set attribute id
     *
     * @param int $attributeId
     * @return $this
     */
    public function setAttributeId($attributeId)
    {
        $this->setData(self::ATTRIBUTE_ID, $attributeId);
        return $this;
    }

    /**
     * Returns Attribute Option ID
     *
     * @return int
     */
    public function getOptionId()
    {
        return $this->_getData(self::OPTION_ID);
    }

    /**
     * Set option id
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId)
    {
        $this->setData(self::OPTION_ID, $optionId);
        return $this;
    }

    /**
     * Returns Dependent EAD Attribute ID
     *
     * @return int
     */
    public function getDependentAttributeId()
    {
        return $this->_getData(self::DEPENDENT_ATTRIBUTE_ID);
    }

    /**
     * Set dependent attribute id
     *
     * @param int $attributeId
     * @return $this
     */
    public function setDependentAttributeId($attributeId)
    {
        $this->setData(self::DEPENDENT_ATTRIBUTE_ID, $attributeId);
        return $this;
    }

    /**
     * Returns relation id
     *
     * @return int
     */
    public function getRelationId()
    {
        return $this->_getData(self::RELATION_ID);
    }

    /**
     * Set relation id
     *
     * @param int $relationId
     * @return $this
     */
    public function setRelationId($relationId)
    {
        $this->setData(self::RELATION_ID, $relationId);

        return $this;
    }
}
