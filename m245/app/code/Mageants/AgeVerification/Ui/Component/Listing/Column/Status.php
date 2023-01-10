<?php
/**
 * @category Mageants AgeVerification
 * @package Mageants_AgeVerification
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\AgeVerification\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Undocumented class
 */
class Status extends Column
{
   
     /**
      * Prepare Data Source
      *
      * @param array $dataSource
      * @return array
      */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                if ($items['status'] == 1) {
                    $items['status'] = '<span>Enable</span>';
                } elseif ($items['status'] == 0) {
                    $items['status'] = '<span>Disable</span>';
                }
            }
        }
        return $dataSource;
    }
}
