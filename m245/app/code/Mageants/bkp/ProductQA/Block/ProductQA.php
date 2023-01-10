<?php
 /**
  * @category  Mageants Product360Image
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Block;

use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Mageants\ProductQA\Model\ResourceModel\ProductQuestion\CollectionFactory as QuestionCollection;
use \Mageants\ProductQA\Model\ResourceModel\ProductQuestionAnswer\CollectionFactory as AnswerCollection;
use \Mageants\ProductQA\Helper\Data;
use Mageants\ProductQA\Model\Source\Status;
use \Mageants\ProductQA\Model\Source\ActionType;
use Magento\Customer\Model\Session as CustomerSession;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Catalog\Model\ProductFactory;

class ProductQA extends \Magento\Framework\View\Element\Template
{
    /**
     * @var default store all store id
     */
    public const ALL_STORE = 0;
    /**
     * @var default answer per page
     */
    public const ANSWER_PAGE_SIZE = 1;
    /**
     * @var _default_curr_page
     */
    protected $_default_curr_page = 1;
    /**
     * @var _total_question
     */
    protected $_total_question;
    /**
     * @var _storeManager
     */
    protected $_storeManager;
    /**
     * @var \Mageants\ProductQA\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
      /**
       * @var \Magento\Framework\Data\Form\FormKey
       */
    protected $_formKey;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory = false;
    /**
     * @var \Magento\Catalog\Model\Product\Interceptor
     */
    protected $_currentProduct = false;
     /**
      * @var Array
      */
    protected $_questionCollection = [];
    /**
     * @var \Mageants\ProductQA\Model\ResourceModel\ProductQuestion\CollectionFactory
     */
    protected $_productQuestion = false;
    /**
     * @var \Mageants\ProductQA\Model\ResourceModel\ProductQuestionAnswer\CollectionFactory
     */
    protected $_productQuestionAnswer = false;
    /**
     * Framework Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Construction function
     * @param Data               $helper                [description]
     * @param QuestionCollection $productQuestion       [description]
     * @param AnswerCollection   $productQuestionAnswer [description]
     * @param Registry           $coreRegistry          [description]
     * @param Context            $context               [description]
     * @param CustomerSession    $_customerSession      [description]
     * @param ProductFactory     $productFactory        [description]
     * @param array              $data                  [description]
     */
    public function __construct(
        Data $helper,
        QuestionCollection $productQuestion,
        AnswerCollection $productQuestionAnswer,
        Registry $coreRegistry,
        Context $context,
        CustomerSession $_customerSession,
        ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_helper = $helper;
        
        $this->_customerSession = $_customerSession;
        
        $this->_formKey = $context->getFormKey();
        
        $this->_coreRegistry = $coreRegistry;
        
        $this->_productFactory = $productFactory;
        
        parent::__construct($context, $data);
        
        $this->initProduct();
        
        $this->_productQuestion = $productQuestion;
        
        $this->_productQuestionAnswer = $productQuestionAnswer;
    }
    
      /**
       * Function initProduct.
       *
       * @return void
       */
    public function initProduct()
    {
        $this->_currentProduct = $this->_coreRegistry->registry('current_product');
        
        if (!$this->_currentProduct) {
            $product_id = $this->getRequest()->getParam("product");
            
            $productFactory = $this->_productFactory->create();
            
            $this->_currentProduct = $productFactory->load($product_id);
        }
    }
    /**
     * Function canShowLoadMore
     *
     * @return boolean
     */
    public function canShowLoadMore()
    {
        return $this->getTotalQuestion() > $this->getProductQaHelper()->getQuestionPageSize() ? true : false;
    }
    /**
     * Function getCurrPage
     *
     * @return void
     */
    public function getCurrPage()
    {
        $page = $this->getRequest()->getParam('page');
        
        if (!$page) {
            $page = $this->_default_curr_page;
        }
        
        return $page;
    }
    
   /**
    * Enable / Disable for current product
    *
    * @return boolean
    */
    public function hasProductQAEnable()
    {
        if ($this->getProductQaHelper()->isExtentionEnable() && $this->_currentProduct) {
            return true;
        }
        return false;
    }
     /**
      * Get current product.
      *
      * @return object
      */
    public function getProduct()
    {
        return $this->_currentProduct ;
    }
    
      /**
       * Get current login customer
       *
       * @return object
       */
    public function getLoginCustomer()
    {
        return $this->_customerSession->getCustomer();
    }
    /**
     * Return Get email
     *
     * @return void
     */
    public function getCustomerEmail()
    {
        return $this->getLoginCustomer()->getEmail();
    }
        
    /**
     * Return get name function
     *
     * @return void
     */
    public function getCustomerName()
    {
        return $this->getLoginCustomer()->getName();
    }
      /**
       * Customer login function
       *
       * @return boolean
       */
    public function isLogin()
    {
        if ($this->_customerSession->isLoggedIn()) {
                return true;
        }
        return false;
    }
    
   /**
    * Get Order Sorting function
    *
    * @return void
    */
    public function getSortOrder()
    {
        $order = $this->getRequest()->getParam("order");
        
        switch ($order) {
            case "oldest":
                $order_field = "created_at";
                $ascdesc ="ASC" ;
                break;
            case "mostlike":
                $order_field = "likes";
                $ascdesc ="DESC" ;
                break;
            case "mostdislike":
                $order_field = "dislikes";
                $ascdesc ="DESC" ;
                break;
            default:
                $order_field = "created_at";
                $ascdesc ="DESC" ;
                break;
        }
             
        return [$order_field , $ascdesc];
    }
     /**
      * Return Sort Url function.
      *
      * @param mixed $sortkey
      * @return void
      */
    public function getSortUrl($sortkey)
    {
        return $this->getLoadMoreUrl()."order/".$sortkey;
    }
   /**
    * Get action dis like url function.
    *
    * @param mixed $question_id
    * @return void
    */
    public function getLikeActionUrl($question_id)
    {
        return $this->getUrl("productqa/question/action/action/".
            ActionType::ACTION_LIKE."/question_id/".$question_id."/");
    }
   /**
    * Get action dis like url function.
    *
    * @param mixed $question_id
    * @return void
    */
    public function getDislikeActionUrl($question_id)
    {
        return $this->getUrl("productqa/question/action/action/".
            ActionType::ACTION_DISLIKE."/question_id/".$question_id."/");
    }
    /**
     * Function for Get Form Url.
     *
     * @return void
     */
    public function getLoadMoreUrl()
    {
        return $this->getUrl("productqa/question/index/product/".$this->getProduct()->getId()."/");
    }
     /**
      * Function for Get Form Url.
      *
      * @return void
      */
    public function getFormUrl()
    {
        return $this->getUrl("productqa/question/ask/product/".$this->getProduct()->getId()."/");
    }
    /**
     * Function for get total question.
     *
     * @return int
     */
    public function getTotalQuestion()
    {
        return $this->_total_question;
    }
     /**
      * Function for get Total Page.
      *
      * @return void
      */
    public function getTotalPage()
    {
        return floor($this->getTotalQuestion() / $this->getProductQaHelper()->getQuestionPageSize());
    }
    /**
     * Function for form key.
     *
     * @return void
     */
    public function getFormKey()
    {
        return $this->_formKey->getFormKey();
    }
    
     /**
      * ProductQuestions of ProductQA.
      *
      * @return void
      */
    public function getProductQuestions()
    {
        if ($this->_currentProduct && $this->_currentProduct->getId()) {
            $this->_questionCollection = $this->_productQuestion->create();
            
            list($order_field , $ascdesc) = $this->getSortOrder();
            
            $this->_questionCollection
                ->addFieldToFilter('product_id', $this->_currentProduct->getId())
                ->addFieldToFilter('status', Status::STATUS_APPROVE)
                ->addFieldToFilter('store_id', ['in'=>[$this->getCurrentStoreId(),self::ALL_STORE]])
                ->setPageSize($this->getProductQaHelper()->getQuestionPageSize())
                ->setCurPage($this->getCurrPage())
                ->setOrder($order_field, $ascdesc);
            
            $q = $this->getRequest()->getParam("q");
            if ($q) {
                    $this->_questionCollection->addFieldToFilter("question", ['like' => '%'.$q.'%']);
            }
        }
       
        $this->_total_question = $this->_questionCollection ->getSize();
        return $this->_questionCollection ;
    }
    
    /**
     * Retrieve Module Data Helper
     *
     * @return int
     */
    public function getQuestionMaxCharacter()
    {
        return $this->getProductQaHelper()->getMaxQuestionLength();
    }
    /**
     * Retrieve Module Data Helper
     *
     * @return _helper
     */
    public function getProductQaHelper()
    {
        return $this->_helper;
    }
   /**
    * ProductAnswer of ProductQA.
    *
    * @param mixed $question_id
    * @return void
    */
    public function getQuestionsAnswer($question_id)
    {
        return $this->getChildBlock('answer')->setQuestionId($question_id)
        ->setPageSize(self::ANSWER_PAGE_SIZE)->toHtml();
    }

    /**
     * Retrieve current Store Id
     *
     * @return store_id
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
