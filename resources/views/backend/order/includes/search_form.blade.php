<div class="form-search-advanced col">
    <form method="GET">
        <div class="row col">
            <div class="row col-md-6">
                <div class="form-group row">
                    <label class="col-md-2" for="title">Name</label>
                    <div class="col-md-10">
                        <input class="form-control"  autofocus type="text" id="validationCustom01" name="prod_name"
                               value="{{$_GET['prod_name'] ?? ''}}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2" for="title">Category</label>
                    <div class="col-md-10">
                        <select class="form-control" id="prodCategory" name="prod_category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                    <?php
                                    $selectedCategory = '';
                                    if (isset($_GET['prod_category']) && $category->id == $_GET['prod_category']) {
                                        $selectedCategory = 'selected';
                                    }
                                    ?>
                                <option {{ $selectedCategory }} value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2" for="title">Quantity</label>
                    <div class="col-md-6 prod_calculation_wrapper">
                        <select required class="form-control prodType" id="prodType" name="prod_calculation">
                            <option @if(isset($_GET['prod_calculation']) && $_GET['prod_calculation'] === '=') selected @endif value="=">Equal</option>
                            <option @if(isset($_GET['prod_calculation']) && $_GET['prod_calculation'] === '<') selected @endif value="<">Less</option>
                            <option @if(isset($_GET['prod_calculation']) && $_GET['prod_calculation'] === '>') selected @endif value=">">Greater</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" placeholder="Quantity" autofocus type="number" id="" name="prod_quantity"
                               value="{{$_GET['prod_quantity'] ?? ''}}">
                    </div>
                </div>
            </div>

            <div class="row col-md-6">
                <div class="form-group row">
                    <label class="col-md-2" for="title">SKU</label>
                    <div class="col-md-10">
                        <input class="form-control"  autofocus type="text" id="validationCustom01" name="prod_sku"
                               value="{{$_GET['prod_sku'] ?? ''}}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2"  for="title">Supplier</label>
                    <div class="col-md-10">
                        <select class="form-control" id="prodSupplier" name="prod_supplier">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                    <?php
                                    $selectedSupplier = '';
                                    if (isset($_GET['prod_supplier']) && $_GET['prod_supplier'] == $supplier->id) {
                                        $selectedSupplier = 'selected';
                                    }
                                    ?>
                                <option {{ $selectedSupplier }} value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row col justify-content-center">
            <button class="btn btn-lg btn-primary pl-3 pr-3"  type="submit">Search</button>
        </div>
    </form>
</div>
<style>
    .justify-content-center {
        display: flex;
        justify-content: center;
    }
</style>
