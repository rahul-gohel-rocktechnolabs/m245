<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Filesystem;
use Magento\Framework\HTTP\PhpEnvironment\Request;

abstract class Category extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsHelper;
    /**
     * @var \Mageants\ImageGallery\Model\Category
     */
    protected $_categoryMOdel;
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;
    /**
     * @var \Mageants\ImageGallery\Helper\Data
     */
    protected $_CategoryHelper;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directory_list;
    /**
     * @var \Magento\Backend\Model\Session
     */
    public $session;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param \Mageants\ImageGallery\Helper\Data $Data
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param Filesystem $fileSystem
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param \Mageants\ImageGallery\Model\Category $categoryModel
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directory_list
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\Session $session
     * @param Request $request
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        \Mageants\ImageGallery\Helper\Data $Data,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        Filesystem $fileSystem,
        \Magento\Backend\Helper\Js $jsHelper,
        \Mageants\ImageGallery\Model\Category $categoryModel,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\Session $session,
        Request $request
    ) {
        $this->_CategoryHelper = $Data;
        $this->coreRegistry = $coreRegistry;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->_jsHelper = $jsHelper;
        $this->_categoryMOdel=$categoryModel;
        $this->directory_list = $directory_list;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->session = $session;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    protected function initPage(Page $resultPage)
    {
        $resultPage->setActiveMenu('Mageants_All::mageants')
            ->addBreadcrumb(__('Category'), __('Category'))
            ->addBreadcrumb(__('Categories'), __('Categories'));
        return $resultPage;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DR_Gallery::gallery_gallery');
    }
}
