<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Helper\Eav;

use Magento\Eav\Helper\Data;

class Config
{
    /**
     * @var \Magento\Eav\Helper\Data $eavData
     */
    protected $eavData;

    /**
     * @param \Magento\Eav\Helper\Data $eavData
     */
    public function __construct(Data $eavData)
    {
        $this->eavData = $eavData;
    }

    /**
     * Get attribute types
     *
     * @param bool $asHash
     * @return array
     */
    public function getAttributeTypes($asHash = false)
    {
        $attributesHash = $this->getAttributeHash();
        if ($asHash) {
            return $attributesHash;
        }

        $attributesOptionArray = [];
        foreach ($attributesHash as $key => $value) {
            $optionItem = ['value' => $key, 'label' => $value];
            $attributesOptionArray[] = $optionItem;
        }
        return $attributesOptionArray;
    }

    /**
     * Get attribute hash
     *
     * @param bool $asHash
     * @return array
     */
    protected function getAttributeHash()
    {
        return [
            'text' => __('Text Field'),
            'textarea' => __('Text Area'),
            'date' => __('Date'),
            'datetime' => __('Date With Time'),
            'boolean' => __('Yes/No'),
            'select' => __('Dropdown'),
            'checkboxes' => __('Checkbox Group'),
            'radios' => __('Radio Buttons'),
        ];
    }

    /**
     * Get validation rules
     *
     * @param bool $asHash
     * @return array
     */
    public function getValidationRules()
    {
        $result = $this->eavData->getFrontendClasses(null);
        return $result;
    }
}
