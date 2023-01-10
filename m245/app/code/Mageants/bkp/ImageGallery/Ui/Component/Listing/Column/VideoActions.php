<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ImageGallery\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class VideoActions extends \Mageants\ImageGallery\Ui\Component\Listing\Column\AbstractColumn
{
    /**
     * default width and height image.
     */
    public const IMAGE_WIDTH = '70%';
    public const IMAGE_HEIGHT = '60';
    public const IMAGE_STYLE = 'display: block;margin: auto;';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Prepare item.
     *
     * @param array $item
     *
     * @return array
     */
    protected function _prepareItem(array &$item)
    {
        $width = $this->hasData('width') ? $this->getWidth() : self::IMAGE_WIDTH;
        $height = $this->hasData('height') ? $this->getHeight() : self::IMAGE_HEIGHT;
        $style = $this->hasData('style') ? $this->getStyle() : self::IMAGE_STYLE;
        if (isset($item[$this->getData('name')])) {
            if ($item[$this->getData('name')]) {
                $srcImage = $this->_storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $item[$this->getData('name')];
                $item[$this->getData('name')] =
                '<video width="800" height="200" controls>
                <source src="'.$srcImage.'" type="video/mp4">
                <source src="mov_bbb.ogg" type="video/ogg">
                    Your browser does not support HTML5 video.
                </video>';
                    
            } else {
                $item[$this->getData('name')] = '';
            }
        }

        return $item;
    }
}
