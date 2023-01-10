<?php

namespace Mageants\ImageGallery\Model\Config;

class Category implements \Magento\Framework\Option\ArrayInterface
{

     /**
      * @var \Webspeaks\BannerSlider\Model\sliderFactory
      */
    protected $_categoryFactory;
    
    /**
     * @param \Mageants\ImageGallery\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Mageants\ImageGallery\Model\CategoryFactory $categoryFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->toArray() as $key => $val) {
            $optionArray[] = [ 'value' => $key , 'label' => $val];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $group = [];
        $collection = $this->_categoryFactory->create()->getCollection();
        
        foreach ($collection as $category) {
            $group[$category->getId()] = $category->getCategoryName();
        }
        return $group;
    }
}
