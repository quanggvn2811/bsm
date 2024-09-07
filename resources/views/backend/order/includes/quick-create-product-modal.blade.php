<!-- Modal -->
<div class="modal fade" id="quick-create-product-modal-dialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Quick create product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input required type="text" name="quick-product-name" class="form-control quick-product-name col-md-6" id="quick-product-name">
                </div>
                <div class="form-group" style="display: inline-block; margin-top: 15px">
                    <div class="col-md-4">
                        <label for="name">Quantity</label>
                        <input required readonly type="number" value="1" name="quick-product-quantity" class="form-control quick-product-quantity col-md-6" id="quick-product-quantity">
                    </div>
                    <div class="col-md-4">
                        <label for="name">Price</label>
                        <input required readonly type="number" value="0" name="quick-product-price" class="form-control quick-product-price col-md-6" id="quick-product-price">
                    </div>
                    <div class="col-md-4">
                        <label for="name">Cost</label>
                        <input required readonly type="number" value="0" name="quick-product-cost" class="form-control quick-product-cost col-md-6" id="quick-product-cost">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-do-quick-create-product off-mode">Create</button>
            </div>
        </div>
    </div>
</div>
<style>
    .off-mode {
        opacity: .5;
        pointer-events: none;
    }
</style>
