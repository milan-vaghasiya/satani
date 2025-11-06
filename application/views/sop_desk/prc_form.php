<form>
	<div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
			<input type="hidden" name="prc_no" value="<?= (!empty($dataRow->prc_no)) ? $dataRow->prc_no : $prc_no ?>" />
			<input type="hidden" name="batch_id" value="<?= (!empty($dataRow->batch_id)) ? $dataRow->batch_id : ((!empty($batch_id))?$batch_id:'') ?>" />
			<div class="col-md-4 form-group">
				<label for="prc_number">PRC No.</label>
				<input type="text" name="prc_number" id="prc_number" class="form-control req" value="<?= (!empty($dataRow->prc_number)) ? $dataRow->prc_number : 'HF'.$prc_no ?>" readonly />
			</div>
			<div class="col-md-4 form-group">
				<label for="prc_date">PRC Date</label>
				<input type="date" id="prc_date" name="prc_date" class="form-control fyDates req" value="<?= (!empty($dataRow->prc_date)) ? $dataRow->prc_date : date("Y-m-d") ?>" />
			</div>
			<div class="col-md-4 form-group">
				<label for="target_date">Target Date</label>
				<input type="date" id="target_date" name="target_date" class="form-control req" value="<?= (!empty($dataRow->target_date)) ? $dataRow->target_date : date("Y-m-d") ?>" />
			</div>
			<div class="col-md-8 form-group">
				<label for="item_id">Product Name</label>
				<select name="item_id" id="item_id" class="form-control select2 req" autocomplete="off">
                    <option value="">Select Item</option>
					<?php
					if (!empty($itemList)) :
						foreach($itemList AS $row):
                            $selected = ((!empty($dataRow->item_id) && $dataRow->item_id == $row->id)?'selected':'');
                            ?><option value="<?=$row->id?>" <?=$selected?>><?=$row->item_code.' '.$row->item_name?></option><?php
                        endforeach;
					endif;
					?>
				</select>
			</div>
			<div class="col-md-4 form-group">
				<label for="qty">Quantity</label>
				<input type="text" name="qty" id="qty" class="form-control numericOnly countWeight req" min="0" placeholder="Enter Qty." value="<?= (!empty($dataRow->prc_qty)) ? floatval($dataRow->prc_qty) : "" ?>" />
			</div>
			<div class="col-md-12 form-group">
				<label for="remark">Production Instruction</label>
				<textarea name="remark" id="remark" class="form-control" rows="2" ><?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?></textarea>
			</div>
			<div class="col-md-8 form-group">
				<label for="tool_method">Tool Method</label>
				<select name="tool_method" id="tool_method" class="form-control select2 req" autocomplete="off">
                    <option value="">Select Method</option>
					<?php
					if (!empty($methodList)) :
						foreach($methodList AS $row):
                            $selected = ((!empty($dataRow->tool_method) && $dataRow->tool_method == $row->tool_method)?'selected':'');
                            ?><option value="<?=$row->tool_method?>" <?=$selected?>><?=$row->method_name?></option><?php
                        endforeach;
					endif;
					?>
				</select>
			</div>
		</div>
	</div>
</form>
<script>
    $(document).ready(function() {
        $(document).on('change',"#item_id",function(e){
            e.stopImmediatePropagation();e.preventDefault();
            var item_id = $("#item_id").val();
			$("#tool_method").html('<option value="">Select Method</option>');
			$("#tool_method").select2();
            if(item_id){
                $.ajax({
                    url : base_url + controller + '/getToolMethod',
                    type : 'post',
                    data : {item_id:item_id},
                    dataType : 'json'
                }).done(function(response){
                    $("#tool_method").html(response.options);
                    $("#tool_method").select2();
                });
            }
        });
    });
</script>