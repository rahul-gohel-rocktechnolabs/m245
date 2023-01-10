<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel\Quote\Address;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;

class Relation implements RelationInterface
{

    /**
     * @var \Mageants\Orderattribute\Model\OrderAttributesManagement
     */
    private $orderAttributesManager;

    /**
     * Relation constructor.
     *
     * @param \Mageants\Orderattribute\Model\OrderAttributesManagement $orderAttributesManager
     */
    public function __construct(
        \Mageants\Orderattribute\Model\OrderAttributesManagement $orderAttributesManager
    ) {
        $this->orderAttributesManager = $orderAttributesManager;
    }

    /**
     * Process relation
     *
     * @param AbstractModel $object
     */
    public function processRelation(AbstractModel $object)
    {
        $attributes = $object->getOrderAttributes();
        $this->orderAttributesManager->saveAttributesFromQuote($object->getQuote()->getId(), $attributes);
    }
}
