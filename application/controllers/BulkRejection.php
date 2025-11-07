<?php
class BulkRejection extends MY_Controller
{
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();

		$this->data['headData']->pageTitle = "Bulk Rejection";
		$this->data['headData']->controller = "bulkRejection";
	}

    public function index(){
        $this->data['rejectionComments'] = $this->comment->getCommentList(['type'=>1]);
        $this->load->view("bulk_rejection/index",$this->data);
    }

    public function getRejectionReviewRows(){
        $data = $this->input->post(); 
        $result = $this->rejectionReview->getRejectionReviewData($data);

        $tbody = '';
		foreach($result as $key => $row){
            
            $selectBox = '<input type="checkbox" name="log_id[]" id="log_id_'.($key+1).'" class="filled-in chk-col-success BulkRejection" value="'.$row->id.'"><label for="log_id_'.($key+1).'"></label>';	

            $pendingQtyInput = '<input type="text" name="qty[]" class="form-control qty_input numericOnly" value="'.$row->pending_qty.'" disabled> 
            <div class="error qty'.($row->id).' text-left"></div>';	
            
            $tbody .= '<tr>
                <td>'.($key+1).'</td>
                <td>'.$selectBox.'</td>
                <td>'.$row->prc_number.'</td>
                <td>'.'['.$row->item_code.'] '.$row->item_name.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td>'.$row->process_name.'</td>
                <td>'.(!empty($row->processor_name)?$row->processor_name:'').'</td>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->rej_found.'</td>
                <td>'.$row->review_qty.'</td>
                <td>'.$pendingQtyInput.'</td>
            </tr>';
        }

        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }

    public function saveReview(){
        $data = $this->input->post(); 
        
        $errorMessage = array();
        if (empty($data['rr_reason'])){
            $errorMessage['rr_reason'] = "Reason is required.";
        }
        if (empty($data['log_id'])) {
            $errorMessage['rr_reason'] = "Please select one record.";
        }
        if (!empty($errorMessage)){
            $this->printJson(['status' => 0, 'message' => $errorMessage]);

        }else{
            $records = [];
            
            foreach ($data['log_id'] as $key => $value) {
                $dataRow = $this->sop->getProcessLogList(['id' => $value,'itemDetail' => 1,'rejection_review_data' => 1,'single_row'=>1]);  

                if ($data['qty'][$key] > $dataRow->pending_qty) {
                    $errorMessage['qty'.$value] = "Qty is Invalid.";
                }else{
                    $postData['id'] = '';
                    $postData['prc_id'] = $dataRow->prc_id;
                    $postData['log_id'] = $value;
                    $postData['item_id'] = $dataRow->item_id;
                    $postData['decision_type'] = 1;
                    $postData['process_id'] = $dataRow->process_id;
                    $postData['completed_process'] = $dataRow->completed_process ?? 0;
                    $postData['source'] = 'MFG';
                    $postData['qty'] = $data['qty'][$key];
                    $postData['rr_reason'] = $data['rr_reason'];
                    $postData['rr_stage'] = $dataRow->process_id;
                    $postData['rr_by'] = $dataRow->process_by == 3 ? $dataRow->processor_id : 0;
                    $postData['rr_comment'] = null;
                    $postData['rr_type'] = $postData['rr_stage'] == 0 ? 'Raw Material' : 'Machine';
                    
                    $postData['operator_id'] = $dataRow->process_by == 3 ? NULL : ($dataRow->process_by == 1 ? $dataRow->operator_id : 0);
                    $postData['machine_id'] = $dataRow->process_by == 3 ? NULL : ($dataRow->process_by == 1 ? $dataRow->processor_id : 0);
                    $postData['in_ch_no'] = $dataRow->process_by == 1 ? NULL : (!empty($dataRow->in_challan_no) ? $dataRow->in_challan_no : NULL);
                    $postData['created_at'] = date("Y-m-d H:i:s");
                    $postData['created_by'] = $this->session->userdata('loginId');
                    $records[] = $postData;
                }
            }
            if (!empty($errorMessage)) :
                $this->printJson(['status' => 0, 'message' => $errorMessage]);
            else :
                if(!empty($records)){
                    foreach ($records as $record) {
                        $insertRecord = $this->rejectionReview->saveReview($record);
                    }
                    $this->printJson($insertRecord);
                    
                }else{
                    $this->printJson(['status' => 2, 'message' => 'Something Went Wring Please Try Again']);
                }
            endif;
        }
    }
}
?>