<?php

namespace Mageants\OrderApprovalRules\Model\ResourceModel\Pendingorder;

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
            \Mageants\OrderApprovalRules\Model\Pendingorder::class,
            \Mageants\OrderApprovalRules\Model\ResourceModel\Pendingorder::class
        );
    }
}
