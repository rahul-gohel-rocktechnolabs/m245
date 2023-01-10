<?php
/**
 * Generate the tab in form
 *
 * @category Mageants OrderApprovalRules
 * @package Mageants_OrderApprovalRules
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants <support@mageants.com>
 */

namespace Mageants\OrderApprovalRules\Block\Adminhtml\Grid\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Set the id and title
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderapprovalrule_grid_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Order Approval Rule Information'));
    }
}
