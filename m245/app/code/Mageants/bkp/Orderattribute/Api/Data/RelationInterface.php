<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Api\Data;

interface RelationInterface
{
    public const RELATION_ID = 'relation_id';

    public const NAME = 'name';

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
     * Returns Relation name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get details
     *
     * @return \Mageants\Orderattribute\Api\Data\RelationDetailInterface[]
     */
    public function getDetails();

    /**
     * Set details
     *
     * @param \Mageants\Orderattribute\Api\Data\RelationDetailInterface[] $relationDetails
     * @return $this
     */
    public function setDetails($relationDetails);
}
