<?php
/**
 * @category Mageants OrderApprovalRules
 * @package Mageants_OrderApprovalRules
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants <support@mageants.com>
 */

namespace Mageants\OrderApprovalRules\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Magento\Framework\Serialize\SerializerInterface;
use Mageants\OrderApprovalRules\Model\OrderApprovalRules;
use Magento\Backend\Model\Session;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var OrderApprovalRules
     */
    protected $OrderApprovalRules;

    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param SerializerInterface $serializer
     * @param OrderApprovalRules $OrderApprovalRules
     * @param Session $backendSession
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        SerializerInterface $serializer,
        OrderApprovalRules $OrderApprovalRules,
        Session $backendSession
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->serializer = $serializer;
        $this->OrderApprovalRules = $OrderApprovalRules;
        $this->backendSession = $backendSession;
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageants_OrderApprovalRules::order_approval_rules');
        return $resultPage;
    }

    /**
     * Edit OrderApprovalRules
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        //$model = $this->_objectManager->create('Mageants\OrderApprovalRules\Model\OrderApprovalRules');
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $this->OrderApprovalRules->load($id);

            if (!$this->OrderApprovalRules->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->backendSession->getFormData(true);
        if (!empty($data)) {
            $this->OrderApprovalRules->setData($data);
        }
        $this->_coreRegistry->register('mageants_orderapprovalrules', $this->OrderApprovalRules);
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        
        $resultPage->addBreadcrumb(
            $id ? __('Edit Rule') : __('New Rule'),
            $id ? __('Edit Rule') : __('New Rule')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('OrderApprovalRules'));
        $resultPage->getConfig()->getTitle()
            ->prepend($this->OrderApprovalRules->getId() ? $this->OrderApprovalRules->getTitle() : __('New Rule'));

        return $resultPage;
    }
}
