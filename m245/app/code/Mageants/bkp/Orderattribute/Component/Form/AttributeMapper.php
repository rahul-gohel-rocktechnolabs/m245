<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Component\Form;

use Mageants\Orderattribute\Model\Relation\ParentAttributeProvider;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class AttributeMapper extends \Magento\Ui\Component\Form\AttributeMapper
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * @var \Mageants\Orderattribute\Model\ResourceModel\RelationDetails\CollectionFactory
     */
    private $relationCollectionFactory;

    /**
     * @var \Mageants\Orderattribute\Block\Data\Form\Element\BooleanFactory
     */
    private $booleanFactory;

    /**
     * @var \Mageants\Orderattribute\Helper\Config
     */
    private $config;

    /**
     * @param TimezoneInterface $localeData
     * @param \Mageants\Orderattribute\Block\Data\Form\Element\BooleanFactory $booleanFactory
     * @param \Mageants\Orderattribute\Model\ResourceModel\RelationDetails\CollectionFactory $relationCollectionFactory
     * @param \Mageants\Orderattribute\Helper\Config $config
     */
    public function __construct(
        TimezoneInterface $localeData,
        \Mageants\Orderattribute\Block\Data\Form\Element\BooleanFactory $booleanFactory,
        \Mageants\Orderattribute\Model\ResourceModel\RelationDetails\CollectionFactory $relationCollectionFactory,
        \Mageants\Orderattribute\Helper\Config $config
    ) {
        $this->localeDate = $localeData;
        $this->relationCollectionFactory = $relationCollectionFactory;
        $this->booleanFactory = $booleanFactory;
        $this->config = $config;
    }

    /**
     * @var $formElementMap
     */
    private $formElementMap = [
        'text' => 'input',
        'hidden' => 'input',
        'boolean' => 'select',
    ];

    /**
     * @var $metaPropertiesMap
     */
    private $metaPropertiesMap = [
        'dataType' => 'getFrontendInput',
        'visible' => 'getIsVisibleOnFront',
        'required' => 'getIsFrontRequired',
        'label' => 'getStoreLabel',
        'sortOrder' => 'getSortingOrder',
        'notice' => 'getNote',
        'default' => 'getDefaultOrLastValue',
        'frontend_class' => 'getFrontendClass',
        'size' => 'getMultilineCount',
        'validate_length_count' => 'getValidateLengthCount',

    ];

    /**
     * Get attributes meta
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function map($attribute)
    {
        $meta = [];
        foreach ($this->metaPropertiesMap as $metaName => $methodName) {
            $value = $attribute->$methodName();
            $meta[$metaName] = $value;
            if ('getFrontendInput' === $methodName) {
                $meta['formElement'] = isset($this->formElementMap[$value])
                ? $this->formElementMap[$value]
                : $value;
            } elseif ('getStoreLabel' == $methodName) {
                $meta[$metaName] = __($meta[$metaName]);
            }
        }
        if ($attribute->usesSource()) {
            $displayEmptyOption = $this->displayEmptyOption($attribute);
            $allOptions = $attribute->getSource()->getAllOptions(
                $displayEmptyOption
            );
            foreach ($allOptions as $key => $option) {
                if ($option['label'] == " ") {
                    $allOptions[$key]['label'] = "";
                }
                break;
            }
            $meta['options'] = $allOptions;
        }

        $rules = [];
        if (isset($meta['required']) && $meta['required'] == 1) {
            $rules['required-entry'] = true;
        }
        if (isset($meta['frontend_class'])) {
            if ($meta['frontend_class'] == 'validate-length') {
                $maxLength = (array_key_exists('validate_length_count', $meta)) ? $meta['validate_length_count'] : 25;
                $rules[$meta['frontend_class']] = 'maximum-length-' . $maxLength;
                $rules['max_text_length'] = $maxLength;
            } else {
                $rules[$meta['frontend_class']] = true;
            }
        }

        $meta['validation'] = $rules;
        if ($elementTmpl = $this->getElementTmpl($attribute->getFrontendInput())) {
            $meta['config']['elementTmpl'] = $elementTmpl;
        }
        if ($attribute->getFrontendInput() == 'datetime') {
            $meta['options'] = [
                'showsTime' => true,
                'timeFormat' => $this->localeDate->getTimeFormat(),
            ];
        }
        if ($attribute->getFrontendInput() == 'date') {
            $meta['options'] = [
                'dateFormat' => $this->config->getCheckoutDateFormat(),
            ];
        }
        if ($attribute->getFrontendInput() == 'boolean') {
            $meta['options'] = $this->booleanFactory->create()->getValues();
        }

        $meta['shipping_methods'] = $attribute->getShippingMethods()
        ? explode(',', $attribute->getShippingMethods())
        : [];

        $meta['config']['relations'] = $this->getElementRelations($attribute);

        return $meta;
    }

    /**
     * Display empty option
     *
     * @param \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute $attribute
     * @return boolean
     */
    protected function displayEmptyOption($attribute)
    {
        switch ($attribute->getFrontendInput()) {
            case 'radios':
            case 'checkboxes':
                $displayEmptyOption = false;
                break;
            default:
                $displayEmptyOption = true;
                break;
        }

        return $displayEmptyOption;
    }

    /**
     * Get element tmpl
     *
     * @param string $attributeFrontendInput
     * @return string
     */
    protected function getElementTmpl($attributeFrontendInput)
    {
        switch ($attributeFrontendInput) {
            case 'radios':
                $elementTmpl = 'Mageants_Orderattribute/form/element/radios';
                break;
            case 'checkboxes':
                $elementTmpl = 'Mageants_Orderattribute/form/element/checkboxes';
                break;
            case 'datetime':
                $elementTmpl = 'Mageants_Orderattribute/form/element/datetime';
                break;
            default:
                $elementTmpl = '';
                break;
        }

        return $elementTmpl;
    }

    /**
     * Get element relations
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @return array|false
     */
    protected function getElementRelations($attribute)
    {
        if (in_array($attribute->getFrontendInput(), ParentAttributeProvider::ATTRIBUTE_FRONTEND_INPUT)) {
            return $this->relationCollectionFactory->create()
                ->getAttributeRelations($attribute->getAttributeId());
        }

        return false;
    }
}
