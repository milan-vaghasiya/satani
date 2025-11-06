<form class="itemMasterForm" enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">

            <div class="col-md-12 form-group">
                <label for="item_id">Product Name</label>
                <select name="item_id" id="item_id" class="form-control select2 req dieCode">
                    <option value="">Select Product Name</option>
                    <?php
                    if(!empty($itemList)){
                        foreach($itemList AS $row){
                            $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id) ? "selected" : "";
                            ?><option value="<?=$row->item_id?>" data-item_code="<?=$row->item_code?>" <?=$selected?>><?=$row->item_code.' '.$row->item_name?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="tool_method">Tool Method</label>
                <select name="tool_method" id="tool_method" class="form-control select2 req dieCode">
                    <option value="">Select Product Name</option>
                    <?php
                    if(!empty($toolMethodList)){
                        foreach($toolMethodList AS $row){
                            ?><option value="<?=$row->id?>"><?=$row->method_code.' '.$row->method_name?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!--<div class="col-md-6 form-group">
                <label for="grade_id">Material Grade</label>
                <select name="grade_id" id="grade_id" class="form-control select2 req">
                    <option value="">Select Material Grade</option>
                    <?php
                        foreach($materialGrade as $row):
                            $selected = (!empty($dataRow->grade_id) && $dataRow->grade_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->material_grade.' '.$row->standard.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>-->

            <!-- <div class="col-md-6 form-group">
                <label for="tool_life">Tool Life</label>
                <input type="text" name="tool_life" class="form-control floatOnly" value="<?= (!empty($dataRow->tool_life)) ? $dataRow->tool_life : "" ?>" />
            </div> --> 

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control" rows="2"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
           
        </div>
    </div>
</form>