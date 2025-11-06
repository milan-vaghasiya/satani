<?php
class VendorPrice extends MY_Controller
{
    private $indexPage = "vendor_price/index";
    private $formPage = "vendor_price/form";
    private $edit_form = "vendor_price/edit_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Vendor Price";
		$this->data['headData']->controller = "vendorPrice";
        $this->data['headData']->pageUrl = "vendorPrice";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionDTHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->vendorPrice->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getVendorPriceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addVendorPrice(){
        $this->data['productList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
        $this->data['processData'] = $this->process->getProcessList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['vendor_id'])){
			$errorMessage['vendor_id'] = "Vendor is required.";
        }

        if(empty($data['item_id'])){
			$errorMessage['item_id'] = "Part is required.";
        }

        if(empty($data['process_id'])){
			$errorMessage['process_id'] = "Process is required.";
        }

        if(empty($data['rate_unit'])){
			$errorMessage['rate_unit'] = "Rate per unit is required.";
        }

        if(empty($data['input_weight'])){
			$errorMessage['input_weight'] = "Input Weight is required.";
        }

        if(empty($data['rate']) || $data['rate'] <= 0){
			$errorMessage['rate'] = "Rate is required.";
        }

        unset($data['processSelect']);

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->vendorPrice->save($data);
            $result['tbodyData'] = $this->itemNVndrTransHtml(['item_id'=>$data['item_id'],'vendor_id'=>$data['vendor_id']]);
            $this->printJson($result);
        endif;
    }

    /*** Vendor Price Item & Vendor wise Transactions */
    public function itemNVndrTransHtml($data){
        $result = $this->vendorPrice->getPriceComparison(['item_id'=>$data['item_id'],'vendor_id'=>$data['vendor_id']]);
              
        $tbodyData ='';
        if(!empty($result)){
            foreach($result as $row){
                $tbodyData .='<tr class="text-center">
                    <td>'.$row->process_name.'</td>
                    <td>'.$row->rate.'</td>
                    <td>'.(($row->rate_unit == 1)?'Per Piece':'Per Kg').'</td>
                    <td>'.$row->cycle_time.'</td>
                </tr>';
            }
        }
        else{
            $tbodyData .= '<tr class="text-center"><td colspan="4">No Data Available.</td></tr>';
        }
        return $tbodyData;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $dataRow = $this->vendorPrice->getVendorPriceData(['id'=>$id]);
        $this->data['productList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['tbodyData'] = $this->itemNVndrTransHtml(['item_id'=>$dataRow->item_id,'vendor_id'=>$dataRow->vendor_id]);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->vendorPrice->delete($id));
        endif;
    }

   

    public function savePrice(){
        $data = $this->input->post();
        
        if(empty($data['rate_unit'])){
            $errorMessage['rate_unit'] = "Rate per unit is required.";
        }

        if(empty($data['rate'])){
            $errorMessage['rate'] = "Rate  is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->vendorPrice->savePrice($data));
        endif;
    }

    public function approvePrice(){
        $data = $this->input->post();
        
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $data['status'] = 1;
            $data['approved_by'] = $this->loginId;
            $data['approved_at'] = date("Y-m-d H:i:s");
            $this->printJson($this->vendorPrice->savePrice($data));
        endif;
    }

    /* Created by Sweta */
    public function getPriceComparison(){
        $data = $this->input->post();
        $result = $this->vendorPrice->getPriceComparison($data);
              
        $tbodyData ='';
        if(!empty($result)){
            foreach($result as $row){
                $tbodyData .='<tr class="text-center">
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->rate.'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.formatDate($row->approved_at).'</td>
                </tr>';
            }
        }
        else{
            $tbodyData .= '<tr class="text-center"><td colspan="4">No Data Available.</td></tr>';
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    /* Created by Sweta @03/07/2023 */
    public function rejectPrice(){
        $data = $this->input->post();
        
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $data['status'] = 2;
            $data['approved_by'] = $this->loginId;
            $data['approved_at'] = date("Y-m-d H:i:s");
            $this->printJson($this->vendorPrice->savePrice($data));
        endif;
    }
}
?>