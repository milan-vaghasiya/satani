<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            
            <div class="col-md-6 form-group">
                <label for="item_id">Part</label>
                <select class="form-control select2 req processData" name="item_id" id="item_id">
                    <option value="">Select</option>
                    <?php
                    if(!empty($productList)){                        
                        foreach($productList as $row){
                            $selected = (!empty($dataRow->item_id) && ($dataRow->item_id == $row->id)) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->item_code.' '.$row->item_name. '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="vendor_id">Vendor</label>
                <select class="form-control select2 req processData" name="vendor_id" id="vendor_id">
                    <option value="">Select</option>
                    <?php
                    if(!empty($vendorList)){
                        foreach($vendorList as $row){
                            $selected = (!empty($dataRow->vendor_id) && ($dataRow->vendor_id == $row->id)) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->party_name. '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="process_id">Process</label>
                <select  id="process_select" class="form-control select2 req" multiple>
                    <?php
                    if(!empty($processData)):
                        foreach ($processData as $row) :
                                $selected = (!empty($dataRow->process_id) && (in_array($row->id, explode(",", $dataRow->process_id)))) ? "selected" : "";
                                echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->process_name . '</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
                <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->process_id)?$dataRow->process_id:'')?>">
				<div class="error process_id"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="rate">Rate</label>
                <input type="text" name="rate" id="rate" class="form-control req" value="<?=(!empty($dataRow->rate) ? $dataRow->rate : '')?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="rate_unit">Rate Unit</label>
                <select name="rate_unit" id="rate_unit" class="form-control select2 req countRate">
                    <option value="">Select Rate Unit</option> 
                    <option value="1"  <?=(!empty($dataRow->rate_unit) && $dataRow->rate_unit == "1")?"selected":""?>>Per Pcs.</option>
                    <option value="2"  <?=(!empty($dataRow->rate_unit) && $dataRow->rate_unit == "2")?"selected":""?>>Per Kg.</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="cycle_time">Cycle Time(In Sec)</label>
                <input type="text" name="cycle_time" id="cycle_time" class="form-control numericOnly" value="<?=(!empty($dataRow->cycle_time) ? $dataRow->cycle_time : '')?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="input_weight">Input Weight</label>
                <input type="text" name="input_weight" id="input_weight" class="form-control floatOnly" value="<?=(!empty($dataRow->input_weight) ? $dataRow->input_weight : '')?>">
            </div>
             <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <div class="input-group">
                    <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark) ? $dataRow->remark : '')?>">
                    <div class="input-append">
                        <button type="button" class="btn waves-effect waves-light btn-success ml-2 loaddata">Price Comparison</button>
                    </div>
                </div>
            </div>
        </div>
        
        <hr>
        
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="general_error error"></div>
                <table class="table jpExcelTable">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th style="width:25%;">Vendor</th>
                            <th style="width:25%;">Price</th>
                            <th style="width:25%;">Approved By</th>
                            <th style="width:25%;">Approved At</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyData">
                        <tr class="text-center"><td colspan="4">No Data Available.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>


        <hr>
        
        <div class="col-md-12">
            <h4>Vendor Price Detail :</h4>
            <div class="table-responsive">
                <div class="general_error error"></div>
                <table class="table jpExcelTable">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th style="width:25%;">Process</th>
                            <th style="width:10%;">Rate</th>
                            <th style="width:20%;">Rate Unit</th>
                            <th style="width:20%;">Cycle Time</th>
                        </tr>
                    </thead>
                    <tbody id="priceTbody">
                        <?php
                        if(!empty($tbodyData)){
                            echo $tbodyData;
                        }else{
                            echo '<tr class="text-center"><td colspan="4">No Data Available.</td></tr>';
                        }
                        ?>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function () {
    const $select = $('#process_select');
    const $hidden = $('#process_id');
    let order = [];

    $select.select2();

    $select.on('select2:select', function (e) {
        const id = e.params.data.id;
        if (!order.includes(id)) {
            order.push(id);
        }
        $hidden.val(order.join(','));
    });

    $select.on('select2:unselect', function (e) {
        const id = e.params.data.id;
        order = order.filter(val => val !== id);
        $hidden.val(order.join(','));
    });

});
</script>
</script>