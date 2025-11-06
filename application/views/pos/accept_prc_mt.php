<form data-res_function="getPrcAcceptResponse">
    <input type="hidden" name="created_by" value="<?=$operator_id?>">
    <div class="col-md-12 form-group">													
        <label><i class="fa fa-qrcode text-primary"></i> SCAN QR CODE</label>
        <input type="text" id="scan_qr" value="" class="form-control" style="background:#93d2ff;color:#000000;font-weight:bold;" placeholder="SCAN QR CODE" autocomplete="off">
        <div class="error scan_qr"></div>
    </div>
    <hr>
    <div class="error general_error"></div>
    <div class="row" id="acceptHtml">

    </div>
</form>
<script>
    
$(document).ready(function(){
    setTimeout(function(){  $('#scan_qr').focus(); }, 1000);
	
    /** LOAD SCANNED(QR) ITEM ON ENTER KEY */
	$(document).on('keypress','#scan_qr',function(e){ 
        // e.stopImmediatePropagation();e.preventDefault();
		if(e.which == 13) {
			var scan_id = $(this).val();
            if(scan_id){
                $.ajax({
                    type: "POST",
                    url: base_url + 'pos/getAcceptDetail',
                    data:{scan_id:scan_id},
                    dataType:'json'
                }).done(function (response) {
                    $("#acceptHtml").html(response.html);
                });
                $('#scan_qr').val('');
            }
		}
    });
});
</script>