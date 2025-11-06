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
                            <button onclick="statusTab('dieProductionTable',0,'getProductionDtHeader','freshDie');" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Fresh</button> 
                        </li>
                        <li class="nav-item"> 
                            <button onclick="statusTab('dieProductionTable',1,'getProductionDtHeader','dieRegister');" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Available</button> 
                        </li>
                        <li class="nav-item"> 
                            <button onclick="statusTab('dieProductionTable',2,'getProductionDtHeader','dieRegister');" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Issued</button> 
                        </li>
                        <li class="nav-item"> 
                            <button onclick="statusTab('dieProductionTable',3,'getProductionDtHeader','dieRegister');" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Rejected</button> 
                        </li>
                        <li class="nav-item"> 
                            <button onclick="statusTab('dieProductionTable',4,'getProductionDtHeader','dieRegister');" id="planned_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Converted</button> 
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
                                <table id='dieProductionTable' class="table table-bordered ssTable ssTable-cf" data-url='/getRegisterDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
