<?php
namespace Mageants\OrderApprovalRules\Model\Payment;

class Orderapproval extends \Magento\Payment\Model\Method\AbstractMethod
{
     
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'orderapproval';
 
    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
}
