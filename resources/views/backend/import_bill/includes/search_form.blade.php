<div class="form-search-advanced col">
    <form method="GET">
        <div class="row col">
            <h4 class="header-wrapper search-form-header title-header-toggle" data-trigger_to="search-box-item">Search Bills</h4>
            <div class="row col-md-6 search-box-item">
                <div class="form-group row">
                    <label class="col-md-4" for="title">Suppliers</label>
                    <div class="col-md-8">
                        <select class="form-control" id="supplier" name="supplier">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                    <?php
                                    $selectedSupplier = '';
                                    if (isset($_GET['supplier']) && $_GET['supplier'] == $supplier->id) {
                                        $selectedSupplier = 'selected';
                                    }
                                    ?>
                                <option {{ $selectedSupplier }} value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4" for="title">Product Name</label>
                    <div class="col-md-8">
                        <input class="form-control"  autofocus type="text" id="validationCustom01" name="product_name"
                               value="{{$_GET['product_name'] ?? ''}}">
                    </div>
                </div>
            </div>
            <div class="row col-md-6 search-box-item">
                <div class="form-group row">
                    <label class="col-md-4 col-sm-4" for="title">Order Date</label>
                    <div class="col-md-4 col-sm-4 date-from-wrapper">
                        <input required type="text" @if(isset($_GET['bills_from'])) value="{{ $_GET['bills_from'] }}" @endif name="bills_from" class="form-control bills_from col-md-6" id="bills_from">
                    </div>
                    <div class="col-md-4 col-sm-4 date-to-wrapper">
                        <input required type="text" @if(isset($_GET['bills_to'])) value="{{ $_GET['bills_to'] }}" @endif name="bills_to" class="form-control bills_to col-md-6" id="bills_to">
                    </div>
                </div>
            </div>
        </div>

        <div class="row col search-box-item justify-content-center">
            <button class="btn btn-sm-action btn-primary pl-3 pr-3 btn-submit-search"  type="submit">Search</button>
            <a class="btn btn-sm-action btn-dark pl-3 pr-3" href="{{ route('admin.import_bills.index', $stock->id) }}" style="margin-left: 10px">Reset</a>
        </div>
        <div class="row col search-box-item justify-content-center">
            <button class="btn btn-sm-action btn-info pl-3 pr-3 search-date today search-today"  type="button" style="margin-left: 10px">Today</button>
            <button class="btn btn-sm-action btn-info pl-3 pr-3 search-date yesterday search-yesterday"  type="button" style="margin-left: 10px">Yesterday</button>
            <button class="btn btn-sm-action btn-info pl-3 pr-3 search-date last-week search-last-week"  type="button" style="margin-left: 10px">Last Week</button>
            <button class="btn btn-sm-action btn-info pl-3 pr-3 search-date this-month search-this-month"  type="button" style="margin-left: 10px">This Month</button>
        </div>
    </form>
</div>
<style>
    .justify-content-center {
        display: flex;
        justify-content: center;
    }
    .header-wrapper {
        text-transform: uppercase;
        font-style: italic;
        color: green !important;
        cursor: pointer;
    }
    .search-box-item {
        /*display: none;*/
    }
</style>
