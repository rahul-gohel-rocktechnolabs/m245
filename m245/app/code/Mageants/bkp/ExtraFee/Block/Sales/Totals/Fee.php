<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Block\Sales\Totals;

use Magento\Framework\View\Element\Template\Context;
use Mageants\ExtraFee\Helper\Data;

class Fee extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageants\Extrafee\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /**
     * Declaration for Fee Constructor
     *
     * @param Context $context
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check if we nedd display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->_source;
    }
    /**
     * Get data (totals) store model
     */
    public function getStore()
    {
        return $this->_order->getStore();
    }

    /**
     * To get order details
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * To get label properties
     *
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * To get properties for value
     *
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Get the totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        
        $fee = new \Magento\Framework\DataObject(
            [
                'code' => 'fee',
                'strong' => false,
                'value' => $this->_order->getFee(),
                'label' => $this->_dataHelper->getFeeLabel(),
            ]
        );
        $parent->addTotal($fee, 'fee');

        return $this;
    }
}
