<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Adminhtml\Category;

class Videos extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;
    /**
     * @var \Mageants\ImageGallery\Model\ResourceModel\Video\CollectionFactory
     */
    protected $_videoCollectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Mageants\ImageGallery\Model\ResourceModel\Video\CollectionFactory $videoCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Mageants\ImageGallery\Model\ResourceModel\Video\CollectionFactory $videoCollectionFactory
    ) {
        parent::__construct($context);
        $this->_jsHelper = $jsHelper;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_videoCollectionFactory = $videoCollectionFactory;
    }
    /**
     * Is allowed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_ImageGallery::save');
    }
    /**
     * Execute action
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('imagegallery.category.edit.tab.videos')
                     ->setInVideo($this->getRequest()->getPost('video', null));
        return $resultLayout;
    }
}
