<?php

namespace Mageants\GoogleCustomerReviews\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;

class SetOrderToSurveyObserver implements ObserverInterface
{
    /** @var LayoutInterface  */
    public $layout;

    public function __construct(LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $block = $this->layout->getBlock('mageants.google_customer_reviews.survey');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
}
