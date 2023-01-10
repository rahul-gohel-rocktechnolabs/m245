<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Block\Adminhtml\ProductQuestionAnswer;

use \Magento\Framework\Registry;
use \Magento\Backend\Block\Widget\Context;
        
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Constructor
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        
        parent::__construct($context, $data);
    }
    /**
     * Initialize Post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        
        $this->_blockGroup = 'Mageants_ProductQA';
        
        $this->_controller = 'adminhtml_productQuestion';
        
        parent::_construct();
        
        $this->buttonList->update('save', 'label', __('Save Product Question'));
        
        $this->buttonList->add(
            'save-and-continue',
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
    }
    
    /**
     * Retrieve text for header element depending on loaded Slider
     *
     * @return string
     */
    public function getHeaderText()
    {
        $productquestion = $this->_coreRegistry->registry('mageants_productquestionanswer');
        
        if ($productquestion->getId()) {
            return __("Edit Template '%1'", $this->escapeHtml($productquestion->getName()));
        }
        
        return __('New  Product Question');
    }
}
