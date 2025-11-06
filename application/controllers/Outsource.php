<?php
class Outsource extends MY_Controller
{
    private $indexPage = "outsource/index";
    private $formPage = "outsource/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Outsource";
		$this->data['headData']->controller = "outsource";
		$this->data['headData']->pageUrl = "outsource";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionDtHeader('outsource');
        $this->data['testTypeList'] = $this->testType->getTypeList();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->outsource->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
			$row->unit_id = $this->unit_id;
            $row->trans_status = $status;
            $sendData[] = getOutsourceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
        $this->data['ch_prefix'] = 'VC/'.getYearPrefix('SHORT_YEAR').'/';
        $this->data['ch_no'] = $this->outsource->getNextChallanNo();
        $this->data['requestData']=$this->sop->getChallanrequestData(['pending_challan'=>1,'itemDetail'=>1,'processDetail'=>1]);
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
		$this->data['transportList'] = $this->transport->getTransportList();
		$this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_id'])){ $errorMessage['party_id'] = "Vendor is required.";}
        if(empty($data['id'])){ $errorMessage['general_error'] = "Select Item ";}else{
            foreach($data['id'] as $key=>$id){
                $reqData = $this->sop->getChallanRequestData(['id'=>$id,'single_row'=>1,'prcDetail'=>1]);
                if($data['ch_qty'][$key] > $reqData->qty || empty($data['ch_qty'][$key])){
                    $errorMessage['chQty' . $id] = "Qty. is invalid.";
                }
                else{
                    
                    $customWhere = '';
                    foreach($data['process_ids'][$id] AS $keyP=>$processId){
                        $customWhere .= ' FIND_IN_SET('.$processId.', vendor_price_history.process_id) AND ';
                    }
                    $customWhere .= "LENGTH(REPLACE(vendor_price_history.process_id, ',', '')) = LENGTH(REPLACE('".implode(",",$data['process_ids'][$id])."', ',', ''))";
                    $vndorPriceData = $this->vendorPrice->getVendorPriceData(['item_id'=>$reqData->item_id,'vendor_id'=>$data['party_id'],'customWhere'=>$customWhere,'is_active'=>1]);
                    if(empty($vndorPriceData)){
                        $errorMessage['chQty' . $id] = "Set Vendor Price Data.";
                    }else{
                        $data['wt_nos'][$key] = $vndorPriceData->input_weight;
                        $data['price'][$key] = $vndorPriceData->rate;
                    }
                }

                if(empty($data['process_ids'][$id])){
                    $errorMessage['process_ids' . $id] = "Process Required";
                }
                elseif($data['process_ids'][$id][0] != $reqData->process_id){
                    $errorMessage['process_ids' . $id] = "Invalid Process.";
                }
                else{
                    $process = explode(",",$reqData->process_ids);
					$outProcessList = $data['process_ids'][$id];
					$a = 0;$jwoProcessIds = array();
					foreach ($process as $k => $value) :
						if (isset($outProcessList[$a])) :
							$processKey = array_search($outProcessList[$a], $process);
							$jwoProcessIds[$processKey] = $outProcessList[$a];
							$a++;
						endif;
					endforeach;
					ksort($jwoProcessIds);
					
					$processList = array();
					foreach ($jwoProcessIds as $k => $value) :
						$processList[] = $value;
					endforeach;
					
					$nextProcessKey = array_search($reqData->process_id,$process);
					$i = 0;$error = false;
					foreach($process as $ky => $pid):
						if ($ky >= $nextProcessKey) :
							if (isset($processList[$i])) :
								if ($processList[$i] != $pid) :
									$error = true;
									break;
								endif;
								$i++;
							endif;
						endif;
					endforeach;

					if ($error == true) :
                        $errorMessage['process_ids' . $id] = "Invalid Process Sequence.";
					endif;

                    $lastProcessKey = array_search($data['process_ids'][$id][(count($data['process_ids'][$id])-1)],$data['process_ids'][$id]);
                    $data['next_process_ids'][$key] = (!empty($process[$lastProcessKey +1])?$process[$lastProcessKey +1]:0);
                }
            }
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->outsource->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->outsource->delete($id));
        endif;
    }  
    
   public function addLog(){
		$data = $this->input->post();
		$this->data['challan_id'] = $data['challan_id'];
        $this->data['ref_trans_id'] = $data['ref_trans_id'];
        $this->data['process_by'] = $data['process_by'];
        $this->data['processor_id'] = $data['processor_id'];
        $this->data['challan_process'] = $data['challan_process'];
        
        $this->data['wt_nos'] = $data['wt_nos'];
		$this->data['processList'] = $this->process->getProcessList();
        $this->data['trans_type'] = (!empty($data['trans_type']))?$data['trans_type']:1;
		$this->data['process_from'] = $data['process_from'];
		$this->data['process_id'] = $data['process_id'];
		$this->data['completed_process'] = $data['completed_process'];
		$this->data['prc_id'] = $data['prc_id'];

		$this->load->view('outsource/log_form',$this->data);
	}
	
	public function saveLog(){
		$data = $this->input->post(); 
        $errorMessage = array();
        if (empty($data['prc_id'])){ $errorMessage['prc_id'] = "Job Card No. is required.";}
        if (empty($data['process_id'])){ $errorMessage['process_id'] = "Process is required.";}
        if (empty($data['in_challan_no'])){ $errorMessage['in_challan_no'] = "Challan is required.";}
		if (empty($data['trans_date']) or $data['trans_date'] == null or $data['trans_date'] == ""){ $errorMessage['trans_date'] = "Date is required."; }
		
        foreach($data['process_id'] as $jobKey=>$process_id){

            $okQty = !empty($data['ok_qty'][$jobKey])?$data['ok_qty'][$jobKey]:0;
            $without_prs_qty = !empty($data['without_process_qty'][$jobKey])?$data['without_process_qty'][$jobKey]:0;
            $rej_qty =  (!empty($data['rej_found'][$jobKey])) ? $data['rej_found'][$jobKey] : 0;

            $totalReceivedQty = $okQty+$without_prs_qty+$rej_qty;

            if($jobKey == 0){
                $challanData = $this->sop->getChallanRequestData(['id'=>$data['ref_trans_id'],'challan_receive'=>1,'single_row'=>1]); 
				$pending_production =$challanData->qty - ($challanData->ok_qty + $challanData->rej_qty+ $challanData->without_process_qty);
                if($totalReceivedQty == 0){
                    $errorMessage['ok_qty'.$process_id] = "Qty is required.";
                }elseif($totalReceivedQty > $pending_production){
                    $errorMessage['ok_qty'.$process_id] = "Qty is invalid.";
                }
            }elseif($jobKey > 0){
                
                if($totalReceivedQty > $data['ok_qty'][$jobKey-1]){
                    $errorMessage['ok_qty'.$process_id] = "Qty is invalid.";
                }
                elseif(!empty($data['ok_qty'][$jobKey-1]) && $data['ok_qty'][$jobKey-1] > 0 && $totalReceivedQty <$data['ok_qty'][$jobKey-1]){
                    $errorMessage['ok_qty'.$process_id] = "Qty is invalid.";
                }
                elseif(!empty($data['ok_qty'][$jobKey-1]) && $data['ok_qty'][$jobKey-1] > 0 && $totalReceivedQty <= 0){
                    $errorMessage['ok_qty'.$process_id] = "Qty is required.";
                }
            }
        }
		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
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
					$data['challan_file'] = $uploadData['file_name'];
				endif;
			endif;
			
			$this->printJson($this->outsource->saveLog($data));
		endif;	
	}

    public function getReceiveLogHtml(){
        $data = $this->input->post();
        $postData = [];
        $postData['outsource_without_process'] = 1;
        $postData['processDetail'] = 1;
        $postData['ref_id'] = $data['ref_id'];
        $postData['prc_id'] = $data['prc_id'];
        $postData['customWhere'] = 'prc_log.process_id IN('.$data['challan_process'].')';
        $logData = $this->sop->getProcessLogList($postData);
        $html="";
        if (!empty($logData)) :
            $i = 1;
            $processData = array_reduce($logData, function($processData, $process) { 
					$processData[$process->in_challan_no][] = $process; 
					return $processData; 
				}, []);
			foreach($processData AS $key=>$process){
				$firstRow=true;
				foreach($process AS $row){
					$html .= '<tr>';
					if($firstRow == true){
						$html .= '<td rowspan="'.count($process).'" class="text-center">'.$row->in_challan_no.'</td>';
						$html .= '<td rowspan="'.count($process).'" class="text-center">'.formatdate($row->trans_date).'</td>';
					}
					$html.='<td>'.$row->process_name.'</td>';
					$productionTag = '';
					$okQty = floatval($row->qty);
					if($okQty > 0){
					    $productionTag = '<a href="' . base_url('pos/printPRCLog/' . $row->id) . '" target="_blank"  title="Tag">'.$okQty.'</a>';
					}
					
					$html.='<td class="text-center">'.$productionTag.'</td>';
				    $rejTag = ''; $rejQty = floatval($row->rej_found);
					if(!empty($rejQty)){
						$rejTag .= '<a href="' . base_url('pos/printPRCRejLog/' . $row->id) . '" target="_blank"  title="Rejection Tag">'.$rejQty.'</a>';
					}
                    $html.= '<td class="text-center">'.$rejTag.'</td>';
					$html.='<td class="text-center">'.floatval($row->without_process_qty).'</td>';
                   

					if($firstRow == true){
                        $challan_file = (!empty($row->challan_file))? '<a href="' . base_url('assets/uploads/prc_log_attch/' . $row->challan_file) . '" target="_blank" class="btn btn-sm btn-outline-warning mr-1" title="Challan"><i class="fas fa-download"></i></a>':'';

						$deleteParam = "{'postData':{'id' : ".$row->id.",'last_log_id':'".$process[(count($process) - 1)]->id."'},'message' : 'Record','fndelete' : 'deleteLog','res_function':'getPrcLogResponse','controller':'outsource'}";
				        $deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
						$html.='<td rowspan="'.count($process).'" class="text-center">'.$deleteBtn.$challan_file.'</td>';
						$firstRow = false;
					}
					

					$html.='</tr>';
				}
			}
        else :
            $html = '<td colspan="12" class="text-center">No Data Found.</td>';
        endif;
        $this->printJson(['status'=>1,'tbodyData'=>$html]);
    }

    public function deleteLog(){
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->outsource->deleteLog($data));
        endif;
    }

    public function outSourcePrint($id){
        $this->data['outSourceData'] = $this->outsource->getOutSourceData(['id'=>$id]);
        $this->data['reqData'] = $this->sop->getChallanRequestData(['challan_id'=>$id,'itemDetail'=>1,'processDetail'=>1,'customWhere'=>'prc_challan_request.auto_log_id = 0']);
        $this->data['companyData'] = $this->outsource->getCompanyInfo();	

        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
    
        $pdfData = $this->load->view('outsource/print', $this->data, true);        
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
}
?>