<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Model\ResourceModel\PricePerCustomer;
 
/**
 * Collection class of Price Per Customer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            \Mageants\PricePerCustomer\Model\PricePerCustomer::class,
            \Mageants\PricePerCustomer\Model\ResourceModel\PricePerCustomer::class
        );
    }
}
