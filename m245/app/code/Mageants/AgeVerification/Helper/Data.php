<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $storeManager;
    public const XML_PATH_MODULE_ENABLE = 'age_verification/general/enable';
    public const XML_PATH_VERIFICATION_BASED_ON = 'age_verification/general/verification_based_on';
    public const XML_PATH_VERIFICATION_PAGE = 'age_verification/general/verification_pages';
    public const XML_PATH_VERIFICATION_AGE = 'age_verification/general/verification_age';
    public const XML_PATH_COOKIE_INTERVAL = 'age_verification/general/cookie_interval';
    public const XML_PATH_TEMPLATE = 'age_verification/age_verification_popup_templates/select_template';
    public const XML_PATH_LOGO_IMAGE = 'age_verification/popup_personalization/logo/image';
    public const XML_PATH_POPUP_TITLE = 'age_verification/popup_personalization/text/popup_title';
    public const XML_PATH_CONTENT_TEXT = 'age_verification/popup_personalization/text/popup_content_text';
    public const XML_PATH_CHECKBOX_VERIFY_TEXT = 'age_verification/popup_personalization/text/checkbox_verify_text';
    public const XML_PATH_CHECKBOX_ENABLE = 'age_verification/popup_personalization/text/checkBoxenable';
    public const XML_PATH_DOB_PATTERN = 'age_verification/popup_personalization/date_of_birth_fields/select_patterns';
    public const XML_PATH_DOB_PATTERN_ENABLE = 'age_verification/popup_personalization/date_of_birth_fields/Dateenable';
    public const XML_PATH_LEAVE_BTN_LABEL = 'age_verification/popup_personalization/buttons/popup_leave_button_label';
    public const XML_PATH_LEAVE_BTN_URL = 'age_verification/popup_personalization/buttons/popup_leave_button_url';
    public const XML_PATH_ENTER_BTN_URL = 'age_verification/popup_personalization/buttons/popup_enter_button_url';
    public const XML_PATH_ENTER_BTN_LABEL = 'age_verification/popup_personalization/buttons/popup_enter_button_label';
    public const XML_PATH_POPUP_BORDER_COLOR = 'age_verification/popup_personalization/appearance/popup_border_color';
    public const
    XML_PATH_POPUP_BACKGROUND_COLOR = 'age_verification/popup_personalization/appearance/popup_background_color';
    public const
     XML_PATH_CONTENT_TEXT_COLOR = 'age_verification/popup_personalization/appearance/popup_content_text_color';
    // @codingStandardsIgnoreLine
    public const XML_PATH_TITLE_BACKGROUND_COLOR = 'age_verification/popup_personalization/appearance/popup_title_background_color';
    public const XML_PATH_TITLE_TEXT_COLOR = 'age_verification/popup_personalization/appearance/popup_title_text_color';
    // @codingStandardsIgnoreLine
    public  const XML_PATH_BUTTON_BACKGROUND_COLOR = 'age_verification/popup_personalization/appearance/popup_button_background_color';
    public const
    XML_PATH_BUTTON_TEXT_COLOR = 'age_verification/popup_personalization/appearance/popup_button_text_color';
    public const XML_PATH_LOADER_COLOR = 'age_verification/popup_personalization/appearance/loader_color';
    public const XML_PATH_POPUP_ANIMATION = 'age_verification/popup_personalization/appearance/popup_animation';
    public const XML_PATH_CAPTCHA_ENABLE = 'age_verification/google_captcha_configuration/enable';
    public const XML_PATH_CAPTCHA_TYPE = 'age_verification/google_captcha_configuration/google_recaptcha_type';
    public const
    XML_PATH_CAPTCHA_VISIBLE_SITE_KEY = 'age_verification/google_captcha_configuration/visible_site_key';
    public const
    XML_PATH_CAPTCHA_VISIBLE_SECRET_KEY = 'age_verification/google_captcha_configuration/visible_secret_key';
    public const
    XML_PATH_CAPTCHA_INVISIBLE_SITE_KEY = 'age_verification/google_captcha_configuration/invisible_site_key';
    public const
    XML_PATH_CAPTCHA_INVISIBLE_SECRET_KEY = 'age_verification/google_captcha_configuration/invisible_secret_key';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getAgeVerificationEnable()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_MODULE_ENABLE, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getVerificationBasedOn()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_VERIFICATION_BASED_ON, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getVerificationPage()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_VERIFICATION_PAGE, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getVerificationAge()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_VERIFICATION_AGE, $storeScope);
    }
  /**
   * Undocumented function
   *
   * @return void
   */
    public function getCookieInterval()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_COOKIE_INTERVAL, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getTemplate()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getLogoImage()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_LOGO_IMAGE, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getPopupTitle()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_POPUP_TITLE, $storeScope);
    }
  /**
   * Undocumented function
   *
   * @return void
   */
    public function getContentText()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CONTENT_TEXT, $storeScope);
    }
  /**
   * Undocumented function
   *
   * @return void
   */
    public function getCheckboxVerifyText()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CHECKBOX_VERIFY_TEXT, $storeScope);
    }
  /**
   * Undocumented function
   *
   * @return void
   */
    public function getCheckboxEnable()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CHECKBOX_ENABLE, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getDobPattern()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_DOB_PATTERN, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getDobEnable()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_DOB_PATTERN_ENABLE, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getLeaveBtnLabel()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_LEAVE_BTN_LABEL, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getLeaveBtnUrl()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_LEAVE_BTN_URL, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getEnterBtnLabel()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENTER_BTN_LABEL, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getEnterBtnUrl()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENTER_BTN_URL, $storeScope);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getPopupBorderColor()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_POPUP_BORDER_COLOR, $storeScope);
    }
 /**
  * Undocumented function
  *
  * @return void
  */
    public function getPopupBackgroundColor()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_POPUP_BACKGROUND_COLOR, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getContentTextColor()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CONTENT_TEXT_COLOR, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getTitleBackgroundColor()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_TITLE_BACKGROUND_COLOR, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getTitleTextColor()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_TITLE_TEXT_COLOR, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getButtonBackgroundColor()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_BUTTON_BACKGROUND_COLOR, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getButtonTextColor()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_BUTTON_TEXT_COLOR, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getLoaderColor()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_LOADER_COLOR, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getPopupAnimation()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_POPUP_ANIMATION, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getCaptchaEnable()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        // return $this->scopeConfig->getValue()->getCollection();

        return $this->scopeConfig->getValue(self::XML_PATH_CAPTCHA_ENABLE, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getCaptchaType()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CAPTCHA_TYPE, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getCaptchaVisibleSiteKey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CAPTCHA_VISIBLE_SITE_KEY, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getCaptchaVisibleSecretKey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CAPTCHA_VISIBLE_SECRET_KEY, $storeScope);
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function getCaptchaInvisibleSiteKey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CAPTCHA_INVISIBLE_SITE_KEY, $storeScope);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getCaptchaInvisibleSecretKey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CAPTCHA_INVISIBLE_SECRET_KEY, $storeScope);
    }
}
