<?php

namespace Mageants\OrderApprovalRules\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class AdditionalConfigProvider implements ConfigProviderInterface
{
    /**
     * Helper
     *
     * @var \Mageants\OrderApprovalRules\Helper\Data
     */
    protected $orderHelper;

    /**
     * @param \Mageants\OrderApprovalRules\Model\PendingorderFactory $pendingorder
     * @param \Mageants\OrderApprovalRules\Helper\Data $orderHelper
     */
    public function __construct(
        \Mageants\OrderApprovalRules\Model\PendingorderFactory $pendingorder,
        \Mageants\OrderApprovalRules\Helper\Data $orderHelper
    ) {
        $this->pendingorder = $pendingorder;
        $this->orderHelper = $orderHelper;
        $this->orderHelper->setCheckoutConfig();
    }

    /**
     * Get store config value
     *
     * @return array
     */
    public function getConfig()
    {
        $output['order_approval'] = 1;
        $output['message_for_buyers'] = $this->orderHelper->getMessageForBuyers();
        return $output;
    }
}
