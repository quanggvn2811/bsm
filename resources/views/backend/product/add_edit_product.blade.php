@extends('backend.index')

@section('title', 'Basic Stock Manager' . ' | ' . 'Admin Dashboard')

@section('breadcrumb-links')
@endsection

@section('content')
    <?php $isEdit = isset($product); ?>
    <div id="page-wrapper">
        <div class="main-page">
            <div class="tables">
                <h2 class="title1 col-md-4" style="width: 100%; margin-top: .8em"><a href="{{ route('admin.categories.index', $stock->id) }}">{{ $stock->name }}</a> / Add Product</h2>
                <div class="form-grids row widget-shadow" data-example-id="basic-forms">
                    <div class="form-body">
                        @php
                            $routeForm = route('admin.products.store', ['stock' => $stock->id]);
                            if ($isEdit) {
                                $routeForm = route('admin.products.update', ['stock' => $stock->id, 'product' => $product->id]);
                            }
                        @endphp
                        <form enctype="multipart/form-data" class="add-edit-product-form" method="post" action="{{ $routeForm }}">
                            @csrf
                            <div class="form-group">
                                <label for="prodName">Name</label>
                                <input required type="text" @if($isEdit) value="{{ $product->name }}" @endif name="name" class="form-control" id="prodName" placeholder="Name">
                            </div>
                            {{--<div class="form-group">
                                <label for="prodSlug">Slug</label>
                                <input type="text" class="form-control" id="prodSlug" placeholder="Slug">
                            </div>--}}
                            <div class="form-group">
                                <label for="prodDescription">Description</label>
                                <textarea class="form-control" id="prodDescription" name="description" cols="30" rows="5" placeholder="Description">@if($isEdit) {!! $product->description !!} @endif</textarea>
                            </div>
                            <?php
                                if ($isEdit) {
                                    $prodImages = json_decode($product->images);
                                    $avatarSrc = '#';
                                    if (!empty($prodImages[0])) {
                                        $avatar = $prodImages[0];
                                        $avatarSrc = asset(\App\Models\Product::PUBLIC_PROD_IMAGE_FOLDER . '/' . $avatar);
                                    }
                                }
                            ?>
                            <div class="form-group row">
                                <div class="import-img col-md-2">
                                    <label for="exampleInputFile">Images</label>
                                    <input type="file" name="images[]" multiple id="prodImages"> <p class="help-block">Select product image(s)</p>
                                </div>
                                <div class="img-avatar col-md-10">
                                    @if($isEdit)
                                        <img class="avatar_product" style="max-width: 200px; max-height: 200px" src="{{ $avatarSrc }}">
                                    @endif
                                </div>
                            </div>
                            <div class="row" style="margin-left: -15px">
                                <div class="checkbox form-group col-md-6">
                                    <?php
                                    $checkedStatus = 'checked';
                                    /*if ($isEdit && $product->status) {
                                        $checkedStatus = 'checked';
                                    }*/
                                    ?>
                                    <label> <input {{ $checkedStatus }} value="1" type="checkbox" name="status"><b>Status</b></label>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="prodSku">SKU</label>
                                    <input name="sku" @if($isEdit) value="{{ $product->sku }}" @endif type="text" class="form-control" id="prodSku" placeholder="SKU">
                                </div>
                            </div>
                            <div class="row" style="margin-left: -15px">
                                <div class="form-group col-md-4">
                                    <label for="prodCategory">Category</label>
                                    <select required class="form-control" id="prodCategory" name="category_id">
                                        @foreach($categories as $category)
                                                <?php
                                                $selectedCategory = '';
                                                if ($isEdit && $category->id == $product->category_id) {
                                                    $selectedCategory = 'selected';
                                                }
                                                ?>
                                            <option {{ $selectedCategory }} value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="prodPrice">Price</label>
                                    <input required @if($isEdit) value="{{ $product->price }}" @endif type="number" name="price" class="form-control" id="prodPrice" placeholder="Price">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="prodQuantity">Quantity</label>
                                    <input @if($isEdit) value="{{ $product->quantity }}" @endif required type="number" class="form-control" name="quantity" id="prodQuantity" placeholder="Quantity">
                                </div>
                            </div>
                            <div class="row" style="margin-left: -15px">
                                <div class="form-group col-md-6">
                                    <label for="prodQuantity">Type</label>
                                    <select required class="form-control prodType" id="prodType" name="type">
                                        @foreach(\App\Models\Product::PRODUCT_TYPE as $val => $type)
                                                <?php
                                                $selectedType = '';
                                                if ($isEdit && $val === $product->type) {
                                                    $selectedType = 'selected';
                                                }
                                                ?>
                                            <option {{ $selectedType }} value="{{ $val }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <?php
                                $display = 'none';
                                if ($isEdit && \App\Models\Product::TYPE_MULTIPLE === $product->type) {
                                    $display = 'block';
                                }
                                ?>
                                <div class="form-group sub_product_section  col-md-6" style="display: {{$display}}">
                                    <label for="">Sub Products</label>
                                    <input @if($isEdit) value="{{ $subProductSku }}" @endif type="text" class="form-control" name="sub_product_sku" id="prodSubProduct" placeholder="Press Sub Product SKU, Ex: MKAR018;MKBN006;...">
                                </div>
                            </div>
                            <div class="form-group suppliers">
                                <div class="header" style="margin-bottom: 20px">
                                    <label style="margin-left: -15px" class="col-md-2" for="prodSupplier">Suppliers</label>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-success btn-add-supplier-row"><i style="margin-right: 10px" class="fa fa-plus"></i>Add Supplier</button>
                                    </div>
                                    <label class="col-md-2" for="">Average Cost</label>
                                    <div class="form-group col-md-3">
                                        <input type="number" id="avg_cost" @if($isEdit) value="{{ $product->cost }}" @endif readonly name="cost" required class="form-control" placeholder="Average cost">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-primary btn-edit-average-cost"><i class="fa fa-edit"></i></button>
                                    </div>
                                </div>
                                {{--Template--}}
                                <?php
                                    $productSuppliersIndex = 1;
                                    if ($isEdit && $productSuppliers) {
                                        $productSuppliersIndex = count($productSuppliers);
                                    }
                                ?>
                                <div data-prod_suppliers_index="{{ $productSuppliersIndex }}" class="row supplier-row supplier-row-template" style="display: none">
                                    <div class="form-group col-md-4">
                                        <select required class="form-control prod_suppliers_id" id="prodSupplier" name="">
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
                                    <div class="form-group col-md-3">
                                        <input value="0" name="" type="number" class="form-control prod_suppliers_cost" id="" placeholder="Cost">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input @if($isEdit) value="{{ $product->supplier_sku }}" @endif name="" type="text" class="form-control prod_suppliers_sku" id="" placeholder="Supplier SKU">
                                    </div>
                                    <div class="form-group col-md-2 justify-content-center">
                                        <button type="button" class="btn-delete-supplier-row btn btn-danger"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                                {{--/Template--}}
                                <div class="row">
                                    <label class="col-md-4" for="prodSupplier">Supplier Name</label>
                                    <label class="col-md-3" for="prodCost">Supplier Cost</label>
                                    <label class="col-md-3" for="prodSupplierSku">Supplier SKU</label>
                                </div>
                                @if($isEdit)
                                    @foreach($productSuppliers as $index => $pSupplier)
                                        <div class="row supplier-row @if(0 === $index) first-supplier-row @endif ">
                                            <div class="form-group col-md-4">
                                                <select class="form-control prod_suppliers_id" id="prodSupplier" name="prod_suppliers[{{ $index }}][id]">
                                                    @foreach($suppliers as $supplier)
                                                            <?php
                                                            $selectedSupplier = '';
                                                            if ($isEdit && $supplier->id === $pSupplier->supplier_id) {
                                                                $selectedSupplier = 'selected';
                                                            }
                                                            ?>
                                                        <option {{ $selectedSupplier }} value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input name="prod_suppliers[{{ $index }}][cost]" @if($isEdit) value="{{ $pSupplier->s_cost }}" @endif type="number" class="form-control prod_suppliers_cost is_active" id="prodCost" placeholder="Cost">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input @if($isEdit) value="{{ $pSupplier->s_sku }}" @endif name="prod_suppliers[{{ $index }}][sku]" type="text" class="form-control prod_suppliers_sku" id="prodSupplierSku" placeholder="Supplier SKU">
                                            </div>
                                            <div class="form-group col-md-2 justify-content-center">
                                                <button type="button" class="btn-delete-supplier-row btn btn-danger"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row supplier-row first-supplier-row">
                                        <div class="form-group col-md-4">
                                            <select class="form-control prod_suppliers_id" id="prodSupplier" name="prod_suppliers[0][id]">
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
                                        <div class="form-group col-md-3">
                                            <input name="prod_suppliers[0][cost]" @if($isEdit) value="{{ $product->cost }}" @endif type="number" class="form-control prod_suppliers_cost is_active" id="prodCost" placeholder="Cost">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input @if($isEdit) value="{{ $product->supplier_sku }}" @endif name="prod_suppliers[0][sku]" type="text" class="form-control prod_suppliers_sku" id="prodSupplierSku" placeholder="Supplier SKU">
                                        </div>
                                        <div class="form-group col-md-2 justify-content-center">
                                            <button type="button" class="btn-delete-supplier-row btn btn-danger"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if(isset($product))
                                <button type="submit" class="btn btn-default">Update</button>
                            @else
                                <button type="submit" class="btn btn-success">Create</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    </style>
    <script src="{{ asset('public/js/products.js') }}"></script>
@endsection
