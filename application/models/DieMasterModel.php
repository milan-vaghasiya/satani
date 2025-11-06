<?php
class DieMasterModel extends MasterModel{

    public function getDTRows($data){
        $data['tableName'] = 'die_master';
        $data['select'] = 'die_master.*,item_category.category_name AS die_name,item_master.item_name,main_cat.category_name';

        $data['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        $data['leftJoin']['item_category main_cat'] = 'main_cat.id = item_category.ref_id';
        // $data['leftJoin']['material_master'] = 'material_master.id = die_master.grade_id';
        $data['leftJoin']['item_master'] = 'item_master.id = die_master.item_id';
        
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "die_master.die_code";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "main_cat.category_name";
        // $data['searchCol'][] = "material_master.material_grade";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }


    public function checkDuplicate($data){
        $queryData['tableName'] = 'die_master';
        $queryData['where']['die_code'] = $data['die_code'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        return $this->numRows($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $toolMethodData = $this->toolMethod->getToolMethodData(['id'=>$data['tool_method']]);
            $die_category = explode(",",$toolMethodData->die_category);
            $itemData = $this->item->getItem(['id'=>$data['item_id']]);
            foreach($die_category AS $key=>$category_id){
                $catData = $this->itemCategory->getCategory(['id'=>$category_id]);
                $dieData = [
                    'id'=>'',
                    'die_code'=>$toolMethodData->method_code.'-'.$catData->category_code.$itemData->item_code,
                    'category_id'=>$category_id,
                    'item_id'=>$data['item_id'],
                    'tool_method'=>$data['tool_method'],
                    // 'grade_id'=>$data['grade_id'],
                    'remark'=>$data['remark'],
                ];
                if($this->checkDuplicate($dieData) > 0):
                    $errorMessage['die_code'] = "Die is duplicate.";
                    return ['status'=>0,'message'=>$errorMessage];
                else:
                    $result = $this->store('die_master',$dieData,'Die');
                endif;
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

    public function getDieData($postData = []){
        $data['tableName'] = 'die_master';
        $data['select'] = 'die_master.*,item_category.category_name AS die_name,material_master.material_grade,item_master.item_name,main_cat.category_name,tool_method.method_code,tool_method.method_name';

        $data['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        $data['leftJoin']['item_category main_cat'] = 'main_cat.id = item_category.ref_id';
        $data['leftJoin']['material_master'] = 'material_master.id = die_master.grade_id';
        $data['leftJoin']['item_master'] = 'item_master.id = die_master.item_id';
        $data['leftJoin']['tool_method'] = 'tool_method.id = die_master.tool_method';

        if(!empty($postData['id'])){ $data['where']['die_master.id'] = $postData['id']; }

        if(!empty($postData['item_id'])){ $data['where']['die_master.item_id'] = $postData['item_id']; }

        if(!empty($postData['tool_method'])){ $data['where']['die_master.tool_method'] = $postData['tool_method']; }

         if(!empty($postData['group_by'])){ $data['group_by'][] = $postData['group_by']; }

        if(!empty($postData['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }

    }

    public function delete($data){
        try{
            $this->db->trans_begin();
             /** Check Usage */
            $checkData['columnName'] = ["die_id"];
            $checkData['value'] = $data['id'];
            $checkUsed = $this->checkUsage($checkData);
            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Die is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash('die_master',['id'=>$data['id']],'Die');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
}
?>