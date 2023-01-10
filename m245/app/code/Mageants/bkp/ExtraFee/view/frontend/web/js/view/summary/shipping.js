/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'mage/cookies',
    'Magento_SalesRule/js/view/summary/discount'
], function ($, _, Component, quote, discountView) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/summary/shipping'
        },
        quoteIsVirtual: quote.isVirtual(),
        totals: quote.getTotals(),

        /**
         * @return {*}
         */
        getShippingMethodTitle: function() {
                if (!this.isCalculated()) {
                    return '';
                }
                var shippingMethod = quote.shippingMethod();
                var feeLabel='';
                var mandatoryShippingExtraFee='';
                var shippingLabel='';
                feeLabel=jQuery.cookie("shippingExtraFeeLabel");
                mandatoryShippingExtraFee=jQuery.cookie("mandatoryShippingExtraFee");

                if(feeLabel==null|| feeLabel=='')
                {
                    if(shippingMethod){
                        shippingLabel = shippingMethod.carrier_title + " - " + shippingMethod.method_title
                    }
                }
                else
                {
                    if(shippingMethod){
                        shippingLabel = shippingMethod.carrier_title + " - " + shippingMethod.method_title + " + "+ feeLabel
                    }
                }
                if(mandatoryShippingExtraFee!=null && mandatoryShippingExtraFee!=''){
                    if(shippingLabel!=''){
                        shippingLabel = shippingLabel+' + '+mandatoryShippingExtraFee;
                    }else{
                        shippingLabel = mandatoryShippingExtraFee;
                    }
                    
                }
                return shippingLabel;
            },

        /**
         * @return {*|Boolean}
         */
        isCalculated: function () {
            return this.totals() && this.isFullMode() && quote.shippingMethod() != null; //eslint-disable-line eqeqeq
        },

        /**
         * @return {*}
         */
        getValue: function() {
                if (!this.isCalculated()) {
                    return this.notCalculatedMessage;
                }
                    var price =  this.totals().shipping_amount;
                
                return this.getFormattedPrice(price);
            },

        /**
         * If is set coupon code, but there wasn't displayed discount view.
         *
         * @return {Boolean}
         */
        haveToShowCoupon: function () {
            var couponCode = this.totals()['coupon_code'];

            if (typeof couponCode === 'undefined') {
                couponCode = false;
            }

            return couponCode && !discountView().isDisplayed();
        },

        /**
         * Returns coupon code description.
         *
         * @return {String}
         */
        getCouponDescription: function () {
            if (!this.haveToShowCoupon()) {
                return '';
            }

            return '(' + this.totals()['coupon_code'] + ')';
        }
    });
});
