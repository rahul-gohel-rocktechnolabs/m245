<?php
namespace Mageants\FacebookChat\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    const FACEBOOKCHAT_GENERAL = 'facebookchat/general/';

    protected $scopeConfig;
    
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }
    public function getConfigValue($configPath)
    {
        return $this->scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function isEnable()
    {
        return $this->getConfigValue(self::FACEBOOKCHAT_GENERAL . 'enable');
    }
    public function getLoginMsg()
    {
        return $this->getConfigValue(self::FACEBOOKCHAT_GENERAL . 'login_msg');
    }
    public function getGuestMsg()
    {
        return $this->getConfigValue(self::FACEBOOKCHAT_GENERAL . 'guest_msg');
    }
    public function getFacebookPageId()
    {
        return $this->getConfigValue(self::FACEBOOKCHAT_GENERAL . 'facebook_pageid');
    }
    public function getThemeColor()
    {
        return $this->getConfigValue(self::FACEBOOKCHAT_GENERAL . 'themecolor');
    }
    public function getIconPostion()
    {
        return $this->getConfigValue(self::FACEBOOKCHAT_GENERAL . 'icon_position');
    }
    public function getDisplayAt()
    {
        return $this->getConfigValue(self::FACEBOOKCHAT_GENERAL . 'display_at');
    }
}
