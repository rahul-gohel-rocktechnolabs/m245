<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Block\Adminhtml\Video;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    public $request;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->request = $request;
        parent::__construct($context, $data);
    }

    /**
     * Department edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'video_id';
        $this->_blockGroup = 'Mageants_ImageGallery';
        $this->_controller = 'adminhtml_video';

        parent::_construct();
        if ($this->_coreRegistry->registry('imagegallery_video')->getId()) {
            $this->addButton(
                'delete',
                [
                    'label' => __('Delete'),
                    'onclick' => 'deleteConfirm(' . json_encode(__('Are you sure you want to do this?'))
                        . ','
                        . json_encode(
                            $this->getDeleteUrl()
                        )
                        . ')',
                    'class' => 'scalable delete',
                    'level' => -1
                ]
            );
        }
        if ($this->_isAllowedAction('Mageants_ImageGallery::imagegallery')) {
            $this->buttonList->update('save', 'label', __('Save'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Mageants_ImageGallery::imagegallery')) {
            $this->buttonList->update('delete', 'label', __('Delete'));
        } else {
            $this->buttonList->remove('delete');
        }
    }
    
    /**
     * Get delete url
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('imagegallery/video/delete', ['_current' => true]);
    }

    /**
     * Get header with Department name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('imagegallery_video')->getId()) {
            return __("Edit video");
        } else {
            return __('New video');
        }
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

    /**
     * Getter of url for "Save and Continue" button
     *
     * Tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('imageGallery/video/save', ['_current' => true, 'back' => 'edit']);
    }
    
    /**
     * Prepare form Html. call the phtm file with form.
     *
     * @return string
     */
    public function getFormHtml()
    {
        // get the current form as html content.
        $html = parent::getFormHtml();
        //Append the phtml file after the form content.
        $html .= $this->setTemplate('Mageants_ImageGallery::videos/video.phtml')->toHtml();
        return $html;
    }

    /**
     * Prepare
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
    /**
     * Get param id
     */
    public function getParamId()
    {
        return $this->request->getParam('id');
    }
}
