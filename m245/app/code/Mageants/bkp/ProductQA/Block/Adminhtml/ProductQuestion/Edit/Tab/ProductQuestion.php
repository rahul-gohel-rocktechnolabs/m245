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
use \Mageants\ProductQA\Model\Source\Status;
use \Mageants\ProductQA\Model\Source\Orientation;
use \Magento\Cms\Ui\Component\Listing\Column\Cms\Options;

class ProductQuestion extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    public const FORM_NAME = 'mageants_productqa_question_form';
    /**
     * Enable / Disable options
     *
     * @var \Mageants\ProductQA\Model\Source\Status
     *
     */
    protected $_status;
    /**
     * Horizintal / Virticle options
     *
     * @var \Mageants\ProductQA\Model\Source\Orientation
     *
     */
    protected $_orientation;
     /**
      * Store View options
      *
      * @var \Magento\Cms\Ui\Component\Listing\Column\Cms\Options
      *
      */
    protected $_cmsOpt;
   /**
    * Undocumented function
    *
    * @param Context $context
    * @param Registry $registry
    * @param FormFactory $formFactory
    * @param Status $status
    * @param Options $cmsOpt
    * @param array $data
    */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Status $status,
        Options $cmsOpt,
        array $data = []
    ) {
        $this->_cmsOpt= $cmsOpt;
        
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
        $productquestion = $this->_coreRegistry->registry('mageants_productquestion');
        
        $form = $this->_formFactory->create();
        
        $form->setHtmlIdPrefix('productquestion_');
        $form->setFieldNameSuffix('productquestion');
        
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
        
        $fieldset->addField(
            'question',
            'textarea',
            [
                'name'  => 'question',
                'label' => __('Question'),
                'title' => __('Question'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'store_id',
            'select',
            [
                'name'  => 'store_id',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_cmsOpt->toOptionArray()
            ]
        );
        
        $productquestionData = $this->_session->getData('mageants_pdfinvoice_pdftemplate_data', true);
       
        if ($productquestionData) {
            $productquestion->addData($productquestionData);
        } else {
            if (!$productquestion->getId()) {
                $productquestion->addData($productquestion->getDefaultValues());
            }
        }
    
        $form->addValues($productquestion->getData());
        
        $form = $this->addProductFieldset($form, $productquestion);
        
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    /**
     * Add ProductFieldset
     *
     * @param int|string $form
     * @param int|string $question
     */
    private function addProductFieldset($form, $question)
    {
        $productBlock = $this->getLayout()->createBlock(
            ProductGrid::class,
            null,
            ['data' => ['product_ids' => explode(',', $question->getProductId()), "question_id"=>$form->getId()]]
        );

        $productFieldset = $form->addFieldset('product_fieldset', []);
         
        $productFieldset->addField(
            'product_grid_container',
            'note',
            [
                'label' => __('Product'),
                'title' => __('Product'),
                'text' => $productBlock->toHtml()
            ]
        );
      
        $productFieldset->addField(
            'product_ids',
            'hidden',
            [
                    'name' => 'product_ids',
                    'data-form-part' => self::FORM_NAME,
                    'after_element_js' => $this->getProductIdsJs($question->getProductId()),
                ]
        );
            
        return $form;
    }
    /**
     * Get Product Ids Js
     *
     * @param  int $prod_ids
     */
    private function getProductIdsJs($prod_ids)
    {
        return <<<HTML
    <script type="text/javascript">      
          require([
                'mage/adminhtml/grid'
            ], function(){
                new serializerController('productquestion_product_ids', [$prod_ids], [], 
                    productsGridJsObject, 'productquestion_product_ids');
            });                
    </script>
HTML;
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
