<?php

namespace Mageants\OrderApprovalRules\Api\Data;

interface GridInterface
{
    public const ID = 'id';
    public const ORDER_ID = 'order_id';
    public const INCREMENT_ID = 'increment_id';
    public const PURCHASEPOINT = 'store_id';
    public const PURCHASEDATE = 'purchase_date';
    public const BILLTONAME = 'bill_to_name';
    public const SHIPTONAME = 'ship_to_name';
    public const GRANDTOTALBASE = 'grand_total_base';
    public const GRANDTOTALPURCHASED = 'grand_total_purchased';
    public const STATUS = 'status';
    
    /**
     * Get the id
     *
     * @return void
     */
    public function getId();

    /**
     * Set the ID
     *
     * @param int $Id
     * @return void
     */
    public function setId($Id);

    /**
     * Get the order id
     *
     * @return void
     */
    public function getOrderId();

    /**
     * Set the order id
     *
     * @param int $order_id
     * @return void
     */
    public function setOrderId($order_id);

    /**
     * Get the increment id
     *
     * @return void
     */
    public function getIncrementId();

    /**
     * Set the increment id
     *
     * @param int $increment_id
     * @return void
     */
    public function setIncrementId($increment_id);
    
    /**
     * Get the purchase point
     *
     * @return void
     */
    public function getPurchasePoint();

    /**
     * Set the purchase point
     *
     * @param array $purchase_point
     * @return void
     */
    public function setPurchasePoint($purchase_point);

    /**
     * Get the purchase date
     *
     * @return void
     */
    public function getPurchaseDate();

    /**
     * Set the purchase date
     *
     * @param date $purchase_date
     * @return void
     */
    public function setPurchaseDate($purchase_date);

    /**
     * Get the billing name
     *
     * @return void
     */
    public function getBillToName();

    /**
     * Set the billing name
     *
     * @param string $bill_to_name
     * @return void
     */
    public function setBillToName($bill_to_name);

    /**
     * Get the ship name
     *
     * @return void
     */
    public function getShipToName();

    /**
     * Set the ship name
     *
     * @param string $ship_to_name
     * @return void
     */
    public function setShipToName($ship_to_name);
 
    /**
     * Get the base grand total
     *
     * @return void
     */
    public function getGrandTotalBase();

    /**
     * Set the base grand total
     *
     * @param float $grand_total_base
     * @return void
     */
    public function setGrandTotalBase($grand_total_base);

    /**
     * Get the purchased grand total
     *
     * @return void
     */
    public function getGrandTotalPurchased();

    /**
     * Set the purchased grand total
     *
     * @param float $grand_total_purchased
     * @return void
     */
    public function setGrandTotalPurchased($grand_total_purchased);

    /**
     * Get the status
     *
     * @return void
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string|int $status
     * @return void
     */
    public function setStatus($status);
}
