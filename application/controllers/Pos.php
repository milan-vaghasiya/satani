<?php
class Pos extends Pos_Controller{

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "POS";
        $this->data['headData']->controller = "pos";
    }
   
    public function index($posUrl = ""){
        if(!empty($posUrl)){
            $this->data['operator_id'] =$operator_id= decodeURL($posUrl);
            $this->data['operatorData'] =  $this->employee->getEmployee(['id'=>$operator_id]);
			$this->load->view("pos/pos_board",$this->data);
        }else{
			$this->load->view("pos/pos_board");
		}
       
    }
    public function prcAccept(){
        $data = $this->input->post();
        $this->data['operator_id'] = $data['operator_id'];
        $this->load->view('pos/accept_prc_mt',$this->data);
    }

    public function getAcceptDetail(){
        $data = $this->input->post();
        $dUrl = decodeURL($data['scan_id']);
        $html = "";
        if($dUrl->type == 'move_tag'){
            $movementData = $this->sop->getProcessMovementList(['id'=>$dUrl->id,'itemDetail'=>1,'processDetail'=>1,'nextProcessDetail'=>1,'single_row'=>1,'completedProcessDetail'=>1]);
            $prcProcessData = $this->sop->getPendingAcceptData(['process_id'=>$movementData->next_process_id,'prc_id'=>$movementData->prc_id,'completed_process'=>$movementData->completed_process,'trans_type'=>$movementData->move_type]); 

            $pending_accept =$prcProcessData->inward_qty - $prcProcessData->accept_qty;
            $html = "";
            if(!empty($movementData)){
                $html=' <div class="col-md-12 form-group">
                            <table class="table table-bordered">
                                <tr class="bg-light">
                                    <th style="width:33%">Prc No</th>
                                    <th style="width:33%">Prc Qty</th>
                                    <th style="width:33%">Unaccepted Qty</th>
                                </tr>
                                <tr>
                                    <td>'.$movementData->prc_number.'</td>
                                    <td>'.$movementData->prc_qty.'</td>
                                    <td>'.$pending_accept.'</td>
                                </tr>
                                <tr >
                                    <th class="bg-light">Product</th>
                                    <td colspan="2">'.$movementData->item_name.'</td>
                                </tr>
                                 <tr > 
                                    <th class="bg-light">CompletedProcess</th>
                                    <td colspan="2">'.$movementData->completed_process_name.'</td>
                                </tr>
                                <tr > 
                                    <th class="bg-light">Process</th>
                                    <td colspan="2">'.$movementData->next_process_name.'</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="accepted_qty">Accepted Qty</label>
                            <input type="text" name="accepted_qty" id="accepted_qty" class="form-control" value="'.floatval($pending_accept).'">
                        </div>
                         <div class="col-md-12 form-group">
                            <label for="remark">Remark</label>
                            <input type="text" name="remark" id="remark" class="form-control" value="">
                        </div>
                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="accepted_process_id" id="accepted_process_id" value="'.$movementData->next_process_id.'">
                        <input type="hidden" name="process_from" id="process_from" value="'.$movementData->process_id.'">
                        <input type="hidden" name="completed_process" id="completed_process" value="'.$movementData->completed_process.'">
                        <input type="hidden" name="prc_id" id="prc_id" value="'.$movementData->prc_id.'">
                        <input type="hidden" name="trans_type" id="trans_type" value="'.$movementData->move_type.'">
                        <input type="hidden" name="move_qty" id="move_qty" value="'.$dUrl->tag_qty.'">
                        ';
            }
        }
        elseif($dUrl->type == 'material_tag'){
            // print_r($dUrl);
            $mtrData = $this->store->getMaterialIssueData(['prc_id'=>$dUrl->prc_id,'item_id'=>$dUrl->item_id,'single_row'=>1,'prcHeatData'=>1]); 
		  
            $acceptData = $this->sop->getPrcAcceptData(['accepted_process_id'=>$dUrl->process_id,'prc_id'=>$dUrl->prc_id,'process_from'=>0,'single_row'=>1,'itemDetail'=>1,'processDetail'=>1]);
            if(!empty($acceptData)){
                $html=' <div class="col-md-12 form-group">
                            <table class="table table-bordered">
                                <tr class="bg-light">
                                    <th style="width:33%">Prc No</th>
                                    <th style="width:33%">Prc Qty</th>
                                    <th style="width:33%">Issue Qty</th>
                                </tr>
                                <tr>
                                    <td>'.$acceptData->prc_number.'</td>
                                    <td>'.$acceptData->prc_qty.'</td>
                                    <td>'.floatval($dUrl->qty).$mtrData->uom.'</td>
                                </tr>
                                <tr >
                                    <th class="bg-light">Material</th>
                                    <td colspan="2">'.$mtrData->item_name.'</td>
                                </tr>
                                <tr >
                                    <th class="bg-light">Product</th>
                                    <td colspan="2">'.$acceptData->item_name.'</td>
                                </tr>
                                <tr > 
                                    <th class="bg-light">Process</th>
                                    <td colspan="2">'.$acceptData->process_name.'</td>
                                </tr>
                            </table>
                        </div>
                        <input type="hidden" name="scan_type" id="scan_type" value="material_tag">
                        <input type="hidden" name="accepted_process_id" id="accepted_process_id" value="'.$dUrl->process_id.'">
                        <input type="hidden" name="prc_process_id" id="prc_process_id" value="0">
                        <input type="hidden" name="prc_id" id="prc_id" value="'.$dUrl->prc_id.'">
                        <input type="hidden" name="id" id="id" value="'.$acceptData->id.'">
                        <input type="hidden" name="move_qty" id="move_qty" value="'.floatval($dUrl->qty).$mtrData->uom.'">
                        ';
            }else{
                $html = '<h4 class="text-danger">Something is wrong.Check PRC is start</h4>';
            }

        }else{
            $html = '<h4 class="text-danger">Invalid QR Code Scaned</h4>';
        }
        $this->printJson(['status'=>1,'html'=>$html]);
    }

    public function saveAcceptedQty(){
		$data = $this->input->post(); 
		$errorMessage = array();
        if (empty($data['accepted_process_id'])){ $errorMessage['general_error'] = "Prc Process required.";}
        if(empty($data['scan_type'])){
            if (empty($data['accepted_qty']) &&  empty($data['short_qty'])) {  $errorMessage['accepted_qty'] = "Quantity is required.";}
            else{
                /*if($data['accepted_qty'] > $data['move_qty']){
                    $errorMessage['accepted_qty'] = "Qty greater than tag qty";
                }else{*/
                    $acceptedQty = !empty($data['accepted_qty'])?$data['accepted_qty']:0;
                    $prcProcessData =$this->sop->getPendingAcceptData(['process_id'=>$data['accepted_process_id'],'prc_id'=>$data['prc_id'],'completed_process'=>$data['completed_process'],'trans_type'=>$data['trans_type']]); 
                    $pending_accept =$prcProcessData->inward_qty - $prcProcessData->accept_qty;
                    if($acceptedQty > $pending_accept){
                        $errorMessage['accepted_qty'] = "Accept Quantity is Invalid.";
                    }
                    
                // }
                
            }
        }
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            if(empty($data['scan_type'])){
                unset($data['move_qty']);
                $data['trans_date'] = date("Y-m-d");
                $data['created_at'] = date("Y-m-d H:i:s");
                $result = $this->sop->saveAcceptedQty($data);
                $result['prc_process_id'] = $result['id'];
            }else{
                $result['status'] = 1;
                $result['message'] = 'Success';
                $result['prc_process_id'] = $data['id'];
                $result['accept_qty'] = $data['move_qty'];
            }
           
			$this->printJson($result);
		endif;
	}

    public function prcProcesstagOld($tag_url = "") {
        $url = (!empty($tag_url)?$tag_url:$this->input->post('url'));
        $data = decodeURL($url);
        
		$processData = $this->sop->getPrcAcceptData(['id'=> $data->id,'single_row'=>1,'processDetail'=>1,'prcDetail'=>1,'itemDetail'=>1,'completedProcessDetail'=>1]);
		$logo = base_url('assets/images/logo.png');
		$qrIMG = base_url('assets/uploads/sop/'.$data->id.'.png');
        $qrText = encodeURL(['id'=>$processData->id,'type'=>'process_tag']);
        $file_name = $data->id;
        $qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/sop/',$file_name);
        $qrIMG =  '<td rowspan="4" class="text-right" style="padding:1px;" style="width:35%;"><img src="'.$qrIMG.'" style="height:30mm;"></td>';

        
        $qty = (!empty($data->accept_qty)?$data->accept_qty:floatval($processData->accepted_qty).'Nos');
        $itemList = '<table class="table tag_print_table"  style="font-size:0.5rem;">
                <tr>
                    <td style="width:25%;"><img src="' . $logo . '" style="height:39px;"></td>
                    <td class="org_title text-center" style="font-size:1rem;width:40%;">Accepted Tag</td>
                    '.$qrIMG.'
                </tr>
                <tr class="text-left">
                    <th >Batch No</th>
                    <td>' . $processData->prc_number . '</td>
                </tr>
                <tr  class="text-left">
                    <th>Accepted Qty</th>
                    <td>' . $qty . '</td>
                </tr>
                <tr  class="text-left">
                    <th>Date</th>
                    <td>' . formatDate($processData->trans_date) . '</td>
                </tr>
                <tr  class="text-left">
                    <th>Process</th>
                    <td colspan="2">' . (!empty($processData->process_name)?$processData->process_name:'') . '</td>
                    
                    
                </tr>
                <tr  class="text-left">
                    <th>Part</th>
                    <td colspan="2">' . (!empty($processData->item_code) ? '['.$processData->item_code.'] ' : '') . $processData->item_name . '</td>
                </tr>
                <tr  class="text-left">
                    <th>Completed Process</th>
                    <td colspan="2">' . $processData->completed_process_name . '</td>
                </tr>
            </table>';

		$pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">'  . $itemList . '</div>';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ",'accept_tag'.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        if(!empty($tag_url)){
            $mpdf->Output($pdfFileName, 'I');
        }else{
            $pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
        
	}

    public function prcProcesstag($tag_url = "") {
        $url = (!empty($tag_url)?$tag_url:$this->input->post('url'));
        $data = decodeURL($url);
        
		$processData = $this->sop->getPrcAcceptData(['id'=> $data->id,'single_row'=>1,'processDetail'=>1,'prcDetail'=>1,'itemDetail'=>1,'completedProcessDetail'=>1]);
		$logo = base_url('assets/images/logo.png');
		$qrIMG = base_url('assets/uploads/sop/'.$data->id.'.png');
        $qrText = encodeURL(['id'=>$processData->id,'type'=>'process_tag']);
        $file_name = $data->id;
        $qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/sop/',$file_name);
        
        
        $qty = (!empty($data->accept_qty)?$data->accept_qty:floatval($processData->accepted_qty).'Nos');
        // Build the HTML content for the PDF
        $itemList ='<table class="table" style="font-size:0.5rem;">
                        <tr>
                            <td><img src="'.$logo.'" style="max-height:40px;"></td>
                            <td class="text-right"><b>Material Status Card </b><br><small>(FQC26B(00/01.01.24))</small></td>
                        </tr>
                    </table>
                    <table class="table top-table-border">
                        <tr>
                            <td>Card No. <br><b>'.(!empty($processData->prc_number)?$processData->prc_number:'-').'</b></td>
                            <td>Date <br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Process Unit<br><b>'.(!empty($irData->company_alias)?$irData->company_alias:'-').'</b></td>
                            <td>Department <br><b>Production</b></td>
                        </tr>
                        <tr>
                            <td>Type & Part<br><b>'.(!empty($processData->item_name)?$processData->item_name:'-').'</b></td>
                            <td>Batch/Heat No <br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Material Qty<br><b>'.$qty.' (NOS)</b></td>
                            <td>Material Stage<br><b>'.(!empty($processData->process_name)?$processData->process_name:'-').'</b></td>
                        </tr>
                        <tr>
                            <td>Material Process<br><b>'.(!empty($processData->process_name)?$processData->process_name:'-').'</b></td>
                            <td>Material Status<br><b>Receive</b></td>
                        </tr>
                        <tr>
                            <td>Operator/Inspector<br><b>'.(!empty($processData->emp_name) ? $processData->emp_name : '-').'</b></td>
                            <td>Supervisor<br><b></b>-</td>
                        </tr>
                        <tr>
                            <td colspan="2">Completed <br><b>'.$processData->completed_process_name.'</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center"><img src="'.$qrIMG.'" style="height:30mm;"></td>
                        </tr>
                    </table>';
        $pdfData = '<div style="text-align:center;float:left;padding:1mm 1mm;rotate: -90;position: absolute;bottom:1mm;width:65mm;height:95mm;">' . $itemList . '</div>';
		// print_r($pdfData);exit;
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 68]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ",'accept_tag'.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        if(!empty($tag_url)){
            $mpdf->Output($pdfFileName, 'I');
        }else{
            $pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
        
	}

    public function printPRCLogOld($log_id = "",$tag_qty="") {
        
        $id = (!empty($log_id) ? $log_id :$this->input->post('id'));
        // Fetch process data
        $processData = $this->sop->getProcessLogList(['id' => $id, 'itemDetail' => 1,'processDetail'=>1,'processorDetail'=>1,'operatorDetail'=>1,'single_row'=>1,'completedProcessDetail'=>1]);
        
        $logo = base_url('assets/images/logo.png');
        $qrIMG = base_url('assets/uploads/sop/' . $id . '.png');
        $qrText = encodeURL(['id' => $id, 'type' => 'log_tag']);
        $file_name = $id;
        $qrIMG = base_url() . $this->getQRCode($qrText, 'assets/uploads/sop/', $file_name);
        $qrIMG = '<td rowspan="4" class="text-right" style="padding:1px;" style="width:30%;"><img src="' . $qrIMG . '" style="height:30mm;"></td>';
    
        // Build the HTML content for the PDF
        $itemList = '<table class="table tag_print_table" style="font-size:0.5rem;">
                    <tr>
                        <td style="width:20%;"><img src="' . $logo . '" style="height:39px;"></td>
                        <td class="org_title text-center" style="font-size:1rem;width:50%;">Production Tag <br><small>('.(!empty($processData->process_name)?$processData->process_name:'Initial Stage').')</small></td>
                        '.$qrIMG.'
                    </tr>
                    <tr class="text-left">
                        <th>Batch No</th>
                        <td>' . $processData->prc_number . '</td>
                    </tr>
                    <tr class="text-left">
                        <th> Qty</th>
                        <td>' .  floatval((empty($tag_qty)?$processData->qty:$tag_qty))  . '</td>
                    </tr>
                    <tr class="text-left">
                        <th>Date</th>
                        <td>' . formatDate($processData->trans_date) . '</td>
                    </tr>
                    <tr class="text-left">
                        <th>Process By</th>
                        <td colspan="2">' . (($processData->process_by == 3)?$processData->processor_name:(!empty($processData->emp_name) ? $processData->emp_name : '')) . '</td>
                    </tr>
                    <tr class="text-left">
                        <th>Part</th>
                        <td colspan="2">' . (!empty($processData->item_code) ? '[' . $processData->item_code . '] ' : '') . $processData->item_name . '</td>
                    </tr>
                    <tr class="text-left">
                        <th>Remark</th>
                        <td colspan="2">' . $processData->remark . '</td>
                    </tr>
                    <tr class="text-left">
                        <th>Completed Process</th>
                        <td colspan="2">' . $processData->completed_process_name . '</td>
                    </tr>
                </table>';
    
        // Prepare the content for the PDF
        $pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">' . $itemList . '</div>';
    
        // Create PDF instance and generate the PDF
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ", 'log_tag'.$id.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
    
        $pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
        if(!empty($log_id)){
            $mpdf->Output($pdfOutputPath, 'I'); 
        }else{
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
    }
    public function printPRCLog($log_id = "",$tag_qty="") {
        
        $id = (!empty($log_id) ? $log_id :$this->input->post('id'));
        // Fetch process data
        $processData = $this->sop->getProcessLogList(['id' => $id, 'itemDetail' => 1,'processDetail'=>1,'processorDetail'=>1,'operatorDetail'=>1,'single_row'=>1,'completedProcessDetail'=>1]);
        
        $logo = base_url('assets/images/logo.png');
        $qrIMG = base_url('assets/uploads/sop/' . $id . '.png');
        $qrText = encodeURL(['id' => $id, 'type' => 'log_tag']);
        $file_name = $id;
        $qrIMG = base_url() . $this->getQRCode($qrText, 'assets/uploads/sop/', $file_name);
       
    
        // Build the HTML content for the PDF
        $itemList ='<table class="table" style="font-size:0.5rem;">
                        <tr>
                            <td><img src="'.$logo.'" style="max-height:40px;"></td>
                            <td class="text-right"><b>Material Status Card </b><br><small>(FQC26B(00/01.01.24))</small></td>
                        </tr>
                    </table>
                    <table class="table top-table-border">
                        <tr>
                            <td>Card No. <br><b>'.(!empty($processData->prc_number)?$processData->prc_number:'-').'</b></td>
                            <td>Date <br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Process Unit<br><b>'.(!empty($irData->company_alias)?$irData->company_alias:'-').'</b></td>
                            <td>Department <br><b>Production</b></td>
                        </tr>
                        <tr>
                            <td>Type & Part<br><b>'.(!empty($processData->item_name)?$processData->item_name:'-').'</b></td>
                            <td>Batch/Heat No <br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Material Qty<br><b>'.floatval((empty($tag_qty)?$processData->qty:$tag_qty)).' (NOS)</b></td>
                            <td>Material Stage<br><b>'.(!empty($processData->process_name)?$processData->process_name:'-').'</b></td>
                        </tr>
                        <tr>
                            <td>Material Process<br><b>'.(!empty($processData->process_name)?$processData->process_name:'-').'</b></td>
                            <td>Material Status<br><b>Log</b></td>
                        </tr>
                        <tr>
                            <td>Operator/Inspector<br><b>'.(!empty($processData->emp_name) ? $processData->emp_name : '-').'</b></td>
                            <td>Supervisor<br><b></b>-</td>
                        </tr>
                        <tr>
                            <td colspan="2">Completed <br><b>'.$processData->completed_process_name.'</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center"><img src="'.$qrIMG.'" style="height:30mm;"></td>
                        </tr>
                    </table>';
        $pdfData = '<div style="text-align:center;float:left;padding:1mm 1mm;rotate: -90;position: absolute;bottom:1mm;width:65mm;height:95mm;">' . $itemList . '</div>';
		// print_r($pdfData);exit;
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 68]]);

        $pdfFileName = str_replace(" ", "_", str_replace("/", " ", 'log_tag'.$id.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 1, 1, 1, 1);
        $mpdf->WriteHTML($pdfData);
    
        $pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
        if(!empty($log_id)){
            $mpdf->Output($pdfOutputPath, 'I'); 
        }else{
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
    }
    public function printPRCRejLog($log_id = "") {
		$id = (!empty($log_id)?$log_id:$this->input->post('id'));
		$processData = $this->sop->getProcessLogList(['id'=>$id,'single_row'=>1, 'itemDetail' => 1,'processDetail'=>1,'processorDetail'=>1,'operatorDetail'=>1,'single_row'=>1]);
		$logo = base_url('assets/images/logo.png');
		$qrIMG = base_url('assets/uploads/sop/'.$id.'.png');
        $qrText = encodeURL(['id'=>$id,'type'=>'rej_tag']);
        $file_name = $id;
        $qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/sop/',$file_name);
        $qrIMG =  '<td rowspan="4" class="text-right" style="padding:1px;" style="width:30%;"><img src="'.$qrIMG.'" style="height:30mm;"></td>';
	
        $itemList = '<table class="table tag_print_table">
                <tr>
                    <td style="width:23%;"><img src="' . $logo . '" style="height:30px;"></td>
                    <td class="org_title text-center" style="font-size:1rem;width:40%;">Rejection Tag <br><small>('.(!empty($processData->process_name)?$processData->process_name:'Initial Stage').')</small></td>
                    '.$qrIMG.'
                </tr>
                <tr class="text-left">
                    <td class="bg-light">Batch No</td>
                    <th>' . $processData->prc_number . '</th>
                </tr>
                <tr class="text-left">
                    <td class="bg-light"> Qty</td>
                    <th>' . floatval($processData->rej_found) . '</th>
                </tr>
                <tr class="text-left">
                    <td class="bg-light">Date</td>
                    <th>' . formatDate($processData->trans_date) . '</th>
                </tr>
                <tr class="text-left">
                    <td class="bg-light">Part</td>
                    <th colspan="2">' . (!empty($processData->item_code) ? '['.$processData->item_code.'] ' : '') . $processData->item_name . '</th>
                </tr>
                <tr class="text-left">
                    <td class="bg-light">Process By</td>
                    <th colspan="2">' . (!empty($processData->emp_name)?$processData->emp_name:'') . '</th>
                </tr>
            </table>';

		$pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">'  . $itemList . '</div>';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ",'rej_log_tag'.$id.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
		
        if(!empty($log_id)){
			$mpdf->Output($pdfFileName, 'I');
        }else{
			$pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
	}
    
    public function printPRCRejLogV2($log_id = "") {
		$id = (!empty($log_id)?$log_id:$this->input->post('id'));
		$processData = $this->sop->getProcessLogList(['id'=>$id,'single_row'=>1, 'itemDetail' => 1,'processDetail'=>1,'processorDetail'=>1,'operatorDetail'=>1,'single_row'=>1]);
		$logo = base_url('assets/images/logo.png');
		$qrIMG = base_url('assets/uploads/sop/'.$id.'.png');
        $qrText = encodeURL(['id'=>$id,'type'=>'rej_tag']);
        $file_name = $id;
        $qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/sop/',$file_name);
        
        // Build the HTML content for the PDF
        $itemList ='<table class="table" style="font-size:0.5rem;">
                        <tr>
                            <td><img src="'.$logo.'" style="max-height:40px;"></td>
                            <td class="text-right"><b>Material Status Card </b><br><small>(FQC26B(00/01.01.24))</small></td>
                        </tr>
                    </table>
                    <table class="table top-table-border">
                        <tr>
                            <td>Card No. <br><b>'.(!empty($processData->prc_number)?$processData->prc_number:'-').'</b></td>
                            <td>Date <br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Process Unit<br><b>'.(!empty($irData->company_alias)?$irData->company_alias:'-').'</b></td>
                            <td>Department <br><b>Production</b></td>
                        </tr>
                        <tr>
                            <td>Type & Part<br><b>'.(!empty($processData->item_name)?$processData->item_name:'-').'</b></td>
                            <td>Batch/Heat No <br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Material Qty<br><b>'.floatval($processData->rej_found).' (NOS)</b></td>
                            <td>Material Stage<br><b>'.(!empty($processData->process_name)?$processData->process_name:'-').'</b></td>
                        </tr>
                        <tr>
                            <td>Material Process<br><b>'.(!empty($processData->process_name)?$processData->process_name:'-').'</b></td>
                            <td>Material Status<br><b>Log</b></td>
                        </tr>
                        <tr>
                            <td>Operator/Inspector<br><b>'.(!empty($processData->emp_name) ? $processData->emp_name : '-').'</b></td>
                            <td>Supervisor<br><b></b>-</td>
                        </tr>
                        <tr>
                            <td colspan="2">Completed <br><b>'.$processData->completed_process_name.'</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center"><img src="'.$qrIMG.'" style="height:30mm;"></td>
                        </tr>
                    </table>';
        $pdfData = '<div style="text-align:center;float:left;padding:1mm 1mm;rotate: -90;position: absolute;bottom:1mm;width:65mm;height:95mm;">' . $itemList . '</div>';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 68]]);

        $pdfFileName = str_replace(" ", "_", str_replace("/", " ",'rej_log_tag'.$id.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
		
        if(!empty($log_id)){
			$mpdf->Output($pdfFileName, 'I');
        }else{
			$pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
	}

    public function addPrcLog(){
        $data = $this->input->post();
        $this->data['operator_id'] = $data['operator_id'];
        $this->load->view('pos/prc_log_form',$this->data);
    }

    public function getPrcLogDetail(){
        $data = $this->input->post();
        $dUrl = decodeURL($data['scan_id']);
        $html = "";
        if($dUrl->type == 'process_tag'){
            $prsData = $this->sop->getPrcAcceptData(['id'=>$dUrl->id,'single_row'=>1,'processDetail'=>1,'prcDetail'=>1,'itemDetail'=>1,'completedProcessDetail'=>1]);
           
            if(!empty($prsData)){
                $this->data['processData'] = $this->process->getProcess(['id'=>$prsData->accepted_process_id]);
                if($this->data['processData']->process_type == 2){
                    $this->data['dieList'] = $this->dieProduction->getProductionData(['item_id'=>$prsData->item_id,'status'=>3]);
                }

                $prodData = $this->sop->getPendingLogData(['prc_id'=>$prsData->prc_id,'process_id'=>$prsData->accepted_process_id,'completed_process'=>$prsData->completed_process,'trans_type'=>$prsData->trans_type]);
                $pending_production = 0;
                $in_qty = (!empty($prodData->in_qty)?$prodData->in_qty:0);
                $ok_qty = (!empty($prodData->ok_qty)?$prodData->ok_qty:0);
                $rej_found = (!empty($prodData->rej_found)?$prodData->rej_found:0);
                $rw_qty = (!empty($prodData->rw_qty)?$prodData->rw_qty:0);
                $rej_qty = (!empty($prodData->rej_qty)?$prodData->rej_qty:0);
                $pendingReview = $rej_found - $prodData->review_qty;
                $this->data['pending_production'] =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$prodData->ch_qty);
                $this->data['prsData'] = $prsData;
                $this->data['wt_nos'] = 0;
                $this->data['machineList'] = $machineList = $this->item->getItemList(['item_type'=>5]);
                $html = $this->load->view("pos/log_form_view",$this->data,true);
            }
        }
        else{
            $html = '<h4 class="text-danger">Invalid QR-Code Scaned </h4>'.$dUrl->type;
        }
        
        $this->printJson(['status'=>1,'html'=>$html]);
    }

    /** If you change in this function also change in sopDesk also */
    public function savePrcLog(){
        $data = $this->input->post(); 
        $errorMessage = array();$prcProcessData = [];
        
        if (empty($data['prc_id'])){ $errorMessage['general_error'] = "Job Card No. is required.";}
        if (empty($data['process_id'])){ $errorMessage['process_id'] = "Process is required.";}
        if (isset($data['wt_nos']) && empty($data['wt_nos'])){ $errorMessage['wt_nos'] = "Input weight is required.";}
        if (isset($data['wt_nos']) && empty($data['die_ids'])){ $errorMessage['die_ids'] = "Die is required.";}

        if (empty($data['ok_qty']) && empty($data['rej_found'])  && empty($data['without_process_qty'])){
            $errorMessage['production_qty'] = "OK Qty Or Rejection Qty. is required.";
        }else{
            $totalProdQty = (!empty($data['ok_qty']))?$data['ok_qty']:0 ;$totalProdQty += (!empty($data['rej_found'])) ? $data['rej_found'] : 0;$totalProdQty += (!empty($data['without_process_qty'])) ? $data['without_process_qty'] : 0;

            $logData = $this->sop->getPendingLogData(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'completed_process'=>$data['completed_process'],'trans_type'=>$data['trans_type']]);
            $in_qty = $logData->in_qty;
            $ok_qty = !empty($logData->ok_qty)?$logData->ok_qty:0;
            $rej_found = !empty($logData->rej_found)?$logData->rej_found:0;
            $rw_qty = !empty($logData->rw_qty)?$logData->rw_qty:0;
            $rej_qty = !empty($logData->rej_qty)?$logData->rej_qty:0;
            $pendingReview = $rej_found - $logData->review_qty;
           
            $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$logData->ch_qty);
            if($pending_production < $totalProdQty ||  $totalProdQty < 0) :
                $errorMessage['production_qty'] = "Invalid Qty.".$pending_production;
            endif;

        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            //Find Last Log Date (LAst Log date is current start date)
            $lastLog = $this->sop->getLastLogDate(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id']]);
            if(empty($lastLog->last_log_date)){
                //If no log then last accept date is start date
                $lastAccept = $this->sop->getLastAcceptDate(['prc_id'=>$data['prc_id'],'accepted_process_id'=>$data['process_id']]);
                $start_time = !empty($lastAccept->last_accept_date)?$lastAccept->last_accept_date:date("Y-m-d H:i:s");
            }else{
                $start_time = !empty($lastLog->last_log_date)?$lastLog->last_log_date:date("Y-m-d H:i:s");
            }
            
            $end_time = !empty($data['end_time'])?$data['end_time']:date("Y-m-d H:i:s");
            $start = new DateTime($start_time);
            $end = new DateTime($end_time);
            $diff = $start->diff($end);
            $daysInSecs = $diff->format('%r%a')* 24 * 60 * 60; $hoursInSecs = $diff->h * 60 * 60; $minsInSecs = $diff->i * 60;
            $totalPrdseconds = $daysInSecs + $hoursInSecs + $minsInSecs + $diff->s;
            $data['production_time'] = $totalPrdseconds;
            $logData = [
                'id'=>'',
                'trans_type' => $data['trans_type'],
                'prc_id' => $data['prc_id'],
                'process_id' => $data['process_id'],
                'completed_process' => $data['completed_process'],
                'process_from' => $data['process_from'],
                'process_by' =>$data['process_by'],
                'trans_date' => date("Y-m-d"),
                'qty' => (!empty($data['ok_qty'])?$data['ok_qty']:0),
                'rej_found' =>  (!empty($data['rej_found'])?$data['rej_found']:0),
                'operator_id' => ((!empty($data['operator_id']))?$data['operator_id']:(!empty($data['created_by'])?$data['created_by']:'')),
                'wt_nos' => (!empty($data['wt_nos'])?$data['wt_nos']:""),
                'die_ids' => (!empty($data['die_ids'])?implode(",",$data['die_ids']):""),
                'created_by' => (!empty($data['created_by'])?$data['created_by']:''),
				'processor_id' =>$data['processor_id'],
				'production_time' => (!empty($data['production_time'])?$data['production_time']:0),
                'start_time'=>$start_time,
                'end_time'=>$end_time,
                'remark' => (!empty($data['remark'])?$data['remark']:'')
            ];

           $result = $this->sop->savePRCLog($logData);
            if(!empty($data['rej_found']) && $data['rej_found'] > 0){
                $result['rej_print'] = 1;
            }
            $result['log_print'] = 1;
            $this->printJson($result);
        endif;	
	}

    public function addPrcMovement(){
        $data = $this->input->post();
        $this->data['operator_id'] = $data['operator_id'];
        $this->load->view('pos/prc_move_form',$this->data);
    }

    public function getPrcMoveDetail(){
        $data = $this->input->post();
        $dUrl = decodeURL($data['scan_id']);
        $html = "";
        if($dUrl->type == 'log_tag'){
            $prsData = $this->sop->getProcessLogList(['id'=>$dUrl->id, 'itemDetail' => 1,'processDetail'=>1,'processorDetail'=>1,'operatorDetail'=>1,'single_row'=>1,'completedProcessDetail'=>1]);
            $prcProcessData =$this->sop->getPendingMovementData(['prc_id'=>$prsData->prc_id,'process_id'=>trim($prsData->process_id),'completed_process'=>trim($prsData->completed_process),'trans_type'=>$prsData->trans_type]);
            
            $ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
            $movement_qty =!empty($prcProcessData->movement_qty)?$prcProcessData->movement_qty:0;
            $pending_movement = $ok_qty - $movement_qty;
            $processList = $this->sop->getPRCProcessList(['prc_id'=>$prsData->prc_id,'process_id'=>$prsData->process_ids]);
            $processOptions = '<option value="">Select Process</option>';
            if(!empty($processList)){
                foreach($processList AS $row){
                    if($row->process_id != $prsData->process_id){
                        $processOptions .= '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                    }
                }
                $processOptions .= '<option value="0">Production Finish</option>';
            }
            $html = "";
            if(!empty($prsData)){
                
                $html=' <div class="col-md-12 form-group">
                            <table class="table table-bordered">
                                <tr class="bg-light">
                                    <th style="width:33%">Prc No</th>
                                    <td>'.$prsData->prc_number.'</td>
                                </tr>
                                <tr >
                                    <th class="bg-light">Product</th>
                                    <td >'.$prsData->item_name.'</td>
                                </tr>
                                <tr >
                                    <th class="bg-light">Completed Process</th>
                                    <td >'.$prsData->completed_process_name.'</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Currunt Process</th>
                                    <td>'.$prsData->process_name.'</td>
                                </tr>
                                <tr > 
                                    <th class="bg-light">Stock Qty</th>
                                    <td >'.$pending_movement.'</td>
                                </tr>
                            </table>
                        </div>
                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="prc_id" id="prc_id" value="'.$prsData->prc_id.'">
                        <input type="hidden" name="process_id" id="process_id" value="'.$prsData->process_id.'">
                        <input type="hidden" name="completed_process" id="completed_process" value="'.$prsData->completed_process.'">
                        <input type="hidden" name="process_from" id="process_from" value="'.$prsData->process_from.'">
                        <input type="hidden" name="log_qty" id="log_qty" value="'.$prsData->qty.'">
                        <input type="hidden" name="move_from" id="move_from" value="'.$prsData->trans_type.'">
                        ';
                $html.='<div class="row">
                            <div class="col-md-6 form-group">
                                <label for="qty">Move Qty</label>
                                <input type="text" id="qty" name="qty" class="form-control numericOnly req" value="">

                            </div>
                            <div class="col-md-6 form-group">
                                <label for="qty">Next Process</label>
                                <select type="text" id="next_process_id" name="next_process_id" class="form-control select2 req">
                                    '.$processOptions.'
                                </select>
                            </div>
                             <div class="col-md-12 form-group">
                                <label for="remark">Remark</label>
                                <input type="text" id="remark" name="remark" class="form-control" value="">
                            </div>
                        </div>';
            }
        }else{
            $html = '<h4 class="text-danger">Invalid QR-Code Scaned</h4>';
        }
        $this->printJson(['status'=>1,'html'=>$html]);
    }

    public function savePRCMovement(){
		$data = $this->input->post(); 
        $errorMessage = array();
        if (empty($data['prc_id'])){ $errorMessage['general_error'] = "Job Card No. is required.";}
        if ($data['next_process_id'] == ""){ $errorMessage['next_process_id'] = "Process is required.";}
        if (empty($data['qty'])){
            $errorMessage['qty'] = "Qty. is required.";
       	}else{
           
            $prcProcessData = $this->sop->getPendingMovementData(['prc_id'=>$data['prc_id'],'process_id'=>trim($data['process_id']),'completed_process'=>trim($data['completed_process']),'trans_type'=>$data['move_from']]);
            
            $ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
            $movement_qty =!empty($prcProcessData->movement_qty)?$prcProcessData->movement_qty:0;
            $pending_movement = $ok_qty - $movement_qty;
            if( $data['qty'] > $pending_movement ||  $data['qty'] < 0) :
                $errorMessage['qty'] = "Invalid Qty.";
            endif;
            
		}
    
		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $move_type = 1;
			if($data['next_process_id'] > 0 && in_array($data['next_process_id'],explode(",",$data['completed_process']))){
				$move_type = 2;
			}
			$logData = [
				'id'=>'',
				'prc_id' => $data['prc_id'],
				'move_from' => $data['move_from'],
				'move_type' => $move_type,
				'process_id' => $data['process_id'],
				'next_process_id' => $data['next_process_id'],
				'process_from' => $data['process_from'],
				'completed_process' => $data['completed_process'].','.$data['process_id'],
				'trans_date' => date("Y-m-d"),
				'qty' => !empty($data['qty'])?$data['qty']:0,
				'created_by' =>  $data['created_by'],
				'remark' => !empty($data['remark'])?$data['remark']:'',
			];
            $result = $this->sop->savePRCMovement($logData);
            $result['movement_tag'] = 1;
			$this->printJson($result);
		endif;	
	}


    public function getPrcAceptLogHtml(){
        $data = $this->input->post();
        $acceptData = $this->sop->getPrcAcceptData(['created_by'=>$data['operator_id'],'processDetail'=>1,'prcDetail'=>1,'itemDetail'=>1]);
		$html="<div class='card'><div class='card-body'><h5>Accept Detail : </h5><table class='table table-bordered' id='reportTable'>
                    <thead class='thead-info'>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>PRC No</th>
                            <th>Item</th>
                            <th>Process</th>
                            <th>Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead><tbody>";
        if (!empty($acceptData)) :
            $i = 1;
            foreach ($acceptData as $row) :
                if($row->accepted_qty > 0):
                    $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePrcAccept','res_function':'getPrcAcceptResponse','controller':'sopDesk'}";
                    $deleteBtn = '<a class="btn btn-outline-danger btn-sm btn-delete " href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
                    $pUrl = encodeURL(['id'=>$row->id]);
                    $printParam = "{'postData':{'url' : '".$pUrl."'},'call_function' : 'prcProcesstag','controller':'pos'}";
                    $printTag = '<a href="javascript:void(0)" onclick="printBox('.$printParam.')"  class="btn btn-sm btn-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';
                    $html .='<tr class="text-center">
                                <td>' . $i++ . '</td>
                                <td>' . formatDate($row->trans_date). '</td>
                                <td>' . $row->prc_number. '</td>
                                <td>' . $row->item_name. '</td>
                                <td>' . $row->process_name. '</td>
                                <td>' . floatval($row->accepted_qty) . ' </td>
                                <td>' . $printTag.$deleteBtn . '</td>
                            </tr>';
                endif;
            endforeach;
        endif;
        $html .='</tbody></table></div></div>';
		$this->printJson(['status'=>1,'html'=>$html]);
    }

    public function getProductionLogHtml(){
        $data = $this->input->post();
        $acceptData = $this->sop->getProcessLogList(['created_by'=>$data['operator_id'],'itemDetail'=>1,'processDetail'=>1]);
		$html="<div class='card'><div class='card-body'><h5>Production Log Detail : </h5><table class='table table-bordered' id='reportTable'>
                    <thead class='thead-info'>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>PRC No</th>
                            <th>Item</th>
                            <th>Process</th>
                            <th>OK Qty</th>
                            <th>Rej Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead><tbody>";
        if (!empty($acceptData)) :
            $i = 1;
            foreach ($acceptData as $row) :
                    $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePRCLog','res_function':'getPrcLogResponse','controller':'sopDesk'}";
					$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete " href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
                    // $printTag = '<a href="' . base_url('pos/printPRCLog/' . $row->id) . '"  class="btn btn-sm btn-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';
                    $printParam = "{'postData':{'id' : ".$row->id."},'call_function' : 'printPRCLog','controller':'pos'}";
                    $printTag = '<a href="javascript:void(0)" onclick="printBox('.$printParam.')"  class="btn btn-sm btn-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';
                    $rejTag = ''; $rejQty = floatval($row->rej_found);
					if(!empty($rejQty)){
                        $rejPrintParam = "{'postData':{'id' : ".$row->id."},'call_function' : 'printPRCRejLog','controller':'pos'}";
						$rejTag .= '<a href="javascript:void(0)" onclick="printBox('.$rejPrintParam.')" class="btn btn-sm btn-dark waves-effect waves-light mr-1" title="Rejection Tag"><i class="fas fa-print"></i></a>';
					}
                 
                    $html .='<tr class="text-center">
                                <td>' . $i++ . '</td>
                                <td>' . formatDate($row->trans_date). '</td>
                                <td>' . $row->prc_number. '</td>
                                <td>' . $row->item_name. '</td>
                                <td>' . $row->process_name. '</td>
                                <td>' . floatval($row->qty) . ' </td>
                                <td>' . floatval($row->rej_found) . ' </td>
                                <td>' . $rejTag .$printTag.$deleteBtn . '</td>
                            </tr>';
            endforeach;
        endif;
        $html .='</tbody></table></div></div>';
		$this->printJson(['status'=>1,'html'=>$html]);
    }

    public function getProductionMovementHtml(){
        $data = $this->input->post();
        $acceptData = $this->sop->getProcessMovementList(['created_by'=>$data['operator_id'],'itemDetail'=>1,'processDetail'=>1,'nextProcessDetail'=>1]);
		$html="<div class='card'><div class='card-body'><h5>Production Movement Detail : </h5><table class='table table-bordered' id='reportTable'>
                    <thead class='thead-info'>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>PRC No</th>
                            <th>Item</th>
                            <th>Process</th>
                            <th>Next Process</th>
                            <th>Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead><tbody>";
        if (!empty($acceptData)) :
            $i = 1;
            foreach ($acceptData as $row) :
                    $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePRCMovement','res_function':'getPrcLogResponse','controller':'sopDesk'}";
					$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete " href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';

                    $printParam = "{'postData':{'id' : ".$row->id."},'call_function' : 'printPRCMovement','controller':'pos'}";
                    $printTag = '<a href="javascript:void(0)" onclick="printBox('.$printParam.')" class="btn btn-sm btn-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';
                    $regenerateParam = "{'postData':{'id' : ".$row->id."},'modal_id' : 'master-modal-md', 'call_function':'regenerateMoveTag', 'form_id' : 'regenerateMoveTag', 'title' : 'Regenerate Tag ', 'fnsave' : 'printRegeneratedTag','js_store_fn' : 'customStore','savebtn_text':'Generate Tag'}";
                    $regenerateTag = '<a href="javascript:void(0)"  onclick="modalAction('.$regenerateParam.')" class="btn btn-sm btn-primary waves-effect waves-light mr-2" title="Regenerate Tag"><i class="fas fa-recycle"></i></a>';
                    $html .='<tr class="text-center">
                                <td>' . $i++ . '</td>
                                <td>' . formatDate($row->trans_date). '</td>
                                <td>' . $row->prc_number. '</td>
                                <td>' . $row->item_name. '</td>
                                <td>' . $row->process_name. '</td>
                                <td>' . $row->next_process_name. '</td>
                                <td>' . floatval($row->qty) . ' </td>
                                <td>' . $regenerateTag.$printTag.$deleteBtn . '</td>
                            </tr>';
            endforeach;
        endif;
        $html .='</tbody></table></div></div>';
		$this->printJson(['status'=>1,'html'=>$html]);
    }

    public function regenerateMoveTag(){
        $data = $this->input->post();
        $this->data['id'] = $data['id'];
        $this->load->view('pos/regenerate_move_tag',$this->data);
    }

    public function printRegeneratedTag(){
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['tag_qty'])){ $errorMessage['tag_qty'] = "Qty required.";}else{
            $movementData = $this->sop->getProcessMovementList(['id'=>$data['id'],'nextPrcProcessData'=>1,'single_row'=>1]);
            if($data['tag_qty'] > $movementData->qty){
                $errorMessage['tag_qty'] = "Qty is invalid.";
            }
        }
        
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result['status'] = 1;
            $result['id'] = $data['id'];
            $result['tag_qty'] = $data['tag_qty'];
			$this->printJson($result);
		endif;
    }

    public function materialIssue(){
        $data = $this->input->post();
        $this->data['operator_id'] = $data['operator_id'];
        $this->load->view('pos/mt_issue_form',$this->data);
    }

    public function getMaterialIssueDetail(){
        $data = $this->input->post();
        $dUrl = decodeURL($data['scan_id']);
        $html = "";
        if($dUrl->type == 'material_stock_tag'){
            $this->data['tagData'] = $dUrl;
            $this->data['itemData'] = $this->item->getItem(['id'=>$dUrl->item_id]);
            $this->data['locationData'] = $this->storeLocation->getStoreLocation(['id'=>$dUrl->location_id]);
            $this->data['prcList'] = $this->sop->getPrcListFromBom(['bom_item_id'=>$dUrl->item_id,'single_row'=>1]);
            $html = $this->load->view("pos/mt_issue_form_view",$this->data,true);
        }
        else{
            $html = '<h4 class="text-danger">Invalid QR-Code Scaned</h4>';
        }
        
        $this->printJson(['status'=>1,'html'=>$html]);
    }

    public function saveIssuedmaterial(){
        $data = $this->input->post(); 
        $errorMessage = array();
        
        if (empty($data['prc_id'])){ $errorMessage['prc_id'] = "PRC is required.";}
        if (empty($data['qty'])){ $errorMessage['qty'] = "Qty is required.";}
        else{
            if($data['qty'] > $data['tag_qty']){
                $errorMessage['qty'] = "Qty is Greater Than  tag qty";
            }else{
                $stockData = $this->itemStock->getItemStockBatchWise(['location_id'=>$data['location_id'],'batch_no'=>$data['batch_no'],'item_id'=>$data['item_id'],'single_row'=>1]);
                
                $stock_qty = (!empty($stockData)) ? $stockData->qty : 0;
                
                if($data['qty'] > $stock_qty){
                    $errorMessage['qty'] = " Stock not available.";
                }
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $postData = [
                'req_id'=>'',
                'issue_type'=>2,
                'item_id'=>$data['item_id'],
                'prc_id'=>$data['prc_id'],
                'process_id'=>$data['process_id'],
                'ppc_qty'=>$data['ppc_qty'],
                'issue_date'=>date("Y-m-d"),
                'issued_to'=>$data['created_by'],
                'created_by'=>$data['created_by'],
            ];
            $postData['batch_no'][] = $data['batch_no'];
            $postData['heat_no'][] = $data['heat_no'];
            $postData['location_id'][] = $data['location_id'];
            $postData['batch_qty'][] = $data['qty'];
            
            $result = $this->store->saveIssueRequisition($postData);
            $result['prc_id'] = $data['prc_id'];
            $result['item_id'] = $data['item_id'];
            $this->printJson($result);
        endif;	
	}
	
	public function getMtIssueLogHtml(){
        $data = $this->input->post();
        $issueData = $this->store->getMaterialIssueData(['created_by'=>$data['operator_id'],'customWhere'=>'issue_register.prc_id > 0 AND issue_type = 2']);
		$html="<div class='card'><div class='card-body'><h5>Issued Material Detail : </h5><table class='table table-bordered' id='reportTable'>
                    <thead class='thead-info'>
                        <tr>
                            <th>#</th>
                            <th>Issue No</th>
                            <th>Issue Date</th>
                            <th>Batch No</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead><tbody>";
        if (!empty($issueData)) :
            $i = 1;
            foreach ($issueData as $row) :
                $pUrl = encodeURL(['item_id'=>$row->item_id,'prc_id'=>$row->prc_id,'id'=>$row->id]);
                $printParam = "{'postData':{'url' : '".$pUrl."'},'call_function' : 'printMaterialAcceptTag','controller':'pos'}";
                $printTag = '<a class="btn btn-dribbble btn-edit" href="javascript:void(0)" onclick="printBox('.$printParam.')"  datatip="Material Tag Print" flow="down"><i class="fas fa-print" ></i></a>';
               
                $html .='<tr class="text-center">
                                <td>' . $i++ . '</td>
                                <td>'.$row->issue_number.'</td>
                                <td>' . formatDate($row->issue_date). '</td>
                                <td>' . $row->prc_number. '</td>
                                <td>' . $row->item_name. '</td>
                                <td>' . floatval($row->issue_qty) . ' </td>
                                <td>' . $printTag . '</td>
                            </tr>';
            endforeach;
        endif;
        $html .='</tbody></table></div></div>';
		$this->printJson(['status'=>1,'html'=>$html]);
    }
    
    public function stockTag(){
        $data = $this->input->post();
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>'2,3']);
        $this->load->view('pos/stock_tag',$this->data);
    }
    
    public function getProcessorList(){
		$process_by = $this->input->post('process_by');		
		$options = '<option value="0">Select</option>';

		if($process_by == 2){
			$deptList = $this->department->getDepartmentList();
			if(!empty($deptList)){
				foreach($deptList as $row){
					$options .= '<option value="'.$row->id.'">'.$row->name.'</option>';
				}
			}
		}else{
			$machineList = $this->item->getItemList(['item_type'=>5]);
			if(!empty($machineList)){
				foreach($machineList as $row){
					$options .= '<option value="'.$row->id.'">[ '.$row->item_code. ' ] '.$row->item_name.'</option>'; 
				}
			}
		}
		$this->printJson(['status'=>1, 'options'=>$options]);
	}
	
	public function getBatchStockHistory(){
		$data = $this->input->post();
		
		$data['location_not_in'] = [$this->FORGE_STORE->id,$this->SCRAP_STORE->id];
		$data['stock_required'] = 1;
		$data['group_by'] = "location_id,batch_no";
		$data['supplier'] = 1;
        $stockHistory = $this->itemStock->getItemStockBatchWise($data);
		
        $i=1; $tbody =""; 
        foreach($stockHistory as $row):  
            $qcTagParam = ['item_id' => $row->item_id,'batch_no' => $row->batch_no,'heat_no'=> $row->heat_no,'location_id' => $row->location_id,'location_name' => '['.$row->store_name.'] '.$row->location,'qty'=>$row->qty];
            $qcTagUrl = encodeURL($qcTagParam);

            $printParam = "{'postData':{'url' : '".$qcTagUrl."'},'call_function' : 'printMaterialTag','controller':'pos'}";
            $iirTagPrint = '<a onclick="printBox('.$printParam.')"  type="button" class="btn btn-primary" datatip="QC Stock Tag" flow="left" target="_blank"><i class="fas fa-print"></i></a>';   
            $heatDetail = implode("<br>",[$row->heat_no,$row->party_name]);
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>'.$row->store_name.' - '.$row->location.'</td>
                <td>'.$heatDetail.'</td>
                <td>'.$row->batch_no.'</td>
                <td>'.$row->qty.'</td>
                <td>'.$iirTagPrint.'</td>
            </tr>';
        endforeach;

        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

    public function printPRCMovementOld($log_id = "",$tag_qty = "") {
        $id = (!empty($log_id)?$log_id:$this->input->post('id'));
        $tag_qty = (!empty($tag_qty)?$tag_qty:$this->input->post('tag_qty'));
		$movementData = $this->sop->getProcessMovementList(['id'=>$id,'single_row'=>1,'itemDetail'=>1,'processDetail'=>1,'nextProcessDetail'=>1,'prcDetail'=>1,'completedProcessDetail'=>1]);

		if (!empty($movementData->next_process_id)) {
            $mtitle = 'Process Tag';
            $revno = date('d.m.Y <br> h:i:s A');
        } else {
            $mtitle = 'Final Inspection	OK Material';
            $revno = 'F QA 25<br>(01/01.10.2021)';
        }

		$logo = base_url('assets/images/logo.png');
		$title = 'Movement Tag';
		$qrIMG = "";
		$tag_qty = !empty($tag_qty)?$tag_qty:$movementData->qty;
		$qrText = encodeURL(['id'=>$movementData->id,'tag_qty'=>$tag_qty,'type'=>'move_tag']);
		$file_name = $movementData->id;
		$qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/movement_tag/',$file_name);
		$qrIMG =  '<td  rowspan="4" colspan="2" class="text-center" style="padding:2px;"><img src="'.$qrIMG.'" style="height:30mm;"> Tag No : '.$movementData->tag_no.'</td>';
		
		$itemList = '<table class="table tag_print_table" style="font-size:0.7rem;">
							<tr>
								<td style="width:23%;"><img src="' . $logo . '" style="height:39px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:47%;">' . $title . '<br><small>('.$movementData->process_name.')</small></td>
								'.$qrIMG.'
							</tr>
							<tr class="text-left">
								<td class="bg-light">Batch No</td>
								<th>' . $movementData->prc_number . '</th>
							</tr>
							<tr class="text-left">
								<td class="bg-light">Qty</td>
								<th>' . floatval($tag_qty) . '</th>
							</tr>
							<tr class="text-left">
								<td class="bg-light">Date</td>
								<th>' . formatDate($movementData->trans_date) . '</th>
							</tr>
							<tr class="text-left">
								<td class="bg-light">Part</td>
								<th colspan="3">' . (!empty($movementData->item_code) ? '['.$movementData->item_code.'] ' : '') . $movementData->item_name . '</th>
							</tr>
							<tr class="text-left">
								<td class="bg-light">Next Process</td>
								<th colspan="3">' . $movementData->next_process_name . '</th>
							</tr>
							<tr class="text-left">
								<td class="bg-light">Remark</td>
								<th colspan="3">' . $movementData->remark . '</th>
							</tr>
                            <tr class="text-left">
								<td class="bg-light">Completed Process</td>
								<th colspan="3">' . $movementData->completed_process_name . '</th>
							</tr>
						</table>';

		$pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">'.$itemList.'</div>';
        
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 68]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ", 'movement_tag'.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        if(!empty($log_id)){
            $mpdf->Output($pdfFileName, 'I');
        }else{
            $pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
	}

    public function printPRCMovement($log_id = "",$tag_qty = "") {
        $id = (!empty($log_id)?$log_id:$this->input->post('id'));
        $tag_qty = (!empty($tag_qty)?$tag_qty:$this->input->post('tag_qty'));
		$movementData = $this->sop->getProcessMovementList(['id'=>$id,'single_row'=>1,'itemDetail'=>1,'processDetail'=>1,'nextProcessDetail'=>1,'prcDetail'=>1,'completedProcessDetail'=>1]);

		if (!empty($movementData->next_process_id)) {
            $mtitle = 'Process Tag';
            $revno = date('d.m.Y <br> h:i:s A');
        } else {
            $mtitle = 'Final Inspection	OK Material';
            $revno = 'F QA 25<br>(01/01.10.2021)';
        }

		$logo = base_url('assets/images/logo.png');
		$title = 'Movement Tag';
		$qrIMG = "";
		$tag_qty = !empty($tag_qty)?$tag_qty:$movementData->qty;
		$qrText = encodeURL(['id'=>$movementData->id,'tag_qty'=>$tag_qty,'type'=>'move_tag']);
		$file_name = $movementData->id;
		$qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/movement_tag/',$file_name);
		// Build the HTML content for the PDF
        $itemList ='<table class="table" style="font-size:0.5rem;">
                        <tr>
                            <td><img src="'.$logo.'" style="max-height:40px;"></td>
                            <td class="text-right"><b>Material Status Card </b><br><small>(FQC26B(00/01.01.24))</small></td>
                        </tr>
                    </table>
                    <table class="table top-table-border">
                        <tr>
                            <td>Card No. <br><b>'.(!empty($movementData->prc_number)?$movementData->prc_number:'-').'</b></td>
                            <td>Date <br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Process Unit<br><b>'.(!empty($movementData->company_alias)?$movementData->company_alias:'-').'</b></td>
                            <td>Department <br><b>Production</b></td>
                        </tr>
                        <tr>
                            <td>Type & Part<br><b>'.(!empty($movementData->item_name)?$movementData->item_name:'-').'</b></td>
                            <td>Batch/Heat No <br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Material Qty<br><b>'.floatval((empty($tag_qty)?$movementData->qty:$tag_qty)).' (NOS)</b></td>
                            <td>Material Stage<br><b>'.(!empty($movementData->process_name)?$movementData->process_name:'-').'</b></td>
                        </tr>
                        <tr>
                            <td>Material Process<br><b>'.(!empty($movementData->process_name)?$movementData->process_name:'-').'</b></td>
                            <td>Material Status<br><b>Movement</b></td>
                        </tr>
                        <tr>
                            <td>Operator/Inspector<br><b>-</b></td>
                            <td>Supervisor<br><b></b>-</td>
                        </tr>
                        <tr>
                            <td colspan="2">Completed <br><b>'.$movementData->completed_process_name.'</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center"><img src="'.$qrIMG.'" style="height:30mm;"></td>
                        </tr>
                    </table>';
        $pdfData = '<div style="text-align:center;float:left;padding:1mm 1mm;rotate: -90;position: absolute;bottom:1mm;width:65mm;height:95mm;">' . $itemList . '</div>';
		// print_r($pdfData);exit;
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 68]]);

        $pdfFileName = str_replace(" ", "_", str_replace("/", " ", 'movement_tag'.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        if(!empty($log_id)){
            $mpdf->Output($pdfFileName, 'I');
        }else{
            $pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
	}
    public function printMaterialTagOld($tag_url = "") {
        $url = (!empty($tag_url)?$tag_url:$this->input->post('url'));
        $data = decodeURL($url);
		$data->id = (!empty($data->id)?$data->id:0);
		$itemData = $this->item->getItem(['id'=>$data->item_id]); 
		$logo = base_url('assets/images/logo.png');
		$qrIMG = "";
		$qrText = encodeURL(['item_id'=>$data->item_id,'batch_no'=>$data->batch_no,'heat_no'=>$data->heat_no,'location_id'=>$data->location_id,'qty'=>$data->qty,'type'=>'material_stock_tag']);
		$file_name = 'mtr_stock_tag'.str_replace(" ", "_", str_replace("/", " ", $data->item_id.$data->batch_no.$data->heat_no.$data->location_id));
		$qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/iir_qr/',$file_name);
		$qrIMG =  '<td  rowspan="5" class="text-right" style="padding:2px;"><img src="'.$qrIMG.'" style="height:25mm;"></td>';
		
		$itemList = '<table class="table tag_print_table text-center">
		                <tr>
                            <td style="width:23%;"><img src="' . $logo . '" style="height:39px;"></td>
                            <td class="org_title text-center" style="font-size:1rem;width:50%;">Material Stock Tag</td>
                            '.$qrIMG.'
                        </tr>
	                	<tr>
	                	    <td class="bg-light">Batch No</td>
	                	    <td class="bg-light">Location</td>
						</tr>
						<tr>
						    <th>' . (!empty($data->heat_no)?$data->heat_no : $data->batch_no). '</th>
						    <th>' . (!empty($data->location_name) ? $data->location_name : ""). '</th>
						</tr>
						<tr>
	                	    <td class="bg-light" colspan="2">Qty</td>
	                	</tr>
						<tr>
							<th colspan="2">' . floatval($data->qty).' '.$itemData->uom. '</th>
						</tr>
						<tr>
	                	    <td class="bg-light" colspan="3">Part</td>
	                	</tr>
						<tr>    
							<th colspan="3">' . (!empty($itemData->item_code) ? '['.$itemData->item_code.'] ' : '') . $itemData->item_name . '</th>
						</tr>
					</table>';

		$pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">' . $itemList . '</div>';
		
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ", 'material_stock_tag'.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        if(!empty($tag_url)){
            $mpdf->Output($pdfFileName, 'I');
        }else{
            $pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
        
	}

    public function printMaterialTag($tag_url = "") {
        $url = (!empty($tag_url)?$tag_url:$this->input->post('url'));
        $data = decodeURL($url);
		$data->id = (!empty($data->id)?$data->id:0);
		$itemData = $this->item->getItem(['id'=>$data->item_id]); 
		$logo = base_url('assets/images/logo.png');
		$qrIMG = "";
		$qrText = encodeURL(['item_id'=>$data->item_id,'batch_no'=>$data->batch_no,'heat_no'=>$data->heat_no,'location_id'=>$data->location_id,'qty'=>$data->qty,'type'=>'material_stock_tag']);
		$file_name = 'mtr_stock_tag'.str_replace(" ", "_", str_replace("/", " ", $data->item_id.$data->batch_no.$data->heat_no.$data->location_id));
		$qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/iir_qr/',$file_name);
		
		$itemList ='<table class="table" style="font-size:0.5rem;">
                        <tr>
                            <td><img src="'.$logo.'" style="max-height:40px;"></td>
                            <td class="text-right"><b>Material Status Card </b><br><small>(FQC26B(00/01.01.24))</small></td>
                        </tr>
                    </table>
                    <table class="table top-table-border">
                        <tr>
                            <td>Card No. <br><b>'.(!empty($data->batch_no)?$data->batch_no:'-').'</b></td>
                            <td>Date <br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Process Unit<br><b>'.(!empty($irData->company_alias)?$irData->company_alias:'-').'</b></td>
                            <td>Department <br><b>'. (!empty($data->location_name) ? $data->location_name : "").'</b></td>
                        </tr>
                        <tr>
                            <td>Customer <br><b>-</b></td>
                            <td>Supplier <br><b>'.(!empty($mtrData->party_name)?$mtrData->party_name:'-').'</b></td>
                        </tr>
                        <tr>
                            <td>Type & Part<br><b>'.(!empty($itemData->item_name)?$itemData->item_name:'-').'</b></td>
                            <td>Batch/Heat No <br><b>'.(!empty($data->heat_no)?$data->heat_no:'-').'</b></td>
                        </tr>
                        <tr>
                            <td>Material Qty<br><b>'.$data->qty.' ('.$itemData->uom.')</b></td>
                            <td>Material Stage<br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Material Process<br><b>Store</b></td>
                            <td>Material Status<br><b>-</b></td>
                        </tr>
                        <tr>
                            <td>Operator/Inspector<br><b>-</b></td>
                            <td>Supervisor<br><b></b>-</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center"><img src="'.$qrIMG.'" style="height:30mm;"></td>
                        </tr>
                    </table>';
        $pdfData = '<div style="text-align:center;float:left;padding:1mm 1mm;rotate: -90;position: absolute;bottom:1mm;width:65mm;height:95mm;">' . $itemList . '</div>';
		
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 68]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ", 'material_stock_tag'.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        if(!empty($tag_url)){
            $mpdf->Output($pdfFileName, 'I');
        }else{
            $pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
        
	}

    public function printMaterialAcceptTagOld($tag_url = "") {
        $url = (!empty($tag_url)?$tag_url:$this->input->post('url'));
		$data = decodeURL($url);
		
		$mtitle = (!empty($data->title)?$data->title:"TAG PRINT");
		$data->id = (!empty($data->id)?$data->id:0);
		$mtrData = $this->store->getMaterialIssueData(['prc_id'=>$data->prc_id,'item_id'=>$data->item_id,'id'=>$data->id,'single_row'=>1,'prcHeatData'=>1]); 
		$logo = base_url('assets/images/logo.png');
		$qrIMG = "";
		$qrText = encodeURL(['prc_id'=>$data->prc_id,'process_id'=>$mtrData->process_id,'item_id'=>$data->item_id,'id'=>$data->id,'qty'=>floatval($mtrData->issue_qty),'type'=>'material_tag']);
		$file_name = 'mtr_tag'.$data->prc_id;
		$qrIMG =base_url().$this->getQRCode($qrText,'assets/uploads/movement_tag/',$file_name);
		$qrIMG =  '<td  rowspan="5" class="text-right" style="padding:2px;"><img src="'.$qrIMG.'" style="height:25mm;"></td>';
		
		$topSectionO = '<table class="table">
							<tr>
								<td style="width:20%;"><img src="' . $logo . '" style="height:40px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%;">Material Tag<br><small>('.$mtrData->process_name.(!empty($data->id) ? ' - '.$mtrData->issue_number : '').')</small></td>
							</tr>
						</table>';

		$itemList = '<table class="table tag_print_table text-center">
		                <tr>
                            <td style="width:23%;"><img src="' . $logo . '" style="height:39px;"></td>
                            <td class="org_title text-center" style="font-size:1rem;width:50%;">Material Tag<br><small>('.$mtrData->process_name.(!empty($data->id) ? ' - '.$mtrData->issue_number : '').')</small></td>
                            '.$qrIMG.'
                        </tr>
	                	<tr>
	                	    <td class="bg-light" colspan="2">Batch No</td>
						</tr>
						<tr>
						    <th colspan="2">' . (!empty($mtrData->prc_number) ? $mtrData->prc_number : '') . '</th>
						</tr>
						<tr>
	                	    <td class="bg-light" colspan="2">Qty</td>
	                	</tr>
						<tr>
							<th colspan="2">' . (!empty($mtrData->issue_qty) ? floatVal($mtrData->issue_qty) : '') . '</th>
						</tr>
						<tr>
	                	    <td class="bg-light" colspan="2">Part</td>
	                	    <td class="bg-light">Date</td>
	                	</tr>
						<tr>    
							<th colspan="2">' . (!empty($mtrData->item_code) ? '['.$mtrData->item_code.'] ' : '') . $mtrData->item_name . '</th>
							<th>' . (!empty($mtrData->issue_date) ? formatDate($mtrData->issue_date) : '') . '</th>
						</tr>
						
					</table>';

		$pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">' . $itemList . '</div>';
		
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ",'material_accept_tag'.time())) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        if(!empty($tag_url)){
            $mpdf->Output($pdfFileName, 'I');
        }else{
            $pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
	}

    public function printMaterialAcceptTag($tag_url = ""){
        $url = (!empty($tag_url)?$tag_url:$this->input->post('url'));
		$data = decodeURL($url);
		
		$mtitle = (!empty($data->title)?$data->title:"TAG PRINT");
		$data->id = (!empty($data->id)?$data->id:0);
		$mtrData = $this->store->getMaterialIssueData(['prc_id'=>$data->prc_id,'item_id'=>$data->item_id,'id'=>$data->id,'single_row'=>1,'prcHeatData'=>1]); 
		$logo = base_url('assets/images/logo.png');
		$qrIMG = "";
		$qrText = encodeURL(['prc_id'=>$data->prc_id,'process_id'=>$mtrData->process_id,'item_id'=>$data->item_id,'id'=>$data->id,'qty'=>floatval($mtrData->issue_qty),'type'=>'material_tag']);
		$file_name = 'mtr_tag'.$data->prc_id;
		$qrIMG =base_url().$this->getQRCode($qrText,'assets/uploads/movement_tag/',$file_name);
		

        $itemList ='<table class="table" style="font-size:0.5rem;">
                        <tr>
                            <td><img src="'.$logo.'" style="max-height:40px;"></td>
                            <td class="text-right"><b>Material Status Card </b><br><small>(FQC26B(00/01.01.24))</small></td>
                        </tr>
                    </table>
                    <table class="table top-table-border">
                        <tr>
                            <td>Card No. <br><b>'.(!empty($mtrData->issue_number)?$mtrData->issue_number:'-').'</b></td>
                            <td>Date <br><b>'.(!empty($mtrData->issue_date)?formatDate($mtrData->issue_date):'-').'</b></td>
                        </tr>
                        <tr>
                            <td>Process Unit<br><b>'.(!empty($irData->company_alias)?$irData->company_alias:'-').'</b></td>
                            <td>Department <br><b>Store</b></td>
                        </tr>
                        <tr>
                            <td>Customer <br><b>-</b></td>
                            <td>Supplier <br><b>'.(!empty($mtrData->party_name)?$mtrData->party_name:'-').'</b></td>
                        </tr>
                        <tr>
                            <td>Type & Part<br><b>'.(!empty($mtrData->item_name)?$mtrData->item_name:'-').'</b></td>
                            <td>Batch/Heat No <br><b>'.(!empty($mtrData->heat_no)?$mtrData->heat_no:'-').'</b></td>
                        </tr>
                        <tr>
                            <td>Material Qty<br><b>'.$mtrData->issue_qty.' ('.$mtrData->uom.')</b></td>
                            <td>Material Stage<br><b>Production</b></td>
                        </tr>
                        <tr>
                            <td>Material Process<br><b>'.$mtrData->process_name.'</b></td>
                            <td>Material Status<br><b>Issue</b></td>
                        </tr>
                        <tr>
                            <td>Operator/Inspector<br><b>-</b></td>
                            <td>Supervisor<br><b></b>-</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center"><img src="'.$qrIMG.'" style="height:30mm;"></td>
                        </tr>
                    </table>';
        $pdfData = '<div style="text-align:center;float:left;padding:1mm 1mm;rotate: -90;position: absolute;bottom:1mm;width:65mm;height:95mm;">' . $itemList . '</div>';

        
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ",'material_accept_tag'.time())) . '.pdf';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 68]]);
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',1,1,1,1,1,1);
		$mpdf->WriteHTML($pdfData);
        if(!empty($tag_url)){
            $mpdf->Output($pdfFileName, 'I');
        }else{
            $pdfOutputPath = 'assets/uploads/sop/' . $pdfFileName;
            $mpdf->Output($pdfOutputPath, 'F'); 
            $pdfUrl = base_url($pdfOutputPath);
            $this->printJson(['status'=>1,'url'=>$pdfUrl]);
        }
    }

    
}
?>