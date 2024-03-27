@extends('backend.index')

@section('title', 'Basic Stock Manager' . ' | ' . 'Admin Dashboard')

@section('breadcrumb-links')
@endsection

@section('content')
    <?php $isEdit = isset($product); ?>
    <div id="page-wrapper">
        <div class="main-page">
            @include('includes.messages')
            <div class="tables">
                <div class="row">
                    {{--<h2 class="title1 col-md-4" style="/*width: 100%; margin-top: .8em*/"><a href="{{ route('admin.categories.index', $stock->id) }}">{{ $stock->name }}</a> / Add Bill</h2>--}}
                    <div class="col-md-4 pd-l-0">
                        <div class="short-url-menu">
                            <div class="first">
                                <a href="{{ route('admin.categories.index', $stock->id) }}">{{ $stock->name }}</a>
                            </div>
                            <div class="second">
                                <a href="{{ route('admin.import_bills.create', $stock->id) }}">Add A BILL</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.import_bills.index', $stock->id)  }}" class="btn btn-dark btn-add-product">Back<i class="fa fa-backward" aria-hidden="true"></i></a>
                    </div>
                </div>
                <div class="form-grids row widget-shadow" data-example-id="basic-forms">
                    <div class="form-body">
                        <form enctype="multipart/form-data" class="add-edit-product-form" method="post" action="">
                            @csrf
                            <div class="import-bills">
                                <h4 class="header-wrapper header-import-bills">Bill Info</h4>
                                <div class="import-bills-wrapper body-import-bills">
                                    <div class="form-group col-md-6">
                                        <label for="">Supplier</label>
                                        <select required class="form-control prod_suppliers_id" id="prodSupplier" name="supplier_id">
                                            @foreach($suppliers as $supplier)
                                                    <?php
                                                    $selectedSupplier = '';
                                                    if ($isEdit && $supplier->id === $product->supplier_id) {
                                                        $selectedSupplier = 'selected';
                                                    }
                                                    ?>
                                                <option {{ $selectedSupplier }} value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Date</label>
                                        <input required type="text" name="order_date" class="form-control order_date" id="order_date">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Total Bill</label>
                                        <input required type="number" value="0" name="total_bill" class="form-control total_bill" id="total_bill">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="notes">Notes</label>
                                        <textarea style="border-radius: 4px" class="form-control" id="notes" cols="10" rows="5" name="notes"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="import-bill-products">
                                <h4 class="header-wrapper header-import-bill-products">Select Product</h4>
                                <div class="import-bill-products-wrapper body-import-bill-products">
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
                                                <th>Image</th>
                                                <th>Action</th>
                                            </tr> </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="import_bill_products" name="import_bill_products">
                            <button style="margin: 20px" type="submit" class="btn btn-success">Create</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{ json_encode($productById) }}" id="product_by_id_string">
    <script src="{{ asset('public/js/import_bills.js')  . '?v=' . config('app.commit_version') }}"></script>
    <script src="{{ asset('public/js/main.js')  . '?v=' . config('app.commit_version') }}"></script>
    <script>
        var productImagePublicFolder = '<?php echo e(asset('public/Pro_Images/')); ?>';
    </script>
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
@endsection
