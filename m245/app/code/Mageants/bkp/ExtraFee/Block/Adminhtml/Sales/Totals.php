<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Block\Adminhtml\Sales;

use \Magento\Framework\View\Element\Template\Context;
use \Mageants\ExtraFee\Helper\Data;
use \Magento\Directory\Model\Currency;

class Totals extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Mageants\Extrafee\Helper\Data
     */
    protected $_dataHelper;
   
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Data $dataHelper
     * @param Currency $currency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        Currency $currency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_currency = $currency;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get Data usig Data source
     *
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get CurrencySymbol using currency modle
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * Order Total in inittotals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();

        if (!$this->getSource()->getFee()) {
            return $this;
        }
        $total = new \Magento\Framework\DataObject(
            [
                'code' => 'fee',
                'value' => $this->getOrder()->getfee(),
                'label' => $this->_dataHelper->getFeeLabel(),
            ]
        );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');

        return $this;
    }
}
