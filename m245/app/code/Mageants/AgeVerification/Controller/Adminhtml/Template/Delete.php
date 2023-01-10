<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Controller\Adminhtml\Template;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Mageants\AgeVerification\Model\PopupTemplateFactory
     */
    public $popupTemplateFactory;
   /**
    * Undocumented function
    *
    * @param \Magento\Backend\App\Action\Context $context
    * @param \Mageants\AgeVerification\Model\PopupTemplateFactory $popupTemplateFactory
    */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageants\AgeVerification\Model\PopupTemplateFactory $popupTemplateFactory
    ) {
        $this->popupTemplateFactory = $popupTemplateFactory;
        parent::__construct($context);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function execute()
    {
        $templateId = (int) $this->getRequest()->getParam('id');
        if ($templateId) {
            $templateModel = $this->popupTemplateFactory->create();
            $templateModel->load($templateId);
            if (!$templateModel->getId()) {
                $this->messageManager->addError(__('This template no longer exists.'));
            } else {
                try {
                    $templateModel->delete();
                    $this->messageManager->addSuccess(__('The template has been deleted.'));
                    // $this->_redirect('*/*/');
                    // return;
                    $resultRedirect->setPath('*/*/');
                    return $resultRedirect;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $resultRedirect->setPath('*/*/edit', ['id' => $templateModel->getId()]);
                    return $resultRedirect;
                }
            }
        }
    }
}
