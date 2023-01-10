<?php
 /**
  * @category  Mageants ProductQA
  * @package   Mageants_ProductQA
  * @copyright Copyright (c) 2017 Mageants
  * @author    Mageants Team <info@mageants.com>
  */
namespace Mageants\ProductQA\Block\Adminhtml\ProductQuestion\Edit\Tab\Renderer;

use \Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;

/**
 * @method string getValue()
 */
class CustomerColumn extends \Magento\Framework\Data\Form\Element\Text
{
    
    /**
     * Get customer url
     *
     * @return string
     */
    /**
     * Construct
     *
     * @param Registry $registry [description]
     */
    public function __construct(
        Registry $registry
    ) {
       
        $this->registry = $registry;
    }
    /**
     * Get After ElementHtml
     *
     * @return [type] [description]
     */
    public function getAfterElementHtml()
    {
        $registry = $this->registry->create();
        $productquestion = $registry->registry('mageants_productquestion');
        return parent::getAfterElementHtml()."test123";
    }
}
