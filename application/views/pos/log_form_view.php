<div class="col-md-12 form-group">
            <table class="table table-bordered">
                <tr class="bg-light">
                    <th style="width:33%">Prc No</th>
                        <td><?=$prsData->prc_number?></td>
                </tr>
                    <tr >
                    <th class="bg-light">Product</th>
                    <td ><?=$prsData->item_name?></td>
                </tr>
                <tr>
                    <th class="bg-light">Completed Process</th>
                    <td><?=$prsData->completed_process_name?></td>
                </tr>
                <tr>
                    <th class="bg-light">Currunt Process</th>
                    <td><?=$prsData->process_name?></td>
                </tr>
                <tr > 
                    <th class="bg-light">Pend. Production qty</th>
                    <td ><?=((!empty($pending_production))?floatval($pending_production):'')?></td>
                </tr>
            </table>
        </div>
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$prsData->prc_id?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=$prsData->accepted_process_id?>">
        <input type="hidden" name="completed_process" id="completed_process" value="<?=$prsData->completed_process?>">
        <input type="hidden" name="process_from" id="process_from" value="<?=$prsData->process_from?>">
        <input type="hidden" name="accepted_qty" id="accepted_qty" value="<?=$prsData->accepted_qty?>">
        <input type="hidden" name="trans_type" id="trans_type" value="<?=$prsData->trans_type?>">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="production_qty">Production Qty</label>
                <input type="text" id="production_qty" class="form-control numericOnly req qtyCal" value="">

            </div>
            <div class="col-md-6 form-group">
                <label for="ok_qty">Ok Qty</label>
                <input type="text" name="ok_qty" id="ok_qty" class="form-control numericOnly req " value="" readonly>
                <div class="error batch_stock_error"></div>
            </div>
            <div class="col-md-6 form-group" >
                <label for="rej_found">Rejection Qty</label>
                <input type="text" name="rej_found" id="rej_found" class="form-control numericOnly qtyCal">
            </div>
            <div class="col-md-6 form-group">
                <label for="process_by">Process By</label>
                <select name="process_by" id="process_by" class="form-control select2">
                    <option value="1">Machine Process</option>
                    <option value="2">Department Process</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="processor_id">Machine</label>
                <select name="processor_id" id="processor_id" class="form-control select2">
                    <option value="">Select Machine</option>
                    <?php
                    if(!empty($machineList)):
                        foreach($machineList as $row):
                            ?><option value="<?=$row->id?>"> <?=((!empty($row->item_code))?'['.$row->item_code.'] ':'').$row->item_name?></option><?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            <?php
            if($processData->process_type == 2){
                ?>
                <div class="col-md-6 form-group">
                    <label for="wt_nos">Input Weight</label>
                    <input type="text" class="form-control floatOnly" name="wt_nos" id="wt_nos" value="<?=!empty($wt_nos)?$wt_nos:''?>">
                </div>
                <div class="col-md-12 form-group">
                    <label for="die_ids">Die</label>
                    <select name="die_ids[]" id="die_ids" class="form-control select2 req" multiple>
                        <?php
                        if(!empty($dieList)){
                            foreach($dieList AS $row){
                                ?>
                                <option value="<?=$row->id?>"><?=$row->die_number?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
            }
            ?>
            <div class="col-md-12 form-group" >
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control">
            </div>
        </div>
