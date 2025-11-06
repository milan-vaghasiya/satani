<form >
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="die_id" id="die_id" value="<?=(!empty($dataRow->die_id))?$dataRow->die_id:""?>">
            <input type="hidden" id="item_id" name="item_id" value="<?=$dataRow->item_id?>">
            <input type="hidden" name="status" id="status" value="6">
           <!--  <div class="col-md-12 form-group">
                <label for="die_id">Die</label>
                <select name="die_id" id="die_id" class="form-control select2 req">
                    <option value="0">Select</option>
                    <?php
                        /* foreach ($dieList as $row) :
                            $selected = (!empty($dataRow->die_id) && $dataRow->die_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->die_code.'] '.$row->die_name . '</option>';
                        endforeach; */
                    ?>
                </select>
            </div>  -->
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control" rows="2"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
           
        </div>
    </div>
</form>
