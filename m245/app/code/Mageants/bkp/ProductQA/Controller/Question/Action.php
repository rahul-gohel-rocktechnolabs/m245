<?php
namespace Mageants\ProductQA\Controller\Question;
 
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Mageants\ProductQA\Model\ResourceModel\ProductqaAction\CollectionFactory as ActionCollection;
use \Mageants\ProductQA\Model\ProductQuestionAnswerFactory;
use \Mageants\ProductQA\Model\ProductQuestionFactory;
use \Mageants\ProductQA\Model\Source\UserType;
use \Mageants\ProductQA\Model\Source\Status;
use \Mageants\ProductQA\Model\Source\ActionType;
use \Magento\Store\Model\StoreManagerInterface;
use \Mageants\ProductQA\Helper\Data;
use \Magento\Customer\Model\Session as CustomerSession;
 
class Action extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Mageants\ProductQA\Model\ResourceModel\ProductqaAction
     */
    protected $_roductqaActionFactory = false;
    /**
     * @var \Mageants\ProductQA\Model\ProductQuestionAnswer
     */
    protected $_productQuestionAnswerFactory = false;
    /**
     * @var \Mageants\ProductQA\Model\ProductQuestion
     */
    protected $_productQuestionFactory = false;
    /**
     * @var _storeManager
     */
    protected $_storeManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Mageants\ProductQA\Helper\Data
     */
    protected $_helper;
      /**
       * @var \Magento\Framework\View\Result\LayoutFactory
       */
    protected $_resultJsonFactory = false;
   /**
    * Construct
    *
    * @param Data                         $helper                       [description]
    * @param Context                      $context                      [description]
    * @param JsonFactory                  $resultJsonFactory            [description]
    * @param StoreManagerInterface        $storeManager                 [description]
    * @param CustomerSession              $_customerSession             [description]
    * @param ActionCollection             $actionCollectionFactory      [description]
    * @param ProductQuestionAnswerFactory $productQuestionAnswerFactory [description]
    * @param ProductQuestionFactory       $productQuestionFactory       [description]
    */
    public function __construct(
        Data $helper,
        Context $context,
        JsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager,
        CustomerSession $_customerSession,
        ActionCollection $actionCollectionFactory,
        ProductQuestionAnswerFactory $productQuestionAnswerFactory,
        ProductQuestionFactory $productQuestionFactory
    ) {
        $this->_helper = $helper;
        
        $this->_actionCollectionFactory = $actionCollectionFactory;
        
        $this->_productQuestionAnswerFactory = $productQuestionAnswerFactory;
        
        $this->_productQuestionFactory = $productQuestionFactory;
        
        $this->_customerSession = $_customerSession;
        
        $this->_resultJsonFactory = $resultJsonFactory;
        
        $this->_storeManager = $storeManager;
         
        parent::__construct($context);
    }
   /**
    * Excute
    *
    * @return [type] [description]
    */
    public function execute()
    {
        $action = $this->getRequest()->getParam("action");
        $count  = 0;
        $object='';
        $question = '';
        $answer = '';
        if ($this->_customerSession->isLoggedIn()) {
            if (($action == ActionType::ACTION_LIKE || $action ==  ActionType::ACTION_DISLIKE)) {
                $question = $this->getRequest()->getParam("question_id");
                 
                $answer = $this->getRequest()->getParam("answer_id");
                
                if ($question) {
                    $object_id = $question;
                    
                    $object = "question";
                } else {
                    $object_id = $answer;
                    $object = "answer";
                }
                if ($object_id && $action) {
                    $product_id = $this->getRequest()->getParam("product");
                    $action_by = $this->_customerSession->getCustomer()->getId();
                    $actionCollectionFactory =  $this->_actionCollectionFactory->create();
                    if ($action == ActionType::ACTION_LIKE) {
                        $actionOpp = ActionType::ACTION_DISLIKE;
                    } else {
                        $actionOpp = ActionType::ACTION_LIKE;
                    }
                    if ($object == "answer") {
                        $qaobject = $this->_productQuestionAnswerFactory->create();
                    } else {
                        $qaobject = $this->_productQuestionFactory->create();
                    }
                    
                       $qaobject->load($object_id);

                       $qaAction = $actionCollectionFactory
                       ->addFieldToFilter("action_by", $action_by)
                       ->addFieldToFilter("object_id", $object_id)
                       ->addFieldToFilter("action", $action)
                       ->addFieldToFilter("object_type", $object)
                       ->getFirstItem();
                    if ($qaAction->getId()) {
                        if ($qaAction->getAction() == $action) {
                            if ($action == ActionType::ACTION_LIKE) {
                                $qaobject->setLikes(new \Zend_Db_Expr('likes - 1'))->save();
                                            
                                $qaobject->load($object_id);
                                            
                                $count  = $qaobject->getLikes();
                                            
                            } else {
                                      $qaobject->setDislikes(new \Zend_Db_Expr('dislikes - 1'))->save();
                                            
                                      $qaobject->load($object_id);
                                            
                                      $count  = $qaobject->getDislikes();
                            }
                                
                             $qaAction->delete();
                        } else {
                            $qaAction
                             ->setActionBy($action_by)
                             ->setObjectId($object_id)
                             ->setObjectType($object)
                             ->setAction($action)
                             ->save();
                                        
                            if ($action == ActionType::ACTION_LIKE) {
                                    $qaobject->setLikes(new \Zend_Db_Expr('likes + 1'))->save();
                                            
                                    $qaobject->load($object_id);
                                            
                                    $count  = $qaobject->getLikes();
                            } else {
                                  $qaobject->setDislikes(new \Zend_Db_Expr('dislikes + 1'))->save();
                                            
                                  $qaobject->load($object_id);
                                            
                                  $count  = $qaobject->getDislikes();
                                            
                            }
                        }
                    } else {
                           $qaAction
                               ->setActionBy($action_by)
                               ->setObjectId($object_id)
                               ->setObjectType($object)
                               ->setAction($action)
                               ->save();
                            
                        if ($action == ActionType::ACTION_LIKE) {
                            $qaobject->setLikes(new \Zend_Db_Expr('likes + 1'))->save();
                                    
                            $qaobject->load($object_id);
                                    
                            $count  = $qaobject->getLikes();
                        } else {
                            $qaobject->setDislikes(new \Zend_Db_Expr('dislikes + 1'))->save();
                                    
                            $qaobject->load($object_id);
                                    
                            $count  = $qaobject->getDislikes();
                        }
                    }
                       $qaActionCheck = $this->_actionCollectionFactory->create()
                       ->addFieldToFilter("action_by", $action_by)
                       ->addFieldToFilter("object_id", $object_id)
                       ->addFieldToFilter("action", $actionOpp)
                       ->addFieldToFilter("object_type", $object)
                       ->getFirstItem();
                    if ($qaActionCheck->getId()) {
                        if ($qaActionCheck->getAction() != $action) {
                            if ($action == ActionType::ACTION_DISLIKE) {
                                    $qaobject->setLikes(new \Zend_Db_Expr('likes - 1'))->save();
                                            
                                    $qaobject->load($object_id);
                                            
                                    $likes  = $qaobject->getLikes();
                                            
                            } else {
                                   $qaobject->setDislikes(new \Zend_Db_Expr('dislikes - 1'))->save();
                                            
                                   $qaobject->load($object_id);
                                            
                                       $dislikes  = $qaobject->getDislikes();
                            }
                                
                                $qaActionCheck->delete();
                        }
                                
                    }
                        $message = __('');
                       $status = 1;
                } else {
                    $message = __("Error!  Please try later.");
                    $status = 0;
                }
            } else {
                $message = __("Error!  Wrong action. Please try later.");
                $status = 0;
            }
                
        } else {
            $message = __('Error! Login to do this action.');
            $status = 0;
        }
        if ($object == 'question') {
            $id = $question;
        } else {
            $id = $answer;
        }
         $result = $this->_resultJsonFactory->create();
        if (isset($likes)) {
             
            $resultData = [
               'status' => $status,
               'message' => $message ,
               'count'=>$count,
               'likes' => $likes,
               'object' => $object,
               'id' => $id
            ];
        } elseif (isset($dislikes)) {
            $resultData = [
               'status' => $status,
               'message' => $message ,
               'count'=>$count,
               'dislikes' => $dislikes,
               'object' => $object,
               'id' => $id
            ];
              
        } elseif (isset($object)) {
            $resultData = [
               'status' => $status,
               'message' => $message ,
               'count'=>$count,
               'object' => $object,
               'id' => $id
            ];
        } else {
            $resultData = [
               'status' => $status,
               'message' => $message
            ];
        }
          return $result->setData($resultData);
    }
}
