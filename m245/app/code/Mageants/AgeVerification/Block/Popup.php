<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Block;

class Popup extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageants\AgeVerification\Helper\Data
     */
    public $helperData;
    /**
     * @var \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory
     */
    public $popupTemplateCollection;

    /**
     * @param \Mageants\AgeVerification\Helper\Data $helperData
     * @param \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory $popupTemplateCollection;
     */
    /**
     * Undocumented function
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageants\AgeVerification\Helper\Data $helperData
     * @param \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory $popupTemplateCollection
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageants\AgeVerification\Helper\Data $helperData,
        \Mageants\AgeVerification\Model\ResourceModel\PopupTemplate\CollectionFactory $popupTemplateCollection
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
        $this->popupTemplateCollection = $popupTemplateCollection;
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getHelperData()
    {
        return $this->helperData;
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getTemplateData()
    {
        return $this->popupTemplateCollection->create();
    }
}
