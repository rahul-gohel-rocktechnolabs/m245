<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Block\Adminhtml\ProductQuestion\Edit\Tab;

use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;
use \Magento\Cms\Ui\Component\Listing\Column\Cms\Options;

class QuestionAnswer extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    
   /**
    * Prepare form
    *
    * @return $this
    */
    protected function _prepareForm()
    {
        /** @var \Mageants\ProductQA\Model\ProductQuestion $productquestion */
        $productquestion = $this->_coreRegistry->registry('mageants_productquestion');
        
        $form = $this->_formFactory->create();
        
        $form->setHtmlIdPrefix('answers_');
        $form->setFieldNameSuffix('answers');
        
         $fieldset = $form->addFieldset(
             'base_fieldset',
             [
                'legend' => __('Answers'),
                'class'  => 'fieldset-wide'
             ]
         );
    
        $answerBlock = $this->getLayout()->createBlock(QAnswer::class)
        ->setQuestionId($productquestion->getId())->setStoreId($productquestion->getStoreId());
        
        $fieldset->addField(
            'answer_value_container',
            'note',
            [
                    'text' => $answerBlock->toHtml(),
            ]
        );
        
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    /**
     * Prepare Sizeadviser for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Answers');
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
