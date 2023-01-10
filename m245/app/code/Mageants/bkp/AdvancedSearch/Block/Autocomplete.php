<?php
/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvancedSearch\Block;

use Mageants\AdvancedSearch\Model\ResourceModel\Recent\Collection;
use \Magento\Search\Helper\Data as SearchHelper;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Autocomplete extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageants\AdvancedSearch\Helper\Data
     */
    protected $helperData;
    /**
     * @var Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory
     */
    protected $_bestSellersCollectionFactory;
    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $storeManager;
   
    /**
     * Autocomplete constructor
     *
     * @param \Mageants\AdvancedSearch\Helper\Data $helperData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param Collection $collection
     * @param SearchHelper $searchHelper
     * @param BestSellersCollectionFactory $bestSellersCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     * @param array $data
     */
    public function __construct(
        \Mageants\AdvancedSearch\Helper\Data $helperData,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        Collection $collection,
        SearchHelper $searchHelper,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        array $data = []
    ) {

        $this->helperData        = $helperData;
        $this->imageHelper       = $imageHelper;
        $this->productRepository = $productRepository;
        $this->collection        = $collection;
        $this->searchHelper      = $searchHelper;
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->storeManage = $context->getStoreManager();
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($context, $data);
    }
    /**
     * Get Retrieve search delay
     *
     * @return $this
     */
    public function getSearchDelay()
    {
        return $this->helperData->getSearchDelay();
    }

    /**
     * Retrieve search action url
     *
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->getUrl("Mageants_AdvancedSearch/ajax/index");
    }

    /**
     * Get popup Width
     *
     * @return $this
     */
    public function popupwidth()
    {
        return $this->helperData->popupwidth();
    }
    /**
     * Get Searchbar Type
     *
     * @return $this
     */
    public function searchtype()
    {
         return $this->helperData->searchtype();
    }
     /**
      * Get Number to start search process
      *
      * @return int
      */
    public function minimumsearch()
    {
         return $this->helperData->minimumsearch();
    }
     /**
      * Get Product Block Title
      *
      * @return $this
      */
    public function producttitle()
    {
         return $this->helperData->producttitle();
    }
     /**
      * Get categort Block Title
      *
      * @return $this
      */
    public function categorytitle()
    {
         return $this->helperData->categorytitle();
    }
     /**
      * Get Suggestion title
      *
      * @return $this
      */
    public function suggestiontitle()
    {
         return $this->helperData->suggestiontitle();
    }
     /**
      * Get Bestseller title
      *
      * @return $this
      */
    public function bestsellertitle()
    {
         return $this->helperData->bestsellertitle();
    }
     /**
      * Get Recent title
      *
      * @return $this
      */
    public function recenttitle()
    {
         return $this->helperData->recenttitle();
    }
     /**
      * Get a value to show number of recent search
      *
      * @return $this
      */
    public function recentnumber()
    {
        return $this->helperData->recentnumber();
    }
    /**
     * Get value to recent enable/disable
     *
     * @return $this
     */
    public function recentenable()
    {
        return $this->helperData->recentenable();
    }
     /**
      * Value to Show popup on first click
      *
      * @return $this
      */
    public function firstclick()
    {
        return $this->helperData->firstclick();
    }
     /**
      * Get Value to Apply to CustomLayout
      *
      * @return $this
      */
    public function customlayoutenable()
    {
        return $this->helperData->customlayoutenable();
    }
     /**
      * Get Value for popup Background color
      *
      * @return $this
      */
    public function backgroundcolor()
    {
        return $this->helperData->backgroundcolor();
    }
     /**
      * Get Value for popup border color
      *
      * @return $this
      */
    public function bordercolor()
    {
        return $this->helperData->bordercolor();
    }
     /**
      * Get Value for popup hover color
      *
      * @return $this
      */
    public function hovercolor()
    {
        return $this->helperData->hovercolor();
    }
     /**
      * Get Value for popup text color
      *
      * @return $this
      */
    public function textcolor()
    {
        return $this->helperData->textcolor();
    }
     /**
      * Get Value for popup hover color
      *
      * @return $this
      */
    public function hovertext()
    {
        return $this->helperData->hovertext();
    }
     /**
      * Get recent search product url
      *
      * @param mixed $url
      * @return $this
      */
    public function getrecenturl($url)
    {
        $item = $this->searchHelper->getResultUrl($url);
        return $item;
    }
     /**
     * Get bestseller Length
     *
     * @return $this
     */
    public function len()
    {
        return  $this->helperData->bestsellernumber();
    }
     /**
      * Get Bestseller product
      *
      * @return $this
      */
    public function getBestSellerData()
    {
        $name=[];
        $bestSellerProColl = $this->_bestSellersCollectionFactory->create()
           ->setPeriod('year');
           $store = $this->storeManage->getStore();
        foreach ($bestSellerProColl as $value) {

            $product_id =  $value['product_id'];
            $name[] =  $value['product_name'];
            $price[] =  $value['product_price'];
            $product = $this->productRepository->getById($product_id);
            $productImageUrl = $store
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
            'catalog/product' .$product->getImage();
            $productUrl[] = $product->getProductUrl();

            $_product = $this->productRepository->getById($product_id);
            $image_url[] = $this->imageHelper->init($_product, 'product_base_image')->getUrl();
        }
            $Bestsellers = [];
            $result1 = [];
            $length = $this->helperData->bestsellernumber();
        foreach (array_slice($name, 0, $length) as $box1) {
            $result1[] = $box1;
        }
           $lengthnumber = count($result1);
        for ($i=0; $i < $lengthnumber; $i++) {
            $Bestsellers[] = [ 'productUrl'=>"$productUrl[$i]",'image_url'=>"
            $image_url[$i]",'name'=>"$name[$i]",'price'=>"$price[$i]"];
        }
           return $Bestsellers;
    }
     /**
      * Get value to bestseller enable/disable
      *
      * @return $this
      */
    public function bestsellerenable()
    {
        return $this->helperData->bestsellerenable();
    }
     /**
      * Get flushCache
      *
      * @return $this
      */
    public function flushCache()
    {
        $types = ['config','layout','full_page'];
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
     /**
      * Get Recentsearch value
      *
      * @return $this
      */
    public function getrecentsearch()
    {
        return $this->collection;
    }
     /**
      * Get Product length value
      *
      * @return $this
      */
    public function productLength()
    {
       return $this->helperData->getProductResultNumber();
    }
}
