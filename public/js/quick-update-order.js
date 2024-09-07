$(document).ready(function() {
    $('.quick-update-order').on('click', function (e) {
        const $this = $(e.target);
        $this.css('opacity', 0.5);

        // Order info
        const order = $this.closest('tr').data('order');
        console.log(order)
        const updateOrderModal = $('#quick-update-order-modal-dialog');
        updateOrderModal.find('.order-number').text(order.order_number)
        updateOrderModal.find('#quick-update-order-name').val(order.customer.name)
        updateOrderModal.find('#quick-update-order-phone').val(order.customer.phone)
        updateOrderModal.find('#quick-update-order-order_date').val(order.order_date)
        updateOrderModal.find('#quick-update-order-total').val(order.total)
        updateOrderModal.find('.quick-update-order-ship_by_customer').val(order.ship_by_customer)
        updateOrderModal.find('.quick-update-order-ship_by_shop').val(order.ship_by_shop)
        updateOrderModal.find('.quick-update-order-cost').val(order.cost)
        updateOrderModal.find('.quick-update-order-customer_address').val(order.customer.address)
        updateOrderModal.find('.quick-update-order-notes').val(order.notes)
        updateOrderModal.find('#_hidden_shop_id').val(order.shop_id);
        updateOrderModal.find('#_hidden_customer_url').val(order.customer.info_url);
        updateOrderModal.find('#_hidden_customer_more_info').val(order.customer.more_info);

        updateOrderModal.find('#status_id').val(order.status_id);
        updateOrderModal.find('#priority').val(order.priority);
        let statusStr = JSON.parse($('#order_status_list').val())[order.status_id].toLowerCase().replace(' ', '_');
        updateOrderModal.find('#status_id').addClass(statusStr);
        let priorityStr = JSON.parse($('#order_priority_list').val())[order.priority].toLowerCase().replace(' ', '_');
        updateOrderModal.find('#priority').addClass(priorityStr);

        // Update order detail
        const orderDetail = order.order_detail;
        let orderDetailHtml = '';
        const productById = JSON.parse($('#product_by_id_string').val())
        $.each(orderDetail, function (index, detail) {
            // Calculator
            const prodImages = JSON.parse(productById[detail.product_id]['images']);
            let avatarSrc = '#';
            if (prodImages[0]) {
                let avatar = prodImages[0];
                avatarSrc = productImagePublicFolder + '/' + avatar;
            }

            let qtyBeforeOrder = productById[detail.product_id]['quantity'] + (detail.quantity ?? 1);
            let qtyClass = qtyBeforeOrder > 0 ? 'btn btn-success' : 'btn btn-danger';

            orderDetailHtml += '<tr data-product_id="' + detail.product_id + '" class="plus-product-item-row">';
            orderDetailHtml += `<td>${index}</td>`;
            orderDetailHtml += `<td>${detail.product.sku ?? '' }</td>`;
            orderDetailHtml += `<td>${detail.product.name ?? '' }</td>`;
            orderDetailHtml += `<td><input type="number" class="form-control quantity-plus" value="${detail.quantity ?? 1}"></td></td>`;
            orderDetailHtml += `<td class="td-cost-plus"><input type="number" class="form-control cost-plus" value="${detail.cost_item}"></td>`;
            orderDetailHtml += `<td class="td-price-plus"><input type="number" class="form-control price-plus" value="${detail.price_item}"></td>`;
            orderDetailHtml += `<td><img class="avatar-plus" style="max-width: 100px; max-height: 100px" src="${avatarSrc}" alt=""></td>`;
            orderDetailHtml += `<td><button type="button" class="${qtyClass}">${qtyBeforeOrder}</button></td>`;
            orderDetailHtml += `<td><button type="button" class="btn btn-danger btn-delete-plus-product-row"><i class="fa fa-trash"></i></button></td>`;
            orderDetailHtml += '</tr>'
        });

        updateOrderModal.find('.body-order-detail table tbody').empty().append(orderDetailHtml);

        const stockId = $('input[name="stock_id"]').val()
        let urlAction = '/admin/orders/stock/' + stockId + '/edit_order/' + order.id;
        $('.quick-update-order-from').attr('action', urlAction);
    });
});
