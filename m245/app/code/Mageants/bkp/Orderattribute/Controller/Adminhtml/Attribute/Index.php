<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Attribute;

use \Mageants\Orderattribute\Controller\Adminhtml;

class Index extends Adminhtml\Attribute
{
    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageants_Orderattribute::attributes_list');
        $resultPage->addBreadcrumb(__('Order Attribute'), __('Order Attribute'));
        $resultPage->addBreadcrumb(__('Manage Order Attributes'), __('Manage Order Attributes'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Order Attributes'));

        return $resultPage;
    }
}
