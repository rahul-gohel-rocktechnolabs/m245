<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Block\Adminhtml\ExtraFee\Edit;

use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;
use \Magento\Store\Model\System\Store;
use \Mageants\ExtraFee\Helper\Data;

/** * To Create Form */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Constructor for form data
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_systemStore = $systemStore;
        $this->_helper = $helper;
    }
    /**
     * To Set Id And Title for ExtraFee Form
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('extrafee_form');
        $this->setTitle(__('Extra Fee Information'));
    }
    
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $model = $this->_coreRegistry->registry('mageants_extrafee');
        $form = $this->_formFactory->create(
            ['data' => [
                        'id' => 'edit_form',
                        'action' => $this->getData('action'),
                        'method' => 'post',
                        'enctype' => 'multipart/form-data'
                    ]
                ]
        );
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Extra Fee Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'feesname',
            'text',
            ['name' => 'feesname', 'label' => __('Fees Name'), 'title' => __('Fees Name'), 'required' => true]
        );

        $fieldset->addField(
            'type',
            'select',
            [
                'label' => __('Type'),
                'title' => __('Type'),
                'name' => 'type',
                'id' => 'type',
                'required' => false,
                'options' => ['Fixed' => __('Fixed'), 'Percentage' => __('Percentage')],
            ]
        );

        $fieldset->addField(
            'amount',
            'text',
            [
                'name' => 'amount',
                'label' => __('Amount'),
                'title' => __('Amount'),
                'required' => true,
                'class'=>'validate-not-negative-number'
            ]
        );

        $fieldset->addField(
            'apply_to',
            'select',
            [
                'label' => __('Apply To'),
                'title' => __('Apply To'),
                'name' => 'apply_to',
                'id' => 'apply_to',
                'required' => false,
                'options' => [
                    'Product' => __('Product'),
                    'Category' => __('Category'),
                    'Shipping' => __('Shipping'),
                    'Order' => __('Order')
                ],
            ]
        );

        $category_ids=$this->_helper->getCategoryList();
        $fieldset->addField(
            'category_ids',
            'multiselect',
            [
                    'name' => 'category_ids',
                    'label' => __('Categories'),
                    'title' => __('Categories'),
                    'required' => false,
                    'values' => $category_ids,
            ]
        );
        $fieldset->addField(
            'is_mandatory',
            'select',
            [
                'label' => __('Is Mandatory'),
                'title' => __('Is Mandatory'),
                'name' => 'is_mandatory',
                'id' => 'is_mandatory',
                'required' => false,
                'options' => ['Yes' => __('Yes'), 'No' => __('No')],
            ]
        );
        
        $fieldset->addField(
            'estatus',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'estatus',
                'id' => 'estatus',
                'required' => false,
                'options' => ['Enabled' => __('Enabled'), 'Disabled' => __('Disabled')],
            ]
        );

        $store_id=$this->_helper->getStoreList();
        $fieldset->addField(
            'store_id',
            'multiselect',
            [
             'name'     => 'store_id',
             'label'    => __('Store Views'),
             'title'    => __('Store Views'),
             'required' => true,
             'values'   => $store_id,
            ]
        );
        if (!empty($model->getData())) {
            $data=$model->getData();
            $form->setValues($model->getData());
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Form\Element\Dependence::class
            )
            ->addFieldMap(
                "apply_to",
                'apply_to'
            )
            ->addFieldMap(
                "category_ids",
                'category_ids'
            )
            ->addFieldMap(
                "is_mandatory",
                'is_mandatory'
            )
            ->addFieldDependence(
                'category_ids',
                'apply_to',
                'Category'
            )
        );
        return parent::_prepareForm();
    }
}
