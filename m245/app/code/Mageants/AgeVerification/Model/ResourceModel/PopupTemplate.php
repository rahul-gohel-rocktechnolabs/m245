<?php
 /**
  * @category Mageants AgeVerification
  * @package Mageants_AgeVerification
  * @copyright Copyright (c) 2017 Mageants
  * @author Mageants Team <info@mageants.com>
  */
namespace Mageants\AgeVerification\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PopupTemplate extends AbstractDb
{
    /**
     * Undocumented function
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('popup_template', 'template_id');
    }
}
