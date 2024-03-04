$(document).ready(function() {
    const Category = {
        selector: '#update-category-dialog',
        name: '',
        description: '',
        sku: '',
        status: 0,
        is_create: true,
        id: null,
        show: function () {
            $(this.selector).modal('show');
        },
        update: function () {
            const stock = $('input[name="stock_id"]').val();
            let url = '/admin/categories/stock/' + stock + '/add';
            if (null !== this.id) {
                url = '/admin/categories/stock/' + stock + '/edit/' + this.id;
            }

            $.ajax({
                type:'POST',
                url: url,
                dataType: 'json',
                data: {
                    _token: $('input[name="_token"]').val(),
                    name: this.name,
                    description: this.description,
                    sku: this.sku,
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

    $(Category.selector).on('hide.bs.modal', function(){
        Category.name = '';
        Category.description = '';
        Category.sku = '';
        Category.status = 0;
        Category.is_create = true;
        Category.id = null;
        $('#update-category-dialog input[name="name"]').val('');
        $('#update-category-dialog input[name="description"]').val('');
        $('#update-category-dialog input[name="sku"]').val('');
        $('#update-category-dialog input[name="status"]').val(1);
    });

    $('.btn-add-category').on('click', function () {
        Category.show();
    });

    $('.btn-edit-category').on('click', function () {
        const closet = $(this).closest('tr');
        Category.id = closet.data('category_id');
        $('#update-category-dialog input[name="name"]').val(closet.find('td.name').text());
        $('#update-category-dialog input[name="description"]').val(closet.find('td.description').text());
        $('#update-category-dialog input[name="sku"]').val(closet.find('td.sku').text());
        Category.show();
    });

    $('.btn-save-modal-category').on('click', function () {
        Category.name = $('#update-category-dialog input[name="name"]').val();
        Category.description = $('#update-category-dialog input[name="description"]').val();
        Category.sku = $('#update-category-dialog input[name="sku"]').val();
        // Stock.status = $('#update-stock-dialog input[name="status"]').hasClass('checked');
        Category.status = 1;
        Category.update();
    });
});



