<?php

namespace Mageants\ExtraFee\Model\Sales\Total\Quote;

use Magento\Customer\Api\Data\AddressInterfaceFactory as CustomerAddressFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory as CustomerAddressRegionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Tax\Api\Data\TaxClassKeyInterface;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\Config;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Api\Data\QuoteDetailsInterfaceFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory;
use Magento\Tax\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageants\ExtraFee\Helper\Data as helperdata;

class Tax extends \Magento\Tax\Model\Sales\Total\Quote\Tax
{
    public const XML_PATH_EXTRAFEE_TAX =  'mageants_extrafee/setting/extrafee_tax';
    
    /**
     * Constructor
     *
     * @param Config $taxConfig
     * @param TaxCalculationInterface $taxCalculationService
     * @param QuoteDetailsInterfaceFactory $quoteDetailsDataObjectFactory
     * @param QuoteDetailsItemInterfaceFactory $quoteDetailsItemDataObjectFactory
     * @param TaxClassKeyInterfaceFactory $taxClassKeyDataObjectFactory
     * @param CustomerAddressFactory $customerAddressFactory
     * @param CustomerAddressRegionFactory $customerAddressRegionFactory
     * @param Data $taxData
     * @param Json $serializer = null
     * @param ScopeConfigInterface $scopeConfig
     * @param helperdata $helperData
     * @param SessionFactory $customerSession
     * @param StoreManagerInterface $storeManager
     * @param Rate $taxrate
     */
    public function __construct(
        Config $taxConfig,
        TaxCalculationInterface $taxCalculationService,
        QuoteDetailsInterfaceFactory $quoteDetailsDataObjectFactory,
        QuoteDetailsItemInterfaceFactory $quoteDetailsItemDataObjectFactory,
        TaxClassKeyInterfaceFactory $taxClassKeyDataObjectFactory,
        CustomerAddressFactory $customerAddressFactory,
        CustomerAddressRegionFactory $customerAddressRegionFactory,
        Data $taxData,
        Json $serializer = null,
        ScopeConfigInterface $scopeConfig,
        helperdata $helperData,
        SessionFactory $customerSession,
        StoreManagerInterface $storeManager,
        Rate $taxrate
    ) {
        $this->setCode('tax');
        $this->_taxData = $taxData;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        $this->scopeConfig = $scopeConfig;
        $this->helperData = $helperData;
        $this->taxcalculation = $taxCalculationService;
        $this->customerSession =$customerSession;
        $this->_storeManager =$storeManager;
        $this->taxConfig = $taxConfig;
        $this->_taxModelConfig = $taxrate;
        parent::__construct(
            $taxConfig,
            $taxCalculationService,
            $quoteDetailsDataObjectFactory,
            $quoteDetailsItemDataObjectFactory,
            $taxClassKeyDataObjectFactory,
            $customerAddressFactory,
            $customerAddressRegionFactory,
            $taxData,
            $serializer
        );
    }
    /**
     * To Collect the all values
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $this->clearValues($total);
        if (!$shippingAssignment->getItems()) {
            return $this;
        }
       
        $total->setData('extra_tax_amount', 300);
        $baseTaxDetails = $this->getQuoteTaxDetails($shippingAssignment, $total, true);
        $taxDetails = $this->getQuoteTaxDetails($shippingAssignment, $total, false);

        //Populate address and items with tax calculation results
        $itemsByType = $this->organizeItemTaxDetailsByType($taxDetails, $baseTaxDetails);
        if (isset($itemsByType[self::ITEM_TYPE_PRODUCT])) {
            $this->processProductItems($shippingAssignment, $itemsByType[self::ITEM_TYPE_PRODUCT], $total);
        }

        if (isset($itemsByType[self::ITEM_TYPE_SHIPPING])) {
            $shippingTaxDetails = $itemsByType[self::ITEM_TYPE_SHIPPING][self::ITEM_CODE_SHIPPING][self::KEY_ITEM];
            $baseShippingTaxDetails =
                $itemsByType[self::ITEM_TYPE_SHIPPING][self::ITEM_CODE_SHIPPING][self::KEY_BASE_ITEM];
            $this->processShippingTaxInfo($shippingAssignment, $total, $shippingTaxDetails, $baseShippingTaxDetails);
        }

        //Process taxable items that are not product or shipping
        $this->processExtraTaxables($total, $itemsByType);

        //Save applied taxes for each item and the quote in aggregation
        $this->processAppliedTaxes($total, $shippingAssignment, $itemsByType);
        if ($this->helperData->getExtrafeetax()) {
            $amount = $total->getTaxAmount();
            $enabled = $this->helperData->isModuleEnabled();
            $rate = [];
            if (!empty($quote->getShippingAddress()->getAppliedTaxes())) {
                $rate = array_column($quote->getShippingAddress()->getAppliedTaxes(), 'percent');
            }

            if ($enabled) {
                 $fee = $this->helperData->getExtrafee();
                 $extraFeeTax = 0;
                if (isset($rate[0])) {
                    $extraFeeTax = ($rate[0] * $fee) / 100;
                } else {
                    $classid =$quote->getCustomerTaxClassId();
                    $taxRates = $this->_taxModelConfig->getCollection()->getData();
                    $array_rates =array_column($taxRates, 'tax_calculation_rate_id');
                    $tax_id = array_search($classid, $array_rates);
                    if (isset($taxRates[$tax_id])) {
                        $taxData = $taxRates[$tax_id];
                        $rate = $taxData['rate'];
                    }
                    
                    $extraFeeTax = ($rate * $fee) / 100;
                    
                }
                 $amount = $amount + $extraFeeTax;
               
                $total->setTotalAmount('tax', $amount);
                $total->setExtraTax($extraFeeTax);
                $quote->setExtraTax($extraFeeTax);
            }
        }
        if ($this->includeExtraTax()) {
            $total->addTotalAmount('extra_tax', $total->getExtraTaxAmount());
            $total->addBaseTotalAmount('extra_tax', $total->getBaseExtraTaxAmount());
        }

        return $this;
    }
}
