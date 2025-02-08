<!-- Modal -->
<div class="modal fade" id="link-with-bsm-product-modal-dialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Connect BSM Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="">
                    <div class="row" style="padding: 10px 30px">
                        <div class="variation-product form-group" style="min-height: 50px">
                            <div class="col-md-10 variation-name"></div>
                            <div style="color: red" class="col-md-2 variation-fields"></div>
                            <input type="hidden" class="_variation_product_id">
                        </div>
                        <div class="form-group">
                            <div class="search-products col-md-10">
                                <select class="select-product-list" id="select-product-item" placeholder="Search product sku or name">
                                    <option value="">Select product</option>
                                    @foreach($products as $product)
                                            <?php $text = $product->sku ? '[' . $product->sku . '] ' . $product->name : $product->name ?>
                                        <option value="{{ $product->id }}">{{ $text }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="quantity col-md-2">
                                <input type="number" style="border-radius: 4px; text-align: center" class="form-control product_quantity" name="product_quantity" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-save-modal-connect">Save</button>
            </div>
        </div>
    </div>
</div>
