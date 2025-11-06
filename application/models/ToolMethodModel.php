<?php
class ToolMethodModel extends MasterModel{
    public function getDTRows($data){
        $data['tableName'] = 'tool_method';
        $data['select'] = 'tool_method.*';

        $data['select'] .= ',(SELECT GROUP_CONCAT(item_category.category_name ORDER BY item_category.category_name) 
							FROM item_category 
							WHERE FIND_IN_SET(item_category.id, tool_method.die_category) > 0) AS die_names';
        
        
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "tool_method.method_code";
        $data['searchCol'][] = "tool_method.method_name";
        $data['searchCol'][] = "";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = 'tool_method';
        $queryData['where']['method_name'] = $data['method_name'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        return $this->numRows($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['method_name'] = "Method is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];
            else:
                $result = $this->store('tool_method',$data,'Method');
            endif;

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
            /** Check Usage */
            $checkData['columnName'] = ["tool_method"];
            $checkData['value'] = $data['id'];
            $checkUsed = $this->checkUsage($checkData);
            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Method is currently in use. you cannot delete it.'];
            endif;
            $result = $this->trash('tool_method',['id'=>$data['id']],'Method');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getToolMethodList(){
        $data['tableName'] = 'tool_method';
        $data['select'] = 'tool_method.*';
        return $this->rows($data);
    }

    public function getToolMethodData($param){
        $data['tableName'] = 'tool_method';
        $data['select'] = 'tool_method.*';
        if(!empty($param['id'])){
            $data['where']['tool_method.id'] = $param['id'];
        }
        return $this->row($data);
    }
}
?>