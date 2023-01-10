<?php
/**
 * @category Mageants_AdvancedSearch
 * @package Mageants_AdvancedSearch
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvancedSearch\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const XML_PATH_SEARCH_DELAY = 'mageants_advancedsearch/AdvancedSearch_main/search_delay';
    public const XML_PATH_AUTOCOMPLETE_FIELDS = 'mageants_advancedsearch/AdvancedSearch_main/autocomplete_fields';
    public const XML_PATH_SUGGESTED_RESULT_NUMBER = 'mageants_advancedsearch/suggestion/suggested_result_number';
    public const XML_PATH_PRODUCT_RESULT_NUMBER = 'mageants_advancedsearch/product/product_result_number';
    public const XML_PATH_PRODUCT_RESULT_FIELDS = 'mageants_advancedsearch/product/product_result_fields';
    public const XML_PATH_POPUP_WIDTH = 'mageants_advancedsearch/AdvancedSearch_main/popup_width';
    public const XML_PATH_SEARCH_TYPE= 'mageants_advancedsearch/AdvancedSearch_main/searchbar';
    public const XML_PATH_MINIMUM_SEARCH = 'mageants_advancedsearch/AdvancedSearch_main/minimum_number';
    public const XML_PATH_PRODUCT_TITLE = 'mageants_advancedsearch/product/product_title';
    public const XML_PATH_CATEGORY_TITLE = 'mageants_advancedsearch/product/category_title';
    public const XML_PATH_DECRIPTION_LENGTH = 'mageants_advancedsearch/product/decription_length';
    public const XML_PATH_URL_REDIRECT = 'mageants_advancedsearch/product/url_redirect';
    public const XML_PATH_SUGGESTION_TITLE = 'mageants_advancedsearch/suggestion/Sugestion_Title';
    public const XML_PATH_BESTSELLER_TITLE = 'mageants_advancedsearch/bestseller/bestseller_title';
    public const XML_PATH_BESTSELLER_NUMBER = 'mageants_advancedsearch/bestseller/bestseller_result_number';
    public const XML_PATH_BESTSELLER_ENABLE = 'mageants_advancedsearch/bestseller/enable';
    public const XML_PATH_SHOPPING_CART= 'mageants_advancedsearch/product/addtocart_redirect';
    public const XML_PATH_RECENT_TITLE = 'mageants_advancedsearch/Recentsearch/recent_title';
    public const XML_PATH_RECENT_NUMBER= 'mageants_advancedsearch/Recentsearch/recent_result_number';
    public const XML_PATH_RECENT_ENABLE= 'mageants_advancedsearch/Recentsearch/recent_enabled';
    public const XML_PATH_FIRST_CLICK= 'mageants_advancedsearch/Recentsearch/firstclick';
    public const XML_PATH_CUSTOM_LAYOUT_ENABLE= 'mageants_advancedsearch/custom_layout/custom_layout_enabled';
    public const XML_PATH_BACKGROUND_COLOR= 'mageants_advancedsearch/custom_layout/background';
    public const XML_PATH_BORDER_COLOR = 'mageants_advancedsearch/custom_layout/border';
    public const XML_PATH_HOVER_COLOR = 'mageants_advancedsearch/custom_layout/hover';
    public const XML_PATH_TEXT_COLOR= 'mageants_advancedsearch/custom_layout/text';
    public const XML_PATH_HOVER_TEXT = 'mageants_advancedsearch/custom_layout/hover_text';
    /**
     * Retrieve search delay
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSearchDelay($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_DELAY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve comma-separated autocomplete fields
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAutocompleteFields($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AUTOCOMPLETE_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve list of autocomplete fields
     *
     * @param int|null $storeId
     * @return array
     */
    public function getAutocompleteFieldsAsArray($storeId = null)
    {
        return explode(',', $this->getAutocompleteFields($storeId));
    }

    /**
     * Retrieve suggest results number
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSuggestedResultNumber($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_SUGGESTED_RESULT_NUMBER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve product results number
     *
     * @param int|null $storeId
     * @return int
     */
    public function getProductResultNumber($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_RESULT_NUMBER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve comma-separated product result fields
     *
     * @param int|null $storeId
     * @return string
     */
    public function getProductResultFields($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_RESULT_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve list of product result fields
     *
     * @param int|null $storeId
     * @return array
     */
    public function getProductResultFieldsAsArray($storeId = null)
    {
        return explode(',', $this->getProductResultFields($storeId));
    }

    /**
     * Get popupwidth
     *
     * @param int|null $storeId
     * @return int
     */
    public function popupwidth($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POPUP_WIDTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Searchbar Type
     *
     * @param int|null $storeId
     */
    public function searchtype($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Number to start search process
     *
     * @param int|null $storeId
     * @return int
     */
    public function minimumsearch($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MINIMUM_SEARCH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Product Block Title
     *
     * @param int|null $storeId
     */
    public function producttitle($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get categort Block Title
     *
     * @param int|null $storeId
     */
    public function categorytitle($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get description length of product
     *
     * @param int|null $storeId
     */
    public function decriptionlength($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DECRIPTION_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get value to redirect product page
     *
     * @param int|null $storeId
     */
    public function urlredirect($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_URL_REDIRECT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Suggestion title
     *
     * @param int|null $storeId
     */

    public function suggestiontitle($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SUGGESTION_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Bestseller title
     *
     * @param int|null $storeId
     */
    public function bestsellertitle($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BESTSELLER_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get a value to show number of bestseller product show
     *
     * @param int|null $storeId
     */
    public function bestsellernumber($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BESTSELLER_NUMBER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get value to bestseller enable/disable
     *
     * @param int|null $storeId
     */
    public function bestsellerenable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BESTSELLER_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Recent title
     *
     * @param int|null $storeId
     */
    public function recenttitle($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECENT_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get a value to show number of recent search
     *
     * @param int|null $storeId
     */
    public function recentnumber($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECENT_NUMBER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get value to recent enable/disable
     *
     * @param int|null $storeId
     */
    public function recentenable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECENT_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Value to Show popup on first click
     *
     * @param int|null $storeId
     */
    public function firstclick($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FIRST_CLICK,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Value to Apply to CustomLayout
     *
     * @param int|null $storeId
     */
    public function customlayoutenable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_LAYOUT_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Value for popup Background color
     *
     * @param int|null $storeId
     */
    public function backgroundcolor($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BACKGROUND_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Value for popup border color
     *
     * @param int|null $storeId
     */
    public function bordercolor($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BORDER_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Value for popup hover color
     *
     * @param int|null $storeId
     */
    public function hovercolor($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HOVER_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Value for popup text color
     *
     * @param int|null $storeId
     */
    public function textcolor($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TEXT_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Value for hover text color
     *
     * @param int|null $storeId
     */
    public function hovertext($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HOVER_TEXT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get Value for shippingcart
     *
     * @param int|null $storeId
     */
    public function shippingcart($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SHOPPING_CART,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
