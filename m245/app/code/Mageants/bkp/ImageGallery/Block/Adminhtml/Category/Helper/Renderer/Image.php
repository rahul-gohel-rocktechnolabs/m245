<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\ImageGallery\Block\Adminhtml\Category\Helper\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Mageants\ImageGallery\Model\GalleryFactory
     */
    protected $_imageFactory;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageants\ImageGallery\Model\GalleryFactory $imageFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageants\ImageGallery\Model\GalleryFactory $imageFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
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
        $image = $this->_imageFactory->create()->load($row->getId());
        $srcImage = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . $image->getImage();

        return '<image width="150" height="50" src ="'.$srcImage.'" alt="'.$image->getImage().'" >';
    }
}
