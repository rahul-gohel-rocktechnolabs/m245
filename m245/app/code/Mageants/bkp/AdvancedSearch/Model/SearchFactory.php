<?php

/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvancedSearch\Model;

use Magento\Framework\ObjectManagerInterface as ObjectManager;
use \Mageants\AdvancedSearch\Model\Search\Suggested;

class SearchFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var string
     */
    protected $map;
    /**
     * @var Suggested
     */
    protected $suggested;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Suggested $suggested
     * @param array $map
     */
    public function __construct(
        ObjectManager $objectManager,
        Suggested $suggested,
        array $map = []
    ) {
        $this->objectManager = $objectManager;
        $this->suggested     = $suggested;
        $this->map           = $map;
    }

    /**
     * SearchFactory
     *
     * @param string $param
     * @param array $arguments
     * @return \Mageants\AdvancedSearch\Model\SearchInterface
     * @throws \UnexpectedValueException
     */
    public function create($param, array $arguments = [])
    {

        if (isset($this->map[$param])) {
       
            $instance = $this->objectManager->create($this->map[$param], $arguments);
           
        } else {
       
            $instance = $this->objectManager->create(
                $this->suggested,
                $arguments
            );
        }

        if (!$instance instanceof \Mageants\AdvancedSearch\Model\SearchInterface) {
            throw new \UnexpectedValueException(
                'Class ' . get_class(
                    $instance
                ) . ' should be an instance of \Mageants\AdvancedSearch\Model\SearchInterface'
            );
        }
        return $instance;
    }
}
