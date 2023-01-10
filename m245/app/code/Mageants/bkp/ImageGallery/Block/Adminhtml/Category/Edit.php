<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Block\Adminhtml\Category;

/**
 * CMS block edit form container
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'category_id';
        $this->_blockGroup = 'Mageants_ImageGallery';
        $this->_controller = 'adminhtml_category';
        $this->_headerText = __('Manage Image Category');
            
        parent::_construct();

        if ($this->_isAllowedAction('Mageants_ImageGallery::save')) {
            $this->buttonList->update('save', 'label', __('Save Image Category'));
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

        if ($this->_isAllowedAction('Mageants_ImageGallery::category_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Image Categroy'));
        } else {
            $this->buttonList->remove('delete');
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
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
    
    /**
     * Get header text
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('imagegalley_category')->getId()) {
            return __("Edit Category");
        } else {
            return __('New Image Category');
        }
    }
}
