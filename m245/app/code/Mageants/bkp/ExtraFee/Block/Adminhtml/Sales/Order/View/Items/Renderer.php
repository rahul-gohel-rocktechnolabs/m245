<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Block\Adminhtml\Sales\Order\View\Items;

use Magento\Framework\Pricing\Helper\Data as Pricedata;
use Magento\Catalog\Helper\Data as HelperData;
use Mageants\ExtraFee\Model\ExtraFee;
use \Magento\Backend\Block\Template\Context;
use \Magento\CatalogInventory\Api\StockRegistryInterface;
use \Magento\CatalogInventory\Api\StockConfigurationInterface;
use \Magento\Framework\Registry;
use \Magento\GiftMessage\Helper\Message;
use \Magento\Checkout\Helper\Data;

class Renderer extends \Magento\Bundle\Block\Adminhtml\Sales\Order\View\Items\Renderer
{
    /**
     * Constructor
     *
     * @param Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param Registry $registry
     * @param Message $messageHelper
     * @param Data $checkoutHelper
     * @param ExtraFee $model
     * @param HelperData $helperdata
     * @param Pricedata $pricedata
     */
    public function __construct(
        Context $context,
        StockRegistryInterface $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        Registry $registry,
        Message $messageHelper,
        Data $checkoutHelper,
        ExtraFee $model,
        HelperData $helperdata,
        Pricedata $pricedata
    ) {
        $this->model = $model;
        $this->helperdata = $helperdata;
        $this->pricedata = $pricedata;
        parent::__construct(
            $context,
            $stockRegistry,
            $stockConfiguration,
            $registry,
            $messageHelper,
            $checkoutHelper
        );
    }
    /**
     * To Prepare the layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('Mageants_ExtraFee::order/view/items/renderer.phtml');
        return $this;
    }

    /**
     * Get fee data using model
     *
     * @param object $fee
     */

    public function getFeeData($fee)
    {
        $feedata = $this->model->load($fee)->getData();
        return $feedata;
    }

    /**
     * Get sku using catalog data
     *
     * @param string $sku
     */
    
    public function getCatalogdata($sku)
    {
        $data = implode(
            '<br />',
            $this->helperdata->splitSku($sku)
        );
        return $data;
    }

    /**
     * Get Currency using pricedata
     *
     * @param string $amount
     */

    public function getCurrency($amount)
    {
        $priceHelper = $this->pricedata->currency($amount, true, false);
        return $priceHelper;
    }
}
