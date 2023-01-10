<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageants\DeliveryDate\Block\Order;

use Magento\Sales\Model\Order\Address;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Mageants\DeliveryDate\Helper\Data as DataHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Invoice view  comments form
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Info extends \Magento\Sales\Block\Order\Info
{
    /**
     * @var string
     */
    protected $_template = 'Mageants_DeliveryDate::order/info.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     *
     * @param TemplateContext $context
     * @param Registry $registry
     * @param PaymentHelper $paymentHelper
     * @param AddressRenderer $addressRenderer
     * @param DataHelper $helper
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer,
        DataHelper $helper,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        parent::__construct(
            $context,
            $registry,
            $paymentHelper,
            $addressRenderer,
            $data
        );
    }
    /**
     * To get Hepler Class
     *
     * @return $this
     */
    public function getHelperFunction()
    {
        return $this->helper;
    }
    /**
     * To get Product Repository class
     *
     * @return $this
     */
    public function productDetails()
    {
        return $this->productRepository;
    }
}
