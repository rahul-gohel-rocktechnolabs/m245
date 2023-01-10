<?php

namespace Mageants\GoogleCustomerReviews\Block;

use Magento\Sales\Model\Order;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Mageants\GoogleCustomerReviews\Helper\Data;

class Survey extends Template
{
    /** @var Mageants\GoogleCustomerReviews\Helper\Data */
    public $helper;

    /** @var OrderCollectionFactory */
    public $salesOrderCollectionFactory;

    public function __construct(
        Template\Context $context,
        Data $helper,
        OrderCollectionFactory $salesOrderCollectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @return array
     */
    public function getOrderData()
    {
        $orderData = [];

        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return $orderData;
        }

        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
        $collection = $this->salesOrderCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['in' => $orderIds]);

        if (!$collection->getSize()) {
            return $orderData;
        }

        foreach ($collection as $order) {
            /** @var Order $order */
            if (in_array($order->getCustomerGroupId(), $this->helper->getCustomerGroups())) {
                $orderData['email'] = $order->getCustomerEmail();
                $orderData['order_id'] = $order->getIncrementId();

                if ($order->getIsVirtual()) {
                    $address = $order->getBillingAddress();
                } else {
                    $address = $order->getShippingAddress();
                }
                $orderData['delivery_country'] = $address->getCountryId();
                $orderData['created'] = $order->getCreatedAt();
                
                foreach ($orderData as $item) {
                    if (empty($item)) {
                        throw new \Exception('Invalid value.');
                    }
                }   
            }   
            return $orderData;
        }
    }

    /**
     * @return int
     */
    public function getMerchantId()
    {
        return $this->helper->getMerchantId();
    }

    /**
     * @return string
     */
    public function getSurveyPosition()
    {
        return $this->helper->getSurveyPosition();
    }
    
    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->helper->getLanguage();
    }

    /**
     * @return int
     */
    public function showSuccessMsg($message)
    {
        $this->messageManager->addSuccess($message);
        return $this;
    }
}