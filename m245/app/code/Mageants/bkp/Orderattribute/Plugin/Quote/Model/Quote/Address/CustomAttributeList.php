<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Quote\Model\Quote\Address;

class CustomAttributeList
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    private $attribute;

    /**
     * CustomAttributeList constructor.
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     */
    public function __construct(
        \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute $attribute
    ) {
        $this->attribute = $attribute;
    }

    /**
     * After Get Attributes
     *
     * @param \Magento\Quote\Model\Quote\Address\CustomAttributeList $object
     * @param array $result
     * @return array
     */
    public function afterGetAttributes($object, $result)
    {
        $attributes = $this->attribute->getOrderAttributesCodes();
        return array_merge($result, $attributes);
    }
}
