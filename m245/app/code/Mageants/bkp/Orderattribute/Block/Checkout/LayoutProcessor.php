<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Checkout;

use Mageants\Orderattribute\Block\Checkout\AttributeMerger;
use Mageants\Orderattribute\Component\Form\AttributeMapper;
use Mageants\Orderattribute\Helper\Config;
use Mageants\Orderattribute\Model\AttributeMetadataDataProvider;
use Magento\Customer\Model\Session as CustomerSession;
use \Exception;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{

    public const SHIPPING_STEP = 2;

    public const BILLING_STEP = 3;

    /**
     * @var AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @var AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @var $customerSession
     */
    private $customerSession;

    /**
     * @var $configHelper
     */
    private $configHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var $scopeConfig
     */
    protected $scopeConfig;

    /**
     * LayoutProcessor constructor.
     *
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param AttributeMapper $attributeMapper
     * @param AttributeMerger $merger
     * @param CustomerSession $customerSession
     * @param Config $configHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AttributeMapper $attributeMapper,
        AttributeMerger $merger,
        CustomerSession $customerSession,
        Config $configHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->merger = $merger;
        $this->customerSession = $customerSession;
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     * @throws Exception
     */
    public function process($jsLayout)
    {
        $moduleStatus = $this->configHelper->isEnabled();
        if ($moduleStatus) {
            $attributes = $this->attributeMetadataDataProvider
                ->loadAttributesFrontendCollection(
                    $this->storeManager->getStore()->getId()
                );

            $this->addToShippingStep($jsLayout, $attributes);
            $this->addToBillingStep($jsLayout, $attributes);

            if ($this->configHelper->getCheckoutProgress()) {
                $this->addAttributesToSidebar($jsLayout);
            }
        }
        return $jsLayout;
    }

    /**
     * Add to billing step
     *
     * @param array $jsLayout
     * @param array $attributes
     * @return void
     */
    protected function addToBillingStep(&$jsLayout, $attributes)
    {
        $elements = $this->getElementsByAttributes($attributes, self::BILLING_STEP);
        if (count($elements) > 0) {
            $this->addToBeforeMethods($jsLayout, $elements);
            $this->addAdditionalValidator($jsLayout);
        }
    }

    /**
     * Add additional validator
     *
     * @param array $jsLayout
     * @return void
     */
    protected function addAdditionalValidator(&$jsLayout)
    {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['additional-payment-validators']['children'])) {
            $fields = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['additional-payment-validators']['children'];
            $fields['order-attributes-validator'] =
                [
                'component' => "Mageants_Orderattribute/js/view/order-attributes-validator",
            ];
        }
    }

    /**
     * Add to before methods
     *
     * @param array $jsLayout
     * @param array $elements
     * @return void
     */
    protected function addToBeforeMethods(&$jsLayout, $elements)
    {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['beforeMethods']['children'])) {
            $fields = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['beforeMethods']['children'];
            $fields['order-attributes-fields'] =
                [
                'component' => "Mageants_Orderattribute/js/view/order-attributes",
            ];

            $fields['order-attributes-fields']['children'] = $this->merger->merge(
                $elements,
                'checkoutProvider',
                'shippingAddress.custom_attributes_beforemethods',
                $elements
            );
        }
    }

    /**
     * Add to shipping step
     *
     * @param array $jsLayout
     * @param array $attributes
     * @return void
     */
    protected function addToShippingStep(&$jsLayout, $attributes)
    {
        $elements = $this->getElementsByAttributes($attributes, self::SHIPPING_STEP);

        if (count($elements) > 0) {
            $customer = $this->customerSession->getCustomer();
            if ($this->customerSession->isLoggedIn() &&
                ($customer->getDefaultShippingAddress() || count($customer->getAdditionalAddresses()) > 0)
            ) {
                $this->addToBeforeForm($jsLayout, $elements);
            } else {
                $this->addToShippingAddressFieldset($jsLayout, $elements);
            }
        }
    }

    /**
     * Add to shipping address fieldset
     *
     * @param array $jsLayout
     * @param array $elements
     * @return void
     */
    protected function addToShippingAddressFieldset(&$jsLayout, $elements)
    {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'])) {
            $fields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

            $fields['order-attributes-fields'] =
                [
                'component' => "Mageants_Orderattribute/js/view/order-attributes-guest",
            ];

            $fields['order-attributes-fields']['children'] = $this->merger->merge(
                $elements,
                'checkoutProvider',
                'shippingAddress.custom_attributes',
                $elements
            );
        }
    }

    /**
     * Add to before form
     *
     * @param array $jsLayout
     * @param array $elements
     * @return void
     */
    protected function addToBeforeForm(&$jsLayout, $elements)
    {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['before-form']['children'])) {
            $fields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['before-form']['children'];
            $fields['order-attributes-fields'] =
                [
                'component' => "Mageants_Orderattribute/js/view/order-attributes",
                'displayArea' => "order-attributes",
            ];

            $fields['order-attributes-fields']['children'] = $this->merger->merge(
                $elements,
                'checkoutProvider',
                'shippingAddress.custom_attributes',
                $elements
            );
        }
    }

    /**
     * Add attributes to sidebar
     *
     * @param array $jsLayout
     * @return array
     */
    protected function addAttributesToSidebar(&$jsLayout)
    {
        if (isset($jsLayout['components']['checkout']['children']['sidebar']['children']['summary']
            ['children']['itemsAfter']['children'])) {
            $fields = &$jsLayout['components']['checkout']['children']['sidebar']['children']['summary']
                ['children']['itemsAfter']['children'];
            $fields['order-attributes-information'] =
                [
                'component' => "Mageants_Orderattribute/js/view/order-attributes-information",
                'config' => [
                    'deps' => 'checkout.steps.shipping-step.shippingAddress',
                ],
                'displayArea' => "shipping-information",
                'hide_empty' => $this->configHelper->getCheckoutHideEmpty(),
            ];
        }

        return $jsLayout;
    }

    /**
     * Get elements by attributes
     *
     * @param array $attributes
     * @param int $checkoutStepId
     * @return array
     */
    protected function getElementsByAttributes($attributes, $checkoutStepId)
    {
        $elements = [];
        foreach ($attributes as $attribute) {
            /**
             * @var \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute $attribute
             */
            if (!$this->isAllowedAttribute($attribute, $checkoutStepId)) {
                continue;
            }
            $elements[self::getAttributeName($attribute->getAttributeCode())] = $this->attributeMapper->map($attribute);
        }
        return $elements;
    }

    /**
     * Return attribute name for JS
     *
     * @param string $attributeCode
     * @return string
     */
    public function getAttributeName($attributeCode)
    {
        return 'mgorderattribute_' . $attributeCode;
    }

    /**
     * Is allowed attribute
     *
     * @param \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute $attribute
     * @param int $checkoutStepId
     * @return boolean
     */
    protected function isAllowedAttribute($attribute, $checkoutStepId)
    {
        $isAllowed = $attribute->getCheckoutStep() == $checkoutStepId;

        if ($isAllowed) {
            $currentCustomerGroup = (string) $this->customerSession->getCustomerGroupId();
            $customerGroupForAttribute = explode(',', $attribute->getCustomerGroups());
            $isAllowed = !empty($customerGroupForAttribute)
            && in_array($currentCustomerGroup, $customerGroupForAttribute, 1) || !($attribute->getCustomerGroups());
        }

        return $isAllowed;
    }
}
