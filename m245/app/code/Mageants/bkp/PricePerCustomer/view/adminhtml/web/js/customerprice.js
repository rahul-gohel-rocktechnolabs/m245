require([
    "jquery",
    'mage/url',
    "Magento_Ui/js/modal/modal"
], function ($, url, modal) {

    function editProductCustomerPrice(id, customer_id, email, price, special_price) {
        event.preventDefault();
        document.getElementById("customerprice_base_fieldset").scrollIntoView();
        $('#customerprice_id')[0].value = id;
        $('#customerprice_customer_id')[0].value = customer_id;
        $('#customerprice_customer_email')[0].value = email;
        if(price != undefined){
            $('#customerprice_customer_price')[0].value = price;
        }else{
            $('#customerprice_customer_price')[0].value = '';
        }
        if(special_price != undefined){
            $('#customerprice_special_price')[0].value = special_price;
        }else{
            $('#customerprice_special_price')[0].value = '';
        }

        return false;
    }

    function deleteProductCustomerPrice(id, url) {
        var answer = confirm ("Are you sure you want to delete this customer data?");
        if (answer){
            $.ajax({
                url: url,
                data: {id: id},
                type: 'post',
                dataType: 'json',
                showLoader: true,
            }).done(function(data) {
                if(data[0] == 1){
                    setTimeout(function() {
                        customerPriceGridJsObject.resetFilter();
                    }, 10);    
                }
            });
        }
    }

    function addCustomerPrice(options) {

        var data = {id: $('#customerprice_id').val(),
          customer_id: $('#customerprice_customer_id').val(),
                email: $('#customerprice_customer_email').val(),
                price: $('#customerprice_customer_price').val(),
                special_price: $('#customerprice_special_price').val(),
                product_id: options.product};
        $.ajax({
            url: options.url,
            data: data,
            type: 'post',
            showLoader: true,
        }).done(function (data) {
            if (data[0] == 0) {
                $('#customerprice-error-popup').html(data[1]);
                $('#customerprice-error-popup').modal({
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: 'Error',
                    buttons: [{
                        text: 'Close',
                        'class': 'action-primary',
                        click: function () {
                            this.closeModal();
                        }
                    }]
                }).modal('openModal');
            } else {
                $('#customerprice_id')[0].value = "";
                $('#customerprice_customer_email')[0].value = "";
                $('#customerprice_customer_price')[0].value = "";
                $('#customerprice_special_price')[0].value = "";
                setTimeout(function () {
                    customerPriceGridJsObject.resetFilter();
                }, 10);
            }
        });
    }

    function selectAddCustomer(id, label){
        $('#customerprice_customer_email')[0].value = label;
        $('#customerprice_customer_id')[0].value = id;
        customerSearchModal.modal('closeModal');
        return false;
    }

    var element = $('.add_customer_price');
    element.on("click", function (e) {

        var url = $('#baseurl').text()+'pricepercustomer/product/addcustomerprice';
        var product_id = $('#customer_product_id').text();
          
        addCustomerPrice({ "url":url, "product":product_id });
    });

    window.editProductCustomerPrice = editProductCustomerPrice;
    window.deleteProductCustomerPrice = deleteProductCustomerPrice;
    window.selectAddCustomer = selectAddCustomer;
});