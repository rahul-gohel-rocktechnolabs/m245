<?php
/**
 * @category Mageants OrderApprovalRules
 * @package Mageants_OrderApprovalRules
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants <support@mageants.com>
 */

namespace Mageants\OrderApprovalRules\Controller\Adminhtml\Rules;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Mageants\OrderApprovalRules\Model\PendingorderFactory $pendingorder
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Mageants\OrderApprovalRules\Model\PendingorderFactory $pendingorder
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_pendingorder = $pendingorder;
    }

    /**
     * Checks allowed pending orders or not.
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_OrderApprovalRules::pending_orders');
    }
    
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Pending Orders For Approval'), __('Pending Orders For Approval'));
        $resultPage->getConfig()->getTitle()->prepend(__('Pending Orders For Approval'));
        return $resultPage;
    }
}
