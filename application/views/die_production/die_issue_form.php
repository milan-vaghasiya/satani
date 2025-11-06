<form>
    <div class="row">
        <input type="hidden" name="id" id="id" value="<?=$dataRow->id?>">
        <input type="hidden" name="die_id" id="die_id" value="<?=$dataRow->die_id?>">
        <input type="hidden" name="status" id="status" value="3">
        <div class="col-md-12 form-group">
            <label for="emp_id">Handover To</label>
            <select name="emp_id" id="emp_id" class="form-control select2 req">
                <option value="">Select</option>
                <?php
                if(!empty($empList)){
                    foreach($empList AS $row){
                        ?>
                        <option value="<?=$row->id?>"><?=$row->emp_name?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-12 form-group">
            <label for="remark">Remark</label>
            <textarea name="remark" id="remark" class="form-control"></textarea>
        </div>
    </div>
</form>