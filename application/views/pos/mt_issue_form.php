<form data-res_function="getMaterialIssueResponse">
    <input type="hidden" name="created_by" value="<?=$operator_id?>">
    <div class="col-md-12 form-group">													
        <label><i class="fa fa-qrcode text-primary"></i> SCAN QR CODE</label>
        <input type="text" id="issue_qr" value="" class="form-control" style="background:#93d2ff;color:#000000;font-weight:bold;" placeholder="SCAN QR CODE" autocomplete="off">
        <div class="error issue_qr"></div>
    </div>
    <hr>
    <div class="error general_error"></div>
    <div class="row" id="issuehtml">

    </div>
</form>
<script>
    
$(document).ready(function(){
    setTimeout(function(){  $('#issue_qr').focus(); }, 1000);
	
    /** LOAD SCANNED(QR) ITEM ON ENTER KEY */
	$(document).on('keypress','#issue_qr',function(e){ 
        // e.stopImmediatePropagation();e.preventDefault();
		if(e.which == 13) {
			var scan_id = $(this).val();
            if(scan_id){
                $.ajax({
                    type: "POST",
                    url: base_url + 'pos/getMaterialIssueDetail',
                    data:{scan_id:scan_id},
                    dataType:'json'
                }).done(function (response) {
                    $("#issuehtml").html(response.html);
                    initSelect2();		setPlaceHolder();
                });
                $('#issue_qr').val('');
            }
		}
    });
});
</script>