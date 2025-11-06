<?php
class DieProductionModel extends MasterModel{
    // private $history_type = [2=>1,3=>2,4=>3,6=>4];
    
    public function getDTRows($data){
        $data['tableName'] = 'die_work_order';
        $data['select'] = 'die_work_order.*,die_master.die_code,item_category.category_name AS die_name,item_master.item_name,item_master.item_code,rm.item_code AS rm_code,rm.item_name As rm_name,IF(die_work_order.trans_type = 1,"Regular","Rework") as wo_type';

        $data['select'] .= ',(SELECT COUNT(die_log.id) FROM die_log WHERE die_log.is_delete = 0 AND die_log.wo_id = die_work_order.id) AS log_count';
        $data['leftJoin']['die_master'] = 'die_work_order.die_id = die_master.id';
        $data['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        $data['leftJoin']['item_master'] = 'item_master.id = die_master.item_id';
        $data['leftJoin']['item_master rm'] = 'rm.id = die_work_order.die_block_id';
        
        if($data['status'] == 0){
            $data['where']['die_work_order.trans_type'] = 1;
            $data['where']['die_work_order.die_block_id'] = 0;
        }elseif($data['status'] == 1){
            $data['customWhere'][] = '(die_work_order.die_block_id > 0 OR die_work_order.trans_type = 2)';
            // $data['where']['die_work_order.die_block_id >'] = 0;
            $data['where']['die_work_order.status'] = 1;
        }elseif($data['status'] == 5){
            $data['where']['die_work_order.status'] =5;
        }
        else{
            $data['where_in']['die_work_order.status'] ='2,3,4,6';
        }
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(die_work_order.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "die_work_order.trans_number";
        $data['searchCol'][] = 'IF(die_work_order.trans_type = 1,"Regular","Rework") ';
        $data['searchCol'][] = "die_master.die_code";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "die_work_order.qty";
        $data['searchCol'][] = "die_work_order.ok_qty";
        $data['searchCol'][] = "(die_work_order.qty - die_work_order.ok_qty)";
        $data['searchCol'][] = "rm.item_name";
        $data['searchCol'][] = "";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getNextWoNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'die_work_order';
        $queryData['select'] = "MAX(trans_no ) as trans_no ";
		
		$queryData['where']['die_work_order.trans_date >='] = $this->startYearDate;
		$queryData['where']['die_work_order.trans_date <='] = $this->endYearDate;

		$trans_no = $this->specificRow($queryData)->trans_no;
		$trans_no = (!empty($trans_no))?($trans_no + 1):1;
		return $trans_no;
    }

     public function getNextDieNo($data){
		$queryData = array(); 
		$queryData['tableName'] = 'die_register';
        $queryData['select'] = "MAX(die_no ) as die_no ";
		
		$queryData['where']['die_register.die_id'] = $data['die_id'];
		

		$die_no = $this->specificRow($queryData)->die_no;
		$die_no = (!empty($die_no))?($die_no + 1):1;
		return $die_no;
    }
    public function save($data){
        try{
            $this->db->trans_begin();
            $trans_no = $this->dieProduction->getNextWoNo();
            $trans_number = 'DWO/'.$this->shortYear.'/'.$trans_no;
            
            foreach($data['die_id'] AS $key=>$die_id){
                if(!empty($data['qty'][$key]) && $data['qty'][$key] > 0){
                    $dieData = $this->dieMaster->getDieData(['id'=>$die_id,'single_row'=>1]);
                    $woData = [
                        'id'=>'',
                        'trans_type'=>((!empty($data['trans_type']))?$data['trans_type']:1),
                        'ref_id'=>((!empty($data['ref_id']))?$data['ref_id']:0),
                        'trans_date'=>$data['trans_date'],
                        'trans_no'=>$trans_no,
                        'trans_number'=>$trans_number,
                        'die_id'=>$die_id,
                        'item_id'=>$dieData->item_id,
                        'qty'=>$data['qty'][$key],
                        'tool_method'=>$data['tool_method'],
                    ];
                    $result = $this->store("die_work_order",$woData);
                }
            }

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function delete($data){
        try{
            $this->db->trans_begin();
            $result = $this->trash("die_work_order",['id'=>$data['id']]);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    /* Material Issue */
    public function saveMaterialIssue($data){
		try {
			$this->db->trans_begin();
			
            $result = $this->store('die_work_order',['id'=>$data['id'],'die_block_id'=>$data['die_block_id'],'mtr_qty'=>array_sum($data['batch_qty'])]);
            foreach($data['batch_qty'] AS $key=>$batch_qty){
                if(!empty($batch_qty) && $batch_qty > 0){
                    $issue_no = $this->store->getNextIssueNo();
                    //Issue Effect
                    $issueData = [
                                'id' => '',
                                'issue_type' => 3,
                                'issue_no' => $issue_no,
                                'issue_number' => 'ISU/'.str_pad($issue_no, 5, '0', STR_PAD_LEFT),
                                'issue_date' => date("Y-m-d"),
                                'item_id' => $data['die_block_id'],
                                'batch_no' => $data['batch_no'][$key],
                                'heat_no' => $data['heat_no'][$key],
                                'prc_id' => $data['id'],
                                'issue_qty' => $data['batch_qty'][$key],
                            ];
                    $stkResult = $this->store('issue_register', $issueData, 'Issue Requisition');

                    $stockMinusQuery = [
                        'id' => "",
                        'trans_type' => 'SDI',
                        'trans_date' => date("Y-m-d"),
                        'location_id'=> $data['location_id'][$key],
                        'batch_no' => $data['batch_no'][$key],
                        'item_id' => $data['die_block_id'],
                        'qty' => $data['batch_qty'][$key],
                        'p_or_m' => -1,
                        'main_ref_id' => $stkResult['insert_id'],
                        'child_ref_id' =>  $data['id'],
                        'ref_no' => 'ISU/'.str_pad($issue_no, 5, '0', STR_PAD_LEFT),
                    ];
                    $issueTrans = $this->store('stock_trans', $stockMinusQuery);
                }
            }
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function getProductionData($postData){
        $data['tableName'] = 'die_work_order';
        $data['select'] = 'die_work_order.*,die_master.die_code,die_master.category_id';

        $data['leftJoin']['die_master'] = 'die_work_order.die_id = die_master.id';

        if(!empty($postData['id'])){
            $data['where_in']['die_work_order.id'] = $postData['id'];
        }
        if(!empty($postData['status'])){
            $data['where']['die_work_order.status'] =$postData['status'];
        }
        if(!empty($postData['item_id'])){
            $data['where']['die_work_order.item_id'] =$postData['item_id'];
        }
        if(!empty($postData['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }
       
    }

    public function saveProductionLog($data){
        try{
            $this->db->trans_begin();

            $result = $this->store("die_log",$data);
            if($data['process_by'] == 2){
                $setData = Array();
                $setData['tableName'] = 'die_work_order';
                $setData['where']['id'] = $data['wo_id'];
                $setData['set']['ch_qty'] = 'ch_qty, - '.$data['qty'];
                $this->setValue($setData);
            }
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getProductionLogData($postData){
        $data['tableName'] = 'die_log';
        $data['select'] = 'die_log.*';


        if(!empty($postData['workOrderDetail'])){
            $data['select'] .= ',die_work_order.status';
            $data['leftJoin']['die_work_order'] = 'die_work_order.id = die_log.wo_id';
        }

        if(!empty($postData['processDetail'])){
            $data['select'] .= ',process_master.process_name';
            $data['leftJoin']['process_master'] = 'process_master.id = die_log.process_id';
        }

        if(!empty($postData['processorDetail'])){
            $data['select'] .= ',(CASE WHEN die_log.process_by = 2 THEN (SELECT party_master.party_name FROM party_master WHERE party_master.is_delete = 0 AND party_master.id = die_log.processor_id) ELSE (SELECT CONCAT(item_master.item_code," ",item_master.item_name) FROM item_master WHERE is_delete = 0 AND item_master.id = die_log.processor_id) END) AS processor_name';
        }

        if(!empty($postData['operatorDetail'])){
            $data['select'] .= ',employee_master.emp_name';
            $data['leftJoin']['employee_master'] = 'employee_master.id = die_log.operator_id';
        }

        if(!empty($postData['id'])){
            $data['where']['die_log.id'] = $postData['id'];
        }
        if(!empty($postData['wo_id'])){
            $data['where']['die_log.wo_id'] = $postData['wo_id'];
        }
        if(isset($postData['ref_trans_id'])){
            $data['where']['die_log.ref_trans_id'] = $postData['ref_trans_id'];
        }
        if(!empty($postData['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }
       
    }

    public function deleteLog($data){
        try{
            $this->db->trans_begin();
            $logData = $this->getProductionLogData(['id'=>$data['id'],'workOrderDetail'=>1,'single_row'=>1]);
            if(!empty($logData)){
                if(!in_array($logData->status,[1,5])){
                    return ['status'=>0,'message'=>'You can not delete this log'];
                }
                $result = $this->trash("die_log",['id'=>$data['id']]);
                if( $logData->process_by == 2){
                    /* $this->store("die_work_order",['id'=>$logData->wo_id,'status'=>5]); */
                    $setData = Array();
                    $setData['tableName'] = 'die_work_order';
                    $setData['where']['id'] = $logData->wo_id;
                    $setData['set']['ch_qty'] = 'ch_qty, + '.$logData->qty;
                    $this->setValue($setData);
                }
            }else{
                return ['status'=>0,'message'=>'Log already deleted'];
            }
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveChallanRequest($data){
        try{
            $this->db->trans_begin();

            $result = $this->store("die_challan_request",$data);
            
            $setData = Array();
            $setData['tableName'] = 'die_work_order';
            $setData['where']['id'] = $data['wo_id'];
            $setData['set']['ch_qty'] = 'ch_qty, + '.$data['qty'];
            $this->setValue($setData);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function deleteChallanReq($data){
        try{
            $this->db->trans_begin();
            $logData = $this->getChallanReqData(['id'=>$data['id'],'single_row'=>1]);
            if(!empty($logData)){
                if($logData->challan_id > 0){
                    return ['status'=>0,'message'=>'You can not delete this Request'];
                }
                $this->store("die_work_order",['id'=>$logData->wo_id,'status'=>1]);
                $result = $this->trash("die_challan_request",['id'=>$data['id']]);
                $setData = Array();
                $setData['tableName'] = 'die_work_order';
                $setData['where']['id'] = $logData->wo_id;
                $setData['set']['ch_qty'] = 'ch_qty, - '.$logData->qty;
                $this->setValue($setData);
            }else{
                return ['status'=>0,'message'=>'Log already deleted'];
            }
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getChallanReqData($postData){
        $data['tableName'] = 'die_challan_request';
        $data['select'] = 'die_challan_request.*';

        if(!empty($postData['receiveDetail'])){
            $data['select'] .= ',IFNULL((SELECT SUM(qty) FROM die_log WHERE die_log.is_delete = 0 AND die_log.process_by = 2 AND die_log.ref_id = die_challan_request.challan_id AND die_log.ref_trans_id = die_challan_request.id),0) AS receive_qty';
        }

        if(!empty($postData['workOrderDetail'])){
            $data['select'] .= ',die_work_order.trans_number';
            $data['leftJoin']['die_work_order'] = 'die_work_order.id = die_challan_request.wo_id';
        }

        if(!empty($postData['dieMasterDetail'])){
            $data['select'] .= ',die_master.die_code,item_category.category_name AS die_name';
            $data['leftJoin']['die_master'] = 'die_master.id = die_challan_request.die_id';
            $data['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        }

        if(!empty($postData['processDetail'])){
            $data['select'] .= ',process_master.process_name';
            $data['leftJoin']['process_master'] = 'process_master.id = die_challan_request.process_id';
        }


        if(!empty($postData['challanDetail'])){
            $data['select'] .= ',die_outsource.ch_number,die_outsource.ch_date,party_master.party_name,party_master.gstin,party_master.party_address';
            $data['leftJoin']['die_outsource'] = 'die_outsource.id = die_challan_request.challan_id';
            $data['leftJoin']['party_master'] = 'party_master.id = die_outsource.party_id';
        }

        if(!empty($postData['wo_id'])){
            $data['where']['die_challan_request.wo_id'] = $postData['wo_id'];
        }
        if(!empty($postData['id'])){
            $data['where_in']['die_challan_request.id'] = $postData['id'];
        }
        if(!empty($postData['challan_id'])){
            $data['where']['die_challan_request.challan_id'] = $postData['challan_id'];
        }
        
        if(!empty($postData['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }
    }

    public function getOutsourceDTRows($data){
        $data['tableName'] = 'die_challan_request';
        $data['select'] = 'die_challan_request.*,die_work_order.trans_number,die_master.die_code,item_category.category_name AS die_name,item_master.item_name,item_master.item_code,process_master.process_name';

        $data['leftJoin']['die_work_order'] = 'die_challan_request.wo_id = die_work_order.id';
        $data['leftJoin']['die_master'] = 'die_challan_request.die_id = die_master.id';
        $data['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        $data['leftJoin']['item_master'] = 'item_master.id = die_master.item_id';
        $data['leftJoin']['process_master'] = 'process_master.id = die_challan_request.process_id';
        if($data['status'] == 0){
            $data['where']['die_challan_request.challan_id'] = 0;
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "die_work_order.trans_number";
            $data['searchCol'][] = "DATE_FORMAT(die_challan_request.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "die_master.die_code";
            $data['searchCol'][] = "item_category.category_name";
            $data['searchCol'][] = "item_master.item_name";
            $data['searchCol'][] = "process_master.process_name";
            $data['searchCol'][] = "die_challan_request.qty";
        }else{
            $data['select'] .= ',IFNULL((SELECT SUM(qty) FROM die_log WHERE die_log.is_delete = 0 AND die_log.process_by = 2 AND die_log.ref_id = die_challan_request.challan_id AND die_log.ref_trans_id = die_challan_request.id),0) AS receive_qty';
            $data['select'] .= ',die_outsource.ch_date,die_outsource.ch_number,party_master.party_name,die_outsource.party_id';
            $data['leftJoin']['die_outsource'] = 'die_outsource.id = die_challan_request.challan_id';
            $data['leftJoin']['party_master'] = 'party_master.id = die_outsource.party_id';
            $data['where']['die_challan_request.challan_id >'] = 0;
            if($data['status'] == 1){
                $data['having'][] = '(die_challan_request.qty - receive_qty) > 0';
            }else{
                $data['having'][] = '(die_challan_request.qty - receive_qty) = 0';
            }

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "DATE_FORMAT(die_outsource.ch_date,'%d-%m-%Y')";
            $data['searchCol'][] = "die_outsource.ch_number";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "die_work_order.trans_number";
            $data['searchCol'][] = "DATE_FORMAT(die_challan_request.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "die_master.die_code";
            $data['searchCol'][] = "item_category.category_name";
            $data['searchCol'][] = "item_master.item_name";
            $data['searchCol'][] = "process_master.process_name";
            $data['searchCol'][] = "die_challan_request.qty";
        }
        
        
        

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getNextChallanNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'die_outsource';
        $queryData['select'] = "MAX(ch_no) as ch_no ";	
		$queryData['where']['die_outsource.ch_date >='] = $this->startYearDate;
		$queryData['where']['die_outsource.ch_date <='] = $this->endYearDate;

		$ch_no = $this->specificRow($queryData)->ch_no;
		$ch_no = $ch_no + 1;
		return $ch_no;
    }

    public function saveChallan($data){
		try {
			$this->db->trans_begin();

            $ch_prefix =  'DC'.n2y(date("Y")).n2m(date("m"));
            $ch_no = $this->getNextChallanNo();
            $ch_number = $ch_prefix.str_pad($ch_no,2,0,STR_PAD_LEFT); 
            $challanData = [
                'id'=>'',
                'party_id'=>$data['party_id'],
                'ch_date'=>$data['ch_date'],
                'ch_no'=>$ch_no,
                'ch_number'=>$ch_number,
            ];
            $result = $this->store('die_outsource',$challanData, 'Challan');
            foreach($data['dp_id'] as $key=>$dp_id){
                $this->edit('die_challan_request',['id'=>$dp_id],['challan_id'=>$result['id']]);
            }
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Challan saved successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function deleteChallan($data){
        try{
            $this->db->trans_begin();
            $chData = $this->getChallanReqData(['challan_id'=>$data['id'],'receiveDetail'=>1]);
            if(!empty($chData)){
                foreach($chData AS $row){
                    if(!empty($row->receive_qty)){
                        return ['status'=>0,'message'=>'You can not delete this Challan'];
                    }
                    $this->store("die_challan_request",['id'=>$row->id,'challan_id'=>0]);
                }
                $result = $this->trash("die_outsource",['id'=>$data['id']]);
            }
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function changeStatus($data){
        try{
            $this->db->trans_begin();

            $result = $this->store('die_work_order',['id'=>$data['id'],'status'=>$data['status'],'issue_to'=>((!empty($data['emp_id']))?$data['emp_id']:0)]);
            /** History */
            
            $historyData = [
                'id'=>'',
                'wo_id'=>$data['id'],
                'die_id'=>$data['die_id'],
                'emp_id'=>((!empty($data['emp_id']))?$data['emp_id']:0),
                'item_id'=>((!empty($data['item_id']))?$data['item_id']:0),
                'old_item_id'=>((!empty($data['item_id']))?$data['item_id']:0),
                'remark'=>((!empty($data['remark']))?$data['remark']:""),
                'trans_type'=>$this->history_type[$data['status']],
            ];
            $this->store('die_history',$historyData);
            /** If Rework */
            if($data['status'] == 6){
                $rwoData = [
                    'id'=>'',
                    'trans_type'=>2,
                    'ref_id'=>$data['id'],
                    'trans_date'=>date("Y-m-d"),
                    'die_id'=>$data['die_id'],
                    'qty'=>1
                ];
                $this->save($rwoData);
            }
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getRegisterDTRows($data){
        $data['tableName'] = 'die_register';
        $data['select'] = 'die_register.*,item_category.category_name AS die_name,item_master.item_name,item_master.item_code,material_master.material_grade,main_cat.category_name,prc_master.prc_number';

       
        $data['leftJoin']['die_master'] = 'die_register.die_id = die_master.id';
        $data['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        $data['leftJoin']['item_master'] = 'item_master.id = die_register.item_id';
        $data['leftJoin']['item_category main_cat'] = 'main_cat.id = item_category.ref_id';
        $data['leftJoin']['material_master'] = 'material_master.id = die_master.grade_id';
        $data['leftJoin']['prc_master'] = 'prc_master.id = die_register.prc_id';
        
        $data['where']['die_register.status'] =$data['status'];
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(die_register.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "die_register.die_number";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
        $data['searchCol'][] = "main_cat.category_name";
        $data['searchCol'][] = "material_master.material_grade";
        $data['searchCol'][] = "prc_master.prc_number";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getFreshDieDTRows($data){
        $data['tableName'] = 'die_work_order';
        $data['select'] = 'die_work_order.*,die_master.die_code,item_category.category_name AS die_name,item_master.item_name,item_master.item_code,material_master.material_grade,main_cat.category_name';


        $data['leftJoin']['die_master'] = 'die_work_order.die_id = die_master.id';
        $data['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        $data['leftJoin']['item_master'] = 'item_master.id = die_work_order.item_id';
        $data['leftJoin']['item_category main_cat'] = 'main_cat.id = item_category.ref_id';
        $data['leftJoin']['material_master'] = 'material_master.id = die_master.grade_id';
        
        $data['having'][] = '(die_work_order.ok_qty - die_work_order.stock_qty) > 0';
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(die_work_order.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "die_work_order.trans_number";
        $data['searchCol'][] = "die_master.die_code";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
        $data['searchCol'][] = "main_cat.category_name";
        $data['searchCol'][] = "material_master.material_grade";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function saveConvertedItem($data){
        try{
            $this->db->trans_begin();

            $result = $this->store('die_work_order',['id'=>$data['id'],'item_id'=>$data['item_id']]);
            /** History */
            
            $historyData = [
                'id'=>'',
                'wo_id'=>$data['id'],
                'die_id'=>$data['die_id'],
                'item_id'=>((!empty($data['item_id']))?$data['item_id']:0),
                'old_item_id'=>((!empty($data['old_item_id']))?$data['old_item_id']:0),
                'remark'=>((!empty($data['remark']))?$data['remark']:""),
                'trans_type'=>5,
            ];
            $this->store('die_history',$historyData);
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function completeProduction($data){
        try{
            $this->db->trans_begin();

            $setData = Array();
            $setData['tableName'] = 'die_work_order';
            $setData['where']['id'] = $data['id'];
            $setData['set']['ok_qty'] = 'ok_qty, + '.$data['ok_qty'];
            $result = $this->setValue($setData);

            $this->updateWorkOrderStatus(['id'=>$data['id']]);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function updateWorkOrderStatus($data){
        try{
            $this->db->trans_begin();
            $woData = $this->dieProduction->getProductionData(['id' => $data['id'],'single_row'=>1]);
            $status = 1;
            if($woData->qty == $woData->ok_qty){
                $status = 2;
            }
            $result = $this->store("die_work_order",['id'=> $data['id'],'status'=>$status]);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveDieStock($data){
        try{
            $this->db->trans_begin();

            $setData = Array();
            $setData['tableName'] = 'die_work_order';
            $setData['where']['id'] = $data['id'];
            $setData['set']['stock_qty'] = 'stock_qty, + '.$data['stock_qty'];
            $result = $this->setValue($setData);

            $dieData = $this->dieMaster->getDieData(['id'=>$data['die_id'],'single_row'=>1]);
            for($i =1; $i<= $data['stock_qty'];$i++){
                $die_no = $this->getNextDieNo(['die_id'=>$data['die_id']]);
                $woData = [
                    'id'=>'',
                    'trans_date'=>date("Y-m-d"),
                    'wo_id'=>$data['id'],
                    'die_id'=>$data['die_id'],
                    'item_id'=>$dieData->item_id,
                    'die_no'=>$die_no,
                    'die_number'=>$dieData->die_code.'/'.$die_no,
                ];
                $result = $this->store("die_register",$woData);

                $historyData = [
                    'id'=>'',
                    'trans_type'=>1,
                    'wo_id'=>$data['id'],
                    'die_reg_id'=>$result['id'],
                    'die_id'=>$data['die_id'],
                    'item_id'=>$dieData->item_id,
                    'old_item_id'=>$dieData->item_id,
                ];
                $this->store('die_history',$historyData);
            }
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getDieRegisterData($param){
        $data['tableName'] = 'die_register';
        $data['select'] = 'die_register.*';

        if(!empty($param['dieDetail']) || !empty($param['tool_method'])){
            $data['select'] .= ',item_category.category_name AS die_name,material_master.material_grade,main_cat.category_name';
            $data['leftJoin']['die_master'] = 'die_register.die_id = die_master.id';
            $data['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
            $data['leftJoin']['item_category main_cat'] = 'main_cat.id = item_category.ref_id';
            $data['leftJoin']['material_master'] = 'material_master.id = die_master.grade_id';

            if(!empty($param['tool_method'])){
                $data['where']['die_master.tool_method'] =$param['tool_method'];
            }
        }
        if(!empty($param['itemDetail'])){
            $data['select'] .= ',item_master.item_name,item_master.item_code';
            $data['leftJoin']['item_master'] = 'item_master.id = die_register.item_id';
        }
        
        if(!empty($param['status'])){
            $data['where']['die_register.status'] =$param['status'];
        }

        if(!empty($param['die_id'])){
            $data['where']['die_register.die_id'] =$param['die_id'];
        }

        if(!empty($param['item_id'])){
            $data['where']['die_register.item_id'] =$param['item_id'];
        }

        if(!empty($param['customWhere'])){
            $data['customWhere'][] =$param['customWhere'];
        }

        if(!empty($param['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }
        
    }
}
?>