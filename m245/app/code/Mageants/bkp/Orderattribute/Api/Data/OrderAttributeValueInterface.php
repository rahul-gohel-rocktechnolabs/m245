<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Api\Data;

/**
 * @api
 */
interface OrderAttributeValueInterface
{
    public const ORDER_ENTITY_ID = 'order_entity_id';
    public const CUSTOMER_ID = 'customer_id';
    public const CREATED_AT = 'created_at';
    public const ID = 'id';

    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get order entity id
     *
     * @return int
     */
    public function getOrderEntityId();

    /**
     * Set order entity id
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderEntityId($orderId);

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $date
     * @return $this
     */
    public function setCreatedAt($date);

    /**
     * Get attributes
     *
     * @param null|int $storeId
     * @return \Mageants\Orderattribute\Api\Data\OrderAttributeDataInterface[]
     */
    public function getAttributes($storeId = null);

    /**
     * Set attributes
     *
     * @param \Mageants\Orderattribute\Api\Data\OrderAttributeDataInterface[] $attributes
     * @return $this
     */
    public function setAttributes($attributes);
}
