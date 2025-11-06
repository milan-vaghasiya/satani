<form data-res_function="getPrcLogResponse">
    
    <div class="row">
        <input type="hidden" name="prc_id" id="prc_id" value="<?=$prc_id?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=$process_id?>">
        <input type="hidden" name="process_from" id="process_from" value="<?=$process_from?>">
        <input type="hidden" name="completed_process" id="completed_process" value="<?=$completed_process?>">
        <input type="hidden" name="ref_id" id="ref_id" value="<?=!empty($challan_id)?$challan_id:0?>">
        <input type="hidden" name="ref_trans_id" id="ref_trans_id" value="<?=!empty($ref_trans_id)?$ref_trans_id:0?>">
        <input type="hidden" name="wt_nos" id="wt_nos" value="<?=$wt_nos?>">
        <input type="hidden" name="process_by" id="process_by" value="<?=$process_by?>">
        <input type="hidden" name="processor_id" id="processor_id" value="<?=$processor_id?>">
        <input type="hidden" name="challan_process" id="challan_process" value="<?=$challan_process?>">
        <div class="error die_error"></div>
        <div class="col-md-4 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
        <div class="col-md-4 form-group">
            <label for="in_challan_no">In Challan No</label>
            <input type="text" name="in_challan_no" id="in_challan_no" class="form-control req">
        </div>
        <div class="col-md-4 form-group">
            <label for="challan_file">Challan File</label>
            <input type="file" name="challan_file" id="challan_file" class="form-control">
        </div>
       
        <div class="col-md-12 form-group">
            <div class="table-responsive">
                <table class="table mb-0 table-borderless" >
                    <thead>
                        <tr>
                            <th>Process</th>
                            <th>OK Qty.</th>
                            <th>Rejection Qty.</th>
                            <th>Without Process</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $processArray = explode(",",$challan_process);
                        $processMaster = array_reduce($processList, function($processMaster, $process) { 
                                $processMaster[$process->id] = $process; 
                                return $processMaster; 
                            }, []);
                        foreach($processArray AS $process){ ?>
                            <tr>
                                <td><?=$processMaster[$process]->process_name?></td>
                                <td>
                                    <input type = "text" class="form-control" name="ok_qty[]">
                                    <input type="hidden" name="process_id[]" value="<?=$process?>">
                                    <div class="error ok_qty<?=$process?>">
                                </td>
                                <td>
                                    <input type = "text" class="form-control" name="rej_found[]">
                                </td>
                                <td>
                                    <input type = "text" class="form-control" name="without_process_qty[]">
                                </td>
                            </tr><?php
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12 form-group">
            <?php $param = "{'formId':'addLog','fnsave':'saveLog','res_function':'getPrcLogResponse','controller':'outsource'}";  ?>
            <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
        </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='logTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th>In Challan No</th>
                        <th style="min-width:100px">Date</th>
                        <th>Process</th>
                        <th>OK Qty.</th>
                        <th>Rejection Qty.</th>
						<th>Without Process Return</th>
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
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'prc_process_id':$("#prc_process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val(),'inputWt':$("#inputWt").val(),'challan_process':$("#challan_process").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getReceiveLogHtml','controller':'outsource'};

        getPRCLogHtml(postData);
        tbodyData = true;
    }
});
function getPrcLogResponse(data,formId="addLog"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'prc_process_id':$("#prc_process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val(),'inputWt':$("#inputWt").val(),'challan_process':$("#challan_process").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getReceiveLogHtml','controller':'outsource'};
        getPRCLogHtml(postData);
        currLoc = $(location).prop('href');
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