<!-- Modal -->
<div class="modal fade" id="quick-update-order-modal-dialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form enctype="multipart/form-data" class="quick-update-order-from" method="post" action="">
            @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Quick update order: <i class="order-number" style="color: red; font-weight: bold; font-size: 16px"></i></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group" style="display: inline-block; margin-top: 15px; width: 100%">
                    <div class="col-md-4">
                        <label for="name">Name</label>
                        <input readonly type="text" value="" name="customer_name" class="form-control quick-update-order-name col-md-6" id="quick-update-order-name">
                    </div>
                    <div class="col-md-4">
                        <label for="phone">Phone</label>
                        <input readonly type="number" value="" name="customer_phone" class="form-control quick-update-order-phone col-md-6" id="quick-update-order-phone">
                    </div>
                    <div class="col-md-4">
                        <label for="order_date">Date</label>
                        <input readonly type="text" value="" name="order_date" class="form-control quick-update-order-order_date col-md-6" id="quick-update-order-order_date">
                    </div>
                </div>
                <div class="form-group" style="display: inline-block; margin-top: 15px; width: 100%">
                    <div class="col-md-4 order_status">
                        <label for="status_id">Status</label>
                        <select name="status_id" id="status_id" class="form-control btn">
                            @foreach(\App\Models\Order::ORDER_STATUS as $statusKey => $status)
                                <option value="{{ $statusKey }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 order_priority">
                        <label for="priority">Priority</label>
                        <select name="priority" id="priority" class="form-control btn normal">
                            @foreach(\App\Models\Order::ORDER_PRIORITY as $pKey => $priority)
                                <option value="{{ $pKey }}">{{ $priority }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="total">Total <small style="color: red">* Without Ship Fee</small></label>
                        <input type="number" value="" name="total" class="form-control amount-total quick-update-order-total total col-md-6" id="quick-update-order-total">
                    </div>
                </div>
                <div class="form-group" style="display: inline-block; margin-top: 15px; width: 100%">
                    <div class="col-md-4 ship_by_customer">
                        <label for="status_id">Ship by customer</label>
                        <input class="form-control col-md-6 quick-update-order-ship_by_customer" type="number" name="ship_by_customer" value="0">
                    </div>
                    <div class="col-md-4 ship_by_shop">
                        <label for="ship_by_shop">Ship by shop</label>
                        <input class="form-control col-md-6 quick-update-order-ship_by_customer" type="number" name="ship_by_shop" value="0">
                    </div>
                    <div class="col-md-4 cost">
                        <label for="cost">Cost</label>
                        <input class="form-control col-md-6 amount-cost quick-update-order-cost" type="number" name="cost" value="0">
                    </div>
                </div>
                <div class="form-group" style="display: inline-block; margin-top: 15px; width: 100%">
                    <div class="col-md-6 customer_address">
                        <label for="status_id">Address</label>
                        <input class="form-control col-md-6 quick-update-order-customer_address" type="text" name="customer_address" value="">
                    </div>
                    <div class="col-md-6 notes">
                        <label for="notes">Notes</label>
                        <input class="form-control col-md-6 quick-update-order-notes" type="text" name="notes" value="">
                    </div>
                </div>
                <div class="order-detail-div">
                    <h4 class="header-wrapper header-order-detail" style="padding-left: 0">Order Detail</h4>
                    <div class="order-detail-wrapper body-order-detail">
                        <div class="row" style="padding: 10px 30px">
                            <div class="search-products col-md-9">
                                <select {{--name="product-list"--}} class="select-product-list" id="select-product-item" placeholder="Search product sku or name">
                                    <option value="">Select product</option>
                                    @foreach($products as $product)
                                            <?php $text = $product->sku ? '[' . $product->sku . '] ' . $product->name : $product->name ?>
                                        <option value="{{ $product->id }}">{{ $text }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-success btn-add-product-detail-row col-md-3"><i style="margin-right: 10px" class="fa fa-plus"></i>Add Product Item</button>
                        </div>
                        <div class="form-group row pd-0-10" style="overflow-x:auto;">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>SKU</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Cost Item</th>
                                    <th>Price Item</th>
                                    <th>Image</th>
                                    <th><span class="span-tooltip" data-toggle="tooltip" data-original-title="Before add order">Before In Stock</span></th>
                                    <th>Action</th>
                                </tr> </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="_hidden_shop_id" name="shop_id" value="">
            <input class="" type="hidden" name="evidence[]" id="_hidden_evidence">
            <input type="hidden" class="order_products" name="order_products">
            <input type="hidden" id="_hidden_customer_url" name="customer_url" value="">
            <input type="hidden" id="_hidden_customer_more_info" name="customer_more_info" value="">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-do-quick-update-order off-mode">Update</button>
            </div>
        </div>
        </form>
    </div>
</div>
<style>
    .off-mode {
        opacity: .5;
        pointer-events: none;
    }
    input {
        border-radius: 4px !important;
    }
    #quick-update-order-modal-dialog .order_priority select {
        border-radius: 4px;
        padding: 5px 15px;
        /*width: 110px;*/
    }
    #quick-update-order-modal-dialog .order_priority .normal {
        color: #fff;
        background-color: #5cb85c !important;
        border-color: #4cae4c !important;
    }
    #quick-update-order-modal-dialog .order_priority .high {
        color: #fff;
        background-color: #c9302c !important;
        border-color: #ac2925 !important;
    }
    #quick-update-order-modal-dialog .order_priority .low {
        background-color: #999;
        border-color: #999;
        color: #fff;
    }

    /*#quick-update-order-modal-dialog .order_status select {
        border-radius: 4px;
        padding: 5px 15px;
        width: 130px;
        border-color: #999;
        color: #fff;
    }*/
    #quick-update-order-modal-dialog .order_status .waiting {
        background-color: rgb(230, 230, 230);
        color: rgb(61, 61, 61);
    }
    #quick-update-order-modal-dialog .order_status .pending {
        background-color: rgb(255, 207, 201);
        color: rgb(177, 2, 2);
    }
    #quick-update-order-modal-dialog .order_status .today_handle {
        background-color: #5cb85c;
        border-color: #4cae4c;
    }
    #quick-update-order-modal-dialog .order_status .processing {
        background-color: rgb(255, 229, 160);
        color: rgb(71, 56, 33);
    }
    #quick-update-order-modal-dialog .order_status .take_care {
        background-color: #c9302c;
        border-color: #ac2925;
    }
    #quick-update-order-modal-dialog .order_status .shipped {
        background-color: rgb(191, 225, 246);
        color: rgb(10, 83, 168);
    }
    #quick-update-order-modal-dialog .order_status .failed {
        background-color: rgb(90, 50, 134);
        color: rgb(229, 207, 242);
    }
    #quick-update-order-modal-dialog .order_status .completed {
        background-color: rgb(212, 237, 188);
        color: rgb(17, 115, 75);
    }
</style>
