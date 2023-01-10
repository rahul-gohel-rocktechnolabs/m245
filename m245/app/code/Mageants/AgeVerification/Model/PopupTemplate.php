<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Contact Model
 *
 */
class PopupTemplate extends AbstractModel
{

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageants\AgeVerification\Model\ResourceModel\PopupTemplate::class);
    }
}
