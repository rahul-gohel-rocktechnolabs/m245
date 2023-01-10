<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageants\DeliveryDate\Model\Checkout\Type;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Directory\Model\AllowedCountries;
use Psr\Log\LoggerInterface;
use Mageants\DeliveryDate\Helper\Data as DeliveryHelper;
use Magento\Quote\Model\ResourceModel\Quote\Item\Collection as QuoteItemCollection;

/**
 * Multishipping checkout model
 *
 * @api
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @since 100.0.2
 */
class Multishipping extends \Magento\Multishipping\Model\Checkout\Type\Multishipping
{
    /**
     * Quote shipping addresses items cache
     *
     * @var array
     */
    protected $_quoteShippingAddressesItems;
    /**
     * @var deliveryhelper
     */
    public $deliveryhelper;

    /**
     * @var quoteItemCollection
     */
    public $quoteItemCollection;
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $_session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Magento\Payment\Model\Method\SpecificationInterface
     */
    protected $paymentSpecification;

    /**
     * Initialize dependencies.
     *
     * @var \Magento\Multishipping\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrder
     */
    protected $quoteAddressToOrder;

    /**
     * @var \Magento\Quote\Model\Quote\Item\ToOrderItem
     */
    protected $quoteItemToOrderItem;

    /**
     * @var \Magento\Quote\Model\Quote\Payment\ToOrderPayment
     */
    protected $quotePaymentToOrderPayment;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrderAddress
     */
    protected $quoteAddressToOrderAddress;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $totalsCollector;

    /**
     * @var \Magento\Quote\Api\Data\CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var AllowedCountries
     */
    private $allowedCountryReader;

    /**
     * @var \Magento\Quote\Model\Quote\ShippingAssignment\ShippingAssignmentProcessor
     */
    private $shippingAssignmentProcessor;

    /**
     * @var Multishipping\PlaceOrderFactory
     */
    private $placeOrderFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Constructor
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\model\Session $customerSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Quote\Model\Quote\AddressFactory $addressFactory
     * @param \Magento\Quote\Model\Quote\Address\ToOrder $quoteAddressToOrder
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress
     * @param \Magento\Quote\Model\Quote\Payment\ToOrderPayment $quotePaymentToOrderPayment
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $quoteItemToOrderItem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Payment\Model\Method\SpecificationInterface $paymentSpecification
     * @param \Magento\Multishipping\Helper\Data $helper
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param DeliveryHelper $deliveryhelper
     * @param QuoteItemCollection $quoteItemCollection
     * @param LoggerInterface $logger
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping\PlaceOrderFactory $placeOrderFactory
     * @param \Magento\Multishipping\Block\Checkout\Shipping $shipping
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Session\Generic $session,
        \Magento\Quote\Model\Quote\AddressFactory $addressFactory,
        \Magento\Quote\Model\Quote\Address\ToOrder $quoteAddressToOrder,
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress,
        \Magento\Quote\Model\Quote\Payment\ToOrderPayment $quotePaymentToOrderPayment,
        \Magento\Quote\Model\Quote\Item\ToOrderItem $quoteItemToOrderItem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Payment\Model\Method\SpecificationInterface $paymentSpecification,
        \Magento\Multishipping\Helper\Data $helper,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        DeliveryHelper $deliveryhelper,
        QuoteItemCollection $quoteItemCollection,
        LoggerInterface $logger,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping\PlaceOrderFactory $placeOrderFactory,
        \Magento\Multishipping\Block\Checkout\Shipping $shipping,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        array $data = []
    ) {
        parent::__construct(
            $checkoutSession,
            $customerSession,
            $orderFactory,
            $addressRepository,
            $eventManager,
            $scopeConfig,
            $session,
            $addressFactory,
            $quoteAddressToOrder,
            $quoteAddressToOrderAddress,
            $quotePaymentToOrderPayment,
            $quoteItemToOrderItem,
            $storeManager,
            $paymentSpecification,
            $helper,
            $orderSender,
            $priceCurrency,
            $quoteRepository,
            $searchCriteriaBuilder,
            $filterBuilder,
            $totalsCollector,
            $data
        );
        $this->deliveryhelper = $deliveryhelper;
        $this->quoteItemCollection = $quoteItemCollection;
        $this->logger = $logger;
        $this->placeOrderFactory = $placeOrderFactory;
        $this->shipping = $shipping;
        $this->cart = $cart;
        $this->cookieManager = $cookieManager;
    }
    /**
     * Create Order function
     *
     * @return void
     */
    public function createOrders()
    {
        $orderIds = [];
        $this->_validate();
        $shippingAddresses = $this->getQuote()->getAllShippingAddresses();
        $orders = [];
        $isEnable = $this->deliveryhelper->getPluginEnable();
        $isdisplay = $this->deliveryhelper->getPluginDisplayAt();
        if ($isdisplay == 3) {
            $test =  $this->shipping;
            foreach ($test->getAddresses() as $_index => $_address) {
                foreach ($test->getAddressItems($_address) as $key => $_item) {
                    $quoteItemId[$key][] =  $_item->getQuoteItemId();
                }
            }
            $data = $this->quoteItemCollection->addFieldToFilter('item_id', ['in' => $quoteItemId]);
            foreach ($data as $key => $value) {
                $productIds[$key] = $value['product_id'];
            }
            foreach ($test->getAddresses() as $_index => $_address) {
                foreach ($test->getAddressItems($_address) as $key => $_item) {
                    if ($productIds[$_item->getQuoteItemId()]) {
                        if ($this->cookieManager
                            ->getCookie('delivery_date' . $productIds[$_item->getQuoteItemId()]) != null
                        ) {
                            $dDate = $this->cookieManager
                                ->getCookie('delivery_date' . $productIds[$_item->getQuoteItemId()]);
                            $date = str_replace('/', '-', $dDate);
                            $newDate[$_index][$productIds[$_item->getQuoteItemId()]]
                                = date('Y-m-d', strtotime($date));
                        }
                        if ($this->cookieManager
                            ->getCookie('delivery_timeslot' . $productIds[$_item->getQuoteItemId()]) != null
                        ) {
                            $dtime[$_index][$productIds[$_item->getQuoteItemId()]] =
                                $this->cookieManager
                                ->getCookie('delivery_timeslot' . $productIds[$_item->getQuoteItemId()]);
                        }
                        if ($this->cookieManager
                            ->getCookie('delivery_comment' . $productIds[$_item->getQuoteItemId()]) != null
                        ) {
                            $dCom[$_index][$productIds[$_item->getQuoteItemId()]]
                                = $this->cookieManager
                                ->getCookie('delivery_comment' . $productIds[$_item->getQuoteItemId()]);
                        }
                    }
                }
            }
            /*Logic for getting Date and add into json format*/
            $j = 0;
            $delivery_date_arr = [];
            $delivery_time_arr = [];
            $delivery_comm_arr = [];
            $delivery_date_arr = $this->getDeliveryDate($newDate);
            $delivery_time_arr = $this->getDeliveryTime($dtime);
            $j = 0;
            /*For the comment*/
            if (isset($dCom)) {
                $delivery_comm_arr = $this->getDeliveryCom($dCom);
            }
        }

        $orders = [];

        if ($this->getQuote()->hasVirtualItems()) {
            $shippingAddresses[] = $this->getQuote()->getBillingAddress();
        }

        try {
            $i = 1;
            $cart = $this->cart;
            $productInfo = $cart->getQuote()->getItemsCollection();
            $productIds = [];
            foreach ($productInfo as $item) {
                $productIds[] = $item->getProductId();
            }
            $this->getMultishippingAdderss(
                $shippingAddresses,
                $isEnable,
                $isdisplay,
                $delivery_date_arr,
                $delivery_time_arr,
                $delivery_comm_arr,
                $i
            );
            $paymentProviderCode = $this->getQuote()->getPayment()->getMethod();
            $placeOrderService = $this->placeOrderFactory->create($paymentProviderCode);
            $exceptionList = $placeOrderService->place($orders);
            $this->logExceptions($exceptionList);
            /** @var OrderInterface[] $failedOrders */
            $failedOrders = [];
            /** @var OrderInterface[] $successfulOrders */
            $successfulOrders = [];
            foreach ($orders as $order) {
                if (isset($exceptionList[$order->getIncrementId()])) {
                    $failedOrders[] = $order;
                } else {
                    $successfulOrders[] = $order;
                }
            }

            $placedAddressItems = [];
            foreach ($successfulOrders as $order) {
                $orderIds[$order->getId()] = $order->getIncrementId();
                if ($order->getCanSendNewEmailFlag()) {
                    $this->orderSender->send($order);
                }
                $placedAddressItems = $this->getPlacedAddressItems($order);
            }

            $addressErrors = [];
            if (!empty($failedOrders)) {
                $this->removePlacedItemsFromQuote($shippingAddresses, $placedAddressItems);
                $addressErrors = $this->getQuoteAddressErrors(
                    $failedOrders,
                    $shippingAddresses,
                    $exceptionList
                );
            } else {
                $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId());
                $this->getQuote()->setIsActive(false);
                $this->quoteRepository->save($this->getQuote());
            }

            $this->_session->setOrderIds($orderIds);
            $this->_session->setAddressErrors($addressErrors);
            $this->_eventManager->dispatch(
                'checkout_submit_all_after',
                ['orders' => $orders, 'quote' => $this->getQuote()]
            );

            return $this;
        } catch (\Exception $e) {
            $this->_eventManager->dispatch('checkout_multishipping_refund_all', ['orders' => $orders]);
            throw $e;
        }
    }
    /**
     * Logs exceptions.
     *
     * @param \Exception[] $exceptionList
     * @return void
     */
    private function logExceptions(array $exceptionList)
    {
        $logger = $this->logger;
        foreach ($exceptionList as $exception) {
            $logger->critical($exception);
        }
    }
    /**
     * Get Delivery Date function
     *
     * @param mixed $newDate
     * @return void
     */
    public function getDeliveryDate($newDate)
    {
        $j = 0;
        $delivery_date_arr = [];
        foreach ($newDate as $value) {
            /*Logic for Multipple addresses*/
            if (count($newDate) > 1) {
                if (count($value) > 1) {
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $data1['item'][] = $item;
                    }
                    foreach ($value as $val) {

                        $data1['date'][] = $val;
                    }
                    $count = 0;
                    if (isset($data1)) {
                        foreach ($data1 as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $data1['item'][$i];
                        $finalData[$i]['delivery_date'] = $data1['date'][$i];
                    }
                    $delivery_date_arr[$j] = json_encode($finalData);
                    unset($data1);
                    unset($finalData);
                    $j++;
                } else {
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $testData['item_id'][] = $item;
                    }
                    foreach ($value as $val) {

                        $testData['delivery_date'][] = $val;
                    }
                    $count = 0;
                    if (isset($testData)) {
                        foreach ($testData as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $testData['item_id'][$i];
                        $finalData[$i]['delivery_date'] = $testData['delivery_date'][$i];
                    }
                    $delivery_date_arr[$j] = json_encode($finalData);
                    $j++;
                    unset($finalData);
                    unset($testData);
                }
            } else {
                if (count($value) > 1) {
                    /*Logic for single addresses multiple items*/
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $data1['item'][] = $item;
                    }
                    foreach ($value as $val) {

                        $data1['date'][] = $val;
                    }
                    $count = 0;
                    if (isset($data1)) {
                        foreach ($data1 as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $data1['item'][$i];
                        $finalData[$i]['delivery_date'] = $data1['date'][$i];
                    }
                    $delivery_date_arr[] = json_encode($finalData);
                    unset($data1);
                    unset($finalData);
                } else {
                    $j = 0;
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $testData['item_id'][] = $item;
                    }
                    foreach ($value as $val) {

                        $testData['delivery_date'][] = $val;
                    }
                    $count = 0;
                    if (isset($testData)) {
                        foreach ($testData as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $testData['item_id'][$i];
                        $finalData[$i]['delivery_date'] = $testData['delivery_date'][$i];
                    }
                    $delivery_date_arr[$j] = json_encode($finalData);
                    $j++;
                    unset($finalData);
                }
            }
        }
        return $delivery_date_arr;
    }

    /**
     * Get Delivery Time function
     *
     * @param mixed $dtime
     * @return void
     */
    public function getDeliveryTime($dtime)
    {
        $j = 0;
        $delivery_time_arr = [];
        foreach ($dtime as $value) {
            /*Logic for Multipple addresses*/
            if (count($dtime) > 1) {
                if (count($value) > 1) {
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $data1['item'][] = $item;
                    }
                    foreach ($value as $val) {

                        $data1['time'][] = $val;
                    }
                    $count = 0;
                    if (isset($data1)) {
                        foreach ($data1 as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $data1['item'][$i];
                        $finalData[$i]['delivery_timeslot'] = $data1['time'][$i];
                    }
                    $delivery_time_arr[$j] = json_encode($finalData);
                    unset($data1);
                    unset($finalData);
                    $j++;
                } else {
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $testData['item_id'][] = $item;
                    }
                    foreach ($value as $val) {

                        $testData['delivery_timeslot'][] = $val;
                    }
                    $count = 0;
                    if (isset($testData)) {
                        foreach ($testData as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $testData['item_id'][$i];
                        $finalData[$i]['delivery_timeslot'] = $testData['delivery_timeslot'][$i];
                    }
                    $delivery_time_arr[$j] = json_encode($finalData);
                    $j++;
                    unset($finalData);
                    unset($testData);
                }
            } else {
                if (count($value) > 1) {
                    /*Logic for single addresses multiple items*/
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $data1['item'][] = $item;
                    }
                    foreach ($value as $val) {

                        $data1['time'][] = $val;
                    }
                    $count = 0;
                    if (isset($data1)) {
                        foreach ($data1 as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $data1['item'][$i];
                        $finalData[$i]['delivery_timeslot'] = $data1['time'][$i];
                    }
                    $delivery_time_arr[] = json_encode($finalData);
                    unset($data1);
                    unset($finalData);
                } else {
                    $j = 0;
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $testData['item_id'][] = $item;
                    }
                    foreach ($value as $val) {

                        $testData['delivery_timeslot'][] = $val;
                    }
                    $count = 0;
                    if (isset($testData)) {
                        foreach ($testData as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $testData['item_id'][$i];
                        $finalData[$i]['delivery_timeslot'] = $testData['delivery_timeslot'][$i];
                    }
                    $delivery_time_arr[$j] = json_encode($finalData);
                    $j++;
                    unset($finalData);
                }
            }
        }
        return $delivery_time_arr;
    }

    /**
     * Get Delivery Comment function
     *
     * @param mixed $dCom
     * @return void
     */
    public function getDeliveryCom($dCom)
    {
        $j = 0;
        $delivery_comm_arr = [];
        foreach ($dCom as $value) {
            /*Logic for Multipple addresses*/
            if (count($dCom) > 1) {
                if (count($value) > 1) {
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $data1['item'][] = $item;
                    }
                    foreach ($value as $val) {

                        $data1['com'][] = $val;
                    }
                    $count = 0;
                    if (isset($data1)) {
                        foreach ($data1 as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $data1['item'][$i];
                        $finalData[$i]['delivery_comment'] = $data1['com'][$i];
                    }
                    $delivery_comm_arr[$j] = json_encode($finalData);
                    unset($data1);
                    unset($finalData);
                    $j++;
                } else {
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $testData['item_id'][] = $item;
                    }
                    foreach ($value as $val) {

                        $testData['delivery_comment'][] = $val;
                    }
                    $count = 0;
                    if (isset($testData)) {
                        foreach ($testData as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $testData['item_id'][$i];
                        $finalData[$i]['delivery_comment'] = $testData['delivery_comment'][$i];
                    }
                    $delivery_comm_arr[$j] = json_encode($finalData);
                    $j++;
                    unset($finalData);
                    unset($testData);
                }
            } else {
                if (count($value) > 1) {
                    /*Logic for single addresses multiple items*/
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $data1['item'][] = $item;
                    }
                    foreach ($value as $val) {

                        $data1['com'][] = $val;
                    }
                    $count = 0;
                    if (isset($data1)) {
                        foreach ($data1 as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $data1['item'][$i];
                        $finalData[$i]['delivery_comment'] = $data1['com'][$i];
                    }
                    $delivery_comm_arr[] = json_encode($finalData);
                    unset($data1);
                    unset($finalData);
                } else {
                    $j = 0;
                    $items = array_keys($value);
                    foreach ($items as $item) {
                        $testData['item_id'][] = $item;
                    }
                    foreach ($value as $val) {

                        $testData['delivery_comment'][] = $val;
                    }
                    $count = 0;
                    if (isset($testData)) {
                        foreach ($testData as $key => $value) {
                            $count = count($value);
                        }
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $finalData[$i]['item_id'] = $testData['item_id'][$i];
                        $finalData[$i]['delivery_comment'] = $testData['delivery_comment'][$i];
                    }
                    $delivery_comm_arr[$j] = json_encode($finalData);
                    $j++;
                    unset($finalData);
                }
            }
        }
        return  $delivery_comm_arr;
    }
    /**
     * Get Multishipping function
     *
     * @param mixed $shippingAddresses
     * @param bool $isEnable
     * @param int $isdisplay
     * @param array $delivery_date_arr
     * @param array $delivery_time_arr
     * @param array $delivery_comm_arr
     * @param int $i
     * @return void
     */
    public function getMultishippingAdderss(
        $shippingAddresses,
        $isEnable,
        $isdisplay,
        $delivery_date_arr,
        $delivery_time_arr,
        $delivery_comm_arr,
        $i
    ) {
        foreach ($shippingAddresses as $key => $address) {
            $order = $this->_prepareOrder($address);
            $orders[] = $order;
            $this->_eventManager->dispatch(
                'checkout_type_multishipping_create_orders_single',
                ['order' => $order, 'address' => $address, 'quote' => $this->getQuote()]
            );
            if ($isEnable) {
                if ($isdisplay == 3) {
                    $orders[] = $order;
                    foreach ($orders as $value) {
                        $date = $delivery_date_arr[$key];
                        $time = $delivery_time_arr[$key];
                        $order->setDeliveryDate($date);
                        $order->setDeliveryTimeslot($time);
                        if (!empty($dCom)) {
                            $comment = $delivery_comm_arr[$key];
                            $order->setDeliveryComment($comment);
                        }
                    }
                } else {
                    $orders[] = $order;
                    foreach ($orders as $value) {
                        if ($this->cookieManager->getCookie('delivery_date' . $i) != null) {
                            $var = $this->cookieManager->getCookie('delivery_date' . $i);
                            $date = str_replace('/', '-', $var);
                            $newDate = date('Y-m-d', strtotime($date));
                            $order->setDeliveryDate($newDate);
                            $order->setDeliveryTimeslot($this->cookieManager->getCookie('delivery_timeslot' . $i));
                            $this->getComment($order, $i);
                        }
                    }
                }
            }
            $i++;
        }
    }

    /**
     * Get Comment function
     *
     * @param mixed $order
     * @param mixed $i
     * @return void
     */
    public function getComment($order, $i)
    {
        if (!empty($this->cookieManager->getCookie('delivery_comment' . $i))) {
            $order->setDeliveryComment($this->cookieManager
                ->getCookie('delivery_comment' . $i));
        }
    }
}
