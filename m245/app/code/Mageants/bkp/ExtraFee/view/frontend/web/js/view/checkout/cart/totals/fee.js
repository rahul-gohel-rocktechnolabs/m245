define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'

], function (ko, Component, quote, priceUtils, totals) {
    'use strict';
    var show_hide_Extrafee_blockConfig = window.checkoutConfig.enable;
    var fee_label = window.checkoutConfig.fee_label;
    var custom_fee_amount = window.checkoutConfig.custom_fee_amount;
    var all_fee_labels = window.checkoutConfig.all_fee_labels;

    return Component.extend({

        totals: quote.getTotals(),
        canVisibleExtrafeeBlock: show_hide_Extrafee_blockConfig,
        getFormattedPrice: ko.observable(priceUtils.formatPrice(custom_fee_amount, quote.getPriceFormat())),
        getFeeLabel:ko.observable(fee_label),
        isDisplayed: function () {
            return this.getPureValue() != 0;
        },
        getValue: function() {
            var price = 0;
            if (this.totals() && totals.getSegment('fee')) {
                price = totals.getSegment('fee').value;
            }
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        },
        getAllFeeLabels: function() {
           var allFeeLabels = '';
           if(all_fee_labels !='' && all_fee_labels !=null){
                allFeeLabels = all_fee_labels;
           }
           if(jQuery.cookie("mandatoryOrderExtraFee")){
                if(allFeeLabels==''){                    
                    allFeeLabels = jQuery.cookie("mandatoryOrderExtraFee");
                }
                else{
                    allFeeLabels = allFeeLabels+' + ' +jQuery.cookie("mandatoryOrderExtraFee");
                }
            }
            if(jQuery.cookie("orderExtraFeeLabel")!= null){
                if(allFeeLabels==''){
                    allFeeLabels = jQuery.cookie("orderExtraFeeLabel");                    
                }
                else{
                    allFeeLabels = allFeeLabels+' + ' + jQuery.cookie("orderExtraFeeLabel");
                }
            }
           return allFeeLabels;
        },
        getPureValue: function() {
            var price = 0;
            if (this.totals() && totals.getSegment('fee')) {
                price = totals.getSegment('fee').value;
            }
            return price;
        }
    });
});
