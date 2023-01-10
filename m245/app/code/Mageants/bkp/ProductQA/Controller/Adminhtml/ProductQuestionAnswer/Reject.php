<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Controller\Adminhtml\ProductQuestionAnswer;

use \Magento\Ui\Component\MassAction\Filter;
use \Mageants\ProductQA\Model\ProductQuestionAnswerFactory;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Backend\App\Action\Context;
use Mageants\ProductQA\Model\Source\Status;
use Magento\Framework\UrlInterface;

class Reject extends \Magento\Backend\App\Action
{
    
    /**
     * @var UrlInterface
     */
    private $_urlBuilder;
    /**
     * Access Resource ID
     *
     */
    public const RESOURCE_ID = 'Mageants_ProductQA::productqnswer_reject';
    /**
     * Dis approve url
     */
    public const URL_ANSWER_REJECT = 'mageants_productqa/productquestionanswer/approve';
    /**
     * Mass Action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * Question Answer Factory
     *
     * @var \Mageants\ProductQA\Model\QuestionAnswerAnswerFactory
     */
    protected $_productQuestionAnswerFactory;
    /**
     * Constructor
     *
     * @param Filter                       $filter                       [description]
     * @param ProductQuestionAnswerFactory $productQuestionAnswerFactory [description]
     * @param JsonFactory                  $jsonFactory                  [description]
     * @param Context                      $context                      [description]
     */
    public function __construct(
        Filter $filter,
        ProductQuestionAnswerFactory $productQuestionAnswerFactory,
        JsonFactory $jsonFactory,
        Context $context
    ) {
        $this->_filter            = $filter;
        
        $this->_productQuestionAnswerFactory = $productQuestionAnswerFactory;
        
        $this->_jsonFactory = $jsonFactory;
                
        parent::__construct($context);
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
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $productQuestionAnswerFactory = $this->_productQuestionAnswerFactory->create();
        
        $id = $this->getRequest()->getParam("id");
        
        $jsonResultFactory = $this->_jsonFactory->create();
        
        $status = 0;
        
        $message = "Error! while  changeing status.";
        
        if ($id) {
            $productQuestionAnswerFactory->load($id);
            
            $productQuestionAnswerFactory->setStatus(Status::STATUS_REJECT);
            
            $productQuestionAnswerFactory->save();
            
            $status = 1;
            
            $message = "Answer Rejected successfully.";
        }
        
        $jsonResultFactory = $this->_jsonFactory->create();
        
         $resultData = [
                'status' => $status,
                'status_text' => __("Approve Now"),
                'prev_status_text' => __("Rejected"),
                'message' => $message,
                'url'=> $this->getUrl(static::URL_ANSWER_REJECT, ['id' => $id ])
         ];

         return $jsonResultFactory->setData($resultData);
    }
}
