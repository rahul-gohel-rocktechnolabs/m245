<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;

class DeliveryDateConfigProvider implements ConfigProviderInterface
{
    /**
     * @var deliveryhelper
     */
    public $deliveryhelper;

    /**
     * @var serializer
     */
    protected $serializer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageants\DeliveryDate\Helper\Data $deliveryhelper
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $quoteItemCollection
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageants\DeliveryDate\Helper\Data $deliveryhelper,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $quoteItemCollection,
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->deliveryhelper = $deliveryhelper;
        $this->cart = $cart;
        $this->quoteItemCollection = $quoteItemCollection;
    }

    /**
     * Get Config Values
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        $store = $this->getStoreId();
        $disabled = $this->deliveryhelper->getDisableValue();
        $hourMin = $this->deliveryhelper->getHourMin();
        $hourMax = $this->deliveryhelper->getHourMax();
        $format = $this->deliveryhelper->getDateFormat();

        $noday = 0;
        if ($disabled == -1) {
            $noday = 1;
        }

        $getTimeslot=$this->deliveryhelper->getTimeSlot();
        
        $timeslotArr=[];

        if (count($getTimeslot)>0) {
            foreach ($getTimeslot as $key => $value) {
                $dayTimeslotArr=[];
                foreach ($value as $k => $val) {
                    $dayTimeslotArr[]=$val;
                }
                $timeslotArr[$key]=$dayTimeslotArr;
            }
        } else {
            $timeslotArr=[];
        }
        $getHolidays=$this->deliveryhelper->getPluginHolidays();
        $holidaysArr = [];
        
        if (!empty($getHolidays)) {
            if (count($this->serializer->unserialize($getHolidays))>0) {
                foreach ($this->serializer->unserialize($getHolidays) as $key => $value) {
                    $value['Date']=date('n-j-Y', strtotime($value['Date']));
                    $holidaysArr[]=$value['Date'];
                }
            } else {
                $holidaysArr[]=[];
            }
        } else {
            $holidaysArr[]=[];
        }
        $holidaysArr[]=[];
        $getDisableDays=$this->deliveryhelper->getPluginDisableDays();

        $getDateFormat=$this->deliveryhelper->getPluginDateFormat();

        $getArrivalComment=$this->deliveryhelper->getPluginArrivalComment();

        $getCutOffTime=$this->deliveryhelper->getPluginCutoffTime();

        $getProcessTime=$this->deliveryhelper->getPluginProcessTime();

        $getdisplayAt = $this->deliveryhelper->getPluginDisplayAt();

        $baseUrl = $this->storeManager->getStore()->getBaseUrl().'helloworld/index/productcheckoutdata';

        if ($this->deliveryhelper->getPluginDisplayAt() == 3) {
            $cart = $this->cart;

            $quote = $cart->getQuote();
            // This will return the current quote
            $quoteId = $quote->getId();
            $quoteItemCollection =$this->quoteItemCollection;
            $quoteItemCollection->addFieldToFilter('quote_id', $quoteId);
            $quoteItemData = $quoteItemCollection->getData();
            $delivery_date_arr = [];
            $delivery_time_arr = [];
            $delivery_comment_arr = [];
            $i = 0;
            foreach ($quoteItemCollection as $value) {
                if ($value->getData('delivery_date') != null) {
                    $delivery_date_arr[$i]['item_id'] = $value->getProductId();
                    $delivery_date_arr[$i]['delivery_date'] = $value->getData('delivery_date');
                    $delivery_time_arr[$i]['item_id'] = $value->getProductId();
                    $delivery_time_arr[$i]['delivery_timeslot'] = $value->getData('delivery_timeslot');
                    $delivery_comment_arr[$i]['item_id'] = $value->getProductId();
                    $delivery_comment_arr[$i]['delivery_comment'] = $value->getData('delivery_comment');
                    $i++;
                }
            }
            $additional_date = $this->serializer->serialize($delivery_date_arr);
            $additional_time = $this->serializer->serialize($delivery_time_arr);
            $additional_comment = $this->serializer->serialize($delivery_comment_arr);
            
            $config = [
                'shippingproduct' => [
                    'delivery_date_product' => [
                        'format' => $format,
                        'disabled' => $disabled,
                        'noday' => $noday,
                        'hourMin' => $hourMin,
                        'hourMax' => $hourMax,
                        'timeslot' => $timeslotArr,
                        'holidays'=>$holidaysArr,
                        'disabledays'=>$getDisableDays,
                        'dateformat'=>$getDateFormat,
                        'cutofftime'=>$getCutOffTime,
                        'processtime'=>$getProcessTime,
                        'date_product'=>$additional_date,
                        'time_product'=>$additional_time,
                        'comment_product'=>$additional_comment,
                        'arrivalcomment'=>$getArrivalComment,
                        'displayAt'=>$getdisplayAt,
                        'baseUrl'=> $baseUrl
                    ]
                ]
            ];
            return $config;
        } else {
            $config = [
            'shipping' => [
                'delivery_date' => [
                    'format' => $format,
                    'disabled' => $disabled,
                    'noday' => $noday,
                    'hourMin' => $hourMin,
                    'hourMax' => $hourMax,
                    'timeslot' => $timeslotArr,
                    'holidays'=>$holidaysArr,
                    'disabledays'=>$getDisableDays,
                    'dateformat'=>$getDateFormat,
                    'arrivalcomment'=>$getArrivalComment,
                    'cutofftime'=>$getCutOffTime,
                    'processtime'=>$getProcessTime
                ]
            ]
            ];

            return $config;
        }
    }

    /**
     * Get Store Id function
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }
}
