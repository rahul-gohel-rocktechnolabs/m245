<?php

/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Controller\Payment;

use \Magento\Framework\App\Action\Context;
use \Magento\Checkout\Model\Session;
use \Mageants\ExtraFee\Helper\Data;
use \Magento\Framework\Stdlib\CookieManagerInterface;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Quote\Model\Quote\Address\Total;
use \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory;
use \Magento\Framework\Session\SessionManagerInterface;

class RemoveCodFee extends \Magento\Framework\App\Action\Action
{
    /**
     * @var $_pageFactory
     */
    protected $_pageFactory;
    /**
     * @var $total
     */
    protected $_total;
    /**
     * @var $cookieManager
     */
    protected $_cookieManager;
    /**
     * @var $_cookieMetadataFactory
     */
    protected $_cookieMetadataFactory;
    /**
     * @var $sessionManager
     */
    protected $sessionManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param Data $helperData
     * @param CookieManagerInterface $cookieManager
     * @param PageFactory $pageFactory
     * @param Total $total
     * @param PublicCookieMetadataFactory $cookieMetadataFactory
     * @param SessionManagerInterface $sessionManager
     */

    public function __construct(
        Context $context,
        Session $checkoutSession,
        Data $helperData,
        CookieManagerInterface $cookieManager,
        PageFactory $pageFactory,
        Total $total,
        PublicCookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_helperData = $helperData;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_total = $total;
        $this->sessionManager = $sessionManager;
        return parent::__construct($context);
    }

    /**
     * To remove the extra fee
     */
    public function execute()
    {
        $metadata = $this->_cookieMetadataFactory->create()
              ->setPath($this->sessionManager->getCookiePath())
              ->setDomain($this->sessionManager->getCookieDomain());
        $this->_cookieManager->deleteCookie('codFee');
        $total = $this->_total;
        $fee = 0;
        $total->setGrandTotal($total->getGrandTotal());
        $total->setBaseGrandTotal($total->getBaseGrandTotal());
        
        if ($this->_helperData->getExtrafee()) {
            $fee = $fee + $this->_helperData->getExtrafee();
        }
        $this->_checkoutSession->setFee($fee);
        $this->_checkoutSession->getQuote()->collectTotals()->save();

        return $this->_pageFactory->create();
    }
}
