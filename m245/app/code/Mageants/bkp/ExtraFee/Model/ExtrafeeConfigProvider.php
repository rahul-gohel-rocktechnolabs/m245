<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ExtraFee\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use \Mageants\ExtraFee\Helper\Data;
use \Magento\Checkout\Model\Session;
use \Psr\Log\LoggerInterface;

class ExtrafeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Mageants\Extrafee\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     *
     * @param Data $dataHelper
     * @param Session $checkoutSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $dataHelper,
        Session $checkoutSession,
        LoggerInterface $logger
    ) {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * To Extra Fee data provide this function
     *
     * @return array
     */
    public function getConfig()
    {
        $ExtrafeeConfig = [];
        $enabled = $this->dataHelper->isModuleEnabled();
        $ExtrafeeConfig['fee_label'] = $this->dataHelper->getFeeLabel();
        $ExtrafeeConfig['custom_fee_amount'] = $this->dataHelper->getExtrafee();
        $ExtrafeeConfig['enable'] = ($enabled) ? true : false;
        $ExtrafeeConfig['checkoutfee_label']=$this->dataHelper->getCheckoutFeeLabel();
        $ExtrafeeConfig['custom_checkoutfee_amount']=$this->dataHelper->getCheckoutFeeAmount();
        $ExtrafeeConfig['mandatory_shipfee_lable']=$this->dataHelper->getAllMandatoryShipingfeeLable();
        $ExtrafeeConfig['mandatory_orderfee_lable']=$this->dataHelper->getAllMandatoryOrderfeeLable();
        $ExtrafeeConfig['orderfee_label']=$this->dataHelper->getOrderFeeLabel();
        $ExtrafeeConfig['custom_orderfee_amount']=$this->dataHelper->getOrderFeeAmount();
        $ExtrafeeConfig['cod_fee_amount']=$this->dataHelper->getCodFee();
        $ExtrafeeConfig['all_fee_labels']=$this->dataHelper->getAllFeeLabels();
        return $ExtrafeeConfig;
    }
}
