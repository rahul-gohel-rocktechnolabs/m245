<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Ui\DataProvider;

class Attributes extends \Magento\ConfigurableProduct\Ui\DataProvider\Attributes
{
    /**
     *
     * @param mixed $name
     * @param mixed $primaryFieldName
     * @param mixed $requestFieldName
     * @param \Mageants\Orderattribute\Model\OrderAttributeHandler $configurableAttributeHandler
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Mageants\Orderattribute\Model\OrderAttributeHandler $configurableAttributeHandler,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $configurableAttributeHandler, $meta, $data);
        $this->configurableAttributeHandler = $configurableAttributeHandler;
         $this->collection = $configurableAttributeHandler->getApplicableAttributes();
    }

     /**
      * Getting the product attribute collection.
      *
      * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
      */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $items = [];
        $skippedItems = 0;
        foreach ($this->getCollection()->getItems() as $attribute) {
            if ($this->configurableAttributeHandler->isAttributeApplicable($attribute)) {
                $items[] = $attribute->toArray();
            } else {
                $skippedItems++;
            }
        }
        return [
            'totalRecords' => $this->collection->getSize()- $skippedItems,
            'items' => $items
        ];
    }
}
