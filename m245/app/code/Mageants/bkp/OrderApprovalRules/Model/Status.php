<?php

namespace Mageants\OrderApprovalRules\Model;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * Get Grid row status type labels array.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = ['orderapproved' => __('Order Approved'),
        'orderdisapproved' => __('Order Disapproved'),
        'orderapprovalpending' => __('Order Approval Pending')];
        return $options;
    }
    
    /**
     * Get Grid row type array for option element.
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }
 
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
