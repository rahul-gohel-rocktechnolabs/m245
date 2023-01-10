<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Adminhtml\Category;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Mageants\ImageGallery\Model\Category
     */
    public $category;
    /**
     * @var \Magento\Backend\Model\Session
     */
    public $session;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mageants\ImageGallery\Model\Category $category
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mageants\ImageGallery\Model\Category $category,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->category = $category;
        $this->session = $session;
        parent::__construct($context);
    }
    
    /**
     * Init action
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageants_All::mageants')
            ->addBreadcrumb(__('Category'), __('Category'))
            ->addBreadcrumb(__('Manage Category'), __('Manage Category'));
        return $resultPage;
    }

    /**
     * Execute
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        
        $model = $this->category;
        
        $registryObject = $this->_coreRegistry;
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This image category is no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $registryObject->register('imagegalley_category', $model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Image Category') : __('New Image Category'),
            $id ? __('Edit Image Category') : __('New Image Category')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Image Categories'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? "Edit Image Category" : __('New Image Category'));

        return $resultPage;
    }
}
