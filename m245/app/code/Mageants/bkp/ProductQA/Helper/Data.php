<?php
 /**
  * @category  Mageants Product Question Answser
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Helper\Context;
use Magento\Email\Model\Template as EmailTemplate;
use \Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterfac
     */
    protected $_scopeConfig;
    /**
     * @var array
     */
    protected $_productquestionsConfig;
    
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helperBackend;
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_url;
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /*Extention Enable Disable Constant*/
    public const ENABLE = 'mageants_productqa/general/enable';
    public const CAPTCHA = 'mageants_productqa/general/captcha';
    public const QPAGE_SIZE = 'mageants_productqa/general/qpaze_size';
    public const ANSPAGE_SIZE = 'mageants_productqa/general/anspaze_size';
    public const MAX_QUESTION_LENGHT = 'mageants_productqa/general/max_question_lenght';
    public const CONF_SEND_EMAIL = 'mageants_productqa/general/sendemail';
    public const CONF_SEND_EMAIL_TEMPLATE = 'mageants_productqa/general/email_template';
    public const CONF_SEND_EMAIL_TEMPLATE_CSS = 'mageants_productqa/general/email_template_css';
    public const CONF_SEND_EMAIL_SUBJECT = 'mageants_productqa/general/email_subject';
    public const CUSTOMEREMAILENABLE = 'mageants_productqa/user_email/user_notify';
    public const CUSTOMEREMAILSENDER = 'mageants_productqa/user_email/sender';
    public const CUSTOMEREMAILTEMPLATE = 'mageants_productqa/user_email/template';
    public const ADMINMAILENABLE = 'mageants_productqa/admin_email/enable_notify';
    public const ADMINEMAILSENDER = 'mageants_productqa/admin_email/send_to';
    public const ADMINEMAILTEMPLATE = 'mageants_productqa/admin_email/template';
    public const ADMINMAILENABLE_ANSWER = 'mageants_productqa/admin_email/enable_notify_foranswer';
    public const ADMINEMAILSENDER_ANSWER = 'mageants_productqa/admin_email/send_to_foranswer';
    public const ADMINEMAILTEMPLATE_ANSWER = 'mageants_productqa/admin_email/template_foranswer';
    public const USELIKEDISLIKESYMBOL = 'mageants_productqa/general/use_like_symbol';
    public const ANSWERED_EMAIL_ENABLE = 'mageants_productqa/email_to_answered_users/enable_email_to_answered_users';
    public const ANSWERED_USERS_EMAIL_SENDER = 'mageants_productqa/email_to_answered_users/sender';
    public const ANSWERED_USERS_EMAIL_TEMPLATE = 'mageants_productqa/email_to_answered_users/template';
    
     /**
      * @param Context                      $context
      * @param \Magento\Backend\Helper\Data $HelperBackend
      * @param SerializerInterface          $serializer
      */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Data $HelperBackend,
        SerializerInterface $serializer
    ) {
        
        $this->_url = $context->getUrlBuilder();
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_helperBackend = $HelperBackend;
        $this->serializer = $serializer;
    }
    
    /**
     * Retrieve editor variable url
     *
     * @return string
     */
    public function getEditorVariableUrl()
    {
        return $this->_url->getUrl('mageants_productqa/variable/template');
    }
    /**
     * Retrieve question per page
     *
     * @return boolean
     */
    public function getQuestionPageSize()
    {
        return $this->_scopeConfig->getValue(self::QPAGE_SIZE, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Retrieve answer per page
     *
     * @return boolean
     */
    public function getAnswerPageSize()
    {
        return $this->_scopeConfig->getValue(self::ANSPAGE_SIZE, ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Retrieve max character allow in question
     *
     * @return int
     */
    public function getMaxQuestionLength()
    {
        return $this->_scopeConfig->getValue(self::MAX_QUESTION_LENGHT, ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Retrieve extention enable or disable
     *
     * @return boolean
     */
    public function isExtentionEnable()
    {
        return $this->_scopeConfig->getValue(self::ENABLE, ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Retrieve captcha enable or disable
     *
     * @return boolean
     */
    public function isCaptchaEnable()
    {
        return $this->_scopeConfig->getValue(self::CAPTCHA, ScopeInterface::SCOPE_STORE);
    }
     /**
      * Retrieve is email send enable or not
      *
      * @param  int  $store
      * @return boolean
      */
    public function isEmailEnable($store = null)
    {
        if ($store) {
            $data =  $store->getConfig(self::CONF_SEND_EMAIL);
        } else {
             $data = $this->_scopeConfig->getValue(self::CONF_SEND_EMAIL, ScopeInterface::SCOPE_STORE);
        }
        return $data;
    }
    /**
     * Retrieve email subject
     *
     * @param  int  $store
     * @return string
     */
    public function getEmailSubject($store = null)
    {
        if ($store) {
            $data =  $store->getConfig(self::CONF_SEND_EMAIL_SUBJECT);
        } else {
             $data = $this->_scopeConfig->getValue(self::CONF_SEND_EMAIL_SUBJECT, ScopeInterface::SCOPE_STORE);
        }
        return $data;
    }
    /**
     * Retrieve email html template
     *
     * @param  int  $store
     * @return string
     */
    public function getEmailTemplate($store = null)
    {
        if ($store) {
            $data =  $store->getConfig(self::CONF_SEND_EMAIL_TEMPLATE);
        } else {
             $data = $this->_scopeConfig->getValue(self::CONF_SEND_EMAIL_TEMPLATE, ScopeInterface::SCOPE_STORE);
        }
        return $data;
    }
    
    /**
     * Retrieve email template css
     *
     * @param  int  $store
     * @return string
     */
    public function getEmailTemplateCss($store = null)
    {
        $data='';
        if ($store) {
            $data =  $store->getConfig(self::CONF_SEND_EMAIL_TEMPLATE_CSS);
        }
        return $data;
    }
    
    /**
     * Retrieve serialize setting
     *
     * @param  int|sting $data
     * @return array
     */
    public function serializeSetting($data)
    {
         return $this->serializer->serialize($data);
    }
        
    /**
     * Retrieve unserialize setting
     *
     * @param  int|sting $string
     * @return array
     */
    public function unserializeSetting($string)
    {
        $data = [];
        
        if (!empty($string)) {
            $data =  $this->serializer->unserialize($string);
        }
        
        return $data;
    }

    /**
     * Retrieve customer email enable or disable
     *
     * @return boolean
     */
    public function isNotifyCustomer()
    {
        return $this->_scopeConfig->getValue(self::CUSTOMEREMAILENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve customer email sender enable or disable
     *
     * @return boolean
     */
    public function sendCustomerEmailSender()
    {
        return $this->_scopeConfig->getValue(self::CUSTOMEREMAILSENDER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve customer email template
     *
     * @return boolean
     */
    public function customerEmailTemplate()
    {
        return $this->_scopeConfig->getValue(self::CUSTOMEREMAILTEMPLATE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve admin email enable or disable
     *
     * @return boolean
     */
    public function isNotifyAdmin()
    {
        return $this->_scopeConfig->getValue(self::ADMINMAILENABLE, ScopeInterface::SCOPE_STORE);
    }
     /**
      * Retrieve admin email enable or disable
      *
      * @return boolean
      */
    public function isNotifyAnsweredUsers()
    {
        return $this->_scopeConfig->getValue(self::ANSWERED_EMAIL_ENABLE, ScopeInterface::SCOPE_STORE);
    }
     /**
      * Retrieve customer email sender enable or disable
      *
      * @return boolean
      */
    public function sendAnsweredUserEmailSender()
    {
        return $this->_scopeConfig->getValue(self::ANSWERED_USERS_EMAIL_SENDER, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Answered User Email Template
     *
     * @return [type] [description]
     */
    public function answeredUserEmailTemplate()
    {
        return $this->_scopeConfig->getValue(self::ANSWERED_USERS_EMAIL_TEMPLATE, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Retrieve admin email enable or disable
     *
     * @return boolean
     */
    public function isNotifyAdminForAnswer()
    {
        return $this->_scopeConfig->getValue(self::ADMINMAILENABLE_ANSWER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve customer email sender enable or disable
     *
     * @return boolean
     */
    public function sendAdminEmailSender()
    {
        return $this->_scopeConfig->getValue(self::ADMINEMAILSENDER, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Retrieve customer email sender enable or disable
     *
     * @return boolean
     */
    public function sendAdminEmailSenderForAnswer()
    {
        return $this->_scopeConfig->getValue(self::ADMINEMAILSENDER_ANSWER, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Retrieve admin email template
     *
     * @return boolean
     */
    public function adminEmailTemplate()
    {
        return $this->_scopeConfig->getValue(self::ADMINEMAILTEMPLATE, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Retrieve admin email template
     *
     * @return boolean
     */
    public function adminEmailTemplateForAnswer()
    {
        return $this->_scopeConfig->getValue(self::ADMINEMAILTEMPLATE_ANSWER, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Retrieve customer email template
     *
     * @return boolean
     */
    public function useLikeDislikeSymbol()
    {
        return $this->_scopeConfig->getValue(self::USELIKEDISLIKESYMBOL, ScopeInterface::SCOPE_STORE);
    }
}
