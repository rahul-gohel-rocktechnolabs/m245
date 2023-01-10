<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Cron;

class ArchiveOrdersByCron
{
    /**
     * @var \Mageants\OrderArchive\Model\Config
     */
    protected $_config;

    /**
     * @var \Mageants\OrderArchive\Model\ArchiveFactory
     */
    protected $_archiveFactory;

    /**
     * @param \Mageants\OrderArchive\Model\Config $config
     * @param \Mageants\OrderArchive\Model\ArchiveFactory $archiveFactory
     */
    public function __construct(
        \Mageants\OrderArchive\Model\Config $config,
        \Mageants\OrderArchive\Model\ArchiveFactory $archiveFactory
    ) {
        $this->_config = $config;
        $this->_archiveFactory = $archiveFactory;
    }

    /**
     * Archive order by cron
     *
     * @return $this
     */
    public function execute()
    {
        if ($this->_config->isArchiveActive()) {
            $this->_archiveFactory->create()->archiveOrders();
        }

        return $this;
    }
}
