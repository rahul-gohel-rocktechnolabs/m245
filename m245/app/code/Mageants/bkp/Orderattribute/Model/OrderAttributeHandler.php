<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model;

class OrderAttributeHandler extends \Magento\ConfigurableProduct\Model\ConfigurableAttributeHandler
{
    /**
     * @var \Mageants\Orderattribute\Model\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @param \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * Retrieve list of attributes applicable for configurable product
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    public function getApplicableAttributes()
    {
        return $this->attributeMetadataDataProvider->loadAttributesCollection();
    }

    /**
     * Is attribute applicable
     *
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @return bool
     */
    public function isAttributeApplicable($attribute)
    {
        $types = [
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
        ];
        return !$attribute->getApplyTo() || count(array_diff($types, $attribute->getApplyTo())) === 0;
    }
}
