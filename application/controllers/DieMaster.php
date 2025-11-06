<?php
class DieMaster extends MY_Controller{
    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Tool Master";
		$this->data['headData']->controller = "dieMaster";  
        $this->data['headData']->pageUrl = "dieMaster";
	}

    public function index(){
        $this->data['tableHeader'] = getMasterDtHeader("dieMaster");
        $this->load->view('die_master/index',$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->dieMaster->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getDieMasterData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDie(){
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
        $this->data['toolMethodList'] = $this->toolMethod->getToolMethodList();
        $this->load->view('die_master/form',$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['tool_method'])){ $errorMessage['tool_method'] = "Tool is required.";}
        if(empty($data['item_id'])){ $errorMessage['item_id'] = "Product is required.";}
        // if(empty($data['grade_id'])){ $errorMessage['grade_id'] = "Grade is required.";}
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieMaster->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->dieMaster->getDieData(['id'=>$data['id'],'single_row'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['category_type'=>11,'final_category'=>1]);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view('die_master/form',$this->data);
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dieMaster->delete($data));
        endif;
    }

}
?>