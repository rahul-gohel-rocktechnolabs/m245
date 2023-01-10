<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
declare(strict_types=1);

namespace Mageants\OrderArchive\Model\ResourceModel\Archive;

use Mageants\OrderArchive\Model\ArchivalList;

/**
 * Class TableMapper
 *
 * @package Mageants\OrderArchive\Model\ResourceModel\Archive
 */
class TableMapper extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Archive entities tables association
     *
     * @var $tables array
     */
    private $tables = [
        ArchivalList::ORDER => [
            'sales_order_grid',
            'mageants_sales_order_grid_archive',
        ],
        ArchivalList::INVOICE => [
            'sales_invoice_grid',
            'magento_sales_invoice_grid_archive',
        ],
        ArchivalList::SHIPMENT => [
            'sales_shipment_grid',
            'magento_sales_shipment_grid_archive',
        ],
        ArchivalList::CREDITMEMO => [
            'sales_creditmemo_grid',
            'magento_sales_creditmemo_grid_archive',
        ],
    ];

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
    }

    /**
     * Check archive entity existence
     *
     * @param string $archiveEntity
     * @return bool
     */
    public function isArchiveEntityExists($archiveEntity)
    {
        return isset($this->tables[$archiveEntity]);
    }

    /**
     * Get archive entity table
     *
     * @param string $archiveEntity
     * @return false|string
     */
    public function getArchiveEntityTable($archiveEntity)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return false;
        }
        return $this->getTable($this->tables[$archiveEntity][1]);
    }

    /**
     * Retrieve archive entity source table
     *
     * @param string $archiveEntity
     * @return false|string
     */
    public function getArchiveEntitySourceTable($archiveEntity)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return false;
        }
        return $this->getTable($this->tables[$archiveEntity][0]);
    }

    /**
     * Retrieve archive entity table by provided source table
     *
     * @param string $sourceEntityTable
     * @return string|null $table
     */
    public function getArchiveEntityTableBySourceTable($sourceEntityTable)
    {
        foreach ($this->tables as $table) {
            if ($table[0] === $sourceEntityTable) {
                return $this->getTable($table[1]);
            }
        }
        return null;
    }
}
