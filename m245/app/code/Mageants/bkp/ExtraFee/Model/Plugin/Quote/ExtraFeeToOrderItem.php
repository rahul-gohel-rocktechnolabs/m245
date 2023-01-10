<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Model\Plugin\Quote;

use \Magento\Quote\Model\Quote\Item\ToOrderItem;
use \Closure;
use \Magento\Quote\Model\Quote\Item\AbstractItem;

class ExtraFeeToOrderItem
{
    /**
     * To convert the value
     *
     * @param object $subject
     * @param object $proceed
     * @param object $item
     * @param object $additional
     */
    public function aroundConvert(
        ToOrderItem $subject,
        Closure $proceed,
        AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem Item */
        $orderItem = $proceed($item, $additional);
        $orderItem->setBuyRequest($item->getBuyRequest());
        return $orderItem;
    }
}
