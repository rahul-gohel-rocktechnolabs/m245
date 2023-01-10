define([
    'ko',
    'uiComponent',
    'jquery',
    'mage/cookies',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
    ],
    function(ko,Component,$,quote,priceUtils)
    {
    'use strict';
    var show=window.checkoutConfig.enable;
    var checkout_fee_label=window.checkoutConfig.checkoutfee_label;
    var custom_fee_amount=window.checkoutConfig.custom_checkoutfee_amount;
    var mandatory_shippingfee_lable=window.checkoutConfig.mandatory_shipfee_lable;
    var showfee = true;
    if (custom_fee_amount.length == 0) {
        showfee = false;
    }
    if(mandatory_shippingfee_lable.length == 0){
        if(custom_fee_amount.length == 0){
            show = false;
        }
        else{
            show = true;
        }        
    }
    if(custom_fee_amount=='' && show=="false"){
        return Component.extend({});
    }
    else
    {
        return Component.extend({defaults:{
            template:'Mageants_ExtraFee/checkout/shipping/additional-block'
        },
            getCustomFeeAmount:ko.observable(custom_fee_amount),
            getCheckoutFeeLabel:ko.observable(checkout_fee_label),
            getShippingMadatoryFeeLabel:ko.observable(mandatory_shippingfee_lable),
            shouldShowMessage: ko.observable(show),
            shouldShowField: ko.observable(showfee),
            removePlace:function (item, event) {
            var value = jQuery('select#shippingFee option:selected').val();
            var data = jQuery('#shippingFee :selected').text();
            if(name>0){
            jQuery.cookie("shippingExtrafeeIds",value);
            jQuery.cookie("shippingExtraFeeLabel",data);}
            else{
            jQuery.cookie("shippingExtrafeeIds",'');
            jQuery.cookie("shippingExtraFeeLabel",'');}
            return true;
            }
        });
    }
});