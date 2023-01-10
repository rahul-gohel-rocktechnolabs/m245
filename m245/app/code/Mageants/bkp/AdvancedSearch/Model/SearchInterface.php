<?php

/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\AdvancedSearch\Model;

interface SearchInterface
{
    /**
     * Retrieve selected in config data
     *
     * @return array
     */
    public function getResponseData();

    /**
     * Check if data used in search result
     *
     * @return bool
     */
    public function canAddToResult();
}
