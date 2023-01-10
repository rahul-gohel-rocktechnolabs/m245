<?php
/**
 * @category Mageants CallForPrice
 * @package Mageants_CallForPrice
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants <support@mageants.com>
 */
namespace Mageants\GoogleCustomerReviews\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Helper\Category;

/**
 * Custom Attribute Renderer
 */
class Customerlist implements ArrayInterface
{
    /**
     * Catagory Helper
     *
     * @var \Magento\Catalog\Helper\Category
     */
    public $categoryHelper;

    /**
     * Group collection
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    public $groupcollection;
    /**
     * @param \Magento\Catalog\Helper\Category
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    public function __construct(
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Customer\Model\ResourceModel\Group\Collection $groupCollection
    ) {
        $this->groupcollection=$groupCollection;
        $this->categoryHelper = $catalogCategory;
    }
    
    /**
     * Prepare Option Array
     *
     * @return Array
     */
    public function toOptionArray()
    {
        $groupOptions = $this->groupcollection->toOptionArray();
        foreach ($groupOptions as $group) {
            $ret[] = [
                'value' => $group['value'],
                'label' => $group['label']
            ];
        }
        return $ret;
    }
}
