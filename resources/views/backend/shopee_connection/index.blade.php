@extends('backend.index')

@section('title', 'Basic Stock Manager' . ' | ' . 'Admin Dashboard')

@section('breadcrumb-links')
@endsection

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
    @include('backend.shopee_connection.includes.link-with-bsm-product-modal-dialog')
    <div id="page-wrapper">
        <div class="main-page">
            <div class="tables">
                <div class="row">
                    <div class="col-md-2 pd-l-0">
                        <div class="short-url-menu">
                            <div class="first">
                                <a href="{{ route('admin.categories.index', $stock->id) }}">{{ $stock->name }}</a>
                            </div>
                            <div class="second">
                                <a href="{{ url('admin/shopee_connection') }}">Shopee Connection</a>
                            </div>
                        </div>
                    </div>
                    {{--<div class="shop-to-update">--}}
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <form method="GET" id="form-search-product-from-pancake" class="col-md-10">
                            <div class="col-md-7">
                                <select name="shop_id" id="" class="form-control col-md-4 select_shop_id"
                                        style="width: 30%; height: 36px; margin: 0 20px; border-radius: 4px">
                                    <?php
                                    $isSelectedAllShop = '';
                                    if ((isset($_GET['shop_id']) && '0' == $_GET['shop_id']) || !isset($_GET['shop_id'])) {
                                        $isSelectedAllShop = 'selected';
                                    }
                                    ?>
                                    <option value="0" {{ $isSelectedAllShop }}>All Shops</option>
                                    @foreach($shops as $shop)
                                            <?php
                                            $selectedShop = '';
                                            if (isset($_GET['shop_id']) && $shop->id == $_GET['shop_id']) {
                                                $selectedShop = 'selected';
                                            }

                                            $isShopee = false;
                                            if (str_starts_with($shop->prefix, 'SP_')) {
                                                $isShopee = true;
                                            }
                                            ?>
                                        <option {{ $selectedShop }} value="{{ $shop->id }}"
                                                @if($isShopee) style="background-color: rgb(238, 77, 45);; color: #FFF" @endif>{{ $shop->name }}</option>
                                    @endforeach
                                </select>
                                <input placeholder="Search Product"
                                       style="width: 30%; height: 36px; margin: 0 20px; border-radius: 4px" class="form-control col-md-5"
                                       autofocus="" type="text" id="validationCustom01" name="product_name" value="{{$_GET['product_name'] ?? ''}}"
                                >
                                <select name="linked_status" id="" class="form-control select_status_id col-md-2"
                                        style="width: 20%; height: 36px; margin: 0 20px; border-radius: 4px">
                                    <option @if(!isset($_GET['linked_status']) || $_GET['linked_status'] && $_GET['linked_status'] == '0') selected @endif value="0">All status</option>
                                    <option @if(isset($_GET['linked_status']) && $_GET['linked_status'] == '1') selected @endif value="1">Product linked</option>
                                    <option @if(isset($_GET['linked_status']) && $_GET['linked_status'] == '2') selected @endif value="2">No link</option>
                                    <option @if(isset($_GET['linked_status']) && $_GET['linked_status'] == '3') selected @endif value="3">No COST</option>
                                </select>
                            </div>

                            <div class="col-md-5">
                                <button type="submit" class="btn btn-primary btn-search-product-from-pancake"><i class="fa fa-search"
                                                                                                                 aria-hidden="true"></i>
                                    Search
                                </button>
                                <a class="btn btn-sm-action btn-dark pl-3 pr-3" href="{{ url('admin/shopee_connection') }}">Reset</a>
                                <button style="background-color: #0b55f3" type="button" class="btn btn-primary btn-update-product-from-pancake"><i class="fa fa-download"
                                                                                                                 aria-hidden="true"></i>
                                    DOWNLOAD Product From Pancake
                                </button>
                            </div>
                        </form>
                    {{--</div>--}}
                </div>
                <div class="bs-example widget-shadow" data-example-id="contextual-table">
                    <h4 class="col-md-4" style="margin-bottom: 0">Product Variations ({{ $productVariations->total() }})</h4>
                    <div class="bsm-pagination" style="float: right">
                        {{ $productVariations->appends(Request::all())->links() }}
                    </div>
                    <table class="table" style="font-size: 14px">
                        <thead>
                        {{--<tr>
                            <th>#</th>
                            <th style="text-align: center; background-color: #222d32; color: #ffffff" colspan="2">BSM Shop</th>
                            <th style="text-align: center; background-color: #ee4d2d; color: #ffffff" colspan="3">Shopee Shop</th>
                        </tr>--}}
                        <tr>
                            <th>#</th>
                            <th style="width: 20%">Variation Name</th>
                            <th style="width: 120px;">Last Imported Price</th>
                            <th>Avatar</th>
                            <th>Fields</th>
                            <th>Shop</th>
                            <th style="width: 20%">Product Name</th>
                            <th style="width: 120px;">Product Quantity</th>
                            <th style="width: 200px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($productVariations as $variation)
                                <?php
                                $variationImg = json_decode($variation->images);
                                $avatarSrc = '#';
                                if (!empty($variationImg[0])) {
                                    $avatarSrc = $variationImg[0];
                                }

                                $fields = json_decode($variation->fields);
                                $field = '';
                                $quickProdField = '';
                                if (!empty($fields)) {
                                    foreach ($fields as $f) {
                                        $field .= "\n\r" . $f->name . ': ' . '<span style="color: red">' . $f->value . '</span>';
                                        $quickProdField .= "\n\r" . $f->name . ': ' . $f->value;
                                    }
                                }
                                $isLinked = !empty($variation->product_id);
                                ?>
                            <tr class="active variation variation-{{$variation->id}}" data-variation_id="{{$variation->id}}"
                                data-product_id="{{$variation->product_id}}"
                                data-product_quantity="{{$variation->product_quantity}}"
                            >
                                <th scope="row"><span data-toggle="tooltip" data-original-title="{{$variation->variation_id}}" class="span-tooltip">{{$variation->id}}</span></th>
                                <td class="name">{{ $variation->variation_name }}</td>
                                <td><p style="font-weight: bold">{{ number_format($variation->last_imported_price) }}</p></td>
                                <td class="avatar" style="padding: 3px"><img class="avatar_variation avatar_product" style="max-width: 100px; max-height: 100px" src="{{ $avatarSrc }}"></td>
                                <td class="fields">{!! trim($field) !!}</td>
                                <td>{{ $shopByIds[$variation->shop_id]->name ?? '' }}</td>
                                @if($variation->product_id)
                                    <td class="bsm-product-name"><a target="_blank" href="{{ route('admin.products.edit', ['stock' => $stock->id, 'product' => $variation->product_id]) }}">{{ $variation->product->name ?? '' }}</a></td>
                                @else
                                    <td class="bsm-product-name"></td>
                                @endif
                                <td><p style="font-weight: bold; text-align: center" class="bsm-product-quantity">{{ $variation->product_quantity }}</p></td>
                                <td>
                                    <button data-toggle="tooltip" data-original-title="Link with BSM product"
                                            class="btn btn-sm btn-primary btn-show-link-modal"><i class="fa fa-link"></i></button>
                                    <button data-toggle="tooltip" data-original-title="Unlink with BSM product" class="btn btn-sm btn-warning btn-unlink-bsm-product"><i class="fa fa-sign-out"></i></button>
                                    <button data-toggle="tooltip" data-original-title="Delete this variation product"
                                            class="btn btn-sm btn-danger"><i class="fa fa-trash btn-delete-variation"></i></button>
                                    <a target="_blank" href="{{ route('admin.products.create',
                                                [
                                                    'stock' => $stock->id,
                                                    'quick_prod_name' => $quickProdField ? $variation->variation_name . ' | ' . $quickProdField : $variation->variation_name,
                                                    // 'quick_prod_quantity' => 0,
                                                    'quick_prod_cost' => $variation->last_imported_price,
                                                    // 'quick_prod_price' => 2 * $variation->last_imported_price,
                                                    'quick_prod_avatar_src' => $avatarSrc,
                                                ]
                                                )}}" class="btn btn-sm btn-success btn-add-product"
                                       data-toggle="tooltip" data-original-title="Quick create BSM product"><i style="" class="fa fa-arrow-up"></i></a>
                                    {{--<button data-toggle="tooltip" data-original-title="Quick create BSM product"
                                            class="btn btn-sm btn-success"><i style="" class="fa fa-arrow-up"></i></button>--}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="bsm-pagination" style="float: right">
                        {{ $productVariations->appends(Request::all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('public/js/main.js')  . '?v=' . config('app.commit_version') }}"></script>
    <script src="{{ asset('public/js/shopee-connection.js')  . '?v=' . config('app.commit_version') }}"></script>
    <input type="hidden" value="{{ $stock->id }}" name="stock_id">
    <style>
        .bsm-pagination .pagination {
            margin: 0;
            font-size: 14px;
        }
        .table tr td, .table tr th {
            vertical-align: middle !important;
        }
    </style>
@endsection
