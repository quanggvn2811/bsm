<div class="form-search-advanced col">
    <form method="GET">
        <div class="row col">
            <h4 class="header-wrapper search-bill-header title-header-toggle">Search Conditions</h4>
            <div class="row col-md-6 search-box-item">
                <div class="form-group row">
                    <label class="col-md-4 col-sm-4" for="title">Order Date</label>
                    <div class="col-md-4 col-sm-4 date-from-wrapper">
                        <input required type="text" @if(isset($_GET['bill_date_from'])) value="{{ $_GET['bill_date_from'] }}" @endif name="bill_date_from" class="form-control bill_date_from col-md-6" id="bill_date_from">
                    </div>
                    <div class="col-md-4 col-sm-4 date-to-wrapper">
                        <input required type="text" @if(isset($_GET['bill_date_to'])) value="{{ $_GET['bill_date_to'] }}" @endif name="bill_date_to" class="form-control bill_date_to col-md-6" id="bill_date_to">
                    </div>
                </div>

            </div>
            <div class="row col-md-6 search-box-item">
                <div class="form-group row">
                    <label class="col-md-4" for="title">Shop Name</label>
                    <div class="col-md-8">
                        <select name="shop_id[]" id="" class="form-control select_status_id" multiple>
                            <?php
                            $isSelectedAllShop = '';
                            if ((isset($_GET['shop_id']) && in_array('0', $_GET['shop_id'])) || !isset($_GET['shop_id'])) {
                                $isSelectedAllShop = 'selected';
                            }
                            ?>
                            <option value="0" {{ $isSelectedAllShop }}>All Shops</option>
                            @foreach($shops as $shop)
                                    <?php
                                    /*$defaultSelectedShopPrefix = [
                                        'MDS',
                                        'NX365',
                                    ];*/
                                    $selectedShop = '';
                                    if (isset($_GET['shop_id'])) {
                                        if (in_array($shop->id, $_GET['shop_id'])) {
                                            $selectedShop = 'selected';
                                        }
                                    }/* elseif (in_array($shop->prefix, $defaultSelectedShopPrefix)) {
                                        $selectedShop = 'selected';
                                    }*/
                                    ?>
                                <option {{ $selectedShop }} value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row col search-box-item justify-content-center">
            <button class="btn btn-sm-action btn-primary pl-3 pr-3 btn-submit-search"  type="submit">Search</button>
            <a class="btn btn-sm-action btn-dark pl-3 pr-3" href="{{ route('admin.revenue_report.index', $stock->id) }}" style="margin-left: 10px">Reset</a>
        </div>
        <div class="row col search-box-item justify-content-center">
            <button class="btn btn-sm-action btn-info pl-3 pr-3 search-date today search-today"  type="button" style="margin-left: 10px">Today</button>
            <button class="btn btn-sm-action btn-info pl-3 pr-3 search-date yesterday search-yesterday"  type="button" style="margin-left: 10px">Yesterday</button>
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
