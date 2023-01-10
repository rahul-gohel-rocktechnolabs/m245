<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model;

use Mageants\Orderattribute\Api\Data\OrderAttributeDataInterface;

class OrderAttributeData extends \Magento\Framework\DataObject implements OrderAttributeDataInterface
{
    /**
     * @inheritdoc
     */
    public function setAttributeCode($code)
    {
        $this->setData('attribute_code', $code);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAttributeCode()
    {
        return $this->getData('attribute_code');
    }

    /**
     * @inheritdoc
     */
    public function setLabel($label)
    {
        $this->setData('label', $label);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->getData('label');
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->setData('value', $value);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->getData('value');
    }

    /**
     * @inheritdoc
     */
    public function setValueOutput($value)
    {
        $this->setData('value_output', $value);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValueOutput()
    {
        return $this->getData('value_output');
    }

    /**
     * @inheritdoc
     */
    public function setValueOutputAdmin($value)
    {
        $this->setData('value_output_admin', $value);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValueOutputAdmin()
    {
        return $this->getData('value_output_admin');
    }
}
