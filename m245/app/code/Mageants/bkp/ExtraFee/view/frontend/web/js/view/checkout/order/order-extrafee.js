define([
    'ko',
    'uiComponent',
    'jquery',
    'Magento_Checkout/js/action/get-totals',
    'mage/storage',
    'mage/url',
    'mage/cookies',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
    ],
    function(ko,
            Component,
            $,
            getTotalsAction,
            storage,
            urlBuilder,
            quote,
            priceUtils
        )
    {
        'use strict';
    var show=window.checkoutConfig.enable;
    var checkout_fee_label=window.checkoutConfig.orderfee_label;
    var custom_fee_amount=window.checkoutConfig.custom_orderfee_amount;
    var mandatory_order_fee_lable=window.checkoutConfig.mandatory_orderfee_lable;
    var showfee = true;
    if (custom_fee_amount.length == 0) {
        showfee = false;
    }
    if(mandatory_order_fee_lable.length == 0){
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
            template:'Mageants_ExtraFee/checkout/order/order-fee'
        },
            shouldShowMessage: ko.observable(show),
            showLoader: true,
            getCustomFeeAmount:ko.observable(custom_fee_amount),
            getOrderFeeLabel:ko.observable(checkout_fee_label),
            getMandatoryOrderFeeLabel:ko.observable(mandatory_order_fee_lable),
            shouldShowField: ko.observable(showfee),

            removePlace:function (item, event) {                

                var name = jQuery(event.currentTarget).val();
                var fname=jQuery("#orderFee option[value='"+name+"']").html();
                var orderFeeId=jQuery("#orderFee option[value='"+name+"']").attr("data-id");
                
                if(name>0){
                jQuery.cookie("orderExtrafeeAmount",name);
                jQuery.cookie("orderExtraFeeLabel",fname);
                jQuery.cookie("orderExtrafeeId",orderFeeId);}
                else{
                jQuery.cookie("orderExtrafeeAmount",'');
                jQuery.cookie("orderExtraFeeLabel",'');
                jQuery.cookie("orderExtrafeeId",'');
                }


                var onlyOrderFeeLable, onlyextrafee, codlable;
                if(jQuery.cookie("onlyextrafee")){
                    onlyextrafee = 1;
                }
                else{
                    onlyextrafee = 0;
                }
                if(jQuery.cookie("onlyOrderFeeLableId")){
                    onlyOrderFeeLable = jQuery.cookie("onlyOrderFeeLableId");
                }
                else{
                    onlyOrderFeeLable = "";
                }
                if(onlyOrderFeeLable == ""){
                    jQuery.cookie("onlyOrderFeeLableId", jQuery('tr.totals.fee.excl th.mark span.value').html());
                }

                var commentText = jQuery('tr.totals.fee.excl th.mark span.value').html();
                var commentTextSecond = commentText;

                if(orderFeeId != undefined){
                    if(commentText != undefined){
                        if(onlyextrafee != 1){
                            if(onlyOrderFeeLable){
                                commentText = onlyOrderFeeLable+' + '+jQuery.cookie("orderExtraFeeLabel");    
                                jQuery('tr.totals.fee.excl th.mark span.value').html(commentText);
                                jQuery.cookie("categoryExtraFeeLabel",onlyOrderFeeLable);
                                jQuery.cookie("onlyextrafee", "");
                            }else{
                                commentText = commentText+' + '+jQuery.cookie("orderExtraFeeLabel");    
                                jQuery('tr.totals.fee.excl th.mark span.value').html(commentText);
                                jQuery.cookie("categoryExtraFeeLabel",commentTextSecond);
                                jQuery.cookie("onlyextrafee", "");
                            }
                        }else{
                            if(jQuery.cookie("codFeeLable")){
                                commentText = jQuery.cookie("codFeeLable")+"+"+jQuery.cookie("orderExtraFeeLabel");
                                jQuery('tr.totals.fee.excl th.mark span.value').html(commentText);
                                jQuery.cookie("categoryExtraFeeLabel",onlyOrderFeeLable);
                                jQuery.cookie("onlyextrafee", "1");
                            }else{
                                commentText = jQuery.cookie("orderExtraFeeLabel");
                                jQuery('tr.totals.fee.excl th.mark span.value').html(commentText);
                                jQuery.cookie("categoryExtraFeeLabel",commentTextSecond);
                                jQuery.cookie("onlyextrafee", "1");
                            }
                        }
                    }
                    else{
                        commentText = jQuery.cookie("orderExtraFeeLabel");    
                        jQuery('tr.totals.fee.excl th.mark span.value').html(commentText);
                        jQuery.cookie("categoryExtraFeeLabel",commentTextSecond);
                        jQuery.cookie("onlyextrafee", "1");
                    }
                }else{
                    commentText = jQuery.cookie("categoryExtraFeeLabel");  
                    jQuery('tr.totals.fee.excl th.mark span.value').html(commentText);
                }
                jQuery.cookie("extraFeeComment",commentText);
                var serviceUrl = urlBuilder.build('extrafee/cart/apply'); // Our controller to re-collect the totals
                return storage.post(
                    serviceUrl
                ).done(
                    function(response) {
                        if(response) {
                            var deferred = $.Deferred();
                            getTotalsAction([], deferred);
                        }
                    }
                );
                return true;
            }
        });
    }
});