<?php
 /**
  * @category  Mageants Product Question Answser
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */

namespace Mageants\ProductQA\Model\ResourceModel\ProductQuestionAnswer;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
     /**

      * @var string
      */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Mageants\ProductQA\Model\ProductQuestionAnswer::class,
            \Mageants\ProductQA\Model\ResourceModel\ProductQuestionAnswer::class
        );
    }
     /**
      * Get SQL for get record count.
      *
      * @return \Magento\Framework\DB\Select
      */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        
        return $countSelect;
    }
}
