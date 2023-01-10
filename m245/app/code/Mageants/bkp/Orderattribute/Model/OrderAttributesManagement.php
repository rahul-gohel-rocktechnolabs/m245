<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model;

use Magento\Customer\Model\Session as CustomerSession;
use \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory as AttributeCollectionFactory;

/**
 * Attribute Metadata data provider class
 */
class OrderAttributesManagement
{
    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory
     */
    protected $orderAttributesValueFactory;

    /**
     * @var AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var AttributeCollectionFactory
     */
    protected $orderAttributeCollectionFactory;

    /**
     * @var $validator
     */
    private $validator;

    /**
     * @var $customerSession
     */
    private $customerSession;

    /**
     * @var Order\Attribute\ValueRepository
     */
    private $valueRepository;

    /**
     * OrderAttributesManagement constructor.
     * @param \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory $orderAttributeValueFactory
     * @param \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param AttributeCollectionFactory $orderAttributeCollectionFactory
     * @param \Mageants\Orderattribute\Model\Order\Attribute\ValueRepository $valueRepository
     * @param CustomerSession $customerSession
     * @param Validator $validator
     */
    public function __construct(
        \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory $orderAttributeValueFactory,
        \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AttributeCollectionFactory $orderAttributeCollectionFactory,
        \Mageants\Orderattribute\Model\Order\Attribute\ValueRepository $valueRepository,
        CustomerSession $customerSession,
        Validator $validator
    ) {
        $this->orderAttributesValueFactory = $orderAttributeValueFactory;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->orderAttributeCollectionFactory = $orderAttributeCollectionFactory;
        $this->validator = $validator;
        $this->customerSession = $customerSession;
        $this->valueRepository = $valueRepository;
    }

    /**
     * Save attribute values of order
     *
     * @param int|\Magento\Sales\Api\Data\OrderInterface $order
     * @param array $orderAttributesData
     */
    public function saveOrderAttributes($order, $orderAttributesData = null)
    {
        if ($order instanceof \Magento\Sales\Api\Data\OrderInterface) {
            $orderId = $order->getEntityId();
        } else {
            $orderId = $order;
            $order = null;
        }

        /**
         * @var \Mageants\Orderattribute\Model\Order\Attribute\Value $orderAttributesModel
         */
        if (!$orderAttributesData && $order) {
            $orderAttributesModel = $this->loadOrderAttributeValuesByQuoteId($order->getQuoteId());
            $orderAttributesData = $orderAttributesModel->getData();
            //remove empty
            $orderAttributesData = array_diff($orderAttributesData, [null]);
        } else {
            $orderAttributesModel = $this->loadOrderAttributeValuesAndSetOrderId($orderId);
        }

        $attributes = $this->validateAttributes($order, $orderAttributesData);

        $valuesToInsert = array_merge($this->getDefaultValues($order), $orderAttributesData);
        if ($valuesToInsert) {
            foreach ($valuesToInsert as $key => $value) {
                if (strpos($key, '_output') !== false) {
                    continue;
                }
                $value = is_array($value) ? implode(',', $value) : $value;
                $orderAttributesModel->setData($key, $value);
                /* not default values prepare output */
                if (array_key_exists($key, $attributes)) {
                    $value = $orderAttributesModel->prepareAttributeValue($attributes[$key]);
                    $orderAttributesModel->setData(
                        $key . '_output',
                        $value
                    );
                }
            }

            $orderAttributesModel->setOrderEntityId($orderId);
            if ($customerId = $this->customerSession->getCustomerId()) {
                $orderAttributesModel->setCustomerId($customerId);
            } elseif ($order && $order->getCustomerId()) {
                $orderAttributesModel->setCustomerId($order->getCustomerId());
            }
            $this->valueRepository->save($orderAttributesModel);
        }
    }

    /**
     * Validate Attributes
     *
     * @param obejct $order
     * @param array $orderAttributesData
     * @return array
     */
    protected function validateAttributes(&$order, &$orderAttributesData)
    {
        $attributes = [];
        $attributesCollection = $this->orderAttributeCollectionFactory->create()
            ->addFieldToFilter(
                'attribute_code',
                ['in' => array_keys($orderAttributesData)]
            );

        /* attribute validations */
        if ($order && is_array($orderAttributesData)) {
            $orderAttributesData = $this->validator->validateAttributeRelations($orderAttributesData);
            $orderAttributesData = $this->validator->validateShippingMethods(
                $order,
                $orderAttributesData,
                $attributesCollection
            );
        }

        foreach ($attributesCollection as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $attribute;
        }

        return $attributes;
    }

    /**
     * Get default values
     *
     * @param \Magento\Sales\Model\Order\Interceptor|null $order
     * @return array
     */
    protected function getDefaultValues($order)
    {
        $defaultValues = [];
        $orderAttributesWithDefaultValues = $this->attributeMetadataDataProvider
            ->loadAttributesWithDefaultValueCollection();

        if ($order !== null && $order->getIsVirtual() == true) {
            $orderAttributesWithDefaultValues->addFieldToFilter(
                'checkout_step',
                ['eq' => \Mageants\Orderattribute\Model\Config\Source\CheckoutStep::PAYMENT_STEP]
            );
        }

        foreach ($orderAttributesWithDefaultValues as $orderAttribute) {
            /**
             * @var \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute $orderAttribute
             */
            $defaultValues[$orderAttribute->getAttributeCode()] = $orderAttribute->getDefaultValue();
        }

        return $defaultValues;
    }

    /**
     * Get default value from order attribute
     *
     * @param \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute $orderAttribute
     * @return string|int|bool|float
     */
    protected function getDefaultValueFromOrderAttribute($orderAttribute)
    {
        return $orderAttribute->getDefaultValue();
    }

    /**
     * Load order attribute values and set order id
     *
     * @param int $orderId
     * @return \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    protected function loadOrderAttributeValuesAndSetOrderId($orderId)
    {
        /**
         * @var \Mageants\Orderattribute\Model\Order\Attribute\Value $orderAttributes
         */
        $orderAttributes = $this->orderAttributesValueFactory->create();
        $orderAttributes->load($orderId, 'order_entity_id');
        if (!$orderAttributes->getOrderEntityId()) {
            $orderAttributes->setOrderEntityId($orderId);
        }

        return $orderAttributes;
    }

    /**
     * Load order attribute values by quote id
     *
     * @param int $quoteId
     * @return \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    protected function loadOrderAttributeValuesByQuoteId($quoteId)
    {
        /**
         * @var \Mageants\Orderattribute\Model\Order\Attribute\Value $orderAttributes
         */
        $orderAttributes = $this->orderAttributesValueFactory->create();
        $orderAttributes->load($quoteId, 'quote_id');
        if (!$orderAttributes->getQuoteId()) {
            $orderAttributes->setQuoteId($quoteId);
        }

        return $orderAttributes;
    }

    /**
     * Save attributes from quote
     *
     * @param int $quoteId
     * @param \Magento\Framework\Api\AttributeValue[] $orderAttributes
     * @return \Mageants\Orderattribute\Model\Order\Attribute\Value
     */
    public function saveAttributesFromQuote($quoteId, $orderAttributes)
    {
        if (!is_array($orderAttributes)) {
            $orderAttributes = [];
        }
        $existsOrderAttributes = $this->loadOrderAttributeValuesByQuoteId($quoteId);
        foreach ($orderAttributes as $orderAttributeCode => $orderAttribute) {
            $value = is_string($orderAttribute) ? $orderAttribute : $orderAttribute->getValue();
            $existsOrderAttributes->setData($orderAttributeCode, $value);
        }

        $this->valueRepository->save($existsOrderAttributes);

        $orderAttributes = $existsOrderAttributes->getData();
        return $orderAttributes;
    }
}
