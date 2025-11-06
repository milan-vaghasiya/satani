<?php

class ToolMethod extends MY_Controller{
    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Tool Method";
		$this->data['headData']->controller = "toolMethod";  
        $this->data['headData']->pageUrl = "toolMethod";
	}

    public function index(){
        $this->data['tableHeader'] = getProductionDtHeader("toolMethod");
        $this->load->view('tool_method/index',$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->toolMethod->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getToolMethodData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMethod(){
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['category_type'=>11,'final_category'=>1]);
        $this->load->view('tool_method/form',$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['method_code'])){ $errorMessage['method_code'] = "Code is required.";}
        if(empty($data['method_name'])){ $errorMessage['method_name'] = "Method is required.";}
        if(empty($data['die_category'])){ $errorMessage['die_category'] = "Die is required.";}
       
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['die_category'] = ((!empty($data['die_category']))?implode(",",$data['die_category']):'');
            $this->printJson($this->toolMethod->save($data));
        endif;
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->toolMethod->delete($data));
        endif;
    }
}
?>