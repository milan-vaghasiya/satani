<form data-res_function="getPrcLogResponse">
    <div class="card">
        <div class="media align-items-center btn-group process-tags">
			<span class="badge bg-light-sky btn flex-fill">Movement Type :  <?= $movement_type;?></span>
            <span class="badge bg-light-cream btn flex-fill">PRC No :  <?= $prc_number;?></span>
            <span class="badge bg-light-sky btn flex-fill">Item :  <?= $item_name;?></span>
            <span class="badge bg-light-cream btn flex-fill" id="pending_log_qty">Pending Qty :  </span>
        </div>                                       
    </div>
    <div class="row">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$prc_id?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=$process_id?>">
        <input type="hidden" name="completed_process" id="completed_process" value="<?=$completed_process?>">
        <input type="hidden" name="process_from" id="process_from" value="<?=$process_from?>">
        <input type="hidden" name="ref_id" id="ref_id" value="<?=!empty($challan_id)?$challan_id:0?>">
        <input type="hidden" name="trans_type" id="trans_type" value="<?=!empty($trans_type)?$trans_type:1?>">
        <input type="hidden" name="ref_trans_id" id="ref_trans_id" value="<?=!empty($ref_trans_id)?$ref_trans_id:0?>">
        <input type="hidden"  id="inputWt" value="<?=!empty($inputDiv)?$inputDiv:0?>">
        
        <div class="col-md-3 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
        
        <div class="col-md-3 form-group">
            <label for="production_qty">Production Qty</label>
            <input type="text" id="production_qty" class="form-control numericOnly req qtyCal" value="">

        </div>
        <div class="col-md-3 form-group">
            <label for="ok_qty">Ok Qty</label>
            <input type="text" name="ok_qty" id="ok_qty" class="form-control numericOnly req " value="" readonly>
            <div class="error batch_stock_error"></div>
        </div>
        <div class="col-md-3 form-group" >
            <label for="rej_found">Rejection Qty</label>
            <input type="text" name="rej_found" id="rej_found" class="form-control numericOnly qtyCal">
        </div>
       
        <?php
        if(!empty($process_by) && $process_by == 3){
            ?>
            <div class="col-md-3 form-group">
               <label for="without_process_qty">Without Process Return</label>
               <input type="text" name="without_process_qty" id="without_process_qty" class="form-control numericOnly qtyCal">
            </div>
            <div class="col-md-3 form-group">
               <label for="in_challan_no">In Challan No</label>
               <input type="text" name="in_challan_no" id="in_challan_no" class="form-control">
           </div>
           <input type="hidden" name="process_by" id="process_by" value="<?=$process_by?>">
           <input type="hidden" name="processor_id" id="processor_id" value="<?=$processor_id?>">
           <?php
           if(!empty($inputDiv)){
            ?>
             <input type="hidden" name="wt_nos" id="wt_nos" value="<?=$wt_nos?>">
            <?php
           }
        }else{
            ?>
           
            <div class="col-md-4 form-group">
                <label for="process_by">Process By</label>
                <select name="process_by" id="process_by" class="form-control select2">
                    <option value="1">Machine Process</option>
                    <option value="2">Department Process</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="processor_id">Machine/Dept.</label>
                <select name="processor_id" id="processor_id" class="form-control select2">
                    <option value="0">Select</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
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
            <?php
            if($processData->process_type == 2){
                ?>
                <div class="col-md-3 form-group">
                    <label for="wt_nos">Input Weight</label>
                    <input type="text" class="form-control floatOnly" name="wt_nos" id="wt_nos" value="<?=!empty($wt_nos)?$wt_nos:''?>">
                </div>
                
                <?php
            }
        }
        ?>
        <div class="col-md-4 form-group">
            <label for="challan_file">Challan File</label>
            <input type="file" name="challan_file" id="challan_file" class="form-control">
        </div>
        <div class="col-md-12 form-group">
            <label for="remark">Remark</label>
            <div class="input-group">
                <input type="text" name="remark" id="remark" class="form-control" value="">
                <div class="input-group-append">
                    <?php
                        $param = "{'formId':'addPrcLog','fnsave':'savePRCLog','res_function':'getPrcLogResponse','controller':'sopDesk'}";
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
            <table id='logTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Type</th>
                        <th style="min-width:100px">Date</th>
                        <th >Production Time</th>
                        <th <?=empty($inputDiv)?'hidden':''?>>Input Weight</th>
                        <th>Department/Machine/ Vendor</th>
                        <th>OK Qty.</th>
                        <th>Rejection Qty.</th> 
                        <?php  if(!empty($process_by) && $process_by == 3){ ?>
                            <th>Without Process Return</th>
                            <th>In Challan No</th>
                        <?php  }else{ ?>
                            <th>Operator</th>
                            <?php
                        }
                        ?>
                        <th>Remark</th>
                        <th style="width:50px;">Action</th>
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
    setTimeout(function(){ $('#process_by').trigger('change'); }, 50);

    if(!tbodyData){
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'completed_process':$("#completed_process").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val(),'inputWt':$("#inputWt").val(),'trans_type':$("#trans_type").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getPRCLogHtml','controller':'sopDesk'};
        getPRCLogHtml(postData);
        tbodyData = true;
    }

    $(document).on("change keyup",".qtyCal", function(){
        var rej_qty = ($("#rej_found").val() !='')?$("#rej_found").val():0;
        var without_process_qty = $("#without_process_qty").val()|| 0;

		var okQty=parseFloat($("#production_qty").val())-(parseFloat(rej_qty) + parseFloat(without_process_qty));
      
		$("#ok_qty").val(okQty);
    });

    $(document).on('change','#process_by',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var process_by = $(this).val();
        if(process_by && process_by != 3)
        {		
            $.ajax({
                url:base_url  + "sopDesk/getProcessorList",
                type:'post',
                data:{process_by:process_by}, 
                dataType:'json',
                success:function(data){
                    $("#processor_id").html("");
                    $("#processor_id").html(data.options);
                }
            });
        }
    });
});
function getPrcLogResponse(data,formId="addPrcLog"){ 
    if(data.status==1){
		initTable();
        $('#'+formId)[0].reset();
       var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'completed_process':$("#completed_process").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val(),'inputWt':$("#inputWt").val(),'trans_type':$("#trans_type").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getPRCLogHtml','controller':'sopDesk'};
        getPRCLogHtml(postData);
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