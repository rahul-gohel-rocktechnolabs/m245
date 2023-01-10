<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Ui\Component\Listing\Attribute;

class Repository extends AbstractRepository
{
    /**
     * @inheritdoc
     */
    protected function buildSearchCriteria()
    {
        return $this->searchCriteriaBuilder->addFilter('additional_table.is_used_in_grid', 1)->create();
    }
}
