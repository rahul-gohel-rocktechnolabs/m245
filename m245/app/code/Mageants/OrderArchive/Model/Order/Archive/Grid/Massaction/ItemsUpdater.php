<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Model\Order\Archive\Grid\Massaction;

class ItemsUpdater extends \Mageants\OrderArchive\Model\Order\Grid\Massaction\ItemsUpdater implements
    \Magento\Framework\View\Layout\Argument\UpdaterInterface
{
    /**
     * Remove massaction items in case they disallowed for user
     *
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if ($this->_salesArchiveConfig->isArchiveActive()) {
            if ($this->_authorizationModel->isAllowed('Magento_Sales::cancel') === false) {
                unset($argument['cancel_order']);
            }
            if ($this->_authorizationModel->isAllowed('Magento_Sales::hold') === false) {
                unset($argument['hold_order']);
            }
            if ($this->_authorizationModel->isAllowed('Magento_Sales::unhold') === false) {
                unset($argument['unhold_order']);
            }
            if ($this->_authorizationModel->isAllowed('Mageants_OrderArchive::remove') === false) {
                unset($argument['remove_order_from_archive']);
            }
        }

        return $argument;
    }
}
