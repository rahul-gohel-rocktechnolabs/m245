<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Checkout;

use Magento\Framework\View\Element\Template;

class Attributes extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageants\Orderattribute\Model\AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * Attributes constructor.
     * @param Template\Context $context
     * @param \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        array $data = []
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        parent::__construct($context, $data);
    }

    /**
     * Get attribute codes
     *
     * @return string
     */
    public function getAttributeCodes()
    {
        $collection = $this->attributeMetadataDataProvider->loadAttributesCollection();
        $codesArray = \Zend_Json::encode($collection->getColumnValues('attribute_code'));
        return $codesArray;
    }
}
