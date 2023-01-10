<?php

namespace Mageants\OrderApprovalRules\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Mageants\OrderApprovalRules\Api\Data\GridInterface;

class Pendingorder extends \Magento\Framework\Model\AbstractModel implements IdentityInterface, GridInterface
{
    public const CACHE_TAG = 'mageants_orderapprovalrules_order';

    /**
     * define table name
     *
     * @var string
     */
    protected $_cacheTag = 'mageants_orderapprovalrules_order';

    /**
     * Define event prefix if you like set event observer
     *
     * @var string
     */
    protected $_eventPrefix = 'mageants_orderapprovalrules_order';
 
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Mageants\OrderApprovalRules\Model\ResourceModel\Pendingorder::class);
    }

    /**
     * Get Identities
     *
     * @return void
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set Id
     *
     * @param int $Id
     * @return int
     */
    public function setId($Id)
    {
        return $this->setData(self::ID, $Id);
    }

    /**
     * Get order_id.
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Set order_id
     *
     * @param int $order_id
     * @return void
     */
    public function setOrderId($order_id)
    {
        return $this->setData(self::ORDER_ID, $order_id);
    }

    /**
     * Get increment id.
     *
     * @return int
     */
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }
    /**
     * Set increment id
     *
     * @param int $increment_id
     * @return void
     */
    public function setIncrementId($increment_id)
    {
        return $this->setData(self::INCREMENT_ID, $increment_id);
    }

    /**
     * Get PURCHASEPOINT
     *
     * @return varchar
     */
    public function getPurchasepoint()
    {
        return $this->getData(self::PURCHASEPOINT);
    }
    /**
     * Set purchase point
     *
     * @param int $purchase_point
     * @return void
     */
    public function setPurchasepoint($purchase_point)
    {
        return $this->setData(self::PURCHASEPOINT, $purchase_point);
    }
    
    /**
     * Get PURCHASEDATE
     *
     * @return varchar
     */
    public function getPurchaseDate()
    {
        return $this->getData(self::PURCHASEDATE);
    }

    /**
     * Set PURCHASEDATE
     *
     * @param date $purchase_date
     * @return varchar
     */
    public function setPurchaseDate($purchase_date)
    {
        return $this->setData(self::PURCHASEDATE, $purchase_date);
    }

    /**
     * Get BILLTONAME.
     *
     * @return varchar
     */
    public function getBillToName()
    {
        return $this->getData(self::BILLTONAME);
    }
    /**
     * Set BILLTONAME
     *
     * @param varchar $bill_to_name
     * @return varchar
     */
    public function setBillToName($bill_to_name)
    {
        return $this->setData(self::BILLTONAME, $bill_to_name);
    }

    /**
     * Get SHIPTONAME.
     *
     * @return varchar
     */
    public function getShipToName()
    {
        return $this->getData(self::SHIPTONAME);
    }

    /**
     * Set SHIPTONAME
     *
     * @param varchar $ship_to_name
     * @return varchar
     */
    public function setShipToName($ship_to_name)
    {
        return $this->setData(self::SHIPTONAME, $ship_to_name);
    }

    /**
     * Get GRANDTOTALBASE.
     *
     * @return varchar
     */
    public function getGrandTotalBase()
    {
        return $this->getData(self::GRANDTOTALBASE);
    }
    /**
     * Set GRANDTOTALBASE
     *
     * @param varchar $grand_total_base
     * @return varchar
     */
    public function setGrandTotalBase($grand_total_base)
    {
        return $this->setData(self::GRANDTOTALBASE, $grand_total_base);
    }

    /**
     * Get GRANDTOTALPURCHASED
     *
     * @return varchar
     */
    public function getGrandTotalPurchased()
    {
        return $this->getData(self::GRANDTOTALPURCHASED);
    }

    /**
     * Set GRANDTOTALPURCHASED
     *
     * @param varchar $grand_total_purchased
     * @return varchar
     */
    public function setGrandTotalPurchased($grand_total_purchased)
    {
        return $this->setData(self::GRANDTOTALPURCHASED, $grand_total_purchased);
    }

    /**
     * Get STATUS
     *
     * @return varchar
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set STATUS
     *
     * @param varchar $status
     * @return varchar
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
