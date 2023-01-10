<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ImageGallery\Block\Adminhtml\Category\Helper\Renderer;

class Video extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Mageants\ImageGallery\Model\VideoFactory
     */
    protected $_videoFactory;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageants\ImageGallery\Model\VideoFactory $videoFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageants\ImageGallery\Model\VideoFactory $videoFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_videoFactory = $videoFactory;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $video = $this->_videoFactory->create()->load($row->getId());
        $srcVideo = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . $video->getVideo();

        return '<video width="300" height="125" controls>
                    <source src="'.$srcVideo.'" type="video/mp4">
                    <source src="mov_bbb.ogg" type="video/ogg">
                       Your browser does not support HTML5 video.
                </video>';
    }
}
