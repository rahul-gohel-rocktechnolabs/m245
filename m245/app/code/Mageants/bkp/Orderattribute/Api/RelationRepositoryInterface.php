<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Api;

/**
 * Interface RelationRepositoryInterface
 *
 * @api
 */
interface RelationRepositoryInterface
{
    /**
     * Save
     *
     * @param \Mageants\Orderattribute\Api\Data\RelationInterface $relation
     * @return \Mageants\Orderattribute\Api\Data\RelationInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Mageants\Orderattribute\Api\Data\RelationInterface $relation);

    /**
     * Get
     *
     * @param int $relationId
     * @return \Mageants\Orderattribute\Api\Data\RelationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($relationId);

    /**
     * Delete
     *
     * @param \Mageants\Orderattribute\Api\Data\RelationInterface $relation
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Mageants\Orderattribute\Api\Data\RelationInterface $relation);

    /**
     * Delete by id
     *
     * @param int $ruleId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($ruleId);
}
