<form class="itemMasterForm" enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <div class="col-md-3 form-group">
                <label for="trans_date">Date</label>
                <input type="date" id="trans_date" name="trans_date" class="form-control " value="<?=date("Y-m-d")?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="trans_number">W.O. No.</label>
                <input type="text" id="trans_number" name="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number)?$dataRow->trans_number:$trans_number)?>" readOnly>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 form-group">
                <label for="item_id">Product</label>
                <select name="item_id" id="item_id" class="form-control select2 req ">
                    <option value="0">Select</option>
                    <?php
                    if(!empty($itemList)){
                        foreach ($itemList as $row) :
                            echo '<option value="' . $row->id . '">'.$row->item_code.' '.$row->item_name . '</option>';
                        endforeach;
                    } 
                    ?>
                </select>
            </div> 
            <div class="col-md-5 form-group">
                <label for="tool_method">Tool Method</label>
                <select name="tool_method" id="tool_method" class="form-control select2 req">
                    <option value="0">Select</option>
                </select>
            </div> 
            <div class="col-md-2 form-group">
                <button type="button" class="btn waves-effect waves-light btn-info float-left loadDieList mt-20 btn-block" data-type="1" title="Load Data">
                    <i class="fas fa-sync-alt"></i> Load
                </button>
            </div>
            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th>#</th>
                                <th>Die</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody id="dieTbody">
                            <tr>
                                <th colspan="3" class="text-center">No data available</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="error general_error"></div>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on('change',"#item_id",function(e){
            e.stopImmediatePropagation();e.preventDefault();
            var item_id = $("#item_id").val();
            $("#dieTbody").html('<tr><th colspan="3" class="text-center">No data available</th></tr>');
            if(item_id){
                $.ajax({
                    url : base_url + controller + '/getToolMethod',
                    type : 'post',
                    data : {item_id:item_id},
                    dataType : 'json'
                }).done(function(response){
                    $("#tool_method").html(response.options);
                    $("#tool_method").select2();
                });
            }
        });
        $(document).on('change',"#tool_method",function(e){
            e.stopImmediatePropagation();e.preventDefault();
            $("#dieTbody").html('<tr><th colspan="3" class="text-center">No data available</th></tr>');
        });

        $(document).on('click',".loadDieList",function(e){
            e.stopImmediatePropagation();e.preventDefault();
            var item_id = $("#item_id").val();
            var tool_method = $("#tool_method").val();
            var valid = true;
            if(item_id == ""){ $(".item_id").html("Item required"); valid = false; }
            if(tool_method == ""){ $(".tool_method").html("Tool method required"); valid = false; }
            $("#dieTbody").html("");
            if(valid){
                $.ajax({
                    url : base_url + controller + '/getDieList',
                    type : 'post',
                    data : {item_id:item_id,tool_method:tool_method},
                    dataType : 'json'
                }).done(function(response){
                    $("#dieTbody").html(response.tbodyData);
                });
            }
        });
    });
</script>