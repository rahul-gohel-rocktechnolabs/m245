<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Helper;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Data class for Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var _timeslot
     */
    protected $_timeslot;
    /**
     * @var serializer
     */
    protected $serializer;
    /**
     * @var OrderSaveFactory
     */
    public $OrderSaveFactory;

    public const TIMESLOT = 'mageants_datedelivery/timeslot/friday';
    public const PLUGIN_ENABLE = 'mageants_datedelivery/general/enable';
    public const PLUGIN_DISPLAYAT = 'mageants_datedelivery/general/displayat';
    public const PLUGIN_INCLUDEINTO = 'mageants_datedelivery/general/includeinto';
    public const PLUGIN_HOLIDAYS = 'mageants_datedelivery/general/holidays';
    public const PLUGIN_DISABLEDAYS = 'mageants_datedelivery/general/disabledeliverydate';
    public const PLUGIN_DATEFORMAT = 'mageants_datedelivery/general/datefields';
    public const PLUGIN_ARRIVALCOMMENT = 'mageants_datedelivery/general/shippingarrivalcomment';
    public const PLUGIN_CUTOFFTIME = 'mageants_datedelivery/general/cutofftime';
    public const PLUGIN_PROCESSTIME = 'mageants_datedelivery/general/processingtime';
    
    public const XPATH_FORMAT = 'Mageants_DeliveryDate/general/format';
    public const XPATH_DISABLED = 'Mageants_DeliveryDate/general/disabled';
    public const XPATH_HOURMIN = 'Mageants_DeliveryDate/general/hourMin';
    public const XPATH_HOURMAX = 'Mageants_DeliveryDate/general/hourMax';
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Mageants\DeliveryDate\Model\OrderSaveFactory $OrderSaveFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Mageants\DeliveryDate\Model\OrderSaveFactory $OrderSaveFactory,
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        parent::__construct($context);
        $this->OrderSaveFactory = $OrderSaveFactory;
    }

    /**
     * Get Time Slot function
     *
     * @return array
     */
    public function getTimeSlot()
    {
        $timeslotArray=[];
        $sunday  = $this->scopeConfig
        ->getValue('mageants_datedelivery/timeslot/sunday', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($sunday)) {
             $timeslotArray['sunday']=$this->serializer->unserialize($sunday);
        }
        $monday = $this->scopeConfig
        ->getValue('mageants_datedelivery/timeslot/monday', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($monday)) {
            $timeslotArray['monday']=$this->serializer->unserialize($monday);
        }
        $tuesday = $this->scopeConfig
        ->getValue('mageants_datedelivery/timeslot/tuesday', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($tuesday)) {
            $timeslotArray['tuesday']=$this->serializer->unserialize($tuesday);
        }
        $wednesday= $this->scopeConfig
        ->getValue('mageants_datedelivery/timeslot/wednesday', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($wednesday)) {
            $timeslotArray['wednesday']=$this->serializer->unserialize($wednesday);
        }
        $thursday=$this->scopeConfig
        ->getValue('mageants_datedelivery/timeslot/thursday', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($thursday)) {
            $timeslotArray['thursday']=$this->serializer->unserialize($thursday);
        }
        $friday=$this->scopeConfig
        ->getValue('mageants_datedelivery/timeslot/friday', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($friday)) {
            $timeslotArray['friday']=$this->serializer->unserialize($friday);
        }
        $saturday=$this->scopeConfig
        ->getValue('mageants_datedelivery/timeslot/saturday', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($saturday)) {
            $timeslotArray['saturday']=$this->serializer->unserialize($saturday);
        }
        
        return $timeslotArray;
    }
    /**
     * Check Module is Enable Or Not function
     *
     * @return bool
     */
    public function getPluginEnable()
    {
        return $this->scopeConfig->getValue(
            self::PLUGIN_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Display Position function
     *
     * @return bool
     */
    public function getPluginDisplayAt()
    {
        return $this->scopeConfig->getValue(
            self::PLUGIN_DISPLAYAT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get Included in function
     *
     * @param mixed $storeid
     * @return mixed
     */
    public function getPluginIncludeInto($storeid)
    {
        return $this->scopeConfig->getValue(
            self::PLUGIN_INCLUDEINTO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeid
        );
    }
    /**
     * Get Holiday function
     *
     * @return bool
     */
    public function getPluginHolidays()
    {
        return $this->scopeConfig->getValue(
            self::PLUGIN_HOLIDAYS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get Disable Day function
     *
     * @return bool
     */
    public function getPluginDisableDays()
    {
        return $this->scopeConfig->getValue(
            self::PLUGIN_DISABLEDAYS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get Date Format function
     *
     * @return mixed
     */
    public function getPluginDateFormat()
    {
        return $this->scopeConfig->getValue(
            self::PLUGIN_DATEFORMAT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get Comment function
     *
     * @return mixed
     */
    public function getPluginArrivalComment()
    {
        return $this->scopeConfig->getValue(
            self::PLUGIN_ARRIVALCOMMENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get Cutoff time function
     *
     * @return mixed
     */
    public function getPluginCutoffTime()
    {
        return $this->scopeConfig->getValue(
            self::PLUGIN_CUTOFFTIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Process Time function
     *
     * @return mixed
     */
    public function getPluginProcessTime()
    {
        return $this->scopeConfig->getValue(
            self::PLUGIN_PROCESSTIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get Custom Model function
     *
     * @param mixed $orderId
     * @return mixed
     */
    public function getCustomModelData($orderId)
    {
        $displayAt = '';
        $orderRecord = $this->OrderSaveFactory->create();
        $orderCollection = $orderRecord->getCollection();
        $orderIdData = $orderCollection->addFieldToFilter('order_id', $orderId);
        foreach ($orderIdData as $key => $value) {
            $displayAt = $value['configuration_display_at'];
        }
        return $displayAt;
    }
    /**
     * Get Custom Date Format function
     *
     * @param mixed $orderId
     * @return mixed
     */
    public function getCustomDateFormat($orderId)
    {
        $dateFormat = '';
        $orderRecord = $this->OrderSaveFactory->create();
        $orderCollection = $orderRecord->getCollection();
        $orderIdData = $orderCollection->addFieldToFilter('order_id', $orderId);
        foreach ($orderIdData as $key => $value) {
            $dateFormat = $value['date_format'];
        }
        return $dateFormat;
    }
    /**
     * To get Date Format
     */
    public function getDateFormat()
    {
        $this->scopeConfig->getValue(self::XPATH_FORMAT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    /**
     * To get value for disable
     */
    public function getDisableValue()
    {
        $this->scopeConfig->getValue(self::XPATH_DISABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    /**
     * To get Min Hour
     */
    public function getHourMin()
    {
        $this->scopeConfig->getValue(self::XPATH_HOURMIN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    /**
     * To get Max Hour
     */
    public function getHourMax()
    {
        $this->scopeConfig->getValue(self::XPATH_HOURMAX, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
