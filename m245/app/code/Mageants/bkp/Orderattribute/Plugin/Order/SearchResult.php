<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Plugin\Order;

class SearchResult
{
    /**
     * @var \Mageants\Orderattribute\Helper\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var $columns
     */
    protected $columns;

    /**
     * @param \Mageants\Orderattribute\Helper\Config $config
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Mageants\Orderattribute\Helper\Config $config,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->config = $config;
        $this->resource = $resource;
    }

    /**
     * After get select
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $collection
     * @param object $select
     * @return object
     */
    public function afterGetSelect(
        \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $collection,
        $select
    ) {
        if ((string) $select == "") {
            return $select;
        }

        $attributeFieldTableName = $collection->getTable(
            'mageants_orderattribute_order_attribute_value'
        );
        if (!$this->columns) {
            $connection = $this->resource->getConnection();
            $fields = $connection->describeTable($attributeFieldTableName);
            unset($fields['created_at']);
            $tmp = [];
            foreach ($fields as $field => $value) {
                $tmp[] = 'mgorderattribute.' . $field;
            }

            $this->columns = $tmp;
        }

        if ($collection->getResource() instanceof \Magento\Sales\Model\ResourceModel\Order) {
            if (!array_key_exists('mgorderattribute', $select->getPart('from'))) {
                $select->joinLeft(
                    ['mgorderattribute' => $attributeFieldTableName],
                    'main_table.entity_id = mgorderattribute.order_entity_id',
                    $this->columns
                );
            }
        }

        if ($collection->getResource() instanceof \Magento\Sales\Model\ResourceModel\Order\Invoice) {
            if ($this->config->getShowInvoiceGrid()
                && !array_key_exists('mgorderattribute', $select->getPart('from'))
                && strpos($select, 'COUNT') === false
            ) {
                $select->joinLeft(
                    ['mgorderattribute' => $attributeFieldTableName],
                    'main_table.order_id = mgorderattribute.order_entity_id',
                    $this->columns
                );
            }
        }

        if ($collection->getResource() instanceof \Magento\Sales\Model\ResourceModel\Order\Shipment) {
            if ($this->config->getShowShipmentGrid()
                && !array_key_exists('mgorderattribute', $select->getPart('from'))
                && strpos($select, 'COUNT') === false) {
                $select->joinLeft(
                    ['mgorderattribute' => $attributeFieldTableName],
                    'main_table.order_id = mgorderattribute.order_entity_id',
                    $this->columns
                );
            }
        }

        $where = $select->getPart('where');
        foreach ($where as &$item) {
            if (strpos($item, '(`created_at`') !== false) {
                $item = str_replace('`created_at`', '`main_table`.`created_at`', $item);
            }
            if (strpos($item, '(`customer_id`') !== false) {
                $item = str_replace('`customer_id`', '`main_table`.`customer_id`', $item);
            }
        }
        $select->setPart('where', $where);

        $order = $select->getPart('order');
        foreach ($order as &$item) {
            if (strpos($item, 'created_at') !== false
                && strpos($item, 'main_table.created_at') === false
            ) {
                $item = str_replace('created_at', 'main_table.created_at', $item);
                $item = new \Zend_Db_Expr($item);
            }
        }
        $select->setPart('order', $order);

        return $select;
    }
}
