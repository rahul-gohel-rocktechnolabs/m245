<?php

namespace Mageants\DeliveryDate\Model\ResourceModel\OrderSave;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            \Mageants\DeliveryDate\Model\OrderSave::class,
            \Mageants\DeliveryDate\Model\ResourceModel\OrderSave::class
        );
    }
}
