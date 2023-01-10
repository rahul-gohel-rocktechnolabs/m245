<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order\Adminhtml;

class Attributes
{
    /**
     * @var \Mageants\Orderattribute\Helper\Config
     */
    protected $config;

    /**
     * @param \Mageants\Orderattribute\Helper\Config $config
     */
    public function __construct(\Mageants\Orderattribute\Helper\Config $config)
    {
        $this->config = $config;
    }

    /**
     * After To Html
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(
        \Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
        $result
    ) {
        $attributesBlock = $subject->getChildBlock('order_attributes');
        if ($attributesBlock) {
            $orderInfoArea = $attributesBlock->getOrderInfoArea();
            $attributesBlock->setTemplate("Mageants_Orderattribute::order/view/attributes.phtml");
            switch ($orderInfoArea) {
                case 'order':
                    $attributesHtml = $attributesBlock->toHtml();
                    $result = $result . $attributesHtml;
                    break;
                case 'invoice':
                    if ($this->config->getShowInvoiceView()) {
                        $attributesHtml = $attributesBlock->toHtml();
                        $result = $result . $attributesHtml;
                    }
                    break;
                case 'shipment':
                    if ($this->config->getShowShipmentView()) {
                        $attributesHtml = $attributesBlock->toHtml();
                        $result = $result . $attributesHtml;
                    }
                    break;
            }
        }
        return $result;
    }
}
