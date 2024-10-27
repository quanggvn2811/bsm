<!-- Modal -->
<div class="modal fade" id="create-or-update-delivery-code" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog custom-modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delivery Code Management</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-delivery-code-management">
                        <thead>
                        <tr>
                            <th >Order Number</th>
                            <th>Shipping Unit</th>
                            <th class="hide_with_mobile .min-with-150">Notes</th>
                            <th class="min-with-150">Customer</th>
                            <th class="">Phone</th>
                            <th class="hide_with_mobile {{-- min-with-150 --}}">Address</th>
                            <th class="shipping_cod">COD</th>
                            <th class="">Delivery Code</th>
                            <th class="">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $sumTotal = 0;
                            $sumProfit = 0;
                        ?>
                        @foreach($orders as $order)
                        <tr data-order_id="{{ $order->id }}" data-order="{{ json_encode($order) }}" class="active order-lines">
                            <?php
                                $redirectUrl = route('admin.orders.show', ['stock' => $stock->id, 'order' => $order->id]);
                                session()->put('url_back_to_order_list', url()->full());
                            ?>
                            <td class="order_number" style="font-weight: bold;"><a class="order-number-{{$order->id}} @if('SYSTEM' === $order->last_updated_by) last_updated_by_system @endif" href="{{ $redirectUrl }}">{{ $order->order_number }}</a><i style="color: #337ab7; margin-left: 5px; display: inline" class="fa fa-clone copy-order-number-icon @if('SYSTEM' === $order->last_updated_by) last_updated_by_system @endif" data-trigger_to="order-number-{{$order->id}}"></i></td>
                            <td class="order_shipping_unit">
                                <select name="shipping_unit" id="shipping_unit" class="form-control shipping_unit btn {{ strtolower(\App\Models\ShippingUnit::whereId($order->shipping_unit)->first()->acronym) }}">
                                    @foreach($shippingUnits as $unit)
                                        <option @if($order->shipping_unit == $unit->id) selected @endif value="{{ $unit->id }}">{{ $unit->acronym }}</option>
                                    @endforeach
                                </select>
                                <i class="fa fa-check-circle alert-updated-shipping-unit-{{ $order->id }}" style="font-size: 20px; color: #00ad45; display: none" aria-hidden="true"></i>
                            </td>
                            <td class="order_notes hide_with_mobile min-with-150">{!! $order->notes !!}</td>
                            <td class="customer min-with-150">{{ $order->customer->name }}</td>
                            <td class="phone">{{ $order->customer->phone }}</td>
                            <td class="address hide_with_mobile min-with-150">{{ $order->order_address }}</td>
                            <td class="total">{{ number_format($order->total + $order->ship_by_customer) }}</td>
                            <td class="delivery-code"><button class="btn-primary btn btn-sm btn-delivery-code">{{ $order->customer->phone }}</button></td>
                            <td class="action">
                            	<button class="btn btn-sm btn-success btn-add-delivery-code" data-toggle="modal" data-target="#single-create-or-update-delivery-code"><i class="fa fa-plus"></i></button>
                            	<button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            	<button class="btn btn-sm btn-danger btn-remove-action-delivery-code"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="disabled btn btn-primary btn-do-update-order-from-pancake">Bulk Update</button>
            </div>
        </div>
    </div>
</div>
<style>
	@media only screen and (max-width: 1200px) {
		.custom-modal-xl {
			width: 95%;
		}
	}
	@media only screen and (min-width: 1200px) {
		.custom-modal-xl {
			width: 80%;
		}
	}
	.table-delivery-code-management {
		font-size: 13px;
	}
	.btn-delivery-code {
		background-color: #ff5722;
		border-color: #ff5722;
		font-size: 12px;
	}
</style>