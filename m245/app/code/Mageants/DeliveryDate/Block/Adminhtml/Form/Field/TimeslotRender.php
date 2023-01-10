<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Block\Adminhtml\Form\Field;

class TimeslotRender extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * Set Input function
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
     * @return void
     */
    public function _toHtml()
    {
        $i='';
        for ($i=0; $i < 24; $i++) {
            if (strlen($i)==1) {
                $i='0'.$i;
            }
            $this->addOption($i, $i);
        }
        $i='';
        return parent::_toHtml();
    }
}
