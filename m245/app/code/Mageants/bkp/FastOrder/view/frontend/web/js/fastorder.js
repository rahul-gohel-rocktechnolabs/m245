define([
    'jquery',
    'underscore',
    'mage/template',
    'Magento_Catalog/js/price-utils',
    'js-cookie/js.cookie',
    'jquery/ui',
    'mage/translate',
    'mage/validation',
    'mage/mage',
    'Magento_Catalog/product/view/validation',
    'catalogAddToCart'
], function ($, _, mageTemplate, priceUtils, cookie) {
    "use strict";
    var product_tierprice = product_tierprice || {};

    $.widget('mageants.fastorder', {
        options: {
            row: 1,
            searchUrl: '',
            csvUrl: '',
            pageLoadUrl:'',
            fomatPrice: '',
            charMin: '',
            headerBackgroundColor: '',
            headerTextColor: '',
            suggestCache: {},
        },

        _create: function () {
            var opt = this.options,
                rowFirst = this.element.html(),
                self = this,
                timer = 0,
                tbodyEl = this.element.closest('tbody'),
                formDefault = $(tbodyEl).html(),
                rowAdd,
                row,
                maxRow = this.options.maxRow;
             

            $('#mgantsfastorder-form .mgantsaddline').click(function () {
                row = opt.row;
                maxRow = opt.maxRow;
                if (rowAdd > row) {
                    row = rowAdd;
                }

                row = parseInt(row);
                maxRow = parseInt(maxRow);
                
                if (row < maxRow) 
                {
                    rowAdd = self._addline(rowFirst, row);  
                }
                else{
                    var applyLink = 'index/show';
                    $.ajax({
                               url: applyLink,
                                data:  {
                                    apply_amount:maxRow
                                },
                                type: "post",
                                cache: false,
                                success: function(data) {
                                
                                },
                                error: function(data) {
                               
                                }
                            });
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                }                
            });

            $(document).on("click","button.mgantsbtn-ok", function () {
                if ($(".input-text.mgantssearch-input").val() == "") 
                {
                    $(this).parent().find("#mgantssearch-input-error").remove();
                    $(this).parent().append("<span id='mgantssearch-input-error' style='color: red;'>Enter Product Name or SKU</span>");
                }
                else{
                    $(this).parent().find("#mgantssearch-input-error").remove(); 
                }
                self._searchProduct($(this).prev(),opt.searchUrl);
            });

            $(document).on("change","input.mgantsupload", function () {
                self._uploadCsv($(this), opt.csvUrl);
            });
            
            $(document).on("keyup","input.mgantssearch-input", function (event) {
                $(this).parent().find("#mgantssearch-input-error").remove();                
                var input_col = $(this).parents(".mgantsheight-tr")
                var input_col_auto = $(this).parents(".mgantsheight-tr").find(".mgantsfastorder-autocomplete")
                if(input_col_auto.size()  && (event.keyCode=="38" || event.keyCode=="37" || event.keyCode=="40" || event.keyCode=="39" || event.keyCode=="13"))
                {
                    input_col_auto.show();
                    var curr_li =jQuery("li");
                    var ul = input_col_auto.find("ul");
                    if(event.keyCode=="38" || event.keyCode=="37") //up left
                    {
                        var tmpli = ul.find("li.active").prev("li");
                        if(tmpli.size())
                        {
                            curr_li = tmpli;
                        }
                        else
                        {
                            curr_li = ul.find("li").last();
                        }
                        
                    }
                    else if(event.keyCode=="40" || event.keyCode=="39")//down right
                    {
                        
                        var tmpli = ul.find("li.active").next("li");
                        if(tmpli.size())
                        {
                            curr_li = tmpli;
                        }
                        else
                        {
                            curr_li = ul.find("li").first();
                        }
                    }
                    else if(event.keyCode=="13")
                    {
                        event.preventDefault();
                        jQuery("#mgantsfastorder-form .mgantsfastorder-autocomplete ul li.active a").trigger("mousedown");
                        return false;
                    }
                    ul.find("li").removeClass("active");
                    curr_li.addClass("active")
                    var  scrtop = 0;
                    ul.find("li").each(function(){
                        if(jQuery(this).hasClass("active")) return false;
                        var li_h = jQuery(this).outerHeight();
                        scrtop += li_h;                     
                    })
                    input_col_auto.animate({ scrollTop: scrtop}, "slow")
                }
                else
                {
                      var _this = this;
                        if ($(_this).val().length >= opt.charMin) {
                            clearTimeout(timer);
                            timer = setTimeout(function () {
                                self._searchProduct(_this,opt.searchUrl);
                            }, 5);
                        }
                }
             })
             

            $(document).on("blur","input.mgantssearch-input", function () {
                var _this = this;
                $(_this).closest('.mgantsfastorder-row').find('.mgantsfastorder-autocomplete').hide();
            });

            $(document).on("click",".mgantsfastorder-row-action button", function () {
                self._resetRow(this,rowFirst);
            });

            $(document).on("click",".mgantsfastorder-row-edit button", function () {
                self._editRow(this);
            });

            $(document).on("click",".mgantsfastorder-row-image img", function () {
                self._showLightbox(this);
            });
            
            $(document).ready(function () {
                var prodCookieName = "page_refresh_product";
                if (localStorage.getItem('attributeids')) {
                    localStorage.removeItem('attributeids');
                }
                if(localStorage.getItem(prodCookieName)){
                    var page_refresh_product = JSON.parse(localStorage.getItem('page_refresh_product'));
                    var itemRefresh = {};
                    if (!_.isEmpty(page_refresh_product)) {
                        itemRefresh = page_refresh_product;
                        var tmpItemRefresh = {};
                        var index = 0;
                        for (var key in itemRefresh) {
                            if (!_.isEmpty(itemRefresh[key])) {
                                tmpItemRefresh[index] = itemRefresh[key];
                                index++;
                            }
                        }
                        localStorage.setItem("page_refresh_product", JSON.stringify(tmpItemRefresh));   
                    }
                }
                var jsonObj = [];
                if(localStorage.getItem("page_refresh_product")){
                    var pageRefreshProduct = JSON.parse(localStorage.getItem("page_refresh_product"));
                    $(pageRefreshProduct).each(function(i, item){
                        if (!_.isEmpty(item)) {
                            jsonObj.push(item);
                        }
                    });
                    self._pageLoadProduct(jsonObj, opt.pageLoadUrl);
                }
            });

            $(document).on("click",".mgantsfastorder-lightbox", function () {
                $(this).fadeTo('slow', 0.3, function () {
                    $(this).remove();
                }).fadeTo('slow', 1);
            });

            $(document).on("change",'.mgantsfastorder-row-qty .qty', function () {
                $('.mgantsfastorder-row-qty .qty').each(function () {
                    self._reloadTotalPrice(this,opt.fomatPrice);
                });
            });

            $('#mgantsfastorder-form').submit(function (e) {
                if (!self.validateForm('#mgantsfastorder-form')) {
                    e.preventDefault();
                   return;
                }
                e.preventDefault();
                var form = $(this);
                self._submitForm(form,formDefault);
            });
        },
        _addline: function (data, row) {
            // Remove Attributeids
            if (localStorage.getItem('attributeids')) {
                localStorage.removeItem('attributeids');
            }
            row = parseInt(row);
            var lineNew = '<tr class="mgantsfastorder-row mgantsrow" data-sort-order="'+row+'" id="mgantsfastorder-'+row+'">'+data+'</tr>';
            $('#mgantsfastorder-form table.mgantsfastorder-multiple-form tbody').append(lineNew);

            row = parseInt(row) + 1;
            return row;
        },
        _searchProduct: function (el,searchUrl) {
            var input = $(el).val(),
                $widget = this;
            if (input == '') {
                $(el).closest('.mgantsfastorder-row').find('.mgantsfastorder-autocomplete').empty();
                return false;
            }
            var sortOrder = $(el).closest('.mgantsfastorder-row').attr('data-sort-order');
            $(el).addClass('mgantsloading');
            var suggestCacheKey = 'mgants'+input;
            $widget._XhrKiller();
            if (suggestCacheKey in $widget.options.suggestCache) {
                $widget._getItemsLocalStorage(el, suggestCacheKey, sortOrder);
            } else {
                $widget.xhr = $.ajax({
                    type: 'post',
                    url: searchUrl,
                    data: {product:input,sort_order:sortOrder},
                    dataType: 'json',
                    success: function (data) {
                        $widget._setItemsLocalStorage(el, suggestCacheKey, JSON.stringify(data), sortOrder);
                        for (var key in data) {
                            if (data.hasOwnProperty(key)) {
                                if (data[key]['tier_price_'+data[key].product_id] != null && data[key]['tier_price_'+data[key].product_id] != 'undefined' && data[key]['tier_price_'+data[key].product_id] != undefined) {
                                    product_tierprice['id_' + data[key].product_id] = data[key]['tier_price_'+data[key].product_id];
                                }
                            }
                        }
                    },
                });
            }
        },
        _submitForm: function (el,formDefault) {
            var actionForm = $(el).attr('action'),
                serializeData = $(el).serialize();
            $('#mgantsfastorder-form tr').removeClass('mgantsrow-error');
            $('#mgantsfastorder-form td').removeClass('mgantshide-border');
            $.ajax({
                type: 'post',
                url: actionForm,
                data: serializeData,
                dataType: 'json',
                showLoader:true,
                success: function (data) {
                    if (data.status == true) {
                        $('#mgantsfastorder-form tbody').html(formDefault);
                         // Remove localstorage on add to cart
                        if(localStorage.getItem("page_refresh_product")){
                            localStorage.removeItem("page_refresh_product");
                        }
                        // Attribute ids
                        if (localStorage.getItem('attributeids')) {
                            localStorage.removeItem('attributeids');
                        }
                    } else if (data.status == false && data.row >= 0) {
                        $('#mgantsfastorder-form tbody #mgantsfastorder-'+data.row).addClass('mgantsrow-error');
                        if ($('#mgantsfastorder-form tbody #mgantsfastorder-'+data.row).next().length > 0) {
                            $('#mgantsfastorder-form tbody #mgantsfastorder-'+data.row).next().find('td').addClass('mgantshide-border');
                        } else {
                            $('#mgantsfastorder-form tfoot tr td').addClass('mgantshide-border');
                        }
                    }
                },
                error: function () {
                    console.log('Can not add to cart');
                }
            });
        },
        _resetRow: function (el,data) {
            
            var sortOrder = $(el).closest('.mgantsfastorder-row').attr('data-sort-order');
            var prodCookieName = "page_refresh_product";

            if(localStorage.getItem(prodCookieName)){
                var page_refresh_product = JSON.parse(localStorage.getItem('page_refresh_product'));
                if (sortOrder in page_refresh_product) {
                    page_refresh_product[sortOrder] = "";
                    localStorage.setItem('page_refresh_product', JSON.stringify(page_refresh_product)); 
                }
            }
            // Attribute ids
            if (localStorage.getItem('attributeids')) {
                localStorage.removeItem('attributeids');
            }
            $(el).closest('.mgantsfastorder-row').html(data);
            $(el).addClass('disabled');
            $('#mgantsfastorder-form tr').removeClass('mgantsrow-error');
            $('#mgantsfastorder-form td').removeClass('mgantshide-border');
        },
        _editRow: function (el) {
            var $this = el.closest( ".mgantsfastorder-row" );
            var addToCart = $($this).find( ".mgantsaddtocart-info" );
            var mgantsproduct_id = $(addToCart).find( ".mgantsproduct-id" ).val();
            $.cookie('mgantsproduct_id', mgantsproduct_id, {path: '/'});
            var mgantsaddtocart_option_div = $(addToCart).find( ".mgantsaddtocart-option" );
            var options_val = "";
            var custom_selected_value = "";
            var custom_option = $(addToCart).find( ".mgantsaddtocart-custom-option" );
            $(mgantsaddtocart_option_div).each(function(){
                var child = $(this).find( ".mageants-attribute-select" );

                $(child).each(function(){
                    var data_selectore_id = $(this).attr('data-selectore-id');
                    if (data_selectore_id) {
                        options_val = options_val+","+data_selectore_id+"=>"+$(this).val();
                    }
                    else{                        
                        options_val = options_val+","+$(this).val();
                    } 
                })    

                $.cookie('fastorder_options_val', options_val, {path: '/'});             
            })

            $(custom_option).each(function(){
                var child = $(this).find( ".mgantsproduct-custom-option-select" );
                $(child).each(function(){
                    custom_selected_value = $(this).val();
                })    

                $.cookie('custom_selected_value', custom_selected_value, {path: '/'});             
            })

            var productType = $(el).closest('tr.mgantsfastorder-row').find('.mgantsfastorder-autocomplete .mgantsproduct-type').val();

            if(productType=='configurable' || productType=='grouped'){
                $(el).closest('tr.mgantsfastorder-row').find('.mgantsfastorder-autocomplete .mgantsshow-popup').val(1);
            }
            $(el).closest('.mgantsfastorder-row').find('.mgantsfastorder-autocomplete li:first a').mousedown();

        },
        _showLightbox: function (el) {
            $('.mgantsfastorder-lightbox').remove();
            var img = $(el).parent().html();
            var elLightbox = '<div class="mgantsfastorder-lightbox">'+img+'</div>';
            $('form.mgantsfastorder-form').fadeTo('slow', 0.3, function () {
                $(this).append(elLightbox);
            }).fadeTo('slow', 1);
        },
        _getFormattedPrice: function (price,fomatPrice) {
            return priceUtils.formatPrice(price, fomatPrice);
        },
        _reloadTotalPrice: function (el,fomatPrice) {
            var totalPrice,
                totalPriceFomat,
                productCurId,
                qty = $(el).val(),
                price = $(el).next().val(),
                priceOption = $(el).closest('.mgantsfastorder-row-qty').find('.mgantsproduct-price-custom-option').val(),
                productId = $(el).closest('tr.mgantsfastorder-row').find('.mgantsaddtocart-info .mgantsproduct-id').val(),
                productType = $(el).closest('tr.mgantsfastorder-row').find('.mgantsfastorder-autocomplete .mgantsproduct-type').val(),
                obj = {},
                row = $(el).closest('tr.mgantsfastorder-row').attr('data-sort-order'),
                sortOrder = $(el).closest('tr.mgantsfastorder-row').attr('data-sort-order');
            obj = product_tierprice['id_'+productId];
            if (qty > 0 && obj != null && obj != 'undefined' && obj != undefined && productType != 'grouped') {
                var qtyTotal = parseFloat(qty);
                $('.mgantsfastorder-row .mgantsfastorder-row-qty .mgantsproduct-id-calc').each(function () {
                    var productIdClone = $(this).val(),
                        rowClone = $(this).closest('tr.mgantsfastorder-row').attr('data-sort-order'),
                        qtyClone = 0;
                    if (row != rowClone) {
                        qtyClone = $(this).closest('tr.mgantsfastorder-row').find('.mgantsfastorder-row-qty .qty').val();
                    }
                    if (parseInt(productIdClone) == parseInt(productCurId)) {
                        qtyTotal += parseFloat(qtyClone);
                    }
                });
                for (var key in obj) {
                    if (typeof obj[key] != 'object') {
                        if (parseFloat(qtyTotal) >= parseFloat(key)) {
                            price = obj[key] + parseFloat(priceOption);
                        }
                    } else {
                        for (var key2 in obj[key]) {
                            if (parseFloat(qtyTotal) >= parseFloat(key2)) {
                                price = obj[key][key2] + parseFloat(priceOption);
                            }
                        }
                    }
                }
                $(el).next().val(price);
            }
            if (productId) {
                var tierPriceUpdate = $(el).closest('tr.mgantsfastorder-row.mgantsrow').find('.mgantsproduct-tier-price-update').val();
                var result = {};
                tierPriceUpdate.split(',').forEach(function(x){
                    var arr = x.split('=');
                    arr[1] && (result[arr[0]] = arr[1]);
                });
                $.each(result, function(tierQty, tierPrice ) {
                  if(parseInt(qty) >= tierQty){
                    if(tierPrice>0 ){
                        price = tierPrice;
                    }
                  }
                });
                totalPrice = parseFloat(qty) * parseFloat(price);
                totalPriceFomat = this._getFormattedPrice(totalPrice, fomatPrice);

                var prodCookieId = "page_refresh_product";

                if(totalPrice || $.isNumeric(totalPrice)){
                    if(localStorage.getItem(prodCookieId)){
                        var prodCookieData = localStorage.getItem(prodCookieId);
                        if (prodCookieData) {
                            var prodCookie = JSON.parse(prodCookieData);
                        }

                        if(prodCookie[sortOrder]['qty']){
                            prodCookie[sortOrder]['qty'] = qty;
                        }

                        if(prodCookie[sortOrder]['totalPrice']){
                            prodCookie[sortOrder]['totalPrice'] = totalPrice;
                        }else{
                            prodCookie[sortOrder]['totalPrice'] = totalPrice;
                        }

                        if(prodCookie[sortOrder]['totalPriceFomat']){
                            prodCookie[sortOrder]['totalPriceFomat'] = totalPriceFomat;
                        }else{
                            prodCookie[sortOrder]['totalPriceFomat'] = totalPriceFomat;
                        }
                        localStorage.setItem(prodCookieId, JSON.stringify(prodCookie));
                    }
                    $(el).closest('tr.mgantsfastorder-row').find('.mgantsfastorder-row-price .configprice').html(totalPriceFomat);
                    $(el).closest('tr.mgantsfastorder-row').find('.mgantsfastorder-row-price .price').html(totalPriceFomat);
                    $('#mgantsfastorder-form tbody tr').removeClass('mgantsrow-error');
                    $('#mgantsfastorder-form tbody td').removeClass('mgantshide-border');     
                }
            }
        },
        _XhrKiller: function () {
            var $widget = this;
            if ($widget.xhr !== undefined && $widget.xhr !== null) {
                $widget.xhr.abort();
                $widget.xhr = null;
            }
        },
        _getItemsLocalStorage: function (el, suggestCacheKey, sortOrder) {
            var $widget = this,
                data1 = $widget.options.suggestCache[suggestCacheKey],
                data2 = '',
                html = '';
            if (data1 && data1 != "null") {
                data2 = JSON.parse(data1);
            }
            html = mageTemplate('#mgantsfastorder-search-complete',{data:data2});
            $('#mgantsfastorder-'+sortOrder+'').find('.mgantsfastorder-autocomplete').show();
            $('#mgantsfastorder-'+sortOrder+'').find('.mgantsfastorder-autocomplete').html(html);
            $(el).removeClass('mgantsloading');

        },
        _setItemsLocalStorage: function (el, suggestCacheKey, data, sortOrder) {
            var $widget = this;
            $widget.options.suggestCache[suggestCacheKey] = data;
            $widget._getItemsLocalStorage(el, suggestCacheKey, sortOrder);
            
        },
        _pageLoadProduct: function (item, pageLoadUrl) {
            var $widget = this,
                lengthObj = 0,
                data = item;

            if (!item) {
                return false;
            }
            $widget._XhrKiller();
            $widget.xhr = $.ajax({
                type: 'post',
                url: pageLoadUrl,
                data: {prodData:data},
                dataType: 'json',
                success: function (res) {
                    // item.el.val('');
                   
                    if (res) {
                        var obj = JSON.stringify(res);
                        var obj = JSON.parse(obj);

                        lengthObj = obj.length;
                        $widget._checkLineCsv(lengthObj);
                        for (var key in obj) {
                            var data = {},
                                html;
                            if (obj.hasOwnProperty(key)) {
                                data[0] = obj[key];
                                html = mageTemplate('#mgantsfastorder-search-complete',{data:data});
                                $('#mgantsfastorder-form .mgantsrow').each(function () {
                                    var sortOrder,
                                        self = $(this);
                                    if (self.find('.mgantsrow-suggest').length > 0) {
                                        return true;
                                    }
                                    sortOrder = self.attr('data-sort-order');
                                    $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-autocomplete').html(html);
                                    $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-autocomplete .mgantsrow-suggest:first').mousedown();
                                    return false;
                                });
                            }
                        }
                    }
                },
                error: function () {
                
                     item.el.val('');
                  
                }
            });
        },
        _uploadCsv: function (el, csvUrl) {
            var file_data = el.prop("files")[0],
                data = new FormData(),
                $widget = this,
                lengthObj = 0;
            if (!file_data) {
                return false;
            }
            $widget._XhrKiller();
            data.append("file", file_data);
            $widget.xhr = $.ajax({
                type: 'post',
                url: csvUrl,
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                showLoader:true,
                success: function (res) {
                    el.val('');
                    if (res) {
                        var obj = JSON.parse(res);
                        lengthObj = obj.length;
                        $widget._checkLineCsv(lengthObj);
                        for (var key in obj) {
                            var data = {},
                                html;
                            if (obj.hasOwnProperty(key)) {
                                data[0] = obj[key];
                                html = mageTemplate('#mgantsfastorder-search-complete',{data:data});
                                $('#mgantsfastorder-form .mgantsrow').each(function () {
                                    var sortOrder,
                                        self = $(this);
                                    if (self.find('.mgantsrow-suggest').length > 0) {
                                        return true;
                                    }
                                    sortOrder = self.attr('data-sort-order');
                                    $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-autocomplete').html(html);
                                    $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-autocomplete .mgantsrow-suggest:first').mousedown();
                                    return false;
                                });
                            }
                        }
                    }
                },
                error: function () { 
                    el.val('');
                  
                }
            });
        },
        _checkLineCsv: function (lengthObj) {
            var lineCurrent,
                lineUse,
                lineSurplus,
                lineNew,
                i;
            lineCurrent = $('#mgantsfastorder-form .mgantsrow').size();
            lineUse = $('#mgantsfastorder-form .mgantsfastorder-row .mgantsfastorder-autocomplete ul').size();
            lineSurplus = parseInt(lineCurrent) - parseInt(lineUse);
            if (lengthObj <= lineSurplus) {
                return;
            }
            lineNew = parseInt(lengthObj) - parseInt(lineSurplus);
            for (i = 0; i < lineNew; i++) {
                $('#mgantsfastorder-form .mgantsaddline').click();
            }
        },
        /* Validation Form*/
        validateForm: function (form) {
            return $(form).validation() && $(form).validation('isValid');
        },
    });
    return $.mageants.fastorder;
});
