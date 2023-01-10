<?php declare(strict_types=1);
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Mageants\ImageGallery\Helper\Data as helperData;

class Data implements ArgumentInterface
{
    /**
     * \Mageants\ImageGallery\Helper\Data
     *
     * @var \Mageants\ImageGallery\Helper\Data
     */
    private $helperData;
    /**
     * Viewmodel Constructor
     *
     * @param helperData $helperData
     */
    public function __construct(
        helperData $helperData
    ) {
        $this->helperData = $helperData;
    }
    /**
     * GetHelper function
     *
     * @return void
     */
    public function getHelper()
    {
        return $this->helperData;
    }
}
