<?php

namespace Mageants\DeliveryDate\Api\Data;

interface GridInterface
{
    public const ID = 'id';
    public const ORDER = 'order_id';
    public const DISPLAYAT = 'configuration_display_at';
    public const FORMAT = 'date_format';
 
    /**
     * Get Id function
     *
     * @return mixed
     */
    public function getId();
    /**
     * Set ID function
     *
     * @param int $Id
     * @return mixed
     */
    public function setId($Id);
 
    /**
     * Get Order Id function
     *
     * @return mixed
     */
    public function getOrderId();
    /**
     * Set Order Id function
     *
     * @param int $orderId
     * @return mixed
     */
    public function setOrderId($orderId);
 
    /**
     * Get Display At function
     *
     * @return bool
     */
    public function getConfigurationDisplayAt();
    /**
     * Set Display Position function
     *
     * @param int $displayAt
     * @return mixed
     */
    public function setConfigurationDisplayAt($displayAt);

    /**
     * Get Date Format function
     *
     * @return mixed
     */
    public function getDateFormat();
    /**
     * Set Date Format function
     *
     * @param mixed $format
     * @return mixed
     */
    public function setDateFormat($format);
}
