<?php
 /**
  * @category  Mageants Product Question Answser
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */

namespace Mageants\ProductQA\Model\Source;

class UserType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Status values
     */
    public const ADMIN = 1;
    public const CUSTOMER = 0;

    /**
     * Get Option IN Array function.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $optionArray = ['' => ' '];
        
        foreach ($this->toOptionArray() as $option) {
            $optionArray[$option['value']] = $option['label'];
        }
        
        return $optionArray;
    }

    /**
     * Array in Option function.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ADMIN,  'label' => __('Admin')],
            ['value' => self::CUSTOMER,  'label' => __('Customer')],
        ];
    }
}
