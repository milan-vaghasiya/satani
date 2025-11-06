<form>
<div class="col-md-12 form-group">
    
    <label for="item_id">Item</label>
    <select name="item_id" id="item_id" class="form-control select2">
        <option value="">Select Item</option>
        <?php
        if(!empty($itemList)):
            foreach($itemList as $row):
                ?><option value="<?=$row->id?>"> <?=((!empty($row->item_code))?'['.$row->item_code.'] ':'').$row->item_name?></option><?php
            endforeach;
        endif;
        ?>
    </select>
</div>
<div class="col-md-12  form-group">
    <div class="table-responsive">
        <table class="table jpExcelTable">
            <thead class="thead-dark" id="theadData">
				<tr role="row">
				    <th >#</th>
				    <th  >Location</th>
				    <th  >Heat No.</th>
				    <th  >Batch No.</th>
				    <th >Qty</th>
				    <th  >Tag</th>
			    </tr>
			</thead>
			<tbody id="stockTbody">
			    
			</tbody>
        </table>
    </div>
</div>    
</form>

<script>
    $(document).ready(function(){
         $(document).on('change','#item_id',function(){
    		var item_id = $(this).val();
            if(item_id)
            {		
                $.ajax({
                    url:base_url + controller + "/getBatchStockHistory",
                    type:'post',
                    data:{item_id:item_id}, 
                    dataType:'json',
                    success:function(data){
                        $("#stockTbody").html("");
                        $("#stockTbody").html(data.tbody);
                    }
                });
            }
        });
    });
</script>