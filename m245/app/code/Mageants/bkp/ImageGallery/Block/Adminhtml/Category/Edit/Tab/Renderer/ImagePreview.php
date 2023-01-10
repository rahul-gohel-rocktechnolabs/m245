<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Block\Adminhtml\Category\Edit\Tab\Renderer;

class ImagePreview extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     *
     * @param \Magento\Backend\Block\Context              $context
     * @param \Magento\Store\Model\StoreManagerInterface  $storeManager
     * @param \Webspeaks\BannerSlider\Model\SlideFactory  $imageFactory
     * @param \Magento\Cms\Model\Template\FilterProvider  $filterProvider
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageants\ImageGallery\Model\GalleryFactory $imageFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
        $this->_filterProvider = $filterProvider;
    }

    /**
     * Render
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $_slide = $this->_imageFactory->create()->load($row->getId());
        $html = $this->_filterProvider->getPageFilter()->filter($_slide->getContent());
        $output = '<div style="max-height:100px;max-width:100px;overflow:hidden">' . $html . '</div>';
        $output .= '<br><a href="' . $this->getUrl('*/*/edit', ['_current' => false, 'slide_id' => $row->getId()]) . '" 
        target="_blank">Edit</a> ';
        return $output;
    }
}
