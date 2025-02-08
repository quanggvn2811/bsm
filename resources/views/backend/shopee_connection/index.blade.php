@extends('backend.index')

@section('title', 'Basic Stock Manager' . ' | ' . 'Admin Dashboard')

@section('breadcrumb-links')
@endsection

@section('content')
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
                            <div class="col-md-6">
                                <select name="shop_id" id="" class="form-control col-md-6 select_status_id"
                                        style="width: 45%; height: 36px; margin: 0 20px; border-radius: 4px">
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
                                       style="width: 45%; height: 36px; margin: 0 20px; border-radius: 4px" class="form-control"
                                       autofocus="" type="text" id="validationCustom01" name="product_name" value="{{$_GET['product_name'] ?? ''}}"
                                >
                            </div>

                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary btn-search-product-from-pancake"><i class="fa fa-search"
                                                                                                                 aria-hidden="true"></i>
                                    Search
                                </button>
                                <a class="btn btn-sm-action btn-dark pl-3 pr-3" href="{{ url('admin/shopee_connection') }}">Reset</a>
                                <button style="background-color: #0b55f3" type="button" class="btn btn-primary btn-update-product-from-pancake"><i class="fa fa-download"
                                                                                                                 aria-hidden="true"></i>
                                    Update Product From Pancake
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
                            <th style="width: 150px">Action</th>
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
                                if (!empty($fields)) {
                                    foreach ($fields as $f) {
                                        $field .= "\n\r" . $f->name . ': ' . '<span style="color: red">' . $f->value . '</span>';
                                    }
                                }
                                $isLinked = !empty($variation->product_id);
                                ?>
                            <tr class="active">
                                <th scope="row">{{$variation->id}}</th>
                                <td>{{ $variation->variation_name }}</td>
                                <td><p style="font-weight: bold">{{ number_format($variation->last_imported_price) }}</p></td>
                                <td class="avatar" style="padding: 3px"><img class="avatar_variation avatar_product" style="max-width: 100px; max-height: 100px" src="{{ $avatarSrc }}"></td>
                                <td>{!! trim($field) !!}</td>
                                <td>{{ $shopByIds[$variation->shop_id]->name ?? '' }}</td>
                                <td>Ly flute uống vang phong cách Retro | Ly thủy tinh uống vang | Ly cocktail sinh tố đẹp | Ly Whisky</td>
                                <td><p style="font-weight: bold; text-align: center">{{ $variation->product_quantity }}</p></td>
                                <td>
                                    @if($isLinked)
                                        <button data-toggle="tooltip" data-original-title="Link with BSM product" title="Link with BSM product" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                                    @else
                                        <button class="btn btn-sm btn-primary"><i class="fa fa-link"></i></button>
                                    @endif
                                    <button class="btn btn-sm btn-warning"><i class="fa fa-sign-out"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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
    </style>
@endsection
