<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Block\Sales\Order\Items;

class Renderer extends \Magento\Bundle\Block\Sales\Order\Items\Renderer
{
    /**
     * Prepare Layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('Mageants_ExtraFee::sales/order/items/renderer.phtml');
        return $this;
    }
}
