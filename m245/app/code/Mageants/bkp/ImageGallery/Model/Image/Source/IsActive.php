<?php
namespace Mageants\ImageGallery\Model\Image\Source;

class IsActive implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Webspeaks\BannerSlider\Model\Slide
     */
    protected $image;

    /**
     * Constructor
     *
     * @param \Webspeaks\BannerSlider\Model\Slide $image
     */
    public function __construct(\Mageants\ImageGallery\Model\Gallery $image)
    {
        $this->image = $image;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->image->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
