<form data-res_function="getDieLogResponse">
    <div class="row ">
        <div class="card">
            <div class="media align-items-center btn-group process-tags">
                <span class="badge bg-light-peach btn flex-fill">Die : <?=$dataRow->die_code?></span>
                <span class="badge bg-light-cream btn flex-fill" >Wo No :  <?=$dataRow->trans_number?></span>
            </div>                                       
        </div>
    </div>
    <div class="row">
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="die_id" id="die_id" value="<?=$dataRow->die_id?>">
        <input type="hidden" name="wo_id" id="wo_id" value="<?=$dataRow->id?>">
        <input type="hidden" name="process_by" id="process_by" value="<?=$process_by?>">
        <input type="hidden" name="ref_id" id="ref_id" value="<?=!empty($challan_id)?$challan_id:''?>">
        <input type="hidden" name="ref_trans_id" id="ref_trans_id" value="<?=!empty($ref_trans_id)?$ref_trans_id:''?>">
        <div class="col-md-3 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
        <?php if($process_by == 1){ ?>
            <div class="col-md-6 form-group">
                <label for="process_id">Setup</label>
                <select name="process_id" id="process_id" class="form-control select2" >
                    <option value="">Select Process</option>
                    <?php
                    if(!empty($processList)){
                        foreach($processList As $row){
                            ?><option value="<?=$row->process_id?>"><?=$row->process_name?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
           
        <?php } ?>
        <div class="col-md-3 form_group">
            <label for="qty">Qty</label>
            <input type="text" name="qty" id="qty" class="form-control numericOnly req" >
        </div>
      
        <?php if(!empty($process_by) && $process_by == 2){ ?>
        <!-- IF CHALLAN RECEIVE FROM VENDOR -->
            <div class="col-md-3 form-group">
                <label for="in_challan_no">In challan No</label>
                <input type="text" name="in_challan_no" id="in_challan_no" class="form-control req">
            </div>
            <input type="hidden" name="processor_id" id="processor_id" value="<?=$processor_id?>">
            <input type="hidden" name="process_id" id="process_id" value="<?=$process_id?>">
            <input type="hidden" name="status" id="status" value="1">
            <div class="col-md-3 form-group" >
                <label for="attachment">Attachment</label>
                <input type="file" name="attachment" id="attachment" class="form-control" />
            </div>
        <?php }else{?>
        <!--- IF INHOUSE PRODUCTION -->
        <div class="col-md-4 form-group">
            <label for="production_time">Production Time(In Hours)</label>
            <input type="text" name="production_time" id="production_time" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="processor_id">Machine</label>
            <select name="processor_id" id="processor_id" class="form-control select2 req">
                <option value="0">Select</option>
                <?php
                if(!empty($machineList)){
                    foreach($machineList as $row){
                        $selected = (!empty($dataRow->processor_id ) && $dataRow->processor_id  == $row->id)?'selected':'';
                        echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_code.'</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="operator_id">Operator</label>
            <select name="operator_id" id="operator_id" class="form-control select2">
                <option value="0">Select</option>
                <?php
                if(!empty($operatorList)){
                    foreach($operatorList as $row){
                        ?><option value="<?=$row->id?>"><?=$row->emp_name?></option><?php
                    }
                }
                ?>
            </select>
        </div>
        <?php } ?>
        <div class="col-md-12 form-group">
            <label for="remark">Remark</label>
            <textarea type="text" name="remark" id="remark" class="form-control" value="" rows="2"></textarea>
        </div>
        <div class="col-md-12 form-group">
             <?php
                $param = "{'formId':'addDieLog','fnsave':'saveProductionLog','controller':'dieProduction'}";
            ?>
            <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" ><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</form>

<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='logTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th style="min-width:100px">Process</th>
                        <th style="min-width:100px">Qty</th>
                        <th>Production Time</th>
                        <th>Machine/ Vendor</th>
						<th>In Challan No</th>
                        <th>Operator</th>
                        <th>Remark</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="logTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'wo_id':$("#wo_id").val(),'ref_trans_id':$("#ref_trans_id").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getProductionLogHtml','controller':'dieProduction'};
        getTransHtml(postData);
        tbodyData = true;
    }

});
function getDieLogResponse(data,formId="addDieLog"){ 
    if(data.status==1){
        if(formId){ $('#'+formId)[0].reset(); }
        
        var postData = {'postData':{'wo_id':$("#wo_id").val(),'ref_trans_id':$("#ref_trans_id").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getProductionLogHtml','controller':'dieProduction'};
        getTransHtml(postData);
        initTable();
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}
</script>