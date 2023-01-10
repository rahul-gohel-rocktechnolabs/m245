<?php
/**
  * @category   Mageants SampleProduct
  * @package    Mageants_SampleProduct
  * @copyright  Copyright (c) 2017 Mageants
  * @author     Mageants Team <support@Mageants.com>
  */
namespace Mageants\SampleProduct\Block\Frontend\Catalog\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Sample extends Template
{

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Product
     */
    private $product;


    public function __construct(Template\Context $context,
      Registry $registry,
      array $data=[]
    )
    {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }
    /**
     * @return Product
     */
    private function getProduct()
    {
        if (is_null($this->product)) {
            $this->product = $this->registry->registry('product');

            if (!$this->product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }

        return $this->product;
    }

    public function IsSampleProduct()
    {       
        return $this->getProduct()->getOfferSample();
    }

    public function SampleProductID()
    {       
        return $this->getProduct()->getId();
    }
    
    public function SampleProductMaxQty()
    {       
        return $this->getProduct()->getOfferSampleMaxQty();
    }
        public function getOfferSampleCost()
    {       
        return $this->getProduct()->getOfferSampleCost();
    }

}