<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Adminhtml\Category;

class Images extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;
    /**
     * @var \Mageants\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory
     */
    protected $_imageCollectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Mageants\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $imageCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Mageants\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $imageCollectionFactory
    ) {
        parent::__construct($context);
        $this->_jsHelper = $jsHelper;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_imageCollectionFactory = $imageCollectionFactory;
    }
    /**
     * Is allowed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_ImageGallery::save');
    }
    /**
     * Save action
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('imagegallery.category.edit.tab.images')
                     ->setInImage($this->getRequest()->getPost('image', null));
        return $resultLayout;
    }
}
