<?php
/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutOrderSaveMultishipping implements ObserverInterface
{
    /**
     * @var order
     */
    public $order;
    /**
     * @var logger
     */
    protected $logger;
    /**
     * @var orderSave
     */
    public $orderSave;
    /**
     * @var helper
     */
    public $helper;
    /**
     * @var coreSession
     */
    public $coreSession;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface CookieManagerInterface
     */
    private $cookieManager;
 
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory CookieMetadataFactory
     */
    private $cookieMetadataFactory;
    
    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Mageants\DeliveryDate\Model\OrderSave $orderSave
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     */
    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Mageants\DeliveryDate\Model\OrderSave $orderSave,
        \Mageants\DeliveryDate\Helper\Data $helper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->order = $order;
        $this->orderSave = $orderSave;
        $this->helper = $helper;
        $this->coreSession = $coreSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Multishipping Save Observer function
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderid = $observer->getEvent()->getData('order_ids');
        $count = count($orderid);
        $dateFormat =$this->helper->getPluginDateFormat();
        $displayat = $this->helper->getPluginDisplayAt();
        for ($i=0; $i <$count; $i++) {

            $orderids = $orderid[$i];
            $this->orderSave->setOrderId($orderids);
            $this->orderSave->setConfigurationDisplayAt($displayat);
            $this->orderSave->setDateFormat($dateFormat);
            $this->orderSave->save();
            $this->orderSave->unsetData();
        }
        foreach ($orderid as $i) {
            if ($this->cookieManager->getCookie('delivery_date'.$i)) {
                $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
                $metadata->setPath('/');
     
                 $this->cookieManager->deleteCookie(
                     'delivery_date'.$i,
                     $metadata
                 );
            }

            if ($this->cookieManager->getCookie('delivery_timeslot'.$i)) {
                $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
                $metadata->setPath('/');
     
                 $this->cookieManager->deleteCookie(
                     'delivery_timeslot'.$i,
                     $metadata
                 );
            }
            if ($this->cookieManager->getCookie('delivery_comment'.$i)) {
                $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
                $metadata->setPath('/');
     
                 $this->cookieManager->deleteCookie(
                     'delivery_comment'.$i,
                     $metadata
                 );
            }
        }
    }
}
