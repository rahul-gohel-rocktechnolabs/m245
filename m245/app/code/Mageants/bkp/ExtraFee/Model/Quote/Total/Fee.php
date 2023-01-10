<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ExtraFee\Model\Quote\Total;

use Magento\Store\Model\ScopeInterface;
use \Magento\Quote\Model\QuoteValidator;
use \Magento\Framework\Pricing\PriceCurrencyInterface;
use \Mageants\ExtraFee\Helper\Data;
use \Magento\Framework\Stdlib\CookieManagerInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use \Magento\Quote\Model\Quote\Address\Total;

class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    public const XML_PATH_EXTRAFEE_TAX = 'mageants_extrafee/setting/extrafee_tax';
    /**
     * @var Data
     */
    protected $helperData;
    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;
    /**
     * @var QuoteValidators
     */
    protected $quoteValidator = null;
    /**
     * Collect grand total address amount
     *
     * @param QuoteValidator $quoteValidator
     * @param PriceCurrencyInterface $priceCurrency
     * @param Data $helperData
     * @param CookieManagerInterface $cookieManager
     * @param ScopeConfigInterface $scopeConfig
     * @return $this
     */

    public function __construct(
        QuoteValidator $quoteValidator,
        PriceCurrencyInterface $priceCurrency,
        Data $helperData,
        CookieManagerInterface $cookieManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->quoteValidator = $quoteValidator;
        $this->_priceCurrency = $priceCurrency;
        $this->helperData = $helperData;
        $this->scopeConfig = $scopeConfig;
        $this->_cookieManager = $cookieManager;
    }

    /**
     * Collect grand total address amount
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }
        $orderFee=$this->_cookieManager->getCookie("orderExtrafeeAmount");
        $enabled = $this->helperData->isModuleEnabled();
        $subtotal = $total->getTotalAmount('subtotal');
        if ($enabled) {
             $fee = $this->helperData->getExtrafee();
             $mandatoryOrderExtraFeeAmount = $this->_cookieManager->getCookie("mandatoryOrderExtraFeeAmount");
             
             $codFee = $this->_cookieManager->getCookie("codFee");
            if ($orderFee != "") {
                $fee = $fee + $orderFee;
            }
            if ($codFee != null) {
                $fee = $fee + $codFee;
            }
            if ($mandatoryOrderExtraFeeAmount != "") {
                $fee = $fee + $mandatoryOrderExtraFeeAmount;
            }
            if ($this->helperData->getExtrafeetax()
                ) {
                $extraFeeTax = 0;
                $rate = [];
                if (!empty($quote->getShippingAddress()->getAppliedTaxes())) {
                    $rate = array_column($quote->getShippingAddress()->getAppliedTaxes(), 'percent');
                }
                if (isset($rate[0])) {
                    $extraFeeTax = ($rate[0] * $fee) / 100;
                }
                $tax = $quote->getShippingAddress()->getData('tax_amount');
                $tax = $tax + $extraFeeTax;
                $enabled = $this->helperData->isModuleEnabled();
                $subtotal = $total->getTotalAmount('subtotal');
             
            }
            
            $total->setTotalAmount('fee', $fee);
            $total->setBaseTotalAmount('fee', $fee);
            $total->setFee($fee);
            $quote->setFee($fee);
            //$total->setGrandTotal($total->getGrandTotal() + $fee);
        }
        return $this;
    }

    /**
     * To get Quote and total
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {

        $enabled = $this->helperData->isModuleEnabled();
        $subtotal = $quote->getSubtotal();
        $fee = 0;
        $fee = $this->helperData->getExtrafee();
        $orderFee=$this->_cookieManager->getCookie("orderExtrafeeAmount");
        $mandatoryOrderExtraFeeAmount = $this->_cookieManager->getCookie("mandatoryOrderExtraFeeAmount");

        $codFee = $this->_cookieManager->getCookie("codFee");
        if ($orderFee != "") {
            $fee = $fee + $orderFee;
        }
        if ($codFee != null) {
            $fee = $fee + $codFee;
        }
        if ($mandatoryOrderExtraFeeAmount != "") {
            $fee = $fee + $mandatoryOrderExtraFeeAmount;
        }
        $result = [];
        if ($enabled && $fee) {
            $result = [
                'code' => 'fee',
                'title' => $this->getLabel(),
                'value' => $fee
            ];
        }
        return $result;
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Total Extra Fee');
    }

    /**
     * To set the total
     *
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     */
    protected function clearValues(\Magento\Quote\Model\Quote\Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
}
