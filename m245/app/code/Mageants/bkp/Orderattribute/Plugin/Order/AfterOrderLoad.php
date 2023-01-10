<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order;

class AfterOrderLoad
{
    /**
     * @var \Magento\Catalog\Api\Data\ProductExtensionFactory
     */
    protected $productExtensionFactory;

    /**
     * @param \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory
     */
    public function __construct(
        \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory
    ) {
        $this->productExtensionFactory = $productExtensionFactory;
    }

    /**
     * After load
     *
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order
     */
    public function afterLoad(\Magento\Sales\Model\Order $order)
    {
        $productExtension = $order->getExtensionAttributes();
        if ($productExtension === null) {
            $productExtension = $this->productExtensionFactory->create();
        }
        $order->setExtensionAttributes($productExtension);
        return $order;
    }
}
