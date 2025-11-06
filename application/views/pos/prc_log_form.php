<form data-res_function="getPrcLogResponse"> <!-- onsubmit="return false;">-->
    <input type="hidden" name="created_by" value="<?=$operator_id?>">
    <div class="col-md-12 form-group">													
        <label><i class="fa fa-qrcode text-primary"></i> SCAN QR CODE</label>
        
        <input type="text" id="scan_qr_log" value="" class="form-control"  style="background:#93d2ff;color:#000000;font-weight:bold;" placeholder="SCAN QR CODE" autocomplete="off">
    </div>
    <hr>
    <div class="error general_error"></div>
    <div class="row" id="logHtml">

    </div>
</form>
<script>
    $(document).ready(function(){
        setTimeout(function(){ $('#scan_qr_log').focus(); }, 1000)
        /** LOAD SCANNED(QR) ITEM ON ENTER KEY */
        $(document).on('keypress','#scan_qr_log',function(e){ 
            if(e.which == 13) {
                var scan_id = $("#scan_qr_log").val();
                if(scan_id){
                    $.ajax({
                        type: "POST",
                        url: base_url + 'pos/getPrcLogDetail',
                        data:{scan_id:scan_id},
                        dataType:'json'
                    }).done(function (response) {
                        $("#logHtml").html(response.html);
                        initSelect2();		setPlaceHolder();
                    });
                    // $('#scan_qr_log').val('');
                }
            }
        });


        $(document).on("change keyup",".qtyCal", function(){
            var rej_qty = ($("#rej_found").val() !='')?$("#rej_found").val():0;
            var production_qty = ($("#production_qty").val() !='')?$("#production_qty").val():0;
            if(production_qty == 0){$("#production_qty").val(rej_qty)}
            var without_process_qty = $("#without_process_qty").val()|| 0;
            var okQty=parseFloat($("#production_qty").val())-(parseFloat(rej_qty) + parseFloat(without_process_qty));
        
            $("#ok_qty").val(okQty);
        });
        
         $(document).on('change','#process_by',function(){
    		var process_by = $(this).val();
            if(process_by && process_by != 3)
            {		
                $.ajax({
                    url:base_url + controller + "/getProcessorList",
                    type:'post',
                    data:{process_by:process_by}, 
                    dataType:'json',
                    success:function(data){
                        $("#processor_id").html("");
                        $("#processor_id").html(data.options);
                    }
                });
            }
        });
    });
</script>