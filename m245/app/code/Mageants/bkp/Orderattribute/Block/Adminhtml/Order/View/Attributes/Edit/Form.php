<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Adminhtml\Order\View\Attributes\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Exception\LocalizedException;

class Form extends Generic
{
    /**
     * @var \Mageants\Orderattribute\Model\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory
     */
    protected $orderAttributesValuesFactory;

    /**
     * @var $formData
     */
    protected $formData;
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory $valueFactory
     * @param \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory $valueFactory,
        \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        array $data = []
    ) {
        $this->orderAttributesValuesFactory = $valueFactory;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->_sessionQuote = $sessionQuote;
        $this->backendSession = $context->getBackendSession();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'class' => 'admin__scope-old',
                'method' => 'post',
            ],
        ]);

        $form->setUseContainer(true);
        $orderAttributeValues = $this->getOrderAttributeValues();

        $fieldset = $this->createAttributesFieldSet($form);
        $orderEavAttributes = $this->getOrderEavAttributes();

        $this->formData = $orderAttributeValues->getData();

        $this->_setFieldset($orderEavAttributes, $fieldset);
        foreach ($orderEavAttributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if (!array_key_exists($code, $this->formData)) {
                $this->formData[$code] = $attribute->getDefaultValue();
            }
        }

        if ($this->getFieldNameSuffix()) {
            $form->addFieldNameSuffix($this->getFieldNameSuffix());
        }

        $form->setValues($this->formData);
        $this->setForm($form);
        parent::_prepareForm();
        return $this;
    }

    /**
     * Create attributes field set
     *
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function createAttributesFieldSet($form)
    {
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Attributes\' Values'),
                'collapsable' => true,
            ]
        );
        return $fieldset;
    }

    /**
     * Apply type specific config
     *
     * @param string $inputType
     * @param string $element
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return void
     */
    protected function _applyTypeSpecificConfig(
        $inputType,
        $element,
        \Magento\Eav\Model\Entity\Attribute $attribute
    ) {
        parent::_applyTypeSpecificConfig($inputType, $element, $attribute);

        switch ($inputType) {
            case 'select':
                $values = $attribute->getSource()->getAllOptions(false, true);
                array_unshift($values, ['label' => ' ', 'value' => '']);
                $element->setValues($values);
                break;
            case 'date':
                $element->addClass('date-calendar');
                break;
            case 'datetime':
                $element->addClass('date-calendar');
                $element->setDateFormat($this->_localeDate->getDateFormatWithLongYear());
                $element->setTimeFormat($this->_localeDate->getTimeFormat());
                break;
            case 'checkboxes':
                $attributeCode = $attribute->getAttributeCode();
                if (array_key_exists($attributeCode, $this->formData)) {
                    $this->formData[$attributeCode] = explode(
                        ',',
                        $this->formData[$attributeCode]
                    );
                }
                $element->setValues($attribute->getSource()->getAllOptions(false, true));
                $element->setName($attributeCode . '[]');

                break;
            case 'radios':
                $element->setValues($attribute->getSource()->getAllOptions(false, true));
                break;
            default:
                break;
        }
    }

    /**
     * Get order eav attributes
     *
     * @return \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\Collection
     */
    protected function getOrderEavAttributes()
    {
        return $this->attributeMetadataDataProvider
            ->loadAttributesForEditFormByStoreId($this->getStoreId());
    }

    /**
     * Count order eav attributes
     *
     * @return int
     */
    protected function countOrderEavAttributes()
    {
        $collection = $this->getOrderEavAttributes();
        return $collection->getSize();
    }

    /**
     * Get order attribute values
     *
     * @return \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    protected function getOrderAttributeValues()
    {
        /**
         * @var \Mageants\Orderattribute\Model\Order\Attribute\Value $orderAttributes
         */
        $orderAttributes = $this->orderAttributesValuesFactory->create();

        if ($this->getOrderId()) {
            $orderAttributes->load($this->getOrderId(), 'order_entity_id');
        }

        return $orderAttributes;
    }

    /**
     * Get store id
     *
     * @return string
     */
    protected function getStoreId()
    {
        $registryModel = $this->getRegistryModel();
        return $registryModel->getStoreId();
    }

    /**
     * Get order id
     *
     * @return bool
     */
    protected function getOrderId()
    {
        $registryModel = $this->getRegistryModel();
        if ($registryModel) {
            return $registryModel->getId();
        }

        return false;
    }

    /**
     * Get registry model
     *
     * @return \Magento\Sales\Model\Order
     * @throws LocalizedException
     */
    protected function getRegistryModel()
    {
        if ($this->_coreRegistry->registry('current_order')) {
            return $this->_coreRegistry->registry('current_order');
        }

        if ($this->_getSession()->getOrder()) {
            return $this->_getSession()->getOrder();
        }
    }

    /**
     * Retrieve quote session object
     *
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function _getSession()
    {
        return $this->_sessionQuote;
    }

    /**
     * Get Additional Element Types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return [
            'boolean' => \Mageants\Orderattribute\Block\Data\Form\Element\Boolean::class,
            'datetime' => \Mageants\Orderattribute\Block\Data\Form\Element\Datetime::class,
            'checkboxes' => \Mageants\Orderattribute\Block\Data\Form\Element\Checkboxes::class,
            'radios' => \Mageants\Orderattribute\Block\Data\Form\Element\Radios::class,
        ];
    }

    /**
     * To html
     *
     * @return string
     */
    public function toHtml()
    {

        if ($this->countOrderEavAttributes() > 0) {
            return parent::toHtml();
        } else {
            return '';
        }
    }

    /**
     * Check whether attribute is visible
     *
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return bool
     */
    protected function _isAttributeVisible(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        return !(!$attribute || !$attribute->getData('is_visible_on_back'));
    }
}
