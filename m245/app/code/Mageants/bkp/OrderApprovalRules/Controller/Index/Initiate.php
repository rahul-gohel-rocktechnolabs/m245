<?php

namespace Mageants\OrderApprovalRules\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;

class Initiate extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var session
     */
    protected $checkoutSession;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Cart $cart
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Cart $cart,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->cart = $cart;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Remove item from cart
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('id');
        $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
        foreach ($quoteItems as $item) {
            $this->cart->removeItem($item->getId());
        }
        $this->cart->save();
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
        ->setPath('orderapprovalrules/index/orderorigin', ['id'=>$orderId]);
    }
}
