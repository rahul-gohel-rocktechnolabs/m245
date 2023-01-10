<?php
/**
 * @category Mageants OrderApprovalRules
 * @package Mageants_OrderApprovalRules
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants <support@mageants.com>
 */

namespace Mageants\OrderApprovalRules\Model\Source;

use Magento\Framework\Controller\ResultFactory;

/**
 * Return Category List
 */
class CategoryList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Factory of category
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;
  
    /**
     * category
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $_categories;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Category $categories
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Category $categories
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_categories = $categories;
    }
  
    /**
     * Create options array
     *
     * @return Array
     */
    public function toOptionArray()
    {
        $collection = $this->_categoryFactory->create()->getCollection()->addFieldToFilter('is_active', 1);
        $options=[];
        if ($collection->getSize()) {
            foreach ($collection as $template) {
                $cat=$this->_categories->load($template->getEntityId());
                $options[$template->getEntityId()]=['value'=>$template->getEntityId(),'label'=>$cat->getName()];
                
            }
        }
        return $options;
    }
}
