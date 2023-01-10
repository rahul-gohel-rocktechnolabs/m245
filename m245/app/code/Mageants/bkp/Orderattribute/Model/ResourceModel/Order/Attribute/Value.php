<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel\Order\Attribute;

class Value extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init('mageants_orderattribute_order_attribute_value', 'id');
    }

    /**
     * Update Attributes
     *
     * @param array $attributes
     * @param int $ids
     */
    public function updateAttributes($attributes, $ids)
    {
        $connection = $this->getConnection();

        $output = [];
        foreach ($attributes as $key => $value) {
            $output[$key . '_output'] = $value;
        }
        $attributes = array_merge($attributes, $output);

        $connection->update(
            $this->getMainTable(),
            $attributes,
            ['order_entity_id IN (?)' => $ids]
        );
    }
}
