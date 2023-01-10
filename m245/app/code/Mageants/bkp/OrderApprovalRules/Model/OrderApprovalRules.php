<?php
/**
 * @category Mageants OrderApprovalRules
 * @package Mageants_OrderApprovalRules
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\OrderApprovalRules\Model;

class OrderApprovalRules extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Mageants\OrderApprovalRules\Model\ResourceModel\OrderApprovalRules::class);
    }
}
