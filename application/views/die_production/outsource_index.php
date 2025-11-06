<?php $this->load->view('includes/header'); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('outsourceTable',0,'getProductionDtHeader','dieOutsource');" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info mr-2 active pendTab" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                            </li>
							<li class="nav-item"> 
                                <button onclick="statusTab('outsourceTable',1,'getProductionDtHeader','dieOutsourceChallan');" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info mr-2" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending Receive</button> 
                            </li>
							<li class="nav-item"> 
                                <button onclick="statusTab('outsourceTable',2,'getProductionDtHeader','dieOutsourceChallan');" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                            </li>
                           
                        </ul>
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='outsourceTable' class="table table-bordered ssTable ssTable-cf" data-url='/getOutsourceDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
	$(document).ready(function() {
		initBulkChallanBtn();
		if (typeof initTable === 'function') {
			const originalInitTable = initTable;
			window.initTable = function(...args) {
				const result = originalInitTable.apply(this, args);
				initBulkChallanBtn(); 
				return result;
			};
		}
		$(document).on('click', '.BulkChallan', function() {
			if ($(this).attr('id') == "masterChSelect") {
				if ($(this).prop('checked') == true) {
					$(".bulkCh").show();
					$("input[name='dp_id[]']").prop('checked', true);
				} else {
					$(".bulkCh").hide();
					$("input[name='dp_id[]']").prop('checked', false);
				}
			} else {
				if ($("input[name='dp_id[]']").not(':checked').length != $("input[name='dp_id[]']").length) {
					$(".bulkCh").show();
					$("#masterChSelect").prop('checked', false);
				} else {
					$(".bulkCh").hide();
				}

				if ($("input[name='dp_id[]']:checked").length == $("input[name='dp_id[]']").length) {
					$("#masterChSelect").prop('checked', true);
					$(".bulkCh").show();
				}
				else{$("#masterChSelect").prop('checked', false);}
			}
		});
		
        $(document).on('click', '.bulkCh', function() {
			var dp_id = [];
			$("input[name='dp_id[]']:checked").each(function() {
				dp_id.push(this.value);
			});
			var ids = dp_id.join(",");
			
            var data ={postData:{ids:ids},call_function:'addChallan',modal_id:'bs-right-lg-modal', form_id : 'addChallan', title : 'Outsource Challan',fnsave:'saveChallan'}
			modalAction(data);
		});


		
	});
	
	function initBulkChallanBtn() {
		var bulkChBtn = '<button class="btn btn-outline-dark bulkCh" tabindex="0" aria-controls="outsourceTable" type="button"><span>Bulk Challan</span></button>';
		// Prevent duplicate buttons
		setTimeout(function(){
			if ($("#outsourceTable_wrapper .dt-buttons .bulkCh").length === 0) {
				$("#outsourceTable_wrapper .dt-buttons").append(bulkChBtn);
				$(".bulkCh").hide();
			}
		}, 500);
		
	}
</script>