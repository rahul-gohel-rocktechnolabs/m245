<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Block\Adminhtml\Sales\Order\View;

use Magento\Sales\Model\ConfigInterface;

/**
 * Class Buttons
 *
 * @api
 * @since 100.0.2
 */
class Buttons extends \Magento\Sales\Block\Adminhtml\Order\View
{
    /**
     * @var \Mageants\OrderArchive\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ConfigInterface $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param \Mageants\OrderArchive\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        ConfigInterface $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Mageants\OrderArchive\Model\Config $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
    }

    /**
     * Add "Move to Order Management" button
     *
     * @return void
     */
    protected function addMoveToArchiveButton()
    {
        $archiveUrl = $this->getUrl(
            'sales/archive/add',
            ['order_id' => $this->getOrder()->getId()]
        );
        $this->getToolbar()->addChild(
            'move_to_archive',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Archive Order'),
                'onclick' => 'setLocation(\'' . $archiveUrl . '\')'
            ]
        );
    }

    /**
     * Add "Move to Order Management" button
     *
     * @return void
     */
    protected function addRestoreFromArchiveButton()
    {
        $restoreUrl = $this->getUrl(
            'sales/archive/remove',
            ['order_id' => $this->getOrder()->getId()]
        );
        $this->getToolbar()->addChild(
            'restore_from_archive',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('UnArchive Order'),
                'onclick' => 'setLocation(\'' . $restoreUrl . '\')'
            ]
        );
    }

    /**
     * Add SalesArchive buttons on toolbar
     *
     * @return $this
     */
    public function addButtons()
    {
        if ($this->getOrder()->getIsArchived()
            && $this->_authorization->isAllowed('Mageants_OrderArchive::remove')
        ) {
            $this->addRestoreFromArchiveButton();
        } elseif ($this->getOrder()->getIsMoveable() !== false
            && $this->config->isArchiveActive()
            && $this->_authorization->isAllowed('Mageants_OrderArchive::add')
        ) {
            $this->addMoveToArchiveButton();
        }
        return $this;
    }
}
