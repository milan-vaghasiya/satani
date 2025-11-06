<?php
class BulkRejection extends MY_Controller
{
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();

		$this->data['headData']->pageTitle = "Bulk Rejection";
		$this->data['headData']->controller = "bulkRejection";
		$this->data['headData']->pageUrl = "";
	}

    public function index(){
        $controller = 'bulkRejection';
        $this->data['tableHeader'] = getProductionDtHeader($controller);
        $this->data['rejectionComments'] = $this->comment->getCommentList(['type'=>1]);

        $this->load->view("bulk_rejection/index",$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post(); 
        $data['source'] = 'MFG';
        $result = $this->rejectionReview->getDTRows($data);
        // print_r($result); exit;

        $sendData = array();$i=($data['start']+1);

        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getbulkRejectionData($row);
        endforeach;
        $result['data'] = $sendData;
        
        $this->printJson($result);
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

                if ($data['qty'][$key] > ($dataRow->pending_qty)) {
                    $errorMessage['qty'] = "Qty is Invalid.";
                }

                // if($dataRow->process_by == 3){
                //     $data['machine_id'] = null;
                //     $data['operator_id'] = null;
                //     $data['in_challan_no'] = $dataRow->in_challan_no;
                // }
                // else if($dataRow->process_by != 3){
                //     $data['machine_id'] = $dataRow->processor_id;
                //     $data['operator id'] = $dataRow->operator_id;
                //     $data['in_challan_no'] = NULL;
                // }

                $data['prc_id'] = $dataRow->prc_id ?? '';
                $data['log_id'] = $value ?? '';
                $data['item_id'] = $dataRow->item_id ?? '';
                $data['process_id'] = $dataRow->process_id ?? '';
                $data['completed_process'] = $dataRow->completed_process ?? 0;
                $data['rr_by'] = $dataRow->process_by == 3 ? $dataRow->processor_id : 0;

                $data['machine_id'] = $dataRow->process_by == 3 ? NULL : $dataRow->processor_id;
                $data['operator_id'] = $dataRow->process_by == 3 ? NULL : $dataRow->operator_id;
                $data['in_challan_no'] = $dataRow->process_by == 3 ? $dataRow->in_challan_no : NULL;

                $records[] = $data;
                // print_r($dataRow);
            }
            if (!empty($errorMessage)) :
                $this->printJson(['status' => 0, 'message' => $errorMessage]);
            else :
                print_r($records); exit;

                $data['created_at'] = date("Y-m-d H:i:s");
                $data['created_by'] = $this->session->userdata('loginId');
                $this->printJson($this->rejectionReview->saveReview($data));
            endif;
        }
    }
}
?>