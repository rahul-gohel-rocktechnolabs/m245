<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * PricePerCustomer Model class
 */
class PricePerCustomer extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(\Mageants\PricePerCustomer\Model\ResourceModel\PricePerCustomer::class);
    }
}
