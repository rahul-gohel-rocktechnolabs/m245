<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Block\Adminhtml\Template;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends Container
{
   /**
    * Undocumented variable
    *
    * @var mixed
    */
    public $coreRegistry = null;
    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_template';
        $this->_blockGroup = 'Mageants_AgeVerification';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]

            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete'));
        $this->buttonList->remove('reset');
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getHeaderText()
    {
        $templateRegistry = $this->coreRegistry->registry('ageverification_template');
        if ($templateRegistry->getId()) {
            $templateTitle = $this->escapeHtml($templateRegistry->getTitle());
            return __("Edit Template '%1'", $templateTitle);
        } else {
            return __('Add Template');
        }
    }
    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    // @codingStandardsIgnoreLine
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
		function toggleEditor() {
		if (tinyMCE.getInstanceById('post_content') == null) {
		tinyMCE.execCommand('mceAddControl', false, 'post_content');
		} else {
		tinyMCE.execCommand('mceRemoveControl', false, 'post_content');
		}
		};
		";
        return parent::_prepareLayout();
    }
}
