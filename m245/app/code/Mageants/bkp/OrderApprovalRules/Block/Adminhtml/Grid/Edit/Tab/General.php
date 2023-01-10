<?php
/**
 * @category Mageants OrderApprovalRules
 * @package Mageants_OrderApprovalRules
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants <support@mageants.com>
 */

namespace Mageants\OrderApprovalRules\Block\Adminhtml\Grid\Edit\Tab;

use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Cms\Ui\Component\Listing\Column\Cms\Options;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class General extends Generic implements TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var \Mageants\OrderApprovalRules\Helper\Data
     */
    protected $_helper;
   
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Mageants\OrderApprovalRules\Helper\Data $helper
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Mageants\OrderApprovalRules\Helper\Data $helper,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_fieldFactory = $fieldFactory;
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        /** @var \Magento\Framework\Data\Form $form */
        $model = $this->_coreRegistry->registry('mageants_orderapprovalrules');
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
            'enctype' => 'multipart/form-data']]
        );
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Order Approval Rule Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'rule_name',
            'text',
            ['name' => 'rule_name', 'label' => __('Rule Name'), 'title' => __('Rule Name'), 'required' => true]
        );

        $fieldset->addField(
            'orderstatus',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'orderstatus',
                'id' => 'orderstatus',
                'required' => false,
                'options' => ['0' => __('Enabled'), '1' => __('Disabled')],
            ]
        );

        $fieldset->addField(
            'apply_to',
            'select',
            [
                'label' => __('Apply'),
                'title' => __('Apply'),
                'name' => 'apply_to',
                'id' => 'apply_to',
                'required' => false,
                'options' => [
                                'whole_category' => __('To Whole Category'),
                                'specific_products' => __('To Specific Products')
                            ],
            ]
        );
        /*'specific_users_country' => __('To Specific User\'s Country')*/
        $category_ids=$this->_helper->getCategoryList();
        $fieldset->addField(
            'category_ids',
            'multiselect',
            [
                'name' => 'category_ids',
                'label' => __('Select Categories'),
                'title' => __('Categories'),
                'required' => true,
                'values' => $category_ids,
            ]
        );

        $country_ids=$this->_helper->getCountryList();
        $fieldset->addField(
            'country_ids',
            'multiselect',
            [
                'name' => 'country_ids',
                'label' => __('Select Countries'),
                'title' => __('Countries'),
                'required' => true,
                'values' => $country_ids,
            ]
        );

        if (!empty($model->getData())) {
            $data=$model->getData();
            $form->setValues($model->getData());
        }
        $form->setUseContainer(false);
        $this->setForm($form);
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class)->addFieldMap(
                "apply_to",
                'apply_to'
            )
            ->addFieldMap(
                "category_ids",
                'category_ids'
            )
            ->addFieldMap(
                "country_ids",
                'country_ids'
            )
            ->addFieldMap(
                "payment_methods",
                'payment_methods'
            )
            ->addFieldDependence(
                'category_ids',
                'apply_to',
                'whole_category'
            )
            ->addFieldDependence(
                'country_ids',
                'apply_to',
                'specific_users_country'
            )
            ->addFieldDependence(
                'payment_methods',
                'apply_to',
                'specific_payment_method'
            )
        );
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
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
        return __('General');
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
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

    /**
     * Prepare form html
     *
     * @return $string
     */
    public function getFormHtml()
    {
        $html=parent::getFormHtml();
        return $html;
    }
}
