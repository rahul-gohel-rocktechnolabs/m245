<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Ui\Component\Listing\Attribute;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var null|\Magento\Catalog\Api\Data\ProductAttributeInterface[]
     */
    protected $attributes;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Mageants\Orderattribute\Model\Order\Attribute\Repository
     */
    protected $orderAttributeRepository;

    /**
     * @param \Mageants\Orderattribute\Model\Order\Attribute\Repository $orderAttributeRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Mageants\Orderattribute\Model\Order\Attribute\Repository $orderAttributeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderAttributeRepository = $orderAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    abstract protected function buildSearchCriteria();

    /**
     * Get list
     *
     * @return \Mageants\Orderattribute\Api\Data\OrderAttributeInterface[]
     */
    public function getList()
    {
        if (null == $this->attributes) {
            $this->attributes = $this->orderAttributeRepository
                ->getList($this->buildSearchCriteria())
                ->getItems();
        }
        return $this->attributes;
    }
}
