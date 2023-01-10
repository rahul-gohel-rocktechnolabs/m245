<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Model\Config\Source;

class DateOfBirthFields implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'dd/mm/yy', 'label' => __('Day-Month-Year')],
            ['value' => 'mm/dd/yy', 'label' => __('Month-Day-Year')],
            ['value' => 'yy/mm/dd', 'label' => __('Year-Month-Day')]
        ];
    }
}
