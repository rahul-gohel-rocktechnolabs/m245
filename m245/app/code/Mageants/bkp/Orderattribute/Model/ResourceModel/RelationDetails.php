<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RelationDetails extends AbstractDb
{
    /**
     * Construct
     */
    public function _construct()
    {
        $this->_init('mageants_orderattribute_attributes_relation_details', 'id');
    }

    /**
     * Delete Details data for relation
     *
     * @param int $relationId
     */
    public function deleteAllDetailForRelation($relationId)
    {
        $this->getConnection()->delete($this->getMainTable(), ['relation_id = ?' => $relationId]);
    }

    /**
     * Fast delete
     *
     * @param int $ids
     */
    public function fastDelete($ids)
    {
        $db = $this->getConnection();
        $table = $this->getTable('mageants_customer_attributes_details');
        $db->delete($table, $db->quoteInto('id IN(?)', $ids));
    }
}
