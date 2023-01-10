<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model;

class ShippingMethod extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Mageants\Orderattribute\Model\ResourceModel\ShippingMethod::class);
    }

    /**
     * Save shipping methods
     *
     * @param int $attributeId
     * @param string $shippingMethods
     */
    public function saveShippingMethods($attributeId, $shippingMethods)
    {
        $this->getResource()->saveShippingMethods($attributeId, $shippingMethods);
    }
}
