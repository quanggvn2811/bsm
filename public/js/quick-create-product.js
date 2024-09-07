$(document).ready(function() {
    $('#quick-product-name').on('keyup', function () {
        let nameLength= $(this).val().length;
        if (nameLength > 0) {
            $('.quick-product-quantity').removeAttr('readonly');
            $('.quick-product-price').removeAttr('readonly');
            $('.quick-product-cost').removeAttr('readonly');
            $('.btn-do-quick-create-product').removeClass('off-mode');
        } else {
            $('.quick-product-quantity').attr('readonly', 'readonly');
            $('.quick-product-price').attr('readonly', 'readonly');
            $('.quick-product-cost').attr('readonly', 'readonly');
            $('.btn-do-quick-create-product').addClass('off-mode');
        }
    });

    $('.btn-do-quick-create-product').on('click', function (e) {
        e.preventDefault();
        const stock = $('input#_stock_id').val();

        $.ajax({
            type:'POST',
            url: '/admin/products/stock/' + stock + '/quick_add/',
            dataType: 'json',
            data: {
                _token: $('input[name="_token"]').val(),
                name: $('#quick-product-name').val(),
                quantity: $('.quick-product-quantity').val(),
                price: $('.quick-product-price').val(),
                cost: $('.quick-product-cost').val(),
            },
            success: function(data) {
                $('.reload-product-list').trigger('click');
                const modal = $('#quick-create-product-modal-dialog');
                modal.find('#quick-product-name').val('');
                modal.find('#quick-product-quantity').val(1);
                modal.find('#quick-product-price').val(0);
                modal.find('#quick-product-cost').val(0);
                // Todo;; fix bootstrap cannot hide/ show modal
                //modal.modal('hide');
                modal.hide();
                $('.modal-backdrop').hide();
            },
            error: function() {
            }
        });
    })
});
