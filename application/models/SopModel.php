<?php
class SopModel extends MasterModel{

    public function getDTRows($data){
        $data['tableName'] = "prc_master";
		
		$data['select'] = "prc_master.id, prc_master.prc_number,prc_master.item_id, DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y') as prc_date,DATE_FORMAT(prc_master.target_date,'%d-%m-%Y') as 	target_date, prc_master.status, prc_master.prc_qty,prc_master.remark, prc_master.tool_status";
		$data['select'] .= ", IFNULL(im.item_name,'') as item_name,IFNULL(im.item_code,'') as item_code";
        
        $data['leftJoin']['item_master im'] = "im.id = prc_master.item_id";
		
		$data['where']['prc_master.prc_type'] = 1;
		if(!empty($data['status'])){ $data['where_in']['prc_master.status'] = $data['status']; }
       
		$data['order_by']['prc_master.prc_date'] = 'DESC';
		$data['order_by']['prc_master.id'] = 'DESC';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(im.item_code,' ',im.item_name)";
        $data['searchCol'][] = "prc_master.prc_qty";
        $data['searchCol'][] = "DATE_FORMAT(prc_master.target_date,'%d-%m-%Y')";
        $data['searchCol'][] = "prc_master.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

    public function getNextPRCNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'prc_master';
		$queryData['select'] = "MAX(prc_no ) as prc_no";
		$queryData['where']['prc_master.prc_date >='] = $this->startYearDate;
		$queryData['where']['prc_master.prc_date <='] = $this->endYearDate;

		$prc_no = $this->specificRow($queryData)->prc_no;
		$prc_no = (!empty($prc_no))?($prc_no + 1):1;
		return $prc_no;
    }

    public function savePRC($data){ 
		try {
			$this->db->trans_begin();

			if(empty($data['id'])){
                $itemData = $this->item->getItem(['id'=>$data['item_id'],'parentCategory'=>1]);
                $data['prc_no'] = $this->getNextPRCNo();
                $data['prc_number'] = 'HF'.$data['prc_no'].'/'.date("dmy",strtotime($data['prc_date'])).'/'.$itemData->parent_category;
            }
			
            $result = $this->store('prc_master', $data, 'PRC');
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function deletePRC($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash('prc_master',['id'=>$id],'PRC');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getPRCDetail($param = []){
        $data['tableName'] = 'prc_master';
		$data['select'] = 'prc_master.*';
        if(!empty($param['itemDetail'])){
            $data['select'] .= ',item_master.item_code,item_master.item_name,item_master.uom,item_master.drg_no,item_master.rev_no';
            $data['leftJoin']['item_master'] = 'item_master.id = prc_master.item_id';
        }
		if(!empty($param['createdDetail'])){
            $data['select'] .= ',employee_master.emp_name';
            $data['leftJoin']['employee_master'] = 'employee_master.id = prc_master.created_by';
        }
		if(!empty($param['id'])){ $data['where']['prc_master.id'] = $param['id']; }
		
		return $this->row($data);
    }

    public function startPRC($data){
		try {
			$this->db->trans_begin();
            $prcData = $this->getPRCDetail(['id'=>$data['id']]);
			
			if($prcData->status == 1){
                /*** Movement Entry */
                $movementQty = [
                    'id'=>'',
                    'prc_id' => $data['id'],
                    'process_id' => 0,
                    'next_process_id' =>  $data['first_process'],
                    'trans_date' => date("Y-m-d"),
                    'qty' => $prcData->prc_qty,
                    'completed_process' =>0,
                ];
                $this->sop->savePRCMovement($movementQty);
				/** Auto Accept Entry */
                $accept = [
                    'id' => '',
                    'accepted_process_id' => $data['first_process'],
                    'prc_id' => $data['id'],
                    'accepted_qty' => $prcData->prc_qty,
                    'short_qty' => '',
                    'trans_date' => date("Y-m-d"),
                    'completed_process' =>0,
                    'created_by' => $this->loginId,
                    'created_at' => date("Y-m-d H:i:s"),
                ];
                $this->sop->saveAcceptedQty($accept);
				/** BOM Entry */
				$heatData = $this->store->getPrcBatchDetail(['id'=>$prcData->batch_id,'single_row'=>1]);
				if(!empty($heatData)){
					$bomData = [
						'id'=>'',
						'item_id'=>$heatData->item_id,
						'batch_id'=>$prcData->batch_id,
						'process_id'=>$data['first_process'],
						'prc_id'=>$data['id'],
					];
					$this->store("prc_heat",$bomData);
				}
                $result = $this->edit("prc_master",['id'=>$data['id']],['first_process'=>$data['first_process'],'status'=>2]);	
				
				
			}
			if(!empty($data['process_ids'])){
				$result = $this->edit("prc_master",['id'=>$data['id']],['process_ids'=>$data['process_ids']]);	
			}else{
				$result = $this->edit("prc_master",['id'=>$data['id']],['process_ids'=>null]);	
			}
			

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Saved Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	/**** PRC HEAT ****/
	
	public function getPrcHeat($param){
		$data['tableName'] = 'prc_heat';

		if(!empty($param['select'])){
			$data['select'] = $param['select'];
		}else{
			$data['select'] = 'prc_heat.*';
		}
		$data['select'] .= ',prc_batch.trans_number,(prc_batch.issue_qty - (prc_batch.used_qty + prc_batch.return_qty + prc_batch.scrap_qty)) AS stock_qty';
		$data['leftJoin']['prc_batch'] = 'prc_batch.id = prc_heat.batch_id';
		$data['where']['prc_heat.prc_id'] = $param['prc_id'];
		
		if(!empty($param['batch_no'])):
			$data['where']['prc_heat.batch_no'] = $param['batch_no'];
		endif;
		
		if(!empty($param['heat_no'])):
			$data['where']['prc_heat.heat_no'] = $param['heat_no'];
		endif;

		if(!empty($param['id'])):
			$data['where']['prc_heat.id'] = $param['id'];
		endif;
		
		if(!empty($param['item_id'])):
			$data['where']['prc_heat.item_id'] = $param['item_id'];
		endif;

		if(!empty($param['itemDetail'])):
			$data['select'] .= ',item_master.item_code, item_master.item_name,item_master.uom';
			$data['leftJoin']['item_master'] = 'item_master.id = prc_heat.item_id';
		endif;
		
		

		if(!empty($param['result_type']) AND $param['result_type'] == "row"):
			return $this->row($data);
		else:
			return $this->rows($data);
		endif;
		return $this->row($data);
	}

	public function updatePrcBom($param){
		
		$setData = array();
		$setData['tableName'] = 'prc_heat';
		$setData['where']['prc_id'] = $param['prc_id'];
		
		if(!empty($param['id'])):
			$setData['where']['id'] = $param['id'];
		endif;

		if(!empty($param['item_id'])):
			$setData['where']['item_id'] = $param['item_id'];
		endif;
		
		
		if(!empty($param['opr']) AND $param['opr'] == "Minus"):
			$setData['set'][$param['update_field']] = $param['update_field'].', - ' . $param['qty'];
		else:
			$setData['set'][$param['update_field']] = $param['update_field'].', + ' . $param['qty'];
		endif;
		
		$this->setValue($setData);
		
		return true;
	}

	public function updatePrcBatch($param){
		
		$setData = array();
		$setData['tableName'] = 'prc_batch';
		
		$setData['where']['id'] = $param['id'];

		if(!empty($param['item_id'])):
			$setData['where']['item_id'] = $param['item_id'];
		endif;
		
		
		if(!empty($param['opr']) AND $param['opr'] == "Minus"):
			$setData['set'][$param['update_field']] = $param['update_field'].', - ' . $param['qty'];
		else:
			$setData['set'][$param['update_field']] = $param['update_field'].', + ' . $param['qty'];
		endif;
		
		$this->setValue($setData);
		
		return true;
	}
	/** PRC Movement */

	public function getNextTagno(){
		$queryData = array(); 
		$queryData['tableName'] = 'prc_movement';
		$queryData['select'] = "MAX(tag_no ) as tag_no";
		
		$tag_no = $this->specificRow($queryData)->tag_no;
		$tag_no = (!empty($tag_no))?($tag_no + 1):1;
		return $tag_no;
	}

	public function savePRCMovement($param){
		try {
			$this->db->trans_begin();
            $prcData = $this->getPRCDetail(['id'=>$param['prc_id']]);
			if(!in_array($prcData->status,[1,2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
			$param['tag_no'] = $this->getNextTagno();
			$result = $this->store('prc_movement', $param, 'PRC Log');
			if($param['next_process_id'] == 0){
				$stockData = [
					'id' => "",
					'trans_type' => 'PRD',
					'trans_date' => $param['trans_date'],
					'ref_no' => $prcData->prc_number,
					'main_ref_id' => $param['prc_id'],
					'child_ref_id' => $result['id'],
					'location_id' => $this->RTD_STORE->id,
					'batch_no' =>$prcData->prc_number,
					'completed_process' =>$param['completed_process'],
					'item_id' => $prcData->item_id,
					'p_or_m' => 1,
					'qty' => $param['qty'],
				];
				$this->store('stock_trans',$stockData);
				$this->changePrcStatus(['prc_id'=>$param['prc_id']]);
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

	/** PRC Accept */
    public function saveAcceptedQty($data){
		try {
			$this->db->trans_begin();
			$prcData = $this->getPRCDetail(['id'=>$data['prc_id']]);
			if(!in_array($prcData->status,[1,2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
			$data['trans_date'] = date("Y-m-d");
			$result = $this->store('prc_accept_log', $data, 'Acceped');
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function getPrcAcceptData($param){
		$queryData = array();          
		$queryData['tableName'] = "prc_accept_log";
		$queryData['select'] = "prc_accept_log.*";

		if(!empty($param['completedProcessDetail'])){
			$queryData['select'] .= ',(SELECT GROUP_CONCAT(pm.process_name ORDER BY pm.process_name) 
							FROM process_master pm 
							WHERE FIND_IN_SET(pm.id, prc_accept_log.completed_process) > 0) AS completed_process_name';
		}
		if(!empty($param['prcDetail']) || !empty($param['itemDetail'])){
			$queryData['select'] .= ",prc_master.prc_number,prc_master.prc_qty,prc_master.item_id";
			$queryData['leftJoin']['prc_master'] = 'prc_master.id = prc_accept_log.prc_id';

			if(!empty($param['itemDetail'])){
				$queryData['select'] .= ",item_master.item_code,item_master.item_name";
				$queryData['leftJoin']['item_master'] = 'item_master.id = prc_master.item_id';
			}
		}
		
		if(!empty($param['processDetail'])){
			$queryData['select'] .= ",process_master.process_name,process_master.process_type";
			$queryData['leftJoin']['process_master'] = 'process_master.id = prc_accept_log.accepted_process_id';
		}
		if(!empty($param['fromProcessDetail'])){
			$queryData['select'] .= ",fromProcess.process_name AS from_process_name";
			$queryData['leftJoin']['process_master fromProcess'] = 'fromProcess.id = prc_accept_log.process_from';
		}
		if(!empty($param['fromProcessDetail'])){
			$queryData['select'] .= ",fromProcess.process_name AS from_process_name";
			$queryData['leftJoin']['process_master fromProcess'] = 'fromProcess.id = prc_accept_log.process_from';
		}
		if(!empty($param['id'])){ $queryData['where']['prc_accept_log.id'] = $param['id']; }
		if(!empty($param['prc_id'])){ $queryData['where']['prc_accept_log.prc_id'] = $param['prc_id']; }
		if(!empty($param['accepted_process_id'])){ $queryData['where']['prc_accept_log.accepted_process_id'] = $param['accepted_process_id']; }	
		if(!empty($param['completed_process'])){ $queryData['where']['prc_accept_log.completed_process'] = $param['completed_process']; }	
		if(!empty($param['trans_date'])){ $queryData['where']['prc_accept_log.trans_date'] = $param['trans_date']; }	
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		if(isset($param['process_from'])){ $queryData['where']['prc_accept_log.process_from'] = $param['process_from']; }	
		if(isset($param['trans_type'])){ $queryData['where']['prc_accept_log.trans_type'] = $param['trans_type']; }	

		if(!empty($param['group_by'])){$queryData['group_by'][] = $param['group_by'];}
		if(!empty($param['single_row'])){ $result = $this->row($queryData); }
		else{ $result = $this->rows($queryData); }
		
        return $result;  
	}
	/** PRC Process Detail */

	public function getPRCProcessList($param = []){
		$queryData = array();
		$queryData['tableName'] = "product_process";
		$queryData['select'] = "product_process.item_id,product_process.process_id,product_process.cycle_time,product_process.output_qty,prc_master.id as prc_id,process_master.process_name,prc_master.process_ids,prc_master.item_id,item_master.item_code,item_master.item_name";
		
		$queryData['leftJoin']['prc_master'] = "prc_master.item_id = product_process.item_id AND prc_master.is_delete = 0";
		$queryData['leftJoin']['item_master'] = "prc_master.item_id = item_master.id";
		$queryData['leftJoin']['process_master'] = "process_master.id = product_process.process_id";

		/** SUM of Qty Inward */
		if(!empty($param['inwardDetail'])){
			$queryData['select'] .= ',(SELECT SUM(prc_movement.qty) FROM prc_movement WHERE prc_movement.is_delete = 0 AND prc_movement.prc_id = prc_master.id AND  prc_movement.next_process_id = product_process.process_id) AS inward_qty';
		}

		/** SUM of Qty Accept */
		if(!empty($param['acceptDetail'])){
			$queryData['select'] .= ',(SELECT SUM(prc_accept_log.accepted_qty) FROM prc_accept_log WHERE prc_accept_log.is_delete = 0 AND prc_accept_log.prc_id = prc_master.id AND  prc_accept_log.accepted_process_id = product_process.process_id) AS accepted_qty';
		}

		/** SUM of Qty Accept */
		if(!empty($param['challanDetail'])){
			$queryData['select'] .= ',(SELECT SUM(prc_challan_request.qty) FROM prc_challan_request WHERE prc_challan_request.is_delete = 0 AND prc_challan_request.prc_id = prc_master.id AND  prc_challan_request.process_id = product_process.process_id) AS ch_qty';
		}


		/** SUM of Qty Movement */
		if(!empty($param['movementDetail'])){
			$queryData['select'] .= ',(SELECT SUM(prc_movement.qty) FROM prc_movement WHERE prc_movement.is_delete = 0 AND prc_movement.prc_id = prc_master.id AND  prc_movement.process_id = product_process.process_id) AS move_qty';
		}

		/** Log Detail */
		if(!empty($param['logDetail'])){
			$queryData['select'] .= ',IFNULL(prc_log.ok_qty,0) AS ok_qty,IFNULL(prc_log.rej_found,0) AS rej_found,IFNULL(prc_log.rej_qty,0) AS rej_qty,IFNULL(prc_log.rw_qty,0) AS rw_qty';
			$queryData['leftJoin']['(SELECT SUM(qty) AS ok_qty,SUM(rej_found) AS rej_found,SUM(rej_qty) AS rej_qty,SUM(rw_qty) AS rw_qty,prc_id,process_id
										FROM prc_log WHERE prc_log.is_delete = 0 GROUP BY prc_id,process_id ) prc_log'] = 'prc_log.prc_id = prc_master.id AND product_process.process_id = prc_log.process_id';
		}
		/** Review Qty */
		if(!empty($param['rejReviewDetail'])){
			$queryData['select'] .= ',(SELECT SUM(rejection_log.qty) FROM rejection_log WHERE rejection_log.is_delete = 0 AND rejection_log.prc_id = prc_master.id AND  rejection_log.process_id = product_process.process_id) AS review_qty';
		}

		

		if(!empty($param['prc_id'])){ $queryData['where']['prc_master.id'] = $param['prc_id']; }
		if(!empty($param['process_id'])){ $queryData['where_in']['product_process.process_id'] = $param['process_id']; }
		if(!empty($param['item_id'])){ $queryData['where']['product_process.item_id'] = $param['item_id']; }
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		
		if(!empty($param['order_by_date'])){ $queryData['order_by']['prc_master.prc_date'] = 'DESC'; }
		
		if(!empty($param['process_id'])){ $queryData['order_by']['FIELD(product_process.process_id,'.$param['process_id'].')'] = '';}
		else{ $queryData['order_by']['product_process.sequence'] = 'ASC'; }
		
		if(!empty($param['single_row'])){ $result = $this->row($queryData); }
		else{ $result = $this->rows($queryData); }
		
		$queryData['group_by'][]="prc_master.id,product_process.process_id";
        return $result;  
	}

	/** This Function is to find production qty of single process
	 * Required Parameter : prc_id,process_id,completed_process
	*/
	public function getPendingLogData($param){

		if(isset($param['prc_id']) && isset($param['process_id']) && isset($param['completed_process'])){
			$queryData['tableName'] = 'prc_accept_log';
			$queryData['select'] = 'SUM(prc_accept_log.accepted_qty) AS in_qty';

			$queryData['select'] .= ',IFNULL((SELECT SUM(prc_challan_request.qty) FROM prc_challan_request WHERE prc_challan_request.is_delete = 0 AND prc_challan_request.prc_id = prc_accept_log.prc_id AND  prc_challan_request.process_id = prc_accept_log.accepted_process_id AND prc_challan_request.completed_process = prc_accept_log.completed_process '.(!empty($data['trans_type'])?' AND prc_challan_request.trans_type = '.$param['trans_type']:'').'),0) AS ch_qty';


			$queryData['where']['prc_accept_log.accepted_process_id'] = $param['process_id'];
			$queryData['where']['prc_accept_log.prc_id'] = $param['prc_id'];
			$queryData['where']['prc_accept_log.completed_process'] = $param['completed_process'];
			if(!empty($param['trans_type'])){
				$queryData['where']['prc_accept_log.trans_type'] = $param['trans_type'];
			}
			$result = $this->row($queryData);
			$logQuery['tableName'] = 'prc_log';
			$logQuery['select'] = ' SUM(qty) AS ok_qty,SUM(rej_found) AS rej_found,SUM(rej_qty) AS rej_qty,SUM(rw_qty) AS rw_qty,SUM(IFNULL((SELECT SUM(rejection_log.qty) FROM rejection_log WHERE rejection_log.log_id = prc_log.id),0)) AS review_qty';
			$logQuery['where']['prc_log.process_id'] = $param['process_id'];
			$logQuery['where']['prc_log.prc_id'] = $param['prc_id'];
			$logQuery['where']['prc_log.process_by != '] = 3;
			$logQuery['where']['prc_log.completed_process'] = $param['completed_process'];
			if(!empty($param['trans_type'])){
				$logQuery['where']['prc_log.trans_type'] = $param['trans_type'];
			}
			$logResult = $this->row($logQuery);
			if(!empty($logResult)){
				foreach($logResult AS $key=>$value){
					$result->{$key} = $value ;
				}
			}
			return $result;
		}else{
			return ['status'=>0,'message'=>'Some Parameter missing'];
		}
	}

	/** This Function is to find Movement qty of single process
	 * Required Parameter : prc_id,process_id,completed_process
	*/
	public function getPendingMovementData($param){

		if(isset($param['prc_id']) && isset($param['process_id']) && isset($param['completed_process'])){
			$queryData['tableName'] = 'prc_log';
			$queryData['select'] = 'SUM(prc_log.qty) AS ok_qty';

			$pids = implode(",",[$param['completed_process'],$param['process_id']]);
			$queryData['where']['prc_log.process_id'] = $param['process_id'];
			$queryData['where']['prc_log.prc_id'] = $param['prc_id'];
			$queryData['where']['prc_log.completed_process'] = $param['completed_process'];
			if(!empty($param['trans_type'])){
				$queryData['where']['prc_log.trans_type'] = $param['trans_type'];
			}
			$result =  $this->row($queryData);
			$moveQuery['tableName'] = 'prc_movement';
			$moveQuery['select'] = 'SUM(prc_movement.qty) AS movement_qty';
			$moveQuery['where']['prc_movement.process_id'] = $param['process_id'];
			$moveQuery['where']['prc_movement.prc_id'] = $param['prc_id'];
			$moveQuery['where']['prc_movement.completed_process'] = $pids;
			if(!empty($param['trans_type'])){
				$moveQuery['where']['prc_movement.move_from'] = $param['trans_type'];
			}
			$moveResult = $this->row($moveQuery);
			$result->movement_qty = $moveResult->movement_qty ;
			return $result;
		}else{
			return ['status'=>0,'message'=>'Some Parameter missing'];
		}
	}
	/** This Function is to find Movement qty of single process
	 * Required Parameter : prc_id,process_id,completed_process
	*/
	public function getPendingAcceptData($param){

		if(isset($param['prc_id']) && isset($param['process_id']) && isset($param['completed_process'])){
			$queryData['tableName'] = 'prc_movement';
			$queryData['select'] = 'SUM(prc_movement.qty) AS inward_qty';

			$queryData['where']['prc_movement.next_process_id'] = $param['process_id'];
			$queryData['where']['prc_movement.prc_id'] = $param['prc_id'];
			$queryData['where']['prc_movement.completed_process'] = $param['completed_process'];
			if(!empty($param['trans_type'])){
				$queryData['where']['prc_movement.move_type'] = $param['trans_type'];
			}
			$result =  $this->row($queryData);
			$acceptQuery['tableName'] = 'prc_accept_log';
			$acceptQuery['select'] = 'SUM(prc_accept_log.accepted_qty) AS accept_qty';
			$acceptQuery['where']['prc_accept_log.accepted_process_id'] = $param['process_id'];
			$acceptQuery['where']['prc_accept_log.prc_id'] = $param['prc_id'];
			$acceptQuery['where']['prc_accept_log.completed_process'] = $param['completed_process'];
			if(!empty($param['trans_type'])){
				$acceptQuery['where']['prc_accept_log.trans_type'] = $param['trans_type'];
			}
			$acceptResult = $this->row($acceptQuery);
			$result->accept_qty = $acceptResult->accept_qty ;
			return $result;
		}else{
			return ['status'=>0,'message'=>'Some Parameter missing'];
		}
	}

	public function getLastLogDate($data){
		$queryData = array();          
		$queryData['tableName'] = "prc_log";
		$queryData['select'] = 'MAX(created_at) AS last_log_date';
		$queryData['where']['prc_id'] = $data['prc_id'];
		$queryData['where']['process_id'] = $data['process_id'];
		$result = $this->row($queryData);
		return $result;
	}

	public function getLastAcceptDate($data){
		$queryData = array();          
		$queryData['tableName'] = "prc_accept_log";
		$queryData['select'] = 'MAX(created_at) AS last_accept_date';
		$queryData['where']['prc_id'] = $data['prc_id'];
		$queryData['where']['accepted_process_id'] = $data['accepted_process_id'];
		$result = $this->row($queryData);
		return $result;
	}

	public function checkIssueMaterialForPrc($data){
		try {
			$this->db->trans_begin();
			$prcData = $this->getPRCDetail(['id'=>$data['prc_id']]);
			$prevLogData = $this->sop->getProcessLogList(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'rejection_review_data'=>1,'log_used_material'=>1,'single_row'=>1]);
			$processData = $this->process->getProcess(['id'=>$data['process_id']]);
			$kitData = $this->item->getProductKitData(['item_id'=>$prcData->item_id,'process_id'=>$data['process_id']]);	
			if(!empty($kitData)){
				$reqQty = ($data['production_qty'] * $data['wt_nos']) +  (!empty($prevLogData->used_material) ? $prevLogData->used_material:0);
				$qty=0;
				foreach($kitData AS $row){
					$issueData = $this->sop->getPrcHeat(['prc_id'=>$data['prc_id'],'item_id'=>$row->ref_item_id,'select'=>'SUM(issue_qty) AS issue_qty,SUM(return_qty) AS return_qty,SUM(scrap_qty) As scrap_qty','result_type'=>'row']);
					$issue_qty = ((!empty($issueData->issue_qty))?$issueData->issue_qty:0);
					$return_qty = ((!empty($issueData->return_qty))?$issueData->return_qty:0);
					$scrap_qty = ((!empty($issueData->scrap_qty))?$issueData->scrap_qty:0);
					$qty += $issue_qty - ($return_qty + $scrap_qty);
				}
				if(round($reqQty,0) > round($qty,0)){
					return ['status'=>0,'message'=>'Material Not Available'.round($reqQty,0).' > '.round($qty,0)];
				}
			}
				
			
			
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function savePRCLog($param){
		try {
			$this->db->trans_begin();
			$firData = (!empty($param['firData']))?$param['firData']:[];
			$without_process_qty = (!empty($param['without_process_qty']))?$param['without_process_qty']:0;
			unset($param['without_process_qty'],$param['firData']);
			$prcData = $this->getPRCDetail(['id'=>$param['prc_id']]);
			if($param['process_id'] > 0 && $prcData->first_process == $param['process_id'] && $param['trans_type'] == 1){
				$production_qty = ($param['qty']+$param['rej_found']);
				$wt_nos = !empty($param['wt_nos'])?$param['wt_nos']:0;
				$batchData = $this->store->getPrcBatchDetail(['id'=>$prcData->batch_id,'single_row'=>1]);
				if($wt_nos == 0){
					$kitData = $this->item->getProductKitData(['item_id'=>$prcData->item_id,'ref_item_id' => $batchData->item_id,'single_row'=>1]);
					$wt_nos = $kitData->qty;
				}

				$required_qty = $production_qty * $wt_nos;
				$stockQty = $batchData->issue_qty - ($batchData->used_qty + $batchData->return_qty + $batchData->scrap_qty);
				if(round($required_qty,3) > round($stockQty,3)){
					return ['status'=>0,'message'=>'Material is not available'.$required_qty.'>'.$stockQty];
				}else{
					/** UPDATE BOM & MAIN BATCH TABLE */	
					$this->updatePrcBatch(['id'=>$batchData->id,'qty'=> $required_qty , 'update_field'=>'used_qty']);
					$this->updatePrcBom(['item_id'=>$batchData->item_id,'prc_id'=>$param['prc_id'],'qty'=> $required_qty , 'update_field'=>'used_qty']);
				}
				$param['wt_nos'] = $wt_nos;
				
			}
            $result = $this->store('prc_log', $param, 'PRC Log');
			if($param['process_id'] == 2){
				$firData['id'] = "";
				$firData['ref_id'] = $result['id'];
				$this->store('production_inspection',$firData,'Final Inspection');
			}
			
			// IF Vendor Return Log Without process
			if($param['process_by'] == 3 && !empty($without_process_qty)){
				//Set Without Process Qty in prc challan request table
				$setData = array();
                $setData['tableName'] = 'prc_challan_request';
                $setData['where']['id'] = $param['ref_trans_id'];
                $setData['set']['without_process_qty'] = 'without_process_qty, + ' . $without_process_qty;
                $this->setValue($setData);

				//Without process Log
				$logData = [
					'id'=>'',
					'prc_id'=>$param['prc_id'],
					'challan_id'=>$param['ref_id'],
					'challn_req_id'=>$param['ref_trans_id'],
					'log_id'=>$result['id'],
					'in_challan_no'=>$param['in_challan_no'],
					'qty'=>$without_process_qty,
					'created_by'=>$this->loginId,
					'created_at'=>date("Y-m-d H:i:s"),
				];
				$this->store('without_process_log',$logData);
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

	public function getProcessLogList($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "prc_log";
		
		$queryData['select'] = "prc_log.*";

		if(!empty($param['completedProcessDetail'])){
			$queryData['select'] .= ',(SELECT GROUP_CONCAT(pm.process_name ORDER BY pm.process_name) 
							FROM process_master pm 
							WHERE FIND_IN_SET(pm.id, prc_log.completed_process) > 0) AS completed_process_name';
		}
		if(!empty($param['processorDetail'])){
			$queryData['select'] .=', IF(prc_log.process_by = 1, machine.item_code, IF(prc_log.process_by = 2,department_master.name, IF(prc_log.process_by = 3,party_master.party_name,""))) as processor_name,machine.item_name as machine_name';

			$queryData['leftJoin']['item_master machine'] = "machine.id = prc_log.processor_id";
			$queryData['leftJoin']['department_master'] = "department_master.id = prc_log.processor_id";
			$queryData['leftJoin']['party_master'] = "party_master.id = prc_log.processor_id";
		}
		if(!empty($param['operatorDetail'])){
			$queryData['select'] .=',employee_master.emp_name';
			$queryData['leftJoin']['employee_master'] = "employee_master.id = prc_log.operator_id";
		}	
		if(!empty($param['shiftDetail'])){
			$queryData['select'] .=',shift_master.shift_name';
			$queryData['leftJoin']['shift_master'] = "shift_master.id = prc_log.shift_id";
		}
        if(!empty($param['prcDetail']) || !empty($param['itemDetail']) || !empty($param['productProcessDetail'])){
			$queryData['select'] .=',prc_master.item_id,prc_master.prc_number,prc_master.process_ids';
			$queryData['leftJoin']['prc_master'] = "prc_master.id = prc_log.prc_id";
			if(!empty($param['itemDetail'])){
				$queryData['select'] .=',item_master.item_name';
				$queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
			}
			if(!empty($param['productProcessDetail'])){
				$queryData['select'] .=',product_process.cycle_time';
				$queryData['leftJoin']['product_process'] = "product_process.process_id = prc_log.process_id AND product_process.item_id = prc_master.item_id AND product_process.is_delete = 0";
			}
		}	
		if(!empty($param['processDetail'])){
			$queryData['select'] .=',process_master.process_name';
			$queryData['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";
		}
       	
		if(!empty($param['outsource_without_process'])){
			$queryData['select'] .= ',without_process_log.qty AS without_process_qty';
			$queryData['leftJoin']['without_process_log'] = 'without_process_log.log_id  = prc_log.id';
		}

		if(!empty($param['rejection_review_data'])){
			$queryData['select'] .=',IFNULL(rejection_log.review_qty,0) as review_qty,(prc_log.rej_found-IFNULL(rejection_log.review_qty,0)) as pending_qty,rejection_log.ok_qty';
			$queryData['leftJoin']['(SELECT SUM(qty) as review_qty,SUM(CASE WHEN decision_type = 5 THEN qty ELSE 0 END) as ok_qty,log_id,prc_id FROM rejection_log WHERE is_delete = 0 AND decision_type != 2 GROUP BY rejection_log.log_id,prc_id) rejection_log'] = "rejection_log.log_id = prc_log.id AND prc_log.prc_id = rejection_log.prc_id";
		}

		if(!empty($param['log_used_material'])){
			$queryData['select'] .= ",SUM((prc_log.qty+(prc_log.rej_found - IFNULL(rejection_log.ok_qty,0))) * wt_nos) as used_material,SUM(prc_log.qty+(prc_log.rej_found - IFNULL(rejection_log.ok_qty,0))) as production_qty";
		}

		if(!empty($param['fir_data'])){
			$queryData['select'] .=',production_inspection.id AS inspection_id,production_inspection.sampling_qty';
			$queryData['leftJoin']['production_inspection'] = "production_inspection.ref_id = prc_log.id AND production_inspection.report_type = 2";
		}

		if(!empty($param['id'])){ $queryData['where']['prc_log.id'] = $param['id']; }
		
		if(!empty($param['prc_id'])){ $queryData['where']['prc_log.prc_id'] = $param['prc_id']; }	
		
		if(!empty($param['trans_date'])){ $queryData['where']['prc_log.trans_date'] = $param['trans_date']; }	
		
		if(isset($param['process_id'])){ $queryData['where']['prc_log.process_id'] = $param['process_id']; }
		
		if(!empty($param['process_by'])){ $queryData['where_in']['prc_log.process_by'] = $param['process_by']; }
		
		if(!empty($param['processor_id'])){ $queryData['where']['prc_log.processor_id'] = $param['processor_id']; }		
			
		if(!empty($param['operator_id'])){ $queryData['where']['prc_log.operator_id'] = $param['operator_id']; }
		
		if(!empty($param['machine_id'])){ $queryData['where']['prc_log.machine_id'] = $param['machine_id']; }
		
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		
		if(!empty($param['machine_id'])){ $queryData['where']['prc_log.machine_id'] = $param['machine_id']; }

		if(!empty($param['ref_id'])){ $queryData['where']['prc_log.ref_id'] = $param['ref_id']; }
		if(!empty($param['completed_process'])){ $queryData['where']['prc_log.completed_process'] = $param['completed_process']; }
		
 		/*if(!empty($param['created_by'])){ $queryData['where']['prc_log.created_by'] = $param['created_by']; } */

		if(isset($param['ref_trans_id'])){ $queryData['where']['prc_log.ref_trans_id'] = $param['ref_trans_id']; }
		
		if(!empty($param['limit'])){ $queryData['limit'] = $param['limit']; }
		
		if(!empty($param['group_by'])){
			$queryData['group_by'][] = $param['group_by'];
		}
		
		
		if(!empty($param['single_row'])){
			$result = $this->row($queryData);
		}else{
			$result = $this->rows($queryData);
		}
        return $result;  
    }

	public function getProcessMovementList($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "prc_movement";
		
		$queryData['select'] = "prc_movement.*";
		
		if(!empty($param['completedProcessDetail'])){
			$queryData['select'] .= ',(SELECT GROUP_CONCAT(pm.process_name ORDER BY pm.process_name) 
							FROM process_master pm 
							WHERE FIND_IN_SET(pm.id, prc_movement.completed_process) > 0) AS completed_process_name';
		}

		if(!empty($param['prcDetail']) || !empty($param['itemDetail'])){
			$queryData['select'] .= ',prc_master.item_id,prc_master.prc_number,prc_master.prc_qty';
			$queryData['leftJoin']['prc_master'] = "prc_master.id = prc_movement.prc_id";

			if(!empty($param['itemDetail'])){
				$queryData['select'] .= ', item_master.item_name, item_master.item_code';
				$queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
			}
		}
		if(!empty($param['processDetail'])){
			$queryData['select'] .= ', process_master.process_name';
			$queryData['leftJoin']['process_master'] = "process_master.id = prc_movement.process_id";
		}

		if(!empty($param['nextProcessDetail'])){
			$queryData['select'] .= ', next_process.process_name AS next_process_name';
			$queryData['leftJoin']['process_master next_process'] = "next_process.id = prc_movement.next_process_id";
		}

		if(!empty($param['locationDetail'])){
			$queryData['select'] .= ',location_master.store_name';
			$queryData['leftJoin']['location_master'] = "location_master.id = prc_movement.location_id";
		}
			
		if(!empty($param['id'])){ $queryData['where']['prc_movement.id'] = $param['id']; }

		
		if(!empty($param['prc_id'])){ $queryData['where']['prc_movement.prc_id'] = $param['prc_id']; }	
		
		if(!empty($param['trans_date'])){ $queryData['where']['prc_movement.trans_date'] = $param['trans_date']; }	
		
		if(!empty($param['process_id'])){ $queryData['where']['prc_movement.process_id'] = $param['process_id']; }

		if(!empty($param['next_process_id'])){ $queryData['where']['prc_movement.next_process_id'] = $param['next_process_id']; }
		
		if(!empty($param['send_to'])){ $queryData['where']['prc_movement.send_to'] = $param['send_to']; }
		
		if(!empty($param['processor_id'])){ $queryData['where']['prc_movement.processor_id'] = $param['processor_id']; }

		if(!empty($param['completed_process'])){ $queryData['where']['prc_movement.completed_process'] = $param['completed_process']; }

		if(!empty($param['created_by'])){ $queryData['where']['prc_movement.created_by'] = $param['created_by']; } //30-12-2024


		if(!empty($param['next_processor_id'])){ $queryData['where']['prc_movement.next_processor_id'] = $param['next_processor_id']; }	

		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		
		if(!empty($param['group_by'])){
			$queryData['group_by'][] = $param['group_by'];
		}
		if(!empty($param['single_row'])){
			$result = $this->row($queryData);
		}else{
			$result = $this->rows($queryData);
		}
		
		
        return $result;  
    }

	public function getPrcListFromBom($data){
		$queryData['tableName'] = 'prc_master';
		$queryData['select'] = "prc_master.id,prc_master.prc_number,item_kit.process_id,item_kit.qty";
		$queryData['leftJoin']['item_kit'] = "item_kit.item_id = prc_master.item_id AND item_kit.is_delete = 0";
		$queryData['where_in']['prc_master.status'] = '1,2';
		if(!empty($data['bom_item_id'])){
			$queryData['where']['item_kit.ref_item_id'] = $data['bom_item_id'];
		}
		return $this->rows($queryData);
	}

	public function getSopProcessList(){
		$queryData['tableName'] = "process_master";

		$queryData['select'] = 'process_master.id,process_master.process_name';

		//Inward
		$queryData['select'] .= ',(SELECT SUM(prc_movement.qty) FROM prc_movement WHERE prc_movement.is_delete = 0  AND  prc_movement.next_process_id = process_master.id) AS inward_qty';

		//Rej Log
		$queryData['select'] .= ',(SELECT SUM(rejection_log.qty) FROM rejection_log WHERE rejection_log.is_delete = 0 AND  rejection_log.process_id = process_master.id) AS review_qty';

		//Movement
		$queryData['select'] .= ',(SELECT SUM(prc_movement.qty) FROM prc_movement WHERE prc_movement.is_delete = 0 AND  prc_movement.process_id = process_master.id) AS movement_qty';

		//Log
		$queryData['select'] .= ',IFNULL(prc_log.ok_qty,0) AS ok_qty,IFNULL(prc_log.rej_found,0) AS rej_found,IFNULL(prc_log.rej_qty,0) AS rej_qty,IFNULL(prc_log.rw_qty,0) AS rw_qty';
		$queryData['leftJoin']['(SELECT SUM(qty) AS ok_qty,SUM(rej_found) AS rej_found,SUM(rej_qty) AS rej_qty,SUM(rw_qty) AS rw_qty,prc_id,process_id
								FROM prc_log WHERE prc_log.is_delete = 0 GROUP BY process_id ) prc_log'] = 'process_master.id = prc_log.process_id';

		$result = $this->rows($queryData); 
		return $result ;
	}

	public function getInwardDTRow($data){
		
		$data['tableName'] = 'prc_movement';
		$data['select'] = 'prc_movement.move_type,SUM(prc_movement.qty) AS inward_qty,prc_movement.process_id AS process_from,prc_movement.next_process_id AS accepted_process_id,prc_movement.prc_id,prc_movement.completed_process,prc_master.prc_number,prc_master.prc_date,item_master.item_code,item_master.item_name';
		$data['select'] .= ',IFNULL((
									SELECT SUM(prc_accept_log.accepted_qty) 
									FROM prc_accept_log 
									WHERE prc_accept_log.is_delete = 0 
									AND prc_accept_log.prc_id = prc_movement.prc_id 
									AND  prc_accept_log.accepted_process_id = prc_movement.next_process_id 
									AND prc_accept_log.completed_process = prc_movement.completed_process 
									AND prc_accept_log.trans_type = prc_movement.move_type
									),0) AS accepted_qty';

		$data['select'] .= ',(SELECT GROUP_CONCAT(pm.process_name ORDER BY pm.process_name) 
							FROM process_master pm 
							WHERE FIND_IN_SET(pm.id, prc_movement.completed_process) > 0) AS completed_process_name';
		$data['leftJoin']['prc_master'] = 'prc_master.id = prc_movement.prc_id';
		$data['leftJoin']['item_master'] = 'item_master.id = prc_master.item_id';
		$data['where']['prc_movement.next_process_id'] = $data['process_id'];
		
		$data['where']['prc_master.status'] = 2;
		$data['where']['prc_master.prc_type'] = 1;
		/* $data['where']['prc_movement.move_type'] = $data['move_type']; */

		$data['group_by'][]="prc_movement.prc_id,prc_movement.process_id,prc_movement.completed_process,prc_movement.move_type";
		$data['having'][] = '(inward_qty - accepted_qty) > 0';
		

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "prc_master.prc_date";
        $data['searchCol'][] = "CONCAT('[ ',item_master.item_code,' ] ',item_master.item_name,' - ',item_detail.part_no)";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
	
        return $result;
	}

	public function getLogDTRows($data){
		
		$data['tableName'] = 'prc_accept_log';
		$data['select'] = 'prc_accept_log.trans_type,prc_accept_log.accepted_process_id AS process_id,prc_accept_log.prc_id,prc_accept_log.process_from,prc_accept_log.completed_process,SUM(prc_accept_log.accepted_qty) AS in_qty,prc_master.prc_number,prc_master.prc_date,item_master.item_code,item_master.item_name';
		$data['select'] .= ',IFNULL(prc_log.ok_qty,0) AS ok_qty,IFNULL(prc_log.rej_found,0) AS rej_found,IFNULL(prc_log.rej_qty,0) AS rej_qty,IFNULL(prc_log.rw_qty,0) AS rw_qty';

		$data['select'] .= ',IFNULL((SELECT SUM(rejection_log.qty) FROM rejection_log WHERE rejection_log.is_delete = 0 AND rejection_log.prc_id = prc_accept_log.prc_id AND  rejection_log.process_id = prc_accept_log.accepted_process_id AND rejection_log.completed_process = prc_accept_log.completed_process),0) AS review_qty';

		$data['select'] .= ',IFNULL((SELECT SUM(prc_challan_request.qty) FROM prc_challan_request WHERE prc_challan_request.is_delete = 0 AND prc_challan_request.prc_id = prc_accept_log.prc_id AND  prc_challan_request.process_id = prc_accept_log.accepted_process_id AND prc_challan_request.completed_process = prc_accept_log.completed_process),0) AS ch_qty';

		$data['select'] .= ',(SELECT GROUP_CONCAT(pm.process_name ORDER BY pm.process_name) 
							FROM process_master pm 
							WHERE FIND_IN_SET(pm.id, prc_accept_log.completed_process) > 0) AS completed_process_name';

		$data['leftJoin']['(SELECT SUM(qty) AS ok_qty,
									SUM(rej_found) AS rej_found,SUM(rej_qty) AS rej_qty,
									SUM(rw_qty) AS rw_qty,prc_id,process_id,completed_process
								FROM prc_log WHERE prc_log.is_delete = 0 
								GROUP BY prc_id,process_id,completed_process ) prc_log'] = 'prc_log.prc_id = prc_accept_log.prc_id AND prc_accept_log.accepted_process_id = prc_log.process_id  AND prc_accept_log.completed_process = prc_log.completed_process';
		$data['leftJoin']['prc_master'] = 'prc_master.id = prc_accept_log.prc_id';
		$data['leftJoin']['item_master'] = 'item_master.id = prc_master.item_id';
		$data['where']['prc_accept_log.accepted_process_id'] = $data['process_id'];
		
		$data['group_by'][]="prc_accept_log.prc_id,prc_accept_log.accepted_process_id,prc_accept_log.completed_process,prc_accept_log.trans_type";
		$data['having'][] = '(((in_qty) - (ok_qty+(rej_found - rw_qty))) > 0)';
		

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "prc_master.prc_date";
        $data['searchCol'][] = "CONCAT('[ ',item_master.item_code,' ] ',item_master.item_name,' - ',item_detail.part_no)";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
		
        return $result;
	}

	public function getMovementDTRows($data){
		
		$data['tableName'] = 'prc_log';
		$data['select'] = 'prc_log.trans_type,prc_log.process_id,prc_log.completed_process,prc_log.process_from,prc_log.prc_id,SUM(prc_log.qty) AS ok_qty,prc_master.prc_number,prc_master.prc_date,item_master.item_code,item_master.item_name';

		$data['select'] .= ',IFNULL(prc_movement.movement_qty,0) AS movement_qty';

		$data['select'] .= ', (SELECT GROUP_CONCAT(pm.process_name ORDER BY FIND_IN_SET(pm.id, prc_log.completed_process))
     FROM process_master pm
     WHERE FIND_IN_SET(pm.id, prc_log.completed_process) > 0) AS completed_process_name';


		$data['leftJoin']['(SELECT SUM(prc_movement.qty) AS movement_qty,prc_id,process_id,completed_process FROM prc_movement WHERE prc_movement.is_delete = 0 GROUP BY prc_id,process_id,completed_process) prc_movement'] = 'prc_movement.prc_id = prc_log.prc_id AND prc_movement.process_id = prc_log.process_id AND prc_movement.completed_process = CONCAT(prc_log.completed_process,",",prc_log.process_id)';
		$data['leftJoin']['prc_master'] = 'prc_master.id = prc_log.prc_id';
		$data['leftJoin']['item_master'] = 'item_master.id = prc_master.item_id';
		$data['where']['prc_log.process_id'] = $data['process_id'];
		
		$data['group_by'][]="prc_log.prc_id,prc_log.process_id,prc_log.completed_process,prc_log.trans_type";
		// $data['having'][] = 'ok_qty - movement_qty';
		

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "prc_master.prc_date";
        $data['searchCol'][] = "CONCAT('[ ',item_master.item_code,' ] ',item_master.item_name,' - ',item_detail.part_no)";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
		// print_r($this->db->last_query());exit;
        return $result;
	}

	public function getChallanRequestData($param = []){
		$queryData = array();          
		$queryData['tableName'] = "prc_challan_request";
		$queryData['select'] = "prc_challan_request.*";

		if(!empty($param['prcDetail']) || !empty($param['itemDetail']) || !empty($param['productProcess']) || !empty($param['prevProcess'])){
			$queryData['select'] .= ',prc_master.item_id,prc_master.prc_date,prc_master.prc_number,prc_master.process_ids';
			$queryData['leftJoin']['prc_master'] = "prc_master.id = prc_challan_request.prc_id";

			if(!empty($param['itemDetail'])){
				$queryData['select'] .= ',item_master.item_code,item_master.item_name,item_master.hsn_code, material_master.material_grade';
				$queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
				$queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
			}

			if(!empty($param['productProcess'])){
				$queryData['select'] .= ', product_process.finish_wt, product_process.uom,product_process.mfg_instruction,product_process.output_qty,product_process.process_cost';
				$queryData['leftJoin']['product_process'] = "product_process.item_id = prc_master.item_id AND product_process.process_id = prc_challan_request.process_id AND product_process.is_delete = 0";
			}

			if(!empty($param['prevProcess'])){
				$queryData['select'] .= ',prevProcess.finish_wt AS prev_weight';
				$queryData['leftJoin']['product_process prevProcess'] = "prevProcess.item_id = item_master.id AND prevProcess.process_id = prc_challan_request.process_from AND prevProcess.is_delete = 0";
			}
		}
		if(!empty($param['processDetail'])){
			$queryData['select'] .= ',process_master.process_name';
			$queryData['leftJoin']['process_master'] = "process_master.id = prc_challan_request.process_id";
		}	
		if(!empty($param['fromProcessDetail'])){
			$queryData['select'] .= ', fromProcess.process_name AS from_process_name';
			$queryData['leftJoin']['process_master fromProcess'] = "fromProcess.id = prc_challan_request.process_from";
		}
		
		if(!empty($param['createdByDetail'])){
			$queryData['select'] .= ',employee_master.emp_name as created_name';
			$queryData['leftJoin']['employee_master'] = "employee_master.id = prc_challan_request.created_by";
		}
		
		if(!empty($param['challan_receive'])){
			$queryData['select'] .= ',IFNULL(receiveLog.ok_qty,0) as ok_qty,IFNULL(receiveLog.rej_qty,0) as rej_qty';
			$queryData['leftJoin']['(SELECT sum(qty) as ok_qty,SUM(rej_found) as rej_qty,process_id,ref_trans_id FROM prc_log WHERE is_delete = 0 AND process_by = 3 GROUP BY process_id,ref_trans_id) as receiveLog'] = "receiveLog.ref_trans_id = prc_challan_request.id AND prc_challan_request.process_id = receiveLog.process_id";
		}
		if(!empty($param['id'])){ $queryData['where']['prc_challan_request.id'] = $param['id']; }
		if(!empty($param['challan_id'])){ $queryData['where']['prc_challan_request.challan_id'] = $param['challan_id']; }
		if(!empty($param['process_id'])){ $queryData['where']['prc_challan_request.process_id'] = $param['process_id']; }
		if(!empty($param['completed_process'])){ $queryData['where']['prc_challan_request.completed_process'] = $param['completed_process']; }
		if(!empty($param['prc_id'])){ $queryData['where']['prc_challan_request.prc_id'] = $param['prc_id']; }	
		if(!empty($param['trans_date'])){ $queryData['where']['prc_challan_request.trans_date'] = $param['trans_date']; }	
		if(!empty($param['trans_type'])){ $queryData['where']['prc_challan_request.trans_type'] = $param['trans_type']; }	
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		if(!empty($param['pending_challan'])){ $queryData['where']['prc_challan_request.challan_id'] = 0; }
		if(!empty($param['single_row'])){ $result = $this->row($queryData); }
		else{ $result = $this->rows($queryData); }
		
        return $result; 
	}

	public function saveChallanRequest($data){
		try {
			$this->db->trans_begin();
			$prcData = $this->getPRCDetail(['id'=>$data['prc_id']]);
			if(!in_array($prcData->status,[1,2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
			
			$result = $this->store('prc_challan_request', $data, 'Challan Request');
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function deletePRCMovement($param){
		try {
			$this->db->trans_begin();
			$movementData = $this->getProcessMovementList(['id'=>$param['id'],'itemDetail'=>1,'single_row'=>1]);
			$prcData = [];
			if(!empty($movementData)){
				$prcData = $this->getPRCDetail(['id'=>$movementData->prc_id]);
			}
			if(empty($prcData->status) || !in_array($prcData->status,[2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
			if(!empty($movementData)){
				if(!empty($movementData->next_process_id)){
					$nextProcessData =  $this->sop->getPendingAcceptData(['process_id'=>$movementData->next_process_id,'prc_id'=>$movementData->prc_id,'completed_process'=>$movementData->completed_process,'trans_type'=>$movementData->move_type]); 
					$pending_accept =$nextProcessData->inward_qty - $nextProcessData->accept_qty;
					if($movementData->qty > $pending_accept ){
						return ['status'=>0,'message'=>'You can not delete this movement. This movement accepted by next process'];
					}
				}else{
					
					$stockData = $this->itemStock->getItemStockBatchWise(['item_id'=>$movementData->item_id,'location_id'=>$this->RTD_STORE->id,'batch_no'=>$movementData->prc_number,'single_row'=>1,'completed_process'=>$movementData->completed_process]);
					if($movementData->qty > $stockData->qty){
						return ['status'=>0,'message'=>'You can not delete this movement'];
					}
					
					$this->remove('stock_trans',['main_ref_id'=>$movementData->prc_id,'child_ref_id'=>$movementData->id,'trans_type'=>'PRD']);
				}
				
				$result = $this->trash('prc_movement',['id'=>$param['id']]);
				
				$this->changePrcStatus(['prc_id'=>$movementData->prc_id]);
			}else{
				$result = ['status'=>0,'message'=>'movement already deleted'];
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

	public function deletePrcAccept($param){
		try {
			$this->db->trans_begin();
			$acceptData = $this->getPrcAcceptData(['id'=>$param['id'],'single_row'=>1]);
			$prcData = [];
			if(!empty($movementData)){
				$prcData = $this->getPRCDetail(['id'=>$acceptData->prc_id]);
			}
			if(empty($prcData->status) || !in_array($prcData->status,[2,3])){
				return ['status'=>0,'message'=>'Accepted qty already deleted.'];
			}
			if(!empty($acceptData)){
				$logData = $this->sop->getPendingLogData(['prc_id'=>$acceptData->prc_id,'process_id'=>$acceptData->accepted_process_id,'completed_process'=>$acceptData->completed_process,'trans_type'=>$acceptData->trans_type]);
				$in_qty = $logData->in_qty;
				$ok_qty = !empty($logData->ok_qty)?$logData->ok_qty:0;
				$rej_found = !empty($logData->rej_found)?$logData->rej_found:0;
				$rw_qty = !empty($logData->rw_qty)?$logData->rw_qty:0;
				$rej_qty = !empty($logData->rej_qty)?$logData->rej_qty:0;
				$pendingReview = $rej_found - $logData->review_qty;
               	$pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$logData->ch_qty);
				if($acceptData->accepted_qty > $pending_production ){ return ['status'=>0,'message'=>'You can not unaccept this qty'.$acceptData->accepted_qty .'>' .$pending_production ]; }
				
				$result = $this->trash('prc_accept_log',['id'=>$param['id']]);
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

	public function deleteChallanRequest($data){
		try {
			$this->db->trans_begin();
			$getChallanData = $this->getChallanRequestData(['id'=>$data['id'],'single_row'=>1]);
			if(!empty($getChallanData->challan_id)){
				return ['status'=>0,'message'=>'Challan already created.'];
			}
			$result = $this->trash('prc_challan_request', ['id'=>$data['id']], 'Challan Request');
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function deletePRCLog($param){
		try {
			$this->db->trans_begin();

			$logData = $this->getProcessLogList(['id'=>$param['id'],'rejection_review_data'=>1,'single_row'=>1,'outsource_without_process'=>1]);
			$prcData = [];
			if(!empty($logData)){
				$prcData = $this->getPRCDetail(['id'=>$logData->prc_id]);
			}
			if(empty($prcData->status) || !in_array($prcData->status,[2,3])){
				return ['status'=>0,'message'=>'Log already deleted'];
			}

			if(!empty($logData)){
				$movementData =  $this->sop->getPendingMovementData(['prc_id'=>$logData->prc_id,'process_id'=>$logData->process_id,'completed_process'=>$logData->completed_process,'trans_type'=>$logData->trans_type]);
				$pending_movement = $movementData->ok_qty - $movementData->movement_qty;

				if ($logData->review_qty > 0){
					return ['status'=>0,'message'=>'You can not delete this Log. You have to delete rejection review first'];
				}
				if(($logData->qty > $pending_movement) ){
					return ['status'=>0,'message'=>'You can not delete this Log. Qty is sent to next process'];
				}
				$result = $this->trash('prc_log',['id'=>$param['id']]);
				if($logData->process_id == 2){
					$this->trash('production_inspection',['ref_id'=>$param['id'],'report_type'=>2]);
				}
				if($prcData->first_process == $logData->process_id){
					$required_qty = ($logData->qty + $logData->rej_found) * $logData->wt_nos;
					/** UPDATE BOM & MAIN BATCH TABLE */	
					$this->updatePrcBatch(['id'=>$prcData->batch_id,'qty'=> $required_qty , 'update_field'=>'used_qty','opr'=>'Minus']);
					$this->updatePrcBom(['prc_id'=>$logData->prc_id,'qty'=> $required_qty , 'update_field'=>'used_qty','opr'=>'Minus']);
				}
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

	public function changePrcStatus($param){
		try {
			$this->db->trans_begin();
			$queryData['tableName'] = "prc_master";
			$queryData['select'] = 'prc_master.prc_qty,prc_master.item_id,IFNULL(rejection_log.rej_qty,0) as rej_qty,IFNULL(prc_movement.stored_qty,0) as stored_qty';
			$queryData['leftJoin']['(SELECT SUM(qty) as rej_qty,prc_id FROM rejection_log WHERE decision_type = 1 AND source="MFG" AND is_delete = 0 GROUP BY prc_id) rejection_log'] = "prc_master.id = rejection_log.prc_id";
			$queryData['leftJoin']['(SELECT SUM(qty) as stored_qty,prc_id FROM prc_movement WHERE next_process_id = 0 AND is_delete = 0 GROUP BY prc_id) prc_movement'] = "prc_master.id = prc_movement.prc_id";
			$queryData['where']['prc_master.id'] = $param['prc_id'];
			$prcData = $this->row($queryData); 

			$prdProcessData = $this->item->getProductProcessList(['item_id'=>$prcData->item_id,'max_output_qty'=>1,'single_row'=>1]);
			$status = 2;
			if(($prcData->rej_qty + $prcData->stored_qty) >= ($prcData->prc_qty * $prdProcessData->max_output_qty)){
				$status = 3;
			}
			$result = $this->store("prc_master",['id'=>$param['prc_id'],'status'=>$status,'stored_qty'=>$prcData->stored_qty,'rej_qty'=>$prcData->rej_qty]);

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function getFirNextNo($type = 2){
		$queryData['tableName'] = 'production_inspection';
		$queryData['select'] = "ifnull(MAX(trans_no + 1),1) as next_no";
		$queryData['where']['report_type'] = $type;
		$queryData['where']['insp_date >='] = $this->startYearDate;
		$queryData['where']['insp_date <='] = $this->endYearDate;
		return $this->row($queryData)->next_no;
	}

	public function getFinalInspectData($data) {
		$queryData = array();
		$queryData['tableName'] = 'production_inspection';
		$queryData['select'] = "production_inspection.id, production_inspection.report_type, production_inspection.ref_id, production_inspection.insp_date, production_inspection.insp_time, production_inspection.rev_no, production_inspection.prc_id, production_inspection.item_id, production_inspection.process_id, production_inspection.machine_id, production_inspection.operator_id, production_inspection.sampling_qty, production_inspection.param_count, production_inspection.parameter_ids, production_inspection.observation_sample, production_inspection.trans_no, production_inspection.trans_number,prc_master.prc_number,item_master.item_name,employee_master.emp_name,prc_log.qty AS ok_qty,prc_log.rej_found,ecn_master.rev_no, ecn_master.cust_rev_no, ecn_master.drw_no,material_master.material_grade,item_master.item_code,item_master.mfg_type,item_master.wt_pcs";
		$queryData['leftJoin']['prc_master'] = "prc_master.id = production_inspection.prc_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = production_inspection.item_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = production_inspection.created_by";
		$queryData['leftJoin']['prc_log'] = "prc_log.id = production_inspection.ref_id";
		$queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
		$queryData['leftJoin']['(SELECT drw_no,cust_rev_date,cust_rev_no,rev_no,rev_date,item_id FROM ecn_master WHERE is_delete=0 AND status=2 GROUP BY item_id ORDER BY ecn_date DESC) as ecn_master'] = "ecn_master.item_id = production_inspection.item_id";
		

		
		if(!empty($data['rejection_review_data'])){
			$queryData['select'] .= ",IFNULL(rejection_log.review_qty,0) as review_qty,(production_inspection.rej_found-IFNULL(rejection_log.review_qty,0)) as pending_qty";
			$customWhere = !empty($data['id'])?' AND log_id ='.$data['id']:'';
			$queryData['leftJoin']['(SELECT SUM(qty) as review_qty,log_id,prc_id FROM rejection_log WHERE is_delete = 0 '.$customWhere.' AND rejection_log.source="FIR" GROUP BY rejection_log.log_id,prc_id) rejection_log'] = "rejection_log.log_id = production_inspection.id";
		}
		$queryData['where']['production_inspection.id'] = $data['id']; 
		return $this->row($queryData);
		
	}

	public function getPrcBatchDTRows($data){
        $data['tableName'] = 'prc_batch';
        $data['select'] = "prc_batch.*,item_master.item_name,item_master.item_code,employee_master.emp_name as issue_name,createdBy.emp_name as created_name";
		
        $data['leftJoin']['item_master'] = "item_master.id  = prc_batch.item_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = prc_batch.issue_to";
        $data['leftJoin']['employee_master createdBy'] = "createdBy.id = prc_batch.created_by";

        $data['where']['prc_batch.status'] = 2;
        $data['order_by']['prc_batch.trans_number'] = 'ASC';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(prc_batch.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "prc_batch.tras_number";
        $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
        $data['searchCol'][] = "prc_batch.issue_qty"; 
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "CONCAT(createdBy.emp_name,DATE_FORMAT(prc_batch.created_at,'%d-%m-%Y %H:%i:%s'))";
        $data['searchCol'][] = "prc_batch.remark";
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

	public function storeReturnedMaterial($data){
		try {
			$this->db->trans_begin();
			$prcData = $this->getPRCDetail(['id'=>$data['prc_id']]);
			$stockData = [
                'id'=>'',
                'trans_type'=>"PMR",
                'trans_date'=>date("Y-m-d"),
                'ref_no'=>$prcData->prc_number,
                'main_ref_id'=>$data['batch_id'],
                'child_ref_id'=>$data['prc_id'],
                'location_id '=>$data['location_id'],
                'batch_no'=>$data['batch_no'],
                'item_id'=>$data['item_id'],
                'p_or_m'=>1,
                'qty'=>$data['qty'],
                'remark'=>$data['remark'],
                'created_by'=>$this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $result = $this->store("stock_trans",$stockData);

			/** UPDATE BOM & MAIN BATCH TABLE */	
			$this->updatePrcBatch(['id'=>$data['batch_id'],'qty'=> $data['qty'] , 'update_field'=>'return_qty']);
			$this->updatePrcBom(['item_id'=>$data['item_id'],'prc_id'=>$data['prc_id'],'qty'=> $data['qty'] , 'update_field'=>'return_qty']);
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function saveIssuedTool($data){ 
		try {
			$this->db->trans_begin();

			foreach($data['die_id'] As $key=>$die_id){
				$prcTool = [
					'id'=>'',
					'prc_id'=>$data['prc_id'],
					'die_id'=>$die_id,
					'die_reg_id'=>$data['die_reg_id'][$die_id],
				];
				$this->store("prc_tool_log",$prcTool);
				$result = $this->store("die_register",['id'=>$data['die_reg_id'][$die_id],'prc_id'=>$data['prc_id'],'status'=>2]);
				
				$getDieRegister = $this->dieProduction->getDieRegisterData(['customWhere'=>'die_register.id ='.$data['die_reg_id'][$die_id],"single_row"=>1]);
				$historyData = [
					'id'=>'',
					'die_reg_id'=>(!empty($result['id']) ? $result['id'] : 0),
					'wo_id'=>(!empty($getDieRegister->wo_id) ? $getDieRegister->wo_id : 0),
					'die_id'=>$die_id,
					'emp_id'=>$this->loginId,
					'item_id'=>(!empty($getDieRegister->item_id)?$getDieRegister->item_id:0),
					'old_item_id'=>(!empty($getDieRegister->item_id)?$getDieRegister->item_id:0),
					'remark'=>"",
					'trans_type'=>$this->history_type[3]
				];
				$this->store('die_history',$historyData);
			}
			$result = $this->store("prc_master",['id'=>$data['prc_id'],'tool_status'=>1]);
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function saveReleaseTool($data){ 
		try {
			$this->db->trans_begin();

			foreach($data['log_id'] As $key=>$log_id){
				$result = $this->store("prc_tool_log",['id'=>$log_id,'status'=>2]);
				$this->edit("die_register",['id'=>$data['die_reg_id'][$log_id]],['prc_id'=>0,'status'=>$data['status'][$log_id]]);

				// IF Tool is converted
				if($data['status'][$log_id] == 4){
					$prcData =$this->sop->getPRCDetail(['id'=>$data['prc_id']]);
					$regData = $this->dieProduction->getDieRegisterData(['id'=>$data['die_reg_id'][$log_id],'single_row'=>1]);
					$stockData = [
						'id' => "",
						'trans_type' => 'CDIE',
						'trans_date' => date("Y-m-d"),
						'ref_no' => $prcData->prc_number,
						'main_ref_id' => $data['prc_id'],
						'child_ref_id' => $log_id,
						'location_id' => $this->SCRAP_STORE->id,
						'batch_no' =>$regData->die_number,
						'item_id' => 1,
						'p_or_m' => 1,
						'qty' => 1,
					];
					$this->store('stock_trans',$stockData);
				}
				
				if($data['status'][$log_id] == 5){
					$getDieRegister = $this->dieProduction->getDieRegisterData(['customWhere'=>'die_register.id ='.$data['die_reg_id'][$log_id],"single_row"=>1]);
					$historyData = [
						'id'=>'',
						'die_reg_id'=>(!empty($data['die_reg_id'][$log_id]) ? $data['die_reg_id'][$log_id] : 0),
						'wo_id'=>(!empty($getDieRegister->wo_id) ? $getDieRegister->wo_id : 0),
						'die_id'=>(!empty($getDieRegister->die_id) ? $getDieRegister->die_id : 0),
						'emp_id'=>$this->loginId,
						'item_id'=>(!empty($getDieRegister->item_id)?$getDieRegister->item_id:0),
						'old_item_id'=>(!empty($getDieRegister->item_id)?$getDieRegister->item_id:0),
						'remark'=>"",
						'trans_type'=>$this->history_type[6]
					];
					$this->store('die_history',$historyData);
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

	public function getToolData($data){
		$queryData['tableName'] = 'prc_tool_log';
		$queryData['select'] = 'prc_tool_log.*,item_category.category_name AS die_name,die_register.die_number,main_cat.category_name';

		$queryData['leftJoin']['die_register'] = 'prc_tool_log.die_reg_id = die_register.id';
		$queryData['leftJoin']['die_master'] = 'prc_tool_log.die_id = die_master.id';
        $queryData['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        $queryData['leftJoin']['item_category main_cat'] = 'main_cat.id = item_category.ref_id';
		if(!empty($data['prc_id'])){
			$queryData['where']['prc_tool_log.prc_id'] = $data['prc_id'];
		}
		if(!empty($data['status'])){
			$queryData['where']['prc_tool_log.status'] = $data['status'];
		}
		if(!empty($data['single_row'])){
			return $this->row($queryData);
		}else{
			return $this->rows($queryData);
		}
	}

	public function changePRCStage($data){
        try{
            $this->db->trans_begin();

            $result = $this->store('prc_master',$data,'PRC');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function savePrcQty($data){
        try {
            $this->db->trans_begin();

            $operation = ($data['log_type'] == 1) ? '+' : '-';
            $result = $this->store('prc_update', $data, 'PRC Qty');

			$prcData = $this->getPRCDetail(['id'=>$data['prc_id']]);
			/* Update Movement Qty */
            $setData = array();
            $setData['tableName'] = 'prc_movement';
            $setData['where']['prc_id'] = $data['prc_id'];
            $setData['where']['process_id'] = 0;
            $setData['set']['qty'] = 'qty,' . $operation . $data['qty'];
            $this->setValue($setData);

			$setData = array();
            $setData['tableName'] = 'prc_accept_log';
            $setData['where']['prc_id'] = $data['prc_id'];
            $setData['where']['accepted_process_id'] =$prcData->first_process;
            $setData['set']['accepted_qty'] = 'accepted_qty,' . $operation . $data['qty'];
            $this->setValue($setData);

			/* Update PRC Qty */
            $updateQuery = array();
            $updateQuery['tableName'] = 'prc_master';
            $updateQuery['where']['id'] = $data['prc_id'];
            $updateQuery['set']['prc_qty'] = 'prc_qty,' . $operation . $data['qty'];
            $this->setValue($updateQuery);
			
			$this->changePrcStatus(['prc_id'=>$data['prc_id']]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function getPRCUpdateLogData($param){
		$data['tableName'] = "prc_update";
		if(!empty($param['prc_id'])) { $data['where']['prc_id'] = $param['prc_id']; }
		if(!empty($param['id'])) { $data['where']['id'] = $param['id']; }
		
		if(!empty($param['single_row'])):
			return $this->row($data);
		else:
			return $this->rows($data);
		endif;
	}

    public function deletePrcUpdateQty($id){
        try {
            $this->db->trans_begin();

			$logData = $this->getPrcUpdateLogData(['id'=>$id,'single_row'=>1]);
            $operation = ($logData->log_type == 1) ? '-' : '+';
            $result = $this->trash('prc_update', ['id' => $id], 'PRC Qty');

			$prcData = $this->getPRCDetail(['id'=>$logData->prc_id]);
			/* Update Log Qty */
            $setData = array();
            $setData['tableName'] = 'prc_movement';
            $setData['where']['prc_id'] = $logData->prc_id;
            $setData['where']['process_id'] = 0;
            $setData['set']['qty'] = 'qty,' . $operation . $logData->qty;
            $this->setValue($setData);

			$setData = array();
            $setData['tableName'] = 'prc_accept_log';
            $setData['where']['prc_id'] = $logData->prc_id;
            $setData['where']['accepted_process_id'] =$prcData->first_process;
            $setData['set']['accepted_qty'] = 'accepted_qty,' . $operation . $logData->qty;
            $this->setValue($setData);

			/* Update PRC Qty */
            $updateQuery = array();
            $updateQuery['tableName'] = 'prc_master';
            $updateQuery['where']['id'] = $logData->prc_id;
            $updateQuery['set']['prc_qty'] = 'prc_qty,' . $operation . $logData->qty;
            $this->setValue($updateQuery);			

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
	
	/****** Start Logs Detail */
	public function getAccepedLogDTRow($data){
		$data['tableName'] = 'prc_accept_log';
		$data['select'] = 'prc_accept_log.*,prc_master.prc_number,prc_master.prc_date,item_master.item_name,item_master.item_code,item_master.part_no';

		$data['leftJoin']['prc_master'] = 'prc_master.id = prc_accept_log.prc_id';
		$data['leftJoin']['item_master'] = 'prc_master.item_id = item_master.id';

		$data['where_in']['prc_accept_log.accepted_process_id'] = $data['process_id'];
		$data['order_by']["prc_accept_log.id"] = 'DESC';
		$data['where']['prc_accept_log.trans_type'] = $data['move_type'];
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "prc_master.prc_date";
        $data['searchCol'][] = "CONCAT('[ ',item_master.item_code,' ] ',item_master.item_name,' - ',item_master.part_no)";
        $data['searchCol'][] = "prc_accept_log.accepted_qty";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
	}
	
	public function getChallanLogDTRows($data){
		$data['tableName'] = "prc_challan_request";
		$data['select'] = "prc_challan_request.*,prc_master.prc_date,prc_master.prc_number,item_master.item_name,item_master.item_code,party_master.party_name,item_master.part_no";
		$data['leftJoin']['prc_master'] = "prc_master.id = prc_challan_request.prc_id";
		$data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$data['leftJoin']['outsource'] = "outsource.id = prc_challan_request.challan_id";
		$data['leftJoin']['party_master'] = "party_master.id = outsource.party_id";

		$data['where_in']['prc_challan_request.process_id'] = $data['process_id'];
		$data['where']['prc_challan_request.trans_type'] = $data['move_type'];
		$data['order_by']["prc_challan_request.id"] = 'DESC';
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('[ ',item_master.item_code,' ] ',item_master.item_name,' - ',item_master.part_no)";
        $data['searchCol'][] = "DATE_FORMAT(prc_challan_request.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "prc_challan_request.qty";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
	}
	
	public function getProductionLogDTRows($data){
		$data['tableName'] = 'prc_log';
		$data['select'] = 'prc_log.*,prc_master.prc_number,prc_master.prc_date,item_master.item_name,item_master.item_code,shift_master.shift_name, machine.item_code as machine_code, machine.item_name as machine_name,employee_master.emp_name,item_master.part_no';
		$data['select'] .=', IF(prc_log.process_by = 1, machine.item_code, IF(prc_log.process_by = 2,department_master.name, IF(prc_log.process_by = 3,party_master.party_name,""))) as processor_name';
		
		$data['leftJoin']['prc_master'] = 'prc_master.id = prc_log.prc_id';
		$data['leftJoin']['item_master'] = 'prc_master.item_id = item_master.id';
		$data['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";
		$data['leftJoin']['party_master'] = "party_master.id = prc_log.processor_id";
		$data['leftJoin']['department_master'] = "department_master.id = prc_log.processor_id";
		$data['leftJoin']['item_master machine'] = "machine.id = prc_log.processor_id";
		$data['leftJoin']['shift_master'] = "shift_master.id = prc_log.shift_id";
		$data['leftJoin']['employee_master'] = "employee_master.id = prc_log.operator_id";

		$data['where_in']['prc_log.process_id'] = $data['process_id'];
		$data['where']['prc_log.trans_type'] = $data['move_type'];
		$data['order_by']["prc_log.id"] = 'DESC';
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "CONCAT('[ ',item_master.item_code,' ] ',item_master.item_name,' - ',item_master.part_no)";
		$data['searchCol'][] = "prc_log.trans_date";
        $data['searchCol'][] = "prc_log.production_time";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "shift_master.shift_name";
        $data['searchCol'][] = "prc_log.qty";
        $data['searchCol'][] = "prc_log.rej_found";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
	}
	
	public function getPrcMovementDTRows($data){
		$data['tableName'] = 'prc_movement';
		$data['select'] = 'prc_movement.*,prc_master.prc_number,prc_master.prc_date,item_master.item_name,item_master.item_code,item_master.part_no';

		$data['leftJoin']['prc_master'] = 'prc_master.id = prc_movement.prc_id';
		$data['leftJoin']['item_master'] = 'prc_master.item_id = item_master.id';
		
		$data['where_in']['prc_movement.process_id'] = $data['process_id'];
		$data['where']['prc_movement.move_type'] = $data['move_type'];
		$data['where']['prc_movement.ref_id'] = 0;
		$data['order_by']["prc_movement.id"] = 'DESC';
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "prc_master.prc_date";
        $data['searchCol'][] = "CONCAT('[ ',item_master.item_code,' ] ',item_master.item_name,' - ',item_master.part_no)";
        $data['searchCol'][] = "prc_movement.qty";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
	}
}
?>