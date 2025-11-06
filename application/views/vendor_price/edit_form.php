<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            
            <div class="col-md-6 form-group">
                <label for="rate_unit">Rate Unit</label>
                <select name="rate_unit" id="rate_unit" class="form-control modal-select2 req countRate">
                    <option value="">Select Rate Unit</option> 
                    <option value="1"  <?=(!empty($dataRow->rate_unit) && $dataRow->rate_unit == "1")?"selected":""?>>Per Pcs.</option>
                    <option value="2"  <?=(!empty($dataRow->rate_unit) && $dataRow->rate_unit == "2")?"selected":""?>>Per Kg.</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="rate">Vendor</label>
                <input type="text" class="form-control floatOnly" name="rate" id="rate" value="<?= (!empty($dataRow->rate)) ? $dataRow->rate : ""; ?>">
            </div>
        </div>
       
    </div>
</form>