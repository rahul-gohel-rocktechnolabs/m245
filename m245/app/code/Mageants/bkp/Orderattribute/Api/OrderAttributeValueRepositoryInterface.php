<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Api;

/**
 * Interface for Managing Mageants Order Attribute values
 * @api
 */
interface OrderAttributeValueRepositoryInterface
{
    /**
     * Loads a specified order.
     *
     * @param int $orderId The order ID.
     *
     * @return \Mageants\Orderattribute\Api\Data\OrderAttributeValueInterface
     */
    public function getByOrder($orderId);

    /**
     * Performs persist operations for a specified order.
     *
     * @param \Mageants\Orderattribute\Api\Data\OrderAttributeValueInterface $entity
     *
     * @return \Mageants\Orderattribute\Api\Data\OrderAttributeValueInterface|bool
     */
    public function save(\Mageants\Orderattribute\Api\Data\OrderAttributeValueInterface $entity);

    /**
     * Performs persist operations for a specified order.
     *
     * @param \Mageants\Orderattribute\Api\Data\OrderAttributeValueInterface $entity
     *
     * @return \Mageants\Orderattribute\Api\Data\OrderAttributeValueInterface|bool
     */
    public function saveApi(\Mageants\Orderattribute\Api\Data\OrderAttributeValueInterface $entity);
}
