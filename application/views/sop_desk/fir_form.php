
<form data-res_function="getPrcLogResponse">
    <div class="col-md-12">
        <div class="row">
            <div class="card">
                <div class="media align-items-center btn-group process-tags">
                    <span class="badge bg-light-peach btn flex-fill"><?=$dataRow->item_code.' '.$dataRow->item_name?></span>
                    <span class="badge bg-light-cream btn flex-fill" id="pending_log_qty">Pending Qty :  </span>
                </div>                                       
            </div>
            <div class="row">
                <input type="hidden" name="id" id="id" value="" />
                <input type="hidden" name="prc_id" id="prc_id" value="<?=(!empty($prc_id))?$prc_id:""?>" />
                <input type="hidden" name="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>" />
                <input type="hidden" name="trans_number" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:$trans_number?>" />
                <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:""?>" />
                <input type="hidden" name="prc_number" id="prc_number" value="<?=(!empty($dataRow->prc_number))?$dataRow->prc_number:""?>" />
                <input type="hidden" name="report_type" id="report_type" value="2" />
                <input type="hidden" name="trans_type" id="trans_type" value="<?=!empty($trans_type)?$trans_type:1?>">
                <input type="hidden" name="process_from" id="process_from" value="<?=!empty($process_from)?$process_from:0?>">
                <input type="hidden" name="process_id" id="process_id" value="<?=$process_id?>">
                <input type="hidden" name="completed_process" id="completed_process" value="<?=$completed_process?>">
                <input type="hidden" name="process_by" id="process_by" value="1">
                <!-- <input type="hidden" name="rev_no" id="rev_no" value="<?=(!empty($dataRow->rev_no))?$dataRow->rev_no:""?>"> -->


                <?php $sample_size = 5?>
                <div class="col-md-3 form-group">
                    <label for="trans_date">Date</label>
                    <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=date('Y-m-d')?>" />
                </div>  
                <div class="col-md-3 form-group">
                    <label for="ok_qty">Ok Qty</label>
                    <input type="text" name="ok_qty" id="ok_qty" class="form-control floatonly req" value="0" />
                <div class="error production_qty"></div>

                </div>   
                <div class="col-md-3 form-group">
                    <label for="rej_found">Reject Found</label>
                    <input type="text" name="rej_found" id="rej_found" class="form-control floatonly req" value="0" />
                </div>    
                 
                <div class="col-md-3 form-group">  
                    <label for="sampling_qty">Sampling Qty.</label>
                    <div class="input-group">
                        <input type="text" name="sampling_qty" id="sampling_qty" class="form-control floatOnly req" value="<?=$sample_size?>" />
                        <button type="button" class="btn waves-effect waves-light btn-success float-center loaddata" title="Load Data">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div> 
                <div class="table-responsive">
                    <table id="preDispatchtbl" class="table table-bordered generalTable excelTable">
                        <thead class="thead-info" id="theadData">
                            <tr style="text-align:center;">
                                <th rowspan="2" style="width:5%;">#</th>
                                <th rowspan="2" style="width:20%">Parameter</th>
                                <th rowspan="2" style="width:20%">Specification</th>
                                <th rowspan="2" style="width:10%">Instrument</th>
                                <th colspan="<?= $sample_size?>">Observation on Samples</th>
                                <th rowspan="2" style="width:5%">Result</th>
                            </tr>
                            <tr style="text-align:center;">
                                <?php for($j=1;$j<=$sample_size;$j++):?> 
                                    <th><?= $j ?></th>
                                <?php endfor;?>    
                            </tr>
                        </thead>
                        <tbody id="tbodyData">
                        </tbody>
                    </table>
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
                        <th style="min-width:100px">Date</th>
                        <th>Sample Qty</th>
                        <th>OK Qty.</th>
                        <th>Rejection Qty.</th>
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
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val(),'process_from':$("#process_from").val(),'trans_type':$("#trans_type").val(),'completed_process':$("#completed_process").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getPRCLogHtml','controller':'sopDesk'};
        getPRCLogHtml(postData);
        tbodyData = true;
    }

   

    setTimeout(function(){ $('.loaddata').trigger('click');}, 500);
    $(document).on('click', '.loaddata', function(e) { 
        e.stopImmediatePropagation();e.preventDefault();
        var item_id = $('#item_id').val();
        var sampling_qty = $("#sampling_qty").val()||5;
        $.ajax({
            url: base_url + 'sopDesk/getFinalInspectionData',
            data: {
                item_id: item_id,
                sampling_qty:sampling_qty,
            },
            type: "POST",
            dataType: 'json',
            success: function(data) {
                $("#theadData").html(data.theadData);
                $("#tbodyData").html(data.tbodyData);
            }
        });
    });

});
function getPrcLogResponse(data,formId="firInsp"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'prc_id':$("#prc_id").val(),'process_id':$("#process_id").val(),'process_by':$("#process_by").val(),'ref_id':$("#ref_id").val(),'ref_trans_id':$("#ref_trans_id").val(),'process_from':$("#process_from").val(),'trans_type':$("#trans_type").val(),'completed_process':$("#completed_process").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getPRCLogHtml','controller':'sopDesk'};
        getPRCLogHtml(postData);
        currLoc = $(location).prop('href');
        if (currLoc.indexOf('/sopDesk/productionLog/') > 0) { 
			initTable();
		}
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