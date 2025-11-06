<?php
class OutsourceModel extends MasterModel{

    public function getNextChallanNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'outsource';
        $queryData['select'] = "MAX(ch_no) as ch_no ";	
		$queryData['where']['outsource.ch_date >='] = $this->startYearDate;
		$queryData['where']['outsource.ch_date <='] = $this->endYearDate;

		$ch_no = $this->specificRow($queryData)->ch_no;
		$ch_no = $ch_no + 1;
		return $ch_no;
    }

    public function getDTRows($data) {
        $style = 'style="margin:0px;padding:0px"';
        $data['tableName'] = "prc_challan_request";
		$data['select'] = "prc_challan_request.*,outsource.id as out_id,outsource.ch_number,outsource.ch_date,outsource.party_id,prc_master.prc_date,prc_master.prc_number,process_master.process_name,item_master.item_name,IFNULL(receiveLog.ok_qty,0) as ok_qty,IFNULL(receiveLog.rej_qty,0) as rej_qty,party_master.party_name,GROUP_CONCAT(otherProcess.process_name SEPARATOR '<hr ".$style.">') As process_names";
		$data['leftJoin']['outsource'] = "outsource.id = prc_challan_request.challan_id";
		$data['leftJoin']['prc_master'] = "prc_master.id = prc_challan_request.prc_id";
		$data['leftJoin']['process_master'] = "process_master.id = prc_challan_request.process_id";
		$data['leftJoin']['process_master otherProcess'] = "FIND_IN_SET(otherProcess.id,prc_challan_request.challan_process) > 0 ";
		$data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$data['leftJoin']['party_master'] = "party_master.id = outsource.party_id";
        $data['leftJoin']['(SELECT sum(qty) as ok_qty,SUM(rej_found) as rej_qty,ref_trans_id FROM prc_log WHERE is_delete = 0 AND process_by = 3 GROUP BY ref_trans_id) as receiveLog'] = "receiveLog.ref_trans_id = prc_challan_request.id";
		$data['where']['prc_challan_request.challan_id >'] = 0;
		$data['where']['prc_challan_request.auto_log_id'] = 0;
        if ($data['status'] == 0) :
            $data['having'][] = "prc_challan_request.qty > (ok_qty+rej_qty+without_process_qty)";
        endif;
        if ($data['status'] == 1) :
            $data['having'][] = "prc_challan_request.qty - (ok_qty+rej_qty+without_process_qty) <= 0";

        endif;
       
        $data['order_by']['prc_challan_request.id'] = 'DESC';
        $data['group_by'][] = 'prc_challan_request.id';
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(outsource.ch_date,'%d-%m-%Y')";
        $data['searchCol'][] = "outsource.ch_number";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "prc_challan_request.qty";
        $data['searchCol'][] = "(ok_qty+rej_qty)";
        $data['searchCol'][] = "prc_challan_request.without_process_qty";
        $data['searchCol'][] = "(prc_challan_request.qty - (ok_qty+rej_qty+without_process_qty))";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        // $this->printQuery();
        return $result;
    }

    public function save($data){
		try {
			$this->db->trans_begin();
            $ch_prefix = 'VC/'.getYearPrefix('SHORT_YEAR').'/';
            $ch_no = $this->outsource->getNextChallanNo();
            $challanData = [
                'id'=>'',
                'party_id'=>$data['party_id'],
                'ch_date'=>$data['ch_date'],
                'ch_no'=>$ch_no,
                'ch_number'=>$ch_prefix.$ch_no,
                'vehicle_no'=>$data['vehicle_no'],
                'remark'=>$data['remark']
            ];
            $result = $this->store('outsource',$challanData);
            foreach($data['id'] as $key=>$id){
                $chData = [
                    'id'=>$id,
                    'qty'=>$data['ch_qty'][$key],
                    'price'=>$data['price'][$key],
                    'challan_id'=>$result['id'],
                    'wt_nos'=>!empty($data['wt_nos'][$key])?$data['wt_nos'][$key]:'',
					'challan_process'=>implode(",",$data['process_ids'][$id]),
                ];
                $this->store('prc_challan_request',$chData, 'Challan Request');
            }
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function delete($id){
        try {
			$this->db->trans_begin();
            $chData = $this->sop->getChallanRequestData(['challan_id'=>$id,'challan_receive'=>1]);
            foreach($chData as $row){
                if(($row->ok_qty+$row->rej_qty) > 0){
                    return ['status'=>0,'message'=>'You can not delete this Challan'];
                }
                $this->store("prc_challan_request",['id'=>$row->id,'challan_id'=>0,'qty'=>$row->old_qty]);
            }
			$result = $this->trash('outsource', ['id'=>$id], 'Challan');
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function getOutSourceData($data){
		$data['tableName'] = 'outsource';
		$data['select'] = 'outsource.*,employee_master.emp_name,party_master.party_name,party_master.party_address,party_master.gstin';
		$data['leftJoin']['employee_master'] = 'employee_master.id = outsource.created_by';
		$data['leftJoin']['party_master'] = 'party_master.id = outsource.party_id';
		$data['where']['outsource.id'] = $data['id'];
		return $this->row($data);
	}

    public function checkDuplicateInChNo($data){
        $queryData['tableName'] = 'prc_log';
		if(!empty($data['in_challan_no'])):
            $queryData['where']['in_challan_no'] = $data['in_challan_no']; 
        endif;
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function saveLog($data){
		try {
			$this->db->trans_begin();
            $chData = $this->sop->getChallanRequestData(['id'=>$data['ref_trans_id'],'single_row'=>1]);
            $log_id = "";
          
            $prevCompleted = [];
            foreach($data['process_id'] As $key=>$process_id){
                $trans_type = 1;
                if($key == 0){
                    //First Process receive
                    $logData=[
                                'id'=>'',
                                'prc_id' => $data['prc_id'],
                                'trans_type' => $chData->trans_type,
                                'completed_process' => $chData->completed_process,
                                'process_from' => $chData->process_from,
                                'process_id' => $process_id,
                                'ref_id' => !empty($data['ref_id'])?$data['ref_id']:'',
                                'ref_trans_id' => !empty($data['ref_trans_id'])?$data['ref_trans_id']:'',
                                'trans_date' => $data['trans_date'],
                                'qty' => !empty($data['ok_qty'][$key])?$data['ok_qty'][$key]:0,
                                'rej_found' =>  !empty($data['rej_found'][$key])?$data['rej_found'][$key]:0,
                                'without_process_qty' =>  !empty($data['without_process_qty'][$key])?$data['without_process_qty'][$key]:0, // Used in outsource Receive Form
                                'in_challan_no' => !empty($data['in_challan_no'])?$data['in_challan_no']:0,
                                'process_by' => $data['process_by'],
                                'processor_id' =>!empty($data['processor_id'])?$data['processor_id']:0,
                                'wt_nos' => (!empty($data['wt_nos'])?$data['wt_nos']:""),
                                'challan_file' => (!empty($data['challan_file'])?$data['challan_file']:""),
                            ];
                    $result = $this->sop->savePRCLog($logData);
                    
                    if(empty($result['status'])):
                        $errorMessage['ok_qty'] = $result['message'];
				        return ['status' => 0, 'message' => $errorMessage];
                    endif;
                    
                    $log_id = $result['id'];
                    $trans_type = $chData->trans_type;
                }else{
                   
                    //Prev Process Movement
                    if($data['ok_qty'][($key-1)] > 0){
                        $comProcess = implode(",",$prevCompleted);
                        $move_type = 1;
                        if(in_array($process_id,explode(",",$chData->completed_process.','.$comProcess))){
                            $move_type = 2;
                        }
                        $trans_type = $move_type;
                        $movementData=[
                                    'id'=>'',
                                    'auto_log_id'=>$log_id,
                                    'move_from'=>$trans_type,
                                    'move_type'=>$move_type,
                                    'prc_id' => $data['prc_id'],
                                    'process_id' => $data['process_id'][($key-1)],
                                    'completed_process' => $chData->completed_process.','.$comProcess,
                                    'process_from' => ((!empty($data['process_id'][($key-2)]))?$data['process_id'][($key-2)]:$chData->process_from),
                                    'next_process_id' => $process_id,
                                    'trans_date' => $data['trans_date'],
                                    'qty' => !empty($data['ok_qty'][($key-1)])?$data['ok_qty'][($key-1)]:0,
                                ];
                        $this->sop->savePRCMovement($movementData);

                        //Auto Accept
                        $acceptData = [
                                        'id' => '',
                                        'auto_log_id'=>$log_id,
                                        'trans_type'=>$trans_type,
                                        'accepted_process_id' => $process_id,
                                        'completed_process' => $chData->completed_process.','.$comProcess,
                                        'process_from' => ((!empty($data['process_id'][($key-1)]))?$data['process_id'][($key-1)]:''),
                                        'prc_id' => $data['prc_id'],
                                        'accepted_qty' => (!empty($data['ok_qty'][($key-1)])?$data['ok_qty'][($key-1)]:0),
                                        'trans_date' => $data['trans_date'],
                                        'created_by' => $this->loginId,
                                        'created_at' => date("Y-m-d H:i:S")
                                    ];
                        $this->sop->saveAcceptedQty($acceptData);

                        //Auto Challan
                        
                        $challanData=[
                                        'id' => '',
                                        'auto_log_id'=>$log_id,
                                        'trans_type'=>$trans_type,
                                        'request_ref_id'=>$data['ref_trans_id'],
                                        'prc_id' => $data['prc_id'],
                                        'process_id' => $process_id,
                                        'completed_process' => $chData->completed_process.','.$comProcess,
                                        'process_from' => ((!empty($data['process_id'][($key-1)]))?$data['process_id'][($key-1)]:''),
                                        'trans_date' =>  $data['trans_date'],
                                        'qty' => (!empty($data['ok_qty'][($key-1)])?$data['ok_qty'][($key-1)]:0),
                                        'old_qty' => (!empty($data['ok_qty'][($key-1)])?$data['ok_qty'][($key-1)]:0),
                                        'price'=>$chData->price,
                                        'challan_process'=>$chData->challan_process,
                                        'challan_id'=>$chData->challan_id,
                                    ];
                        $chResult = $this->store('prc_challan_request',$challanData);

                        //Auto Receive
                        $logData=[
                                    'id'=>'',
                                    'prc_id' => $data['prc_id'],
                                    'process_id' => $process_id,
                                    'auto_log_id'=>$log_id,
                                    'trans_type'=>$trans_type,
                                    'completed_process' => $chData->completed_process.','.$comProcess,
                                    'process_from' => ((!empty($data['process_id'][($key-1)]))?$data['process_id'][($key-1)]:''),
                                    'ref_id' => !empty($data['ref_id'])?$data['ref_id']:'',
                                    'ref_trans_id' => $chResult['id'],
                                    'trans_date' => $data['trans_date'],
                                    'qty' => !empty($data['ok_qty'][$key])?$data['ok_qty'][$key]:0,
                                    'rej_found' =>  !empty($data['rej_found'][$key])?$data['rej_found'][$key]:0,
                                    'without_process_qty' =>  !empty($data['without_process_qty'][$key])?$data['without_process_qty'][$key]:0, // Used in outsource Receive Form
                                    'in_challan_no' => !empty($data['in_challan_no'])?$data['in_challan_no']:0,
                                    'process_by' => $data['process_by'],
                                    'processor_id' =>!empty($data['processor_id'])?$data['processor_id']:0
                                ];
                        $result = $this->sop->savePRCLog($logData);
                    }
                }
                $prevCompleted[] = $process_id;
            }
            if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}

    }

    public function deleteLog($param){
        try {
			$this->db->trans_begin();
            
            $logData = $this->sop->getProcessLogList(['customWhere'=>' (prc_log.id = '.$param['id'].' OR prc_log.auto_log_id = '.$param['id'].')','rejection_review_data'=>1,'outsource_without_process'=>1]);
          
			if(!empty($logData)){
                foreach($logData AS $row){
                    if ($row->review_qty > 0){
                        return ['status'=>0,'message'=>'You can not delete this Log. You have to delete rejection review first'];
                    }
                    if($row->without_process_qty > 0){
                        $processData = $this->sop->getPendingLogData(['prc_id'=>$row->prc_id,'process_id'=>$row->process_id,'completed_process'=>$row->completed_process]);
                        $in_qty = $processData->in_qty;
                        $ok_qty = !empty($processData->ok_qty)?$processData->ok_qty:0;
                        $rej_found = !empty($processData->rej_found)?$processData->rej_found:0;
                        $rw_qty = !empty($processData->rw_qty)?$processData->rw_qty:0;
                        $rej_qty = !empty($processData->rej_qty)?$processData->rej_qty:0;
                        $pendingReview = $rej_found - $processData->review_qty;
                    
                        $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$processData->ch_qty);

                        if($row->without_process_qty > $pending_production){
                            return ['status'=>0,'message'=>'You can not delete this Log. Production of returned qty without process has been done. '];
                        }
                        $setData = array();
                        $setData['tableName'] = 'prc_challan_request';
                        $setData['where']['id'] = $row->ref_trans_id;
                        $setData['set']['without_process_qty'] = 'without_process_qty, - ' . $row->without_process_qty;
                        $this->setValue($setData);
                        $this->trash('without_process_log',['log_id'=>$row->id]);
                    }

                    if($row->id == $param['last_log_id']){
                        $movementData =  $this->sop->getPendingMovementData(['prc_id'=>$row->prc_id,'process_id'=>$row->process_id,'completed_process'=>$row->completed_process]);
				        $pending_movement = $movementData->ok_qty - $movementData->movement_qty;

                        if(($row->qty > $pending_movement) ){
                            return ['status'=>0,'message'=>'You can not delete this Log. Qty is sent to next process'];
                        }
                    }

                }
                $this->trash("prc_movement",['auto_log_id'=>$param['id']]);
                $this->trash("prc_accept_log",['auto_log_id'=>$param['id']]);
                $this->trash("prc_challan_request",['auto_log_id'=>$param['id']]);
                $this->trash("prc_log",['auto_log_id'=>$param['id']]);
				$result = $this->trash('prc_log',['id'=>$param['id']]);
				
		
			}else{
				$result = ['status'=>0,'message'=>'Log already deleted'];
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
}
?>