<?php
/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2016  Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\DeliveryDate\Block;

class DeliveryDate extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageants\DeliveryDate\Helper\Data
     */
    protected $helper;

    /**
     * @var registry
     */
    protected $_registry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageants\DeliveryDate\Helper\Data $helper,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper = $helper;
        $this->_registry = $registry;
        parent::__construct($context);
    }
    /**
     * Check Module Is Enalbe function
     *
     * @return bool
     */
    public function getPluginEnable()
    {
        return $this->helper->getPluginEnable();
    }
    /**
     * Show Display Position function
     *
     * @return mixed
     */
    public function getDisplayAt()
    {
        return $this->helper->getPluginDisplayAt();
    }
    /**
     * Get Date Format function
     *
     * @return mixed
     */
    public function getDateFormatValue()
    {
        return $this->helper->getPluginDateFormat();
    }
    /**
     * Get Disable Days function
     *
     * @return mixed
     */
    public function getPluginInDisableDaysValue()
    {
        return $this->helper->getPluginDisableDays();
    }

    /**
     * Get Holiday List function
     *
     * @return mixed
     */
    public function getpluginHoliDaysValue()
    {
        return $this->helper->getPluginHolidays();
    }

    /**
     * Get Cutoff Time function
     *
     * @return mixed
     */
    public function getpluginCutOffTimeValue()
    {
        return $this->helper->getPluginCutoffTime();
    }

    /**
     * Get Process Time function
     *
     * @return mixed
     */
    public function pluginProcessingTimeValue()
    {
        return $this->helper->getPluginProcessTime();
    }

    /**
     * Get Time Slot function
     *
     * @return mixed
     */
    public function getTimesLotData()
    {
        return $this->helper->getTimeSlot();
    }

    /**
     * Get Arrival Comment function
     *
     * @return mixed
     */
    public function getArrivalCommentValue()
    {
        return $this->helper->getPluginArrivalComment();
    }

    /**
     * Get Current Product function
     *
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
}
