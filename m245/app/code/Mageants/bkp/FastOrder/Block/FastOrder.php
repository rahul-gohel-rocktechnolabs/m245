<?php
/**
 * @category Mageants FastOrder
 * @package Mageants_FastOrder
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FastOrder\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class FastOrder extends Template
{
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;
    
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleDirReader;
    
    /**
     * @var \Mageants\FastOrder\Helper\Data
     */
    protected $_fastOrderHelper;

     /**
      * @var \Magento\Tax\Helper\Data
      */
    protected $taxData;
    /**
     * @var CUST_GROUP_CONF_ALL
     */
    public const CUST_GROUP_CONF_ALL = 32000 ;
    
    /**
     * @var CUST_NOT_LOGIN
     */
    public const CUST_NOT_LOGIN = 0;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Module\Dir\Reader $moduleDirReader
     * @param \Mageants\FastOrder\Helper\Data $fastOrderHelper
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Module\Dir\Reader $moduleDirReader,
        \Mageants\FastOrder\Helper\Data $fastOrderHelper,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->moduleDirReader=$moduleDirReader;
        $this->_fastOrderHelper=$fastOrderHelper;
        $this->_customerUrl = $customerUrl;
        $this->pageConfig = $context->getPageConfig();
        $this->customerSession = $customerSession;
        $this->_localeFormat = $localeFormat;
        $this->taxData = $taxData;
        $this->messageManager = $messageManager;
        parent::__construct($context, $data);
    }
    /**
     * Set page title
     *
     * @return string
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Fast Order'));
        return parent::_prepareLayout();
    }
    /**
     * Helper
     */
    public function helperData()
    {
        return $this->_fastOrderHelper;
    }
    /**
     * Get Login Url
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->_customerUrl->getLoginUrl();
    }
    /**
     * Get Csv Url
     *
     * @return array
     */
    public function getCsvUrl()
    {
        return $this->_fastOrderHelper->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .'fastorder/csv/import_fastorder.csv';
    }
     /**
      * Get No Of Lines
      *
      * @return int
      */
    public function getNoOfLines()
    {
        return $this->_fastOrderHelper->getFastOrderConfig('fastorder/general/number_of_lines');
    }
    /**
     * Get Maximum Row
     *
     * @return int
     */
    public function getMaxRow()
    {
        return $this->_fastOrderHelper->getFastOrderConfig('fastorder/general/max_number_of_lines');
    }
    
    /**
     * Get Format Interface
     *
     * @return int|string
     */
    public function getFormatInterface()
    {
         $product =$this->_localeFormat->getPriceFormat();
         return $product;
    }

    /**
     * Get Display BothPrices
     *
     * @return int|string
     */
    public function displayBothPrices()
    {
        
        return $this->taxData->displayBothPrices();
    }

    /**
     * Get Success Message
     *
     * @return string
     */
    public function successmsg()
    {
        $this->messageManager->addSuccess(__("Success"));
    }
}
