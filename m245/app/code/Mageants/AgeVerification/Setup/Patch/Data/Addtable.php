<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mageants\AgeVerification\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Store\Model\StoreManagerInterface;
// use Magento\Framework\Registry;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class Addtable implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

/**
 * Undocumented function
 *
 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
 * @param ModuleDataSetupInterface $moduleDataSetup
 * @param StoreManagerInterface $StoreManager
 * @param EavSetupFactory $eavSetupFactory
 * @param Registry $Registry
 * @param \Mageants\AgeVerification\Helper\Data $DataPatchInterface
 */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ModuleDataSetupInterface $moduleDataSetup,
        StoreManagerInterface $StoreManager,
        EavSetupFactory $eavSetupFactory,
        // Registry $Registry,
        // DataPatchInterface $DataPatchInterface
        \Mageants\AgeVerification\Model\PopupTemplateFactory $PopupTemplateFactory,
        \Mageants\AgeVerification\Helper\Data $DataPatchInterface
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->StoreManager = $StoreManager;
        $this->scopeConfig = $scopeConfig;
        // $this->Registry = $Registry;
        $this->_PopupTemplateFactory = $PopupTemplateFactory;
        $this->DataPatchInterface = $DataPatchInterface;
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' =>  $this->moduleDataSetup]);
        $eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'age_verification', [
            'type'     => 'int',
            'label'    => 'age verification',
            'input'    => 'boolean',
            'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
            'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => 'General Information',
                'backend' => ''
        ]);
        // $this->Registry;
        $popupanimation = $this->DataPatchInterface->getPopupAnimation();
        $content =  $this->DataPatchInterface->getContentText();
        $checkbox_text = $this->DataPatchInterface->getCheckboxVerifyText();
        // $popupanimation = $this->DataPatchInterface;
        // $content =  $this->DataPatchInterface;
        // $checkbox_text = $this->DataPatchInterface;
        
        $currentStore = $this->StoreManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        $img_detail = $mediaUr.'templatelogo/default/no-admittance-98740_960_720_2_2.png';
        
        $template_name_1='AgeVerification Latest';
        $template_status_1=1;
        $template_content_1='
        
        <div id="verify_txt">
            <span>your age must be 18 year or older to access this site.</span>
        </div><br/>';
         
        $template_css_1='
        
        #verify_txt{
            text-align:center;
            color:#333333;  
            font-weight: bold;   
        }
        
        .ageVerification_popup .title{
            background-color:#3f98f2;
            color:#333333;
        }
        aside.modal-popup.modal-slide._inner-scroll._show .modal-inner-wrap{
            background-color:white;            
        }
        .ageVerification-dob div.dob_label{
            color: #333333;
        }
        .default_popup button.enter_button, 
        .custom_popup button.enter_button, .ageVerification_captcha button#recaptcha, button.leave_button{
            color: #333333;   
            font-weight: bold; 
            background-color:#3f98f2;
        }
        .ageVerification-dob div.dob_label{
            color: #333333;
        }
        .default_popup button.enter_button, 
        .custom_popup button.enter_button, .ageVerification_captcha button#recaptcha, button.leave_button{
            color: #333333;   
            font-weight: bold; 
        }
        .ageVerification-dob div.dob_label{
            color: #333333; 
            font-weight: bold;   
        }
        #content_text{
            color: #333333; 
            font-weight: bold;   
        }
        #checkbox_text{
            color: #333333; 
            font-weight: bold;   
        }
        ';
        $template_js_1="
        require(['jquery', 'jquery/ui'], function($){ 
            $(document).ready(function(){
                
                if($('#ageVerification_check1').attr('checked', false)){
                    console.log('call');
                    var content = 'Not allowed';
                    $('#default_notAllow1').innerHTML = content;
                }
                else{
                    $('#default_notAllow1').innerHTML = '';    
                }
                var popupanimation = '$popupanimation';
                var mediaUrl = '$mediaUrl'
                console.log(popupanimation);
                console.log(mediaUrl);
                setTimeout(function(){ 
                    console.log('call'); 
                    $('.modal-inner-wrap').removeClass('animated '+popupanimation);
                    $('.ageVerification_popup').modal('closeModal'); 
                    // $('.modal-inner-wrap').addClass('animated flipInX'); 
                    // $('.ageVerification_popup').modal('openModal'); 
                }, 750);
                setTimeout(function(){ 
                    console.log('call 1'); 
                    // $('.ageVerification_popup').modal('closeModal'); 
                    // $('.modal-inner-wrap').removeClass('animated slideInDown');
                    $('.modal-inner-wrap').addClass('animated bounceIn'); 
                    $('.ageVerification_popup').modal('openModal'); 
                }, 755);
            });    
         });";
        $templates = [
            [
            'template_name' => $template_name_1,
            'status' => $template_status_1,
            'content' => $template_content_1,
            'popup_css' => $template_css_1,
            'popup_script' => $template_js_1,
            ]
        ];

        $template_name_2='AgeVerification Ultimate';
        $template_status_2=1;
        $template_content_2='
        <label class="label content_text" for="ageVerification" id="content_text">
            <span>you must verify that you are 18 year of age or older to enter this site.</span>
        </label><br/><br/>';
        
        $template_css_2='
        .ageVerification_popup .title{
            background-color: #e6b72b;
            color: #333333;
        }
        aside.modal-popup.modal-slide._inner-scroll._show .modal-inner-wrap{
            background-color:white;            
        }
        .ageVerification-dob div.dob_label{
            color: #333333;
        }
        .default_popup button.enter_button, 
        .custom_popup button.enter_button, .ageVerification_captcha button#recaptcha, button.leave_button{
            color: #333333;   
            font-weight: bold; 
        }
        .ageVerification-dob div.dob_label{
            color: #333333; 
            font-weight: bold;   
        }
        #content_text{
            color: #333333; 
            font-weight: bold;   
        }
        #checkbox_text{
            color: #333333; 
            font-weight: bold;   
        }';
        $template_js_2="
        require(['jquery', 'jquery/ui'], function($){ 
            $(document).ready(function(){
                var popupanimation = '$popupanimation';
                var mediaUrl = '$mediaUrl'
                console.log(popupanimation);
                console.log(mediaUrl);
                setTimeout(function(){ 
                    console.log('call'); 
                    $('.modal-inner-wrap').removeClass('animated '+popupanimation);
                    $('.ageVerification_popup').modal('closeModal'); 
                    // $('.modal-inner-wrap').addClass('animated flipInX'); 
                    // $('.ageVerification_popup').modal('openModal'); 
                }, 750);

                setTimeout(function(){ 
                    console.log('call 1'); 
                    // $('.ageVerification_popup').modal('closeModal'); 
                    // $('.modal-inner-wrap').removeClass('animated slideInDown');
                    $('.modal-inner-wrap').addClass('animated flipInX'); 
                    $('.ageVerification_popup').modal('openModal'); 
                }, 755);
            });    
        });";
        $templates = [
            [
            'template_name' => $template_name_2,
            'status' => $template_status_2,
            'content' => $template_content_2,
            'popup_css' => $template_css_2,
            'popup_script' => $template_js_2,
            ],
        ];

        $templates = [
            [
            'template_name' => $template_name_1,
            'status' => $template_status_1,
            'content' => $template_content_1,
            'popup_css' => $template_css_1,
            'popup_script' => $template_js_1,
            ],
            [
            'template_name' => $template_name_2,
            'status' => $template_status_2,
            'content' => $template_content_2,
            'popup_css' => $template_css_2,
            'popup_script' => $template_js_2,
            ]
        ];
        $post = $this->_PopupTemplateFactory->create();
        foreach ($templates as $data) {
            $post->setData($data)->save();
        }
        $this->moduleDataSetup->endSetup();
    }
    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
