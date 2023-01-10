<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Controller\Adminhtml\Archive;

use Magento\Framework\Controller\ResultFactory;

class Creditmemos extends \Mageants\OrderArchive\Controller\Adminhtml\Archive
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageants_OrderArchive::creditmemos';

    /**
     * Creditmemos view page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Mageants_OrderArchive::sales_archive_creditmemos');
        $resultPage->getConfig()->getTitle()->prepend(__('Credit Memos'));

        return $resultPage;
    }
}
