<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('materialTable',1);" id="issue_batch" class="nav-tab btn waves-effect waves-light btn-outline-info mr-2 active" style="outline:0px" data-toggle="tab" aria-expanded="false">Issued</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('materialTable',2);" id="approved_batch" class="nav-tab btn waves-effect waves-light btn-outline-info mr-2" style="outline:0px" data-toggle="tab" aria-expanded="false">Approved</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('materialTable',3);" id="complete_batch" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                            </li>
                        </ul>
                    </div>
                    <div class="float-end">
                        <?php
                            $addParam = "{'postData':{'item_type' : 1},'modal_id' : 'bs-right-lg-modal', 'call_function':'generatePrcBatch', 'form_id' : 'generatePrcBatch', 'title' : 'Generate Batch' , 'fnsave' : 'savePrcBatch'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Generate Batch</button>
					</div>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='materialTable' class="table table-bordered ssTable" data-url='/getPrcBatchDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
