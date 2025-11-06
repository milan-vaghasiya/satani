<?php
class DieProduction extends MY_Controller{

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Tool Production";
		$this->data['headData']->controller = "dieProduction";  
        $this->data['headData']->pageUrl = "dieProduction";
	}

    public function index(){
        $this->data['tableHeader'] = getProductionDtHeader("dieProduction");
        $this->load->view('die_production/index',$this->data);
    }

    public function getDTRows($status = 0 ){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->dieProduction->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getDieProductionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addWorkOrder(){
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $trans_no = $this->dieProduction->getNextWoNo();
        $this->data['trans_number'] = 'DWO/'.$this->shortYear.'/'.$trans_no;
        $this->load->view('die_production/form',$this->data);
    }

    public function delete(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dieProduction->delete($data));
        endif;
	}

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['item_id'])){ $errorMessage['item_id'] = "Select Item ";}
        if(empty($data['tool_method'])){ $errorMessage['tool_method'] = "Tool Method Required ";}
        if(empty($data['qty']) OR empty(array_sum($data['qty']))){ $errorMessage['general_error'] = "Die Required ";}
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieProduction->save($data));
        endif;
    }

    /* Material Issue */
    public function materialIssue(){
		$data = $this->input->post();
        $this->data['id'] = $data['id'];
        $dieData = $this->dieMaster->getDieData(['id'=>$data['die_id'],'single_row'=>1]);
        // print_r($dieData);exit;
		$this->data['dieBlockList'] = $this->item->getItemList(['item_type'=>'3,7']);
        
		$this->load->view('die_production/issue_form',$this->data);
	}

    public function getBatchWiseStock(){
        $data = $this->input->post();
        $batchData = $this->itemStock->getItemStockBatchWise(["item_id" => $data['item_id'],'stock_required'=>1,'group_by'=>'location_id,batch_no','supplier'=>1]);
        
        $tbodyData='';$i=1;
        if (!empty($batchData)) {
            foreach ($batchData as $row) {
                $batch_no = $row->batch_no.((!empty($row->heat_no))?('<hr style="margin:0px">'.$row->heat_no):'');
                $tbodyData .= '<tr>';
                $tbodyData .= '<td>'.$row->location.'</td>';
                $tbodyData .= '<td>'.$batch_no.'</td>';
                $tbodyData .= '<td>'.floatVal($row->qty).'</td>';
                $tbodyData .= '<td>
                                    <input type="text" name="batch_qty[]" id="batch_qty_'.$i.'" class="form-control batchQty floatOnly batchInput checkRow' . $i . '" min="0" value="" data-stock_qty="'.floatVal($row->qty).'" data-rowid="'.$i.'" />
                                    <input type="hidden" name="batch_no[]" id="batch_number_' . $i . '" value="' . $row->batch_no . '"  class="batchInput checkRow' . $i . '"/>
                                    <input type="hidden" name="heat_no[]" id="heat_no_' . $i . '" value="' . (!empty($row->heat_no) ? $row->heat_no : '') . '" class="batchInput checkRow' . $i . '" />
                                    <input type="hidden" name="location_id[]" id="location_' . $i . '" value="' . $row->location_id . '" class="batchInput checkRow' . $i . '" />
                                    <div class="error batch_qty_'.$i.'"></div>
                              </td>';
                $tbodyData .= '</tr>';
                $i++;
            }
        } else {
            $tbodyData .= "<td colspan='4' class='text-center'>No Data</td>";
        }
        $this->printJson(['status' => 1, 'tbodyData' => $tbodyData]);
    }

    public function saveMaterialIssue(){
		$data = $this->input->post();

		if(empty($data['die_block_id'])){   $errorMessage['die_block_id'] = "Item is required.";  }
        if(empty($data['batch_qty']) || empty(array_sum($data['batch_qty']))){ $errorMessage['general_error'] = "Material is required."; }
        else{
            foreach($data['batch_qty'] AS $key=>$batch_qty){
                if(!empty($batch_qty) && $batch_qty > 0){
                    $stockData = $this->itemStock->getItemStockBatchWise(["item_id" => $data['die_block_id'],'stock_required'=>1,'group_by'=>'location_id,batch_no','supplier'=>1,'location_id'=>$data['location_id'][$key],'batch_no'=>$data['batch_no'][$key],'single_row'=>1]);
                    $stock_qty = (!empty($stockData)) ? $stockData->qty : 0;
                    if($batch_qty> $stock_qty){
                        $errorMessage['general_error'] = "The selected batch does not have stock.";
                    }
                }
            }
        }
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:

            $this->printJson($this->dieProduction->saveMaterialIssue($data));
        endif;		
	}

    /* Log Form */
    public function addDieLog(){
		$data = $this->input->post();
        $this->data['dataRow'] = $this->dieProduction->getProductionData(['id'=>$data['id'],'single_row'=>1]);
        
        if(!empty($data['challan_id'])){
			$this->data['challan_id'] = $data['challan_id'];
			$this->data['ref_trans_id'] = $data['ref_trans_id'];
			$this->data['process_by'] = 2;
			$this->data['processor_id'] = $data['party_id'];
			$this->data['process_id'] = $data['process_id'];
		}else{
            $this->data['processList'] = $this->itemCategory->getDieProcessList(['category_id'=>$this->data['dataRow']->category_id,'processDetail'=>1]);
            $this->data['operatorList'] = $this->employee->getEmployeeList();
            $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
			$this->data['process_by'] = 1;
        }
		
		$this->load->view('die_production/die_log_form',$this->data);
	}
	
	public function saveProductionLog(){
		$data = $this->input->post(); 
        $errorMessage = array();
        
        if(empty($data['die_id'])){ 
            $errorMessage['die_id'] = "Tool No. is required.";
        }
        // if(empty($data['production_time']) && $data['process_by'] == 1){ 
            // $errorMessage['production_time'] = "Production Time is required.";
        // }
        if(empty($data['processor_id'])){ 
            $errorMessage['processor_id'] = "Required.";
        }
		if(empty($data['trans_date']) or $data['trans_date'] == null or $data['trans_date'] == ""){ 
            $errorMessage['trans_date'] = "Date is required."; 
        }
        if(!empty($data['process_by']) && $data['process_by'] == 2){
            if (empty($data['in_challan_no'])){ 
                $errorMessage['in_challan_no'] = "In Challan No. is required.";
            }            
        }        

        if(empty($data['qty'])){
            $errorMessage['qty'] = "Required.";
        }else{
            if($data['process_by'] == 1){
                $woData = $this->dieProduction->getProductionData(['id' => $data['wo_id'],'single_row'=>1]);
                $pendingQty = $woData->qty - ($woData->ok_qty + $woData->ch_qty);
            }else{
                $chData = $this->dieProduction->getChallanReqData(['id' => $data['ref_trans_id'],'receiveDetail'=>1,'single_row'=>1]);
                $pendingQty = $chData->qty - $chData->receive_qty;
            }
            if($data['qty'] > $pendingQty){
                $errorMessage['qty'] = "Invalid Qty.";
            }
        }
		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $postData = [
                'id'=>$data['id'],
                'wo_id' => $data['wo_id'],
                'die_id' => $data['die_id'],
                'trans_date' => $data['trans_date'],
                'process_id' => $data['process_id'],
                'process_by' =>$data['process_by'],
                'processor_id' => $data['processor_id'],
                'qty' => $data['qty'],
                'ref_id' => ((!empty($data['ref_id']))?$data['ref_id']:0),
                'ref_trans_id' => ((!empty($data['ref_trans_id']))?$data['ref_trans_id']:0),
                'operator_id' => ((!empty($data['operator_id']))?$data['operator_id']:0),
                'production_time' => ((!empty($data['production_time']))?$data['production_time']:0),
                'in_challan_no' => ((!empty($data['in_challan_no']))?$data['in_challan_no']:""),
                'remark' => $data['remark']
            ];
            if($data['process_by'] == 2):
				if($_FILES['attachment']['name'] != null || !empty($_FILES['attachment']['name'])):
                    $this->load->library('upload');
    				$_FILES['userfile']['name']     = $_FILES['attachment']['name'];
    				$_FILES['userfile']['type']     = $_FILES['attachment']['type'];
    				$_FILES['userfile']['tmp_name'] = $_FILES['attachment']['tmp_name'];
    				$_FILES['userfile']['error']    = $_FILES['attachment']['error'];
    				$_FILES['userfile']['size']     = $_FILES['attachment']['size'];
    				
    				$imagePath = realpath(APPPATH . '../assets/uploads/die_outsource/');
    				$config = ['file_name' => 'in_challan-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];
    
    				$this->upload->initialize($config);
    				if (!$this->upload->do_upload()):
    					$errorMessage['attachment'] = $this->upload->display_errors();
    					$this->printJson(["status"=>0,"message"=>$errorMessage]);
    				else:
    					$uploadData = $this->upload->data();
    					$postData['attachment'] = $uploadData['file_name'];
    				endif;
    			endif;
            endif;
			$this->printJson($this->dieProduction->saveProductionLog($postData));
		endif;	
	}

    public function getProductionLogHtml(){
        $data = $this->input->post();
        $logData = $this->dieProduction->getProductionLogData(['wo_id'=>$data['wo_id'],'ref_trans_id'=>$data['ref_trans_id'],'processDetail'=>1,'processorDetail'=>1,'operatorDetail' => 1]);
        $tbodyData = "";
        if(!empty($logData)){
            $i=1;
            foreach($logData AS $row){
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deleteLog','res_function':'getDieLogResponse','controller':'dieProduction'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
                
                $challan_file = (!empty($row->attachment))? '<a href="' . base_url('assets/uploads/die_outsource/' . $row->attachment) . '" target="_blank" class="btn btn-sm btn-warning mr-2" title="Challan"><i class="fas fa-download"></i></a>':'';

                $tbodyData .= '<tr>
                                    <td>'.$i++.'</td>
                                    <td>'.formatDate($row->trans_date).'</td>
                                    <td>'.$row->process_name.'</td>
                                    <td>'.$row->qty.'</td>
                                    <td>'.$row->production_time.'</td>
                                    <td>'.$row->processor_name.'</td>
                                    <td>'.$row->in_challan_no.'</td>
                                    <td>'.$row->emp_name.'</td>
                                    <td>'.$row->remark.'</td>
                                    <td>'.$challan_file.$deleteBtn.'</td>
                               </tr>';
            }
        }else{
            $tbodyData = '<tr>
                                <th colspan="9" class="text-center">No data avalable.</th>
                          </tr>';
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    public function deleteLog(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dieProduction->deleteLog($data));
        endif;
	}

    /** Challan Request*/
    public function addChallanRequest(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->dieProduction->getProductionData(['id'=>$data['id'],'single_row'=>1]);
        $this->data['processList'] = $this->itemCategory->getDieProcessList(['category_id'=>$this->data['dataRow']->category_id,'processDetail'=>1]);
        
		$this->load->view('die_production/challan_req_form',$this->data);
    }

    public function saveChallanRequest(){
		$data = $this->input->post(); 
        $errorMessage = array();
        
        if(empty($data['process_id'])){ 
            $errorMessage['process_id'] = "Process is required.";
        }
		if(empty($data['trans_date']) or $data['trans_date'] == null or $data['trans_date'] == ""){ 
            $errorMessage['trans_date'] = "Date is required."; 
        }
        
        if(empty($data['qty'])){ 
            $errorMessage['qty'] = "Qty is required."; 
        }else{
            $woData = $this->dieProduction->getProductionData(['id' => $data['wo_id'],'single_row'=>1]);
            $pendingQty = $woData->qty - ($woData->ok_qty + $woData->ch_qty);
            if($data['qty'] > $pendingQty){
                $errorMessage['qty'] = "Invalid Qty.";
            }
        }

		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$this->printJson($this->dieProduction->saveChallanRequest($data));
		endif;	
	}

    public function deleteChallanReq(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dieProduction->deleteChallanReq($data));
        endif;
	}

    public function getChallanReqHtml(){
        $data = $this->input->post();
        $logData = $this->dieProduction->getChallanReqData(['wo_id'=>$data['wo_id'],'processDetail'=>1]);
        $tbodyData = "";
        if(!empty($logData)){
            $i=1;
            foreach($logData AS $row){
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deleteChallanReq','res_function':'getDieChallanResponse','controller':'dieProduction'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
                $tbodyData .= '<tr>
                                    <td>'.$i++.'</td>
                                    <td>'.formatDate($row->trans_date).'</td>
                                    <td>'.$row->process_name.'</td>
                                    <td>'.$row->qty.'</td>
                                    <td>'.$deleteBtn.'</td>
                               </tr>';
            }
        }else{
            $tbodyData = '<tr>
                                <th colspan="9" class="text-center">No data avalable.</th>
                          </tr>';
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    /** Outsoource */
    public function dieOutsource(){
		$this->data['headData']->pageTitle = "Tool Outsource";
        $this->data['tableHeader'] = getProductionDtHeader("dieOutsource");
        $this->load->view('die_production/outsource_index',$this->data);
    }

    public function getOutsourceDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->dieProduction->getOutsourceDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;    
            if($data['status'] == 0){
                $sendData[] = getDieOutsourceData($row);
            }else{
                $sendData[] = getDieChallanData($row);
            }  
            
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
        $data = $this->input->post();
        $this->data['ch_prefix'] = 'DC'.n2y(date("Y")).n2m(date("m"));
        $this->data['ch_no'] = $this->dieProduction->getNextChallanNo();
        $this->data['dieProdList'] = $this->dieProduction->getChallanReqData(['id'=>$data['ids'],'processDetail'=>1,'dieMasterDetail'=>1,'workOrderDetail'=>1]); 
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
        $this->load->view('die_production/challan_form',$this->data);
    }

    public function saveChallan(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_id'])){ $errorMessage['party_id'] = "Vendor is required.";}
        if(empty($data['dp_id'])){ $errorMessage['general_error'] = "Select Item ";}
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieProduction->saveChallan($data));
        endif;
    }

     public function deleteChallan(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dieProduction->deleteChallan($data));
        endif;
	}

    public function dieOutSourcePrint($challan_id){
        $this->data['dieOutSourceData'] = $this->dieProduction->getChallanReqData(['challan_id'=>$challan_id,'challanDetail'=>1,'dieMasterDetail'=>1,'workOrderDetail'=>1,'processDetail'=>1]);
        $this->data['companyData'] = $this->dieProduction->getCompanyInfo();	

        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
    
        $pdfData = $this->load->view('die_production/outsource_print', $this->data, true);        
		$mpdf = new \Mpdf\Mpdf();
        $pdfFileName='VC-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,45));
        $mpdf->showWatermarkImage = true;
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');
		
        $mpdf->WriteHTML($pdfData);
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
		
    }

    /** Die Status */
    public function changeStatus(){
        $data = $this->input->post();
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['id'])){ $errorMessage = "Something is wrong";}
        if($data['status'] == 6 && empty($data['die_id'])){ $errorMessage['die_id'] = "Select Tool ";}
        if($data['status'] == 3 && empty($data['emp_id'])){ $errorMessage['emp_id'] = "Select Handover To ";}
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieProduction->changeStatus($data));
        endif;
    }

    public function dieLogDetail(){
        $data = $this->input->post();
        $this->data['logData'] = $this->dieProduction->getProductionLogData(['wo_id'=>$data['id'],'processDetail'=>1,'processorDetail'=>1,'operatorDetail' => 1]);
        $this->load->view('die_production/process_detail', $this->data);  
    }

    /** Rework Die */
    public function reworkDie(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->dieProduction->getProductionData(['id'=>$data['id'],'single_row'=>1]);
        /* $this->data['dieList'] = $this->dieMaster->getDieData(); */
        $this->load->view("die_production/rw_form",$this->data);
    }

    public function dieRegister(){
		$this->data['headData']->pageTitle = "Tool Register";
        $this->data['tableHeader'] = getProductionDtHeader("dieRegister");
        $this->load->view('die_production/die_register',$this->data);
    }

    public function getRegisterDTRows($status = 1 ){
        $data = $this->input->post();$data['status'] = $status;
        if($status == 0){
            $result = $this->dieProduction->getFreshDieDTRows($data);
        }else{
            $result = $this->dieProduction->getRegisterDTRows($data);
        }
        
        
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            if($status == 0){
                $sendData[] = getFreshDieData($row);
            }else{
                $sendData[] = getDieRegisterData($row);
            }
            
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function issueDie(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->dieProduction->getProductionData(['id'=>$data['id'],'single_row'=>1]);
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view("die_production/die_issue_form",$this->data);
    }

    public function convertItem(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->dieProduction->getProductionData(['id'=>$data['id'],'single_row'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("die_production/convert_item",$this->data);
    }

    public function saveConvertedItem(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['id'])){ $errorMessage = "Something is wrong";}
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieProduction->saveConvertedItem($data));
        endif;
    }

    public function getToolMethod(){
        $data = $this->input->post();
        $methodData = $this->dieMaster->getDieData(['item_id'=>$data['item_id'],'group_by'=>'die_master.tool_method']);
        $options = '<option value="">Select Tool Method</option>';
        if(!empty($methodData)){
            foreach($methodData AS $row){
                $options .= '<option value="'.$row->tool_method.'">'.(!empty($row->method_code) ? '['.$row->method_code.'] '.$row->method_name : $row->method_name).'</option>';
            }
        }
        $this->printJson(['status'=>1,'options' => $options]);
    }

    public function getDieList(){
        $data = $this->input->post();
        $dieData = $this->dieMaster->getDieData(['item_id'=>$data['item_id'],'tool_method'=>$data['tool_method']]);
        $tbodyData = '';
        if(!empty($dieData)){
            $i=1;
            foreach($dieData AS $row){
                $tbodyData .= '<tr>
                                    <td>'.$i.'</td>
                                    <td>'.$row->die_code.' '.$row->die_name.'</td>
                                    <td>
                                        <input type="text" name="qty[]" class="form-control numericOnly">
                                        <input type="hidden" name="die_id[]" value="'.$row->id.'">
                                    </td>
                            </tr>';
                $i++;
            }
        }else{
            $tbodyData = '<tr>
                            <th class="text-center" colspan="3">No data available</th>
                        </tr>';
        }
        $this->printJson(['status'=>1,'tbodyData' => $tbodyData]);
    }

    public function completeProductionView(){
        $data = $this->input->post();
        $this->data['id'] = $data['id'];
        $this->data['dataRow'] = $this->dieProduction->getProductionData(['id'=>$data['id'],'single_row'=>1]);
        $this->load->view('die_production/complete_form', $this->data);  
    }

    public function completeProduction(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['id'])){ $errorMessage['id'] = "Something is wrong";}
        if(empty($data['ok_qty'])){ $errorMessage['ok_qty'] = "Qty is required";}
        else{
            $woData = $this->dieProduction->getProductionData(['id' => $data['id'],'single_row'=>1]);
            $pendingQty = $woData->qty - ($woData->ok_qty + $woData->ch_qty);
            if($data['ok_qty'] > $pendingQty){
                $errorMessage['ok_qty'] = "Invalid Qty.";
            }
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieProduction->completeProduction($data));
        endif;
    }

    public function addDieInStock(){
        $data = $this->input->post();
        $this->data['id'] = $data['id'];
        $this->data['dataRow'] = $this->dieProduction->getProductionData(['id'=>$data['id'],'single_row'=>1]);
        $this->load->view('die_production/add_die_stock', $this->data);  
    }

    public function saveDieStock(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['id'])){ $errorMessage['id'] = "Something is wrong";}
        if(empty($data['stock_qty'])){ $errorMessage['stock_qty'] = "Qty is required";}
        else{
            $woData = $this->dieProduction->getProductionData(['id' => $data['id'],'single_row'=>1]);
            $pendingQty = $woData->ok_qty + $woData->stock_qty;
            if($data['stock_qty'] > $pendingQty){
                $errorMessage['stock_qty'] = "Invalid Qty.";
            }
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieProduction->saveDieStock($data));
        endif;
    }
}
?>