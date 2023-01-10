<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Block\Adminhtml\Form\Field;

class DateField implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    
    public function toOptionArray()
    {
        return[
            "dd-mm-yy"=>"dd-mm-yy (ex:25-12-2012)",
            "mm-dd-yy"=>"mm-dd-yy (ex:05-20-2012)",
            "yy-mm-dd"=>"yy-mm-dd (ex:2012-12-20)"
        ];
    }
}
