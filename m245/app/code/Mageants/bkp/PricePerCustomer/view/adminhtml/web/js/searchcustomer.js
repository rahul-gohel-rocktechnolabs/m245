require(["jquery",'Magento_Ui/js/modal/modal'], function($, modal){
        var customerSearchModal = $('#customer-search-modal-content').modal({
            type: 'slide',
            responsive: true,
            innerScroll: true,
            title: 'Search Customer',
            buttons: [{
                text: 'Close',
                'class': 'action-secondary',
                click: function () {
                    this.closeModal();
                }
            }]
        });

        $('#action-customer-search').click(function(){
            customerSearchModal.modal('openModal');
        });
        window.customerSearchModal = customerSearchModal;
    });