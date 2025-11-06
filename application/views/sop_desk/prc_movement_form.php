<form data-res_function="getPrcMovementResponse">
    <div class="card">
        <div class="media align-items-center btn-group process-tags">
            <span class="badge bg-light-cream btn flex-fill">Currunt Process : <?=!empty($dataRow->process_name)?$dataRow->process_name:'Initial Stage'?></span>
			<span class="badge bg-light-sky btn flex-fill">Movement Type :  <?= $movement_type;?></span>
            <span class="badge bg-light-cream btn flex-fill">PRC No :  <?= $prc_number;?></span>
            <span class="badge bg-light-sky btn flex-fill">Item :  <?= $item_name;?></span>
            <span class="badge bg-light-cream btn flex-fill" id="pending_movement_qty">Pending Qty :  </span>
        </div>                                       
    </div>
    <div class="row">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$dataRow->prc_id?>">
        <input type="hidden" name="process_from" id="process_from" value="<?=$process_from?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=$process_id?>">
        <input type="hidden" name="completed_process" id="completed_process" value="<?=$completed_process?>">
        <input type="hidden" name="move_from" id="move_from" value="<?=!empty($trans_type)?$trans_type:1?>"> 

        <div class="col-md-4 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
        
        <div class="col-md-4 form-group">
            <label for="qty"> Qty</label>
            <input type="text" id="qty" name="qty" class="form-control numericOnly req qtyCal" value="">
            <input type="hidden" id="wt_nos" name="wt_nos" class="form-control floatOnly req" value="">
        </div>
        <div class="col-md-4 form-group">
             <label for="next_process_id"> Next Process</label>
            <select name="next_process_id" id="next_process_id" class="form-control select2 req">
               
                <option value="">Select Process</option>
                <?php
                if(!empty($processList)){
                    foreach($processList AS $row){
                        if($row->process_id != $process_id && (!in_array($row->process_id,explode(",",$completed_process)) OR $trans_type == 2)){
                            echo '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                        }
                    }
                    echo '<option value="0">Production Finish</option>';
                }
                ?>
            </select>
        </div>
        
        <div class="col-md-12 form-group remarkDiv">
            <label for="remark">Remark</label>
            <div class="input-group">
                <input type="text" name="remark" id="remark" class="form-control" value="">
                <div class="input-group-append">
                    <?php
                        $param = "{'formId':'addPrcMovement','fnsave':'savePRCMovement','res_function':'getPrcMovementResponse','controller':'sopDesk'}";
                    ?>
                    <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right btn-block" onclick="customStore(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='movementTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th>Send To</th>
                        <!--<th>Processor</th>-->
                        <th>Qty.</th>
                        <!--<th>Weight Per Nos</th>-->
                        <th>Remark</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="movementTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    $('.storeList').hide();
    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'completed_process':$("#completed_process").val(),'trans_type':$("#move_from").val()},'table_id':"movementTransTable",'tbody_id':'movementTbodyData','tfoot_id':'','fnget':'getPRCMovementHtml'};
        getPRCMovementHtml(postData);
        tbodyData = true;
    }
});
function getPrcMovementResponse(data,formId="addPrcMovement"){ 
    if(data.status==1){
        if(formId){
            $('#'+formId)[0].reset();
        }
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'completed_process':$("#completed_process").val(),'trans_type':$("#move_from").val()},'table_id':"movementTransTable",'tbody_id':'movementTbodyData','tfoot_id':'','fnget':'getPRCMovementHtml'};
        getPRCMovementHtml(postData);
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