<?php

namespace Mageants\SampleProduct\Model\Attribute\Backend;

/**
 * Class SampleProductAttribute
 */
class SampleProductAttribute extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

    /**
     * @var int $minimumValueLength
     */
    protected $minimumValueLength = 0;

    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function afterLoad($object)
    {
        // your after load logic

        return parent::afterLoad($object);
    }

    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function beforeSave($object)
    {
        $this->validateLength($object);

        return parent::beforeSave($object);
    }

    /**
     * Validate length
     *
     * @param \Magento\Framework\DataObject $object
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateLength($object)
    {
        /** @var string $attributeCode */
        $attributeCode = $this->getAttribute()->getAttributeCode();
        /** @var int $value */
        $value = (int)$object->getData($attributeCode);
        /** @var int $minimumValueLength */
        $minimumValueLength = $this->getMinimumValueLength();
        if ($value != null) {
            if ($value <= $minimumValueLength) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The cost of sample product  must be greater than %1', $minimumValueLength)
                );
            }
        }

        return true;
    }

    /**
     * Get minimum attribute value length
     *
     * @return int
     */
    public function getMinimumValueLength()
    {
        return $this->minimumValueLength;
    }
}
