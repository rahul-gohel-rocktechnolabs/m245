<?php
/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2016  Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\DeliveryDate\Block\Product\View;

class DeliveryDateConfig extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageants\DeliveryDate\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageants\DeliveryDate\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageants\DeliveryDate\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }
    /**
     * Get Display At function
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
     * Get Holiday list function
     *
     * @return mixed
     */
    public function getpluginHoliDaysValue()
    {
        return $this->helper->getPluginHolidays();
    }

    /**
     * Get Cut Off time function
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
}
