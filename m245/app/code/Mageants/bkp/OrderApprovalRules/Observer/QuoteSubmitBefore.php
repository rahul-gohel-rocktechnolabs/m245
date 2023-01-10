<?php
namespace Mageants\OrderApprovalRules\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\PaymentException;
use Magento\Framework\DataObject;

class QuoteSubmitBefore implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Checkout\Model\Session $checkoutsession
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $checkoutsession,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutsession = $checkoutsession;
        $this->coreSession = $coreSession;
    }

    /**
     * Get Data for quote submit
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $result = new DataObject();
    }
}
