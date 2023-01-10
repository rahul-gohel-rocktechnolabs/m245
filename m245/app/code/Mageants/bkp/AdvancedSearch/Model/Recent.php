<?php
/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace  Mageants\AdvancedSearch\Model;

use Magento\Framework\Model\AbstractModel;
use Mageants\AdvancedSearch\Model\ResourceModel\Recent as ResourceModel;

class Recent extends AbstractModel
{
    /**
     * Recent
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
