<!DOCTYPE html>
<html lang="en">
<?php
    $hdrFile = 'headerfiles';
    $this->load->view('includes/'.$hdrFile);
?>


<body id="body" class="auth-page" style="background-image: url('<?=base_url()?>/assets/images/p-1.png'); background-size: cover; background-position: center center;">
<?php if(empty($operator_id)): ?> 
    <div class="left-sidebar" style="min-width:300px !important">
        <div class="brand">
            <a href="javascript:void(0);" class="logo">
                <span>
                    <img src="<?=base_url()?>assets/images/logo_text.png" alt="logo-large" class="logo-lg logo-light" style="height:70px;">
                    <img src="<?=base_url()?>assets/images/logo_text.png" alt="logo-large" class="logo-lg logo-dark"  style="height:70px;">
                </span>
            </a>
        </div>
        <div class="col-12 align-self-center">
            <div class="card-body">
                <div class="row">
                    <form class="my-4"action="<?=base_url('posDesk/authOperator');?>" method="post">  
                        <div class="form-group mb-2">
                            <label class="form-label" for="access_token">Scan QR Code</label>          
                            <input type="text" name="access_token" id="access_token" class="form-control" placeholder="Scanqrcode" aria-label="Scanqrcode" aria-describedby="basic-addon1">                  
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label" for="user_name">Username</label>          
                            <input type="text" name="user_name" id="user_name" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">                  
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label" for="password">Password</label>                                            
                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password">      
                        </div>                      

                        <div class="form-group mb-0 row">
                            <div class="col-12">
                                <div class="d-grid mt-3">
                                    <button class="btn btn-primary" type="submit">Log In <i class="fas fa-sign-in-alt ms-1"></i></button>
                                </div>
                            </div> 
                        </div>                      
                    </form>
                </div>   
            </div>
        </div>     
    </div>
    <div class="container-md" style="margin-left:300px;max-width:80%!important;">
<?php else : ?>
    <!-- Navbar -->
    <nav class="navbar-custom" id="navbar-custom" style="margin-left:0px !important">    
        <ul class="list-unstyled topbar-nav float-end mb-0">
            <li class="dropdown">
                <div class="d-flex align-items-center">
                    <img src="<?=base_url()?>assets/images/users/user_default.png" alt="profile-user" class="rounded-circle me-2 thumb-sm" />
                    <div>
                        <small class="d-none d-md-block font-11"><?=$operatorData->designation_name?></small>
                        <span class="d-none d-md-block fw-semibold font-12"><?=$operatorData->emp_name?>
                    </div>
                </div>
            </li> 
        </ul>
        <ul class="list-unstyled topbar-nav mb-0">                        
            <li>
            <img src="<?=base_url()?>assets/images/logo_text.png" alt="logo-large" class="logo-lg logo-dark"  style="height:50px;">
            </li> 
        </ul>
    </nav>
    <!-- end navbar-->
    <div class="container-md">
<?php endif; ?>   
   <!-- Log In page -->
    
        <div class="row  d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3" hidden>
                            <div class="card">
                                <div class="card-body">
                                    <div class="pricingTable1 text-center">  
                                        <span class="badge bg-danger ml-auto a-animate-blink">Issue Material</span>                                      
                                        <img src="<?=base_url()?>/assets/images/accept_material.png" alt="" class="d-block mx-auto" height="150">
                                        
                                        <h6 class="title1 py-3 mt-2 mb-0">Issue Material</h6>
                                        <div class="mt-2">
                                            <div class="d-flex">
                                                <?php
                                                $issueParam = "{'postData':{'operator_id' : ".(!empty($operatorData->id)?$operatorData->id:'')."},'modal_id' : 'bs-right-md-modal', 'call_function':'materialIssue', 'form_id' : 'materialIssue', 'title' : 'Issue Material ', 'fnsave' : 'saveIssuedmaterial','js_store_fn' : 'customStore'}";
                                                $stockParam = "{'postData':{'operator_id' : ".(!empty($operatorData->id)?$operatorData->id:'')."},'modal_id' : 'bs-right-lg-modal', 'call_function':'stockTag', 'form_id' : 'stockTag', 'title' : 'Stock Detail & Tag ', 'button':'close'}";
                                                ?>
                                                <button type="button" class="btn btn-primary  mr-1" style="width:30%;" <?=empty($operatorData->id)?'disabled':''?> onclick="modalAction(<?=$issueParam ?>)"><span>Add</span></button>
                                                <button type="button" class="btn btn-success detailIssueMaterial mr-1" style="width:35%;" <?=empty($operatorData->id)?'disabled':''?>  data-operator_id = "<?=!empty($operatorData->id)?$operatorData->id:''?>"><span>Detail</span></button>
                                                <button type="button" class="btn btn-info   mr-1" style="width:35%;" <?=empty($operatorData->id)?'disabled':''?>  data-operator_id = "<?=!empty($operatorData->id)?$operatorData->id:''?>" onclick="modalAction(<?=$stockParam ?>)"><span>Stock</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="pricingTable1 text-center">  
                                            <span class="badge bg-warning ml-auto a-animate-blink">Accept Material</span>                                      
                                        <img src="<?=base_url()?>/assets/images/accept_material.png" alt="" class="d-block mx-auto" height="150">
                                        
                                        <h6 class="title1 py-3 mt-2 mb-0">Received Material</h6>
                                        <div class="mt-2">
                                            <div class="d-flex justify-content-center">
                                                <?php
                                                $acceptParam = "{'postData':{'operator_id' : ".(!empty($operatorData->id)?$operatorData->id:'')."},'modal_id' : 'bs-right-md-modal', 'call_function':'prcAccept', 'form_id' : 'addPrcAccept', 'title' : 'Accept Material ', 'fnsave' : 'saveAcceptedQty','js_store_fn' : 'customStore'}";
                                                ?>
                                                <button type="button" class="btn btn-primary" style="width:40%;" <?=empty($operatorData->id)?'disabled':''?> onclick="modalAction(<?=$acceptParam ?>)"><span>Add</span></button>
                                                <button type="button" class="btn btn-success detailAccept" style="width:40%;" <?=empty($operatorData->id)?'disabled':''?>  data-operator_id = "<?=!empty($operatorData->id)?$operatorData->id:''?>"><span>Detail</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="pricingTable1 text-center">  
                                        <span class="badge bg-info ml-auto a-animate-blink">Production Log</span>                                      
                                        <img src="<?=base_url()?>/assets/images/grn.png" alt="" class="d-block mx-auto" height="150">
                                        
                                        <h6 class="title1 py-3 mt-2 mb-0">Production Log</h6>
                                        <div class="mt-2">
                                            <div class="d-flex justify-content-center">
                                                <?php
                                                $logParam = "{'postData':{'operator_id' : ".(!empty($operatorData->id)?$operatorData->id:'')."},'modal_id' : 'bs-right-md-modal', 'call_function':'addPrcLog', 'form_id' : 'addPrcLog', 'title' : 'Production Log ', 'fnsave' : 'savePrcLog','js_store_fn' : 'customStore'}";
                                                ?>
                                                <button type="button" class="btn btn-primary" style="width:40%;" <?=empty($operatorData->id)?'disabled':''?> onclick="modalAction(<?=$logParam ?>)"><span>Add</span></button>
                                                <button type="button" class="btn btn-success  detailLog" style="width:40%;" <?=empty($operatorData->id)?'disabled':''?>  data-operator_id = "<?=!empty($operatorData->id)?$operatorData->id:''?>"><span>Detail</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="pricingTable1 text-center">  
                                            <span class="badge bg-primary ml-auto a-animate-blink">Movement</span>                                      
                                        <img src="<?=base_url()?>/assets/images/send_material.png" alt="" class="d-block mx-auto" height="150">
                                        
                                        <h6 class="title1 py-3 mt-2 mb-0">Send Material</h6>
                                        <div class="mt-2">
                                            <div class="d-flex justify-content-center">
                                            <?php
                                                $moveParam = "{'postData':{'operator_id' : ".(!empty($operatorData->id)?$operatorData->id:'')."},'modal_id' : 'bs-right-md-modal', 'call_function':'addPrcMovement', 'form_id' : 'addPrcMovement', 'title' : 'Movement ', 'fnsave' : 'savePRCMovement','js_store_fn' : 'customStore'}";
                                                ?>
                                                <button type="button" class="btn btn-primary " style="width:40%;" <?=empty($operatorData->id)?'disabled':''?> onclick="modalAction(<?=$moveParam ?>)"><span>Add</span></button>
                                                <button type="button" class="btn btn-success detailMovement" style="width:40%;" <?=empty($operatorData->id)?'disabled':''?>  data-operator_id = "<?=!empty($operatorData->id)?$operatorData->id:''?>"><span>Detail</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive" id="detailEntry">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
        $footerFile = 'footerfiles';
        $this->load->view('includes/'.$footerFile);
    ?>
    <?php $this->load->view('includes/modal');?>
</body>
</html>
<script>
    
    $(document).ready(function(){
        $(document).on('click','.detailAccept',function(e){ 
            e.stopImmediatePropagation();e.preventDefault();
            var operator_id = $(this).data('operator_id');
            if(operator_id){
                $.ajax({
                    type: "POST",
                    url: base_url + 'pos/getPrcAceptLogHtml',
                    data:{operator_id:operator_id},
                    dataType:'json'
                }).done(function (response) {
                    $("#detailEntry").html(response.html);
                    reportTable();
                });
            }
        });
        $(document).on('click','.detailLog',function(e){ 
            e.stopImmediatePropagation();e.preventDefault();
            var operator_id = $(this).data('operator_id');
            if(operator_id){
                $.ajax({
                    type: "POST",
                    url: base_url + 'pos/getProductionLogHtml',
                    data:{operator_id:operator_id},
                    dataType:'json'
                }).done(function (response) {
                    $("#detailEntry").html(response.html);
                    reportTable();
                });
            }
        });

        $(document).on('click','.detailMovement',function(e){ 
            e.stopImmediatePropagation();e.preventDefault();
            var operator_id = $(this).data('operator_id');
            if(operator_id){
                $.ajax({
                    type: "POST",
                    url: base_url + 'pos/getProductionMovementHtml',
                    data:{operator_id:operator_id},
                    dataType:'json'
                }).done(function (response) {
                    $("#detailEntry").html(response.html);
                    reportTable();
                });
            }
        });
        
        $(document).on('click','.detailIssueMaterial',function(e){ 
            e.stopImmediatePropagation();e.preventDefault();
            var operator_id = $(this).data('operator_id');
            if(operator_id){
                $.ajax({
                    type: "POST",
                    url: base_url + 'pos/getMtIssueLogHtml',
                    data:{operator_id:operator_id},
                    dataType:'json'
                }).done(function (response) {
                    $("#detailEntry").html(response.html);
                    reportTable();
                });
            }
        });
    });
    function getPrcAcceptResponse(data,formId=""){
        if(data.status==1){
            if(formId){
                closeModal(formId);
            }
            
            if(data.prc_process_id){
                var postData ={id:data.prc_process_id,accept_qty:data.accept_qty};
                pUrl = encodeURIComponent(window.btoa(JSON.stringify(postData)));
                printBox({postData:{url:pUrl},controller:'pos',call_function:'prcProcesstag',reload_page:'1'});
                
            }
            // window.location.href = base_url+controller;
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});
            }else{
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }
    }

    function getPrcLogResponse(data,formId=""){
        if(data.status==1){
            if(formId){
                closeModal(formId);
            }
            
            if (data.movement_tag) {
                printBox({ postData: { id: data.id }, controller: 'pos', call_function: 'printPRCMovement' ,reload_page:'1'});
            }
            if (data.log_print) {
                printBox({ postData: { id: data.id }, controller: 'pos', call_function: 'printPRCLog' ,reload_page:'1'});
            }
            if (data.rej_print) {
                printBox({ postData: { id: data.id }, controller: 'pos', call_function: 'printPRCRejLog' ,reload_page:'1'});
            }
            
        
            // window.location.href = base_url+controller;
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});
            }else{
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }
    }

    function regenerateTagResponse(data){
        if(data.status==1){
            printBox({postData:{id:data.id,tag_qty:data.tag_qty},controller:'pos',call_function:'printPRCMovement'});
            $('#regenerateMoveTag')[0].reset(); closeModal('regenerateMoveTag');
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});
            }else{
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }
    }

    function getMaterialIssueResponse(data,formId=""){
        if(data.status==1){
            if(formId){
                closeModal(formId);
            }
            var postData ={item_id:data.item_id,prc_id:data.prc_id,id:data.id};
            pUrl = encodeURIComponent(window.btoa(JSON.stringify(postData)));
            printBox({postData:{url:pUrl},controller:'pos',call_function:'printMaterialAcceptTag',reload_page:'1'});
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});
            }else{
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }
    }

   
    function printBox(data) {
        var controllerName = data.controller || controller;
        // Perform the AJAX call to fetch the URL for the PDF
        $.ajax({
            type: "POST",
            url: base_url + controllerName + '/' + data.call_function,
            data: data.postData,
            dataType: 'json'
        }).done(function (response) {
            if (response.url) {
                // Create a new iframe (this will hold the PDF to be printed)
                var iframe = document.createElement('iframe');
                iframe.style.position = 'absolute';
                iframe.style.width = '0px';
                iframe.style.height = '0px';
                iframe.style.border = 'none';
                iframe.src = response.url;  // Set the iframe source to the PDF URL
                document.body.appendChild(iframe);  // Append the iframe to the body
                // Trigger the print dialog after the iframe is loaded
                iframe.onload = function () {
                    iframe.contentWindow.print();  // Trigger the print dialog from the iframe
                    // Listen for window blur (losing focus), meaning print dialog opened
                    window.onblur = function () {
                    };
                    // Listen for window focus (gained focus), meaning print dialog closed
                    window.onfocus = function () {
                        // Refresh the page when the print dialog is closed
                        if(data.reload_page){
                            window.location.href = base_url+controller;
                        }
                        
                    };
                };
                // Optional: Handle case where user cancels the print dialog
                iframe.contentWindow.onbeforeunload = function () {
                    // Clean up the iframe after dialog closure
                    document.body.removeChild(iframe);
                    if(data.reload_page){
                        window.location.href = base_url+controller;
                    }
                };
            }
        });
    }
</script>