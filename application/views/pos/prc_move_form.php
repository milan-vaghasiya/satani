<form data-res_function="getPrcLogResponse" id="moveForm"> <!-- onsubmit="return false;">-->
    
    <div class="col-md-12 form-group">													
        <label><i class="fa fa-qrcode text-primary"></i> SCAN QR CODE</label>
        <input type="text" id="scan_qr_move" value="" class="form-control" style="background:#93d2ff;color:#000000;font-weight:bold;" placeholder="SCAN QR CODE" autocomplete="off">
    </div>
    <input type="hidden" name="created_by" value="<?=$operator_id?>">
    <hr>
    <div class="error general_error"></div>
    <div class="row" id="moveHtml">

    </div>
</form>
<script>
    $(document).ready(function(){
        setTimeout(function(){ $('#scan_qr_move').focus(); }, 1000);
    /** LOAD SCANNED(QR) ITEM ON ENTER KEY */
        $(document).on('keypress','#scan_qr_move',function(e){ 
           
            if(e.which == 13) {
                var scan_id = $("#scan_qr_move").val();
                if(scan_id){
                    $.ajax({
                        type: "POST",
                        url: base_url + 'pos/getPrcMoveDetail',
                        data:{scan_id:scan_id},
                        dataType:'json'
                    }).done(function (response) {
                        $("#moveHtml").html(response.html);
                    });
                    $('#scan_qr_move').val('');
                }
            }
        });

        $(document).on("change keyup",".qtyCal", function(){
            var rej_qty = ($("#rej_found").val() !='')?$("#rej_found").val():0;
            var without_process_qty = $("#without_process_qty").val()|| 0;

            var okQty=parseFloat($("#production_qty").val())-(parseFloat(rej_qty) + parseFloat(without_process_qty));
        
            $("#ok_qty").val(okQty);
        });
    });
</script>