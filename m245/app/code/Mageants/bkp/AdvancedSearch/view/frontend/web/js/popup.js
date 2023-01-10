define([
    'jquery',
    'uiComponent',
    'ko',
    'customerData'
], function ($, Component, ko,customerData) {
    'use strict';
    

      $(document).on('click','#mycart',function() {
                    var slidercount = $(this).attr('value');
                    var ajaxurl = window.value;
                           $.ajax({
                                type: "POST",
                                url: ajaxurl,
                                data: {id: slidercount},
                                success: function(data) {
                                   var sections = ['cart'];
                                   customerData.invalidate(sections);
                                   customerData.reload(sections, true); 
                                  setTimeout(function() {
                                  var Message = "You added Product to your shopping cart";
                                  customerData.set('messages', {
                                  messages: [{                      
                                 type: 'success',
                                 text: Message
                                         }]
                                   });
                                    }, 300);

                                     setTimeout(function() {
                                     $(".success").hide('blind', {}, 500)
                                     }, 5000);
                                    if(data.value != "simple" && data.value != null)
                                    {
                                     window.location.href = data.value;
                                    }

                                    
                                },
                                error: function(data) {
                               
                                }
                            });
                     });


    return Component.extend({
        defaults: {
            template: 'Mageants_AdvancedSearch/autocomplete',
            addToCartFormSelector: '[data-role=AdvancedSearch-tocart-form]',
            showPopup: ko.observable(false),
            result: {
                suggest: {
                    data: ko.observableArray([])
                },
                product: {
                    data: ko.observableArray([]),
                    size: ko.observable(0),
                    url: ko.observable('')
                }
            },
            anyResultCount: false
        },


        initialize: function () {
            var self = this;
            this._super();
            window.value = this.ajaxurl;
            this.anyResultCount = ko.computed(function () {
                var sum = self.result.suggest.data().length + self.result.product.data().length;
                if (sum > 0) {
                    return true; }
                return false;
            }, this);
        },
    });
});
