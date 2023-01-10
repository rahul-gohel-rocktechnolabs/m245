require(["jquery"], function($) {
    jQuery(document).ready(function(){
        $('body').on('change', '.col-in_product input[type="checkbox"], .col-customer_price  .custom_price, .col-customer_special_price  .custom_specialprice', function(event) {
            var pageid = $('#customerprice_grid_product_price_page-current').val();
            var pricedata = $('#myform_select_product_price').val();
            var prochanges = $('#myform_product_changes').val();
            var result_data = {};
            if(pricedata != ''){
                var final_data =  JSON.parse(pricedata);
            }else{
                var final_data = {};
            }
            if(prochanges != ''){
                var final_pro_changes =  JSON.parse(prochanges);
            }else{
                var final_pro_changes = {};
            }
            if(event.target.type == 'checkbox'){
                final_pro_changes[$(this).val()] = $(this).val();
            }
            $('.col-in_product input[type="checkbox"]:checked').each(function(i, ele) {
                var custom_price = 'custom_price-'+$(ele).val();
                var custom_special_price = 'custom_specialprice-'+$(ele).val();
                result_data[$(ele).val()] = {'price' : $("input[name="+custom_price+"]").val(),
                                            'special_price' : $("input[name="+custom_special_price+"]").val()};
            })
            final_data[pageid] = result_data;
            $('#myform_select_product_price').val(JSON.stringify(final_data));
            $('#myform_product_changes').val(JSON.stringify(final_pro_changes));
        });
        var triggerPrice = false;
        triggerPrice = setInterval(function(){
            if($( '.col-in_product input[type="checkbox"]' ).length > 0) {
                clearInterval(triggerPrice);
                $( '.col-in_product input[type="checkbox"]' ).trigger( "change" );
            }
        }, 2000);
        
    });
});