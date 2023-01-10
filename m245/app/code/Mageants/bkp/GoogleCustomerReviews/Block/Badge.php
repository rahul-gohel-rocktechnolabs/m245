<?php

namespace Mageants\GoogleCustomerReviews\Block;

use Magento\Framework\View\Element\Template;

class Badge extends Template
{
    public $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageants\GoogleCustomerReviews\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @return boolean
     */
    public function isBadge()
    {
        return $this->helper->isBadge();
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
    public function getPosition()
    {
        return $this->helper->getBadgePosition();
    }

    public function getLanguage()
    {
        return $this->helper->getLanguage();
    }

}