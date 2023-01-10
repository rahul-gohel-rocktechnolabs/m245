<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel\Order;

use Symfony\Component\Config\Definition\Exception\Exception;

class Attribute extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init('mageants_orderattribute_order_eav_attribute', 'attribute_id');
    }

    /**
     * Add Attribute Field
     *
     * @param string $code
     * @param string $type
     */
    public function addAttributeField($code, $type)
    {
        $sql = sprintf(
            'ALTER TABLE `%s` ADD `%s` %s',
            $this->getAttributeFieldTableName(),
            $code,
            $this->getSqlType($type)
        );
        $this->getConnection()->query($sql);

        $sql = sprintf(
            'ALTER TABLE `%s` ADD `%s` %s',
            $this->getAttributeFieldTableName(),
            $code . '_output',
            $this->getSqlType('text')
        );
        $this->getConnection()->query($sql);
    }

    /**
     * Get sql type
     *
     * @param string $fieldType
     * @return string
     */
    protected function getSqlType($fieldType)
    {
        switch ($fieldType) {
            case 'textarea':
                $type = 'TEXT';
                break;
            case 'text':
                $type = 'TEXT ';
                break;
            case 'date':
                $type = 'DATE NULL';
                break;
            case 'datetime':
                $type = 'DATETIME NULL';
                break;
            case 'boolean':
                $type = 'TINYINT(1) UNSIGNED';
                break;
            case 'select':
            case 'radios':
                $type = 'INT(11) UNSIGNED';
                break;
            default:
                $type = 'TEXT';
                break;
        }
        return $type;
    }

    /**
     * Drop attribute field
     *
     * @param string $code
     */
    public function dropAttributeField($code)
    {
        $sql = sprintf(
            'ALTER TABLE `%s` DROP COLUMN `%s`',
            $this->getAttributeFieldTableName(),
            $code
        );
        $this->getConnection()->query($sql);

        try {
            $sql = sprintf(
                'ALTER TABLE `%s` DROP COLUMN `%s_output`',
                $this->getAttributeFieldTableName(),
                $code
            );
            $this->getConnection()->query($sql);
        } catch (\Exception $e) {
            null;
        }
    }

    /**
     * Get attribute field table name
     *
     * @return string
     */
    protected function getAttributeFieldTableName()
    {
        return $this->getTable(
            'mageants_orderattribute_order_attribute_value'
        );
    }
}
