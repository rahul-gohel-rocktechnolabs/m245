define([
    'jquery',
    'js-cookie/js.cookie',
    'jquery/ui',
], function ($, cookie) {
    'use strict';
    var itemsArray = {};
    var confOptions = [];
    var confOpt = {};
    $.widget('mageants.option', {
        options: {
            resetButtonSelector: '.mgantsfastorder-row-action button',
            cancelButtonSelector: 'button#mgantscancel-option',
            selectButtonSelector: 'button#mgantsselect-option',
            formSubmitSelector: 'form#mgantsfastorder-form-option',
            optionsSelector: '#mgantsfastorder-form-option .product-custom-option',
        },
        _create: function () {
            this._bind();
        },
        _bind: function () {
            var self = this;
            this.createElements();            
        },
        createElements: function () {
            if (!($('#mgantscontent-option-product').length)) {
                $(document.body).append('<div class="mgantscontent-option-product" id="mgantscontent-option-product"></div>');
            }
            this.options.optionsPopup = $('#mgantscontent-option-product');
            this.options.optionsPopup.hide();
        },
        showPopup: function (selectUrl, el) {
            var self = this,
                productId = $(el).find('.mgantsproduct-id').val(),
                sortOrder = $(el).closest('.mgantsfastorder-row').attr('data-sort-order');
            $.ajax({
                url: selectUrl,
                data: {productId: productId,sortOrder: sortOrder},
                type: 'post',
                dataType: 'json',
                showLoader:true,
                success: function (res) {
                    if (res.popup_option) {
                        var hasValue = 0;
                        self.options.optionsPopup.html(res.popup_option);
                        self.options.optionsPopup.show();
                        $(self.options.cancelButtonSelector).click(function (event) {   
                            var $this = self.options.optionsPopup;
                            var $this_option_div = $($this).find("form#mgantsfastorder-form-option .mgantsproduct-option");
                            var productId = $($this_option_div).find(".mgantsproduct-id");
                            var this_product_id = $(productId).val();
                            var selected_product_id = $.cookie('mgantsproduct_id');
                            if (this_product_id == selected_product_id) {
                                var $group_table = $($this).find("form table tbody");
                                var parent = $(this).closest( ".mgantsproduct-option" );
                                var main_attribute = $(parent).find(".mgantsswatch-attribute");
                                var custom_selected_value = $.cookie('custom_selected_value');

                                if (custom_selected_value) {
                                    var $this_select = $($this_option_div).find("select");
                                    $($this_select).val(custom_selected_value).trigger('change');
                                    $($this_select).each(function(){
                                        var option_val = $(this).val();
                                        if (option_val == custom_selected_value) {
                                            $(this).attr("selected","selected");
                                            hasValue = 1;
                                        }
                                    })  
                                }

                                $.each($group_table, function () {
                                    var $this_table = this;
                                    var $options_val = $.cookie('fastorder_options_val');
                                    var fast_group_option_array = $options_val.split(",");
                                    $.each(fast_group_option_array, function(){
                                        var options = this.split("=>");
                                        var group_value = $($this_table).find("tr td.qty .mageants-attribute-select");
                                        var sub_product_id  = $(group_value).attr("data-selectore-id");
                                        if ($.inArray(sub_product_id, options) == 0 && options != "") {
                                            $(group_value).val(options[1]);
                                            $($this_table).find("tr td.qty .mageants-attribute-select").change();
                                            if ($(group_value).val() != 0) {
                                                hasValue = 1;
                                            }

                                        }
                                    });
                                });

                                $.each(main_attribute, function () {
                                    var option = $(this).find(".mgantsswatch-attribute-options .mgantsswatch-option");
                                    $.each(option, function () {
                                        var attr_div = this;
                                        var $options_val = $.cookie('fastorder_options_val');
                                        var res = $options_val.split(",");
                                        var mgantsoption_id = $(this).attr("mgantsoption-id");
                                        $.each(res, function () { 
                                            if (mgantsoption_id == this) {   
                                                if ($(attr_div).hasClass('selected')) {                        
                                                    $(attr_div).click();                   
                                                    $(attr_div).click();   
                                                    hasValue = 1;
                                                }     
                                                else{                                                                       
                                                    $(attr_div).click(); 
                                                    hasValue = 1;
                                                }                  
                                            }
                                        });
                                    });
                                });                                
                            }
                            if (hasValue != 1) {                                
                                self.closePopup();
                                $('tr#mgantsfastorder-'+sortOrder).find(self.options.resetButtonSelector).click();
                            }
                            else{                                
                                self.selectOption(sortOrder);
                                self.closePopup();
                            }
                            $.cookie('mgantsproduct_id', '', { expires: -1, path: '/'});
                            $.cookie('fastorder_options_val', '', { expires: -1, path: '/'});
                            $.cookie('custom_selected_value', '', { expires: -1, path: '/'});
                        });
                        $(self.options.selectButtonSelector).click(function (event) {
                           var parent = $(this).closest( ".mgantsproduct-option" );
                           var main_attribute = $(parent).find(".mgantsswatch-attribute");
                           $.each(main_attribute, function () {
                                var option = $(this).find(".mgantsswatch-attribute-options .mgantsswatch-option");
                                $.each(option, function () {
                                    if ($(this).hasClass('selected')) {
                                        $(this).click();
                                        $(this).click();
                                        hasValue = 1;
                                    }
                                });
                            });
                            event.preventDefault();
                            var isValid = $(self.options.formSubmitSelector).valid();
                            if (isValid) {
                                self.selectOption(sortOrder);
                            }
                            $.cookie('mgantsproduct_id', '', { expires: -1, path: '/'});
                            $.cookie('fastorder_options_val', '', { expires: -1, path: '/'});
                            $.cookie('custom_selected_value', '', { expires: -1, path: '/'});
                        });
                    }
                },
                error: function (response) {
                    console.log('Can not load option');
                }
            });
        },
        selectProduct: function (el) {
            var productSku = $(el).find('.mgantsproduct-sku-select').val(),
                elProductName,
                productId,
                productUrl = $(el).find('.mgantsproduct-url').val(),
                productImage = $(el).find('.mgantsproduct-image').html(),
                productName = $(el).find('.mgantsproduct-name .product.name').text(),
                productPrice = $(el).find('.mgantsproduct-price').html(),
                productPriceAmount = $(el).find('.mgantsproduct-price-amount').val(),
                rowEl = $(el).closest('tr.mgantsfastorder-row'),
                liSelect = $(el).parent(),
                qty = $(el).find('.mgantsproduct-qty').val(),
                sortOrder = $(el).closest('.mgantsfastorder-row').attr('data-sort-order'),
                productType = $(el).find('.mgantsproduct-type').val();
                
            var pageRefreshObj = 
                {
                    'productSku' : productSku,
                    'elProductName' : '<a href="'+productUrl+'" alt="'+productName+'" class="product name" target="_blank">'+productName+'</a>',
                    'productId' : $(el).find('.mgantsproduct-id').val(),
                    'productUrl' : productUrl,
                    'productImage' : productImage,
                    'productName' : productName,
                    'productType' : productType,
                    'productPrice' : productPrice,
                    'productPriceAmount' : productPriceAmount,
                    'rowEl' : rowEl,
                    'liSelect' : liSelect,
                    'qty' : qty,
                    'el' : el,
                    'sortOrder' :sortOrder
                }

            var page_refresh_product = localStorage.getItem('page_refresh_product');
            
            if(!localStorage.getItem("page_refresh_product")){
                itemsArray[sortOrder] = pageRefreshObj;
            }else{
                var page_refresh_product = JSON.parse(localStorage.getItem('page_refresh_product'));
                if (!(sortOrder in page_refresh_product)) {
                    page_refresh_product[sortOrder] = pageRefreshObj;
                    localStorage.setItem('page_refresh_product',JSON.stringify(page_refresh_product));
                }
            }

            $('#mgantsfastorder-form tr').removeClass('mgantsrow-error');
            $('#mgantsfastorder-form td').removeClass('mgantshide-border');
            $(rowEl).find('.mgantsaddtocart-info .mgantsaddtocart-option').empty();
            $(rowEl).find('.mgantsfastorder-row-name .mgantsproduct-option-select ul').empty();
            $(rowEl).find('.mgantsfastorder-row-name .mgantsproduct-baseprice ul').empty();
            $(rowEl).find('.mgantsfastorder-row-edit button').addClass('disabled');
            $(rowEl).find('.mgantsfastorder-row-qty input.qty').removeAttr('readonly');
            $(rowEl).find('.mgantsfastorder-row-action button').removeClass('disabled');

            productId = $(el).find('.mgantsproduct-id').val();
            if(localStorage.getItem('page_refresh_product')){
                var prodCookieData = localStorage.getItem('page_refresh_product');
                if (prodCookieData) {
                    var prodCookie = JSON.parse(prodCookieData);
                }

                if(prodCookie[sortOrder]['productOptions']){
                    var productOptions = prodCookie[sortOrder]['productOptions'];
                    $(rowEl).find('.mgantsfastorder-row-name .mgantsproduct-option-select ul').html(productOptions);
                }

                if(prodCookie[sortOrder]['groupedIds']){
                    var opt = prodCookie[sortOrder]['groupedIds'];
                    var optionData = '';
                    for(var i in opt){
                        if (opt[i] != null) {
                            optionData += '<input type="number" name="mageants-fastorder-super_group['+sortOrder+']['+i+']" data-selector="mageants-fastorder-super_group['+i+']" data-selectore-id="'+i+'" maxlength="12" value="'+opt[i]+'" title="Qty" class="input-text qty mageants-attribute-select">';
                        }
                    }
                    prodCookie[sortOrder]['addtocartOption'] = optionData;
                    $(rowEl).find('.mgantsaddtocart-info.mgantsfastorder-hidden .mgantsaddtocart-option').html(optionData);
                    localStorage.setItem('page_refresh_product', JSON.stringify(prodCookie));
                }
                else if(prodCookie[sortOrder]['options']){
                    var opt = prodCookie[sortOrder]['options'];
                    var optionData = '';
                    for(var i in opt){
                        optionData += '<input type="hidden" class="mageants-attribute-select" name="mgantsfastorder-super_attribute['+sortOrder+']['+i+']" value="'+opt[i]+'">';
                    }
                    prodCookie[sortOrder]['addtocartOption'] = optionData;
                    $(rowEl).find('.mgantsaddtocart-info.mgantsfastorder-hidden .mgantsaddtocart-option').html(optionData);
                    localStorage.setItem('page_refresh_product', JSON.stringify(prodCookie));
                }else{
                    if(prodCookie[sortOrder]['addtocartOption']){
                        var addtocartOption = prodCookie[sortOrder]['addtocartOption'];
                        $(rowEl).find('.mgantsaddtocart-info.mgantsfastorder-hidden .mgantsaddtocart-option').html(addtocartOption);
                    }
                }
                prodCookie[sortOrder]['sortOrder'] = sortOrder;
                localStorage.setItem('page_refresh_product', JSON.stringify(prodCookie));
                
                // Edit Button
                var groupedProduct = false;
                if(prodCookie[sortOrder]['productType']){
                    var productType = prodCookie[sortOrder]['productType'];
                    if(productType=='configurable' || productType=='grouped'){
                        $(rowEl).find('.mgantsfastorder-row-edit button').removeClass('disabled');     
                    }
                    if (productType=='grouped') {
                        groupedProduct = true;
                    }
                }
            }

            $(rowEl).find('.mgantsfastorder-img').html(productImage);
            if (qty && qty > 0) {
                $(rowEl).find('.mgantsfastorder-row-qty input.qty').val(qty);
            }
            elProductName = '<a href="'+productUrl+'" alt="'+productName+'" class="product name" target="_blank">'+productName+'</a>';
            $(rowEl).find('.mgantsfastorder-row-qty .mgantsproduct-id-calc').val(productId);
            $(rowEl).find('.mgantsfastorder-row-name .mgantsproduct-name-select').html(elProductName);
            $(rowEl).find('.mgantsfastorder-row-qty .mgantsproduct-price-number').val(productPriceAmount);
            if (groupedProduct == true) {
                $(rowEl).find('.mgantsfastorder-row-name .mgantsproduct-baseprice ul').empty();
            }else{
                $(rowEl).find('.mgantsfastorder-row-name .mgantsproduct-baseprice ul').append('<li>'+productPrice+'</li>');
            }
            $(rowEl).find('.mgantsfastorder-row-ref .mgantssearch-input').val(productSku);
            $(el).closest('.mgantsfastorder-autocomplete').hide();
            $(el).closest('.mgantsfastorder-row').find('.mgantsaddtocart-info .mgantsproduct-id').val(productId);
            $(el).closest('.mgantsfastorder-autocomplete').find('li').not(liSelect).remove();
            $(rowEl).find('.mgantsfastorder-row-qty .qty').change();
        },
        closePopup: function () {
            this.options.optionsPopup.empty().hide();
            $('.loading-mask').hide();
        },
        selectOption: function (sortOrder) {
            var self = this,
                disabledSelect = false,
                selectedLinks = '',
                elAddtocart = $('#mgantsfastorder-'+sortOrder+'').find('.mgantsaddtocart-option'),
                elAddtocartOption = $('#mgantsfastorder-'+sortOrder+'').find('.mgantsaddtocart-custom-option'),
                priceInfo,
                linksInfo,
                groupedPrice = 0,
                elProductinfo = $('#mgantsfastorder-'+sortOrder+'').find('.mgantsfastorder-row-name .mgantsproduct-option-select ul'),
                elPricetinfo = $('#mgantsfastorder-'+sortOrder+'').find('.mgantsfastorder-row-name .mgantsproduct-baseprice ul'),
                elCustomOption = $('#mgantsfastorder-'+sortOrder+'').find('.mgantsfastorder-row-name .mgantsproduct-custom-option-select ul'),
                productId = $('#mgantsfastorder-'+sortOrder+'').find('.mgantsaddtocart-info .mgantsproduct-id').val();

            $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-row-qty .qty').removeAttr('readonly');
            elProductinfo.empty();
            elPricetinfo.empty();
            elAddtocart.empty();
            elCustomOption.empty();
            elAddtocartOption.empty();

            // move id child product configurable to form
            if ($('#mgantsfastorder-form-option .mgantsswatch-attribute').length > 0) {
                var priceNew = $('#mgantscontent-option-product .mgantsproduct-info-price .price-wrapper').attr('data-price-amount');
                var childId = $('#mgantsfastorder-form-option .mgantsproduct-child-id').val();

                if(childId){
                    // store ChildId 
                    var prodCookieId = localStorage.getItem('page_refresh_product');

                    if(prodCookieId){
                        var prodCookieData = JSON.parse(prodCookieId);
                        self.setLocalStorage(prodCookieData, "childProductId", childId, sortOrder);

                        if(prodCookieData[sortOrder]['productOptions']){
                            prodCookieData[sortOrder]['productOptions'] = "";
                        }

                        if(prodCookieData[sortOrder]['addtocartOption']){
                            prodCookieData[sortOrder]['addtocartOption'] = "";
                        }
                        localStorage.setItem('page_refresh_product', JSON.stringify(prodCookieData));
                    }
                }

                $('#mgantsfastorder-'+sortOrder+' .mgantsfastorder-row-qty .mgantsproduct-price-number').val(priceNew);
                
            }
            this.options.optionsPopup.find('#mgantsfastorder-form-option .mageants-attribute-select').each(function (event) {
                confOptions.push($(this).val());
                if ($('#mgantsfastorder-form-option .mgantsswatch-attribute').length > 0) {// configurable product option
                    disabledSelect = self._selectConfigurable(this,disabledSelect,elAddtocart,elProductinfo, sortOrder, confOptions);
                } else if ($('#mgantsfastorder-form-option .field.downloads').length > 0) {// downloadable product links
                    selectedLinks = self._selectDownloads(this,elAddtocart,selectedLinks);
                } else if ($('#mgantsfastorder-form-option .table-wrapper.grouped').length > 0) {//grouped product child qty
                    var priceChild = 0;
                    if ($(this).val() != '') {
                        $(this).clone().appendTo(elAddtocart);
                    }
                    if ($(this).next().val() != '') {
                        priceChild = $(this).next().val();
                    }
                    groupedPrice = parseFloat(groupedPrice) + parseFloat(priceChild);

                    // Product options set into localstorage for grouped product
                    var addtocartOption = $('#mgantsfastorder-'+sortOrder+' .mgantsfastorder-hidden .mgantsaddtocart-option').html();
                    var prodCookieId = localStorage.getItem('page_refresh_product');
                    
                    if(prodCookieId){
                        var prodCookie = JSON.parse(prodCookieId);

                        if(addtocartOption){
                            if(prodCookie[sortOrder]['addtocartOption']){
                                var addtocartoptionsjson = prodCookie[sortOrder]['addtocartOption'];
                                prodCookie[sortOrder]['addtocartOption'] = addtocartoptionsjson+addtocartOption;
                                prodCookie[sortOrder]['groupedPrice'] = groupedPrice;
                            }else{
                                prodCookie[sortOrder]['addtocartOption'] = addtocartOption;
                                prodCookie[sortOrder]['groupedPrice'] = groupedPrice;
                            }
                        }
                        localStorage.setItem('page_refresh_product', JSON.stringify(prodCookie));
                    }
                }
            });
            if ($('#mgantsfastorder-form-option .field.downloads').length > 0) {
                if (selectedLinks == '') {
                    disabledSelect = true;
                    $('#mgantslinks-advice-container').show();
                } else {
                    var linksLabel = $('#mgantsfastorder-form-option .mgantsrequired-label').html();
                    linksInfo = '<li><span class="label">' + linksLabel + '</span></li>' + selectedLinks;
                    $(elProductinfo).append(linksInfo);
                }
            } else if ($('#mgantsfastorder-form-option .table-wrapper.grouped').length > 0) {
                $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-row-qty .mgantsproduct-price-number').val(groupedPrice);
                $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-row-qty .qty').val(1);
                $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-row-name .mgantsproduct-baseprice').remove();
                $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-row-qty .qty').attr('readonly', 'readonly');
                if (groupedPrice <= 0) {
                    disabledSelect = true;
                    $('.mgantsvalidation-message-box').show();
                }
            }
            $(this.options.optionsSelector).each(function () {
                self._onOptionChanged(this, sortOrder, elAddtocartOption);
            });

            if (disabledSelect == false) {
                priceInfo = $('#mgantscontent-option-product .mgantsproduct-info-price .price-wrapper').html();
                $(elProductinfo).find('li .price').parent().remove();
                if (priceInfo) {
                    $(elPricetinfo).append('<li>'+priceInfo+'</li>');
                    //set option price to subtotal
                    var configPrice = $("#mgantscontent-option-product .mgantsproduct-info-price .price-wrapper span.price").text();
                    $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-row-price').empty();
                    $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-row-price').append('<span class="configprice">'+configPrice+'</span>');
                    //----//
                 }
                $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-row-edit button').removeClass('disabled');
                $('#mgantsfastorder-'+sortOrder).find('.mgantsfastorder-row-qty .qty').change();
                self.closePopup();
            }
        },

        _selectConfigurable: function (el, disabledSelect,elAddtocart,elProductinfo, sortOrder, confOptions) {
            var selectInfo;
            var addtocartOption;
            if ($(el).val() == '') {
                disabledSelect = true;
                if ($(el).parent().find('.mgantsmage-error').length == 0) {
                    $(el).parent().append('<div generated="true" class="mgantsmage-error">This is a required field.</div>');
                }
            } else {
                var selectLabel = $(el).parent().find('.mgantsswatch-attribute-label').text();
                var selectValue = $(el).parent().find('.mgantsswatch-attribute-selected-option').text();
                if (selectValue == '') {
                    selectValue = $(el).parent().find('.mgantsswatch-select option:selected').text();
                }
                selectInfo = '<li><span class="label">' + selectLabel + '</span>&nbsp;:&nbsp;' + selectValue + '</li>';
                $(el).parent().find('.mgantsmage-error').remove();
                $(el).clone().appendTo(elAddtocart);
                $(elProductinfo).append(selectInfo);

                addtocartOption = $('#mgantsfastorder-'+sortOrder+' .mgantsfastorder-hidden .mgantsaddtocart-option').html();
                
                // Product options set into localstorage for config product
                var prodCookieId = localStorage.getItem('page_refresh_product');
                
                if(prodCookieId){
                    var prodCookie = JSON.parse(prodCookieId)
                    
                    if(prodCookie[sortOrder]['productOptions']){
                        var optionsjson = prodCookie[sortOrder]['productOptions'];
                        prodCookie[sortOrder]['productOptions'] = optionsjson+selectInfo;
                    }else{
                        //var optionsJson = {'productOptions' : selectInfo };
                        prodCookie[sortOrder]['productOptions'] = selectInfo;
                    }

                    if(addtocartOption){
                        if(prodCookie[sortOrder]['addtocartOption']){
                            var addtocartoptionsjson = prodCookie[sortOrder]['addtocartOption'];
                            prodCookie[sortOrder]['addtocartOption'] = addtocartOption;
                        }else{
                            prodCookie[sortOrder]['addtocartOption'] = addtocartOption;
                        }
                    }

                    if (confOptions && localStorage.getItem('attributeids')) {
                        var attrIds = JSON.parse(localStorage.getItem('attributeids'));
                        for (var i = 0; i < attrIds.length; i++){
                             confOpt[attrIds[i]] = confOptions[i];
                        }
                        if(prodCookie[sortOrder]['options']){
                            var confOptionsjson = prodCookie[sortOrder]['options'];
                            prodCookie[sortOrder]['options'] = confOpt;
                        }else{
                            prodCookie[sortOrder]['options'] = confOpt;
                        }
                        
                    }
                    localStorage.setItem('page_refresh_product', JSON.stringify(prodCookie));
                }
            }
            return disabledSelect;
        },

        _selectDownloads: function (el,elAddtocart,selectedLinks) {
            if ($(el).val() != '') {
                $(el).clone().appendTo(elAddtocart);
                var linkOption = $(el).next().html();
                selectedLinks += '<li>' + linkOption + '</li>';
            }
            return selectedLinks;
        },

        setLocalStorage: function(prodCookieData, prodKey, prodValue, sortOrder){
    
            if (prodCookieData) {
                if(prodCookieData[sortOrder][prodKey]){
                    prodCookieData[sortOrder][prodKey] = prodValue;
                }else{
                    prodCookieData[sortOrder]["childProductId"] = prodValue;
                }
                localStorage.setItem('page_refresh_product', JSON.stringify(prodCookieData));
            }
        },

        _onOptionChanged: function (el, sortOrder, elAddtocartOption) {
            var element = $(el),
                label = '',
                productPrice = 0,
                option = '',
                id = '',
                idSelect = '',
                price = 0,
                optionValue = element.val(),
                optionName = element.prop('name'),
                optionType = element.prop('type'),
                elPrice = $('#mgantsfastorder-'+sortOrder+'').find('.mgantsfastorder-row-qty .mgantsproduct-price-number'),
                elPriceOption = $('#mgantsfastorder-'+sortOrder+'').find('.mgantsfastorder-row-qty .mgantsproduct-price-custom-option'),
                elOptionInfo = $('#mgantsfastorder-'+sortOrder+'').find('.mgantsfastorder-row-name .mgantsproduct-custom-option-select ul');
            switch (optionType) {
                case 'text':
                    if (element.val() != '') {
                        label = element.closest('.mgantsoptions-info').find('.label:first').html();
                        price = element.closest('.field').find('.price-container .price-wrapper').attr('data-price-amount');
                        if (price > 0) {
                            productPrice = parseFloat(price) + parseFloat(elPrice.val());
                            elPrice.val(parseFloat(price) + parseFloat(elPrice.val()));
                            elPriceOption.val(parseFloat(price) + parseFloat(elPriceOption.val()));
                        }
                        element.closest('.control').find('.mgantsproduct-custom-option-select').val(element.val());
                        element.closest('.control').find('.mgantsproduct-custom-option-select').clone().appendTo(elAddtocartOption);
                        option = element.val();
                        elOptionInfo.append('<li><span class="label">'+label+'</span></li><li>'+option+'</li>');
                    }
                    break;
                case 'textarea':
                    if (element.val() != '') {
                        label = element.closest('.mgantsoptions-info').find('.label:first').html();
                        price = element.closest('.textarea').find('.price-container .price-wrapper').attr('data-price-amount');
                        if (price > 0) {
                            productPrice = parseFloat(price) + parseFloat(elPrice.val());
                            elPrice.val(parseFloat(price) + parseFloat(elPrice.val()));
                            elPriceOption.val(parseFloat(price) + parseFloat(elPriceOption.val()));
                        }
                        element.closest('.control').find('.mgantsproduct-custom-option-select').val(element.val());
                        element.closest('.control').find('.mgantsproduct-custom-option-select').appendTo(elAddtocartOption);
                        option = element.val();
                        elOptionInfo.append('<li><span class="label">'+label+'</span></li><li>'+option+'</li>');
                    }
                    break;

                case 'radio':
                    if (element.is(':checked')) {
                        price = element.attr('price');
                        if (price > 0) {
                            productPrice = parseFloat(price) + parseFloat(elPrice.val());
                            elPrice.val(parseFloat(price) + parseFloat(elPrice.val()));
                            elPriceOption.val(parseFloat(price) + parseFloat(elPriceOption.val()));
                        }
                        element.next().clone().appendTo(elAddtocartOption);
                        label = element.closest('.mgantsoptions-info').find('.label:first').html();
                        option = element.closest('.field').find('.label:first').html();
                        if (element.val()) {
                            elOptionInfo.append('<li><span class="label">'+label+'</span></li><li>'+option+'</li>');
                        }
                    }
                    break;
                case 'select-one':
                    price = element.find(":selected").attr('price');
                    label = element.closest('.mgantsoptions-info').find('.label:first').html();
                    option = element.find(":selected").text();
                    if (price > 0) {
                        productPrice = parseFloat(price) + parseFloat(elPrice.val());
                        elPrice.val(parseFloat(price) + parseFloat(elPrice.val()));
                        elPriceOption.val(parseFloat(price) + parseFloat(elPriceOption.val()));
                    }
                    element.closest('.control').find('.mgantsproduct-custom-option-select').val(element.val());
                    element.closest('.control').find('.mgantsproduct-custom-option-select').clone().appendTo(elAddtocartOption);
                    if (element.val()) {
                        elOptionInfo.append('<li><span class="label">'+label+'</span></li><li>'+option+'</li>');
                    }
                    break;

                case 'select-multiple':
                    label = element.closest('.mgantsoptions-info').find('.label:first').html();
                    element.find(":selected").each(function (i, selected) {
                        price += parseFloat($(selected).attr('price'));
                        id += $(selected).val() + ',';
                        option += '<li>'+$(selected).text()+'</li>';
                    });
                    if (price > 0) {
                        productPrice = parseFloat(price) + parseFloat(elPrice.val());
                        elPrice.val(parseFloat(price) + parseFloat(elPrice.val()));
                        elPriceOption.val(parseFloat(price) + parseFloat(elPriceOption.val()));
                    }
                    element.closest('.control').find('.mgantsproduct-custom-option-select').val(id);
                    element.closest('.control').find('.mgantsproduct-custom-option-select').clone().appendTo(elAddtocartOption);
                    elOptionInfo.append('<li><span class="label">'+label+'</span></li><li>'+option+'</li>');
                    break;

                case 'checkbox':
                    if (element.is(':checked')) {
                        idSelect = element.closest('.mgantsoptions-info').find('.label:first').attr('for');
                        if (elOptionInfo.find('.'+idSelect).length == 0) {
                            label = element.closest('.mgantsoptions-info').find('.label:first').html();
                        }
                        price = parseFloat($(element).attr('price'));
                        element.next().clone().appendTo(elAddtocartOption);
                        option = '<li>'+element.closest('.field').find('.label:first').html()+'</li>';
                    }
                    if (price > 0) {
                        productPrice = parseFloat(price) + parseFloat(elPrice.val());
                        elPrice.val(parseFloat(price) + parseFloat(elPrice.val()));
                        elPriceOption.val(parseFloat(price) + parseFloat(elPriceOption.val()));
                    }
                    elOptionInfo.append('<li><span class="label '+idSelect+'">'+label+'</span></li><li>'+option+'</li>');
                    break;
            }
        }
    });
    return $.mageants.option;
});
