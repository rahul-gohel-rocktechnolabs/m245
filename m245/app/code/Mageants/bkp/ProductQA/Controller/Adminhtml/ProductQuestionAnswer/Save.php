<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Controller\Adminhtml\ProductQuestionAnswer;

use \Mageants\ProductQA\Model\ProductQuestionAnswerFactory;
use \Mageants\ProductQA\Model\ResourceModel\ProductQuestionAnswer\CollectionFactory;
use \Magento\Framework\Registry;
use \Magento\Backend\Model\View\Result\RedirectFactory;
use \Magento\Backend\App\Action\Context;
use \Mageants\ProductQA\Helper\Data;
use \Mageants\ProductQA\Model\Upload;
use \Mageants\ProductQA\Model\ResourceModel\Image;
use \Mageants\ProductQA\Helper\Email as QaEmailHelper;
        
class Save extends \Mageants\ProductQA\Controller\Adminhtml\ProductQuestionAnswer
{
    /**
     * Access Resource ID
     *
     */
    public const RESOURCE_ID = 'Mageants_ProductQA::productanswer_save';
     /**
      * @var \Mageants\ProductQA\Model\Upload
      */
    protected $_uploadModel;

    /**
     * @var \Mageants\ProductQA\Model\ResourceModel\Image
     */
    protected $_imageModel;
    
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;
    
    /**
     * ProductQuestionAnswer Data Helper
     *
     * @var \Mageants\ProductQA\Helper\Data
     */
    protected $_productquestionanswerHelper;
    /**
     * @var \\Mageants\ProductQA\Model\ResourceModel\ProductQuestionAnswer\CollectionFactory
     */
    protected $_productquestionanswerCollectionFactory;
    /**
     * @var \Mageants\ProductQA\Helper\Email
     */
    protected $_qaEmailHelper;
    /**
     * Constructor
     *
     * @param ProductQuestionAnswerFactory $productquestionanswerFactory           [description]
     * @param Registry                     $registry                               [description]
     * @param Context                      $context                                [description]
     * @param QaEmailHelper                $qaEmailHelper                          [description]
     * @param CollectionFactory            $productquestionanswerCollectionFactory [description]
     * @param Data                         $productquestionanswerHelper            [description]
     */
    public function __construct(
        ProductQuestionAnswerFactory $productquestionanswerFactory,
        Registry $registry,
        Context $context,
        QaEmailHelper $qaEmailHelper,
        CollectionFactory $productquestionanswerCollectionFactory,
        Data $productquestionanswerHelper
    ) {
        $this->_backendSession = $context->getSession();
        
        $this->_productquestionanswerHelper = $productquestionanswerHelper;

        $this->_qaEmailHelper = $qaEmailHelper;

        $this->_productquestionanswerCollectionFactory = $productquestionanswerCollectionFactory;
        
        parent::__construct($productquestionanswerFactory, $registry, $context);
    }

    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $productquestionanswer = $this->_initProductQuestionAnswer();
            
        $data = $this->getRequest()->getPost('productquestionanswer');
        
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $template_data = $this->getRequest()->getPost('template');
            
            $productquestionanswer->setData($data);
            
            $this->_eventManager->dispatch(
                'mageants_productqa_productquestionanswer_prepare_save',
                [
                    'productquestionanswer' => $productquestionanswer,
                    'request' => $this->getRequest()
                ]
            );
            
            try {
                $productquestionanswer->save();
                if ($productquestionanswer->getStatus() == 1) {
                    $productquestionanswer_mailData = $productquestionanswer
                    ->getCollection()->addFieldToFilter('id', $productquestionanswer->getId());
                    $this->_qaEmailHelper->sendEmail($productquestionanswer_mailData->getFirstItem());
                    
                }
                $this->messageManager->addSuccess(__('The Product Answer has been saved.'));
                $this->_backendSession->setMageantsProductQAData(false);
                
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageants_productqa/*/edit',
                        [
                        'id' => $productquestionanswer->getId(),
                        '_current' => true
                        ]
                    );
                    
                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageants_productqa/*/');
                return $resultRedirect;
                
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                         $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Product Answer.'));
            }
            
            $this->_getSession()->setMageantsProductQAPostData($data);
            
            $resultRedirect->setPath(
                'mageants_productqa/*/edit',
                [
                    'id' => $productquestionanswer->getId(),
                    '_current' => true
                ]
            );
            
                  return $resultRedirect;
        }
        
        $resultRedirect->setPath('mageants_productqa/*/');
        
        return $resultRedirect;
    }
    /**
     *  Check permission via ACL resource
     *
     * @return boolean [description]
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::RESOURCE_ID);
    }
}
