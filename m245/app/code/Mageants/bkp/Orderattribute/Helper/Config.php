<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const CHECKOUT_PROGRESS = 'mgorderattribute/checkout/progress';
    public const CHECKOUT_HIDE_EMPTY = 'mgorderattribute/checkout/hide_empty';
    public const CHECKOUT_DATE_FORMAT = 'mgorderattribute/checkout/format';
    public const PDF_SHIPMENT = 'mgorderattribute/pdf/shipment';
    public const PDF_INVOICE = 'mgorderattribute/pdf/invoice';
    public const SHOW_INVOICE_GRID = 'mgorderattribute/invoices_shipments/invoice_grid';
    public const SHOW_INVOICE_VIEW = 'mgorderattribute/invoices_shipments/invoice_view';
    public const SHOW_SHIPMENT_GRID = 'mgorderattribute/invoices_shipments/shipment_grid';
    public const SHOW_SHIPMENT_VIEW = 'mgorderattribute/invoices_shipments/shipment_view';
    public const SHOW_EMAIL = 'mgorderattribute/checkout/email';

    /**
     * Get carrier config value
     *
     * @param string $carrierCode
     * @return string
     */
    public function getCarrierConfigValue($carrierCode)
    {
        $configPath = sprintf('carriers/%s/title', $carrierCode);
        return $this->scopeConfig->getValue($configPath);
    }

    /**
     * Get required on front only id
     *
     * @return int
     */
    public function getRequiredOnFrontOnlyId()
    {
        return 2;
    }

    /**
     * Get checkout date format
     *
     * @return string
     */
    public function getCheckoutDateFormat()
    {
        $value = $this->getValue(self::CHECKOUT_DATE_FORMAT);
        if (!$value) {
            $value = 'yyyy-MM-dd';
        }

        return $value;
    }

    /**
     * Underscore
     *
     * @param string $name
     * @return string
     */
    protected function underscore($name)
    {
        return strtolower(
            trim(preg_replace('/([A-Z]|[0-9]+)/', "_$1", $name), '_')
        );
    }

    /**
     * Get value
     *
     * @param string $key
     * @return string
     */
    protected function getValue($key)
    {
        return $this->scopeConfig->getValue($key);
    }

    /**
     * Call
     *
     * @param string $getterName
     * @param string $arguments
     */
    public function __call($getterName, $arguments)
    {
        switch (substr($getterName, 0, 3)) {
            case 'get':
                $key = $this->underscore(substr($getterName, 3));
                $key = function_exists('mb_strtoupper')
                ? mb_strtoupper($key) : strtoupper($key);
                $configPath = constant("static::$key");
                return $this->getValue($configPath);
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Invalid method %1::%2(%3)', [get_class($this), $getterName])
        );
    }

    /**
     * Enabled
     *
     * @return isEnabled
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            self::CHECKOUT_PROGRESS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
