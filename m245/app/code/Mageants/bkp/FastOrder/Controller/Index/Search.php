<?php
/**
 * @category Mageants FastOrder
 * @package Mageants_FastOrder
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FastOrder\Controller\Index;

use Magento\Framework\App\Action\Context;

class Search extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Option
     */
    protected $_productOption;
    
    /**
     * @var  \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_productStatus;
    
    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_productVisibility;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;
    
    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockItem;
    
    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_fastOrderHelper;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
     /**
      * @var \Magento\Customer\Model\SessionFactory
      */
    protected $_customerSession;
     /**
      * @var \Magento\Catalog\Model\Product\TierPriceManagement
      */
    protected $_tierPriceManagement;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */

    protected $_pricingHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;
    /**
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Mageants\FastOrder\Helper\Data $fastOrderHelper
     * @param \Magento\Catalog\Model\Product\Option $productOption
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockItem
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Catalog\Model\Product\TierPriceManagement $tierPriceManagement
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Image $imageHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Mageants\FastOrder\Helper\Data $fastOrderHelper,
        \Magento\Catalog\Model\Product\Option $productOption,
        \Magento\CatalogInventory\Api\StockStateInterface $stockItem,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Catalog\Model\Product\TierPriceManagement $tierPriceManagement,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $imageHelper
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_productOption = $productOption;
        $this->_productloader = $_productloader;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        $this->_storeManager = $storeManager;
        $this->_priceHelper = $priceHelper;
        $this->_stockItem = $stockItem;
        $this->_fastOrderHelper=$fastOrderHelper;
        $this->_customerSession = $customerSession;
        $this->_tierPriceManagement = $tierPriceManagement;
        $this->_pricingHelper = $pricingHelper;
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        parent::__construct($context);
    }
    
    /**
     * Return json string
     */
    public function execute()
    {
        
        $keyword = $this->getRequest()->getPost('product');

        $products = [];
        
        $limit = $this->_fastOrderHelper->getFastOrderConfig('fastorder/general/max_results');
        
        if ($limit < 1) {
            $collection = [];
        } else {
            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('status', ['in' => $this->_productStatus->getVisibleStatusIds()]);
            $collection->setVisibility($this->_productVisibility->getVisibleInSiteIds());
            $collection->addFieldToFilter([
                ['attribute'=>'name','like'=>'%'. $keyword.'%'],
                ['attribute'=>'sku', 'like'=>'%'. $keyword.'%']
            ]);
            $collection->setPageSize($limit);
            $collection->setCurPage(1);
            $collection->setOrder('name', 'ASC');

            $collection->load();
        }
        
        if (count($collection)) {
            foreach ($collection as $product) {
                $products[] = $product->getId();
            }
        }
    
        $result = [];
        $items = [];
        $thumb_width = 70;
        if (count($products)) {
            foreach ($products as $productId) {
                $product = $this->_productloader->create()->load($productId);
                $img = "";
                if ($product->getData('thumbnail')) {
                    $img = $this->_storeManager->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .'catalog/product'. $product
                    ->getData('thumbnail');
                } else {
                    $img =$this->imageHelper->getDefaultPlaceholderUrl('small_image');
                }

                $customOptions = $this->_productOption->getProductOptionCollection($product);
                $pro=$product->hasCustomOptions();
                $type=$product->getTypeId();
                if (count($customOptions) > 0 || $pro || $type == 'configurable' || $type=='grouped') {
                    $popup = 1;
                } else {
                    $popup = 0;
                }
                $regularPricetxt = '';
                if (!$product->getSpecialPrice()) {
                    $formattedPrice = $this->_priceHelper->currency($product->getPrice(), true, false);
                    $formattedPricehtml = str_ireplace(
                        $keyword,
                        '<span class="mgantsprice price">'.$keyword.'</span>',
                        $formattedPrice
                    );
                } else {
                    $specialprice = $product->getSpecialPrice();
                    $specialfromdate = $product->getSpecialFromDate();
                    $specialtodate = $product->getSpecialToDate();
                    $today = time();
                    if ((($specialfromdate=== null) &&
                        ($specialtodate === null)) ||
                        ($today >=strtotime($specialfromdate) &&
                        ($specialtodate=== null)) ||
                        ($today <= strtotime($specialtodate) &&
                        ($specialfromdate=== null )) ||
                        ($today >=strtotime($specialfromdate) &&
                        $today <= strtotime($specialtodate))) {
                        $formattedPrice = $this->_priceHelper->currency($product->getSpecialPrice(), true, false);
                        $formattedRegularPrice = $this->_priceHelper->currency($product->getPrice(), true, false);
                        $regularPricetxt = 'Regular Price '.$formattedRegularPrice;
                    } else {
                        $formattedPrice = $this->_priceHelper->currency($product->getPrice(), true, false);
                    }
                    
                }

                $product_url = $product->getProductUrl();
                $product_type = $product->getTypeId();
                $qty = $this->_stockItem->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
                
                $productName = str_ireplace(
                    $keyword,
                    '<span class="mgantshighlight">'
                    .$keyword.'</span>',
                    $product->getName()
                );
                
                $productSku = str_ireplace($keyword, '
                    <span class="mgantshighlight">'.$keyword.'
                    </span>', $product->getSku());
                
                //----------Tier Price Start-----------
                $tierPriceQty=0;
                $tier_price=0;
                $tierPriceUpdate=[];
                $tierPriceProduct = $this->productRepository->get($product->getSku());
                $customerGroupId = $this->_customerSession->create()->getCustomerGroupId();
                $allTiers = $tierPriceProduct->getData('tier_price');
                $tier_price_array = [];
                if ($allTiers) {

                    foreach ($allTiers as $key => $value) {
                        $tierPriceQty = (int)$value['price_qty'];

                        if ($value['cust_group'] == $customerGroupId || $value['cust_group'] == 32000) {
                              
                            $this->setQty($tier_price, $value);
                         
                            $tierPriceUpdate[$tierPriceQty]=$tier_price;
                            $price = $value['price'];
                            $formattedTierPrice = $this->_pricingHelper
                           ->currency(number_format($price, 2), true, false);

                            $savePercentageFormat = ceil(100 - ((100 /
                            $tierPriceProduct->getPrice())* $value['price'])) ."%";
                            $tier_price_array[] = "Buy $tierPriceQty 
                        for ".$formattedTierPrice." each and 
                        save ".$savePercentageFormat;
                        }
                    }
                }
                if (count($tierPriceUpdate) !=0) {
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
                if (isset($tier_price_array)) {
                    $tier_price_string=implode('<br>', $tier_price_array);
                } else {
                    $tier_price_string = '';
                }

                //----------Tier Price End-----------

                $items[] = ["qty"=>1,"product_price"=>$formattedPrice,
                "regular_price"=>$regularPricetxt,"popup"=>$popup,
                "product_name"=>$productName,
                "product_sku"=>$product->getSku(),
                'product_sku_highlight'=>$productSku,
                "product_id"=>$product->getId(),
                "product_thumbnail"=>$img,
                "product_price_amount"=>$product->getPrice(),
                "product_url"=>$product_url,
                "product_type"=>$product_type,
                "product_tier_price"=>$tier_price_string,
                "tierPriceUpdate"=>$tierPriceUpdate];
            }
            $result = $items;
        }
        $this->getResponse()->setBody(json_encode($result));
    }
    /**
     * Set tier price
     *
     * @param int|string $tier_price
     * @param int|string $value
     */
    public function setQty($tier_price, $value)
    {
       
        if ($value['website_id'] == 0) {
                                $tier_price= $value['price'];
        } else {
            $tier_price =$value['website_price'];
        }
    }
}
