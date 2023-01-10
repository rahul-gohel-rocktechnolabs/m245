<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Block\Adminhtml\Order\Attribute\Edit\Tab;

use \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\AbstractOptions;

class Options extends AbstractOptions
{
    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'labels',
            \Mageants\Orderattribute\Block\Adminhtml\Order\Attribute\Edit\Tab\Options\Labels::class
        );

        $this->addChild(
            'options',
            \Mageants\Orderattribute\Block\Adminhtml\Order\Attribute\Edit\Tab\Options\Options::class
        );

        return $this;
    }
}
