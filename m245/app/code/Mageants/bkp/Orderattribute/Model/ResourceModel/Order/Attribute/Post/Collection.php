<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(
            \Mageants\Orderattribute\Model\Order\Attribute::class,
            \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute::class
        );
    }
}
