<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
						<button type="button" class="btn btn-info btn-sm float-right press-add-btn addNewPrice permission-write" data-button="both" data-modal_id="bs-right-lg-modal" data-function="addVendorPrice" data-form_title="Add Price" data-js_store_fn="storePrice"><i class="fa fa-plus"></i> Add Price</button>
					</div>
					<ul class="nav nav-pills">
						<li class="nav-item"> <button onclick="statusTab('vendorPriceTbl',0);" class=" btn waves-effect waves-light btn-outline-info active mr-1" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button> </li>
						<li class="nav-item"> <button onclick="statusTab('vendorPriceTbl',1);" class=" btn waves-effect waves-light btn-outline-info mr-1" style="outline:0px" data-bs-toggle="tab" aria-expanded="false"> Approved </button> </li>
						<li class="nav-item"> <button onclick="statusTab('vendorPriceTbl',2);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-bs-toggle="tab" aria-expanded="false"> Rejected </button> </li>
					</ul>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='vendorPriceTbl' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>    
$(document).ready(function(){

    $(document).on('click',".addNewPrice",function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
        var title = $(this).data('form_title');
        var formId = $(this).data('form_id') || functionName.split('/')[0];
        var controllerName = $(this).data('controller') || controller;
        var postData = $(this).data('postdata') || {};
        var fnsave = $(this).data("fnsave") || "save";
        var resFunction = $(this).data('res_function') || "";
        var jsStoreFn = $(this).data('js_store_fn') || 'store';
        var savebtn_text = $(this).data('savebtn_text') || '<i class="fa fa-check"></i> Save';

        var fnJson = "{'formId':'"+formId+"','controller':'"+controllerName+"','fnsave':'"+fnsave+"'}";
        
        
        if(typeof postData === "string"){
            postData = JSON.parse(postData);
        }

        $.ajax({ 
            type: "post",   
            url: base_url + controllerName + '/' + functionName,   
            data: postData
        }).done(function(response){
            $("#"+modalId).modal('show');
            $("#"+modalId).css({'z-index':9999,'overflow':'auto'});
            $("#"+modalId+'').addClass(formId+"Modal");
            $("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html("");
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            if(resFunction != ""){
                $("#"+modalId+" .modal-body form").attr('data-res_function',resFunction);
            }
            $("#"+modalId+" .modal-footer .btn-save").html(savebtn_text);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',jsStoreFn+"("+fnJson+");");

            $("#"+modalId+" .modal-header .close").attr('data-modal_id',modalId);
            $("#"+modalId+" .modal-header .close").attr('data-modal_class',formId+"Modal");
            $("#"+modalId+" .modal-footer .btn-close").attr('data-modal_id',modalId);
            $("#"+modalId+" .modal-footer .btn-close").attr('data-modal_class',formId+"Modal");

            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }

            initSelect2(modalId);
            setTimeout(function(){ setPlaceHolder(); }, 5);
        });
    });	

    $(document).on('change','#item_id',function(){
        $("#vendor_id").val("");
        $("#processSelect").html("");
        $("#priceTbody").html("");
        reInitMultiSelect();
        $('#vendor_id').select2();
    });

    $(document).on('click','.loaddata',function(e){
        $(".error").html("");
        var valid = 1;
        var item_id = $('#item_id').val();
        var vendor_id = $('#vendor_id').val();
        var process_id = $('#process_id').val();
        if(item_id == ""){$(".item_id").html("Part is required.");valid=0;}
        if(vendor_id == ""){$(".vendor_id").html("Party is required.");valid=0;}
        if(process_id == ""){$(".process_id").html("Process is required.");valid=0;}

        if(valid)
        {
            $.ajax({
                url: base_url + controller + '/getPriceComparison',
                data: {item_id:item_id, vendor_id:vendor_id, process_id:process_id},
                type: "POST",
                dataType:'json',
                success:function(data){
                    // $("#tbodyData").html("");
                    $("#tbodyData").html(data.tbodyData);
                }
            });
        }
    });   
    
});

function approvePrice(id,name='Record'){
    var resFunctionName = "";
    var msg = "Are you sure want to Approve this Price ?";
    var ajaxParam = {
        url: base_url + controller + '/approvePrice',
        data: { id:id },
        type: "POST",
        dataType:"json"
    };

    Swal.fire({
        title: 'Are you sure?',
        text: msg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Do it!',
    }).then(function(result) {
        if (result.isConfirmed)
        {
            $.ajax(ajaxParam).done(function(response){
                if(resFunctionName != ""){
                    window[resFunctionName](response);
                }else{
                    if(response.status==1){
                        initTable();
                        Swal.fire( 'Success', response.message, 'success' );
                    }else{
                        if(typeof response.message === "object"){
                            $(".error").html("");
                            $.each( response.message, function( key, value ) {$("."+key).html(value);});
                        }else{
                            initTable();
                            Swal.fire( 'Sorry...!', response.message, 'error' );
                        }			
                    }	
                }			
            });
        }
    });
}

function rejectPrice(id,name='Record'){
    var resFunctionName = "";
    var msg = "Are you sure want to Reject this Price ?";
    var ajaxParam = {
        url: base_url + controller + '/rejectPrice',
        data: { id:id },
        type: "POST",
        dataType:"json"
    };

    Swal.fire({
        title: 'Are you sure?',
        text: msg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Do it!',
    }).then(function(result) {
        if (result.isConfirmed)
        {
            $.ajax(ajaxParam).done(function(response){
                if(resFunctionName != ""){
                    window[resFunctionName](response);
                }else{
                    if(response.status==1){
                        initTable();
                        Swal.fire( 'Success', response.message, 'success' );
                    }else{
                        if(typeof response.message === "object"){
                            $(".error").html("");
                            $.each( response.message, function( key, value ) {$("."+key).html(value);});
                        }else{
                            initTable();
                            Swal.fire( 'Sorry...!', response.message, 'error' );
                        }			
                    }	
                }			
            });
        }
    });
}

function storePrice(postData){
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
            $("#priceTbody").html(data.tbodyData);
            $("#id").val("");
            $("#rate").val("");
            $("#rate_unit").val("");
            $("#cycle_time").val("");
            $("#remark").val("");
            // $("#rate_unit").select2();
            $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
			initTable();
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

function editPrice(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnedit = data.fnedit;if(fnedit == "" || fnedit == null){fnedit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var controllerName = data.controller;if(controllerName == "" || controllerName == null){controllerName=controller;}
	var savebtn_text = data.savebtn_text;
	if(savebtn_text == "" || savebtn_text == null){savebtn_text='<i class="fa fa-check"></i> Save';}

	var resFunction = data.res_function || "";
	var jsStoreFn = data.js_store_fn || 'store';

	var fnJson = "{'formId':'"+data.form_id+"','fnsave':'"+fnsave+"','controller':'"+controllerName+"'}";

	$.ajax({ 
		type: "POST",   
		url: base_url + controllerName + '/' + fnedit,   
		data: data.postData,
	}).done(function(response){
		$("#"+data.modal_id).modal('show');
		$("#"+data.modal_id).css({'z-index':1059,'overflow':'auto'});
		$("#"+data.modal_id).addClass(data.form_id+"Modal");
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html('');
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		if(resFunction != ""){
			$("#"+data.modal_id+" .modal-body form").attr('data-res_function',resFunction);
		}
		$("#"+data.modal_id+" .modal-footer .btn-save").html(savebtn_text);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',jsStoreFn+"("+fnJson+");");

		$("#"+data.modal_id+" .modal-header .close").attr('data-modal_id',data.modal_id);
		$("#"+data.modal_id+" .modal-header .close").attr('data-modal_class',data.form_id+"Modal");
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_class',data.form_id+"Modal");

		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}

		initSelect2(data.modal_id);
		setPlaceHolder();
	});
}
</script>