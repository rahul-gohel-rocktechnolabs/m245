<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Api\Data;

interface RelationDetailInterface
{
    public const ID = 'id';

    public const ATTRIBUTE_ID = 'attribute_id';

    public const OPTION_ID = 'option_id';

    public const DEPENDENT_ATTRIBUTE_ID = 'dependent_attribute_id';

    public const RELATION_ID = 'relation_id';

    /**
     * Returns Detail Relation ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $relationId
     * @return $this
     */
    public function setId($relationId);

    /**
     * Returns Relation ID
     *
     * @return int
     */
    public function getRelationId();

    /**
     * Set relation id
     *
     * @param int $relationId
     * @return $this
     */
    public function setRelationId($relationId);

    /**
     * Returns EAV Attribute ID
     *
     * @return int
     */
    public function getAttributeId();

    /**
     * Set attribute id
     *
     * @param int $attributeId
     * @return $this
     */
    public function setAttributeId($attributeId);

    /**
     * Returns Attribute Option ID
     *
     * @return int
     */
    public function getOptionId();

    /**
     * Set option id
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId);

    /**
     * Returns Dependent EAV Attribute ID
     *
     * @return int
     */
    public function getDependentAttributeId();

    /**
     * Set dependent attribute id
     *
     * @param int $attributeId
     * @return $this
     */
    public function setDependentAttributeId($attributeId);
}
