<div class="card">
    <div class="media align-items-center btn-group process-tags">
        <span class="badge bg-light-peach btn flex-fill">Die : <?=$dataRow->die_code?></span>
        <span class="badge bg-light-cream btn flex-fill" >Wo No :  <?=$dataRow->trans_number?></span>
    </div>                                       
</div>
<form data-res_function="getDieChallanResponse">
    <div class="row">
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="die_id" id="die_id" value="<?=$dataRow->die_id?>">
        <input type="hidden" name="wo_id" id="wo_id" value="<?=$dataRow->id?>">
       
        <div class="col-md-3 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
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
        <div class="col-md-3 form_group">
            <label for="qty">qty</label>
            <input type="text" name="qty" id="qty" class="form-control numericOnly req" value="" max="">
        </div>
        <div class="col-md-12 form-group">
             <?php
                $param = "{'formId':'addChallanRequest','fnsave':'saveChallanRequest','controller':'dieProduction'}";
            ?>
            <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" ><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</form>

<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='chReqTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th style="min-width:100px">Process</th>
                        <th style="min-width:100px">Qty</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="chTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'wo_id':$("#wo_id").val()},'table_id':"chReqTable",'tbody_id':'chTbodyData','tfoot_id':'','fnget':'getChallanReqHtml','controller':'dieProduction'};
        getTransHtml(postData);
        tbodyData = true;
    }

});
function getDieChallanResponse(data,formId="addChallanRequest"){ 
    if(data.status==1){
        if(formId){ $('#'+formId)[0].reset(); }
        
        var postData = {'postData':{'wo_id':$("#wo_id").val()},'table_id':"chReqTable",'tbody_id':'chTbodyData','tfoot_id':'','fnget':'getChallanReqHtml','controller':'dieProduction'};
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