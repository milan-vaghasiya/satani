<?php
    $prcDetail = '<div class="cd-header"><h6 class="m-0 prc_number">PRC DETAIL</h6></div>
                    <div class="sop-body vh-35" data-simplebar>
					    <div>
					        <div class="text-center">
    					        <img src="'.base_url('assets/images/background/dnf_2.png').'" style="width:50%;">
    						    <div class="text-center text-muted font-16 fw-bold">Please click any <strong>PRC</strong> to see Data</div>
						    </div>
					    </div>
					</div>';
    if(!empty($prcData))
    {
        $status = "";
        if($prcData->status == 1){$status = '<span class="badge bg-info">Planned</span>';}
        elseif($prcData->status == 2){$status = '<span class="badge bg-primary">Inprogress</span>';}
        elseif($prcData->status == 3){$status = '<span class="badge bg-success">Completed</span>';}
        elseif($prcData->status == 4){$status = '<span class="badge bg-warning">On Hold</span>';}
        elseif($prcData->status == 5){$status = '<span class="badge bg-danger">Closed</span>';}

        
        $prcDetail='<div class="cd-header" style="padding: 7px 16px;">
                        <h6 class="m-0 prc_number">#'.$prcData->prc_number.' '.$status.'</h6>
                        <p class="mb-0 fs-12 "><i class="far fa-fw fa-clock"></i> <span class="prc_date">'.formatDate($prcData->prc_date,"d M Y").'</span></p>
                        <div class="cd-features">
                            <div class="dropdown d-inline-block">
                                <a class="text-dark" href="'.base_url('sopDesk/printDetailRouteCard/'.$prcData->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print font-22"></i></a>
                                
                                <a class="text-dark" href="'.base_url('sopDesk/').'" datatip="Back" flow="down"><i class="fas fa-backward font-22"></i></a>

                            </div>
                        </div>
                    </div>
                    <div class="sop-body vh-35" data-simplebar>
                        <div class="prcDetail1">
                            <div class="" style="border-bottom: 1px dashed #e8ebf3;" >
                                <p class="m-0 font-15">Product</p>
                                <p class="text-muted fw-semibold1 mb-0">'.$prcData->item_name.'</p>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-lg-4">
                                    <div class="mt-2" style="border-bottom: 1px dashed #e8ebf3;">
                                        <p class="m-0 font-15">Job Qty<br><span class="text-muted">'.floatval($prcData->prc_qty).' <small>'.$prcData->uom.'</small></span></p>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-1">'.$prcData->remark.'</p>
                        </div>
                    </div>';
    }
    echo $prcDetail;

?>