<form>
    <input type="hidden" id="id" name="id" value="<?=$id?>">
    <div class="row">
        <div class="col-md-12 form-group">
            <label for="die_block_id">Item</label>
            <select name="die_block_id" id="die_block_id" class="form-control select2 req">
                <option value="">Select Item</option>
                <?php
                if(!empty($dieBlockList)){
                    foreach($dieBlockList AS $row){
                        ?>
                        <option value="<?=$row->id?>"><?=$row->item_code.' '.$row->item_name?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-12 form-group">
            <div class="table-responsive">
                <div class="error general_error"></div>
                <table class="table table-bordered" id="batchTable">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Batch No</th>
                            <th>Stock Qty</th>
                            <th>Issue Qty</th>
                        </tr>
                    </thead>
                    <tbody id="batchTbody">
                        <tr>
                            <th colspan="4" class="text-center">No data available.</th>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-right" colspan="3">Total</th>
                            <th><input type="text" id="total_qty" class="form-control" readOnly></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        $(document).on('change','#die_block_id',function(e){
            e.stopImmediatePropagation();e.preventDefault();
            var item_id = $(this).val();
            $("#batchTbody").html("");
            if(item_id){
                $.ajax({
                    url: base_url + 'dieProduction/getBatchWiseStock',
                    type:'post',
                    data:{ item_id:item_id},
                    dataType:'json',
                    success:function(data){
                        $("#batchTbody").html("");
                        $("#batchTbody").html(data.tbodyData);
                    }
                });
            }
        });

        $(document).on('keyup change','.batchQty',function(){
            var row_id = $(this).data('rowid');
            var batch_qty = $(this).val() || 0;
            var stock_qty = $(this).data('stock_qty');
            $(".batch_qty_"+row_id).html('');

            if(parseFloat(batch_qty) > parseFloat(stock_qty)){
                $(".batch_qty_"+row_id).html('Invalid Qty.');
                $("#batch_qty_"+row_id).val("");
                $(this).val("");
            }   
            
            var batchQtyArr = $(".batchQty").map(function(){return $(this).val();}).get();
            var batchQtySum = 0;
            $.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
            $('#total_qty').val(batchQtySum); 
        });
        
    });
</script>