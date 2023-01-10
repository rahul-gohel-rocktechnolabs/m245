<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Order\Attributes;

class Edit extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Execute
     */
    public function execute()
    {
        $order = $this->_initOrder();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()
            ->prepend(__(
                'Edit Attributes For The Order #%1',
                $order->getIncrementId()
            ));
        return $resultPage;
    }
}
