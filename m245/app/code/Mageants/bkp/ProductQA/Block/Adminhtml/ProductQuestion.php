<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Block\Adminhtml;

class ProductQuestion extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_productquestion';
        
        $this->_blockGroup = 'Mageants_ProductQA';
        
        $this->_headerText = __('Question');
        
        $this->_addButtonLabel = __('Add Product Question');
        
        parent::_construct();
    }
}
