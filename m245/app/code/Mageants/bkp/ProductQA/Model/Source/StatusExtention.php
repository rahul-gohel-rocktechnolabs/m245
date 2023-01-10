<?php
 /**
  * @category  Mageants Product Question Answser
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */

namespace Mageants\ProductQA\Model\Source;

class StatusExtention implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Status values
     */
    public const STATUS_ENABLE = 1;
    public const STATUS_DISABLE = 0;

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
            ['value' => self::STATUS_ENABLE,  'label' => __('Enable')],
            ['value' => self::STATUS_DISABLE,  'label' => __('Disable')],
        ];
    }
}
