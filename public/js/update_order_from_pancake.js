$(document).ready(function() {
    // Update order from pancake
    const updatePancakeOrder = {
        selector: '#update-order-from-pancake-modal-dialog',
        show: function () {
            $(this.selector).modal('show');
        },
        update: function () {
            const stock = $('input[name="stock_id"]').val();
            $('#update-order-from-pancake-loader').show();
            $('#update-order-from-pancake-error').hide();
            $.ajax({
                type:'GET',
                url: '/admin/orders/stock/' + stock + '/pos_cake_update/',
                dataType: 'json',
                data: {
                    _token: $('input[name="_token"]').val(),
                    date_from: $('#pancake_date_from').val(),
                    date_to: $('#pancake_date_to').val(),
                    pancake_shop_id: $('#select_update_shop').val(),
                },
                success: function(data) {
                    window.location.reload();
                },
                error: function(e) {
                    $('#update-order-from-pancake-loader').hide();
                    $('#update-order-from-pancake-error').show();
                }
            });
        }
    }

    $(updatePancakeOrder.selector).on('hide.bs.modal', function(){
        updatePancakeOrder.name = '';
        updatePancakeOrder.description = '';
        updatePancakeOrder.sku = '';
        updatePancakeOrder.status = 0;
        updatePancakeOrder.is_create = true;
        updatePancakeOrder.id = null;
        $('#update-category-dialog input[name="name"]').val('');
        $('#update-category-dialog input[name="description"]').val('');
        $('#update-category-dialog input[name="sku"]').val('');
        $('#update-category-dialog input[name="status"]').val(1);
    });

    /*$('.btn-update-order-from-pancake').on('click', function (event) {
        event.preventDefault();
        updatePancakeOrder.show();
    });*/

    // Config date from/ date to
    $('input[name="pancake_date_from"]').on('apply.daterangepicker', function(ev, picker) {
        const datePicker = picker.endDate.format('DD/MM/YYYY');
        $(this).val(datePicker);
    });

    $('#pancake_date_from').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minYear: 2000,
        maxYear: parseInt(moment().format('YYYY'),10),
        locale: {
            format: 'DD/MM/YYYY'
        },
        autoApply: true,
    })
        .attr('readonly', 'readonly');

    $('input[name="pancake_date_to"]').on('apply.daterangepicker', function(ev, picker) {
        const datePicker = picker.endDate.format('DD/MM/YYYY');
        $(this).val(datePicker);
    });

    $('#pancake_date_to').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minYear: 2000,
        maxYear: parseInt(moment().format('YYYY'),10),
        locale: {
            format: 'DD/MM/YYYY'
        },
        autoApply: true,
    })
        .attr('readonly', 'readonly');

    $('.btn-do-update-order-from-pancake').on('click', function (e) {
        if (!confirm('Are you sure?')) {
            return;
        }

        updatePancakeOrder.update();

    });
});
