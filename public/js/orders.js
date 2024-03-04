jQuery.noConflict(true);

$(document).ready(function() {
    $('.toggle-user-plus-info').click(function () {
        $('.user-plus-info').toggle();
    });

    $('.header-customer-info').click(function () {
        $('.body-customer-info').toggle();
    });

    $('.header-order-info').click(function () {
        $('.body-order-info').toggle();
    });

    $('.header-order-detail').click(function () {
        $('.body-order-detail').toggle();
    });

    $('#order_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minYear: 1920,
        maxYear: parseInt(moment().format('YYYY'),10),
        locale: {
            format: 'DD/MM/YYYY'
        },
        autoApply: true,
    })
        .attr('readonly', 'readonly');
    $('input[name="order_date"]').on('apply.daterangepicker', function(ev, picker) {
        const datePicker = picker.endDate.format('DD/MM/YYYY');
        $(this).val(datePicker);
    });

    $('.image-trigger-upload-evidence').on('click', function (e) {
        $('.custom-file-input').click();
    });

    $('.select-product-list').selectize({
        //sortField: 'text'
    });
});
