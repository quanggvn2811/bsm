$(document).ready(function() {
    $('.btn-update-product-from-pancake').on('click', function () {
        let shop = $('.select_status_id option:selected');
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
});



