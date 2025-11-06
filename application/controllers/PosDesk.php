<?php
class PosDesk extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('PosModel','pos');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters( '<div class="error">', '</div>' );
	}
	
	public function index($posUrl = ""){
        $this->load->view("pos/pos_board");
    }

    public function authOperator(){
        $data = $this->input->post();
        //$this->form_validation->set_rules('user_name','Username','required|trim');
        //$this->form_validation->set_rules('password','Password','required|trim');
        if (empty($data['access_token'])):
			$this->form_validation->set_rules('user_name','Username','required|trim');
			$this->form_validation->set_rules('password','Password','required|trim');
		else:
			$this->form_validation->set_rules('access_token','Scanqrcode','required|trim');
			
		endif;
		if($this->form_validation->run() == true):
		    if(!empty($data['access_token'])){
		        $dUrl = decodeURL($data['access_token']);
    		    if(!empty($dUrl->type) && $dUrl->type == 'login_qr'){
    		        $data['access_token'] = $dUrl->emp_code;
    		    }else{
    		         redirect( base_url('posDesk/index') , 'refresh');
    		    }
		    }
            $result = $this->pos->checkOperatorAuth($data);
            if($result['status'] == 1):
                $posUrl = encodeURL($result['operator_id']);
                return redirect( base_url('pos/index/'.$posUrl) );
            else:
                $this->session->set_flashdata('loginError',$result['message']);
                redirect( base_url('posDesk/index') , 'refresh');
            endif;
        else:
            $this->load->view("pos/pos_board");
        endif;
    }
	
	

	public function setFinancialYear(){
		$year = $this->input->post('year');
		$this->login_model->setFinancialYear($year);
		echo json_encode(['status'=>1,'message'=>'Financial Year changed successfully.']);
	}
}
?>