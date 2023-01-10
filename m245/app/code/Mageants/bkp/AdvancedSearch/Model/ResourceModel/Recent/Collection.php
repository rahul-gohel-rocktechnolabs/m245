<?php
/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace  Mageants\AdvancedSearch\Model\ResourceModel\Recent;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageants\AdvancedSearch\Model\Recent as Model;
use Mageants\AdvancedSearch\Model\ResourceModel\Recent as ResourceModel;

class Collection extends AbstractCollection
{

    /**
     * For collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
