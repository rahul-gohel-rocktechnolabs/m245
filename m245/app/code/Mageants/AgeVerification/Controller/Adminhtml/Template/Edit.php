<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Model\Session;
use Magento\Framework\Registry;

class Edit extends Action
{

     /**
      * @var \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory
      */
    public $collectionFactory;
    /**
     * @var \Mageants\AgeVerification\Model\PopupTemplateFactory
     */
    public $popupTemplateFactory;
    
    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

  /**
   * Session variable
   *
   * @var mixed
   */
    public $Session;

 /**
  * Undocumented function
  *
  * @param \Magento\Backend\App\Action\Context $context
  * @param \Magento\Framework\Registry $coreRegistry
  * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
  * @param \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory $collectionFactory
  * @param \Mageants\AgeVerification\Model\PopupTemplateFactory $popupTemplateFactory
  * @param Session $Session
  */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory $collectionFactory,
        \Mageants\AgeVerification\Model\PopupTemplateFactory $popupTemplateFactory,
        Session $Session
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->collectionFactory = $collectionFactory;
        $this->popupTemplateFactory = $popupTemplateFactory;
        $this->Session = $Session;
        parent::__construct($context);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_AgeVerification::save');
    }
  /**
   * Undocumented function
   *
   * @return void
   */
    public function execute()
    {
        $templateId = $this->getRequest()->getParam('id');
    
        $collection = $this->collectionFactory->create()->getItemById($templateId);
        if ($templateId) {
            if (!$collection->getId()) {
                $this->messageManager->addError(__('This Data no longer exists.'));
                // $this->_redirect('*/*/');
                // return;
                 $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }
        $data =  $this->Session->getFormData(true);

        $this->coreRegistry->register('ageverification_template', $collection);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageants_AgeVerification::main_menu');
      
        $resultPage->getConfig()->getTitle()->prepend(__('Age Verification Template'));

        return $resultPage;
    }
}
