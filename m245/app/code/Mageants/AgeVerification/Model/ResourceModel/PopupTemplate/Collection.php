<?php
// @codingStandardsIgnoreFile
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Model\ResourceModel\PopupTemplate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Mageants\AgeVerification\Model\PopupTemplate',
            'Mageants\AgeVerification\Model\ResourceModel\PopupTemplate'
        );
    }
}
