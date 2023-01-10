/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'underscore',
    'Magento_Catalog/js/product/view/product-ids-resolver',
    'Magento_Catalog/js/product/view/product-info-resolver',
    'jquery/ui'
], function ($, $t, _, idsResolver, productInfoResolver) {
    'use strict';

    var sample;
 
     $("button").on("click",function() {
        sample = $(this).attr('id');
     });

    $.widget('mage.catalogAddToCart', {
        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: '',
            addToCartButtonTextAdded: '',
            addToCartButtonTextDefault: '',
            sampleorderButtonSelector:'.action.tosample',
            sampleorderButtonDisabledClass: 'disabled',
            sampleorderButtonTextWhileAdding: '',
            sampleorderButtonTextAdded: '',
            sampleorderButtonTextDefault: '',
            productInfoResolver: productInfoResolver
        },

        /** @inheritdoc */
        _create: function () {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },

        /**
         * @private
         */
       _bindSubmit: function () {
            var self = this;
            if (this.element.data('catalog-addtocart-initialized')) {
                return;
            }

            this.element.data('catalog-addtocart-initialized', 1);
            this.element.on('submit', function (e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        /**
         * @return {Boolean}
         */
        isLoaderEnabled: function () {
            return this.options.processStart && this.options.processStop;
        },

        /**
         * Handler for the form 'submit' event
         *
         * @param {Object} form
         */
        submitForm: function (form) {
            console.log('kghjhg');
            var addToCartButton,sampleorderButton, self = this;

            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                // disable 'Add to Cart' button
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);
                addToCartButton.prop('disabled', true);
                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);

                sampleorderButton = $(form).find(this.options.sampleorderButtonSelector);
                sampleorderButton.prop('disabled', true);
                sampleorderButton.addClass(this.options.sampleorderButtonDisabledClass);
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },

        /**
         * @param {String} form
         */
        ajaxSubmit: function (form) {
            var self = this;

            $(self.options.minicartSelector).trigger('contentLoading');

            if(sample == "product-addtocart-button"){
                self.disableAddToCartButton(form);
            }
            else
            {
                 self.disablesampleorderButton(form);
            }
            
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',

                /** @inheritdoc */
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },

                /** @inheritdoc */
                success: function (res) {
                    var eventData, parameters;

                    $(document).trigger('ajax:addToCart', form.data().productSku, form, res);

                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        eventData = {
                            'form': form,
                            'redirectParameters': []
                        };
                        // trigger global event, so other modules will be able add parameters to redirect url
                        $('body').trigger('catalogCategoryAddToCartRedirect', eventData);

                        if (eventData.redirectParameters.length > 0) {
                            parameters = res.backUrl.split('#');
                            parameters.push(eventData.redirectParameters.join('&'));
                            res.backUrl = parameters.join('#');
                        }
                        window.location = res.backUrl;

                        return;
                    }

                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }

                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }

                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                     if(sample == "product-addtocart-button"){
                         self.enableAddToCartButton(form);
                    }
                    else
                    {
                        self.enablesampleorderButton(form);
                    }
                   
                }
            });
        },
        /**
         * @param {String} form
         */

        disablesampleorderButton: function (form) {  

            var sampleorderButtonTextWhileAdding = this.options.sampleorderButtonTextWhileAdding || $t('Adding...'),
                sampleorderButton = $(form).find(this.options.sampleorderButtonSelector);
                    
            sampleorderButton.addClass(this.options.sampleorderButtonDisabledClass);
            sampleorderButton.find('span').text(sampleorderButtonTextWhileAdding);
            sampleorderButton.attr('title', sampleorderButtonTextWhileAdding);
        },

        enablesampleorderButton: function (form) {
                    var sampleorderButtonTextAdded = this.options.sampleorderButtonTextAdded || $t('Added'),
                        self = this,
                        sampleorderButton = $(form).find(this.options.sampleorderButtonSelector);
                        sampleorderButton.find('span').text(sampleorderButtonTextAdded);
                        sampleorderButton.attr('title', sampleorderButtonTextAdded);

                    setTimeout(function () {
                        var sampleorderButtonTextDefault = self.options.sampleorderButtonTextDefault || $t('Order Sample');
                        sampleorderButton.removeClass(self.options.sampleorderButtonDisabledClass);
                        sampleorderButton.find('span').text(sampleorderButtonTextDefault);
                        sampleorderButton.attr('title', sampleorderButtonTextDefault);
                    }, 1000);
        },

        disableAddToCartButton: function (form) {
            var addToCartButtonTextWhileAdding = this.options.addToCartButtonTextWhileAdding || $t('Adding...'),
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
            addToCartButton.find('span').text(addToCartButtonTextWhileAdding);
            addToCartButton.attr('title', addToCartButtonTextWhileAdding);
        },

        /**
         * @param {String} form
         */
        enableAddToCartButton: function (form) {
            var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Added'),
                self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.find('span').text(addToCartButtonTextAdded);
            addToCartButton.attr('title', addToCartButtonTextAdded);

            setTimeout(function () {
                var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Add to Cart');

                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextDefault);
                addToCartButton.attr('title', addToCartButtonTextDefault);
            }, 1000);
        }      

    });

    return $.mage.catalogAddToCart;
});
