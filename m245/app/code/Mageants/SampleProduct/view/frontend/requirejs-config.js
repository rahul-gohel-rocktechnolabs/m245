/*var config = {
    config: {
        '*': {
        	catalogAddToCart:'Mageants_SampleProduct/js/catalog-add-to-cart'
         'Magento_Catalog/js/catalog-add-to-cart':'Mageants_SampleProduct/js/catalog-add-to-cart'
        }
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Mageants_SampleProduct/js/catalog-add-to-cart-mixin': true
            }
        }
    }
};*/

var config = {
    map: {
        "*": {
            'Magento_Catalog/js/catalog-add-to-cart':'Mageants_SampleProduct/js/catalog-add-to-cart'
        }
    }
};