/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'mage/url',
    'mage/storage',
    'Magento_Checkout/js/action/get-totals',
    'mage/cookies'
], function ($,quote,urlBuilder,storage,getTotalsAction) {
    'use strict';

    return function (paymentMethod) {
        quote.paymentMethod(paymentMethod);
        if (paymentMethod.method == "cashondelivery") {
            var cod_fee_amount=window.checkoutConfig.cod_fee_amount;
            $.mage.cookies.set("codFee",cod_fee_amount);
            var serviceUrl = urlBuilder.build('extrafee/payment/apply'); // Our controller to re-collect the totals
            return storage.post(
                serviceUrl
            ).done(
                function(response) {
                    if(response) {
                        var deferred = $.Deferred();
                        getTotalsAction([], deferred);
                        setTimeout(function(){
                            var commentText = $('tr.totals.fee.excl th.mark span.value').html();                            
                            var onlycodlable = '$'+$.cookie("codFee")+' - Cash On Delivery Fee';
                            $.cookie("onlycodlable", onlycodlable);

                            if($.cookie("beforeCodFee") == null){
                                $.cookie("beforeCodFee",commentText);
                            }
                            else{
                                commentText = $.cookie("beforeCodFee");
                            }

                            if(commentText == ''){
                                 commentText = '$'+$.cookie("codFee")+' - Cash On Delivery Fee';
                                
                            }else if(commentText != '' && $.cookie("codFee") != ''){
                                commentText = commentText+' + $'+$.cookie("codFee")+' - Cash On Delivery Fee';
                            }
                            if(commentText){
                                $.cookie("extraFeeComment",commentText);
                                $.cookie("onlyOrderFeeLableId",commentText);
                                $.cookie("codFeeLable",commentText);
                                $('tr.totals.fee.excl th.mark span.value').html(commentText);
                            }  
                        }, 1000);
                    }
                }
            );            
        }
        else{
            var serviceUrl = urlBuilder.build('extrafee/payment/removecodfee'); // Our controller to re-collect the totals
            return storage.post(
                serviceUrl
            ).done(
                function(response) {
                    if(response) {
                        var substring = '$'+$.cookie("codFee")+' - Cash On Delivery Fee';
                        $.cookie("codFee",'');
                        var deferred = $.Deferred();
                        getTotalsAction([], deferred);
                        var commentText = $('tr.totals.fee.excl th.mark span.value').html();
                        if(commentText){
                            var str = commentText.replace(substring, "");
                            var lastChar = str[str.length -2];
                            if(lastChar == '+'){
                                str = str.slice(0, -2);
                            } 
                            if($.cookie("onlycodlable")){
                                str = $.cookie("beforeCodFee");
                                $.cookie("onlycodlable", "");
                            }
                            $.cookie("extraFeeComment",str);
                            $.cookie("onlyOrderFeeLableId",str);
                            $.cookie("codFeeLable", "");
                            $('tr.totals.fee.excl th.mark span.value').html(str);
                        }
                    }
                }
            ); 
        }

    };
});
