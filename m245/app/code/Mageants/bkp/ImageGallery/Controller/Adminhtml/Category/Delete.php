<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Adminhtml\Category;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Mageants\ImageGallery\Model\Category
     */
    public $category;

    /**
     * @param Action\Context $context
     * @param \Mageants\ImageGallery\Model\Category $category
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageants\ImageGallery\Model\Category $category,
        \Magento\Framework\Registry $registry
    ) {
        $this->category = $category;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
    /**
     * Execute
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
                $banner = $this->category->load($id);
                $banner->delete();
                $this->messageManager->addSuccess(
                    __('Delete successfully !')
                );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
