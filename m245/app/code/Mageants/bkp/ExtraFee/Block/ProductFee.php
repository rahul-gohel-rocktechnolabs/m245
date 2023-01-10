<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ExtraFee\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Store\Model\StoreManagerInterface;
use Mageants\ExtraFee\Model\ExtraFee;
use Magento\Catalog\Block\Product\Context;
use Mageants\ExtraFee\Helper\Data;

class ProductFee extends AbstractProduct
{
    /**
     * @var StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var ExtraFee
     */
    public $colModel;

    /**
     * @var Data
     */
    public $heplerdata;
    /**
     * Constructor
     *
     * @param Context $context
     * @param StoreManagerInterface $_storeManager
     * @param ExtraFee $colModel
     * @param Data $heplerdata
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $_storeManager,
        ExtraFee $colModel,
        Data $heplerdata
    ) {
        $this->_storeManager = $_storeManager;
        $this->colModel = $colModel;
        $this->heplerdata = $heplerdata;
        parent::__construct($context);
    }
    /**
     * Return product title
     */
    public function getProductTitle()
    {
        return $this->heplerdata->getProductOfTitle();
    }

    /**
     * Return category title
     */
    public function getCategoryTitle()
    {
        return $this->heplerdata->getCategoryOfTitle();
    }

    /**
     * Return store Id
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }

    /**
     * Return Collection
     */
    public function getExtraFeeProductCollection()
    {
        $storeId=$this->getStoreId();
        $storeIds=['0' =>'0','1'=>$storeId];
        $collection = $this->colModel->getCollection()->addFieldToFilter('apply_to', 'Product')
        ->addFieldToFilter('estatus', 'Enabled')
        ->addFieldToFilter('store_id', ['in'=>$storeIds]);
        return $collection;
    }

    /**
     * Return Collection
     */
    public function getExtraFeeCategoryCollection()
    {
        $storeId=$this->getStoreId();
        $storeIds=['0' =>'0', '1'=>$storeId];
        $collection = $this->colModel->getCollection()->addFieldToFilter('apply_to', 'Category')
        ->addFieldToFilter('estatus', 'Enabled')
        ->addFieldToFilter('store_id', ['in'=>$storeIds]);
        return $collection;
    }
}
