<?php

namespace Mageants\DeliveryDate\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Mageants\DeliveryDate\Api\Data\GridInterface;

class OrderSave extends \Magento\Framework\Model\AbstractModel implements IdentityInterface, GridInterface
{
    public const CACHE_TAG = 'mageants_order_info';
    /**
     * @var string
     */
    protected $_cacheTag = 'mageants_order_info';
    /**
     * @var string
     */
    protected $_eventPrefix = 'mageants_order_info';
 
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Mageants\DeliveryDate\Model\ResourceModel\OrderSave::class);
    }
    /**
     * Get Identities function
     *
     * @return mixed
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }
 
    /**
     * Set id function
     *
     * @param int $Id
     * @return mixed
     */
    public function setId($Id)
    {
        return $this->setData(self::ID, $Id);
    }
 
    /**
     * Get Oreder Id.
     *
     * @return varchar
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER);
    }
 
    /**
     * Set Order Id.
     *
     * @param int $orderId
     * @return varchar
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER, $orderId);
    }
 
    /**
     * Get Display at.
     *
     * @return varchar
     */
    public function getConfigurationDisplayAt()
    {
        return $this->getData(self::DISPLAYAT);
    }

    /**
     * Set Display at Configuration.
     *
     * @param int $displayAt
     * @return varchar
     */
    public function setConfigurationDisplayAt($displayAt)
    {
        return $this->setData(self::DISPLAYAT, $displayAt);
    }
    /**
     * Get Date Format
     *
     * @return varchar
     */
    public function getDateFormat()
    {
        return $this->getData(self::FORMAT);
    }

    /**
     * Set Date Format
     *
     * @param mixed $format
     * @return mixed
     */
    public function setDateFormat($format)
    {
        return $this->setData(self::FORMAT, $format);
    }
}
