<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Adminhtml\Order\Attribute\Edit\Tab;

use Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain;

class Main extends AbstractMain
{
    /**
     * @var \Mageants\Orderattribute\Helper\Eav\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var $fieldToRemoveFromFieldset
     */
    protected $fieldToRemoveFromFieldset = [
        'is_unique',
    ];

    /**
     * @var \Mageants\Orderattribute\Model\Config\SourceFactory
     */
    protected $sourceFactory;

    /**
     * @var \Mageants\Orderattribute\Helper\Config
     */
    protected $configHelper;

    /**
     * @var $dependencies
     */
    protected $dependencies = null;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Eav\Helper\Data $eavData
     * @param \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory
     * @param \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker
     * @param \Mageants\Orderattribute\Helper\Eav\Config $helperConfig
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Mageants\Orderattribute\Model\Config\SourceFactory $sourceFactory
     * @param \Mageants\Orderattribute\Helper\Config $configHelper
     * @param \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $dependencyFieldFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Eav\Helper\Data $eavData,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory,
        \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker,
        \Mageants\Orderattribute\Helper\Eav\Config $helperConfig,
        \Magento\Store\Model\System\Store $systemStore,
        \Mageants\Orderattribute\Model\Config\SourceFactory $sourceFactory,
        \Mageants\Orderattribute\Helper\Config $configHelper,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $dependencyFieldFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_localeDate = $context->getLocaleDate();
        $this->eavConfig = $helperConfig;
        $this->sourceFactory = $sourceFactory;
        $this->configHelper = $configHelper;
        $this->dependencyFieldFactory = $dependencyFieldFactory;
        $this->resource = $resource;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $eavData,
            $yesnoFactory,
            $inputTypeFactory,
            $propertyLocker,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeObject */
        $attributeObject = $this->getAttributeObject();
        $yesno = $this->_yesnoFactory->create()->toOptionArray();

        if (!$this->_storeManager->isSingleStoreMode()) {
            if (!$attributeObject->getData('store_ids')) {
                $storecollection = $this->_systemStore->getStoreCollection();
                $stores = [];
                foreach ($storecollection as $store) {
                    $stores[] = $store->getId();
                }
                $attributeObject->setData(
                    'stores',
                    $stores
                );
            } else {
                $attributeObject->setData(
                    'stores',
                    explode(',', $attributeObject->getData('store_ids'))
                );
            }

        }

        /* @var $form \Magento\Framework\Data\Form */
        $form = $this->getForm();
        /* @var $fieldset \Magento\Framework\Data\Form\Element\Fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $this->removeFieldsFromAbstract($fieldset);

        if (!$this->_storeManager->isSingleStoreMode()) {
            $storeValues = $this->_systemStore->getStoreValuesForForm();
            $fieldset->addField(
                'stores',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $storeValues,
                ],
                'attribute_code'
            );
        } else {
            $fieldset->addField(
                'stores',
                'hidden',
                [
                    'name' => 'stores[]',
                    'value' => $this->_storeManager->getStore()->getId(),
                ],
                'attribute_code'
            );
            $attributeObject->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $groupValues = $this->sourceFactory->getCustomerGroupSource()->toOptionArray();
        $preselectedGroupValues = array_column($groupValues, 'value');
        $groups = $fieldset->addField('customer_groups', 'multiselect', [
            'name' => 'customer_groups[]',
            'label' => ('Customer Groups'),
            'title' => ('Customer Groups'),
            'values' => $groupValues,
        ], 'stores');

        $frontendInputElm = $form->getElement('frontend_input');
        $inputTypes = $this->eavConfig->getAttributeTypes();
        $frontendInputElm->setValues($inputTypes);

        $fieldset->addField('is_visible_on_front', 'select', [
            'name' => 'is_visible_on_front',
            'label' => __('Visible on Front-end'),
            'title' => __('Visible on Front-end'),
            'values' => $yesno,
        ], 'default_value_textarea');

        $fieldset->addField('is_visible_on_back', 'select', [
            'name' => 'is_visible_on_back',
            'label' => __('Visible on Back-end'),
            'title' => __('Visible on Back-end'),
            'values' => $yesno,
        ], 'is_visible_on_front');

        $requiredElm = $form->getElement('is_required');
        $requiredValues = array_merge($requiredElm->getValues(), [[
            'value' => $this->configHelper->getRequiredOnFrontOnlyId(),
            'label' => __('On the Frontend Only'),
        ]]);
        $requiredElm->setValues($requiredValues);

        $validationElm = $form->getElement('frontend_class');
        $validationRules = array_merge($validationElm->getValues(), [[
            'value' => 'validate-length',
            'label' => __('Length less than or equal to'),
        ]]);
        $validationElm->setHtmlId('am_frontend_class');
        $validationElm->setValues($validationRules);

        $validateLength = $fieldset->addField('validate_length_count', 'text', [
            'name' => 'validate_length_count',
            'label' => __('Validate Length'),
            'title' => __('Validate Length'),
        ], 'am_frontend_class');

        $fields1 = $this->dependencyFieldFactory->create(
            [
                'fieldData' =>
                [
                    'separator' => ',',
                    'value' => 'validate-length',
                ],
            ]
        );
        $this->makeDependence($validationElm, $validateLength, $fields1);

        $fields = $this->dependencyFieldFactory->create(
            [
                'fieldData' =>
                [
                    'separator' => ',',
                    'value' => 'text,textarea',
                ],
            ]
        );
        $this->makeDependence($frontendInputElm, $validationElm, $fields);

        $fieldset = $form->addFieldset(
            'front_fieldset',
            ['legend' => __('Attribute Configuration')]
        );

        $fieldset->addField('checkout_step', 'select', [
            'name' => 'checkout_step',
            'label' => __('Position at Checkout Step'),
            'title' => __('Position at Checkout Step'),
            'values' => $this->sourceFactory->getCheckoutStepSource()->toOptionArray(),
        ]);

        $fieldset->addField('sorting_order', 'text', [
            'name' => 'sorting_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'note' => __('Numeric, used in front-end to sort attributes'),
        ]);
        $fieldset->addField('save_selected', 'select', [
            'name' => 'save_selected',
            'label' => __('Save Entered Value For Future Checkout'),
            'title' => __('Save Entered Value For Future Checkout'),
            'note' => __('If set to "Yes", previously entered value will be used during checkout. Works for registered customers only.'),
            'values' => $yesno,
        ]);

        $fieldset->addField('is_used_in_grid', 'select', [
            'name' => 'is_used_in_grid',
            'label' => __('Show on Admin Grids'),
            'title' => __('Show on Admin Grids'),
            'values' => $yesno,
        ]);

        $fieldset->addField('include_html_print_order', 'select', [
            'name' => 'include_html_print_order',
            'label' => __('Include Into HTML Print-out'),
            'title' => __('Include Into HTML Print-out'),
            'note' => __('Order confirmation HTML print-out.'),
            'values' => $yesno,
        ]);

        $fieldset->addField('include_pdf', 'select', [
            'name' => 'include_pdf',
            'label' => __('Include Into PDF Documents'),
            'title' => __('Include Into PDF Documents'),
            'values' => $yesno,
        ]);

        $fieldset->addField('include_api', 'hidden', [
            'name' => 'include_api',
            'label' => __('Include Into API'),
            'title' => __('Include Into API'),
            'values' => '1',
        ]);
        $fieldset->addField('apply_default', 'select', [
            'name' => 'apply_default',
            'label' => __('Automatically Apply Default Value'),
            'title' => __('Automatically Apply Default Value'),
            'note' => __('If set to `Yes`, the default value will be automatically applied for each order if attribute value is not entered or not visible at the frontend.'),
            'values' => $yesno,
        ]);
        $data = $attributeObject->getData();
        if (!array_key_exists('customer_groups', $data)) {
            $data['customer_groups'] = $preselectedGroupValues;
        }
        if (!array_key_exists('is_visible_on_front', $data)) {
            $data['is_visible_on_front'] = 1;
        }
        if (!array_key_exists('is_visible_on_back', $data)) {
            $data['is_visible_on_back'] = 1;
        }
        if (array_key_exists('required_on_front_only', $data) && $data['required_on_front_only'] == 1) {
            $data['is_required'] = '2';
        }
        if (isset($data['attribute_id'])) {
            if ($data['attribute_id']) {
                $tablename = $this->resource->getTableName('mageants_orderattribute_order_eav_attribute');
                $connection = $this->resource->getConnection();
                $runSql =  $connection->select()->from($tablename)->where('attribute_id=?', $data['attribute_id']);

                $result = $connection->fetchRow($runSql);
                if (isset($result['checkout_step'])) {
                    $data['checkout_step'] = $result['checkout_step'];
                }
            }
        }
        $attributeObject->setData($data);
        $this->setAttributeObject($attributeObject);

        $this->setChild('form_after', $this->dependencies);
        $this->setForm($form);

        return $this;
    }

    /**
     * Make dependence
     *
     * @param object $mainElement
     * @param object $dependentElement
     * @param int $values
     */
    protected function makeDependence($mainElement, $dependentElement, $values = '1')
    {
        if (!$this->dependencies) {
            $this->dependencies = $this->getLayout()
                ->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class);
        }

        $this->dependencies->addFieldMap($mainElement->getHtmlId(), $mainElement->getName())
            ->addFieldMap($dependentElement->getHtmlId(), $dependentElement->getName())
            ->addFieldDependence($dependentElement->getName(), $mainElement->getName(), $values);
    }

    /**
     * Remove fields from abstract
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     */
    protected function removeFieldsFromAbstract($fieldset)
    {
        foreach ($this->fieldToRemoveFromFieldset as $fieldCode) {
            $fieldset->removeField($fieldCode);
        }
    }
}
