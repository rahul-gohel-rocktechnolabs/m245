<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Checkout;

class AttributeMerger extends \Magento\Checkout\Block\Checkout\AttributeMerger
{
    /**
     * Map form element
     *
     * @var array
     */
    protected $formElementMap = [
        'input'       => 'Mageants_Orderattribute/js/form/element/abstract',
        'radios'      => 'Mageants_Orderattribute/js/form/element/abstract',
        'checkbox'    => 'Mageants_Orderattribute/js/form/element/select',
        'select'      => 'Mageants_Orderattribute/js/form/element/select',
        'date'        => 'Mageants_Orderattribute/js/form/element/date',
        'datetime'    => 'Mageants_Orderattribute/js/form/element/date',
        'textarea'    => 'Mageants_Orderattribute/js/form/element/textarea',
        'checkboxes'  => 'Mageants_Orderattribute/js/form/element/checkboxes',
    ];

    /**
     * Merge additional address fields for given provider
     *
     * @param array $elements
     * @param string $providerName name of the storage container used by UI component
     * @param string $dataScopePrefix
     * @param array $fields
     * @return array
     */
    public function merge($elements, $providerName, $dataScopePrefix, array $fields = [])
    {
        foreach ($elements as $attributeCode => $attributeConfig) {
            $additionalConfig = isset($fields[$attributeCode]) ? $fields[$attributeCode] : [];
            if (!$this->isFieldVisible($attributeCode, $attributeConfig, $additionalConfig)) {
                continue;
            }
            $fields[$attributeCode] = $this->getFieldConfig(
                $attributeCode,
                $attributeConfig,
                $additionalConfig,
                $providerName,
                $dataScopePrefix
            );
            if ($attributeConfig['config']['relations']) {
                $fields[$attributeCode]['relations'] = $attributeConfig['config']['relations'];
            }
            $fields[$attributeCode]['shipping_methods'] = $additionalConfig['shipping_methods'];
        }
        return $fields;
    }
}
