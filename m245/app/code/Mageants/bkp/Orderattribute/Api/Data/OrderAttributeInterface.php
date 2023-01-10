<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Api\Data;

/**
 * @api
 */
interface OrderAttributeInterface extends \Magento\Eav\Api\Data\AttributeInterface
{
    public const ENTITY_TYPE_CODE = 'order';
}
