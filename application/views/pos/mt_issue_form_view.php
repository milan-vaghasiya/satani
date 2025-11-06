<div class="col-md-12 form-group">
    <table class="table table-bordered">
        <tr class="bg-light">
            <th style="width:33%">Item</th>
            <td><?=$itemData->item_name?></td>
        </tr>
        <tr >
            <th class="bg-light">Location</th>
            <td ><?=$locationData->location?></td>
        </tr>
        <tr>
            <th class="bg-light">Batch No</th>
            <td><?=$tagData->batch_no?></td>
        </tr>
        <tr > 
            <th class="bg-light">Heat No</th>
            <td ><?=$tagData->heat_no?></td>
        </tr>
        <tr > 
            <th class="bg-light">Qty</th>
            <td ><?=floatval($tagData->qty).$itemData->uom?></td>
        </tr>
    </table>
</div>
<input type="hidden" name="id" id="id" value="">
<input type="hidden" name="item_id" id="item_id" value="<?=$tagData->item_id?>">
<input type="hidden" name="batch_no" id="batch_no" value="<?=$tagData->batch_no?>">
<input type="hidden" name="heat_no" id="heat_no" value="<?=$tagData->heat_no?>">
<input type="hidden" name="location_id" id="location_id" value="<?=$tagData->location_id?>">
<input type="hidden" name="tag_qty" id="tag_qty" value="<?=$tagData->qty?>">
<input type="hidden" name="process_id" id="process_id" value="">
<input type="hidden" name="ppc_qty" id="ppc_qty" value="">
<div class="row">
    <div class="col-md-6 form-group">
        <label for="qty">Issue Qty</label>
        <input type="text" id="qty" name="qty" class="form-control floatOnly req" value="">

    </div>
    <div class="col-md-6 form-group">
        <label for="prc_id">Batch</label>
        <select name="prc_id" id="prc_id" class="form-control select2">
            <option value="">Select Batch</option>
            <?php
            if(!empty($prcList)):
                foreach($prcList as $row):
                    ?><option value="<?=$row->id?>" data-process_id = "<?=$row->process_id?>"  data-ppc_qty = "<?=$row->qty?>"> <?=$row->prc_number?></option><?php
                endforeach;
            endif;
            ?>
        </select>
    </div>
</div>

<script>
    $(document).ready(function(){
        $(document).on('change', '#prc_id', function (e) {
            e.stopImmediatePropagation();e.preventDefault();
            var process_id = $("#prc_id :selected").data('process_id');
            var ppc_qty = $("#prc_id :selected").data('ppc_qty');
            $("#process_id").val(process_id);
            $("#ppc_qty").val(ppc_qty);
        });
    });
</script>