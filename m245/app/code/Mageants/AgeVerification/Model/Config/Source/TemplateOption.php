<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Model\Config\Source;

class TemplateOption implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory
     */
    public $collection;

    /**
     * @param \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory $collection
     */

    public function __construct(
        \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory $collection
    ) {
        $this->collection = $collection;
    }
   /**
    * Undocumented function
    *
    * @return void
    */
    public function toOptionArray()
    {
        $res = [];
        // echo "<pre>";
        // var_dump(get_class_methods($this->collection->create()->getData()));
        // exit();
        $collection = $this->collection->create();
        $collection->addFieldToFilter('status', ['eq'=> 1]);
        $data['value'] = 0;
        $data['label'] = 'Default';
        $res[] = $data;
        foreach ($collection as $page) {
            $data['value'] = $page->getData('template_id');
            $data['label'] = $page->getData('template_name');
            $res[] = $data;
        }

        return $res;
    }
}
