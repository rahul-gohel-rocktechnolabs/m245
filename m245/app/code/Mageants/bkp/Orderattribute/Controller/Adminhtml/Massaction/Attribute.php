<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Massaction;

use Magento\Backend\App\Action;

/**
 * Adminhtml action attribute update controller
 */
abstract class Attribute extends Action
{
    public const ADMIN_RESOURCE = 'Mageants_Orderattribute::attribute_value_edit';
}
