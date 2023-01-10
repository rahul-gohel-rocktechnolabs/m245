<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order;

class Attributes
{
    /**
     * After to html
     *
     * @param \Magento\Sales\Block\Order\Info $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(
        \Magento\Sales\Block\Order\Info $subject,
        $result
    ) {
        $attributes = $subject->getChildHtml('order_attributes');
        return $result . $attributes;
    }
}
