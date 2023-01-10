<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ExtraFee\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Fee extends AbstractTotal
{
    /**
     * To collect totalfee in invoice
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setFee(0);
        $amount = $invoice->getOrder()->getFee();
        $invoice->setFee($amount);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getFee());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getFee());
        return $this;
    }
}
