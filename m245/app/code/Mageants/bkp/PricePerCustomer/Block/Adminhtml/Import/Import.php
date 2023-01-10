<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\PricePerCustomer\Block\Adminhtml\Import;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Import class for import Customer Price data
 */
class Import extends Generic
{
    /**
     * [$_template description]
     * @var string
     */
    protected $_template = 'importprice.phtml';
}
