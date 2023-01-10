<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model;

use Mageants\Orderattribute\Api\Data\RelationDetailInterface;
use Mageants\Orderattribute\Api\Data\RelationInterface;

/**
 * @method ResourceModel\Relation _getResource
 * @method ResourceModel\Relation getResource
 */
class Relation extends \Magento\Framework\Model\AbstractModel implements RelationInterface
{
    /**
     * @var \Mageants\Orderattribute\Model\RelationDetailsFactory
     */
    private $detailsFactory;

    /**
     * @var $datailsChanged
     */
    protected $datailsChanged = false;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mageants\Orderattribute\Model\ResourceModel\Relation $resource
     * @param \Mageants\Orderattribute\Model\ResourceModel\Relation\Collection $resourceCollection
     * @param \Mageants\Orderattribute\Model\RelationDetailsFactory $detailsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Mageants\Orderattribute\Model\ResourceModel\Relation $resource,
        \Mageants\Orderattribute\Model\ResourceModel\Relation\Collection $resourceCollection,
        \Mageants\Orderattribute\Model\RelationDetailsFactory $detailsFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->detailsFactory = $detailsFactory;
    }

    /**
     * Construct
     */
    public function _construct()
    {
        $this->_init(\Mageants\Orderattribute\Model\ResourceModel\Relation::class);
    }

    /**
     * Initialize relation model data from array.
     *
     * @param array $data
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadPost(array $data)
    {
        if (!isset($data['name'])
            || !isset($data['attribute_id'])
            || !isset($data['attribute_options'])
            || !isset($data['dependent_attributes'])
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Data is incorrect.'));
        }
        $this->setName($data['name']);

        $details = [];
        foreach ($data['attribute_options'] as $option) {
            foreach ($data['dependent_attributes'] as $attribute) {
                $details[] = $this->detailsFactory->create()
                    ->setAttributeId($data['attribute_id'])
                    ->setOptionId($option)
                    ->setDependentAttributeId($attribute);
            }
        }
        $this->setDetails($details);

        return $this;
    }

    /**
     * Load and set Relation Details data
     *
     * @return $this
     */
    public function loadRelationDetails()
    {
        $this->getAttributeId();
        $this->getAttributeOptions();
        $this->getDependentAttributes();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRelationId()
    {
        return $this->_getData(self::RELATION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRelationId($relationId)
    {
        $this->setData(self::RELATION_ID, $relationId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDetails()
    {
        if ($this->getRelationId() && $this->_getData('relation_details') === null) {
            $this->setDetails($this->getResource()->getDetails($this->getRelationId()));
        }

        $details = $this->_getData('relation_details');
        return is_array($details) ? $details : [];
    }

    /**
     * @inheritdoc
     */
    public function setDetails($relationDetails)
    {
        $this->setData('relation_details', $relationDetails);
        return $this;
    }

    /**
     * Get attribute id
     *
     * @return int
     */
    public function getAttributeId()
    {
        if ($this->_getData('attribute_id') === null) {
            foreach ($this->getDetails() as $relationDetail) {
                $this->setData('attribute_id', $relationDetail->getAttributeId());
                break;
            }
        }

        return $this->_getData('attribute_id');
    }

    /**
     * Get attribute options
     *
     * @return string
     */
    public function getAttributeOptions()
    {
        if ($this->_getData('attribute_options') === null) {
            $this->setData(
                'attribute_options',
                join(',', $this->getDetailColumnValues(RelationDetailInterface::OPTION_ID))
            );
        }

        return $this->_getData('attribute_options');
    }

    /**
     * Get dependent attributes
     *
     * @return string
     */
    public function getDependentAttributes()
    {
        if ($this->_getData('dependent_attributes') === null) {
            $this->setData(
                'dependent_attributes',
                join(',', $this->getDetailColumnValues(RelationDetailInterface::DEPENDENT_ATTRIBUTE_ID))
            );
        }

        return $this->_getData('dependent_attributes');
    }

    /**
     * Get detail column values
     *
     * @param string $column
     * @return array
     */
    protected function getDetailColumnValues($column)
    {
        $options = [];
        foreach ($this->getDetails() as $relationDetail) {
            $options[$relationDetail->getId()] = $relationDetail->getData($column);
        }
        return $options;
    }
}
