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
                            <button onclick="statusTab('dieProductionTable',0);" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info mr-2 active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                        </li>
                        <li class="nav-item"> 
                            <button onclick="statusTab('dieProductionTable',1);" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info mr-2" style="outline:0px" data-toggle="tab" aria-expanded="false">WIP</button> 
                        </li>
                        <!-- <li class="nav-item"> 
                            <button onclick="statusTab('dieProductionTable',5);" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info mr-2" style="outline:0px" data-toggle="tab" aria-expanded="false">Outsource</button> 
                        </li> -->
                        <li class="nav-item"> 
                            <button onclick="statusTab('dieProductionTable',2);" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                        </li>
                       
                    </ul>
					</div>
					<div class="float-end">
                        <?php
                            $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addWorkOrder', 'form_id' : 'addWorkOrder', 'title' : 'Add Work Order'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Work Order</button>
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
                                <table id='dieProductionTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
