<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Controller\Adminhtml\Template;

class Save extends \Magento\Backend\App\Action
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
        $isPost = $this->getRequest()->getPost();
        if ($isPost) {
            $popupTemplateFactory = $this->popupTemplateFactory->create();
            $templateId = $this->getRequest()->getParam('id');
            if ($templateId) {
                $popupTemplateFactory->load($templateId);
            }
            $formData = $this->getRequest()->getParam('template');

            $popupTemplateFactory->setData($formData);
            try {
                $popupTemplateFactory->save();
                $this->messageManager->addSuccess(__('The template has been saved.'));
                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {

                    $redirect = $this->resultFactory->create(\Magento\Backend\Model\View\Result\Redirect::class);
                    $redirect->setUrl('*/*/edit', ['id' => $popupTemplateFactory->getId(), '_current' => true]);
                }
                // Go to grid page
                $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                $redirect->setUrl('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['id' => $templateId]);
        }
    }
}
