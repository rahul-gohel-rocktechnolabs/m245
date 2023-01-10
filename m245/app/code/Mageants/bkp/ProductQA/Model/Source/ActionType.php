<?php
 /**
  * @category  Mageants Product Question Answser
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */

namespace Mageants\ProductQA\Model\Source;

class ActionType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Status values
     */
    public const ACTION_LIKE = 1;
    public const ACTION_DISLIKE = 2;

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
            ['value' => self::ACTION_LIKE,  'label' => __('Like')],
            ['value' => self::ACTION_DISLIKE,  'label' => __('Dislike')],
        ];
    }
}
