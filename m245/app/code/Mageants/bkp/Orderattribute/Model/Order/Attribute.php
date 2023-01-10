<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\Order;

class Attribute extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Mageants\Orderattribute\Model\ResourceModel\Order\Attribute::class);
    }

    /**
     * Drop attribute field
     *
     * @param string $code
     */
    public function dropAttributeField($code)
    {
        $this->getResource()->dropAttributeField($code);
    }

    /**
     * Add attribute field
     *
     * @param string $code
     * @param string $type
     */
    public function addAttributeField($code, $type)
    {
        $this->getResource()->addAttributeField($code, $type);
    }
}
