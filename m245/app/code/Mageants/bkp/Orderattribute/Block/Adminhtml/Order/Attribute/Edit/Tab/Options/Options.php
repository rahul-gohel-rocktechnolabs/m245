<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Adminhtml\Order\Attribute\Edit\Tab\Options;

class Options extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Mageants_Orderattribute::attribute/options.phtml');
    }

    /**
     * @var $inputWithOptions
     */
    protected $inputWithOptions = [
        'select',
        'multiselect',
        'radios',
        'checkboxes',
    ];

    /**
     * @var $defaultValueType
     */
    protected $defaultValueType = [
        'select' => 'radio',
        'multiselect' => 'checkbox',
        'radios' => 'radio',
        'checkboxes' => 'checkbox',
    ];

    /**
     * Prepare option values
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @param array|\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection
     * @return array
     */
    protected function _prepareOptionValues(
        \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute,
        $optionCollection
    ) {
        $type = $attribute->getFrontendInput();
        if (in_array($type, $this->inputWithOptions)) {
            if ($attribute->getDefaultValue()) {
                $defaultValues = explode(',', $attribute->getDefaultValue());
            } else {
                $defaultValues = [];
            }
            $inputType = $this->defaultValueType[$type];
        } else {
            $defaultValues = [];
            $inputType = '';
        }

        $values = [];
        $isSystemAttribute = is_array($optionCollection);
        foreach ($optionCollection as $option) {
            $bunch = $isSystemAttribute ? $this->_prepareSystemAttributeOptionValues(
                $option,
                $inputType,
                $defaultValues
            ) : $this->_prepareUserDefinedAttributeOptionValues(
                $option,
                $inputType,
                $defaultValues
            );
            foreach ($bunch as $value) {
                $values[] = new \Magento\Framework\DataObject($value);
            }
        }
        return $values;
    }
}
