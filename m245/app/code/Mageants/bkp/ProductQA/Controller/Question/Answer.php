<?php
namespace Mageants\ProductQA\Controller\Question;
 
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Mageants\ProductQA\Model\ProductQuestionAnswerFactory;
use \Mageants\ProductQA\Model\ResourceModel\ProductQuestion\Collection;
use \Magento\Catalog\Model\Product;
use \Magento\Catalog\Helper\ImageFactory;
use \Mageants\ProductQA\Helper\Email as QaEmailHelper;
use \Mageants\ProductQA\Helper\Data as QaHelper;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Mageants\ProductQA\Model\Source\UserType;
use \Mageants\ProductQA\Model\Source\Status;
use \Magento\Customer\Model\Session as CustomerSession;
use \Mageants\ProductQA\Helper\Data;
use Magento\Framework\Mail\Template\TransportBuilder;
 
class Answer extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Mageants\ProductQA\Helper\Email
     */
    protected $_qaEmailHelper;
    /**
     * @var \Mageants\ProductQA\Helper\Data
     */
    protected $_qaHelper;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;
    /**
     * @var \Mageants\ProductQA\Model\ResourceModel\ProductQuestionFactory
     */
    protected $_productQAnswer = false;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Mageants\ProductQA\Model\Source\Status;
     */
    protected $_status;
    /**
     * @var \Mageants\ProductQA\Helper\Data
     */
    protected $_helper;
    /**
     * @var _storeManager
     */
    protected $_storeManager;
    /**
     * ResultJsonFactory variable
     *
     * @var boolean
     */
    protected $_resultJsonFactory = false;
    /**
     * @var \Mageants\ProductQA\Model\ResourceModel\ProductQuestionFactory
     */
    protected $_productQuestion;
    
    /**
     * Constructor function.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CustomerSession $_customerSession
     * @param ProductQuestionAnswerFactory $productQAnswer
     * @param QaEmailHelper $qaEmailHelper
     * @param QaHelper $qaHelper
     * @param TimezoneInterface $timezone
     * @param Status $status
     * @param Data $helper
     * @param Product $product
     * @param ImageFactory $imageHelperFactory
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param Collection $productQuestion
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CustomerSession $_customerSession,
        ProductQuestionAnswerFactory $productQAnswer,
        QaEmailHelper $qaEmailHelper,
        QaHelper $qaHelper,
        TimezoneInterface $timezone,
        Status $status,
        Data $helper,
        Product $product,
        ImageFactory $imageHelperFactory,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        Collection $productQuestion
    ) {
        $this->_productQAnswer = $productQAnswer;
        
        $this->_qaHelper = $qaHelper;

        $this->_qaEmailHelper = $qaEmailHelper;

        $this->_timezone = $timezone;
        
        $this->_customerSession = $_customerSession;
        
        $this->_resultJsonFactory = $resultJsonFactory;
        
        $this->_status = $status->getOptionArray();

        $this->_helper = $helper;

        $this->_product = $product;

        $this->_imageHelperFactory = $imageHelperFactory;

        $this->_storeManager = $storeManager;

        $this->_transportBuilder = $transportBuilder;

        $this->_productQuestion = $productQuestion;
                 
        parent::__construct($context);
    }

    /**
     * Execute function.
     *
     * @return void
     */
    public function execute()
    {
        $name = $this->getRequest()->getPost("name");
        
        $email = $this->getRequest()->getPost("email");
        
        $answer = $this->getRequest()->getPost("answer");
        
        $answerdata =[];
        
        if ($answer && $email && $name) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                  
                $message = __('Error! Invalid email format');
                $status = 0;

            } else {
                $question_id = $this->getRequest()->getParam("question_id");
                
                $status = $this->getRequest()->getParam("adminstatus");
                
                $user_id = $this->_customerSession->getCustomer()->getId();
                
                $answer_by = $user_id;
                
                $user_type = UserType::CUSTOMER;
                
                if (! $status) {
                    $status =  Status::STATUS_PENDING;
                }
                
                $answerFactory =  $this->_productQAnswer->create();
                
                $answerFactory->setQuestionId($question_id)
                    ->setUserId($user_id)
                    ->setAnswerBy($user_id)
                    ->setUserType($user_type)
                    ->setStatus($status)
                    ->setName($name)
                    ->setEmail($email)
                    ->setAnswer($answer)
                    ->save();

                if ($answerFactory->getId()) {
                    $message = __('Your Answer submited successfull. Please wait until admin approve your answer.');
                     
                    $this->sendAdminEmailNotification($answerFactory);
                    
                    $status = 1;
                } else {
                     $message = __('Error! while saving data. Please try latter.');
                    $status = 0;
                }
                    
                    $answerdata = [
                        'id'=>$answerFactory->getId(),
                        'answer'=>$answerFactory->getAnswer(),
                        'name'=>$answerFactory->getName(),
                        'date'=>$this->_timezone->formatDate($answerFactory->getCreatedAt()),
                        'status'=>__($this->_status[$answerFactory->getStatus()])
                    ];
            }
        } else {
            $message = __('Error! All fields are required.');
            $status = 0;
        }
         
        $result = $this->_resultJsonFactory->create();
        
        $resultData = [
            'status' => $status,
            'message' => $message ,
            'answer'=>$answerdata
        ];
        
        return $result->setData($resultData);
    }

    /**
     * Send Admin Email Notification function.
     *
     * @param mixed $answerFactory
     * @return void
     */
    public function sendAdminEmailNotification($answerFactory)
    {
        if (!$answerFactory->getId()) {
            return;
        }
        $enable = $this->_helper->isNotifyAdminForAnswer();
        if ($enable) {
                  
                $productFactory =  $this->_product;

                $product = $productFactory->load($answerFactory->getProductId());

                $product_image = $this->_imageHelperFactory->create()
                ->init($product, 'product_thumbnail_image')->getUrl();
                $question_id = $answerFactory->getQuestionId();
                $question_data = $this->_productQuestion->addFieldToFilter('id', $question_id);
                $questionData = $question_data->getData();
                $store_id = $questionData[0]['store_id'];
                $data = [
                    'question' => $questionData[0]['question'],
                    'customer_name' => $answerFactory->getName(),
                    'customer_email' => $answerFactory->getEmail(),
                    "answer" => $answerFactory->getAnswer(),
                    "product_link" => $product->getProductUrl(),
                    "product_name" => $product->getName(),
                    "product_image" => $product_image
                ];
                                
                $emailTo = $this->_helper->sendAdminEmailSenderForAnswer();

                $store = $this->_storeManager->getStore($store_id);

                $store_email = $store->getConfig('trans_email/ident_' . $emailTo . '/email');
                
                $store_name = $store->getConfig('trans_email/ident_' . $emailTo . '/name');

                $templateId = $this->_helper->adminEmailTemplateForAnswer();
                if (empty($templateId)) {
                    $templateId = "mageants_productqa_admin_email_template_foranswer";
                }
                if (!empty($store_email)) {
                    $transport = $this->_transportBuilder
                        ->setTemplateIdentifier($templateId)
                        ->setTemplateOptions([
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store_id])
                        ->setTemplateVars($data)
                        ->setFrom(['name' => $store_name,'email' => $store_email])
                        ->addTo($store_email)
                        ->getTransport();
                    
                    $transport->sendMessage();
                }
        }
    }
}
