<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Model\ResourceModel\Order;

/**
 * Order archive collection
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * Generate select based on order grid select for getting archived order fields.
     *
     * @param \Magento\Framework\DB\Select $gridSelect
     * @return \Magento\Framework\DB\Select
     */
    public function getOrderGridArchiveSelect(\Magento\Framework\DB\Select $gridSelect)
    {
        $select = clone $gridSelect;
        $select->reset('from');
        $select->from(['main_table' => $this->getTable('mageants_sales_order_grid_archive')], []);
        return $select;
    }
}
