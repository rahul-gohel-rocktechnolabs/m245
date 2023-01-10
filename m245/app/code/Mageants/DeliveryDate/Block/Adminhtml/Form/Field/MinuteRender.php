<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Block\Adminhtml\Form\Field;

class MinuteRender extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * Set Input Name function
     *
     * @param mixed $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Get Html function
     *
     * @return mixed
     */
    public function _toHtml()
    {
        $i = 0;
        for ($i = 0; $i <= 59; $i++) {
            if (strlen($i) == 1) {
                $i = '0' . $i;
            }
            $this->addOption($i, $i);
        }
        $i = 0;
        return parent::_toHtml();
    }
}
