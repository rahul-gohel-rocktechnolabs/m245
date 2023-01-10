<?php

namespace Mageants\ExtraFee\ViewModel;

use \Magento\Framework\View\Element\Block\ArgumentInterface;
use Mageants\ExtraFee\Model\ExtraFee;
use Magento\Catalog\Helper\Data;
use Magento\Framework\Pricing\Helper\Data as Pricedata;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Request\Http;
use \Magento\GiftMessage\Helper\Message;

class ViewHelperData implements ArgumentInterface
{
    /**
     * To get data using this method
     *
     * @param ExtraFee $model
     * @param Data $helperdata
     * @param Http $requesthttp
     * @param Pricedata $pricedata
     * @param ProductRepository $productrepository
     * @param Message $message
     */
    public function __construct(
        ExtraFee $model,
        Data $helperdata,
        Http $requesthttp,
        Pricedata $pricedata,
        ProductRepository $productrepository,
        Message $message
    ) {
        $this->model = $model;
        $this->helperdata = $helperdata;
        $this->requesthttp = $requesthttp;
        $this->pricedata = $pricedata;
        $this->productrepository = $productrepository;
        $this->message = $message;
    }
    /**
     * Get Title
     */
    public function getTitle()
    {
        return 'Hello World';
    }

    /**
     * Return Message
     */
    public function getMessage()
    {
        $mess = $this->message;
        return $mess;
    }

    /**
     * Get Request
     */
    public function getRequest()
    {
        $fullAction = $this->requesthttp->getFullActionName();
        return $fullAction;
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
     * Get product id using product repository
     *
     * @param string $productId
     */

    public function getByid($productId)
    {
        $productdata = $this->productrepository->getById($productId);
        return $productdata;
    }
    
    /**
     * Get Currency using pricedata
     *
     * @param int $amount
     */

    public function getCurrency($amount)
    {
        $priceHelper = $this->pricedata->currency($amount, true, false);
        return $priceHelper;
    }

    /**
     * To override category
     *
     * @param string $productId
     * @param string $overrideCat
     * @param object $product
     */

    public function overrideCategory($productId, $overrideCat, $product)
    {
        $feesIds = [];
        if ($overrideCat == "Yes") {
                    $ExtraFeeList = $product->getExtrafeelist();
                    $feeIds = explode(",", $ExtraFeeList ?? '');
                    $ExtraFeeCollection = $this->model->getCollection()
                        ->addFieldToSelect('id')
                        ->addFieldToFilter('estatus', 'Enabled')
                        ->addFieldToFilter('is_mandatory', 'Yes')
                        ->addFieldToFilter('apply_to', [
                                ['finset'=> 'Product']
                            ])
                        ->addFieldToFilter('id', ['in' => $feeIds]);
                $feesIds=$ExtraFeeCollection->getData();
        } elseif ($overrideCat == "No") {
                 $ExtraFeeCollection=$this->model->getCollection()
                     ->addFieldToSelect('id')
                     ->addFieldToFilter('estatus', 'Enabled')
                     ->addFieldToFilter('is_mandatory', 'Yes')
                     ->addFieldToFilter('apply_to', [
                             ['finset'=> 'Category']
                         ]);

                  $feesIds = $ExtraFeeCollection->getData();
        } else {
                 $ExtraFeeList=$product->getExtrafeelist();
                 $feeIds=explode(",", $ExtraFeeList ?? '');
                 $categoryId=$product->getCategoryIds();
                 $ExtraFeeCatIdCollection=$this->model->getCollection()
                     ->addFieldToSelect('id')
                     ->addFieldToSelect('category_ids')
                     ->addFieldToFilter('estatus', 'Enabled')
                     ->addFieldToFilter('is_mandatory', 'Yes')
                     ->addFieldToFilter('apply_to', 'Category');
                 $ExtraFeeCatCollection=$this->model->getCollection()
                     ->addFieldToSelect('id')
                     ->addFieldToFilter('estatus', 'Enabled')
                     ->addFieldToFilter('is_mandatory', 'Yes')
                     ->addFieldToFilter('apply_to', 'Category');
                 $ExtraFeePrdCollection=$this->model->getCollection()
                     ->addFieldToSelect('id')
                     ->addFieldToFilter('estatus', 'Enabled')
                     ->addFieldToFilter('is_mandatory', 'Yes')
                     ->addFieldToFilter('apply_to', 'Product')
                     ->addFieldToFilter('id', ['in' => $feeIds]);

                 $ExtraCatFee = [];
                 $ExtraPrdFee = [];
                    
            if (count($ExtraFeeCatCollection) > 0) {
                        $data = $ExtraFeeCatIdCollection->getData();
                        $result = [];
                foreach ($data as $datas) {
                            $feeCatId = explode(",", $datas['category_ids'] ?? '');
                            $result=array_intersect($feeCatId, $categoryId);
                            
                    if (count($result) > 0) {
                                $ExtraCatFee=$ExtraFeeCatCollection->getData();
                    }
                }
            }
            if (count($ExtraFeePrdCollection) > 0) {
                        $ExtraPrdFee = $ExtraFeePrdCollection->getData();
            }
                     $feesIds = array_merge($ExtraCatFee, $ExtraPrdFee);
        }
        return $feesIds;
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
     * Get fee data using model
     *
     * @param object $data
     */
    public function getFeesData($data)
    {
        $feedata = $this->model->load($data)->getData();
        return $feedata;
    }

    /**
     * To Get the Action on Front side
     *
     * @param string $fullAction
     * @param object $_item
     */
    public function getAction($fullAction, $_item)
    {
        $feesIds= [];
        if ($fullAction=='sales_order_invoice') {
                $options=$_item->getOrderItem()->getProductOptions();
            if (array_key_exists("feesname", $options['info_buyRequest'])) {
                $feesIds=$options['info_buyRequest']['feesname'];
            }
            if (!(count($feesIds)>0)) {
                
                $feesIds = $this->orderInvoice($_item);
            }
        } elseif ($fullAction=='sales_order_view') {
            $feesIds = $this->orderView($_item);
        } elseif ($fullAction = 'sales_order_shipment') {
            $feesIds = $this->orderShipment($_item);
        }
            return $feesIds;
    }

    /**
     * To Get order invoice related data
     *
     * @param object $_item
     */
    public function orderInvoice($_item)
    {
        $productId = $_item->getProductId();
        $product=$this->getByid($productId);
        $overrideCat=$product->getOverrideCat();
                
        if ($overrideCat=="Yes") {
            $ExtraFeeList=$product->getExtrafeelist();
            $feeIds=explode(",", $ExtraFeeList ?? '');
            $ExtraFeeCollection=$this->model->getCollection()
            ->addFieldToSelect('id')
            ->addFieldToFilter('estatus', 'Enabled')
            ->addFieldToFilter('is_mandatory', 'Yes')
            ->addFieldToFilter('apply_to', [
                ['finset'=> 'Product']
                            ])
            ->addFieldToFilter('id', ['in' => $feeIds]);
            $feesIds=$ExtraFeeCollection->getData();
        } elseif ($overrideCat=="No") {
            $ExtraFeeCollection=$this->model->getCollection()
            ->addFieldToSelect('id')
            ->addFieldToFilter('estatus', 'Enabled')
            ->addFieldToFilter('is_mandatory', 'Yes')
            ->addFieldToFilter('apply_to', [
                ['finset'=> 'Category']
            ]);

            $feesIds=$ExtraFeeCollection->getData();
        } else {
            $ExtraFeeList=$product->getExtrafeelist();
            $feeIds=explode(",", $ExtraFeeList ?? '');
            $categoryId=$product->getCategoryIds();
            $ExtraFeeCatIdCollection=$this->model->getCollection()
            ->addFieldToSelect('id')
            ->addFieldToSelect('category_ids')
            ->addFieldToFilter('estatus', 'Enabled')
            ->addFieldToFilter('is_mandatory', 'Yes')
            ->addFieldToFilter('apply_to', 'Category');
            $ExtraFeeCatCollection=$this->model->getCollection()
            ->addFieldToSelect('id')
            ->addFieldToFilter('estatus', 'Enabled')
            ->addFieldToFilter('is_mandatory', 'Yes')
            ->addFieldToFilter('apply_to', 'Category');
            $ExtraFeePrdCollection=$this->model->getCollection()
            ->addFieldToSelect('id')
            ->addFieldToFilter('estatus', 'Enabled')
            ->addFieldToFilter('is_mandatory', 'Yes')
            ->addFieldToFilter('apply_to', 'Product')
            ->addFieldToFilter('id', ['in' => $feeIds]);

            $ExtraCatFee=[];
            $ExtraPrdFee=[];
                        
            if (count($ExtraFeeCatCollection) > 0) {
                $data = $ExtraFeeCatIdCollection->getData();
                $result = [];
                foreach ($data as $datas) {
                    $feeCatId = explode(",", $datas['category_ids'] ?? '');
                    $result=array_intersect($feeCatId, $categoryId);
                                
                    if (count($result) > 0) {
                        $ExtraCatFee=$ExtraFeeCatCollection->getData();
                    }
                }
            }
            if (count($ExtraFeePrdCollection) > 0) {
                $ExtraPrdFee=$ExtraFeePrdCollection->getData();
            }
            $feesIds=array_merge($ExtraCatFee, $ExtraPrdFee);
        }
            return $feesIds;
    }

    /**
     * To Get order view related data
     *
     * @param object $_item
     */
    public function orderView($_item)
    {
         $feesIds=$_item->getBuyRequest()->getFeesname();
        if (empty($feesIds)) {
            $productId = $_item->getProductId();
            $product=$this->getByid($productId);
            $overrideCat=$product->getOverrideCat();
            if ($overrideCat=="Yes") {
                $ExtraFeeList=$product->getExtrafeelist();
                $feeIds=explode(",", $ExtraFeeList ?? '');
                $ExtraFeeCollection=$this->model->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToFilter('estatus', 'Enabled')
                    ->addFieldToFilter('is_mandatory', 'Yes')
                    ->addFieldToFilter('apply_to', [
                            ['finset'=> 'Product']
                        ])
                    ->addFieldToFilter('id', ['in' => $feeIds]);
                 $feesIds=$ExtraFeeCollection->getData();
            } elseif ($overrideCat=="No") {
                $ExtraFeeCollection=$this->model->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToFilter('estatus', 'Enabled')
                    ->addFieldToFilter('is_mandatory', 'Yes')
                    ->addFieldToFilter('apply_to', [
                            ['finset'=> 'Category']
                        ]);

                 $feesIds=$ExtraFeeCollection->getData();
            } else {
                $ExtraFeeList=$product->getExtrafeelist();
                $feeIds=explode(",", $ExtraFeeList ?? '');
                $categoryId=$product->getCategoryIds();
                $ExtraFeeCatIdCollection=$this->model->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToSelect('category_ids')
                    ->addFieldToFilter('estatus', 'Enabled')
                    ->addFieldToFilter('is_mandatory', 'Yes')
                    ->addFieldToFilter('apply_to', 'Category');
                $ExtraFeeCatCollection=$this->model->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToFilter('estatus', 'Enabled')
                    ->addFieldToFilter('is_mandatory', 'Yes')
                    ->addFieldToFilter('apply_to', 'Category');
                $ExtraFeePrdCollection=$this->model->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToFilter('estatus', 'Enabled')
                    ->addFieldToFilter('is_mandatory', 'Yes')
                    ->addFieldToFilter('apply_to', 'Product')
                    ->addFieldToFilter('id', ['in' => $feeIds]);

                $ExtraCatFee=[];
                $ExtraPrdFee=[];
                        
                if (count($ExtraFeeCatCollection) > 0) {
                    $data = $ExtraFeeCatIdCollection->getData();
                    $result = [];
                    foreach ($data as $datas) {
                        $feeCatId = explode(",", $datas['category_ids'] ?? '');
                        $result=array_intersect($feeCatId, $categoryId);
                                
                        if (count($result) > 0) {
                            $ExtraCatFee=$ExtraFeeCatCollection->getData();
                        }
                    }
                }
                if (count($ExtraFeePrdCollection) > 0) {
                    $ExtraPrdFee=$ExtraFeePrdCollection->getData();
                }
                 $feesIds=array_merge($ExtraCatFee, $ExtraPrdFee);
            }
        }
        return $feesIds;
    }

    /**
     * To Get order shipment related data
     *
     * @param object $_item
     */
    public function orderShipment($_item)
    {
        $feesIds= [];
        $options=$_item->getOrderItem()->getProductOptions();
        if (array_key_exists("feesname", $options['info_buyRequest'])) {
            $feesIds=$options['info_buyRequest']['feesname'];
        }
        if (!(count($feesIds)>0)) {
            $productId = $_item->getProductId();
            $product=$this->getByid($productId);
            $overrideCat=$product->getOverrideCat();
            if ($overrideCat=="Yes") {
                $ExtraFeeList=$product->getExtrafeelist();
                $feeIds=explode(",", $ExtraFeeList ?? '');
                $ExtraFeeCollection=$this->model->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToFilter('estatus', 'Enabled')
                    ->addFieldToFilter('is_mandatory', 'Yes')
                    ->addFieldToFilter('apply_to', [
                            ['finset'=> 'Product']
                        ])
                    ->addFieldToFilter('id', ['in' => $feeIds]);
                 $feesIds=$ExtraFeeCollection->getData();
            } elseif ($overrideCat=="No") {
                $ExtraFeeCollection=$this->model->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToFilter('estatus', 'Enabled')
                    ->addFieldToFilter('is_mandatory', 'Yes')
                    ->addFieldToFilter('apply_to', [
                            ['finset'=> 'Category']
                        ]);

                 $feesIds=$ExtraFeeCollection->getData();
            } else {
                $ExtraFeeList=$product->getExtrafeelist();
                $feeIds=explode(",", $ExtraFeeList ?? '');
                $categoryId=$product->getCategoryIds();
                $ExtraFeeCatIdCollection=$this->model->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToSelect('category_ids')
                    ->addFieldToFilter('estatus', 'Enabled')
                    ->addFieldToFilter('is_mandatory', 'Yes')
                    ->addFieldToFilter('apply_to', 'Category');
                $ExtraFeeCatCollection=$this->model->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToFilter('estatus', 'Enabled')
                    ->addFieldToFilter('is_mandatory', 'Yes')
                    ->addFieldToFilter('apply_to', 'Category');
                $ExtraFeePrdCollection=$this->model->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToFilter('estatus', 'Enabled')
                    ->addFieldToFilter('is_mandatory', 'Yes')
                    ->addFieldToFilter('apply_to', 'Product')
                    ->addFieldToFilter('id', ['in' => $feeIds]);

                $ExtraCatFee=[];
                $ExtraPrdFee=[];
                        
                if (count($ExtraFeeCatCollection) > 0) {
                    $data = $ExtraFeeCatIdCollection->getData();
                    $result = [];
                    foreach ($data as $datas) {
                        $feeCatId = explode(",", $datas['category_ids'] ?? '');
                        $result=array_intersect($feeCatId, $categoryId);
                                
                        if (count($result) > 0) {
                            $ExtraCatFee=$ExtraFeeCatCollection->getData();
                        }
                    }
                }
                if (count($ExtraFeePrdCollection) > 0) {
                    $ExtraPrdFee=$ExtraFeePrdCollection->getData();
                }
                 $feesIds=array_merge($ExtraCatFee, $ExtraPrdFee);
            }
        }
        return $feesIds;
    }
}
