<?php
class VendorPriceModel extends MasterModel{
    private $vendor_price_history = "vendor_price_history";

    public function getDTRows($data){
        $data['tableName'] = $this->vendor_price_history;
        $data['select'] = "vendor_price_history.*,party_master.party_name,item_master.item_name,item_master.item_code,employee_master.emp_name,GROUP_CONCAT(process_master.process_name SEPARATOR ', ') as process_name";
        $data['leftJoin']['party_master'] = "party_master.id= vendor_price_history.vendor_id";
        $data['leftJoin']['item_master'] = "item_master.id= vendor_price_history.item_id";
        $data['leftJoin']['process_master'] = "FIND_IN_SET(process_master.id, vendor_price_history.process_id) > 0";
        $data['leftJoin']['employee_master'] = "employee_master.id= vendor_price_history.approved_by";

        $data['where']['vendor_price_history.status'] = $data['status'];
        $data['group_by'][] = 'vendor_price_history.id';
        
        $data['order_by']['vendor_price_history.id'] = 'DESC'; 

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(vendor_price_history.created_at,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "vendor_price_history.rate";
        $data['searchCol'][] = "vendor_price_history.rate_unit";
        $data['searchCol'][] = "employee_master.emp_name";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getNextNo(){
        $queryData['tableName'] = $this->vendor_price_history;
        $queryData['select'] = "ifnull(MAX(trans_no + 1),1) as next_no";
        return $this->row($queryData)->next_no;
    }

    public function save($data){
		try 
        {
            $this->db->trans_begin();
			
            $priceData = [
                'id'=>$data['id'],
                'item_id'=>$data['item_id'],
                'vendor_id'=>$data['vendor_id'],
                'process_id'=>$data['process_id'],
                'rate'=>$data['rate'],
            ];
            
            if($this->checkDuplicate($priceData) > 0):
                $errorMessage['item_id'] = "Part is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            else:
                if(empty($data['id'])){
                    $data['trans_no'] = $this->getNextNo();
                }

                $transData = [
                    'id'=>$data['id'],
                    'item_id'=>$data['item_id'],
                    'vendor_id'=>$data['vendor_id'],
                    'process_id'=>$data['process_id'],
                    'rate'=>$data['rate'],
                    'rate_unit'=>$data['rate_unit'],
                    'cycle_time'=>$data['cycle_time'],
                    'input_weight'=>$data['input_weight'],
                    'remark'=>$data['remark']
                ];

                if(empty($data['id'])){
                    $transData['trans_no']=$data['trans_no'];
                    $transData['created_by']=$this->loginId;
                    $transData['created_at']=date("Y-m-d H:i:s");
                }else{
                    $transData['updated_by']=$this->loginId;
                    $transData['updated_at']=date("Y-m-d H:i:s");
                }
                $result = $this->store($this->vendor_price_history,$transData);
            endif;

			if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
  
	public function delete($id){
		try {
            $this->db->trans_begin();
			
            $result = $this->trash($this->vendor_price_history,['id'=>$id]);

			if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    /*
    Updated By :- Sweta @12/07/2023
    Used At :- VendorPrice/edit
    */
    public function getVendorPriceData($postData){
        $data['tableName'] = $this->vendor_price_history;
        $data['select'] = "vendor_price_history.*,party_master.party_name,item_master.item_name,process_master.process_name,employee_master.emp_name";
        $data['leftJoin']['party_master'] = "party_master.id= vendor_price_history.vendor_id";
        $data['leftJoin']['item_master'] = "item_master.id= vendor_price_history.item_id";
        $data['leftJoin']['process_master'] = "process_master.id= vendor_price_history.process_id";
        $data['leftJoin']['employee_master'] = "employee_master.id= vendor_price_history.approved_by";

        if(!empty($postData['id'])){ $data['where']['vendor_price_history.id'] = $postData['id']; }
        if(!empty($postData['vendor_id'])){ $data['where']['vendor_price_history.vendor_id'] = $postData['vendor_id']; }
        if(!empty($postData['item_id'])){ $data['where']['vendor_price_history.item_id'] = $postData['item_id']; }
        if(!empty($postData['process_id'])){ 
            $data['where']['vendor_price_history.process_id'] = $postData['process_id']; 
        }
        
        if(!empty($postData['customWhere'])){
            $data['customWhere'][] = $postData['customWhere']; 
        }
        if(!empty($postData['is_active'])){ $data['where']['vendor_price_history.is_active'] = $postData['is_active']; }

        return $this->row($data);
    }


    public function savePrice($data){
		try {
            $this->db->trans_begin();
			
            if(!empty($data['approved_by'])){
                $priceData = $this->getVendorPriceData(['id'=>$data['id']]);

                $this->edit($this->vendor_price_history,['vendor_id'=>$priceData->vendor_id,'item_id'=>$priceData->item_id,'process_id'=>$priceData->process_id,'status'=>$data['status']],['is_active'=>0]);

                if($data['status'] == 1){
                    $data['is_active'] = 1;
                }
            }
            $result = $this->store($this->vendor_price_history,$data);            
			
			if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    /* 
    Created by :- Sweta
    Date :- @03/07/2023
    Used at :- getPriceComparison ,itemNVndrTransHtml
    */
    public function getPriceComparison($data){
        $data['tableName'] = $this->vendor_price_history;
        $data['select'] = "vendor_price_history.*,party_master.party_name,employee_master.emp_name,GROUP_CONCAT(process_master.process_name SEPARATOR ', ') as process_name";
        $data['leftJoin']['party_master'] = "party_master.id = vendor_price_history.vendor_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = vendor_price_history.approved_by";
        $data['leftJoin']['process_master'] = "FIND_IN_SET(process_master.id, vendor_price_history.process_id) > 0";
        if(!empty($data['item_id'])){$data['where']['vendor_price_history.item_id'] = $data['item_id'];}
        if(!empty($data['vendor_id'])){$data['where']['vendor_price_history.vendor_id'] = $data['vendor_id'];}
        if(!empty($data['process_id'])){ $data['where_in']['vendor_price_history.process_id'] = $data['process_id']; }
        $data['group_by'][] = 'vendor_price_history.id';
        return $this->rows($data);
    }
    
    /* 
    Created by :- Sweta
    Date :- @03/07/2023
    Used at :- getPriceComparison 
    */
    public function checkDuplicate($priceData){
        $data['tableName'] = $this->vendor_price_history;
        if(!empty($priceData['item_id'])){ $data['where']['item_id'] = $priceData['item_id']; }
        if(!empty($priceData['vendor_id'])) { $data['where']['vendor_id'] = $priceData['vendor_id']; }
        if(!empty($priceData['process_id'])) { $data['where']['process_id'] = $priceData['process_id']; }
        if(!empty($priceData['rate'])) { $data['where']['rate'] = $priceData['rate']; }
        
        if(!empty($priceData['id']))
            $data['where']['id !='] = $priceData['id'];
        return $this->numRows($data);
    }
}