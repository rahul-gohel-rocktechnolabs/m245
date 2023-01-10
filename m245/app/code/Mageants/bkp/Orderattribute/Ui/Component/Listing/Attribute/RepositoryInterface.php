<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Ui\Component\Listing\Attribute;

interface RepositoryInterface
{
    /**
     * Get attributes
     *
     * @return \Mageants\Orderattribute\Api\Data\OrderAttributeInterface[]
     */
    public function getList();
}
