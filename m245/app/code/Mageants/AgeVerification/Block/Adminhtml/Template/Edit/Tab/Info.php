<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Block\Adminhtml\Template\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Mageants\AgeVerification\Model\System\Config\Status;
use Magento\Store\Model\System\Store;

class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    public $wysiwygConfig;
    /**
     * @var \Mageants\AgeVerification\Model\System\Config\Status
     */
    public $templateStatus;
    /**
     * @var \Magento\Store\Model\System\Store
     */
    public $systemStore;
    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Status $templateStatus
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Status $templateStatus,
        Store $systemStore,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->templateStatus = $templateStatus;
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    // @codingStandardsIgnoreLine
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('ageverification_template');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('template_');
        $form->setFieldNameSuffix('template');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );
        if ($model) {
            $fieldset->addField(
                'template_id',
                'hidden',
                ['name' => 'template_id']
            );
        }
                
        $fieldset->addField(
            'template_name',
            'text',
            [
                'name' => 'template_name',
                'label' => __('Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'options' => $this->templateStatus->getOptionArray()
            ]
        );
        $fieldset->addField(
            'store_views',
            'select',
            [
                'name' => 'store_views',
                'label' => __('Store Views'),
                'required' => true,
                'values'   => $this->systemStore->getStoreValuesForForm(false, true),
            ]
        );
        $wysiwygConfig = $this->wysiwygConfig->getConfig();
        $fieldset->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'label' => __('Content'),
                'required' => true,
                'config' => $wysiwygConfig
            ]
        );
        $fieldset->addField(
            'popup_css',
            'textarea',
            [
                'name' => 'popup_css',
                'label' => __('Popup CSS'),
                'required' => true,
                'style' => 'height: 15em; width: 30em;'
            ]
        );
        $fieldset->addField(
            'popup_script',
            'textarea',
            [
                'name' => 'popup_script',
                'label' => __('Popup script'),
                'required' => true,
                'style' => 'height: 15em; width: 30em;'
            ]
        );
        if ($model) {
            $data = $model->getData();
            $form->setValues($data);
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Popup Info');
    }
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Popup Info');
    }
   /**
    * Undocumented function
    *
    * @return boolean
    */
    public function canShowTab()
    {
        return true;
    }
   /**
    * Undocumented function
    *
    * @return boolean
    */
    public function isHidden()
    {
        return false;
    }
}
