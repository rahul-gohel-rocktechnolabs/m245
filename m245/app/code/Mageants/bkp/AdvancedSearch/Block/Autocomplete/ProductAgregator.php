<?php

/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\AdvancedSearch\Block\Autocomplete;

use \Mageants\AdvancedSearch\Block\Product as ProductBlock;
use \Magento\Catalog\Helper\Output as CatalogHelperOutput;
use \Magento\Catalog\Block\Product\ReviewRendererInterface;
use \Magento\Framework\Stdlib\StringUtils;
use \Magento\Framework\Url\Helper\Data as UrlHelper;
use \Magento\Framework\Data\Form\FormKey;
use \Magento\Framework\View\Asset\Repository;
use \Magento\Framework\Escaper;
use Magento\Catalog\Helper\ImageFactory;

class ProductAgregator extends \Magento\Framework\DataObject
{
    /**
     * @var \Mageants\AdvancedSearch\Block\Product
     */
    protected $productBlock;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var CatalogHelperOutput
     */
    protected $catalogHelperOutput;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var ImageFactory
     */
    private $imageFactory;
    /**
     * @var \Mageants\AdvancedSearch\Helper\Data
     */
    protected $helperData;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $StoreManagerInterface;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    public $collectionfactory;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    public $productrepository;
    /**
     * ProductAgregator constructor
     *
     * @param \Magento\Store\Model\StoreManagerInterface $StoreManagerInterface
     * @param \Mageants\AdvancedSearch\Helper\Data $helperData
     * @param ImageFactory $imageFactory
     * @param ProductBlock $productBlock
     * @param StringUtils $string
     * @param UrlHelper $urlHelper
     * @param Repository $assetRepo
     * @param CatalogHelperOutput $catalogHelperOutput
     * @param FormKey $formKey
     * @param Escaper $escaper
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionfactory
     * @param \Magento\Catalog\Model\ProductRepository $productrepository
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $StoreManagerInterface,
        \Mageants\AdvancedSearch\Helper\Data $helperData,
        ImageFactory $imageFactory,
        ProductBlock $productBlock,
        StringUtils $string,
        UrlHelper $urlHelper,
        Repository $assetRepo,
        CatalogHelperOutput $catalogHelperOutput,
        FormKey $formKey,
        Escaper $escaper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionfactory,
        \Magento\Catalog\Model\ProductRepository $productrepository
    ) {
        $this->helperData          = $helperData;
        $this->imageFactory        = $imageFactory;
        $this->productBlock        = $productBlock;
        $this->string              = $string;
        $this->urlHelper           = $urlHelper;
        $this->assetRepo           = $assetRepo;
        $this->catalogHelperOutput = $catalogHelperOutput;
        $this->formKey             = $formKey;
        $this->escaper             = $escaper;
        $this->storeManagerInterface = $StoreManagerInterface;
        $this->collectionfactory = $collectionfactory;
        $this->productrepository = $productrepository;
    }

    /**
     * Retrieve product name
     *
     * @return string
     */
    public function getName()
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        return strip_tags(html_entity_decode($this->getProduct()->getName()));
    }

    /**
     * Retrieve product sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getProduct()->getSku();
    }

    /**
     * Retrieve product small image url
     *
     * @return bool|string
     */
    public function getSmallImage()
    {
        $product   = $this->getProduct();

        $image = $this->imageFactory->create()->init($product, 'product_small_image');

        return $image->getUrl();
    }

    /**
     * Retrieve product reviews rating html
     *
     * @return string
     */
    public function getReviewsRating()
    {
        return $this->productBlock->getReviewsSummaryHtml(
            $this->getProduct(),
            ReviewRendererInterface::SHORT_VIEW,
            true
        );
    }

    /**
     * Retrieve product short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $shortDescription = html_entity_decode($this->getProduct()
            ->getShortDescription() ?? '');

        return $this->cropDescription($shortDescription);
    }

    /**
     * Retrieve product description
     *
     * @return string
     */
    public function getDescription()
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $description = html_entity_decode($this->getProduct()->getDescription()?? '');

        return $this->cropDescription($description);
    }

    /**
     * Crop description to 50 symbols
     *
     * @param string $html
     * @return string
     */
    protected function cropDescription($html)
    {
         $size = $this->helperData->decriptionlength();
        $string = strip_tags($html);
        $string = (strlen($string) > $size) ? $this->string->substr($string, 0, $size) . '...' : $string;

        return $string;
    }

    /**
     * Retrieve product price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->productBlock->getProductPrice(
            $this->getProduct(),
            \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE
        );
    }

    /**
     * Retrieve product url
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->productBlock->getProductUrl($this->getProduct());
    }

    /**
     * Retrieve product add to cart data
     *
     * @return array
     */
    public function getAddToCartData()
    {
        $formUrl             = $this->productBlock->getAddToCartUrl(
            $this->getProduct(),
            ['Mageants_AdvancedSearch' => true]
        );
        $productId           = $this->getProduct()->getEntityId();
        $paramNameUrlEncoded = \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED;
        $urlEncoded          = $this->urlHelper->getEncodedUrl($formUrl);
        $formKey             = $this->formKey->getFormKey();
        $addToCartData = [
            'formUrl'             => $formUrl,
            'productId'           => $productId,
            'paramNameUrlEncoded' => $paramNameUrlEncoded,
            'urlEncoded'          => $urlEncoded,
            'formKey'             => $formKey,

        ];
        return $addToCartData;
    }
    /**
     * Get RootCategory
     */
    public function getRootCategoryId()
    {
        return $this->storeManagerInterface->getStore()->getRootCategoryId();
    }
    /**
     * Get Category
     *
     * @param mixed $text
     */
    public function category($text)
    {
        $categoryCollection = $this->collectionfactory;
        $productRepository = $this->productrepository;

        $productId =$text;

        $product = $productRepository->get($productId);

        $categoryIds = $product->getCategoryIds();

        foreach ($categoryIds as $k => $categorys) {
            $root = 2;
            if ($categorys == $root) {
                unset($categoryIds[$k]);
            }
        }
        $categories = $categoryCollection->create()
                     ->addAttributeToSelect('*')
                     ->addAttributeToFilter('entity_id', $categoryIds);
        $cata = [];
        foreach ($categories as $categorys) {
            $cata1[] = $categorys->getUrl();
            $cata[] = $categorys->getName();
        }

          $lenght =  count($cata1);
        for ($i=0; $i < $lenght; $i++) {
            $collectionProduct[] = [ 'url'=>"$cata1[$i]",'name'=>"$cata[$i]"];

        }

        return $collectionProduct;
    }
}
