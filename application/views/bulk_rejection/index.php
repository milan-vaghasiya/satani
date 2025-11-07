<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid mt-4">
        <form action="" method="post" id="addRejection" data-res_function="bulkRejectionRes">
            <div class="row">
                <div class="col-sm-4">
                    <label for="rr_reason">Rejection Reason</label>
                    <select id="rr_reason" name="rr_reason" class="form-control select2 req">
                        <option value="">Select Reason</option>
                        <?php
                            foreach ($rejectionComments as $row){
                                $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                                echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';
                            }
                        ?>
                    </select>
                </div>	
                <div class="col-sm-2">
                    <label>&nbsp;</label><br>
                    <button type="button" class="btn btn-success btn-save save-form"  onclick="customStore({'formId':'addRejection','fnsave':'saveReview'});"><i class="fa fa-check"></i> Save</button>
                </div>	                
                <button type="button" class="refreshReportData loadData d-none"></button>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body reportDiv">
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered">
                                    <thead id="theadData">                                                                         
                                        <tr>
                                            <th>#</th>
                                            <th><input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRejection"><label for="masterSelect">ALL</label></th>
                                            <th>PRC No.</th>
                                            <th>Product</th>
                                            <th>Date</th>
                                            <th>Process</th>
                                            <th>Machine/Vendor</th>
                                            <th>Operator/Inspector</th>
                                            <th>Qty</th>
                                            <th>Reviewed Qty</th>
                                            <th>Pending Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<script>
    $(document).ready(function() {
        reportTable();
        setTimeout(function(){$(".loadData").trigger('click');},500);
    
        $(document).on('click','.loadData',function(e){
            $(".error").html("");
            $.ajax({
                url: base_url + controller + '/getRejectionReviewRows',
                data: {},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
                    $("#tbodyData").html(data.tbody);
                    reportTable();
                }
            });
        });   
        
        $(document).on('change', '.BulkRejection', function() {
            if ($(this).attr('id') == "masterSelect") {
                if ($(this).prop('checked') == true) {
                    $("input[name='log_id[]']").prop('checked', true);
                    $('.qty_input').attr('disabled', false);
                } else {
                    $("input[name='log_id[]']").prop('checked', false);
                    $('.qty_input').attr('disabled', true);
                }
            }
            else {
                if ($("input[name='log_id[]']").not(':checked').length != $("input[name='log_id[]']").length) {
                    $("#masterSelect").prop('checked', false);
                }
                if ($("input[name='log_id[]']:checked").length == $("input[name='log_id[]']").length) {
                    $("#masterSelect").prop('checked', true);
                }
                else{
                    $("#masterSelect").prop('checked', false);
                }

                if ($(this).is(':checked')) {
                    $(this).closest('td').nextAll('td').find('.qty_input').attr('disabled', false);
                }else{
                    $(this).closest('td').nextAll('td').find('.qty_input').attr('disabled', true);
                }
            }
        });
    });

    function bulkRejectionRes(data){
        if(data.status==1){
            Swal.fire({ icon: 'success', title: data.message});

            setTimeout(() => {
                window.location.reload();
            }, 1000);
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