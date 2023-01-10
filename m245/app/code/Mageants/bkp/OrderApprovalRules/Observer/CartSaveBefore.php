<?php
namespace Mageants\OrderApprovalRules\Observer;

use Magento\Framework\Event\ObserverInterface;

class CartSaveBefore implements ObserverInterface
{
    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Mageants\OrderApprovalRules\Block\Frontend\OrderApprovalRules $orderApprovalRules
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Mageants\OrderApprovalRules\Block\Frontend\OrderApprovalRules $orderApprovalRules
    ) {
        $this->quote = $checkoutSession->getQuote();
        $this->orderApprovalRules = $orderApprovalRules;
    }
    
    /**
     * Get the all visible items for if empty then remove session
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $cartProduct = $this->quote->getAllVisibleItems();
        if (empty($cartProduct)) {
            $this->orderApprovalRules->removeSession();
        }
    }
}
