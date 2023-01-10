<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Model\Order\Grid\Massaction;

class ItemsUpdater implements \Magento\Framework\View\Layout\Argument\UpdaterInterface
{
    /**
     * @var \Mageants\OrderArchive\Model\Config $_salesArchiveConfig
     */
    protected $_salesArchiveConfig;

    /**
     * @var \Magento\Framework\AuthorizationInterface $_authModel
     */
    protected $_authorizationModel;

    /**
     * @param \Mageants\OrderArchive\Model\Config $config
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Mageants\OrderArchive\Model\Config $config,
        \Magento\Framework\AuthorizationInterface $authorization,
        $data = []
    ) {
        $this->_salesArchiveConfig = $config;
        $this->_authorizationModel = $authorization;
    }

    /**
     * Remove massaction items in case they disallowed for user
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if ($this->_salesArchiveConfig->isArchiveActive() === false
            || $this->_authorizationModel->isAllowed('Mageants_OrderArchive::add') === false
        ) {
            unset($argument['add_order_to_archive']);
        }

        return $argument;
    }
}
