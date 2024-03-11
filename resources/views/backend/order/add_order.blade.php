@extends('backend.index')

@section('title', 'Basic Stock Manager' . ' | ' . 'Admin Dashboard')

@section('breadcrumb-links')
@endsection

@section('content')
    @include('includes.messages')
    <div id="page-wrapper">
        <div class="main-page">
            <div class="tables">
                <h2 class="title1 col-md-4" style="width: 100%; margin-top: .8em"><a href="{{ route('admin.categories.index', $stock->id) }}">{{ $stock->name }}</a> / Add Product</h2>
                <div class="form-grids row widget-shadow" data-example-id="basic-forms">
                    <div class="form-body">
                        <form enctype="multipart/form-data" class="add-edit-product-form" method="post" action="{{ route('admin.orders.store', ['stock' => $stock->id]) }}">
                            @csrf
                            <div class="customer-info-div">
                                <h4 class="header-wrapper header-customer-info">Customer Info</h4>
                                <div class="customer-info-wrapper body-customer-info">
                                    <div class="form-group row pd-0-10 body-customer-info">
                                        <div class="form-group col-md-6">
                                            <label for="customer_name">Name</label> <button type="button" style="position: absolute; top: -10px; margin-left: 20px" class="btn btn-sm btn-primary toggle-user-plus-info"><i class="fa fa-user-plus"></i></button>
                                            <input required type="text" name="customer_name" class="form-control" id="customer_name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="customer_phone">Phone</label>
                                            <input type="text" required class="form-control customer_phone" id="customer_phone" name="customer_phone">
                                        </div>
                                    </div>
                                    <div class="form-group user-plus-info row pd-0-10" style="display: none">
                                        <div class="form-group col-md-6">
                                            <label for="customer_name">Address</label>
                                            <input type="text" name="customer_address" class="form-control" id="customer_address">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="customer_name">URL</label>
                                            <input type="text" name="customer_url" class="form-control" id="customer_url">
                                        </div>
                                    </div>
                                    <div class="form-group user-plus-info row pd-0-10" style="display: none">
                                        <div class="form-group col-md-6">
                                            <label for="prodDescription">More Info</label>
                                            <input type="text" class="form-control" id="customer_more_info" name="customer_more_info">
                                        </div>
                                        <div class="col-md-6">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-info-div">
                                <h4 class="header-wrapper header-order-info">Order Info</h4>
                                <div class="order-info-wrapper body-order-info">
                                    <div class="form-group row pd-0-10">
                                        <div class="form-group col-md-4">
                                            <label for="customer_name">Date</label>
                                            <input required type="text" name="order_date" class="form-control order_date" id="order_date">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="customer_status_id">Status</label>
                                            <select name="status_id" id="status_id" required class="form-control">
                                                @foreach(\App\Models\Order::ORDER_STATUS as $sKey => $status)
                                                    <option value="{{ $sKey }}">{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="customer_priority">Priority</label>
                                            <select name="priority" id="priority" required class="form-control">
                                                @foreach(\App\Models\Order::ORDER_PRIORITY as $pKey => $priority)
                                                    <option @if(\App\Models\Order::PRIORITY_NORMAL == $pKey) selected @endif value="{{ $pKey }}">{{ $priority }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="order-info-wrapper body-order-info">
                                    <div class="form-group row pd-0-10">
                                        <div class="form-group col-md-4">
                                            <label for="customer_name">Shop Name</label>
                                            <select name="shop_id" id="shop_id" required class="form-control">
                                                @foreach($shops as $shop)
                                                    <option @if('MDS' == $shop->prefix) selected @endif value="{{ $shop->id }}">{{ $shop->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="customer_phone">Total Order</label>
                                            <div class="row">
                                                <div class="col-md-10" style="padding: 0">
                                                    <input type="number" readonly  value="0" class="form-control amount-total" id="total" name="total">
                                                    <small style="color: red">* Total Without Ship Fee</small>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-primary btn-edit-amount-total"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="customer_phone">Ship By Customer</label>
                                            <input type="number"  value="0" class="form-control" id="ship_by_customer" name="ship_by_customer">
                                        </div>
                                    </div>
                                </div>
                                <div class="order-info-wrapper body-order-info">
                                    <div class="form-group row pd-0-10">
                                        <div class="form-group col-md-4">
                                            <label for="ship_by_shop">Ship By Shop</label>
                                            <input type="number"  value="0" class="form-control" id="ship_by_shop" name="ship_by_shop">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="cost">Cost</label>
                                            <div class="row">
                                                <div class="col-md-10" style="padding: 0">
                                                    <input type="number" readonly  value="0" class="form-control amount-cost" id="cost" name="cost">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-primary btn-edit-amount-cost"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="order-info-wrapper body-order-info">
                                    <div class="form-group row pd-0-10">
                                        <div class="form-group col-md-6">
                                            <label for="evidence">Evidence</label>
                                            <input class="custom-file-input" type="file" name="evidence[]" multiple="" id="evidence">
                                            <div style="margin-top: 10px; display: flex; justify-content: center; border-style: dotted; border-color: #4F52BA; max-width: 300px; max-height: 300px">
                                                <img class="image-trigger-upload-evidence" style="max-width: 250px; max-height: 250px" src="{{ asset('public/template/images/file-upload.png') }}" alt="">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="notes">Notes</label>
                                            <textarea style="border-radius: 4px" class="form-control" id="notes" cols="10" rows="5" name="notes"></textarea>
                                        </div>
                                    </div>
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
                                                <th>Sub Products</th>
                                                <th>Action</th>
                                            </tr> </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="order_products" name="order_products">
                            <button style="margin: 20px" type="submit" class="btn btn-success">Create</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{ json_encode($productById) }}" id="product_by_id_string">
    <style>
        .add-edit-product-form input, .add-edit-product-form select {
            border-radius: 4px;
        }
        .justify-content-center {
            display: flex;
            justify-content: center;
        }
        .first-supplier-row .btn-delete-supplier-row {
            opacity: 0.5;
            pointer-events: none;
        }
        .pd-0-10 {
            padding: 0 10px;
        }
        .header-wrapper {
            text-transform: uppercase;
            font-style: italic;
            color: green !important;
            cursor: pointer;
        }
        .custom-file-input {
            /*display: none;
            opacity: 0;
            height: 0;
            width: 0;*/
        }
        /*.custom-file-input {
            position: relative;
            z-index: 2;
            width: 100%;
            height: calc(1.5em + 0.75rem + 2px);
            margin: 0;
            opacity: 0;
        }*/

        .td-cost-plus, .td-price-plus {
            min-width: 100px;
        }
        td {
            vertical-align: center !important;
        }
        @media only screen and (max-width: 991px) {
            .search-products {
                padding: 0 !important;
            }
            .btn-add-product-detail-row {
                margin-top: 20px;
                margin-right: 15px;
                float: right;
            }
            .td-cost-plus, .td-price-plus {
                padding: 13px 3px !important;
            }
        }
    </style>
    <script src="{{ asset('public/js/orders.js')  . '?v=' . config('app.commit_version') }}"></script>
    <script src="{{ asset('public/js/main.js')  . '?v=' . config('app.commit_version') }}"></script>
    <script>
        var productImagePublicFolder = '{{ asset('public/Pro_Images/') }}';
    </script>
@endsection
