<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order\Api;

/**
 * For API. Extension Attributes Save Get
 */
class OrderRepository
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface|null
     */
    protected $currentOrder;

    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var \Mageants\Orderattribute\Model\AttributeMetadataDataProvider
     */
    private $attributeProvider;

    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory
     */
    private $valueFactory;

    /**
     * @var \Mageants\Orderattribute\Model\OrderAttributesManagement
     */
    private $orderAttributesManagement;

    /**
     * @var \Mageants\Orderattribute\Model\OrderAttributeDataFactory
     */
    private $dataFactory;

    /**
     * OrderRepository constructor.
     *
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory                $orderExtensionFactory
     * @param \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory  $valueFactory
     * @param \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeProvider
     * @param \Mageants\Orderattribute\Model\OrderAttributesManagement     $attributesManagement
     * @param \Mageants\Orderattribute\Model\OrderAttributeDataFactory     $dataFactory
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory,
        \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory $valueFactory,
        \Mageants\Orderattribute\Model\AttributeMetadataDataProvider $attributeProvider,
        \Mageants\Orderattribute\Model\OrderAttributesManagement $attributesManagement,
        \Mageants\Orderattribute\Model\OrderAttributeDataFactory $dataFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->attributeProvider = $attributeProvider;
        $this->valueFactory = $valueFactory;
        $this->orderAttributesManagement = $attributesManagement;
        $this->dataFactory = $dataFactory;
    }

    /**
     * After get
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface      $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $this->addOrderAttributes($order);

        return $order;
    }

    /**
     * After get list
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param obejct $searchResult
     * @return mixed
     */
    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        $searchResult
    ) {
        foreach ($searchResult->getItems() as $order) {
            $this->addOrderAttributes($order);
        }

        return $searchResult;
    }

    /**
     * Before save
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface      $order
     */
    public function beforeSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $this->currentOrder = $order;
    }

    /**
     * After save
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface      $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        if ($this->currentOrder !== null) {
            $extensionAttributes = $this->currentOrder->getExtensionAttributes();
            if ($extensionAttributes && $extensionAttributes->getMageantsOrderAttributes()) {
                $attributes = $extensionAttributes->getMageantsOrderAttributes();
                $attributesValue = [];
                if (is_array($attributes)) {
                    foreach ($attributes as $attribute) {
                        if (isset($attribute['attribute_code']) && isset($attribute['value'])) {
                            $attributesValue[$attribute['attribute_code']] = $attribute['value'];
                        }
                    }
                    if (count($attributesValue)) {
                        $this->orderAttributesManagement
                            ->saveOrderAttributes($order, $attributesValue);
                    }
                }
            }
            $this->currentOrder = null;
        }
        return $order;
    }

    /**
     * Add mageants order attributes data to Extension Attributes
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    private function addOrderAttributes(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        } elseif ($extensionAttributes->getMageantsOrderAttributes() !== null) {
            return;
        }
        $customAttributes = [];
        /** @var \Mageants\Orderattribute\Model\Order\Attribute\Value $attributeModel */
        $attributeModel = $this->valueFactory->create();
        $attributeModel->loadByOrderId($order->getId());
        $attributes = $this->attributeProvider->loadAttributesForApi($order->getStoreId());

        /** @var \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute $attribute */
        foreach ($attributes as $attribute) {
            if ($attributeModel->prepareAttributeValue($attribute)) {
                /** @var \Mageants\Orderattribute\Model\OrderAttributeData $data */
                $data = $this->dataFactory->create();
                $customAttributes[$attribute->getAttributeCode()] = $data->addData(
                    [
                        'attribute_code' => $attribute->getAttributeCode(),
                        'label' => $attribute->getFrontendLabel(),
                        'value' => $attributeModel->getData($attribute->getAttributeCode()),
                        'value_output' => $attributeModel->prepareAttributeValue($attribute),
                        'value_output_admin' => $attributeModel->getAdminAttributeValue($attribute),
                    ]
                );
            }
        }
        $extensionAttributes->setMageantsOrderAttributes($customAttributes);
        $order->setExtensionAttributes($extensionAttributes);
    }
}
