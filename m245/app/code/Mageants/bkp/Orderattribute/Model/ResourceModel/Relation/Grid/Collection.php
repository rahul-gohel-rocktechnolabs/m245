<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel\Relation\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
//use Magento\Sales\Ui\Component\DataProvider\Document;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{

    /**
     * @var $_map
     */
    protected $_map = [
        'parent_label' => 'parent.frontend_label',
        'attribute_codes' => 'dependent.attribute_code',
    ];

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger        $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager  $eventManager
     * @param string        $mainTable
     * @param string        $resourceModel
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'mageants_orderattribute_attributes_relation',
        $resourceModel = \Mageants\Orderattribute\Model\ResourceModel\Relation::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }
    
    /**
     * Init Select for Relation Grid
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->joinInner(
                ['detail' => $this->getTable('mageants_orderattribute_attributes_relation_details')],
                'main_table.relation_id = detail.relation_id',
                ['detail.attribute_id']
            )
            ->joinInner(
                ['attribute' => $this->getTable('mageants_orderattribute_order_eav_attribute')],
                'attribute.attribute_id = detail.attribute_id',
                ['attribute.checkout_step']
            )
            ->joinInner(
                ['parent' => $this->getTable('eav_attribute')],
                'detail.attribute_id = parent.attribute_id',
                [
                    'parent.frontend_label as parent_label',
                    'CONCAT(parent.attribute_code, ",", GROUP_CONCAT(dependent.attribute_code)) as attribute_codes',
                ]
            )
            ->joinInner(
                ['dependent' => $this->getTable('eav_attribute')],
                'detail.dependent_attribute_id = dependent.attribute_id',
                ['GROUP_CONCAT(dependent.frontend_label) as dependent_label']
            )
            ->group('main_table.relation_id');

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function beforeAddLoadedItem(\Magento\Framework\DataObject $item)
    {
        if ($item->getDependentLabel()) {
            $labels = implode(', ', array_unique(explode(',', $item->getDependentLabel())));
            $item->setDependentLabel($labels);
        }
        if ($item->getAttributeCodes()) {
            $labels = implode(', ', array_unique(explode(',', $item->getAttributeCodes())));
            $item->setAttributeCodes($labels);
        }

        return parent::beforeAddLoadedItem($item);
    }
}
