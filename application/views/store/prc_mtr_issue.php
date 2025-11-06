<form>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id)?$dataRow->id:'')?>">
                <input type="hidden" name="trans_no" value="<?= (!empty($dataRow->trans_no)?$dataRow->trans_no:$trans_no) ?>"  />
               
                <h6 id="item_name" class="text-primary"></h6>
                <div class="col-md-2 form-group">
                    <label for="trans_number">Trans No.</label>
                    <input type="text" name="trans_number" class="form-control" value="<?= (!empty($dataRow->trans_number)?$dataRow->trans_number:$trans_number) ?>" readOnly />
                </div>
                <div class="col-md-5 form-group">
                    <label for="item_id">Item</label>
                    <select name="item_id" id="item_id" class="form-control select2 req">
                        <option value="">Select Item</option>
                        <?php
                            if(!empty($itemList)){
                                foreach ($itemList as $row) {
                                    $selected = ((!empty($dataRow->item_id) && $dataRow->item_id == $row->id)?'selected':'');
                                    echo "<option value='".$row->id."' ".$selected.">".$row->item_code.' '.$row->item_name."</option>";
                                }
                            }
                        ?>
                    </select>
                    <div class="error item_err"></div>
                </div>
                <?php if(!empty($dataRow->id)) {
                    ?>
                    <input type="hidden" name="issue_to" id="issue_to" value="<?=$dataRow->issue_to?>">
                    <?php
                }else{
                    ?>
                    <div class="col-md-5 form-group">
                        <label for="issue_to">Issued To</label>
                        <select name="issue_to" id="issue_to" class="form-control select2 req">
                            <option value="">Select Issued To</option>
                            <?php
                                if(!empty($empData)){
                                    foreach ($empData as $row) {
                                        echo "<option value='".$row->id."'>".$row->emp_name."</option>";
                                    }
                                }
                            ?>
                        </select>
                        <div class="error item_err"></div>
                    </div>
                    <?php
                }
                ?>
                <div class="col-md-12 form-group">
                    <label for="remark">Remark</label>
                    <textarea name="remark" id="remark" class="form-control" rows="2"><?=(!empty($dataRow->remark)?$dataRow->remark:'')?></textarea>
                </div>
            </div>
            
        </div>
        <div class="col-md-12 form-group mt-4">
            <div class="error general_batch_no"></div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <th>Location</th>
                        <th>Batch No.</th>
                        <th>Stock Qty.</th>
                        <th>Issue Qty.</th>
                    </thead>
                    <tbody id="tbodyData">
                        <tr>
                            <th colspan="4" class="text-center">No data available</th>
                        </tr>
                    </tbody>
                </table>
                <div class="error table_err"></div>
            </div>
        </div>
    </div>
</form>
<script>
    var tbodyData = false;
    $(document).ready(function(){
        setTimeout(function(){ 
            $('#item_id').trigger('change');	
        }, 500);
        $(document).on('change', '#item_id', function (e) {
            e.stopImmediatePropagation();e.preventDefault();
            var item_id = $(this).val();
			if(item_id){
				$.ajax({
					url:base_url + controller + "/getBatchWiseStock",
					type:'post',
					data:{item_id:item_id},
					dataType:'json',
					success:function(data){
						if(data.status == 1){
							$('#tbodyData').html('');
							$('#tbodyData').html(data.tbodyData);
						}
					}
				});
			}
        });
    });


function storeIssueMaterial(postData){
    setPlaceHolder();
    var formId = postData.formId;
    var fnsave = postData.fnsave || "save";
    var controllerName = postData.controller || controller;

    var form = $('#'+formId)[0];
    var fd = new FormData(form);
    $.ajax({
        url: base_url + controllerName + '/' + fnsave,
        data:fd,
        type: "POST",
        processData:false,
        contentType:false,
        dataType:"json",
    }).done(function(data){
        if(data.status==1){
            initTable(); 
            $("#item_id").val("");
            $("#item_name").html("");
            $("#required_qty").val("");
            $("#emp_dept_id").val("");
            $("#issued_to").val("");
            $("#tbodyData").html('<tr><th colspan="5" class="text-center">No Data Available</th></tr>');
            initSelect2();	
            Swal.fire({ icon: 'success', title: data.message});
           
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});
            }else{
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }				
    });
}
</script>