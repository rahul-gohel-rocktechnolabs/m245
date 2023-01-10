<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Order archive observer model
 *
 */
class SalesObjectAfterLoadObserver implements ObserverInterface
{
    /**
     * @var \Mageants\OrderArchive\Model\ArchivalList
     */
    protected $_archivalList;

    /**
     * @var \Mageants\OrderArchive\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData;

    /**
     * @var \Mageants\OrderArchive\Model\ArchiveFactory
     */
    protected $_archiveFactory;

    /**
     * @param \Mageants\OrderArchive\Model\ArchivalList $archivalList
     * @param \Mageants\OrderArchive\Model\Config $config
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Mageants\OrderArchive\Model\ArchiveFactory $archiveFactory
     */
    public function __construct(
        \Mageants\OrderArchive\Model\ArchivalList $archivalList,
        \Mageants\OrderArchive\Model\Config $config,
        \Magento\Backend\Helper\Data $backendData,
        \Mageants\OrderArchive\Model\ArchiveFactory $archiveFactory
    ) {
        $this->_backendData = $backendData;
        $this->_archivalList = $archivalList;
        $this->_config = $config;
        $this->_archiveFactory = $archiveFactory;
    }

    /**
     * Mark sales object as archived and set back urls for them
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->_config->isArchiveActive()) {
            return $this;
        }
        $object = $observer->getEvent()->getDataObject();
        $archive = $this->_archiveFactory->create();
        $archiveEntity = $this->_archivalList->getEntityByObject($object);

        if (!$archiveEntity) {
            return $this;
        }
        $ids = $archive->getIdsInArchive($archiveEntity, $object->getId());
        $object->setIsArchived(!empty($ids));

        if ($object->getIsArchived()) {
            $object->setBackUrl($this->_backendData->getUrl('sales/archive/' . $archiveEntity . 's'));
        } elseif ($object->getIsMoveable() !== false) {
            $object->setIsMoveable(in_array($object->getStatus(), $this->_config->getArchiveOrderStatuses()));
        }

        return $this;
    }
}
