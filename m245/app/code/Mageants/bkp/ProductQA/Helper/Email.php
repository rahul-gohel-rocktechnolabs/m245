<?php
 /**
  * @category  Mageants Product Question Answser
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Helper;

use Magento\Framework\Mail\Template\TransportBuilder;
use \Mageants\ProductQA\Model\ProductQuestionFactory;
use \Magento\Catalog\Model\Product;
use \Magento\Catalog\Helper\ImageFactory;
use \Mageants\ProductQA\Helper\Data as QaHelper ;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Mageants\ProductQA\Model\Source\Status;

class Email
{
    
    public const MAIL_SEND_SUCCESS = 1;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterfac
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product = false;
    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $_imageHelperFactory = false;
    /**
     * @var \Mageants\ProductQA\Model\ResourceModel\ProductQuestionFactory
     */
    protected $_productQuestion = false;
    /**
     * @var \Mageants\ProductQA\Helper\Data
     */
    protected $_qaHelper;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @param TransportBuilder       $transportBuilder   [description]
     * @param Product                $product            [description]
     * @param ImageFactory           $imageHelperFactory [description]
     * @param ProductQuestionFactory $productQuestion    [description]
     * @param QaHelper               $qaHelper           [description]
     * @param TimezoneInterface      $timezone           [description]
     * @param StoreManagerInterface  $storeManager       [description]
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Product $product,
        ImageFactory $imageHelperFactory,
        ProductQuestionFactory $productQuestion,
        QaHelper $qaHelper,
        TimezoneInterface $timezone,
        StoreManagerInterface $storeManager
    ) {
        $this->_productQuestion = $productQuestion;
        
        $this->_product = $product;
        
        $this->_imageHelperFactory = $imageHelperFactory;
        
        $this->_qaHelper = $qaHelper;
        
        $this->_timezone = $timezone;
    
        $this->_storeManager = $storeManager;
        
        $this->_transportBuilder = $transportBuilder;
    }
    
    /**
     * Retrieve question per page
     *
     * @param int $answerFactory
     * @return boolean
     */
    public function sendEmail($answerFactory)
    {
        
        if (!$answerFactory->getId()) {
            return;
        }
        
        $to=[];
        $data=[];
        
        $question_id = $answerFactory->getQuestionId();
            
        $questionFactory =  $this->_productQuestion->create();
        
        $question = $questionFactory->load($question_id);
        
        $store_id = $question->getStoreId();
        
        $store = $this->_storeManager->getStore($store_id);
        if (!$answerFactory->getMailSendStatus() && $this->_qaHelper->isEmailEnable($store)) {
            if ($question->getStatus() == Status::STATUS_APPROVE) {
                $productFactory =  $this->_product;
                $product = $productFactory->load($question->getProductId());
                $product_image = $this->_imageHelperFactory->create()
                ->init($product, 'product_thumbnail_image')->getUrl();
                $answers = $answerFactory->getCollection()
                ->addFieldToFilter("question_id", $question_id)->addFieldToSelect("email");
                $answers->getSelect()->group("email");
                $to[] = $question->getEmail();
                foreach ($answers as $ans) {
                    $to[] = $ans->getEmail();
                }
                
                $data = [
                    'store' => $store,
                    'store_id' => 1,
                    "question" => $question->getQuestion(),
                    "question_username" => $question->getName(),
                    "question_date" => $this->_timezone->formatDate($question->getCreatedAt()),
                    "new_answer" => $answerFactory->getAnswer(),
                    "new_answer_username" => $answerFactory->getName(),
                    "new_answer_date" => $this->_timezone->formatDate($answerFactory->getCreatedAt()),
                    "product_image" => $product_image,
                    "product_url" => $product->getProductUrl(),
                    "product_name" => $product->getName()
                ];
                
                $store_id = $data['store_id'];
                $store = $data['store'];
                $data["store_obj"] = $store;
                
                $store_email = $store->getConfig('trans_email/ident_general/email');
                
                $store_name = $store->getConfig('trans_email/ident_general/name');
                if (!empty($to)) {
                    $transport = $this->_transportBuilder
                        ->setTemplateIdentifier('productqa_email_template')
                        ->setTemplateModel(\Mageants\ProductQA\Model\Email\BackendTemplate::class)
                        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                          'store' => $store_id])
                        ->setTemplateVars($data)
                        ->setFrom(['name' => $store_name,'email' => $store_email])
                        ->addTo($to)
                        ->getTransport();
                        
                    $transport->sendMessage();
                    
                    $answerFactory->setMailSendStatus(self::MAIL_SEND_SUCCESS)->save();
                }
            }
        }
    }
}
