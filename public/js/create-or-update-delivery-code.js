$(document).ready(function() {
   $('.btn-remove-action-delivery-code').on('click', function(e) {
    if (!confirm('Are you sure?')) {
        return;
    }

    const $this = $(e.target);
    $this.closest('tr').remove();
   });

   $('.btn-add-delivery-code').on('click', function(e) {
    const $this = $(e.target);
    const orderData = $this.closest('tr').data('order');
    $('#single-create-or-update-delivery-code .order-number-title').text(orderData.order_number)

    // Set delivery info
    $('#single-create-or-update-delivery-code .shipping_to_name').val(orderData?.customer?.name)
    $('#single-create-or-update-delivery-code .shipping_to_phone').val(orderData?.customer?.phone)
    $('#single-create-or-update-delivery-code .shipping_to_address').val(orderData?.customer?.address)

    $('#single-create-or-update-delivery-code').modal('show');
   });
});
