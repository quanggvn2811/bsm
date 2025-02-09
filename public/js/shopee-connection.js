$(document).ready(function() {
    var selectizeProduct = $('#select-product-item').selectize({
        //sortField: 'text'
    });
    $('.btn-update-product-from-pancake').on('click', function () {
        let shop = $('.select_shop_id option:selected');
        if (confirm('Update for: ' + shop.text() + ' ?')) {
            let paramUrl = window.location.href;
            if (paramUrl.indexOf('?') > -1) {
                var href = new URL(paramUrl);
                href.searchParams.set('shop_id', shop.val());
                paramUrl = href.toString();
            } else {
                paramUrl += "?shop_id=" + shop.val();
                paramUrl += "&product_name=";
            }

            const stock = $('input[name="stock_id"]').val();
            let url = '/admin/shopee_connection/' + stock + '/update-product-from-pancake';

            $.ajax({
                type:'POST',
                url: url,
                dataType: 'json',
                data: {
                    _token: $('input[name="_token"]').val(),
                    shop_id: shop.val(),
                },
                success: function(data) {
                    // window.location.reload();
                    window.location = paramUrl;
                },
                error: function() {
                }
            });
        }
    })

    const connectModal = $("#link-with-bsm-product-modal-dialog");
    $(".btn-show-link-modal").click(function(){
        connectModal.modal();
        let variation = $(this).closest('tr');
        let variationId = $(variation).data('variation_id');
        let productId = $(variation).data('product_id');
        let productQuantity = $(variation).data('product_quantity');

        connectModal.find('.product_quantity').val(productQuantity);
        connectModal.find('._variation_product_id').val(variationId);
        connectModal.find('.variation-name').text($(variation).find('td.name').text());
        connectModal.find('.variation-fields').text($(variation).find('td.fields').text());

        if (!productId || parseInt(productQuantity) < 1) {
            $('.btn-save-modal-connect').attr("disabled", true);
        }

        if (productId) {
            selectizeProduct[0].selectize.setValue([productId]);
        }
    });

    connectModal.on('hidden.bs.modal', function () {
        $('#select-product-item option:selected').removeAttr('selected');
        selectizeProduct[0].selectize.clear();
    });

    $('#select-product-item').on('change', function () {
        if ($(this).val()) {
            $('.btn-save-modal-connect').removeAttr("disabled");
        }
    })

    $('.btn-save-modal-connect').on('click', function () {
        let productId = $('#select-product-item').val();
        let productQuantity = connectModal.find('.product_quantity').val();
        let variationProductId = connectModal.find('._variation_product_id').val();

        if (productId && productQuantity && variationProductId) {
            updateProductConnection(variationProductId, productId, productQuantity);
        }
    });

    function updateProductConnection(variationId, productId, productQuantity) {
        const stock = $('input[name="stock_id"]').val();
        let url = '/admin/shopee_connection/' + stock + '/update-bsm-connection';

        $.ajax({
            type:'POST',
            url: url,
            dataType: 'json',
            data: {
                _token: $('input[name="_token"]').val(),
                variation_id: variationId,
                product_id: productId,
                product_quantity: productQuantity
            },
            success: function(data) {
                // Todo set url by product id
                let html = data?.product_name.length ? `<a target="_blank" href="/admin/products/stock/${stock}/edit/${productId}">` + data?.product_name + '</a>' : '';
                $('.variation-' + variationId).find('.bsm-product-name').empty().append(html);
                $('.variation-' + variationId).find('.bsm-product-quantity').text(data?.product_quantity);
                $('.variation-' + variationId).data('product_id', productId)
                $('.variation-' + variationId).data('product_quantity', productQuantity)
                connectModal.modal('hide');
            },
            error: function() {
            }
        });
    }

    function deleteProductVariation(variationId) {
        const stock = $('input[name="stock_id"]').val();
        let url = '/admin/shopee_connection/' + stock + '/delete-variation';

        $.ajax({
            type:'POST',
            url: url,
            dataType: 'json',
            data: {
                _token: $('input[name="_token"]').val(),
                variation_id: variationId
            },
            success: function(data) {
                $('.variation-' + variationId).remove();
            },
            error: function() {
            }
        });
    }

    $('.btn-delete-variation').on('click', function () {
        if (confirm('Delete this variation?')) {
            const variationField = $(this).closest('tr');
            let variationId = $(variationField).data('variation_id');
            deleteProductVariation(variationId);
        }
    })

    $('.btn-unlink-bsm-product').on('click', function () {
        if (confirm('Confirm unlink this product, are you sure?')) {
            updateProductConnection($(this).closest('tr').data('variation_id'), '', 1)
        }
    })
});



