<form >
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" id="old_item_id" name="old_item_id" value="<?=$dataRow->item_id?>">
            <input type="hidden" id="die_id" name="die_id" value="<?=$dataRow->die_id?>">
            <div class="col-md-12 form-group">
                <label for="item_id">Product</label>
                <select name="item_id" id="item_id" class="form-control select2 req">
                    <option value="">Select</option>
                    <?php
                        foreach ($itemList as $row) :
                            if($row->id != $dataRow->item_id):
                                echo '<option value="' . $row->id . '" >[' . $row->item_code.'] '.$row->item_name . '</option>';
                            endif;
                        endforeach;
                    ?>
                </select>
            </div> 
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control" rows="2"></textarea>
            </div>
           
        </div>
    </div>
</form>