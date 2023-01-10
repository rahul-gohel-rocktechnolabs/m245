<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Controller\Adminhtml\ProductQuestion;

use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    
    /**
     * Access Resource ID
     *
     */
    public const RESOURCE_ID = 'Mageants_ProductQA::productquestion_grid';
     /**
      * @var \Magento\Framework\View\Result\PageFactory
      */
    protected $_resultPage = null;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory = null;
    /**
     * @param Context     $context           [description]
     * @param PageFactory $resultPageFactory [description]
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        
        $this->_resultPageFactory = $resultPageFactory;
    }
    /**
     * Call page factory to render layout and page content
     */
    public function execute()
    {
        $this->_setPageData();
        return $this->getResultPage();
    }

   /**
    * Check permission via ACL resource
    *
    * @return boolean [description]
    */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::RESOURCE_ID);
    }
    
   /**
    * Get Result Page
    *
    * @return [type] [description]
    */
    public function getResultPage()
    {
        $var=$this->_resultPage;
        if ($var === null) {
            $this->_resultPage = $this->_resultPageFactory->create();
        }
        
        return $this->_resultPage;
    }
    
    /**
     * Set page data and active menu
     */
    protected function _setPageData()
    {
        $resultPage = $this->getResultPage();
        
        $resultPage->setActiveMenu('Mageants_ProductQA::productquestion');
        
        $resultPage->getConfig()->getTitle()->prepend((__('Product Question')));

        //Add bread crumb
        $resultPage->addBreadcrumb(__('Mageants'), __('Mageants'));
        
        $resultPage->addBreadcrumb(__(' Product Q/A'), __('Manage Product Question'));

        return $this;
    }
}
