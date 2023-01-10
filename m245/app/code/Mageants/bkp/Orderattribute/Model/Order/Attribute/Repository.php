<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\Order\Attribute;

use Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\Collection;

class Repository
{
    /**
     * @var \Magento\Eav\Model\AttributeRepository
     */
    protected $eavAttributeRepository;

    /**
     * @var \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var \Magento\Eav\Api\Data\AttributeSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * Repository constructor.
     *
     * @param \Magento\Eav\Api\AttributeRepositoryInterface                                  $eavAttributeRepository
     * @param \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Eav\Api\Data\AttributeSearchResultsInterfaceFactory                   $searchResultsFactory
     * @param \Magento\Framework\App\ResourceConnection                                      $resource
     */
    public function __construct(
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository,
        \Mageants\Orderattribute\Model\ResourceModel\Order\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Eav\Api\Data\AttributeSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->eavAttributeRepository = $eavAttributeRepository;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resource = $resource;
    }

    /**
     * @inheritdoc
     */
    public function get($attributeCode)
    {
        $connection = $this->resource->getConnection();
        $attributeCodeData = $this->eavAttributeRepository->get(
            \Mageants\Orderattribute\Api\Data\OrderAttributeInterface::ENTITY_TYPE_CODE,
            $attributeCode
        );
        $tablename = $this->resource->getTableName('mageants_orderattribute_order_eav_attribute');
        $runSql =  $connection->select()->from($tablename)->where('attribute_id=?', $data['attribute_id']);
        $result = $connection->fetchRow($runSql);
        if (isset($result['checkout_step'])) {
            $attributeCodeData->setCheckoutStep($result['checkout_step']);
        }
        return $attributeCodeData;
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {

        /** @var Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $attributeCollection);
        }
        /** @var SortOrder $sortOrder */
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $attributeCollection->addOrder(
                $sortOrder->getField(),
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        $totalCount = $attributeCollection->getSize();

        // Group attributes by id to prevent duplicates with different attribute sets
        $attributeCollection->addAttributeGrouping();
        $attributeCollection->setCurPage($searchCriteria->getCurrentPage());
        $attributeCollection->setPageSize($searchCriteria->getPageSize());

        $attributes = [];
        /** @var \Magento\Eav\Api\Data\AttributeInterface $attribute */
        foreach ($attributeCollection as $attribute) {
            $attributes[] = $attribute;
        }
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($attributes);
        $searchResults->setTotalCount($totalCount);
        return $searchResults;
    }

    /**
     * Add filter group to collection
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param Collection                                $collection
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        Collection $collection
    ) {
        /** @var \Magento\Framework\Api\Search\FilterGroup $filter */
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $collection->addFieldToFilter(
                $filter->getField(),
                [$condition => $filter->getValue()]
            );
        }
    }
}
