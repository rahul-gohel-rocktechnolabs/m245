<?php

namespace Mageants\OrderApprovalRules\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface $order
     */
    protected $order;

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Mageants\OrderApprovalRules\Model\PendingorderFactory $pendingorder
     * @param \Magento\Sales\Model\Order $orderModel
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $datetime
     * @param \Mageants\OrderApprovalRules\Block\Frontend\OrderApprovalRules $orderApprovalRules
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     */

    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Mageants\OrderApprovalRules\Model\PendingorderFactory $pendingorder,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Framework\Stdlib\DateTime\Timezone $datetime,
        \Mageants\OrderApprovalRules\Block\Frontend\OrderApprovalRules $orderApprovalRules,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    ) {
        $this->_order = $order;
        $this->_orderModel = $orderModel;
        $this->_datetime = $datetime;
        $this->orderRepository = $orderRepository;
        $this->_pendingOrder = $pendingorder;
        $this->orderApprovalRules = $orderApprovalRules;
        $this->registry = $registry;
        $this->coreSession = $coreSession;
        $this->orderSender = $orderSender;
    }

    /**
     * Set the orders data
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $dateTime = $this->_datetime->date()->format('Y-m-d h:i:s');
        $orderIds = $observer->getEvent()->getOrderIds();
        $data = [];
        $productIds = [];
        $counter = 0;
        foreach ($orderIds as $orderId) {
            $order = $this->_order->load($orderId);
            $orderIncId = $this->orderRepository->get($orderId);
            if ($this->orderApprovalRules->getOrderId() && !($this->coreSession->getExtraProduct())) {
                $paymentMethod = $order->getPayment()->getMethod();
                $orderCollection = $this->_orderModel->load($this->orderApprovalRules->getOrderId());
                $orderCollection->setState("pending");
                $orderCollection->setStatus("pending");
                $orderCollection->addStatusHistoryComment("Customer has been notified at ".$dateTime);
                $orderCollection->save();
                $this->orderApprovalRules->deleteSequenceIds($orderId);
                $this->registry->register('isSecureArea', true);
                $orderIncId->delete();
                $this->registry->unregister('isSecureArea');
                $order = $this->_order->load($this->orderApprovalRules->getOrderId());
                $order->getPayment()->setMethod($paymentMethod);
                $order->save();
                unset($order);
                $pendingOrder = $this->_pendingOrder->create()
                            ->getCollection()->load()
                            ->addFieldToFilter('order_id', $this->orderApprovalRules->getOrderId())->getData();
                $this->_pendingOrder->create()->load($pendingOrder[0]['id'])->delete();
                unset($order);
                $order = $this->_order->load($this->orderApprovalRules->getOrderId());
                $order->setCanSendNewEmailFlag(true);
                $order->setSendEmail(true);
                $order->save();
                $this->orderSender->send($order, true);
            } else {
                foreach ($order->getAllVisibleItems() as $item) {
                    $productIds[$counter] = $item->getProductId();
                    $counter++;
                }
                $isApproval = $this->orderApprovalRules->getComputedValuesAfterOrderPlace($productIds);
                if ($isApproval) {
                    $orderCollection = $this->_orderModel->load($orderId);
                    $orderCollection->setState("pending");
                    $orderCollection->setStatus("orderapprovalpending");
                    $orderCollection->save();
                    $orderIncId = $this->orderRepository->get($orderId);
                    $data['order_id'] = $orderId;
                    $data['increment_id'] = $orderIncId->getIncrementId();
                    $data['store_id'] = $order->getStoreId();
                    $data['purchase_date'] = $dateTime;
                    $data['bill_to_name'] = $order->getBillingAddress()
                    ->getFirstName()." ".$order->getBillingAddress()->getLastName();
                    $data['ship_to_name'] = $order->getShippingAddress()
                    ->getFirstName()." ".$order->getShippingAddress()->getLastName();
                    $data['grand_total_base'] = $order->getBaseGrandTotal();
                    $data['grand_total_purchased'] = $order->getGrandTotal();
                    $data['status'] = $order->getStatus();
                    $Model = $this->_pendingOrder->create();
                    $Model->setData($data);
                    $Model->save();
                    $customerName = $order->getCustomerName();
                    $customerEmail = $order->getCustomerEmail();
                    $templateIdentifier = $this->orderApprovalRules->getOrderApprovalPendingTemplate();
                    $this->orderApprovalRules->sendMail($customerName, $customerEmail, $templateIdentifier);
                    $customerName = "Admin";
                    $customerEmail = $this->orderApprovalRules->getAdminEmail();
                    $templateIdentifier = $this->orderApprovalRules->getOrderApprovalAdminTemplate();
                    $this->orderApprovalRules->sendMail($customerName, $customerEmail, $templateIdentifier);
                }
            }
        }
    }
}
