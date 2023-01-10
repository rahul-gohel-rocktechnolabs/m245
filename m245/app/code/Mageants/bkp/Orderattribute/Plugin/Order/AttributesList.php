<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order;

class AttributesList
{
    /**
     * @var \Mageants\Orderattribute\Model\Validator
     */
    private $validator;

    /**
     * @param \Mageants\Orderattribute\Model\Validator $validator
     */
    public function __construct(
        \Mageants\Orderattribute\Model\Validator $validator
    ) {
        $this->validator = $validator;
    }

    /**
     * Before set custom attributes
     *
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param array $attributes
     * @return object
     */
    public function beforeSetCustomAttributes(
        \Magento\Quote\Model\Quote\Address $subject,
        array $attributes
    ) {
        $orderAttributes = $this->filterOrderAttributesFromCheckout($attributes);

        $subject->setData('order_attributes', $orderAttributes);
    }

    /**
     * Filter order attributes from checkout
     *
     * @param array $orderAttributes
     * @return array
     */
    protected function filterOrderAttributesFromCheckout($orderAttributes)
    {
        $orderAttributesList = [];
        $orderAttributesData = $this->prepareAttributeData($orderAttributes);

        $orderAttributesData = $this->validator->validateAttributeRelations($orderAttributesData);
        foreach ($orderAttributes as $attributeCode => $attributeValue) {
            if (strpos($attributeCode, 'mgorderattribute_') !== false) {
                $newCode = str_replace('mgorderattribute_', '', $attributeCode);
                if (isset($orderAttributesData[$newCode])) {
                    $orderAttributesList[$newCode] = $attributeValue;
                }
            }
        }
        return $orderAttributesList;
    }

    /**
     * Prepare attribute data
     *
     * @param array $orderAttributes
     * @return array
     */
    private function prepareAttributeData($orderAttributes)
    {
        $attributesData = [];
        foreach ($orderAttributes as $code => $data) {
            $attributesData[str_replace('mgorderattribute_', '', $code)] = $data->getValue();
        }
        return $attributesData;
    }
}
