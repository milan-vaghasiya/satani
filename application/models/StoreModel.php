<?php
class StoreModel extends MasterModel{
    private $issueRegister = "issue_register";
    private $materialReturn = "material_return";
    private $stockTransation = "stock_trans";
    private $store_request = "store_request";

    public function getNextIssueNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'issue_register';
        $queryData['select'] = "ifnull(MAX(issue_no + 1),1) as issue_no";
		$queryData['where']['issue_register.issue_date >='] = $this->startYearDate;
		$queryData['where']['issue_register.issue_date <='] = $this->endYearDate;

		$issue_no = $this->specificRow($queryData)->issue_no;
		return $issue_no;
    }

    public function getIssueDTRows($data){
        $data['tableName'] = $this->issueRegister;
        $data['select'] = "issue_register.*,item_master.item_name,item_master.item_code,(CASE WHEN issue_register.issue_type = 2 THEN prc_master.prc_number ELSE '' END) as prc_number,item_master.item_type,(issue_register.issue_qty - issue_register.return_qty) as pending_qty,employee_master.emp_name as issue_name,employee_master.unit_id,createdBy.emp_name as created_name";
		
        $data['leftJoin']['item_master'] = "item_master.id  = issue_register.item_id";
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        $data['leftJoin']['prc_master'] = "issue_register.prc_id  = prc_master.id";
        $data['leftJoin']['store_request'] = "issue_register.prc_id = store_request.id";
        $data['leftJoin']['employee_master'] = "employee_master.id = issue_register.issued_to";
        $data['leftJoin']['employee_master createdBy'] = "createdBy.id = issue_register.created_by";

        if($data['item_type'] == 1){
            $data['where_in']['item_master.item_type'] = "1,3,7";
        }else{
            $data['where_in']['item_master.item_type'] = "2,9"; 
        }
        if($data['status'] == 2){
            $data['where']['item_category.is_return'] = 1;
			$data['where']['(issue_register.issue_qty - issue_register.return_qty) >'] = 0;
        }
        $data['order_by']['issue_register.issue_number'] = 'ASC';
        if($data['status'] == 1){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "issue_register.issue_number";
            $data['searchCol'][] = "DATE_FORMAT(issue_register.issue_date,'%d-%m-%Y')";
            $data['searchCol'][] = "prc_master.prc_number";
            $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
            $data['searchCol'][] = "issue_register.issue_qty";
            $data['searchCol'][] = "issue_register.batch_no";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "(CASE WHEN employee_master.unit_id = 1 THEN 'UNIT 1' WHEN employee_master.unit_id = 2 THEN 'UNIT 2' ELSE '' END)";
		    $data['searchCol'][] = "CONCAT(createdBy.emp_name,DATE_FORMAT(issue_register.created_at,'%d-%m-%Y %H:%i:%s'))";
			$data['searchCol'][] = "issue_register.remark";
			
            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        }elseif($data['status'] == 2){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "issue_register.issue_number";
            $data['searchCol'][] = "DATE_FORMAT(issue_register.issue_date,'%d-%m-%Y')";
            $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
            $data['searchCol'][] = "issue_register.issue_qty";
            $data['searchCol'][] = "issue_register.return_qty";
            $data['searchCol'][] = "ifnull(issue_register.issue_qty - issue_register.return_qty,0.00)";
			$data['searchCol'][] = "issue_register.remark";
            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        }
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getInspDTRows($data) {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = "material_return.*,issue_register.issue_number";
        $data['leftJoin']['issue_register'] = "material_return.issue_id  = issue_register.id";
        $data['where']['material_return.trans_type'] = $data['trans_type'];

        if($data['trans_type'] == 1){
            $data['where']['(material_return.total_qty - material_return.insp_qty) > '] = 0;
        }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "issue_register.issue_number";
        $data['searchCol'][] = "DATE_FORMAT(material_return.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "material_return.total_qty";
        $data['searchCol'][] = "material_return.batch_no";
        $data['searchCol'][] = "material_return.remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getIssueRequest($param) {
        $queryData = array();          
		$queryData['tableName'] = "issue_register";
        if(!empty($param['id'])){ $queryData['where']['id'] = $param['id']; }
        if(!empty($param['item_id'])){ $queryData['where']['item_id'] = $param['item_id']; }
        $result = $this->row($queryData);
        return $result;
    }

    public function getMaterialIssueData($param) {
        $queryData = array();          
		$queryData['tableName'] = "issue_register";
        $queryData['select'] = "issue_register.*, IFNULL(im.item_name,'') as item_name, IFNULL(prc_master.prc_number,'') as prc_number,im.uom";
        $queryData['leftJoin']['item_master im'] = "im.id = issue_register.item_id ";
        $queryData['leftJoin']['prc_master'] = "prc_master.id = issue_register.prc_id";
        if(!empty($param['prcHeatData'])){
            $queryData['select'] .= ',prc_heat.process_id,process_master.process_name';
            $queryData['leftJoin']['prc_heat'] = "prc_heat.item_id = issue_register.item_id AND prc_heat.prc_id  = issue_register.prc_id AND prc_heat.batch_no = issue_register.batch_no";
            $queryData['leftJoin']['process_master'] = "process_master.id = prc_heat.process_id";
        }
        if(!empty($param['supplier_data'])){
            $queryData['select'] .= ',party_master.party_name,batch_history.party_id';
            $queryData['leftJoin']['batch_history'] = 'issue_register.item_id = issue_register.item_id AND issue_register.batch_no = batch_history.batch_no AND batch_history.is_delete = 0';
            $queryData['leftJoin']['party_master'] = 'batch_history.party_id = party_master.id';
        }
        if(!empty($param['sum_data'])){
            $queryData['select'] .= ',SUM(issue_qty) as issue_qty';
            
        }

        if(!empty($param['id'])){ $queryData['where']['issue_register.id'] = $param['id']; }
        if(!empty($param['item_id'])){ $queryData['where']['issue_register.item_id'] = $param['item_id']; }
        if(!empty($param['prc_id'])){ $queryData['where']['issue_register.prc_id'] = $param['prc_id']; }
        if(!empty($param['batch_no'])){ $queryData['where']['issue_register.batch_no'] = $param['batch_no']; }
        if(!empty($param['issue_date'])){ $queryData['where']['issue_register.issue_date'] = $param['issue_date']; }
        if(!empty($param['from_date'])){ $queryData['where']['issue_register.issue_date >='] = $param['from_date']; }
        if(!empty($param['to_date'])){ $queryData['where']['issue_register.issue_date <='] = $param['to_date'];  }
        if(!empty($param['issue_type'])){ $queryData['where']['issue_register.issue_type'] = $param['issue_type']; }
        if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }

        if(!empty($param['group_by'])){ $queryData['group_by'][] = $param['group_by']; }

        if(!empty($param['skey'])){
			$queryData['like']['issue_register.trans_number'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['issue_register.trans_date'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['prc_master.prc_number'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['im.item_name'] = str_replace(" ", "%", $param['skey']);
        }

        if(!empty($param['limit'])){ $queryData['limit'] = $param['limit']; }
        
		if(isset($param['start'])){ $queryData['start'] = $param['start']; }
		
		if(!empty($param['length'])){ $queryData['length'] = $param['length']; }

        $queryData['order_by']['issue_register.issue_date'] = 'DESC';
		$queryData['order_by']['issue_register.id'] = 'DESC';

        if(!empty($param['single_row'])){
            $result = $this->row($queryData);
        }else{
            $result = $this->rows($queryData);
        }
        
        return $result;
    }

    public function getMaterialData($param) {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = "material_return.*";
        $data['leftJoin']['issue_register'] = "issue_register.id  = material_return.issue_id";
		$data['where']['material_return.id'] = $param['id'];
        return $this->row($data);
    }

    public function saveIssueRequisition($data){           
        try {
            $this->db->trans_begin();
            $ppc_qty = (!empty($data['ppc_qty']) ? $data['ppc_qty'] : 0);unset($data['ppc_qty']);
            foreach ($data['batch_qty'] as $key => $value) {

                $issue_no = $this->getNextIssueNo();
                if(!empty($value) && $value > 0) {

                    $issueData = [
                        'id' => '',
                        'issue_type' => (!empty($data['issue_type']) ? $data['issue_type'] : 1),
                        'issue_no' => $issue_no,
                        'issue_number' => 'ISU/'.str_pad($issue_no, 5, '0', STR_PAD_LEFT),
                        'issue_date' => date("Y-m-d"),
                        'item_id' => $data['item_id'],
                        'batch_no' => $data['batch_no'][$key],
                        'heat_no' => $data['heat_no'][$key],
                        'prc_id' => $data['prc_id'],
                        'issue_qty' => $value,
                        'issued_to' => $data['issued_to'],
						'remark' => (!empty($data['remark'])?$data['remark']:''),
                        'created_by' => $data['created_by']
                    ];
                    $result = $this->store($this->issueRegister, $issueData, 'Issue Requisition');

                    $stockMinusQuery = [
                        'id' => "",
                        'trans_type' => ((!empty($data['issue_type']) && $data['issue_type'] == 3) ? 'SDI' :'SSI'),
                        'trans_date' => date("Y-m-d"),
                        'location_id'=> $data['location_id'][$key],
                        'batch_no' => $data['batch_no'][$key],
                        'item_id' => $data['item_id'],
                        'qty' => $value,
                        'p_or_m' => -1,
                        'main_ref_id' => $result['insert_id'],
                        'child_ref_id' =>  $data['prc_id'],
                        'ref_no' => 'ISU/'.str_pad($issue_no, 5, '0', STR_PAD_LEFT),
                        'created_by' => $data['created_by']
                    ];
                    $issueTrans = $this->store('stock_trans', $stockMinusQuery);
					
					if(!empty($data['prc_id']) && $data['issue_type'] == 4){
                        $setData = Array();
                        $setData['tableName'] = 'store_request';
                        $setData['where']['id'] = $data['prc_id'];
                        $setData['set']['issue_qty'] = 'issue_qty, + '.$value;
                        $this->setValue($setData);
                    }

                    if(!empty($data['prc_id']) && $data['issue_type'] == 2){
                        $this->sop->updatePrcBatch(['id'=>$data['prc_id'],'qty'=> $value , 'update_field'=>'issue_qty']);
                    }
                }
            }

          
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material issue Successfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteIssueRequisition($data) {
        try {
            $this->db->trans_begin();

            $issueData = $this->getIssueRequest(['id' =>$data['id']]);
            if(!empty($issueData)){
                /** Cutting & Reguler PRC */
                if(!empty($data['prc_id']) && $issueData->issue_type == 2){
                    $postData =['prc_id'=>$data['prc_id'],'item_id'=>$issueData->item_id,'production_data'=>1,'stock_data'=>1,'single_row'=>1];
		            $prcData = $this->sop->getPrc(['id'=>$data['prc_id']]);
                    if($prcData->prc_type == 1 && $prcData->cutting_flow == 2){
                        $processArray = explode(",",$prcData->process_ids);
                        $postData['log_process_id'] = $processArray[0];
                    }
                   
                    if($prcData->prc_type == 2){
                        $stockData = $this->cutting->getCuttingBomData(['prc_id'=>$data['prc_id'],'single_row'=>1,'stock_data'=>1,'production_data'=>1]);
                        $stockQty = $stockData->issue_qty - (( $stockData->cutting_cons) + $stockData->return_qty);

                    }else{
                        $stockData = $this->sop->getPrcBomData($postData);
                        $stockQty = $stockData->issue_qty - ((( $stockData->production_qty * $stockData->ppc_qty)/$stockData->output_qty) + $stockData->return_qty);
                    }
                    if($issueData->issue_qty > $stockQty){
                        return ['status' => 0, 'message' => 'You can not delete is record'];
                    }
                }

                /** Die Production */
                if(!empty($data['prc_id']) && $issueData->issue_type == 3){
                    $dieData = $this->dieProduction->getDieProduction(['id'=>$data['prc_id']]);
                    if($dieData->status > 2 ){
                        return ['status' => 0, 'message' => 'You can not delete is record'];
                    }
                    $logData = $this->dieProduction->getDieLogData(['die_id'=>$data['prc_id']]);

                    if(!empty($logData)){
                        return ['status' => 0, 'message' => 'You can not delete is record'];
                    }
                }

                $stockData = $this->remove($this->stockTransation, ['trans_type'=>'SSI', 'main_ref_id'=>$data['id'], 'child_ref_id'=>$issueData->prc_id]);
				
				if(!empty($data['prc_id']) && $data['issue_type'] == 4){
                    $setData = Array();
                    $setData['tableName'] = 'store_request';
                    $setData['where']['id'] = $data['prc_id'];
                    $setData['set']['issue_qty'] = 'issue_qty, - '.$issueData->issue_qty;
                    $this->setValue($setData);
                }

                
			}
            $this->trash($this->issueRegister,['id'=>$data['id']], 'Delete Issue Requisitin');

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Issue suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function saveReturnReq($data) {
        try {
			$this->db->trans_begin();

            $issue_number = (!empty($data['issue_number'])) ? $data['issue_number'] : "" ;
            $location_id = (!empty($data['location_id'])) ? $data['location_id'] : "" ;
            unset($data['issue_number']);unset($data['location_id']);

            $data['id'] = '';
            $result = $this->store($this->materialReturn, $data, 'Return Material');

            if($data['trans_type'] == 1){
                $setData = array();
                $setData['tableName'] = $this->issueRegister;
                $setData['where']['id'] = $data['issue_id'];
                $setData['set']['return_qty'] = 'return_qty, + ' . $data['total_qty'];
                $this->setValue($setData);
            }

            if($data['trans_type'] == 2){

                if($data['usable_qty'] != "" && $data['usable_qty'] != 0)
                {
                    $stockPlusQuery = [
                        'id' => "",
                        'trans_type' => 'SSR',
                        'trans_date' => date("Y-m-d"),
                        'location_id'=> $location_id,
                        'batch_no' => $data['batch_no'],
                        // 'heat_no' => $data['heat_no'],
                        'item_id' => $data['item_id'],
                        'qty' => $data['usable_qty'],
                        'p_or_m' => 1,
                        'main_ref_id' =>  $data['issue_id'],
                        'child_ref_id' => $result['insert_id'],
                        'ref_no' => $issue_number,
                        'created_by' => $data['created_by']
                    ];
                    $issueTrans = $this->store('stock_trans', $stockPlusQuery);
                }

                $setData = array();
                $setData['tableName'] = $this->materialReturn;
                $setData['where']['id'] = $data['ref_id'];
                $setData['set']['insp_qty'] = 'insp_qty, + ' . $data['insp_qty'];
                $this->setValue($setData);
            }
			
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function getManualRejDTRows($data) {
        $data['tableName'] = 'rej_found';
        $data['select'] = "rej_found.*,item_master.item_code,item_master.item_name,location_master.location";
        $data['leftJoin']['item_master'] = "item_master.id = rej_found.item_id";
        $data['leftJoin']['location_master'] = "location_master.id  = rej_found.location_id";
        $data['where']['rej_found.trans_type'] = 1;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(rej_found.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "rej_found.qty";
        $data['searchCol'][] = "location_master.location";
        $data['searchCol'][] = "rej_found.batch_no";
        $data['searchCol'][] = "rej_found.remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function saveRejection($data){        
        try {
            $this->db->trans_begin();
       
            foreach ($data['batch_qty'] as $key => $value) {

                if(!empty($value) && $value > 0) {
                    $rejData = [
                        'id' => '',
                        'trans_type' => 1,
                        'trans_date' => date("Y-m-d"),
                        'item_id' => $data['item_id'],
                        'location_id'=> $data['location_id'][$key],
                        'batch_no' => $data['batch_no'][$key],
                        'qty' => $value,
                        'remark' => $data['remark'],
                        'created_by' => $this->loginId,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $result = $this->store('rej_found', $rejData, 'Rejection');

                    $stockMinusQuery = [
                        'id' => '',
                        'trans_type' => 'MRJ',
                        'trans_date' => date("Y-m-d"),
                        'location_id'=> $data['location_id'][$key],
                        'batch_no' => $data['batch_no'][$key],
                        'item_id' => $data['item_id'],
                        'qty' => $value,
                        'p_or_m' => -1,
                        'main_ref_id' => $result['insert_id'],
                        'created_by' => $this->loginId,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $issueTrans = $this->store('stock_trans', $stockMinusQuery);
                }
            }

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material issue Successfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteRejection($data) {
        try {
            $this->db->trans_begin();
			
            $this->trash('stock_trans', ['trans_type'=>'MRJ', 'main_ref_id'=>$data['id']]);

            $result = $this->trash('rej_found', ['id'=>$data['id']], 'Rejection');

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function getSparPartRequestDTRows($data){
        $data['tableName'] = $this->store_request;
        $data['select'] = "store_request.*,item_master.item_name,item_master.item_code,machine_breakdown.trans_number as ticket_number";
        $data['leftJoin']['item_master'] = "item_master.id  = store_request.item_id";
        $data['leftJoin']['machine_breakdown'] = "machine_breakdown.id  = store_request.ref_id";

        $data['where']['store_request.req_type'] = 2;
        $data['where']['(store_request.req_qty - store_request.issue_qty) >'] = 0;
    
        $data['order_by']['store_request.trans_number'] = 'ASC';
    
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "store_request.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(store_request.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "store_request.req_qty";
        $data['searchCol'][] = "store_request.issue_qty";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
      
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function deleteSparPartRequest($data){ 
        try{
            $this->db->trans_begin();
            
            if($data['issue_qty'] > 0){
                return ['status'=>0,'message'=>'You can not delete this Sparpart Request'];
            }
            $result = $this->trash('store_request', ['id'=>$data['id']], 'Sparpart Request');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    /*** PRC Batch */
    public function getPrcBatchDTRows($data){
        $data['tableName'] = 'prc_batch';
        $data['select'] = "prc_batch.*,item_master.item_name,item_master.item_code,employee_master.emp_name as issue_name,createdBy.emp_name as created_name";
		
        $data['leftJoin']['item_master'] = "item_master.id  = prc_batch.item_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = prc_batch.issue_to";
        $data['leftJoin']['employee_master createdBy'] = "createdBy.id = prc_batch.created_by";

        $data['where']['prc_batch.status'] = $data['status'];
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

    public function getNextPrcBatchNo(){
        $queryData = array(); 
		$queryData['tableName'] = 'prc_batch';
        $queryData['select'] = "ifnull(MAX(trans_no + 1),1) as trans_no";
		$queryData['where']['YEAR(prc_batch.trans_date)'] = date("Y");

		$trans_no = $this->specificRow($queryData)->trans_no;
		return $trans_no;
    }

    public function savePrcBatch($data){           
        try {
            $this->db->trans_begin();
            
            
            $batchdata = [
                'id'=>$data['id'],
                'item_id'=>$data['item_id'],  
                'remark'=>$data['remark'],   
                'issue_to' => $data['issue_to'],
            ];
            if(empty($data['id'])){
                $trans_no = $this->store->getNextPrcBatchNo();
                $trans_number = n2y(date("Y")).n2m(date("m")).str_pad($trans_no, 5, '0', STR_PAD_LEFT);
                $batchdata['trans_no'] = $trans_no;
                $batchdata['trans_number'] = $trans_number;
                $batchdata['trans_date'] = date("Y-m-d");
            }
            $result =$this->store("prc_batch",$batchdata);
            $data['prc_id'] = $result['id'];
            $data['issued_to'] = $data['issue_to'];
            $data['issue_type'] = 2;
            $data['created_by'] = $this->loginId;

            /** StockEffect */
            $issueresult = $this->saveIssueRequisition($data);
            if($issueresult['status'] != 1){
                return ['status'=>0,'message'=>'Something is wrong'];
            }

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material issue Successfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    
    public function deletePrcBatch($data) {
        try {
            $this->db->trans_begin();
            $queryData['tableName'] = 'issue_register';
            $queryData['where']['issue_type'] = 2;
            $queryData['where']['prc_id'] = $data['id'];
            $issueData = $this->rows($queryData);
            
            if(!empty($issueData)){
                foreach($issueData AS $row){
                    $this->trash($this->issueRegister,['id'=> $row->id], 'Delete Issue Requisitin');
                    $stockData = $this->remove($this->stockTransation, ['trans_type'=>'SSI', 'main_ref_id'=>$row->id, 'child_ref_id'=>$row->prc_id]);
                }   
			}
            $result = $this->trash('prc_batch',['id'=> $data['id']]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Deleted suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function changeBatchStatus($data) {
        try {
            $this->db->trans_begin();
            if($data['status'] == 2){
                $data['approved_by'] = $this->loginId;
                $data['approved_at'] = date("Y-m-d H:i:s");
            }elseif($data['status'] == 3){
                $batchData = $this->store->getPrcBatchDetail(['id'=>$data['id'],'single_row'=>1]);
		        $stockQty = $batchData->issue_qty - ($batchData->used_qty + $batchData->return_qty + $batchData->scrap_qty);
                if($stockQty > 0){
                    return ['status'=>0,'message'=>'Batch cannot be completed. Return material first.'];
                }
            }
            $result = $this->store("prc_batch",$data);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getPrcBatchDetail($param){
        $data['tableName'] = 'prc_batch';
        $data['select'] = "prc_batch.*";
		if(!empty($param['itemDetail'])){
            $data['select'] .= ",item_master.item_name,item_master.item_code";
            $data['leftJoin']['item_master'] = "item_master.id  = prc_batch.item_id";
        }
        if(!empty($param['issueToDetail'])){
            $data['select'] .= ",employee_master.emp_name as issue_name";
            $data['leftJoin']['employee_master'] = "employee_master.id = prc_batch.issue_to";
        }
        
        if(!empty($param['id'])){
            $data['where']['prc_batch.id'] = $param['id'];
        }

        if(!empty($param['single_row'])){
            $result = $this->row($data);
        }else{
            $result = $this->rows($data);
        }
        
        return $result;
        
    }
}
?> 