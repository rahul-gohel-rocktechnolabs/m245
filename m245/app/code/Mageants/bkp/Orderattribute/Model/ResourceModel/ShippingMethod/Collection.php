<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel\ShippingMethod;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Mageants\Orderattribute\Model\ShippingMethod::class,
            \Mageants\Orderattribute\Model\ResourceModel\ShippingMethod::class
        );
    }

    /**
     * Get shipping methods by attribute id
     *
     * @param int $attributeId
     * @return void
     */
    public function getShippingMethodsByAttributeId($attributeId)
    {
        $this->addFilter('attribute_id', $attributeId);
        $this->load();
        return $this->getItems();
    }
}
