define([
    "jquery",
    "jquery/ui",
], function ($) {
    "use strict";
    var attrIds = [];

    $.widget('mageants.fastorder_grouped', {
        options: {
            priceHolderSelector: '#mageants-content-option-product .price-box',
            mageantsqtyElement: '',
            sortOrder: ''
        },

        _create: function () {
            var self = this;
            $('#mageants-validation-message-box').hide();
            $("#mgantsselect-option").click(function (event) {
                $('#mageants-validation-message-box').show();
                $(".mageants-mage-error").css("color", "red");
            });

            $.each(this.element.find(this.options.mageantsqtyElement), function () {
                if ($(this).val() != 0) {
                    $(this).attr('value', $(this).val());
                    $('#mageants-validation-message-box').hide();
                    var qtyEl = parseFloat($(this).val());
                    var priceEl = parseFloat($(this).closest('tr').find('.price-wrapper').attr('data-price-amount')),
                        priceElExclTax = parseFloat($(this).closest('tr').find('.price-wrapper.price-excluding-tax').attr('data-price-amount'));
                    $(this).next().val(qtyEl*priceEl);
                    $(this).next().attr('data-excl-tax', qtyEl*priceElExclTax);
                    var prodCookieId = localStorage.getItem('page_refresh_product');
                
                    if(prodCookieId){
                        var prodCookie = JSON.parse(prodCookieId);
                        if($(this).val() != 0){
                            var selectId = $(this).attr('data-selectore-id');
                            var qty = $(this).val();
                            var sortOrder = $('.mgantsrow-select').val();
                            attrIds[selectId] = qty;
                            prodCookie[sortOrder]["groupedIds"] = attrIds;
                            localStorage.setItem('page_refresh_product', JSON.stringify(prodCookie));
                        }
                    }
                }
            });
            this.element.find(this.options.mageantsqtyElement).on('change',function () {
                $(this).attr('value', $(this).val());
                $('#mageants-validation-message-box').hide();
                var qtyEl = parseFloat($(this).val());
                var priceEl = parseFloat($(this).closest('tr').find('.price-wrapper').attr('data-price-amount')),
                    priceElExclTax = parseFloat($(this).closest('tr').find('.price-wrapper.price-excluding-tax').attr('data-price-amount'));
                $(this).next().val(qtyEl*priceEl);
                $(this).next().attr('data-excl-tax', qtyEl*priceElExclTax);                    
                var prodCookieId = localStorage.getItem('page_refresh_product');
                
                if(prodCookieId){
                    var prodCookie = JSON.parse(prodCookieId);
                    if($(this).val() != 0){
                        var selectId = $(this).attr('data-selectore-id');
                        var qty = $(this).val();
                        var sortOrder = $('.mgantsrow-select').val();
                        attrIds[selectId] = qty;
                        prodCookie[sortOrder]["groupedIds"] = attrIds;
                        localStorage.setItem('page_refresh_product', JSON.stringify(prodCookie));
                    }
                }
            });
        },
    });
    
    return $.mageants.fastorder_grouped;
});