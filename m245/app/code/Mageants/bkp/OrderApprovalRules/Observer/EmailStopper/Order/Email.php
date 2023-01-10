<?php

 namespace Mageants\OrderApprovalRules\Observer\EmailStopper\Order;

class Email implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Mageants\OrderApprovalRules\Block\Frontend\OrderApprovalRules $orderApprovalRules
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Mageants\OrderApprovalRules\Block\Frontend\OrderApprovalRules $orderApprovalRules
    ) {
        $this->messageManager = $messageManager;
        $this->orderApprovalRules = $orderApprovalRules;
    }

    /**
     * Check the payment method set or not
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $this->_current_order = $order;
            $payment = $order->getPayment()->getMethodInstance()->getCode();
            if ($payment == 'orderapproval') {
                $this->stopNewOrderEmail($order);
            } elseif ($this->orderApprovalRules->getOrderId()) {
                $this->stopNewOrderEmail($order);
            }
        } catch (\ErrorException $e) {
            $this->messageManager->addError($e);
        }
    }

    /**
     * Stop the new order email if different payment method
     *
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    public function stopNewOrderEmail(\Magento\Sales\Model\Order $order)
    {
        try {
            $order->setCanSendNewEmailFlag(false);
            $order->setSendEmail(false);
            $order->save();
        } catch (\ErrorException $e) {
            $this->messageManager->addError($e);
        }
    }
}
