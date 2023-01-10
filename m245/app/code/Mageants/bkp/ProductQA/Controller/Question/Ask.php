<?php
namespace Mageants\ProductQA\Controller\Question;
 
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Mageants\ProductQA\Model\ProductQuestionFactory;
use \Mageants\ProductQA\Model\Source\UserType;
use \Mageants\ProductQA\Model\Source\Status;
use \Magento\Store\Model\StoreManagerInterface;
use \Mageants\ProductQA\Helper\Data;
use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Captcha\Observer\CaptchaStringResolver;
use \Magento\Captcha\Helper\Data as CaptchaHelper;
use \Magento\Catalog\Model\Product;
use Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Catalog\Helper\ImageFactory;
use \Mageants\ProductQA\Model\ResourceModel\ProductQuestionAnswer\Collection;
 
class Ask extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Mageants\ProductQA\Model\ResourceModel\ProductQuestionFactory
     */
    protected $_productQuestion = false;
    /**
     * @var _storeManager
     */
    protected $_storeManager;
    /**
     * @var _storeManager
     */
    public const CAPTCHA_FORM_ID = 'qa_captcha_form_1';
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Mageants\ProductQA\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Magento\Captcha\Observer\CaptchaStringResolver
     */
    protected $_captchaStringResolver;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
    
    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_captchaHelper;

   /**
    * @var \Mageants\ProductQA\Model\ResourceModel\ProductQuestionAnswer\Collection
    */
    protected $answerCollection;
    /**
     * @var         \Mageants\ProductQA\Model\ResourceModel\ProductQuestion\Collection
     */
    protected $_productQuestionColl;
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultJsonFactory = false;
    /**
     * @param Data                                                               $helper                [description]
     * @param Context                                                            $context               [description]
     * @param JsonFactory                                                        $resultJsonFactory     [description]
     * @param CustomerSession                                                    $_customerSession      [description]
     * @param ProductQuestionFactory                                             $productQuestion       [description]
     * @param CaptchaStringResolver                                              $captchaStringResolver [description]
     * @param StoreManagerInterface                                              $storeManager          [description]
     * @param CaptchaHelper                                                      $captchaHelper         [description]
     * @param Product                                                            $product               [description]
     * @param TransportBuilder                                                   $transportBuilder      [description]
     * @param ImageFactory                                                       $imageHelperFactory    [description]
     * @param Collection                                                         $answerCollection      [description]
     * @param \Mageants\ProductQA\Model\ResourceModel\ProductQuestion\Collection $productQuestionColl   [description]
     */
    public function __construct(
        Data $helper,
        Context $context,
        JsonFactory $resultJsonFactory,
        CustomerSession $_customerSession,
        ProductQuestionFactory $productQuestion,
        CaptchaStringResolver $captchaStringResolver,
        StoreManagerInterface $storeManager,
        CaptchaHelper $captchaHelper,
        Product $product,
        TransportBuilder $transportBuilder,
        ImageFactory $imageHelperFactory,
        Collection $answerCollection,
        \Mageants\ProductQA\Model\ResourceModel\ProductQuestion\Collection $productQuestionColl
    ) {
        $this->_helper = $helper;
        
        $this->_productQuestion = $productQuestion;
        
        $this->_customerSession = $_customerSession;
        
        $this->_resultJsonFactory = $resultJsonFactory;
        
        $this->_storeManager = $storeManager;
        
        $this->_captchaStringResolver = $captchaStringResolver;
        
        $this->_captchaHelper = $captchaHelper;
        
        $this->_messageManager = $context->getMessageManager();

        $this->_product = $product;

        $this->_transportBuilder = $transportBuilder;

        $this->_imageHelperFactory = $imageHelperFactory;

        $this->answerCollection = $answerCollection;

        $this->_productQuestionColl = $productQuestionColl;
        
        parent::__construct($context);
    }
    /**
     * Execute
     */
    public function execute()
    {
        $formId = self::CAPTCHA_FORM_ID;
        
         $captchaModel = $this->_captchaHelper->getCaptcha($formId);

        if ($captchaModel->isCorrect($this->_captchaStringResolver
            ->resolve($this->getRequest(), $formId)) || !$this->_helper->isCaptchaEnable()) {
            $name = $this->getRequest()->getPost("name");
             
            $email = $this->getRequest()->getPost("email");
             
            $question = $this->getRequest()->getPost("question");
             
            if ($question && $email && $name) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                     
                    $message = __('Error! Invalid email format');
                    $status = 0;
                } else {
                    $question_lenght = $this->_helper->getMaxQuestionLength();
                 
                    if (strlen($question) <= $question_lenght) {
                        $product_id = $this->getRequest()->getParam("product");
                     
                        $user_id = $this->_customerSession->getCustomer()->getId();
                     
                        $ask_by = $user_id;
                     
                        $user_type = UserType::CUSTOMER;
                     
                        $store_id =  $this->_storeManager->getStore()->getId();
                     
                        $status =  Status::STATUS_PENDING;
                     
                        $questionFactory =  $this->_productQuestion->create();
                     
                        $questionFactory->setProductId($product_id)
                        ->setUserId($user_id)
                        ->setAskBy($ask_by)
                        ->setUserType($user_type)
                        ->setStoreId($store_id)
                        ->setStatus($status)
                        ->setName($name)
                        ->setEmail($email)
                        ->setQuestion($question)
                        ->save();

                        $this->sendCustomerEmailNotification($questionFactory);

                        $this->sendAdminEmailNotification($questionFactory);

                        $allQuestions = $this->_productQuestionColl->addFieldToFilter('store_id', $store_id);
                        $allQuestionIds=[];
                        foreach ($allQuestions as $que) {
                            $allQuestionIds[]= $que['id'];
                        }
                         $answerCollection = $this->answerCollection
                         ->addFieldToFilter('question_id', ['in'=>$allQuestionIds]);
                         $answerCollectionData = $answerCollection->getData();
                        if ($questionFactory->getId()) {
                            $this->setAnswer($answerCollectionData,$questionFactory);
                                                        
                            $message = __('Your Question submited successfull. 
                                Please wait until admin approve your question.');
                            $status = 1;
                        } else {
                            $message = __('Error! while saving data. Please try latter.');
                            $status = 0;
                        }
                    } else {
                        $message = __('Question is too long. max %1 allow"', $question_lenght);
                        $status = 0;
                    }
                }
                    
            } else {
                 $message = __('Error! All fields are required.');
                 $status = 0;
            }
        } else {
            $message = __('Invalide security code!');
            $status = 0;
        }
         
         $this->messageManager->getMessages();
         
         $result = $this->_resultJsonFactory->create();
         
         $resultData = [
                'status' => $status,
                'message' => $message ,
            ];

            return $result->setData($resultData);
    }
  /**
   * Send Customer Email Notification
   *
   * @param  mixed $questionFactory [description]
   * @return mixed                  [description]
   */
    public function sendCustomerEmailNotification($questionFactory)
    {
        if (!$questionFactory->getId()) {
            return;
        }
        $enable = $this->_helper->isNotifyCustomer();
        if ($enable) {
                  
                  $to = $questionFactory->getEmail();

                  $productFactory =  $this->_product;

                $product = $productFactory->load($questionFactory->getProductId());

                $product_image = $this->_imageHelperFactory->create()
                ->init($product, 'product_thumbnail_image')->getUrl();

                $data = [
                    'customer_name' => $questionFactory->getName(),
                    "question" => $questionFactory->getQuestion(),
                    "product_link" => $product->getProductUrl(),
                    "product_name" => $product->getName(),
                    "product_image" => $product_image
                ];
                
                $store_id = $questionFactory->getStoreId();

                $emailTo = $this->_helper->sendCustomerEmailSender();

                $store = $this->_storeManager->getStore($store_id);

                $store_email = $store->getConfig('trans_email/ident_' . $emailTo . '/email');
                
                $store_name = $store->getConfig('trans_email/ident_' . $emailTo . '/name');

                $templateId = $this->_helper->customerEmailTemplate();

                if (empty($templateId)) {
                    $templateId = "mageants_productqa_user_email_template";
                }
                if (!empty($to)) {
                    $transport = $this->_transportBuilder
                        ->setTemplateIdentifier($templateId)
                        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                         'store' => $store_id])
                        ->setTemplateVars($data)
                        ->setFrom(['name' => $store_name,'email' => $store_email])
                        ->addTo($to)
                        ->getTransport();
                        
                    $transport->sendMessage();
                }
        }
    }

    public function setAnswer($answerCollectionData,$questionFactory){
        if ($answerCollectionData) {
            foreach ($answerCollectionData as $ansData) {
                $this->sendAnsweredUserEmail($questionFactory, $ansData['email']);
            }
        }
    }

   /**
    * Send Admin Email Notification
    *
    * @param mixed $questionFactory
    * @return mixed
    */
    public function sendAdminEmailNotification($questionFactory)
    {
        if (!$questionFactory->getId()) {
            return;
        }
        $enable = $this->_helper->isNotifyAdmin();
        if ($enable) {
                  $productFactory =  $this->_product;
                  $product = $productFactory->load($questionFactory->getProductId());
                  $product_image = $this->_imageHelperFactory->create()
                  ->init($product, 'product_thumbnail_image')->getUrl();

                $data = [
                    'customer_name' => $questionFactory->getName(),
                    'customer_email' => $questionFactory->getEmail(),
                    "question" => $questionFactory->getQuestion(),
                    "product_link" => $product->getProductUrl(),
                    "product_name" => $product->getName(),
                    "product_image" => $product_image
                ];
                
                $store_id = $questionFactory->getStoreId();

                $emailTo = $this->_helper->sendAdminEmailSender();

                $store = $this->_storeManager->getStore($store_id);

                $store_email = $store->getConfig('trans_email/ident_' . $emailTo . '/email');
                
                $store_name = $store->getConfig('trans_email/ident_' . $emailTo . '/name');

                $templateId = $this->_helper->adminEmailTemplate();
                if (empty($templateId)) {
                    $templateId = "mageants_productqa_admin_email_template";
                }
                if (!empty($store_email)) {
                    $transport = $this->_transportBuilder
                        ->setTemplateIdentifier($templateId)
                        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $store_id])
                        ->setTemplateVars($data)
                        ->setFrom(['name' => $store_name,'email' => $store_email])
                        ->addTo($store_email)
                        ->getTransport();
                        
                    $transport->sendMessage();
                }
        }
    }
   /**
    * Send Answered User Email
    *
    * @param  int|string $questionFactory
    * @param  int|string $toEmail
    * @return mixed
    */
    public function sendAnsweredUserEmail($questionFactory, $toEmail)
    {
        $enable = $this->_helper->isNotifyAnsweredUsers();
        if ($enable) {
                  
                  $to = $toEmail;

                  $productFactory =  $this->_product;

                $product = $productFactory->load($questionFactory->getProductId());

                $product_image = $this->_imageHelperFactory->create()
                ->init($product, 'product_thumbnail_image')->getUrl();

                $data = [
                    'customer_name' => $questionFactory->getName(),
                    'customer_email' => $questionFactory->getEmail(),
                    "question" => $questionFactory->getQuestion(),
                    "product_link" => $product->getProductUrl(),
                    "product_name" => $product->getName(),
                    "product_image" => $product_image
                ];
                
                $store_id = $questionFactory->getStoreId();

                $emailTo = $this->_helper->sendAnsweredUserEmailSender();

                $store = $this->_storeManager->getStore($store_id);

                $store_email = $store->getConfig('trans_email/ident_' . $emailTo . '/email');
                
                $store_name = $store->getConfig('trans_email/ident_' . $emailTo . '/name');

                $templateId = $this->_helper->answeredUserEmailTemplate();
                if (empty($templateId)) {
                    $templateId = "mageants_productqa_email_to_answered_users_template";
                }
                if (!empty($to)) {
                    $transport = $this->_transportBuilder
                        ->setTemplateIdentifier($templateId)
                        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $store_id])
                        ->setTemplateVars($data)
                        ->setFrom(['name' => $store_name,'email' => $store_email])
                        ->addTo($to)
                        ->getTransport();
                        
                    $transport->sendMessage();
                }
        }
    }
}
