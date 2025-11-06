<?php
    $prcMaterial = '<div class="text-center">
				        <img src="'.base_url('assets/images/background/dnf_3.png').'" style="width:50%;">
					    <div class="text-center text-muted font-16 fw-bold">Please click any <strong>PRC</strong> to see Data</div>
				    </div>';
	if(!empty($materialData))
	{
        $prcMaterial = "";
        foreach($materialData as $row){
            $rm_name = $row->item_code.' '.$row->item_name;
            
            $uq = $row->used_qty;
            $return = $row->return_qty;
            $scrap_qty = $row->scrap_qty;
            $sq = $row->stock_qty;
            $returnParam = "{'postData':{'prc_id' : ".$row->prc_id.",'item_id' : ".$row->item_id.",'batch_id':".$row->batch_id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'materialReturn', 'form_id' : 'materialReturn', 'title' : 'Return Material', 'js_store_fn' : 'customStore', 'fnsave' : 'storeReturned','button':'close'}";
            
			$prcMaterial .= '<div class=" grid_item" style="width:100%;">
                                <div class="card sh-perfect">
                                    <div class="card-body">                                    
                                        <div class="task-box">
                                            <div class="float-end">
                                                <div class="dropdown d-inline-block">
                                                    <a class="dropdown-toggle" id="dLabel1" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                                        <i class="las la-ellipsis-v font-24 text-muted"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="javascript:void(0)" class="dropdown-item btn btn-danger permission-modify" datatip="Return Material" flow="down"  onclick="modalAction('.$returnParam.')"><i class="icon icon-action-redo"></i> Return</a>
                                                     </div>
                                                </div>
                                            </div>
                                            <h5 class="mt-0 fs-15 cursor-pointer" >'.$rm_name.' </h5>
                                            <div class="d-flex justify-content-between mb-0">  
                                                <h6 class="fw-semibold mb-0">Material Batch : <span class="text-muted font-weight-normal"> '.$row->trans_number.'</span></h6> 
                                            </div>
                                            <hr class="hr-dashed my-5px">
                                            <div class="media align-items-center btn-group process-tags">
                                                <span class="badge bg-light-cream btn flex-fill" datatip="Used Qty" flow="down">UQ : '.floatval($uq).'</span>
                                                <span class="badge bg-light-cream btn flex-fill" datatip="Return Qty" flow="down">MR : '.floatval($return).'</span>
                                                <span class="badge bg-light-raspberry btn flex-fill" datatip="Stock Qty" flow="down">SQ : '.round($sq,3).'</span>
                                            </div>                                       
                                        </div>
                                    </div>
                                </div>
                            </div>';
        }
    }
    echo $prcMaterial;

?>