<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\Value;

class Collection extends \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(
            \Mageants\Orderattribute\Model\Order\Attribute\Value::class,
            \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\Value::class
        );
    }
}
