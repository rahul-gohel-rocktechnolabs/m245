<?php

namespace Mageants\OrderApprovalRules\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory;

class Orderorigin extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Cart
     */
    protected $cart;
    
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var productRepository
     */
    protected $productRepository;

    /**
     * @var SessionManagerInterface
     */
    protected $coreSession;

    /**
     * @var CollectionFactory
     */
    protected $quote;

    /**
     * @param Context $context
     * @param FormKey $formKey
     * @param PageFactory $resultPageFactory
     * @param Order $order
     * @param Cart $cart
     * @param Session $checkoutSession
     * @param ProductRepositoryInterface $productRepository
     * @param SessionManagerInterface $coreSession
     * @param CollectionFactory $quote
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        PageFactory $resultPageFactory,
        Order $order,
        Cart $cart,
        Session $checkoutSession,
        ProductRepositoryInterface $productRepository,
        SessionManagerInterface $coreSession,
        CollectionFactory $quote
    ) {
        parent::__construct($context);
        $this->formKey = $formKey;
        $this->resultPageFactory = $resultPageFactory;
        $this->order = $order;
        $this->cart = $cart;
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->coreSession = $coreSession;
        $this->quote = $quote;
    }

    /**
     * Set the last order id
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $order =$this->order ;
        $cart = $this->cart ;
        $checkoutSession = $this->checkoutSession ;
        $quoteItems = $checkoutSession->getQuote()->getAllVisibleItems();
        foreach ($quoteItems as $item) {
            $cart->removeItem($item->getId());
            $cart->save();
        }
        $productRepository =$this->productRepository ;
        $order_id = $this->getRequest()->getParam('id');
        $coreSession =$this->coreSession;
        $quote = $this->quote->create();
        $order = $order->load($order_id);
        $orderQuoteId = $order->getQuoteId();
        $resultPage = $this->resultPageFactory->create();
        foreach ($order->getAllVisibleItems() as $item) {
            $_product = $productRepository->getById($item->getProductId());
            $superAttributes = $item->getProductOptions();
            $params = [
                'form_key' => $this->formKey->getFormKey(),
                'product' =>$item->getProductId(),
                'qty'   =>$item->getQtyOrdered(),
                'price' =>$item->getPrice(),
                'super_attribute' => $this->getSuperAttribute($superAttributes)
                
            ];
            $cart->addProduct($_product, $params);
            $cart->save();
        }
        foreach ($cart->getQuote()->getAllVisibleItems() as $product) {
            foreach ($quote->addFieldToFilter('quote_id', $product->getQuoteId())->load()->getItems() as $quoteData) {
                $quoteData->setData('reserved_order_id', $orderQuoteId);
                $quoteData->save();
            }
        }
        $coreSession->start();
        $coreSession->setLastOrder($orderQuoteId);
        $coreSession->setLastOrderId($order_id);

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
        ->setPath('checkout', ['_fragment' => 'payment']);
    }
    /**
     * Get the supper attribute
     *
     * @param array $superAttributes
     */
    public function getSuperAttribute($superAttributes)
    {
        foreach ($superAttributes as $value) {
            if (isset($value['super_attribute'])) {
                return $value['super_attribute'];
            }
        }
    }
}
