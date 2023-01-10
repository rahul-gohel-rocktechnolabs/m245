<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Adminhtml\Video;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Mageants\ImageGallery\Model\Video
     */
    public $video;

    /**
     * @param Action\Context $context
     * @param \Mageants\ImageGallery\Model\Video $video
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageants\ImageGallery\Model\Video $video,
        \Magento\Framework\Registry $registry
    ) {
        $this->video = $video;
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
                $image = $this->video->load($id);
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
