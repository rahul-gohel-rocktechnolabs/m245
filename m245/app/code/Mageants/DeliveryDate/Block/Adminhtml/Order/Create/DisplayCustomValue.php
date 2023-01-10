<?php
namespace Mageants\DeliveryDate\Block\Adminhtml\Order\Create;

use Magento\Sales\Api\Data\OrderInterface;

class DisplayCustomValue extends \Magento\Backend\Block\Template
{
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param OrderInterface $orderInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        OrderInterface $orderInterface,
        array $data = []
    ) {
        $this->orderInterface = $orderInterface;
        parent::__construct($context, $data);
    }
    /**
     * Customer Comment function
     *
     * @return string
     */
    public function getCustomerComment()
    {
        return " ";
    }
}
