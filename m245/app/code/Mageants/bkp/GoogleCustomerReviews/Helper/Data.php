<?php

namespace Mageants\GoogleCustomerReviews\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $scopeConfig;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    
    /**
     * @return bool
     */
    public function isEnable()
    {
        return (bool) $this->scopeConfig->getValue('googlecustomerreviews/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getMerchantId()
    {
        return (int) $this->scopeConfig->getValue('googlecustomerreviews/general/merchant_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->scopeConfig->getValue('googlecustomerreviews/general/language',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getSurveyPosition()
    {
        return $this->scopeConfig->getValue('googlecustomerreviews/survey/postition_popup',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getCustomerGroups()
    {
        return explode(',',$this->scopeConfig->getValue('googlecustomerreviews/survey/customer_group',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }
    
    /**
     * @return boolean
     */
    public function isBadge()
    {
        return (bool) $this->scopeConfig->getValue('googlecustomerreviews/badge/show',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * @return string
     */
    public function getBadgePosition()
    {
        return $this->scopeConfig->getValue('googlecustomerreviews/badge/position_badge',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}