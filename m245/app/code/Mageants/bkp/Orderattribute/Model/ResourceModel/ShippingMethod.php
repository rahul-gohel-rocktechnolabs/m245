<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel;

class ShippingMethod extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageants_orderattribute_shipping_methods', 'id');
    }

    /**
     * Save shipping methods
     *
     * @param int $attributeId
     * @param string $shippingMethods
     * @return void
     */
    public function saveShippingMethods($attributeId, $shippingMethods)
    {
        $this->deleteShippingMethodsByAttributeId($attributeId);

        if (is_array($shippingMethods)) {
            $insertData = [];
            foreach ($shippingMethods as $shippingMethod) {
                $shippingMethodsData = [];
                $shippingMethodsData['attribute_id'] = $attributeId;
                $shippingMethodsData['shipping_method'] = $shippingMethod;
                $insertData[] = $shippingMethodsData;
            }
        } else {
            $insertData[] = [
                'attribute_id' => $attributeId,
                'shipping_method' => $shippingMethods,
            ];
        }
        if (!empty($insertData)) {
            $this->getConnection()->insertMultiple(
                $this->getMainTable(),
                $insertData
            );
        }
    }

    /**
     * Delete shipping methods by attribute id
     *
     * @param int $attributeId
     */
    public function deleteShippingMethodsByAttributeId($attributeId)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            sprintf('attribute_id = %d', $attributeId)
        );
    }
}
