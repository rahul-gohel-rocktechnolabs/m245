<?php
namespace Mageants\ImageGallery\Model\Category\Source;

class IsActive implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Webspeaks\BannerSlider\Model\Slider
     */
    protected $category;

    /**
     * Constructor
     *
     * @param \Mageants\ImageGallery\Model\Category $category
     */
    public function __construct(\Mageants\ImageGallery\Model\Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->category->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
