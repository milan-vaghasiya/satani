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
                                <button data-tab_type="acceptedLog" data-header_name="getProductionDtHeader" class="nav-tab btn waves-effect waves-light btn-outline-success active mr-2 statusTb tabFilter" id="planned_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Accepted Log</button>
                            </li>
							<li class="nav-item">
                                <button data-tab_type="challanLog" data-header_name="getProductionDtHeader" class="nav-tab btn waves-effect waves-light btn-outline-success mr-2 statusTb tabFilter" id="planned_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Challan Request</button>
                            </li>
                            <li class="nav-item">
                                <button data-tab_type="productionLog" data-header_name="getProductionDtHeader" class="nav-tab btn waves-effect waves-light btn-outline-success mr-2 statusTb tabFilter" id="planned_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Prc Log</button>
                            </li>
                            <li class="nav-item">
                                <button data-tab_type="movement" data-header_name="getProductionDtHeader" class="nav-tab btn waves-effect waves-light btn-outline-success mr-2 statusTb tabFilter" id="planned_index" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Movement</button>
                            </li>
                        </ul>
					</div>
                    <div class="float-end" style="width:15%" data-basic-select2-id="4" hidden>
						<select id="production_type" class="form-control tabFilter" >
							<option value="1" >Regular</option>
							<option value="2">Rework</option>
						</select>
                    </div>
                    <h4 class="card-title text-center"><?=$processData->process_name?></h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='sopTable' class="table table-bordered ssTable ssTable-cf" data-url='/getLogDetailDTRow/<?=$process_id?>'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/js/custom/sop_desk.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
    $("#production_type").on("click",function(){ return false;});
    $(document).on('change click','.tabFilter', function() {
        var move_type = $("#production_type").val();
        var tab_type = $(".statusTb.active").data('tab_type');
        var header_name = $(".statusTb.active").data('header_name');
        var param = move_type+'/'+tab_type;
        
        statusTab('sopTable',param,header_name,tab_type)
    });
});
    
</script>
