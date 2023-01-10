<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Relation extends AbstractDb
{
    /**
     * @var RelationDetails\CollectionFactory
     */
    private $detailFactory;

    /**
     * @var RelationDetails
     */
    private $detailResource;

    /**
     * Relation constructor.
     *
     * @param Context                           $context
     * @param RelationDetails\CollectionFactory $detailFactory
     * @param RelationDetails                   $detailResource
     * @param null                              $connectionName
     */
    public function __construct(
        Context $context,
        \Mageants\Orderattribute\Model\ResourceModel\RelationDetails\CollectionFactory $detailFactory,
        \Mageants\Orderattribute\Model\ResourceModel\RelationDetails $detailResource,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->detailFactory = $detailFactory;
        $this->detailResource = $detailResource;
    }

    /**
     * Construct
     */
    public function _construct()
    {
        $this->_init('mageants_orderattribute_attributes_relation', 'relation_id');
    }

    /**
     * Get details
     *
     * @param int $relationId
     * @return \Mageants\Orderattribute\Api\Data\RelationDetailInterface[]
     */
    public function getDetails($relationId)
    {
        /** @var RelationDetails\Collection $detailsCollection */
        $detailsCollection = $this->detailFactory->create();
        $detailsCollection->getByRelation($relationId);

        return $detailsCollection->getItems();
    }

    /**
     * Save relation details
     *
     * @param \Magento\Framework\Model\AbstractModel|\Mageants\Orderattribute\Api\Data\RelationInterface $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->hasData('relation_details')) {
            $this->detailResource->deleteAllDetailForRelation($object->getRelationId());
            foreach ($object->getDetails() as $detail) {
                $detail->setRelationId($object->getRelationId());
                $this->detailResource->save($detail);
            }
        }

        return parent::_afterSave($object);
    }
}
