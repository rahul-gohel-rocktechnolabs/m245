<?php

/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\AdvancedSearch\Model\Search;

use Magento\Store\Model\StoreManagerInterface;
use \Mageants\AdvancedSearch\Helper\Data as HelperData;
use \Magento\Search\Helper\Data as SearchHelper;
use \Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use \Magento\Search\Model\QueryFactory;
use \Mageants\AdvancedSearch\Model\Source\AutocompleteFields;
use \Mageants\AdvancedSearch\Model\Source\ProductFields;
use \Magento\Review\Model\ResourceModel\Review\SummaryFactory;
use \Mageants\AdvancedSearch\Block\Autocomplete\ProductAgregator;

class Product implements \Mageants\AdvancedSearch\Model\SearchInterface
{
    /**
     * @var \Mageants\AdvancedSearch\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $searchHelper;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $layerResolver;

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    private $queryFactory;

    /**
     * @var SummaryFactory
     */
    private $sumResourceFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductAgregator
     */
    protected $productAgregator;

    /**
     * Product constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param SummaryFactory $sumResourceFactory
     * @param HelperData $helperData
     * @param SearchHelper $searchHelper
     * @param LayerResolver $layerResolver
     * @param QueryFactory $queryFactory
     * @param ProductAgregator $productAgregator
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        SummaryFactory $sumResourceFactory,
        HelperData $helperData,
        SearchHelper $searchHelper,
        LayerResolver $layerResolver,
        QueryFactory $queryFactory,
        ProductAgregator $productAgregator
    ) {
        $this->storeManager       = $storeManager;
        $this->sumResourceFactory = $sumResourceFactory;
        $this->helperData         = $helperData;
        $this->searchHelper       = $searchHelper;
        $this->layerResolver      = $layerResolver;
        $this->queryFactory       = $queryFactory;
        $this->productAgregator   = $productAgregator;
    }

    /**
     * @inheritdoc
     */
    public function getResponseData()
    {
        $responseData['code'] = AutocompleteFields::PRODUCT;
        $responseData['data'] = [];

        if (!$this->canAddToResult()) {
            return $responseData;
        }

        $query                 = $this->queryFactory->get();
        $queryText             = $query->getQueryText();
        $productResultFields   = $this->helperData->getProductResultFieldsAsArray();
        $productResultFields[] = ProductFields::URL;

        $productCollection = $this->getProductCollection($queryText);

        foreach ($productCollection as $product) {
            $responseData['data'][] = array_intersect_key(
                $this->getProductData($product),
                array_flip($productResultFields)
            );
        }

        $responseData['size'] = $productCollection->getSize();
        $responseData['url']  = ($productCollection->getSize() > 0) ? $this->searchHelper->getResultUrl(
            $queryText
        ) : '';

        $query->saveNumResults($responseData['size']);
        $query->saveIncrementalPopularity();

        return $responseData;
    }

    /**
     * Retrive product collection by query text
     *
     * @param string $queryText
     * @return mixed
     */
    protected function getProductCollection($queryText)
    {
        $productResultNumber = $this->helperData->getProductResultNumber();

        $this->layerResolver->create(LayerResolver::CATALOG_LAYER_SEARCH);

        $productCollection = $this->layerResolver->get()
                                                 ->getProductCollection()
                                                 ->addAttributeToSelect(
                                                     [ProductFields::DESCRIPTION, ProductFields::SHORT_DESCRIPTION]
                                                 )
                                                 ->setPageSize($productResultNumber)
                                                 ->addAttributeToSort('relevance')
                                                 ->setOrder('relevance');
        /** @var \Magento\Review\Model\ResourceModel\Review\Summary $sumResource */
        $sumResource = $this->sumResourceFactory->create();
        return $productCollection;
    }

    /**
     * Get Store Id
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Retrieve all product data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function getProductData($product)
    {
        /** @var \Mageants\AdvancedSearch\Block\Autocomplete\ProductAgregator $productAgregator */
        $productAgregator = $this->productAgregator->setProduct($product);

        $data = [
            ProductFields::NAME              => $productAgregator->getName(),
            ProductFields::SKU               => $productAgregator->getSku(),
            ProductFields::IMAGE             => $productAgregator->getSmallImage(),
            ProductFields::REVIEWS_RATING    => $productAgregator->getReviewsRating(),
            ProductFields::SHORT_DESCRIPTION => $productAgregator->getShortDescription(),
            ProductFields::DESCRIPTION       => $productAgregator->getDescription(),
            ProductFields::PRICE             => $productAgregator->getPrice(),
            ProductFields::CATEGORY          => $productAgregator->category($productAgregator->getSku())
        ];
        $url = $this->helperData->urlredirect();
        if ($url== 1) {
            $data[ProductFields::URL] = $productAgregator->getUrl();
        }
        if ($product->getData('is_salable')) {
            $data[ProductFields::ADD_TO_CART] = $productAgregator->getAddToCartData();
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function canAddToResult()
    {
        return in_array(AutocompleteFields::PRODUCT, $this->helperData->getAutocompleteFieldsAsArray());
    }
}
