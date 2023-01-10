<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Data\Form\Element;

class Boolean extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Boolean
{
    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setValues([
            ['label' => __(' '), 'value' => ''],
            ['label' => __('No'), 'value' => '0'],
            ['label' => __('Yes'), 'value' => '1'],
        ]);
    }
}
