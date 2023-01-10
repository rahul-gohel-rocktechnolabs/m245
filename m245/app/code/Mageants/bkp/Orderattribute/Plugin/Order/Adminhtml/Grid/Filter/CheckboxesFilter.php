<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order\Adminhtml\Grid\Filter;

class CheckboxesFilter
{
    /**
     * @var \Mageants\Orderattribute\Ui\Component\Filters\Type\CheckboxesFactory
     */
    protected $checkboxesFilterFactory;

    /**
     * @var \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute
     */
    protected $orderAttribute;

    /**
     * @var \Mageants\Orderattribute\Model\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @param \Mageants\Orderattribute\Ui\Component\Filters\Type\CheckboxesFactory $checkboxes
     * @param \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute $attribute
     * @param \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        \Mageants\Orderattribute\Ui\Component\Filters\Type\CheckboxesFactory $checkboxes,
        \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute $attribute,
        \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->checkboxesFilterFactory = $checkboxes;
        $this->orderAttribute = $attribute;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * Around prepare
     *
     * @param \Magento\Ui\Component\Filters\Type\Select $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundPrepare(
        \Magento\Ui\Component\Filters\Type\Select $subject,
        \Closure $proceed
    ) {
        return $proceed();
    }

    /**
     * Is allowed
     *
     * @param string $listingName
     * @return bool
     */
    protected function isAllowed($listingName)
    {
        $listings = [
            'sales_order_grid',
            'sales_order_invoice_grid',
            'sales_order_shipment_grid',
        ];

        return (in_array($listingName, $listings)
            && ($this->attributeMetadataDataProvider->countOrderAttributes() > 0));
    }
}
