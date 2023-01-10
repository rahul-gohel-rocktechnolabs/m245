<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Block\Adminhtml\ProductQuestionAnswer\Edit\Tab;

use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;
use \Mageants\ProductQA\Model\Source\Status;
use \Mageants\ProductQA\Model\Source\Orientation;
use \Magento\Cms\Ui\Component\Listing\Column\Cms\Options;

class ProductQuestionAnswer extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    public const FORM_NAME = 'mageants_productqa_question_form';
    /**
     * @var \Mageants\ProductQA\Model\Source\Status
     */
    protected $_status;
    /**
     * @var \Mageants\ProductQA\Model\Source\Orientation
     */
    protected $_orientation;
     /**
      * @var  \Magento\Cms\Ui\Component\Listing\Column\Cms\Options
      */
    protected $_cmsOpt;
    /**
     * @param Context     $context     [description]
     * @param Registry    $registry    [description]
     * @param FormFactory $formFactory [description]
     * @param Status      $status      [description]
     * @param Options     $cmsOpt      [description]
     * @param array       $data        [description]
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Status $status,
        Options $cmsOpt,
        array $data = []
    ) {
        $this->_cmsOpt = $cmsOpt;
        
        $this->_status = $status;
        
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        
        /** @var \Mageants\ProductQA\Model\ProductQuestion $productquestion */
        $productquestion = $this->_coreRegistry->registry('mageants_productquestionanswer');
        
        $form = $this->_formFactory->create();
        
        $form->setHtmlIdPrefix('productquestionanswer_');
        $form->setFieldNameSuffix('productquestionanswer');
        
         $fieldset = $form->addFieldset(
             'base_fieldset',
             [
                'legend' => __('Setting'),
                'class'  => 'fieldset-wide'
             ]
         );
        if ($productquestion->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }
        
        $fieldset->addField(
            'status',
            'select',
            [
                'name'  => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => $this->_status->toOptionArray()
            ]
        );
        $fieldset->addField(
            'answer',
            'editor',
            [
                'name'  => 'answer',
                'label' => __('Answer'),
                'title' => __('Answer'),
                'required' => true,
            ]
        );
        
        $fieldset->addField(
            'name',
            'text',
            [
                'name'  => 'name',
                'label' => __('Customer Name'),
                'title' => __('Customer Name'),
                'renderer' => Renderer\CustomerColumn::class,
                'required' => true,
            ]
        );
        
        $fieldset->addField(
            'email',
            'text',
            [
                'name'  => 'email',
                'label' => __('Customer Email'),
                'title' => __('Customer Email'),
                'required' => true,
            ]
        );
        
        $productquestionData = $this->_session->getData('mageants_productqa_productquestionanswer_data', true);
       
        if ($productquestionData) {
            $productquestion->addData($productquestionData);
        } else {
            if (!$productquestion->getId()) {
                $productquestion->addData($productquestion->getDefaultValues());
            }
        }
    
        $form->addValues($productquestion->getData());
        
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    /**
     * Prepare ProductQuestion for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
