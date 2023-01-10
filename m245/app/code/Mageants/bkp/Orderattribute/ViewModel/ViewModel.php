<?php

namespace Mageants\Orderattribute\ViewModel;

class ViewModel implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    private $helperData;

    /**
     * @param \Magento\Catalog\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Catalog\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }
    /**
     * Get Hepler class
     */
    public function getHeplerclass()
    {
        return $this->helperData;
    }
}
