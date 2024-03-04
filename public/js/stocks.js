$(document).ready(function() {
    const Stock = {
        selector: '#update-stock-dialog',
        name: '',
        description: '',
        status: 0,
        unique_prefix: null,
        is_create: true,
        show: function () {
            $(this.selector).modal('show');
        },
        update: function () {
            $.ajax({
                type:'POST',
                url:'/admin/stocks/add',
                dataType: 'json',
                data: {
                    _token: $('input[name="_token"]').val(),
                    name: this.name,
                    description: this.description,
                    unique_prefix: this.unique_prefix,
                    status: this.status,
                },
                success: function(data) {
                    window.location.reload();
                },
                error: function() {
                }
            });
        }
    }

    $('.btn-add-stock').on('click', function () {
        Stock.show();
    });

    $('.btn-save-modal-stock').on('click', function () {
        Stock.name = $('#update-stock-dialog input[name="name"]').val();
        Stock.description = $('#update-stock-dialog input[name="description"]').val();
        Stock.unique_prefix = $('#update-stock-dialog input[name="unique_prefix"]').val();
        // Stock.status = $('#update-stock-dialog input[name="status"]').hasClass('checked');
        Stock.status = 1;
        Stock.update();
    });
});



