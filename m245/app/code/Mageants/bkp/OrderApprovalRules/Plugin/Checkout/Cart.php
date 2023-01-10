<?php
namespace Mageants\OrderApprovalRules\Plugin\Checkout;

use Magento\Framework\Exception\LocalizedException;

class Cart
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Plugin constructor
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Mageants\OrderApprovalRules\Block\Frontend\OrderApprovalRules $orderApprovalRules
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Mageants\OrderApprovalRules\Block\Frontend\OrderApprovalRules $orderApprovalRules,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->quote = $checkoutSession->getQuote();
        $this->orderApprovalRules = $orderApprovalRules;
        $this->messageManager = $messageManager;
    }

    /**
     * BeforeAddProduct
     *
     * @param string $subject
     * @param array $productInfo
     * @param array $requestInfo
     *
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        if ($this->orderApprovalRules->getOrderId()) {
            throw new LocalizedException(__('Could not add Product to Cart Because Complete Your Order First'));
        }

        return [$productInfo, $requestInfo];
    }
}
