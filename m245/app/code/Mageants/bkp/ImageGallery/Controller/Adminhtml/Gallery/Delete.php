<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Adminhtml\Gallery;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Mageants\ImageGallery\Model\Gallery
     */
    public $gallery;

    /**
     * @param Action\Context $context
     * @param \Mageants\ImageGallery\Model\Gallery $gallery
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageants\ImageGallery\Model\Gallery $gallery,
        \Magento\Framework\Registry $registry
    ) {
        $this->gallery = $gallery;
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
                $image = $this->gallery->load($id);
                $image->delete();
                $this->messageManager->addSuccess(
                    __('Delete successfully !')
                );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
