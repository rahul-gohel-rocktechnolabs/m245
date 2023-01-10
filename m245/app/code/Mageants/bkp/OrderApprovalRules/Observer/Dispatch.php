<?php

namespace Mageants\OrderApprovalRules\Observer;

use Magento\Framework\Event\ObserverInterface;

class Dispatch implements ObserverInterface
{
    /**
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $shippingInformation
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Mageants\OrderApprovalRules\Helper\Data $helperData
     * @param \Magento\Framework\Url $url
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\App\Response\Http $http
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \Magento\Quote\Api\Data\AddressInterface $address,
        \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $shippingInformation,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Mageants\OrderApprovalRules\Helper\Data $helperData,
        \Magento\Framework\Url $url,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\Response\Http $http,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->session = $session;
        $this->address = $address;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->shippingInformation = $shippingInformation;
        $this->quoteRepository = $quoteRepository;
        $this->helperData = $helperData;
        $this->url = $url;
        $this->messageManager = $messageManager;
        $this->http = $http;
        $this->coreSession = $coreSession;
    }

    /**
     * Save the shipping information
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->session->getQuote()) {
            $cartId = $this->session->getQuote()->getId();
            if ($cartId) {
                $order_id = $this->coreSession->getLastOrderId();
                if (isset($order_id)) {
                    $this->helperData->saveShippingInformation();
                }
            }
        }
    }
}
