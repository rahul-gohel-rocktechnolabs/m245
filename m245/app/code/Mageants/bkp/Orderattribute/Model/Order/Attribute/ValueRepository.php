<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\Order\Attribute;

use Mageants\Orderattribute\Api\Data\OrderAttributeValueInterface;
use Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\Value as ResourceValue;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;

class ValueRepository implements \Mageants\Orderattribute\Api\OrderAttributeValueRepositoryInterface
{
    /**
     * @var $attributes
     */
    private $attributes = [];

    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory
     */
    private $valueFactory;

    /**
     * @var \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var ResourceValue
     */
    private $valueResource;

    /**
     * @param \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory $valueFactory
     * @param \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory $attributeCollectionFactory
     * @param ResourceValue $valueResource
     */
    public function __construct(
        \Mageants\Orderattribute\Model\Order\Attribute\ValueFactory $valueFactory,
        \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory $attributeCollectionFactory,
        ResourceValue $valueResource
    ) {
        $this->valueFactory = $valueFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->valueResource = $valueResource;
    }

    /**
     * @inheritdoc
     */
    public function getByOrder($orderId)
    {
        if (!isset($this->attributes[$orderId])) {
            $attribute = $this->valueFactory->create()->loadByOrderId($orderId);
            if (!$attribute->getOrderEntityId()) {
                $attribute->setOrderEntityId($orderId);
            }
            $this->attributes[$orderId] = $attribute;
        }

        return $this->attributes[$orderId];
    }

    /**
     * Save
     *
     * @param OrderAttributeValueInterface $orderAttribute
     * @return OrderAttributeValueInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderAttributeValueInterface $orderAttribute)
    {
        if ($orderAttribute->getId()) {
            $orderAttribute = $this->get($orderAttribute->getId())->addData($orderAttribute->getData());
        }

        try {
            $this->valueResource->save($orderAttribute);
            unset($this->attributes[$orderAttribute->getId()]);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save attribute %1', $orderAttribute->getId()));
        }

        return $orderAttribute;
    }

    /**
     * Get
     *
     * @param int $attributeId
     * @return ResourceValue
     * @throws NoSuchEntityException
     */
    public function get($attributeId)
    {
        if (!isset($this->attributes[$attributeId])) {
            /** @var \Mageants\Orderattribute\Model\Order\Attribute\Value $attribute */
            $attribute = $this->valueFactory->create();
            $this->valueResource->load($attribute, $attributeId);
            if (!$attribute->getId()) {
                throw new NoSuchEntityException(__('Attribute with specified ID "%1" not found.', $attributeId));
            }
            $this->attributes[$attributeId] = $attribute;
        }
        return $this->attributes[$attributeId];
    }

    /**
     * Delete
     *
     * @param OrderAttributeValueInterface $attribute
     * @return bool
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function delete(OrderAttributeValueInterface $attribute)
    {
        try {
            $this->valueResource->delete($attribute);
            unset($this->attributes[$attribute->getId()]);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove attribute %1', $attribute->getId()));
        }
        return true;
    }

    /**
     * Delete by id
     *
     * @param int $attributeId
     * @return bool
     */
    public function deleteById($attributeId)
    {
        $model = $this->get($attributeId);
        $this->delete($model);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function saveApi(\Mageants\Orderattribute\Api\Data\OrderAttributeValueInterface $orderAttribute)
    {
        if (!$orderAttribute->getOrderEntityId()) {
            return false;
        }
        $orderAttribute = $this->getByOrder($orderAttribute->getOrderEntityId())->addData($orderAttribute->getData());
        $collection = $this->attributeCollectionFactory->create()->addFieldToFilter('include_api', 1);
        foreach ($collection as $attributeConfig) {
            foreach ($orderAttribute->getAttributes() as $attributeValue) {
                if ($attributeConfig->getAttributeCode() != $attributeValue->getAttributeCode()) {
                    continue;
                }
                $value = is_array($attributeValue->getValue()) ? implode(',', $attributeValue->getValue())
                : $attributeValue->getValue();
                $orderAttribute->setData($attributeConfig->getAttributeCode(), $value);

                $orderAttribute->setData(
                    $attributeConfig->getAttributeCode() . '_output',
                    $orderAttribute->prepareAttributeValue($attributeConfig)
                );
                break;
            }
        }
        $orderAttribute->save();
        return $orderAttribute;
    }
}
