define([
    "jquery",
    "jquery/ui",
], function ($) {
    "use strict";
    
    $.widget('mageants.fastorder_downloadable', {
        options: {
            priceHolderSelector: '#mageants-content-option-product .price-box',
            sortOrder: '',
            defaultPrice: ''
        },

        _create: function () {
            var self = this;
            this.element.find(this.options.mageantslinkElement).on('change', $.proxy(function () {
                this._reloadPrice();
                $('#mageants-links-advice-container').hide();
            }, this));

            this.element.find(this.options.mageantsallElements).on('change', function () {
                if (this.checked) {
                    $('#mageants-links-advice-container').hide();
                    $('label[for="' + this.id + '"] > span').text($(this).attr('data-checked'));
                    self.element.find(self.options.mageantslinkElement + ':not(:checked)').each(function () {
                        $(this).trigger('click');
                    });
                } else {
                    $('[for="' + this.id + '"] > span').text($(this).attr('data-notchecked'));
                    self.element.find(self.options.mageantslinkElement + ':checked').each(function () {
                        $(this).trigger('click');
                    });
                }
            });
        },

        /**
         * Reload product price with selected link price included
         * @private
         */
        _reloadPrice: function () {
            var finalPrice = 0;
            var basePrice = 0;
            var refreshPrice = 0;
            $('#mageants-fastorder-form-option .mageants-attribute-select').val('');
            this.element.find(this.options.mageantslinkElement + ':checked').each($.proxy(function (index, element) {
                finalPrice += this.options.mageantsconfig.links[$(element).val()].finalPrice;
                basePrice += this.options.mageantsconfig.links[$(element).val()].basePrice;
                $(element).next().val($(element).val());
            }, this));
            refreshPrice = parseFloat(this.options.defaultPrice) + parseFloat(finalPrice);
            $('#mageants-fastorder-'+this.options.sortOrder+' .mageants-product-price-number').val(refreshPrice);
            $('#mageants-fastorder-'+this.options.sortOrder+' .mageants-product-price-number-download').val(refreshPrice);
            $(this.options.priceHolderSelector).trigger('updatePrice', {
                'prices': {
                    'finalPrice': { 'amount': finalPrice },
                    'basePrice': { 'amount': basePrice }
                }
            });
        }
    });
    
    return $.mageants.fastorder_downloadable;
});