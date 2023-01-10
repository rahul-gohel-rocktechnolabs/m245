<?php
/**
 * @category Mageants OrderApprovalRules
 * @package Mageants_OrderApprovalRules
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants <support@mageants.com>
 */

namespace Mageants\OrderApprovalRules\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Generate the options for label enable or disable
 */
class Status implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options=[
            ['label'=>'Enabled', 'value'=>'0'],
            ['label'=>'Disabled', 'value'=>'1'],
        ];
        return $options;
    }
}
