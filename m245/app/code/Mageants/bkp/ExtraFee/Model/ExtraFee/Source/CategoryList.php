<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ExtraFee\Model\ExtraFee\Source;

use Magento\Framework\Controller\ResultFactory;
use \Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Category;

class CategoryList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * To get Category using Category factory
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
     * @param CategoryFactory $categoryFactory
     * @param Category $categories
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        Category $categories
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_categories = $categories;
    }
  
    /**
     * To set Option Array
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
                $options[$template->getEntityId()] = [
                    'value'=>$template->getEntityId(),
                    'label'=>$cat->getName()
                ];
                
            }
        }
        return $options;
    }
}
