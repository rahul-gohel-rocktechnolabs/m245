<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Controller\Cart;

use \Magento\Framework\App\Action\Context;
use \Magento\Checkout\Model\Session;
use \Mageants\ExtraFee\Helper\Data;
use \Magento\Framework\Stdlib\CookieManagerInterface;
use \Magento\Framework\View\Result\PageFactory;

class Apply extends \Magento\Framework\App\Action\Action
{
    /**
     * Use PageFactory for page data
     *
     * @var $_pageFactory;
     */
    protected $_pageFactory;

    /**
     * Constuctor
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param Data $helperData
     * @param CookieManagerInterface $cookieManager
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Data $helperData,
        CookieManagerInterface $cookieManager,
        PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_helperData = $helperData;
        $this->_cookieManager = $cookieManager;
        return parent::__construct($context);
    }
    
    /**
     * Get the chance to get extra data
     */
    public function execute()
    {
        
        $orderFee=$this->_cookieManager->getCookie("orderExtrafeeAmount");
        $fee = 0;
        if ($orderFee != "") {
            $fee = $fee + $orderFee;
        }
        if ($this->_helperData->getExtrafee()) {
            $fee = $fee + $this->_helperData->getExtrafee();
        }
        $this->_checkoutSession->setFee($fee);
        $this->_checkoutSession->getQuote()->collectTotals()->save();

        return $this->_pageFactory->create();
    }
}
