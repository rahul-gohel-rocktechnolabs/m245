<?php
/**
 * @category Mageants PricePerCustomer
 * @package Mageants_PricePerCustomer
 * @copyright Copyright (c) 2020 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\PricePerCustomer\Controller\Adminhtml\Export;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Exportprice class for export customer price data
 */
class Exportprice extends \Magento\Backend\App\Action
{
    /**
     * [$uploaderFactory description]
     * @var [type]
     */
    protected $uploaderFactory;

    /**
     * [$customerpriceFactory description]
     * @var [type]
     */
    protected $customerpriceFactory;

    /**
     * Construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Mageants\PricePerCustomer\Model\PricePerCustomerFactory $customerpricefactory
    ) {
        parent::__construct($context);
        $this->_fileFactory = $fileFactory;
        $this->customerpriceFactory = $customerpricefactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return file
     */
    public function execute()
    {
        $name = date('m-d-Y-H-i-s');
        $filepath = 'export/export-data-' .$name. '.csv';
        $this->directory->create('export');

        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();

        $columns = ['User ID','Product ID','User Price','User Special Price'];

        foreach ($columns as $column) {
            $header[] = $column;
        }

        $stream->writeCsv($header);

        $location = $this->customerpriceFactory->create();
        $location_collection = $location->getCollection();

        foreach ($location_collection as $item) {

            $itemData = [];

            if ($item->getData('product_id') == 'all') {
                continue;
            }

            $itemData[] = $item->getData('customer_id');
            $itemData[] = $item->getData('product_id');
            $itemData[] = $item->getData('customer_price');
            $itemData[] = $item->getData('special_price');

            $stream->writeCsv($itemData);

        }

        $content = [];
        $content['type'] = 'filename';
        $content['value'] = $filepath;
        $content['rm'] = '1';

        $csvfilename = 'customerprice-'.$name.'.csv';
        return $this->_fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);
    }
}
