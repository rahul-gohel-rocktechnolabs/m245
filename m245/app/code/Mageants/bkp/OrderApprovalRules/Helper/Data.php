<?php
/**
 * @category Mageants OrderApprovalRules
 * @package Mageants_OrderApprovalRules
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants <support@mageants.com>
 */

namespace Mageants\OrderApprovalRules\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;
        
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Directory\Model\Config\Source\Country $countryFactory
     */
    protected $countryFactory;
    
    /**
     * @var \Mageants\OrderApprovalRules\Model\Source\PaymentMethods $paymentMethods
     */
    protected $paymentMethods;

    /**
     * Uses all class for get the data
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageants\OrderApprovalRules\Model\OrderApprovalRules $ruleCollection
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Mageants\OrderApprovalRules\Model\Source\CategoryList $categoryList
     * @param \Mageants\OrderApprovalRules\Model\Source\Status $status
     * @param \Magento\Directory\Model\Config\Source\Country $countryFactory
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $shippingInformation
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Sales\Model\Order $order
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageants\OrderApprovalRules\Model\OrderApprovalRules $ruleCollection,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Mageants\OrderApprovalRules\Model\Source\CategoryList $categoryList,
        \Mageants\OrderApprovalRules\Model\Source\Status $status,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Checkout\Model\Session $session,
        \Magento\Quote\Api\Data\AddressInterface $address,
        \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $shippingInformation,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Sales\Model\Order $order
    ) {
        parent::__construct($context);
        $this->_collection = $ruleCollection;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $context->getScopeConfig();
        $this->allCategoryList=$categoryList;
        $this->_productRepository = $productRepository;
        $this->countryFactory = $countryFactory;
        $this->_backendUrl = $backendUrl;
        $this->status = $status;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->session = $session;
        $this->address = $address;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->shippingInformation = $shippingInformation;
        $this->messageManager = $messageManager;
        $this->regionFactory = $regionFactory;
        $this->coreSession = $coreSession;
        $this->customerSession = $customerSession;
        $this->cart = $cart;
        $this->order = $order;
    }
    /**
     * Save the shipping information
     *
     * @return void
     */
    public function saveShippingInformation()
    {
        if ($this->session->getQuote()) {
            $cartId = $this->session->getQuote()->getId();
            $cartSkuArray = $this->getCartItemsSkus();
            if ($cartSkuArray) {
                $shippingAddress = $this->getShippingAddressInformation();
                return $this->shippingInformationManagement->saveAddressInformation($cartId, $shippingAddress);
            }
        }
    }
    
    /**
     * Get the shipping address information
     *
     * @return void
     */
    public function getShippingAddressInformation()
    {
        $cartSkuArray = $this->getCartItemsSkus();
        $collectionPointResponse = $this->getCollectionPointAddress($cartSkuArray);
        $shippingAddress = $this->prepareShippingAddress($collectionPointResponse);
        $address = $this->shippingInformation->setShippingAddress($shippingAddress);
        $address->setShippingCarrierCode('flatrate');
        $address->setShippingMethodCode('flatrate');
        return $address;
    }

    /**
     * Prepare the shipping address data
     *
     * @param [type] $collectionPointResponse
     * @return void
     */
    protected function prepareShippingAddress($collectionPointResponse)
    {
        $collectionMessage = $collectionPointResponse;
        $firstName = $collectionMessage['firstname'];
        $lastName = $collectionMessage['lastname'];
        $countryId = $collectionMessage['country_id'];
        $pincode = $collectionMessage['c_pincode'];
        $region = $collectionMessage['region'];
        $street = $collectionMessage['c_address'];
        $city = $collectionMessage['c_city'];
        $telephone = $collectionMessage['telephone'];
        $regionId = $this->getRegionByName($region, $countryId);
        $address = $this->address
            ->setFirstname($firstName)
            ->setLastname($lastName)
            ->setStreet($street)
            ->setCity($city)
            ->setCountryId($countryId)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setPostcode($pincode)
            ->setTelephone($telephone)
            ->setSaveInAddressBook(0)
            ->setSameAsBilling(1);
        return $address;
    }

    /**
     * Get cart item sku
     *
     * @return void
     */
    public function getCartItemsSkus()
    {
        $cartSkuArray = [];
        $cartItems = $this->session->getQuote()->getAllVisibleItems();
        foreach ($cartItems as $product) {
            $cartSkuArray[] = $product->getSku();
        }
        return $cartSkuArray;
    }

    /**
     * Get the region and country
     *
     * @param string $region
     * @param int $countryId
     * @return void
     */
    public function getRegionByName($region, $countryId)
    {
        return $this->regionFactory->create()->loadByName($region, $countryId)->getRegionId();
    }

    /**
     * Get the data of point address
     *
     * @param array $cartSkuArray
     * @return void
     */
    protected function getCollectionPointAddress($cartSkuArray)
    {
        $customShipping = [];
        $order_id = $this->coreSession->getLastOrderId();
        $orderCollection = $this->order->load($order_id);
        $customShipping['firstname'] = $orderCollection->getCustomerFirstname();
        $customShipping['lastname'] = $orderCollection->getCustomerLastname();
        if (empty($customShipping['firstname'])) {
            if ($this->customerSession->isLoggedIn()) {
                $customerData = $this->customerSession->getCustomer()->getData();
                $customShipping['firstname'] = $customerData['firstname'];
            }
        }
        if (empty($customShipping['lastname'])) {
            if ($this->customerSession->isLoggedIn()) {
                $customerData = $this->customerSession->getCustomer()->getData();
                $customShipping['lastname'] = $customerData['lastname'];
            }
        }
        $customShipping['country_id'] = $orderCollection->getShippingAddress()->getCountryId();
        $customShipping['c_pincode'] = $orderCollection->getShippingAddress()->getPostCode();
        $customShipping['region'] = $orderCollection->getShippingAddress()->getRegionId();
        $customShipping['c_address'] = $orderCollection->getShippingAddress()->getStreet();
        $customShipping['c_city'] = $orderCollection->getShippingAddress()->getCity();
        $customShipping['telephone'] = $orderCollection->getShippingAddress()->getTelephone();
        return $customShipping;
    }

    /**
     * Get the store code
     *
     * @return void
     */
    public function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }

    /**
     * Get config value
     *
     * @param bool $config_path
     * @return bool|string
     */

    public function getConfig($config_path)
    {
        $storeCode=$this->getStoreCode();
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeCode
        );
    }

    /**
     * Get Category List
     *
     * @return Array
     */
    public function getCategoryList()
    {
        return $this->allCategoryList->toOptionArray();
    }

    /**
     * Get the country list
     *
     * @return void
     */
    public function getCountryList()
    {
        return $this->countryFactory->toOptionArray();
    }

    /**
     * Get Status
     *
     * @return void
     */
    public function getStatus()
    {
        return $this->status->toOptionArray();
    }

    /**
     * Get the product grid url
     *
     * @return void
     */
    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('orderapprovalrules/grid/products', ['_current' => true]);
    }

    /**
     * Get the custom rule url
     *
     * @return void
     */
    public function getCustomRuleUrl()
    {
        return $this->_backendUrl->getUrl('orderapprovalrules/grid/conditions', ['_current' => true]);
    }

    /**
     * Set the checkout config
     *
     * @return void
     */
    public function setCheckoutConfig()
    {
        $this->configWriter->save(
            'checkout/options/display_billing_address_on',
            0,
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $scopeId = 0
        );
    }

    /**
     * Get the message for buyers
     *
     * @return void
     */
    public function getMessageForBuyers()
    {
        return $this->getConfig('orderapprovalrules/general/message_for_buyer');
    }

    /**
     * Check the Extension enable or disable
     *
     * @return boolean
     */
    public function isExtensionEnable()
    {
        return $this->getConfig('orderapprovalrules/general/enabled');
    }

    /**
     * Get the admin email
     *
     * @return void
     */
    public function getAdminEmail()
    {
        return $this->getConfig('orderapprovalrules/general/admin_email');
    }

    /**
     * Get conditional order approve check
     *
     * @return void
     */
    public function getConditionalOrderApprovalCheck()
    {
        return $this->getConfig('orderapprovalrules/general/conditional_order_approval_check');
    }

    /**
     * Get order approval pending template
     *
     * @return void
     */
    public function getOrderApprovalPendingTemplate()
    {
        return $this->getConfig('orderapprovalrules/email_templates_setting/order_approval_pending_mail');
    }

    /**
     * Get the order approved template
     *
     * @return void
     */
    public function getOrderApprovedTemplate()
    {
        return $this->getConfig('orderapprovalrules/email_templates_setting/order_approved_mail');
    }

    /**
     * Get the order disapproved template
     *
     * @return void
     */
    public function getOrderDisapprovedTemplate()
    {
        return $this->getConfig('orderapprovalrules/email_templates_setting/order_disapproved_mail');
    }

    /**
     * Get the order approval admin template
     *
     * @return void
     */
    public function getOrderApprovalAdminTemplate()
    {
        return 'orderapprovalrules_email_templates_setting_order_approval_admin';
    }

    /**
     * Get the message if mail not send
     *
     * @return void
     */
    public function getMessageIfMailNotSend()
    {
        return $this->getConfig('orderapprovalrules/email_templates_setting/message_mail_error');
    }
}
