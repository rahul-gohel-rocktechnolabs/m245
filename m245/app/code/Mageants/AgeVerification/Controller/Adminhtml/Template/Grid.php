<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Controller\Adminhtml\Template;

/**
 * Undocumented class
 */
class Grid extends \Magento\Backend\App\Action
{
 /**
  * Undocumented function
  *
  * @return void
  */
    public function execute()
    {
        $this->_view->build(false);
        $this->_view->renderResult();
    }
}
