<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Model;

/**
 * Sales archive operations model
 */
class Archive
{
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * Sales archive resource archive
     *
     * @var \Mageants\OrderArchive\Model\ResourceModel\Archive
     */
    protected $_resourceArchive;

    /**
     * @param \Mageants\OrderArchive\Model\ResourceModel\Archive $resourceArchive
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Mageants\OrderArchive\Model\ResourceModel\Archive $resourceArchive,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->_resourceArchive = $resourceArchive;
        $this->_eventManager = $eventManager;
    }

    /**
     * Update grid records in archive
     *
     * @param string $archiveEntity
     * @param array $ids
     * @return $this
     */
    public function updateGridRecords($archiveEntity, $ids)
    {
        $this->_resourceArchive->updateGridRecords($this, $archiveEntity, $ids);
        return $this;
    }

    /**
     * Retrieve ids in archive for specified entity
     *
     * @param string $archiveEntity
     * @param array $ids
     * @return array
     */
    public function getIdsInArchive($archiveEntity, $ids)
    {
        return $this->_resourceArchive->getIdsInArchive($archiveEntity, $ids);
    }

    /**
     * Archive orders
     *
     * @throws \Exception
     * @return $this
     */
    public function archiveOrders()
    {
        $orderIds = $this->_resourceArchive->getOrderIdsForArchiveExpression();
        $this->_resourceArchive->beginTransaction();
        try {
            $this->_resourceArchive->moveToArchive(
                \Mageants\OrderArchive\Model\ArchivalList::ORDER,
                'entity_id',
                $orderIds
            );
            $this->_resourceArchive->moveToArchive(
                \Mageants\OrderArchive\Model\ArchivalList::INVOICE,
                'order_id',
                $orderIds
            );
            $this->_resourceArchive->moveToArchive(
                \Mageants\OrderArchive\Model\ArchivalList::SHIPMENT,
                'order_id',
                $orderIds
            );
            $this->_resourceArchive->moveToArchive(
                \Mageants\OrderArchive\Model\ArchivalList::CREDITMEMO,
                'order_id',
                $orderIds
            );
            $this->_resourceArchive->removeFromGrid(
                \Mageants\OrderArchive\Model\ArchivalList::ORDER,
                'entity_id',
                $orderIds
            );
            $this->_resourceArchive->removeFromGrid(
                \Mageants\OrderArchive\Model\ArchivalList::INVOICE,
                'order_id',
                $orderIds
            );
            $this->_resourceArchive->removeFromGrid(
                \Mageants\OrderArchive\Model\ArchivalList::SHIPMENT,
                'order_id',
                $orderIds
            );
            $this->_resourceArchive->removeFromGrid(
                \Mageants\OrderArchive\Model\ArchivalList::CREDITMEMO,
                'order_id',
                $orderIds
            );
            $this->_resourceArchive->commit();
        } catch (\Exception $e) {
            $this->_resourceArchive->rollBack();
            throw $e;
        }
        $this->_eventManager->dispatch('Mageants_OrderArchive_archive_archive_orders', ['order_ids' => $orderIds]);
        return $this;
    }

    /**
     * Archive orders, returns archived order ids
     *
     * @param array $orderIds
     * @throws \Exception
     * @return array
     */
    public function archiveOrdersById($orderIds)
    {
        $orderIds = $this->_resourceArchive->getOrderIdsForArchive($orderIds, false);

        if (!empty($orderIds)) {
            $this->_resourceArchive->beginTransaction();
            try {
                $this->_resourceArchive->moveToArchive(
                    \Mageants\OrderArchive\Model\ArchivalList::ORDER,
                    'entity_id',
                    $orderIds
                );
                $this->_resourceArchive->moveToArchive(
                    \Mageants\OrderArchive\Model\ArchivalList::INVOICE,
                    'order_id',
                    $orderIds
                );
                $this->_resourceArchive->moveToArchive(
                    \Mageants\OrderArchive\Model\ArchivalList::SHIPMENT,
                    'order_id',
                    $orderIds
                );
                $this->_resourceArchive->moveToArchive(
                    \Mageants\OrderArchive\Model\ArchivalList::CREDITMEMO,
                    'order_id',
                    $orderIds
                );
                $this->_resourceArchive->removeFromGrid(
                    \Mageants\OrderArchive\Model\ArchivalList::ORDER,
                    'entity_id',
                    $orderIds
                );
                $this->_resourceArchive->removeFromGrid(
                    \Mageants\OrderArchive\Model\ArchivalList::INVOICE,
                    'order_id',
                    $orderIds
                );
                $this->_resourceArchive->removeFromGrid(
                    \Mageants\OrderArchive\Model\ArchivalList::SHIPMENT,
                    'order_id',
                    $orderIds
                );
                $this->_resourceArchive->removeFromGrid(
                    \Mageants\OrderArchive\Model\ArchivalList::CREDITMEMO,
                    'order_id',
                    $orderIds
                );
                $this->_resourceArchive->commit();
            } catch (\Exception $e) {
                $this->_resourceArchive->rollBack();
                throw $e;
            }
            $this->_eventManager->dispatch(
                'Mageants_OrderArchive_archive_archive_orders',
                ['order_ids' => $orderIds]
            );
        }

        return $orderIds;
    }

    /**
     * Move all orders from archive grid tables to regular grid tables
     *
     * @throws \Exception
     * @return $this
     */
    public function removeOrdersFromArchive()
    {
        $this->_resourceArchive->beginTransaction();
        try {
            $this->_resourceArchive->removeFromArchive(\Mageants\OrderArchive\Model\ArchivalList::ORDER);
            $this->_resourceArchive->removeFromArchive(\Mageants\OrderArchive\Model\ArchivalList::INVOICE);
            $this->_resourceArchive->removeFromArchive(\Mageants\OrderArchive\Model\ArchivalList::SHIPMENT);
            $this->_resourceArchive->removeFromArchive(\Mageants\OrderArchive\Model\ArchivalList::CREDITMEMO);
            $this->_resourceArchive->commit();
        } catch (\Exception $e) {
            $this->_resourceArchive->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Restores the orders from archive to order grid tables
     *
     * @param array $orderIds
     * @throws \Exception
     * @return array The restored order ids
     */
    public function removeOrdersFromArchiveById($orderIds)
    {
        return $this->_resourceArchive->removeOrdersFromArchiveById($orderIds);
    }

    /**
     * Find related to order entity ids for checking of new items in archive
     *
     * @param string $archiveEntity
     * @param array $ids
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRelatedIds($archiveEntity, $ids)
    {
        return $this->_resourceArchive->getRelatedIds($archiveEntity, $ids);
    }
}
