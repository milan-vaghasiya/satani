<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid mt-4">
        <form action="" method="post" id="addRejection">
            <input type="hidden" id="decision_type" name="decision_type" value="1">
            <input type="hidden" id="source" name="source" value="MFG">

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
                    <button type="button" class="btn btn-success btn-save save-form" onclick="store({'formId':'addRejection','fnsave':'saveReview','controller':'bulkRejection','txt_editor':'','form_close':''});"><i class="fa fa-check"></i> Save</button>
                </div>		
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='cftTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
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
</script>