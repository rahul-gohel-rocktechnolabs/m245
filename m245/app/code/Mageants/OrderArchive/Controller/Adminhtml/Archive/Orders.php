<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Controller\Adminhtml\Archive;

use Magento\Framework\Controller\ResultFactory;

class Orders extends \Mageants\OrderArchive\Controller\Adminhtml\Archive
{
    /**
     * Orders view page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Mageants_OrderArchive::sales_archive_orders');
        $resultPage->getConfig()->getTitle()->prepend(__('Orders'));

        return $resultPage;
    }
}
