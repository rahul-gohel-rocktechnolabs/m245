<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ExtraFee\Model\ExtraFee\Source;

use Magento\Framework\Controller\ResultFactory;

class OverrideCat extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * To get all option
     *
     * @return Array
     */
    public function getAllOptions()
    {
        $options=[ ['value' => 'Yes', 'label' => __('Yes')],
            ['value' => 'No', 'label' => __('No')],['value' => 'Apply Both', 'label' => __('Apply Both')]];

        return $options;
    }
}
