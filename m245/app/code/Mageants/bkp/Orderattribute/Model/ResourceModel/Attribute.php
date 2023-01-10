<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Model\ResourceModel;

class Attribute extends \Magento\Eav\Model\ResourceModel\Entity\Attribute
{

    /**
     * @inheritdoc
     */
    protected function _updateDefaultValue(
        $object,
        $optionId,
        $intOptionId,
        &$defaultValue
    ) {
        parent::_updateDefaultValue(
            $object,
            $optionId,
            $intOptionId,
            $defaultValue
        );
        if (in_array($optionId, $object->getDefault())) {
            $frontendInput = $object->getFrontendInput();
            if ($frontendInput === 'checkboxes') {
                $defaultValue[] = $intOptionId;
            } elseif ($frontendInput === 'radios') {
                $defaultValue = [$intOptionId];
            }
        }
    }
}
