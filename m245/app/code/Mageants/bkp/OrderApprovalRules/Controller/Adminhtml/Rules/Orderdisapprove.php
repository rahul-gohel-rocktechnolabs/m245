<?php

namespace Mageants\OrderApprovalRules\Controller\Adminhtml\Rules;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mageants\OrderApprovalRules\Model\ResourceModel\Pendingorder\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Mageants\OrderApprovalRules\Block\Frontend\OrderApprovalRules;
use Magento\Sales\Model\Order;

class Orderdisapprove extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.​_
     * @var Filter
     */
    protected $_filter;
 
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Timezone
     */
    protected $objDate;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderApprovalRules $orderApprovalRules
     * @param Order $order
     * @param TimeZone $objDate
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderApprovalRules $orderApprovalRules,
        Order $order,
        TimeZone $objDate
    ) {
        parent::__construct($context);
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->orderApprovalRules = $orderApprovalRules;
        $this->_order = $order;
        $this->objDate = $objDate;
    }

    /**
     * Set status for order disapprove and customer notified
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $date = $this->objDate->date();
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $recordApproved = 0;
        $status = 1;
        foreach ($collection as $record) {
            $orderCollection = $this->_order->load($record->getOrderId());
            $orderCollection->setState("canceled")->setStatus("orderdisapproved");
            $orderCollection->addStatusHistoryComment("Customer has been notified at ".$date->format('Y-m-d h:i:s'));
            $customerName = $orderCollection->getCustomerName();
            $customerEmail = $orderCollection->getCustomerEmail();
            $templateIdentifier = $this->orderApprovalRules->getOrderDisapprovedTemplate();
            $this->orderApprovalRules->sendMail($customerName, $customerEmail, $templateIdentifier);
            $orderCollection->save();
            $record->setStatus('orderdisapproved');
            $record->save();
            $recordApproved++;
        }
        $this->messageManager->addSuccess(__('The %1 Order(s) has been Disapproved.', $recordApproved));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
