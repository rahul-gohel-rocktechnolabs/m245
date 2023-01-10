<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Controller\Adminhtml\ProductQuestion;

use \Mageants\ProductQA\Model\ProductQuestionFactory;
use \Magento\Framework\Registry;
use \Magento\Backend\Model\View\Result\RedirectFactory;
use \Magento\Backend\App\Action\Context;
use \Mageants\ProductQA\Helper\Data;
use \Mageants\ProductQA\Model\Upload;
use \Mageants\ProductQA\Model\ResourceModel\Image;
        
class Save extends \Mageants\ProductQA\Controller\Adminhtml\ProductQuestion
{
    /**
     * Access Resource ID
     *
     */
    public const RESOURCE_ID = 'Mageants_ProductQA::productquestion_save';
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
     * @var \Mageants\ProductQA\Helper\Data
     */
    protected $_productquestionHelper;
    
    /**
     * Constructor
     *
     * @param ProductQuestionFactory $productquestionFactory [description]
     * @param Registry               $registry               [description]
     * @param Context                $context                [description]
     * @param Data                   $productquestionHelper  [description]
     */
    public function __construct(
        ProductQuestionFactory $productquestionFactory,
        Registry $registry,
        Context $context,
        Data $productquestionHelper
    ) {
        $this->_backendSession = $context->getSession();
        
        $this->_productquestionHelper = $productquestionHelper;
        
        parent::__construct($productquestionFactory, $registry, $context);
    }

    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $productquestion = $this->_initProductQuestion();
            
        $data = $this->getRequest()->getPost('productquestion');
        
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $template_data = $this->getRequest()->getPost('template');
            
            $data['template_data'] =  $this->_productquestionHelper->serializeSetting($template_data);
            
            $productquestion->setData($data);
            
            $this->_eventManager->dispatch(
                'mageants_productqa_productquestion_prepare_save',
                [
                    'productquestion' => $productquestion,
                    'request' => $this->getRequest()
                ]
            );
            
            try {
                $productquestion->save();
                
                $this->messageManager->addSuccess(__('The Product Question has been saved.'));
                
                $this->_backendSession->setMageantsProductQAData(false);
                
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageants_productqa/*/edit',
                        [
                        'id' => $productquestion->getId(),
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
                $this->messageManager->addException($e, __('Something went wrong while saving the Product Question.'));
            }
            
            $this->_getSession()->setMageantsProductQAPostData($data);
            
            $resultRedirect->setPath(
                'mageants_productqa/*/edit',
                [
                    'id' => $productquestion->getId(),
                    '_current' => true
                ]
            );
            
                  return $resultRedirect;
        }
        
        $resultRedirect->setPath('mageants_productqa/*/');
        
        return $resultRedirect;
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
}
