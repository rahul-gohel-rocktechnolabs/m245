<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /**
     * Execute
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
