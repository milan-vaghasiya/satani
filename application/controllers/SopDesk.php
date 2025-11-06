<?php
class SopDesk extends MY_Controller{

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "SOP DESK";
		$this->data['headData']->controller = "sopDesk";
	}

    public function index(){
        $this->data['tableHeader'] = getProductionDtHeader('prc');
        $this->load->view('sop_desk/index',$this->data);
    }

	
    public function getDTRows($status = 1){
		$data = $this->input->post();$data['status'] = $status;
		if($status == 0){
			$result = $this->sop->getPrcBatchDTRows($data);
		}else{
			$result = $this->sop->getDTRows($data);
		}
        
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;   
			if($status == 0){
				$sendData[] = getPrcBatchIssueData($row);
			}else{
				$sendData[] = getPRCData($row);
			}       
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPrc(){
		$data = $this->input->post();
        $this->data['prc_prefix'] = 'PRC/'.$this->shortYear.'/';
        $this->data['prc_no'] = $this->sop->getNextPRCNo();
		if(!empty($data['batch_id'])){
			$this->data['batch_id'] = $data['batch_id'];
			$this->data['itemList'] = $this->item->getProductKitData(['ref_item_id'=>$data['item_id'],'select' =>'product.id,product.item_code,product.item_name']);
			
		}else{
			$this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		}
		
        $this->load->view('sop_desk/prc_form',$this->data);
	}

    public function savePRC(){
		$data = $this->input->post();
		
        $errorMessage = array();
       
        if (empty($data['item_id'])){ $errorMessage['item_id'] = "Product is required."; }
        if (empty($data['tool_method'])){ $errorMessage['tool_method'] = "Method is required."; }
        if (empty($data['qty']) || $data['qty'] < 0){ $errorMessage['qty'] = "Quantity is required."; }
        if (empty($data['prc_date'])){  $errorMessage['prc_date'] = "PRC Date is required."; }
		
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$postData = [
				'id'=>$data['id'],
				'prc_no'=>$data['prc_no'],
				'prc_date'=>$data['prc_date'],
				'item_id'=>$data['item_id'],
				'prc_qty'=>$data['qty'],
				'target_date'=>$data['target_date'],
				'prc_number'=>$data['prc_number'],
				'batch_id'=>$data['batch_id'],
				'tool_method'=>$data['tool_method']
			];
            $this->printJson($this->sop->savePRC($postData));
        endif;
	}

    public function edit(){
		$data = $this->input->post();
		$this->data['dataRow']  = $this->sop->getPRCDetail(['id'=>$data['id']]);
		$batchData = $this->store->getPrcBatchDetail(['id'=>$this->data['dataRow']->batch_id,'single_row'=>1]);
		$this->data['itemList'] = $this->item->getProductKitData(['ref_item_id'=>$batchData->item_id,'select' =>'product.id,product.item_code,product.item_name']);
		$this->data['methodList'] = $this->dieMaster->getDieData(['item_id'=>$this->data['dataRow']->item_id,'group_by'=>'die_master.tool_method']);
        $this->load->view('sop_desk/prc_form',$this->data);
	}

	public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deletePRC($id));
        endif;
    }
    public function setPrcProcesses(){
		$data = $this->input->post();
		$this->data['prcData']  = $this->sop->getPRCDetail(['id'=>$data['id']]);
		$this->data['processList'] = $this->item->getProductProcessList(['item_id'=>$data['item_id'],'order_process_ids'=>$this->data['prcData']->process_ids,'is_active' => 1]);
        $this->data['itemKitData'] = $this->item->getProductKitData(['item_id'=>$data['item_id'],'group_by'=>'item_kit.process_id','not_in_item_type'=>9]);
		$this->data['acceptData'] = $this->sop->getPRCProcessList(['prc_id'=>$data['id'],'process_id'=>$this->data['prcData']->process_ids,'inwardDetail'=>1]);
		$this->load->view('sop_desk/process_form',$this->data);
	}

    public function startPRC(){
		$data = $this->input->post();
		
		if (empty($data['first_process']) && $data['production_type'] == 1){ $errorMessage['first_process'] = "Initial Stage is required."; }
		if (empty($data['process'])){ $errorMessage['process_error'] = "Process required."; }
		

		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else:
			//If Fix Production then First process is first process of selected process_ids
			if(!in_array($data['first_process'],$data['process'])){
				$data['process'][] = $data['first_process'];
			}
			$data['process_ids'] = implode(",",$data['process']);
			$this->printJson($this->sop->startPRC($data));
        endif;
	}

    public function prcDetail($prc_id){
		$this->data['prc_id'] = $prc_id;
        $this->load->view('sop_desk/prc_view',$this->data);
	}

    public function getPRCDetail(){
        $postData = $this->input->post();
		$prcDetail ='';$prcMaterial ='';$processDetail ='';
		
		$prcData = $this->data['prcData'] = $this->sop->getPRCDetail(['id'=>$postData['id'],'itemDetail'=>1]);
		
		if(!empty($prcData)){
    		$this->data['status'] = (!empty($prcData->status)) ? $prcData->status : 1;
    		
    		$this->data['prcProcessData'] = $this->sop->getPRCProcessList(['prc_id'=>$postData['id'],'process_id'=>$prcData->process_ids,'inwardDetail'=>1,'acceptDetail'=>1,'movementDetail'=>1,'logDetail'=>1,'rejReviewDetail'=>1,'challanDetail'=>1]);
			
			$this->data['materialData'] = $this->sop->getPrcHeat(['prc_id'=>$postData['id'],'itemDetail'=>1]);
			
    		$prcDetail = $this->load->view('sop_desk/prc_detail',$this->data,true);
    		
    		$prcMaterial = $this->load->view('sop_desk/prc_material',$this->data,true);
    		
    		$processDetail = $this->load->view('sop_desk/prc_process',$this->data,true);
		}
        $this->printJson(['prcDetail'=>$prcDetail,'prcMaterial'=>$prcMaterial,'processDetail'=>$processDetail]);
    }

	public function productionProcess(){
		$this->data['headData']->pageTitle = "Production Process";
		$this->data['headData']->pageUrl = "sopDesk/productionProcess";
		$this->data['processList'] = $this->sop->getSopProcessList();
        $this->load->view('sop_desk/production_process',$this->data);
	}

	public function productionLog($process_id){
		$this->data['headData']->pageTitle = "Production Log";
		$this->data['headData']->pageUrl = "sopDesk/productionProcess";
		$this->data['tableHeader'] = getProductionDtHeader('prcInward');
		
		$this->data['process_id'] = $process_id;
		$this->data['processData'] = $this->process->getProcess(['id'=>$process_id]);
        $this->load->view('sop_desk/production_log',$this->data);
	}


	public function getLogDTRows($process_id,$tab_type="prcInward"){
        $data = $this->input->post();
		$data['process_id'] = $process_id;
		if($tab_type=="prcInward"){
			$result = $this->sop->getInwardDTRow($data);
			
		}elseif($tab_type=="prcLog"){
			$result = $this->sop->getLogDTRows($data);
		}elseif($tab_type=="prcMovement"){
			$result = $this->sop->getMovementDTRows($data);
		}
       
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
			if($tab_type=="prcInward"){
				$sendData[] = getPrcInwardData($row); 
			}elseif($tab_type=="prcLog"){
				$sendData[] = getPRCLogData($row);
			}elseif($tab_type=="prcMovement"){
				$sendData[] = getPRCMovementData($row);
			}
			
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
	public function prcAccept(){
		$data = $this->input->post();
		$this->data['accepted_process_id'] = $data['accepted_process_id'];
		$this->data['process_from'] = $data['process_from'];
		$this->data['prc_id'] = $data['prc_id'];
		$this->data['completed_process'] = $data['completed_process'];
		$this->data['trans_type'] = !empty($data['trans_type'])?$data['trans_type']:1;
		$this->data['movement_type'] = $data['movement_type'];
		$this->data['prc_number'] = $data['prc_number'];
		$this->data['item_name'] = $data['item_name'];
		$this->load->view('sop_desk/accept_prc_qty',$this->data);
	}

	public function saveAcceptedQty(){
		$data = $this->input->post(); 
		$errorMessage = array();
        if (empty($data['accepted_process_id'])){ $errorMessage['general_error'] = "Prc Process required.";}
        if (empty($data['accepted_qty'])){ $errorMessage['accepted_qty'] = "Qty required.";}
        $acceptedQty = !empty($data['accepted_qty'])?$data['accepted_qty']:0;
		$prcProcessData =$this->sop->getPendingAcceptData(['process_id'=>$data['accepted_process_id'],'prc_id'=>$data['prc_id'],'completed_process'=>$data['completed_process'],'trans_type'=>$data['trans_type']]); 
		$pending_accept =$prcProcessData->inward_qty - $prcProcessData->accept_qty;
		if($acceptedQty > $pending_accept){
			$errorMessage['accepted_qty'] = "Accept Quantity is Invalid.";
		}
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result = $this->sop->saveAcceptedQty($data);
			$this->printJson($result);
		endif;
	}

	public function getPRCAcceptHtml(){
        $data = $this->input->post();
        $acceptData = $this->sop->getPrcAcceptData(['prc_id'=>$data['prc_id'],'accepted_process_id'=>$data['accepted_process_id'],'process_from'=>$data['process_from'],'completed_process'=>$data['completed_process'],'trans_type'=>$data['trans_type'],'processDetail'=>1,'prcDetail'=>1,'itemDetail'=>1]);
		$html="";
        if (!empty($acceptData)) :
            $i = 1;
            foreach ($acceptData as $row) :
                if($row->accepted_qty > 0):
                    $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePrcAccept','res_function':'getPrcAcceptResponse','controller':'sopDesk'}";
                    $deleteBtn = '<a class="btn btn-outline-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
                    $pUrl = encodeURL(['id'=>$row->id]);
                    $printParam = "{'url' : '".$pUrl."'}";
                    $printTag = '<a  href="' . base_url('pos/prcProcesstag/' . $pUrl) . '"  class="btn btn-sm btn-info waves-effect waves-light mr-2" title="Print" target="_blank"><i class="fas fa-print"></i></a>';
                    $html .='<tr class="text-center">
                                <td>' . $i++ . '</td>
                                <td>' . formatDate($row->trans_date). '</td>
                                <td>' . floatval($row->accepted_qty) . ' </td>
                                <td>' . $printTag.$deleteBtn . '</td>
                            </tr>';
                endif;
            endforeach;
        endif;
		$prcProcessData =$this->sop->getPendingAcceptData(['process_id'=>$data['accepted_process_id'],'prc_id'=>$data['prc_id'],'completed_process'=>$data['completed_process'],'trans_type'=>$data['trans_type']]); 
		$pending_accept =$prcProcessData->inward_qty - $prcProcessData->accept_qty;
		$this->printJson(['status'=>1,'tbodyData'=>$html,'pending_accept_qty'=>$pending_accept]);
    }

	public function prcLog(){
		$data = $this->input->post();
		if(!empty($data['challan_id'])){
			$this->data['challan_id'] = $data['challan_id'];
			$this->data['ref_trans_id'] = $data['ref_trans_id'];
			$this->data['process_by'] = $data['process_by'];
			$this->data['processor_id'] = $data['processor_id'];
		}
		$this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
		$this->data['operatorList'] = $this->employee->getEmployeeList();
		$this->data['processData'] = $this->process->getProcess(['id'=>$data['process_id']]);
		if($this->data['processData']->process_type == 2){
			$prcData = $this->sop->getPRCDetail(['id'=>$data['prc_id']]);
			$this->data['dieList'] = $this->dieProduction->getProductionData(['item_id'=>$prcData->item_id,'status'=>3]);
		}
		$this->data['trans_type'] = (!empty($data['trans_type']))?$data['trans_type']:1;
		$this->data['process_from'] = $data['process_from'];
		$this->data['process_id'] = $data['process_id'];
		$this->data['completed_process'] = $data['completed_process'];
		$this->data['prc_id'] = $data['prc_id'];
		$this->data['movement_type'] = $data['movement_type'];
		$this->data['prc_number'] = $data['prc_number'];
		$this->data['item_name'] = $data['item_name'];
		$this->load->view('sop_desk/prc_log_form',$this->data);
	}

	public function getPRCLogHtml(){
		$data = $this->input->post();
		$data['itemDetail'] = 1;
		$data['processDetail'] = 1;
		$data['processDetail'] = 1;
		$data['processorDetail'] = 1;
		$data['operatorDetail'] = 1;
		if($data['process_id'] == 2){
			$data['fir_data'] = 1;
		}
        $logData = $this->sop->getProcessLogList($data);
		
		$html="";
        if (!empty($logData)) :
            $i = 1;
            foreach ($logData as $row) :
                    $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePRCLog','res_function':'getPrcLogResponse','controller':'sopDesk'}";
					$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
                    $printTag = '<a href="' . base_url('pos/printPRCLog/' . $row->id) . '"  class="btn btn-sm btn-info waves-effect waves-light mr-2" title="Print" target="_blank"><i class="fas fa-print"></i></a>';
                  
					$challan_file = (!empty($row->challan_file))? '<a href="' . base_url('assets/uploads/prc_log_attch/' . $row->challan_file) . '" target="_blank" class="btn btn-sm btn-outline-warning mr-1" title="Challan"><i class="fas fa-download"></i></a>':'';

                    $rejTag = ''; $rejQty = floatval($row->rej_found);
					if(!empty($rejQty)){
                        $rejPrintParam = "{'postData':{'id' : ".$row->id."},'call_function' : 'printPRCRejLog','controller':'pos'}";
						$rejTag .= '<a href="javascript:void(0)" onclick="printBox('.$rejPrintParam.')" class="btn btn-sm btn-dark waves-effect waves-light mr-1" title="Rejection Tag"><i class="fas fa-print"></i></a>';
					}
					if($data['process_id'] == 2){
						$fir_print = '<a href="' . base_url('sopDesk/printFinalInspection/' . $row->inspection_id) . '" target="_blank" class="btn btn-sm btn-success waves-effect waves-light mr-1" title="Inspection Tag"><i class="fas fa-print"></i></a>';
						$html .='<tr class="text-center">
									<td>' . $i++ . '</td>
									<td>' . formatDate($row->trans_date). '</td>
									<td>' . $row->sampling_qty . '</td>
									<td>' . floatval($row->qty) . '</td>
									<td>' . floatval($row->rej_found) . '</td>
									<td>' .$fir_print. $rejTag.' '.$deleteBtn . '</td>
								</tr>';
					}else{
						$html .='<tr class="text-center">
									<td>' . $i++ . '</td>
									<td>'.(($row->trans_type == 1)?'Regular':'Rework').'</td>
									<td>' . formatDate($row->trans_date). '</td>
									<td>' . floatval($row->production_time) . ' </td>
									<td>'.$row->processor_name.'</td>
									<td>' . floatval($row->qty) . ' </td>
									<td>' . floatval($row->rej_found) . ' </td>
									<td>' . $row->remark . ' </td>
									<td>'.$row->emp_name.'</td>
									<td>' . $challan_file . $rejTag .$printTag.$deleteBtn . '</td>
								</tr>';
					}
            endforeach;
        endif;
       
		$logData = $this->sop->getPendingLogData(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'completed_process'=>$data['completed_process'],'trans_type'=>$data['trans_type']]);
		$in_qty = $logData->in_qty;
		$ok_qty = !empty($logData->ok_qty)?$logData->ok_qty:0;
		$rej_found = !empty($logData->rej_found)?$logData->rej_found:0;
		$rw_qty = !empty($logData->rw_qty)?$logData->rw_qty:0;
		$rej_qty = !empty($logData->rej_qty)?$logData->rej_qty:0;
		$pendingReview = $rej_found - $logData->review_qty;
		
		$pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$logData->ch_qty);

		$this->printJson(['status'=>1,'tbodyData'=>$html,'pendingQty' => $pending_production]);
	}

	public function prcMovement(){
		$data = $this->input->post();
		$this->data['dataRow'] = $dataRow = $this->sop->getPRCProcessList(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'single_row'=>1]);
		$this->data['processList'] = $this->sop->getPRCProcessList(['prc_id'=>$dataRow->prc_id,'process_id'=>$dataRow->process_ids]);
       
		$this->data['trans_type'] = (!empty($data['trans_type']))?$data['trans_type']:1;
		$this->data['process_from'] = ((!empty($data['process_from']))?$data['process_from']:0);
		$this->data['completed_process'] = $data['completed_process'];
		$this->data['process_id'] = $data['process_id'];
		$this->data['movement_type'] = $data['movement_type'];
		$this->data['prc_number'] = $data['prc_number'];
		$this->data['item_name'] = $data['item_name'];
		$this->load->view('sop_desk/prc_movement_form',$this->data);
	}

	public function getPRCMovementHtml(){
		$data = $this->input->post();$data['nextProcessDetail'] = 1;
		$completedProcess = $data['completed_process'];
		$data['completed_process'] = $data['completed_process'].",".$data['process_id'];
		$data['move_from'] = $data['trans_type'];
		$transData = $this->sop->getProcessMovementList($data);
		$html="";
        if (!empty($transData)) :
            $i = 1;
            foreach ($transData as $row) :
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePRCMovement','res_function':'getPrcMovementResponse','controller':'sopDesk'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';

				$printTag = '<a href="' . base_url('pos/printPRCMovement/' . $row->id) . '" target="_blank" class="btn btn-sm btn-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';
				
				$html .='<tr class="text-center">
							<td>' . $i++ . '</td>
							<td>' . formatDate($row->trans_date). '</td>
							<td>' . $row->next_process_name. '</td>
							<td>' . floatval($row->qty) . ' </td>
							<td>' . $row->remark. '</td>
							<td>' . $printTag.$deleteBtn . '</td>
						</tr>';
            endforeach;
        endif;
		$prcProcessData = $this->sop->getPendingMovementData(['prc_id'=>$data['prc_id'],'process_id'=>trim($data['process_id']),'completed_process'=>$completedProcess,'trans_type'=>$data['trans_type']]);

		$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
		$movement_qty =!empty($prcProcessData->movement_qty)?$prcProcessData->movement_qty:0;
		$pending_movement = $ok_qty - $movement_qty;
		$this->printJson(['status'=>1,'tbodyData'=>$html,'pendingQty'=>$pending_movement]);
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
				'remark' => !empty($data['remark'])?$data['remark']:'',
			];
			
            $result = $this->sop->savePRCMovement($logData);
			$this->printJson($result);
		endif;	
	}

	/** If you change in this function also change in Pos also */
	public function savePrcLog(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if (empty($data['prc_id'])){ $errorMessage['general_error'] = "Job Card No. is required.";}
        if (empty($data['process_id'])){ $errorMessage['process_id'] = "Process is required.";}
        if (isset($data['wt_nos']) && empty($data['wt_nos'])){ $errorMessage['wt_nos'] = "Input weight is required.";}
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
				'processor_id' => (!empty($data['processor_id'])?$data['processor_id']:0),
				'production_time' => (!empty($data['production_time'])?$data['production_time']:0),
                'start_time'=>$start_time,
                'end_time'=>$end_time,
                'remark' => (!empty($data['remark'])?$data['remark']:'')
            ];
			//If Final Inspection Process
			if($data['process_id'] == 2){
				$insParamData =  $this->item->getInspectionParameter(['item_id'=>$data['item_id'],'control_method'=>'FIR']);
				if(count($insParamData) <= 0) { $errorMessage['general'] = "Item Parameter is required."; }
		
				if (!empty($errorMessage)) { $this->printJson(['status' => 0, 'message' => $errorMessage]); }
						
				$pre_inspection = Array(); $param_ids = Array();
				if(!empty($insParamData)):
					foreach($insParamData as $row):
						$param = Array();
						for($j = 1; $j <= $data['sampling_qty']; $j++):
							$param[] = $data['sample'.$j.'_'.$row->id];
							unset($data['sample'.$j.'_'.$row->id]);
						endfor;
						$param[] = (!empty($data['result_'.$row->id]) ? $data['result_'.$row->id] : ""); 
						unset($data['result_'.$row->id]);

						$pre_inspection[$row->id] = $param;
						$param_ids[] = $row->id;
					endforeach;
				endif;
		
				
				$firData = [
					'observation_sample'=>json_encode($pre_inspection),
					'parameter_ids'=>implode(',',$param_ids),
					'param_count'=>count($insParamData),
					'insp_date'=>$data['trans_date'],
					'prc_id'=>$data['prc_id'],
					'item_id'=>$data['item_id'],
					'process_id'=>$data['process_id'],
					'trans_no'=>$data['trans_no'],
					'trans_number'=>$data['trans_number'],
					'sampling_qty'=>$data['sampling_qty'],
					'report_type'=>2,
				];
				$logData['firData'] = $firData;
			}

			if(isset($_FILES['challan_file']['name']) && ($_FILES['challan_file']['name'] != null || !empty($_FILES['challan_file']['name']))):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['challan_file']['name'];
				$_FILES['userfile']['type']     = $_FILES['challan_file']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['challan_file']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['challan_file']['error'];
				$_FILES['userfile']['size']     = $_FILES['challan_file']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/prc_log_attch/');
				$config = ['file_name' => time()."_challan_file_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['challan_file'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$logData['challan_file'] = $uploadData['file_name'];
				endif;
			endif;
           	$result = $this->sop->savePRCLog($logData);
            $this->printJson($result);
        endif;	
	}

	public function challanRequest(){
		$data = $this->input->post();
		$this->data['trans_type'] = (!empty($data['trans_type']))?$data['trans_type']:1;
		$this->data['process_from'] = $data['process_from'];
		$this->data['process_id'] = $data['process_id'];
		$this->data['completed_process'] = $data['completed_process'];
		$this->data['prc_id'] = $data['prc_id'];
		$this->load->view('sop_desk/prc_challan_request',$this->data);
	}

	public function saveChallanRequest(){
		$data = $this->input->post();
        $errorMessage = array();
        if (empty($data['prc_id'])){ $errorMessage['prc_id'] = "Job Card No. is required.";}
        if (empty($data['process_id'])){ $errorMessage['process_id'] = "Process is required.";}
		if (empty($data['trans_date']) or $data['trans_date'] == null or $data['trans_date'] == ""){ $errorMessage['trans_date'] = "Date is required."; }
        if (empty($data['qty'])){
            $errorMessage['qty'] = "Request qty required";
       	}else{
			$logData = $this->sop->getPendingLogData(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'completed_process'=>$data['completed_process'],'trans_type'=>$data['trans_type']]);
            $in_qty = $logData->in_qty;
            $ok_qty = !empty($logData->ok_qty)?$logData->ok_qty:0;
            $rej_found = !empty($logData->rej_found)?$logData->rej_found:0;
            $rw_qty = !empty($logData->rw_qty)?$logData->rw_qty:0;
            $rej_qty = !empty($logData->rej_qty)?$logData->rej_qty:0;
            $pendingReview = $rej_found - $logData->review_qty;
           
            $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$logData->ch_qty);
			
			if($pending_production < $data['qty'] ||  $data['qty'] < 0) :
				$errorMessage['qty'] = "Invalid Qty.";
			endif;
		}
		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$data['old_qty'] = $data['qty'];
			$this->printJson($this->sop->saveChallanRequest($data));
		endif;	
	}

	public function deleteChallanRequest(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deleteChallanRequest($data));
        endif;
	}

	public function getChallanRequestHtml(){
		$data = $this->input->post();
		$requestData = $this->sop->getChallanRequestData($data);
		$html="";
        if (!empty($requestData)) :
            $i = 1;
            foreach ($requestData as $row) :
				$deleteBtn = "";
				if($row->challan_id == 0){
					$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deleteChallanRequest','res_function':'getChallanRequestResponse','controller':'sopDesk'}";
					$deleteBtn = '<a class="btn btn-outline-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
				}
				$html .='<tr class="text-center">
							<td>' . $i++ . '</td>
							<td>' . formatDate($row->trans_date). '</td>
							<td>' . $row->qty . ' </td>
							<td>' . $deleteBtn . '</td>
						</tr>';
            endforeach;
        else :
            $html = '<td colspan="5" class="text-center">No Data Found.</td>';
        endif;
		$logData = $this->sop->getPendingLogData(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'completed_process'=>$data['completed_process']]);
            $in_qty = $logData->in_qty;
            $ok_qty = !empty($logData->ok_qty)?$logData->ok_qty:0;
            $rej_found = !empty($logData->rej_found)?$logData->rej_found:0;
            $rw_qty = !empty($logData->rw_qty)?$logData->rw_qty:0;
            $rej_qty = !empty($logData->rej_qty)?$logData->rej_qty:0;
            $pendingReview = $rej_found - $logData->review_qty;
           
            $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$logData->ch_qty);
		$this->printJson(['status'=>1,'tbodyData'=>$html,'pending_ch_qty'=>$pending_production]);
	}
	
	public function deletePrcAccept(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deletePrcAccept($data));
        endif;
	}
	public function deletePRCLog(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deletePRCLog($data));
        endif;
	}

	public function deletePRCMovement(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deletePRCMovement($data));
        endif;
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

	function printDetailRouteCard($id){
		$prcData = $this->data['prcData'] = $this->sop->getPRCDetail(['id'=>$id,'itemDetail'=>1,'createdDetail'=>1]);
		if(!empty($prcData))
		{
			$this->data['prcProcessData'] = $this->sop->getPRCProcessList(['prc_id'=>$id,'process_id'=>$prcData->process_ids,'inwardDetail'=>1,'acceptDetail'=>1,'movementDetail'=>1,'logDetail'=>1,'rejReviewDetail'=>1,'challanDetail'=>1]);
			
			$this->data['prcMaterialData'] = $this->sop->getPrcHeat(['prc_id'=>$id,'itemDetail'=>1]);
			

			$this->data['logData'] = $this->sop->getProcessLogList(['prc_id'=>$id,'processDetail'=>1,'processorDetail'=>1,'operatorDetail'=>1]);
		}

		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
        $pdfData = $this->load->view('sop_desk/print_route_card', $this->data, true);
		
        $printedBy = $this->employee->getEmployee(['id'=>$this->loginId]);
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
        $htmlFooter = '
			<table class="table top-table" style="margin-top:10px;border-top:1px solid #000000;">
				<tr>
					<td style="width:50%;">
					    Printed By & Date : '.$printedBy->emp_name.' ('.formatDate(date('Y-m-d H:s:i'), 'd-m-Y H:s:i').')
					</td>
					<td style="width:50%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';

        $mpdf = new \Mpdf\Mpdf();

        $pdfFileName = 'PRC-' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->useSubstitutions = false;
		$mpdf->simpleTables = true;

        $mpdf->AddPage('P', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    } 

	public function addFinalInspection(){
        $data = $this->input->post();
		$this->data['trans_type'] = $data['trans_type'];
		$this->data['process_from'] = $data['process_from'];
		$this->data['completed_process'] = $data['completed_process'];
		$this->data['prc_id'] = $data['prc_id'];
		$this->data['process_id'] = $data['process_id'];
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'single_row'=>1]);
        $this->data['trans_no'] = $this->sop->getFirNextNo();
        $this->data['trans_number'] = "FIR".sprintf(n2y(date('Y'))."%03d",$this->data['trans_no']);
		$this->load->view('sop_desk/fir_form',$this->data);
	}

	public function getFinalInspectionData(){
        $data = $this->input->post();
        $paramData = $this->item->getInspectionParameter(['item_id'=>$data['item_id'],'control_method'=>'FIR']);
        $tbodyData="";$i=1; $theadData='';
                $theadData .= '<tr class="thead-info" style="text-align:center;">
                            <th rowspan="2" style="width:5%;">#</th>
                            <th rowspan="2">Parameter</th>
                            <th rowspan="2">Specification</th>
                            <th rowspan="2" style="width:10%">Instrument</th>
                            <th colspan="'.$data['sampling_qty'].'" style="text-align:center;">Observation on Samples</th>
							<th rowspan="2" style="width:5%">Result</th>
                        </tr>
                        <tr style="text-align:center;">';
                        for($j=1; $j<=$data['sampling_qty']; $j++):
                            $theadData .= '<th>'.$j.'</th>';
                        endfor;    
                $theadData .='</tr>';
        if(!empty($paramData)):
            foreach($paramData as $row):
             
                $tbodyData.= '<tr>
                            <td style="text-align:center;">'.$i++.'</td>
                            <td style="width:10px;">'.$row->parameter.'</td>
                            <td style="width:10px;">'.$row->specification.'</td>    
                            <td style="width:20px;">'.$row->instrument.'</td>';
                            for($j=1; $j<=$data['sampling_qty']; $j++):
                $tbodyData.=' <td style="min-width:100px;"><input type="text" name="sample'.($j).'_'.$row->id.'" class="form-control" value=""></td>';
							endfor;  
				$tbodyData.='<td><select name="result_'.$row->id.'" class="form-control select2">
									<option value="Ok">Ok</option>
									<option value="Not Ok">Not Ok</option>
								</select></td>';
                $tbodyData.='</tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"theadData"=>$theadData]);
    }

	public function printFinalInspection($id,$output_type = 'I',$type = 0){
        $this->data['firData'] = $firData = $this->sop->getFinalInspectData(['id'=>$id]);
        $this->data['paramData'] =  $this->item->getInspectionParameter(['item_id'=>$firData->item_id,'control_method'=>'FIR']);
        $this->data['companyData'] = $this->masterModel->getCompanyInfo();
		
		$this->data['logo'] = $logo=base_url('assets/images/logo.png'); 
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('sop_desk/fir_pdf',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">FINAL INSPECTION REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">QA/F/02 (01/01.05.17)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$firData->emp_name.'</td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='fir_'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		if($output_type == 'I'){
			$mpdf->Output($pdfFileName,$output_type);	
		}else{
			$filePath = realpath(APPPATH . '../assets/uploads/fir_reports/');
			$mpdf->Output($filePath.'/'.$fileName, 'F');
			return $filePath.'/'.$fileName;
		}	
	}

	public function materialReturn(){
		$data = $this->input->post();
		$this->data['prc_id'] = $data['prc_id'];
		$this->data['item_id'] = $data['item_id'];
		$this->data['batch_id'] = $data['batch_id'];
		$this->data['dataRow'] = $this->store->getMaterialIssueData(['prc_id'=>$data['batch_id'],'item_id'=>$data['item_id'],'issue_type'=>2]); //$this->sop->printQuery();
		$this->data['locationList'] = $this->storeLocation->getStoreLocationList(['store_type'=>'0,1,2','final_location'=>1]);
		$this->load->view('sop_desk/prc_material_return',$this->data);
	}

	public function storeReturnedMaterial(){
		$data = $this->input->post();
		if(empty($data['item_id'])){ $errorMessage['general_error'] = "Item is required."; }
		if(empty($data['location_id'])){ $errorMessage['location_id'] = "Location is required."; }
		if(empty($data['batch_no'])){ $errorMessage['batch_no'] = "Batch No is required."; }
		if(empty($data['qty'])){ $errorMessage['qty'] = "Qty is required."; }
		else{
			$batchData = $this->store->getPrcBatchDetail(['id'=>$data['batch_id'],'single_row'=>1]);
			$stockQty = $batchData->issue_qty - ($batchData->used_qty + $batchData->return_qty + $batchData->scrap_qty);

			if($data['qty'] > round($stockQty,3)){ $errorMessage['qty'] = "Qty is invalid."; }
		}

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->sop->storeReturnedMaterial($data));
        endif;
		
	}

	public function getReturnHtml(){
		$data = $this->input->post();
		$customWhere = '  trans_type="PMR"';
		$batchData = $this->itemStock->getItemStockBatchWise(['child_ref_id'=>$data['prc_id'],'item_id'=> $data['item_id'],'customWhere'=>$customWhere,'group_by'=>'stock_trans.id','supplier'=>1]);
		$html = "";
		if(!empty($batchData)){
			$i=1;
			foreach($batchData as $row){
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deleteReturn','res_function':'getReturnResponse'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
				$html .= '<tr>
							<td>'.$i++.'</td>
							<td>'.formatDate($row->trans_date).'</td>
							<td>'.$row->location.'</td>
							<td>'.$row->batch_no.' [Heat : '.$row->heat_no.']</td>
							<td>'.$row->qty.'</td>
							<td>'.$row->remark.'</td>
							<td>'.$deleteBtn.'</td>
						</tr>';
			}
		} else {
			$html = '<td colspan="7" class="text-center">No Data Found.</td>';
		}
            
		
		$batchData = $this->store->getPrcBatchDetail(['id'=>$data['batch_id'],'single_row'=>1]);
		$stockQty = $batchData->issue_qty - ($batchData->used_qty + $batchData->return_qty + $batchData->scrap_qty);
		$this->printJson(['status'=>1,'tbodyData'=>$html,'pending_qty'=>$stockQty]);
	}

	public function getToolMethod(){
        $data = $this->input->post();
        $methodData = $this->dieMaster->getDieData(['item_id'=>$data['item_id'],'group_by'=>'die_master.tool_method']);
        $options = '<option value="">Select Tool Method</option>';
        if(!empty($methodData)){
            foreach($methodData AS $row){
                $options .= '<option value="'.$row->tool_method.'">'.$row->method_name.'</option>';
            }
        }
        $this->printJson(['status'=>1,'options' => $options]);
    }

	public function toolIssue(){
		$data = $this->input->post();
		$prcData = $this->data['prcData'] = $this->sop->getPRCDetail(['id'=>$data['id'],'itemDetail'=>1,'createdDetail'=>1]);
		$this->data['dieList'] = $this->dieMaster->getDieData(['item_id'=>$prcData->item_id,'tool_method'=>$prcData->tool_method]);
		$this->data['dieStock'] = $this->dieProduction->getDieRegisterData(['item_id'=>$prcData->item_id,'tool_method'=>$prcData->tool_method,'status'=>1,'customWhere'=>'die_register.prc_id = 0']);
		$this->data['issueToolList'] = $this->sop->getToolData(['prc_id'=>$prcData->id,'status'=>1]);
		$this->load->view('sop_desk/tool_issue_form',$this->data);
	}

	public function saveIssuedTool(){
		$data = $this->input->post();
		if(empty($data['die_id'])){ $errorMessage['general_error'] = "Tool is required."; }
		else{
			foreach($data['die_id'] As $key=>$die_id){
				if(empty($data['die_reg_id'][$die_id])){$errorMessage['die_'.$die_id] = "Tool is required.";}
			}
		}

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->sop->saveIssuedTool($data));
        endif;
	}

	public function releaseTool(){
		$data = $this->input->post();
		$prcData = $this->data['prcData'] = $this->sop->getPRCDetail(['id'=>$data['id'],'itemDetail'=>1,'createdDetail'=>1]);
		$this->data['dieList'] = $this->sop->getToolData(['prc_id'=>$prcData->id,'status'=>1]);
		$this->load->view('sop_desk/tool_release_form',$this->data);
	}

	public function saveReleaseTool(){
		$data = $this->input->post();
		
		if(empty($data['log_id'])){ $errorMessage['general_error'] = "Select the tool for release."; }
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->sop->saveReleaseTool($data));
        endif;
	}

	
	public function changePRCStage(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->changePRCStage($data));
        endif;
    }

	/* Update PRC Qty */
	public function updatePrcQty(){
        $this->data['prc_id'] = $this->input->post('id');
        $this->load->view('sop_desk/prc_update', $this->data);
    }

    public function getUpdatePrcQtyHtml(){
        $data = $this->input->post();
        $logdata = $this->sop->getPRCUpdateLogData(['prc_id'=> $data['prc_id']]); 
        $tbodyData = ''; 
        if(!empty($logdata)): $i=1; 
            foreach($logdata as $row): 
                $deleteParam = $row->id . ",'PRC Qty'";
                $logType = ($row->log_type == 1)?'(+) Add':'(-) Reduce';
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePrcUpdateQty','res_function':'updatePrcQtyHtml','controller':'sopDesk'}";
                $deleteBtn = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="left"><i class="mdi mdi-trash-can-outline"></i></a>';

                $tbodyData .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->log_date).'</td>
                    <td>'.$logType.'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.$deleteBtn.'</td>
                </tr>';
            endforeach;
		else:
			$tbodyData .= '<tr class="text-center"><td colspan="5">Data not available.</td></tr>';
        endif; 
        $this->printJson(['status' => 1, 'tbodyData' => $tbodyData]);
    }
    
	public function savePrcQty(){
        $data = $this->input->post();  
        $errorMessage = array();

		if (empty($data['qty'])) :
			$errorMessage['qty'] = "Qty is required.";
		endif;

        if ($data['log_type'] == -1) :			
			$prcData = $this->sop->getPRCDetail(['id'=>$data['prc_id']]);
			$process_ids = (!empty($prcData->process_ids) ? explode(',',$prcData->process_ids) : []);
			$processId = (!empty($process_ids) ? $process_ids[0] : 0);

			$logData = $this->sop->getPendingLogData(['prc_id'=>$data['prc_id'],'process_id'=>$prcData->first_process,'completed_process'=>0,'trans_type'=>1]);
            $in_qty = $logData->in_qty;
            $ok_qty = !empty($logData->ok_qty)?$logData->ok_qty:0;
            $rej_found = !empty($logData->rej_found)?$logData->rej_found:0;
            $rw_qty = !empty($logData->rw_qty)?$logData->rw_qty:0;
            $rej_qty = !empty($logData->rej_qty)?$logData->rej_qty:0;
            $pendingReview = $rej_found - $logData->review_qty;
           
            $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$logData->ch_qty);
						
			if($pending_production < $data['qty'] ||  $data['qty'] < 0) :
				$errorMessage['qty'] = "Invalid Qty.".$pending_production;
			endif;
        endif;
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result = $this->sop->savePrcQty($data);
            $this->printJson($result);
        endif;
    }

    public function deletePrcUpdateQty(){
        $id = $this->input->post('id');		
		$logData = $this->sop->getPRCUpdateLogData(['id'=>$id,'single_row'=>1]);
		$prcData = $this->sop->getPRCDetail(['id'=>$logData->prc_id]);
		$process_ids = (!empty($prcData->process_ids) ? explode(',',$prcData->process_ids) : []);
		$processId = (!empty($process_ids) ? $process_ids[0] : 0);

        $errorMessage = '';
        if ($logData->log_type == 1) :
			$logData = $this->sop->getPendingLogData(['prc_id'=>$logData->prc_id,'process_id'=>$prcData->first_process,'completed_process'=>0,'trans_type'=>1]);
            $in_qty = $logData->in_qty;
            $ok_qty = !empty($logData->ok_qty)?$logData->ok_qty:0;
            $rej_found = !empty($logData->rej_found)?$logData->rej_found:0;
            $rw_qty = !empty($logData->rw_qty)?$logData->rw_qty:0;
            $rej_qty = !empty($logData->rej_qty)?$logData->rej_qty:0;
            $pendingReview = $rej_found - $logData->review_qty;
           
            $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$logData->ch_qty);
			
            if ($pending_production < $logData->qty) :
                $errorMessage = "Sorry...! You can't delete this PRC log because this qty moved to next process.";
            endif;
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result = $this->sop->deletePrcUpdateQty($id);
            $this->printJson($result);
        endif;
    }

	/****** Start Logs Detail ******/
	public function productionLogsDetail($process_id){
		$this->data['headData']->pageTitle = "Log Detail";
		$this->data['headData']->pageUrl = "sopDesk/productionLogsDetail";
		$this->data['tableHeader'] = getProductionDtHeader('acceptedLog');
		$this->data['process_id'] = $process_id;
		$this->data['processData'] = $this->process->getProcess(['id'=>$process_id]);
        $this->load->view('sop_desk/log_detail',$this->data);
	}
	
	public function getLogDetailDTRow($process_id,$move_type =1,$tab_type="acceptedLog"){
        $data = $this->input->post();
		$data['process_id'] = $process_id;
		$data['move_type'] =$move_type;
		
		if($tab_type=="acceptedLog"){
			$result = $this->sop->getAccepedLogDTRow($data);
		}elseif($tab_type=="productionLog"){
			$result = $this->sop->getProductionLogDTRows($data);
		}elseif($tab_type=="movement"){
			$result = $this->sop->getPrcMovementDTRows($data);
		}elseif($tab_type=="challanLog"){
			$result = $this->sop->getChallanLogDTRows($data);
		}
       
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
			if($tab_type=="acceptedLog"){
				$sendData[] = getAcceptedLogData($row); 
			}elseif($tab_type=="productionLog"){
				$sendData[] = getProductionLogData($row);
			}elseif($tab_type=="movement"){
				$sendData[] = getMovementData($row);
			}elseif($tab_type=="challanLog"){
				$sendData[] = getChallanLogData($row);
			}
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	/****** End Logs Detail ******/
}
?>