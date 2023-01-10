<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\Relation;

class ParentAttributeProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * attribute frontend input which have options and can have relation as a parent
     *
     * @var array
     */
    public const ATTRIBUTE_FRONTEND_INPUT = ['multiselect', 'select', 'checkboxes', 'radios'];

    /**
     * @var null|array
     */
    protected $options = null;

    /**
     * @var \Mageants\Orderattribute\Model\AttributeMetadataDataProvider
     */
    private $attributeMetadataProvider;

    /**
     * ParentAttributeProvider constructor.
     *
     * @param \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataProvider
     */
    public function __construct(
        \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataProvider
    ) {
        $this->attributeMetadataProvider = $attributeMetadataProvider;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];

            /* attributes only with options */
            $collection = $this->attributeMetadataProvider
                ->loadAttributesCollection()
                ->addFieldToFilter('frontend_input', ['in' => self::ATTRIBUTE_FRONTEND_INPUT]);

            foreach ($collection as $attribute) {
                $label = $attribute->getFrontendLabel();
                if (!$attribute->getIsVisibleOnFront()) {
                    $label .= ' - ' . __('Not Visible');
                }
                $this->options[] = [
                    'value' => $attribute->getAttributeId(),
                    'label' => $label,
                ];
            }
        }

        return $this->options;
    }

    /**
     * Get default selected
     *
     * Get selected Attribute ID for default
     * used when no Attribute ID in data for load Attribute options
     *
     * @return array|false
     */
    public function getDefaultSelected()
    {
        if (count($this->toOptionArray())) {
            return current($this->toOptionArray());
        }
        return false;
    }
}
