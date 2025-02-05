<!-- Modal -->
@if(isset($order))
<div class="modal fade" id="single-create-or-update-delivery-code" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Delivery Code For: <i style="color: #337ab7; font-weight: bold;" class="order-number-title"></i></h5>
                <button style="margin-top: -20px" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Shipping Unit</label>
                    <select name="shipping_unit" id="shipping_unit" class="form-control shipping_unit btn {{ strtolower(\App\Models\ShippingUnit::whereId($order->shipping_unit)->first()->acronym) }}">
                                    @foreach($shippingUnits as $unit)
                                        <option @if($order->shipping_unit == $unit->id) selected @endif value="{{ $unit->id }}">{{ $unit->acronym }}</option>
                                    @endforeach
                                </select>
                                <i class="fa fa-check-circle alert-updated-shipping-unit-{{ $order->id }}" style="font-size: 20px; color: #00ad45; display: none" aria-hidden="true"></i>
                </div>
                <div class="form-group">
                    <label for="name">Delivery Shop:</label>
                    <input required type="text" name="pancake_date_from" class="form-control pancake_date_from col-md-6" id="pancake_date_from">
                </div>
                <div class="form-group">
                    <label for="shipping_to_name">To Name:</label>
                    <input required type="text" name="shipping_to_name" class="form-control shipping_to_name col-md-6" id="shipping_to_name">
                </div>
                <div class="form-group">
                    <label for="shipping_to_phone">To Phone:</label>
                    <input required type="text" name="shipping_to_phone" class="form-control shipping_to_phone col-md-6" id="shipping_to_phone">
                </div>
                <div class="form-group">
                    <label for="shipping_to_address">To Address:</label>
                    <input required type="text" name="shipping_to_address" class="form-control shipping_to_address col-md-6" id="shipping_to_address">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                {{-- <button type="button" class="btn btn-primary btn-do-update-order-from-pancake">Update</button> --}}
            </div>
        </div>
    </div>
</div>
@endif