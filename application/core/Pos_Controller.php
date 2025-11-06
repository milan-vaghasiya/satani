<?php 
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class Pos_Controller extends CI_Controller{

	public $termsTypeArray = ["Purchase","Sales"];
	public $gstPer = ['0'=>"NILL",'0.1'=>'0.10 %','0.25'=>"0.25 %",'1'=>"1 %",'3'=>"3%",'5'=>"5 %","6"=>"6 %","7.5"=>"7.50 %",'12'=>"12 %",'18'=>"18 %",'28'=>"28 %"];
	public $deptCategory = ["1"=>"Admin","2"=>"HR","3"=>"Purchase","4"=>"Sales","5"=>"Store","6"=>"QC","7"=>"General","8"=>"Machining"];
	public $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee","7"=>"Client"];
    public $gender = ["M"=>"Male","F"=>"Female","O"=>"Other"];
    public $systemDesignation = [1=>"Machine Operator",2=>"Line Inspector",3=>"Setter Inspector",4=>"Process Setter",5=>"FQC Inspector",6=>"Sale Executive",7=>"Designer",8=>"Production Executive"];
	public $maritalStatus = ["Married","UnMarried","Widow"];
	public $empType = [1=>"Permanent (Fix)",2=>"Permanent (Hourly)",3=>"Temporary"];
	public $empGrade = ["Grade A","Grade B","Grade C","Grade D"];
	//public $paymentMode = ['CASH','CHEQUE','NEFT','UPI'];
	public $paymentMode = ['CASH','CHEQUE','NEFT/RTGS/IMPS ','CARD','UPI'];

	public $partyCategory = [1=>'Customer',2=>'Supplier',3=>'Vendor',4=>'Ledger'];
	public $suppliedType = [1=>'Goods',2=>'Services',3=>'Goods & Services'];
	public $gstRegistrationTypes = [1=>'Registerd',2=>'Composition',3=>'Overseas',4=>'Un-Registerd'];
	public $automotiveArray = ["1" => 'Yes', "2" => "No"];
	public $vendorTypes = ['Manufacture', 'Service'];

	public $itemTypes = [1 => "Finish Goods", 2 => "Consumable", 3 => "Raw Material", 5 => "Machineries"/* , 4 => "Capital Goods", 6 => "Instruments", 7 => "Gauges", 8 => "Services", 9 => "Packing Material", 10 => "Scrap" */]; // 20-02-2024
	public $stockTypes = [0=>"None",1=>'Batch Wise',2=>"Serial Wise"];
	public $fgColorCode = ["WHITE"=>"W","GREY"=>"G"];
	public $fgCapacity = ["3 TON"=>"3T","5 TON"=>"5T"];

	//Crm Status
	public $leadFrom = ["Facebook","Indiamart","Instagram","Facebook Comments","Trade India","Exporter India","Facebook Admanager"];
	public $leadStatus = ["Initited", "Appointment Fixed", "Qualified", "Enquiry Generated", "Proposal", "In Negotiation", "Confirm", "Close"];
	public $appointmentMode = [1 => "Phone", 2 => "Email", 3 => "Visit", 4 => "Other"];
	public $followupStage = [0 => 'Open', 1 => "Confirmed", 2 => "Hold", 3 => "Won", 4 => "Lost", 5 => "Enquiry" , 6 => "Quatation"];

	//Types of Invoice
	public $purchaseTypeCodes = ["'PURGSTACC'","'PURIGSTACC'","'PURJOBGSTACC'","'PURJOBIGSTACC'","'PURURDGSTACC'","'PURURDIGSTACC'","'PURTFACC'","'PUREXEMPTEDTFACC'","'IMPORTACC'","'IMPORTSACC'","'SEZRACC'"/* ,"'SEZSGSTACC'","'SEZSTFACC'","'DEEMEDEXP'" */];

	public $salesTypeCodes = ["'SALESGSTACC'","'SALESIGSTACC'","'SALESJOBGSTACC'","'SALESJOBIGSTACC'","'SALESTFACC'","'SALESEXEMPTEDTFACC'","'EXPORTGSTACC'","'EXPORTTFACC'","'SEZSGSTACC'","'SEZSTFACC'","'DEEMEDEXP'"];
	
	public function __construct(){
		parent::__construct();
		//echo '<br><br><hr><h1 style="text-align:center;color:red;">We are sorry!<br>Your ERP is Updating New Features</h1><hr><h2 style="text-align:center;color:green;">Thanks For Co-operate</h1>';exit;
        $this->isLoggedin();
		$this->data['headData'] = new StdClass;
		$this->load->library('form_validation');
		
		$this->load->model('masterModel');
		$this->load->model('TransactionMainModel','transMainModel');
	
		$this->load->model("hr/EmployeeModel","employee");
		$this->load->model("StoreLocationModel","storeLocation");
		
		$this->load->model('ItemModel','item');
	
		/* Production Model */
		$this->load->model('ProcessModel','process');
		$this->load->model('RejectionCommentModel','comment');
		$this->load->model('sopModel','sop');
		$this->load->model('PrcMaterialIssueModel','prcMaterialIssue');
		$this->load->model('RejectionReviewModel','rejectionReview');
		$this->load->model('PosModel', 'pos');
		$this->load->model('StockTransModel','itemStock');
		$this->load->model('StoreModel','store');
		$this->load->model("hr/DepartmentModel","department");
		$this->load->model("DieProductionModel","dieProduction");
		
		$this->setSessionVariables(["masterModel",'transMainModel',"employee",'item','process','comment','sop', 'pos','itemStock',"storeLocation",'store',"department","dieProduction"]);

		$this->data['companyDetail'] = $this->masterModel->getCompanyInfo($this->session->userdata('cm_id'));
	}

	public function setSessionVariables($modelNames){
		$this->data['dates'] = $this->dates = explode(' AND ',$this->session->userdata('financialYear'));
        $this->data['shortYear'] = $this->shortYear = date('y',strtotime($this->dates[0])).'-'.date('y',strtotime($this->dates[1]));
		$this->data['startYear'] = $this->startYear = date('Y',strtotime($this->dates[0]));
		$this->data['endYear'] = $this->endYear = date('Y',strtotime($this->dates[1]));
		$this->data['startYearDate'] = $this->startYearDate = date('Y-m-d',strtotime($this->dates[0]));
		$this->data['endYearDate'] = $this->endYearDate = date('Y-m-d',strtotime($this->dates[1]));

		$this->loginId = $this->session->userdata('loginId');
		$this->userName = $this->session->userdata('emp_name');
		$this->userRole = $this->session->userdata('role');
		$this->userRoleName = $this->session->userdata('roleName');
		$this->partyId = $this->session->userdata('partyId');
		$this->cm_id = $this->data['cm_id'] = $this->session->userdata('cm_id');
		$this->processId = $this->session->userdata('processId');

		$this->RTD_STORE = $this->session->userdata('RTD_STORE');
		$this->SCRAP_STORE = $this->session->userdata('SCRAP_STORE');
		$this->CUT_STORE = $this->session->userdata('CUT_STORE');
		$this->FIR_STORE = $this->session->userdata('FIR_STORE');
		$this->PACKING_STORE = $this->session->userdata('PACKING_STORE');
		$this->FORGE_STORE = $this->session->userdata('FORGE_STORE');
		$this->MACHINING_STORE = $this->session->userdata('MACHINING_STORE');

		$models = $modelNames;
		foreach($models as $modelName):
			$modelName = trim($modelName);
			$this->{$modelName}->dates = $this->dates;
			$this->{$modelName}->shortYear = $this->shortYear;
			$this->{$modelName}->startYear = $this->startYear;
			$this->{$modelName}->endYear = $this->endYear;
			$this->{$modelName}->startYearDate = $this->startYearDate;
			$this->{$modelName}->endYearDate = $this->endYearDate;

			$this->{$modelName}->loginId = $this->loginId;
			$this->{$modelName}->userName = $this->userName;
			$this->{$modelName}->userRole = $this->userRole;
			$this->{$modelName}->userRoleName = $this->userRoleName;
			$this->{$modelName}->partyId = $this->partyId;
			$this->{$modelName}->cm_id = $this->cm_id;
			$this->{$modelName}->processId = $this->processId;

			$this->{$modelName}->RTD_STORE = $this->RTD_STORE;
			$this->{$modelName}->SCRAP_STORE = $this->SCRAP_STORE;
			$this->{$modelName}->CUT_STORE = $this->CUT_STORE;
			$this->{$modelName}->FIR_STORE = $this->FIR_STORE;
			$this->{$modelName}->PACKING_STORE = $this->PACKING_STORE;
			$this->{$modelName}->FORGE_STORE = $this->FORGE_STORE;
			$this->{$modelName}->MACHINING_STORE = $this->MACHINING_STORE;
		endforeach;
		return true;
	}
	
	public function isLoggedin(){
		if(!$this->session->userdata("operatorId")):
			echo '<script>window.location.href="'.base_url('posDesk').'";</script>';
		endif;
		return true;
	}
	
	public function printJson($data){
		print json_encode($data);exit;
	}
	
	public function checkGrants($url){
		$empPer = $this->session->userdata('emp_permission');
		if(!array_key_exists($url,$empPer)):
			redirect(base_url('error_403'));
		endif;
		return true;
	}
	
	/**** Generate QR Code ****/
	public function getQRCode($qrData,$dir,$file_name){
		if(isset($qrData) AND isset($file_name)):
			$file_name .= '.png';
			/* Load QR Code Library */
			$this->load->library('ciqrcode');
			
			if (!file_exists($dir)) {mkdir($dir, 0775, true);}

			/* QR Configuration  */
			$config['cacheable']    = true;
			$config['imagedir']     = $dir;
			$config['quality']      = true;
			$config['size']         = '1024';
			$config['black']        = array(255,255,255);
			$config['white']        = array(255,255,255);
			$this->ciqrcode->initialize($config);
	  
			/* QR Data  */
			$params['data']     = $qrData;
			$params['level']    = 'L';
			$params['size']     = 10;
			$params['savename'] = FCPATH.$config['imagedir']. $file_name;
			
			$this->ciqrcode->generate($params);

			return $dir. $file_name;
		endif;

		return false;
	}

	public function getTableHeader(){
		$data = $this->input->post();

		$response = call_user_func_array($data['hp_fn_name'],[$data['page']]);
		
		$result['theads'] = (isset($response[0])) ? $response[0] : '';
		$result['textAlign'] = (isset($response[1])) ? $response[1] : '';
		$result['srnoPosition'] = (isset($response[2])) ? $response[2] : 1;
		$result['sortable'] = (isset($response[3])) ? $response[3] : '';

		$this->printJson(['status'=>1,'data'=>$result]);
	}

	public function getPartyDetails(){
        $data = $this->input->post();
        $partyDetail = $this->party->getParty($data);
        $gstDetails = $this->party->getPartyGSTDetail(['party_id'=>$data['id']]);
        $this->printJson(['status'=>1,'data'=>['partyDetail'=>$partyDetail,'gstDetails'=>$gstDetails]]);
    }

	public function getItemDetail(){
		$data = $this->input->post();
		$itemDetail = $this->item->getItem($data);

		if(empty($itemDetail)):
			$this->printJson(['status'=>0,'message'=>'Item Not Found.']);
		else:
			$this->printJson(['status'=>1,'data'=>['itemDetail'=>$itemDetail]]);
		endif;
	}

	public function getPartyInvoiceList(){
        $data = $this->input->post();
        $this->printJson($this->transMainModel->getPartyInvoiceList($data));
    }

	public function trashFiles(){
        /** define the directory **/
        $dirs = [
            realpath(APPPATH . '../assets/uploads/qr_code/'),
            //realpath(APPPATH . '../assets/uploads/import_excel/'),
            /* realpath(APPPATH . '../assets/uploads/eway_bill/'),
            realpath(APPPATH . '../assets/uploads/eway_bill_detail/'),
            realpath(APPPATH . '../assets/uploads/e_inv/') */
        ];

        foreach($dirs as $dir):
            $files = array();
            $files = scandir($dir);
            unset($files[0],$files[1]);

            /*** cycle through all files in the directory ***/
            foreach($files as $file):
                /*** if file is 24 hours (86400 seconds) old then delete it ***/
                if(time() - filectime($dir.'/'.$file) > 86400):
                    unlink($dir.'/'.$file);
                    //print_r(filectime($dir.'/'.$file)); print_r("<hr>");
                endif;
            endforeach;
        endforeach;

        return true;
    }

	public function importExcelFile($file,$path,$sheetName){
		$excel_file = '';
		if(isset($file['name']) || !empty($file['name']) ):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $file['name'];
			$_FILES['userfile']['type']     = $file['type'];
			$_FILES['userfile']['tmp_name'] = $file['tmp_name'];
			$_FILES['userfile']['error']    = $file['error'];
			$_FILES['userfile']['size']     = $file['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/'.$path);
			$config = ['file_name' => time()."_UP_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' =>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['excel_file'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$excel_file = $uploadData['file_name'];
			endif;

			if(!empty($excel_file)):
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath.'/'.$excel_file);
				$fileData = array($spreadsheet->getSheetByName($sheetName)->toArray(null,true,true,true));
				return $fileData;
			else:
				return ['status'=>2,'message'=>'Data not found...!'];
			endif;
		else:
			return ['status'=>2,'message'=>'Please Select File!'];
		endif;
    }

	public function getStatesOptions($postData=array()){
        $country_id = (!empty($postData['country_id']))?$postData['country_id']:$this->input->post('country_id');

        $result = $this->party->getStates(['country_id'=>$country_id]);

        $html = '<option value="">Select State</option>';
        foreach ($result as $row) :
            $selected = (!empty($postData['state_id']) && $row->id == $postData['state_id']) ? "selected" : "";
            $html .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
        endforeach;

        if(!empty($postData)):
            return $html;
        else:
            $this->printJson(['status'=>1,'result'=>$html]);
        endif;
    }

    public function getCitiesOptions($postData=array()){
        $state_id = (!empty($postData['state_id']))?$postData['state_id']:$this->input->post('state_id');

        $result = $this->party->getCities(['state_id'=>$state_id]);
        
        $html = '<option value="">Select City</option>';
        foreach ($result as $row) :
            $selected = (!empty($postData['city_id']) && $row->id == $postData['city_id']) ? "selected" : "";
            $html .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
        endforeach;

        if(!empty($postData)):
            return $html;
        else:
            $this->printJson(['status'=>1,'result'=>$html]);
        endif;
    }
}
?>