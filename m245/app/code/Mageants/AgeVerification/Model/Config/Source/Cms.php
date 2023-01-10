<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
// @codingStandardsIgnoreFile
namespace Mageants\AgeVerification\Model\Config\Source;

class Cms implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    public $collection;

    /**
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collection
     */

    public function __construct(
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collection
    ) {
        $this->collection = $collection;
    }
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $res = [];
        $collection = $this->collection->create();
        $collection->addFieldToFilter('is_active', \Magento\Cms\Model\Page::STATUS_ENABLED);
        foreach ($collection as $page) {
            $data['value'] = $page->getData('identifier');
            $data['label'] = $page->getData('title');
            $res[] = $data;
        }
        return $res;
    }
}
