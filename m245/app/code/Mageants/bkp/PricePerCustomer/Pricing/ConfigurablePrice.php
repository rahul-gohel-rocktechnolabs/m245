<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Pricing;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\ConfigurableProduct\Pricing\Price\PriceResolverInterface;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;
use Magento\Framework\Pricing\SaleableInterface;

class ConfigurablePrice
{
    /**
     * [$_moduleManager description]
     * @var [type]
     */
    protected $_moduleManager;

    /**
     * [$_jsonEncoder description]
     * @var [type]
     */
    protected $_jsonEncoder;

    /**
     * [$_registry description]
     * @var [type]
     */
    protected $_registry;

    /**
     * Construct
     *
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_registry = $registry;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->_configurableType = $configurableType;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->stockState = $stockState;
    }

    /**
     * Resolve Price
     *
     * @param subject $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Pricing\SaleableInterface|\Magento\Catalog\Model\Product $product
     * @return float
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundResolvePrice($subject, \Closure $proceed, $product)
    {
        $price = null;
        //get parent product id
        $parentId = $product['entity_id'];
        $childObj = $this->getChildProductObj($parentId);
        foreach ($childObj as $childs) {
            $productPrice = $childs;
            $price = $price ? min($price, $productPrice) : $productPrice;
        }
        return $price;
    }

    /**
     * Get ProductInfo
     *
     * @param  id $id
     * @return id
     */
    public function getProductInfo($id)
    {
        //get product obj using api repository...
        if (is_numeric($id)) {
            return $this->productRepository->getById($id);
        } else {
            return;
        }
    }

    /**
     * Get ChildProductObj
     *
     * @param  id $id
     * @return id
     */
    public function getChildProductObj($id)
    {
        $product = $this->getProductInfo($id);
        //if quote with not proper id then return null and exit;
        if (!isset($product)) {
            return;
        }

        if ($product->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return [];
        }

        $storeId = 1;
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter($storeId, $product);
        $childrenList = [];
        $childprice = [];

        foreach ($productTypeInstance->getUsedProducts($product) as $child) {
            $attributes = [];
            $isSaleable = $child->isSaleable();
            $childprice[$child->getId()] = $child->getPrice();
        }
        return $childprice;
    }
}
