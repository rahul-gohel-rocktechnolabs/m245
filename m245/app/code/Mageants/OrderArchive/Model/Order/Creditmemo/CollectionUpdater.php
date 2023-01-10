<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\OrderArchive\Model\Order\Creditmemo;

class CollectionUpdater implements \Magento\Framework\View\Layout\Argument\UpdaterInterface
{
    /**
     * @var \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
     */
    protected $orderItem;

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\AbstractOrder $orderItem
     */
    public function __construct(\Magento\Sales\Block\Adminhtml\Order\AbstractOrder $orderItem)
    {
        $this->orderItem = $orderItem;
    }

    /**
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if ($this->orderItem->getOrder()->getIsArchived()) {
            $argument->setMainTable('magento_sales_creditmemo_grid_archive');
        }
        return $argument;
    }
}
