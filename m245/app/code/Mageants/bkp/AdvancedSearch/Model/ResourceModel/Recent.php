<?php
/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace  Mageants\AdvancedSearch\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Recent extends AbstractDb
{

    /**
     * Recent
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('search_query', 'query_id');
    }
}
