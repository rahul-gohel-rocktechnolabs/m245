<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Controller\Adminhtml\ProductQuestion;

class Delete extends \Mageants\ProductQA\Controller\Adminhtml\ProductQuestion
{
    /**
     * Access Resource ID
     *
     */
    public const RESOURCE_ID = 'Mageants_ProductQA::productquestion_delete';
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();
        
        $id = $this->getRequest()->getParam('id');
        
        if ($id) {
            $name = "";
            
            try {
                /** @var \Mageants\ProductQA\Model\ProductQuestion $productquestion */
                $productquestion = $this->_productquestionFactory->create();
                
                $productquestion->load($id);
                
                $name = $productquestion->getName();
                
                $productquestion->delete();

               // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $productquestionanswer =$this->productquestionanswerfactory->create();
                $questionanswer = $productquestionanswer->getCollection()->addFieldToFilter('question_id', $id);
                foreach ($questionanswer as $answer) {
                    $answer->delete();
                }
                
                $this->messageManager->addSuccess(__('The ProductQuestion has been deleted.'));
                
                $this->_eventManager->dispatch(
                    'adminhtml_mageants_productqa_productquestion_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                
                $resultRedirect->setPath('mageants_productqa/*/');
                
                return $resultRedirect;
                
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageants_productqa_label_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                
                         // display error message
                         $this->messageManager->addError($e->getMessage());
                
                         // go back to edit form
                         $resultRedirect->setPath('mageants_productqa/*/edit', ['id' => $id]);
                
                         return $resultRedirect;
            }
        }
        
        // display error message
        $this->messageManager->addError(__('ProductQuestion to delete was not found.'));
        
        // go to grid
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
