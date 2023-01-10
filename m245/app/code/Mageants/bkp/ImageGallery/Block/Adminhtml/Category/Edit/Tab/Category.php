<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Block\Adminhtml\Category\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;

class Category extends Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('imagegalley_category');
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category')]);

        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', ['name' => 'category_id']);
        }

        $fieldset->addField(
            'category_name',
            'text',
            [
                'name' => 'category_name',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
            ]
        );
        
        $Lastfield =$fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'note' => 'Allow image type: jpg, jpeg, gif, png',
                'required' => true,
            ]
        );
        
         $fieldset->addField(
             'url_key',
             'text',
             [
                'name' => 'url_key',
                'label' => __('Url Key'),
                'title' => __('Url Key'),
                'note' => 'i.e, mageants-image-gallery',
                'required' => true,
             ]
         );
         
         $fieldset->addField(
             'is_active',
             'select',
             [
                'label' => __('Status'),
                'title' => __('Page Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
             ]
         );

         $Lastfield->setAfterElementHtml(
             '<script>
                        //< ![C

                        require(["jquery", "jquery/ui"], function(jQuery)
                        {   
                        jQuery(function ($) 
                        {   
                            var check =jQuery(".admin__field").find("#image_image");
                            var value = check.attr("class");
                            if(typeof value == "undefined"){
                             jQuery("#image").addClass("required-entry required-file ");
                            }

                        }); });

                        //]]>
                </script>'
         );
        
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
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
        return __('Category');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Category');
    }

    /**
     * { @inheritdoc }
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * { @inheritdoc }
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
