<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Model\Config\Source;

class CaptchaType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Value which equal Visible for captcha type dropdown.
     */
    public const VISIBLE_VALUE = 1;

    /**
     * Value which equal Invisible for captcha type dropdown.
     */
    public const INVISIBLE_VALUE = 0;

   /**
    * Undocumented function
    *
    * @return void
    */
    public function toOptionArray()
    {
        return [
        ['value' => self::VISIBLE_VALUE, 'label' => __('Visible')],
        ['value' => self::INVISIBLE_VALUE, 'label' => __('Invisible')]
        ];
    }
}
