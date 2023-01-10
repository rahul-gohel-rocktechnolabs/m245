<?php
namespace Mageants\OrderApprovalRules\Block\Frontend;

class OrderApprovalRules extends \Magento\Framework\View\Element\Template
{
    /**
     * OrderApprovalRules block constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Checkout\Model\Session $cart
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Mageants\OrderApprovalRules\Model\OrderApprovalRulesFactory $orderApprovalRulesFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteFactory
     * @param \Magento\Sales\Model\Order $order
     * @param \Mageants\OrderApprovalRules\Model\PendingorderFactory $pendingorder
     * @param \Mageants\OrderApprovalRules\Helper\Data $orderApprovalHelper
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Api\Data\OrderInterface $orderInterface
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Sales\Helper\Reorder $salesReorder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Checkout\Model\Session $cart,
        \Magento\Catalog\Model\Category $category,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Mageants\OrderApprovalRules\Model\OrderApprovalRulesFactory  $orderApprovalRulesFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteFactory,
        \Magento\Sales\Model\Order $order,
        \Mageants\OrderApprovalRules\Model\PendingorderFactory $pendingorder,
        \Mageants\OrderApprovalRules\Helper\Data $orderApprovalHelper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Sales\Helper\Reorder $salesReorder,
        array $data = []
    ) {
        $this->cart = $cart;
        $this->category = $category;
        $this->productRepository = $productRepository;
        $this->orderApprovalRulesFactory = $orderApprovalRulesFactory;
        $this->coreSession = $coreSession;
        $this->quoteFactory = $quoteFactory;
        $this->order = $order;
        $this->_pendingorder = $pendingorder;
        $this->orderApprovalHelper=$orderApprovalHelper;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->registry = $registry;
        $this->resourceConnection = $resourceConnection;
        $this->orderInterface = $orderInterface;
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
        $this->productMetadataInterface = $productMetadataInterface;
        $this->messageManager = $messageManager;
        $this->postDataHelper = $postDataHelper;
        $this->_salesReorder = $salesReorder;
        parent::__construct($context, $data);
    }

    /**
     * Delete SequenceIds
     *
     * @param varchar $orderid
     * @return varchar
     */
    public function deleteSequenceIds($orderid)
    {
        $order = $this->orderInterface->load($orderid);
        $orderIncId = $this->orderRepository->get($orderid);
        if ($this->storeManager->getStore()->getId() != 0 || $this->storeManager->getStore()->getId() != 1) {
            $sequenceValue = str_split($orderIncId->getIncrementId(), '1');
            $sequenceId = '';
            foreach ($sequenceValue as $key => $value) {
                if ($key != 0) {
                    $sequenceId .= $value;
                }
            }
        } else {
            $sequenceId = $orderIncId->getIncrementId();
        }
        $this->registry->register('isSecureArea', true);
        $connection = $this->resourceConnection->getConnection();
        $tblname = 'sequence_order_'.$order->getStoreId();
        $tableName = $this->resourceConnection->getTableName($tblname);
        $connection->delete($tableName, "sequence_value = '$sequenceId'");
        $sql = "ALTER TABLE ".$tableName." AUTO_INCREMENT = ".$sequenceId;
        $connection->query($sql);
        $this->registry->unregister('isSecureArea');
    }

    /**
     * Get cart products
     *
     * @return void
     */
    public function getCartProductIds()
    {
        $cartProduct = $this->cart->getQuote()->getAllItems();
        $cartProductIds = [];
        $incrementer = 0;
        foreach ($cartProduct as $product) {
            $cartProductIds[$incrementer] = $product->getProduct()->getId();
            $incrementer++;
        }
        return $cartProductIds;
    }

    /**
     * Get category ids
     *
     * @return void
     */
    public function getCategoryIds()
    {
        $categoryIds = [];
        $incrementer = 0;
        foreach ($this->getCartProductIds() as $productId) {
            $categoryIds[$incrementer] = $this->productRepository->getById($productId)->getCategoryIds();
            $incrementer++;
        }
        return $categoryIds;
    }

    /**
     * Get which category allowed
     *
     * @return void
     */
    public function getAllowedCategoryForApproval()
    {
        return $this->orderApprovalRulesFactory->create()->getCollection()->addFieldToFilter(
            'orderstatus',
            ['eq' => 0]
        )->addFieldToSelect('category_ids')->load()->getData();
    }

    /**
     * Get Which product allowed
     *
     * @return void
     */
    public function getAllowedProductForApproval()
    {
        return $this->orderApprovalRulesFactory->create()->getCollection()->addFieldToFilter(
            'orderstatus',
            ['eq' => 0]
        )->addFieldToSelect('product_ids')->load()->getData();
    }

    /**
     * Get Which country allowed
     *
     * @return void
     */
    public function getAllowedCountryForApproval()
    {
        return $this->orderApprovalRulesFactory->create()->getCollection()->addFieldToFilter(
            'orderstatus',
            ['eq' => 0]
        )->addFieldToSelect('country_ids')->load()->getData();
    }
    
    /**
     * Get Computed values
     *
     * @return void
     */
    public function getComputedValues()
    {
        $checkExtraProduct = 0;
        if ($this->isExtensionEnable()) {
            if ($this->getConditionalOrderApprovalCheck()) {
                $isOrderidAvailable = $this->coreSession->getLastOrder();
                $categoryIds  = $this->getCategoryIds();
                $allowedCategoryId = $this->getAllowedCategoryForApproval();
                $productIds  = $this->getCartProductIds();
                $allowedProductId = $this->getAllowedProductForApproval();
                $orderQuoteId = $this->coreSession->getLastOrder();
                $cartProduct = $this->cart->getQuote()->getAllItems();
                if (isset($orderQuoteId)) {
                    foreach ($cartProduct as $product) {
                        $quoteData = $this->quoteFactory->create()
                        ->addFieldToFilter('product_id', $product->getProduct()->getId())
                        ->addFieldToFilter('quote_id', $product->getQuoteId())->load()->getData();
                        if (!empty($quoteData)) {
                            foreach ($quoteData as $getReserveOrderIdArray) {
                                $getReservedOrderId = $getReserveOrderIdArray['reserved_order_id'];
                            }
                        } else {
                            $getReservedOrderId = '';
                        }
                        if (empty($getReservedOrderId)) {
                            $getReservedOrderId =  0;
                        }
                        if (empty($orderQuoteId)) {
                            $orderQuoteId = 1;
                        }
                        if ($getReservedOrderId != $orderQuoteId) {
                            if (!empty($allowedCategoryId) && !empty($categoryIds)) {
                                foreach ($allowedCategoryId as $allowedCategoryIds) {
                                    $explodedCategoryIds =  explode(",", $allowedCategoryIds['category_ids']?? '');
                                    foreach ($explodedCategoryIds as $CheckCategoryId) {
                                        foreach ($categoryIds as $categoryId) {
                                            foreach ($categoryId as $singleCategoryId) {
                                                if (!empty($CheckCategoryId)) {
                                                    if ($CheckCategoryId == $singleCategoryId) {
                                                        $checkExtraProduct = 1;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (!empty($allowedProductId) && !empty($productIds)) {
                                foreach ($allowedProductId as $allowedProductIds) {
                                    $explodedProductIds =  explode("&", $allowedProductIds['product_ids'] ?? '');
                                    foreach ($explodedProductIds as $CheckProductId) {
                                        foreach ($productIds as $productId) {
                                            if (!empty($CheckProductId)) {
                                                if ($CheckProductId == $productId) {
                                                    $checkExtraProduct = 1;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($checkExtraProduct) {
                        $this->coreSession->setExtraProduct(1);
                        return true;
                    } else {
                        $this->coreSession->unsExtraProduct();
                        return false;
                    }
                } else {
                    foreach ($cartProduct as $product) {
                        $quoteData = $this->quoteFactory->create()
                        ->addFieldToFilter('product_id', $product->getProduct()->getId())
                        ->addFieldToFilter('quote_id', $product->getQuoteId())->load()->getData();
                        if (!empty($quoteData)) {
                            foreach ($quoteData as $getReserveOrderIdArray) {
                                $getReservedOrderId = $getReserveOrderIdArray['reserved_order_id'];
                            }
                        } else {
                            $getReservedOrderId = '';
                        }
                        if (empty($getReservedOrderId)) {
                            $getReservedOrderId =  0;
                        }
                        if (empty($orderQuoteId)) {
                            $orderQuoteId = 1;
                        }
                        if ($getReservedOrderId != $orderQuoteId) {
                            if (!empty($allowedCategoryId) && !empty($categoryIds)) {
                                foreach ($allowedCategoryId as $allowedCategoryIds) {
                                    $explodedCategoryIds =  explode(",", $allowedCategoryIds['category_ids']);
                                    foreach ($explodedCategoryIds as $CheckCategoryId) {
                                        foreach ($categoryIds as $categoryId) {
                                            foreach ($categoryId as $singleCategoryId) {
                                                if (!empty($CheckCategoryId)) {
                                                    if ($CheckCategoryId == $singleCategoryId) {
                                                        $checkExtraProduct = 1;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (!empty($allowedProductId) && !empty($productIds)) {
                                foreach ($allowedProductId as $allowedProductIds) {
                                    $explodedProductIds =  explode("&", $allowedProductIds['product_ids']?? '');
                                    foreach ($explodedProductIds as $CheckProductId) {
                                        foreach ($productIds as $productId) {
                                            if (!empty($CheckProductId)) {
                                                if ($CheckProductId == $productId) {
                                                    $checkExtraProduct = 1;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($checkExtraProduct) {
                        $this->coreSession->setExtraProduct(1);
                        return true;
                    } else {
                        $this->coreSession->unsExtraProduct();
                        return false;
                    }
                }
            } else {
                $orderQuoteId = $this->coreSession->getLastOrder();
                $cartProduct = $this->cart->getQuote()->getAllItems();
                $checkExtraProduct = 0;
                if (isset($orderQuoteId)) {
                    foreach ($cartProduct as $product) {
                        $quoteData = $this->quoteFactory->create()
                        ->addFieldToFilter('product_id', $product->getProduct()->getId())
                        ->addFieldToFilter('quote_id', $product->getQuoteId())->load()->getData();
                        if (!empty($quoteData)) {
                            foreach ($quoteData as $getReserveOrderIdArray) {
                                $getReservedOrderId = $getReserveOrderIdArray['reserved_order_id'];
                            }
                        } else {
                            $getReservedOrderId = '';
                        }
                        if (empty($getReservedOrderId)) {
                            $getReservedOrderId =  0;
                        }
                        if (empty($orderQuoteId)) {
                            $orderQuoteId = 1;
                        }
                        if ($getReservedOrderId != $orderQuoteId) {
                            $checkExtraProduct = 1;
                        }
                    }
                    if ($checkExtraProduct) {
                        $this->coreSession->setExtraProduct(1);
                        return true;
                    } else {
                        $this->coreSession->unsExtraProduct();
                        return false;
                    }
                } else {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Get computed value after order place
     *
     * @param array $productIds
     * @return void
     */
    public function getComputedValuesAfterOrderPlace(array $productIds)
    {
        if ($this->isExtensionEnable()) {
            if ($this->getConditionalOrderApprovalCheck()) {
                $categoryIds = [];
                $incrementer = 0;
                foreach ($productIds as $productId) {
                    $categoryIds[$incrementer] = $this->productRepository->getById($productId)->getCategoryIds();
                    $incrementer++;
                }
                $allowedCategoryId = $this->getAllowedCategoryForApproval();
                $allowedProductId = $this->getAllowedProductForApproval();
                if (!empty($allowedCategoryId)) {
                    foreach ($allowedCategoryId as $allowedCategoryIds) {
                        $explodedCategoryIds =  explode(",", $allowedCategoryIds['category_ids']);
                        foreach ($explodedCategoryIds as $CheckCategoryId) {
                            foreach ($categoryIds as $categoryId) {
                                foreach ($categoryId as $singleCategoryId) {
                                    if (!empty($CheckCategoryId)) {
                                        if ($CheckCategoryId == $singleCategoryId) {
                                            return true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($allowedProductId)) {
                    foreach ($allowedProductId as $allowedProductIds) {
                        $explodedProductIds =  explode("&", $allowedProductIds['product_ids']);
                        foreach ($explodedProductIds as $CheckProductId) {
                            foreach ($productIds as $productId) {
                                if (!empty($CheckProductId)) {
                                    if ($CheckProductId == $productId) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($allowedProductId)) {
                    return;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Get checkout url
     *
     * @param varchar $orderId
     * @return void
     */
    public function getCheckoutUrl($orderId)
    {
        return $this->getUrl("orderapprovalrules/index/initiate", ['id'=>$orderId]);
    }

    /**
     * Get order id
     *
     * @return void
     */
    public function getOrderId()
    {
        return $this->coreSession->getLastOrderId();
    }

    /**
     * Get order increment id
     *
     * @return void
     */
    public function getOrderIncrementId()
    {
        if ($this->getOrderId()) {
            $order = $this->order->load($this->getOrderId());
            return $order->getIncrementId();
        } else {
            return false;
        }
    }

    /**
     * Get View order url
     *
     * @return void
     */
    public function getViewOrderUrl()
    {
        return $this->getUrl('sales/order/view', ['order_id'=>$this->getOrderId()]);
    }

    /**
     * Remove session
     *
     * @return void
     */
    public function removeSession()
    {
        $this->coreSession->unsLastOrder();
        $this->coreSession->unsLastOrderId();
    }

    /**
     * Get the Module enable or disable
     *
     * @return boolean
     */
    public function isExtensionEnable()
    {
        return $this->orderApprovalHelper->isExtensionEnable();
    }

    /**
     * Get the admin email
     *
     * @return void
     */
    public function getAdminEmail()
    {
        return $this->orderApprovalHelper->getAdminEmail();
    }

    /**
     * Get order approval check
     *
     * @return void
     */
    public function getConditionalOrderApprovalCheck()
    {
        return $this->orderApprovalHelper->getConditionalOrderApprovalCheck();
    }

    /**
     * Get order approval pending template
     *
     * @return void
     */
    public function getOrderApprovalPendingTemplate()
    {
        return $this->orderApprovalHelper->getOrderApprovalPendingTemplate();
    }

    /**
     * Get order approval template
     *
     * @return void
     */
    public function getOrderApprovedTemplate()
    {
        return $this->orderApprovalHelper->getOrderApprovedTemplate();
    }

    /**
     * Get disapprove template
     *
     * @return void
     */
    public function getOrderDisapprovedTemplate()
    {
        return $this->orderApprovalHelper->getOrderDisapprovedTemplate();
    }

    /**
     * Get order approval admin template
     *
     * @return void
     */
    public function getOrderApprovalAdminTemplate()
    {
        return $this->orderApprovalHelper->getOrderApprovalAdminTemplate();
    }

    /**
     * Send mail
     *
     * @param varchar $customername
     * @param varchar $customeremail
     * @param varchar $templateIdentifier
     * @return void
     */
    public function sendMail($customername, $customeremail, $templateIdentifier)
    {
        try {
            $this->inlineTranslation->suspend();
            $error = false;
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder
                        ->setTemplateIdentifier($templateIdentifier)
                        ->setTemplateOptions(
                            [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                            ]
                        )
                         ->setTemplateVars([
                        'customername' => $customername
                         ])
                        ->setFrom('general')
                        ->addTo($customeremail)
                        ->getTransport();
            if ($transport->sendMessage()) {
                throw new \Magento\Framework\Exception\LocalizedException();
            }
            $this->inlineTranslation->resume();
        } catch (Exception $e) {
            $this->messageManager->addError($this->orderApprovalHelper->getMessageIfMailNotSend());
        }
    }

    /**
     * Check version
     *
     * @return void
     */
    public function checkVersion()
    {
        return $this->productMetadataInterface->getVersion();
    }
    /**
     * Post Data Helper
     */
    public function postDataHelper()
    {
        return  $this->postDataHelper;
    }
    /**
     * Sales Reorder
     */
    public function salesReorder()
    {

        return$this->_salesReorder;
    }
}
