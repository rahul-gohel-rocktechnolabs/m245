<?php
/**
 * @category   Mageants SampleProduct
 * @package    Mageants_SampleProduct
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\SampleProduct\Block\Frontend\Catalog\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class ListSample extends Template
{

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Product
     */
    private $product;
    protected $productRepository;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }
    /**
     * @return Product
     */
    private function getProduct($id)
    {
        return $this->productRepository->getById($id);
    }

    public function IsSampleProduct($id)
    {
        return $this->getProduct($id)->getOfferSample();
    }

    public function getOfferSampleCost($id)
    {
        return $this->getProduct($id)->getOfferSampleCost();
    }
}
