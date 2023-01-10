<?php
/**
 * @category Mageants FastOrder
 * @package Mageants_FastOrder
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FastOrder\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Csv extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var Magento\Customer\Model\SessionFactory
     */
    protected $_customerSession;
    /**
     * @var \Magento\Catalog\Model\Product\TierPriceManagement
     */
    protected $_tierPriceManagement;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data $pricingHelper
     */
    protected $_pricingHelper;
    /**
     * @var  \Magento\Framework\Filesystem\Driver\File $fileSystem
     */
    protected $_fileSystem;
    /**
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Catalog\Model\Product\TierPriceManagement $tierPriceManagement
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param  \Magento\Framework\Filesystem\Driver\File $fileSystem
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Catalog\Model\Product\TierPriceManagement $tierPriceManagement,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Filesystem\Driver\File $fileSystem
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->_priceHelper = $priceHelper;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_tierPriceManagement = $tierPriceManagement;
        $this->_pricingHelper = $pricingHelper;
          $this->_fileSystem = $fileSystem;
        parent::__construct($context);
    }
    
    /**
     * Return json string
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $fh =   $this->_fileSystem->fileOpen(${'_FILES'}['file']['tmp_name'], 'r+');
        $ext = explode(".", ${'_FILES'}['file']["name"]);
        
        foreach ($ext as $value) {
            $path = $value;
        }
        $i = 0;
        $result = [];
        $errorResult = [];
        
        if ($path == 'csv') {
            while (($row = $this->_fileSystem->fileGetCsv($fh)) !== false) {
                if ($i == 0) {
                    if (trim($row[0]) != 'sku' || trim($row[1]) != 'qty') {
                        $this->messageManager->addError(
                            __('Something went wrong, please check CSV data and upload again.')
                        );
                 
                        break;
                    } else {
                        $i++;
                        continue;
                    }
                }
        
                $sku = $row[0];
                $qty = $row[1];
                $product = $this->productRepository->get($sku);
                $tierPriceQty=0;
                $tier_price=0;
                $customerGroupId = $this->_customerSession->create()->getCustomerGroupId();
                $data=$product->getTypeId();
                if (($data == 'simple' || $data == 'virtual' || $data == 'downloadable') && $qty > 0) {
                     $img = $this->_storeManager->getStore()
                     ->getBaseUrl(
                         \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                     ) .
                            'catalog/product'.
                            $product->getData('thumbnail');

                            $formattedPrice = $this->_priceHelper->currency($product->getPrice(), true, false);

                            $product_url = $product->getProductUrl();
                            $product_type = $product->getTypeId();

                            //----------Tier Price Start-----------

                            $allTiers = $product->getData('tier_price');
                            $tier_price_array = [];
                            $tierPriceUpdate=[];
                    if ($allTiers) {
                        foreach ($allTiers as $key => $value) {
                            $tierPriceQty = (int)$value['price_qty'];
                            if ($value['cust_group'] == $customerGroupId || $value['cust_group'] == 32000) {
                                $this->setQtydata();
                                $tierPriceUpdate[$tierPriceQty]=$tier_price;
                                $formattedTierPrice = $this->_pricingHelper
                                ->currency(
                                    number_format($tier_price, 2),
                                    true,
                                    false
                                );
                                $savePercentageFormat = ceil(100 - ((100 / $product->getPrice())* $value['price'])) ."%";
                                  $tier_price_array[] = "Buy $tierPriceQty for "
                                  .$formattedTierPrice." each and save "
                                  .$savePercentageFormat;
                            }
                        }
                    }
                    if (count($tierPriceUpdate) !=0) {
                        $this->setQty($tierPriceUpdate);
                   
                    }
                    if (isset($tier_price_array)) {
                        $tier_price_string=implode('<br>', $tier_price_array);
                    } else {
                        $tier_price_string = '';
                    }
            
                            //----------Tier Price End-----------

                            $result[] = ["qty"=>$qty,
                            "product_price"=>$formattedPrice,
                            "popup"=>0,
                            "product_name"=>$product->getName(),
                            "product_sku"=>$product->getSku(),
                            "product_id"=>$product->getId(),
                            "product_thumbnail"=>$img,
                            "product_price_amount"=>$product->getPrice(),
                            "product_url"=>$product_url,
                            "product_type"=>$product_type,
                            "product_tier_price"=>$tier_price_string,
                            "tierPriceUpdate"=>$tierPriceUpdate];
                } else {
                    $errorResult[] = $product->getSku();
                }
            }
            if (count($errorResult)!=0) {
                  $errorMessage = 'Product(s) with SKU '.implode(', ', $errorResult).' can not be uploaded.';
                  $this->messageManager->addError($errorMessage);
            }
            $this->getResponse()->setBody(json_encode($result));

        } else {
            $this->messageManager->addError(
                __(' please Select Valid File and upload again.')
            );
        }
    }
 /**
  * Tier Price Update
  *
  * @param int|null $tierPriceUpdate
  * @return int|string
  */
    public function setQty($tierPriceUpdate)
    {
        $tierPriceUpdate = implode(', ', array_map(
            function ($v, $k) {
                if (is_array($v)) {
                    return $k.'[]='.implode('&'.$k.'[]=', $v);
                } else {
                    return $k.'='.$v;
                }
            },
            $tierPriceUpdate,
            array_keys($tierPriceUpdate)
        ));
    }

    /**
     * All Tier Price
     *
     * @param int|null $tier_price
     * @return int|string
     */
    public function setQtydata($tier_price)
    {

        if ($value['website_id'] == 0) {
            $tier_price= $value['price'];
        } else {
            $tier_price =$value['website_price'];
        }
    }
}
