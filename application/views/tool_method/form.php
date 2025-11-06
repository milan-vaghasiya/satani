<form class="itemMasterForm" enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">

             <div class="col-md-3 form-group">
                <label for="method_code">Code</label>
                <input type="text" name="method_code" id="method_code" class="form-control req" value="<?=(!empty($dataRow->method_code))?$dataRow->method_code:""; ?>"/>
            </div>
            <div class="col-md-9 form-group">
                <label for="method_name">Tool Method</label>
                <input type="text" name="method_name" class="form-control req" value="<?= (!empty($dataRow->method_name)) ? $dataRow->method_name : "" ?>" />
            </div> 
            <div class="col-md-12 form-group">
                <label for="die_category">Tool</label>
                <select name="die_category[]" id="die_category" class="form-control select2 req" multiple>
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . ' data-category_code = "'.$row->category_code.'">' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error die_category">
            </div> 
        </div>
    </div> 
</form>