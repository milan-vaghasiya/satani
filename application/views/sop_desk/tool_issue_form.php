<form>
    <input type="hidden" name="prc_id" value="<?=$prcData->id?>">
    <div class="error general_error"></div>
    <div class="row">
        <?php
        $dieStockArray= [];
        if(!empty($dieStock)){
            $dieStockArray = array_reduce($dieStock, function($dieStockArray, $die) { 
					$dieStockArray[$die->die_id][] = $die; 
					return $dieStockArray; 
				}, []);
        }
        $issueArray= [];
        if(!empty($dieStock)){
            $issueArray = array_reduce($issueToolList, function($issueArray, $die) { 
					$issueArray[$die->die_id][] = $die; 
					return $issueArray; 
				}, []);
        }
        if(!empty($dieList)){
            $i=1;
            foreach($dieList AS $row){
                if(empty($issueArray[$row->id])){
                ?>
                <div class="col-md-12">
                    <div class="error die_<?=$row->id?>"></div>
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:10%"><?=$i?></th>
                                <th style="width:30%"><?=$row->die_code?></th>
                                <th style="width:30%"><?=$row->die_name?></th>
                                <th style="width:30%"><?=$row->category_name?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <input type="hidden" name="die_id[]" value="<?=$row->id?>">
                            <?php
                            if(!empty($dieStockArray[$row->id])){
                                foreach($dieStockArray[$row->id] As $die){
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" id="die_reg_id<?=$die->id?>" name="die_reg_id[<?=$row->id?>]" class="filled-in dieCheck chk-col-success dieCheck<?=$row->id?>" value="<?=$die->id?>" data-rowid="<?=$row->id?>">
                                        <label for="die_reg_id<?=$die->id?>" class="mr-3"></label>
                                    </td>
                                    <td colspan="3"><?=$die->die_number?></td>
                                </tr>
                                <?php
                                }
                            }else{
                            ?>
                                <tr>
                                    <td class="text-center" colspan="4">No die available</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                $i++;
                }
            }
        }
        ?>
    </div>
</form>
<script>
    $(document).ready(function() {
        
        $(document).on("click", ".dieCheck", function() {
            var rowid = $(this).data('rowid');
            $('.dieCheck'+rowid).not(this).prop('checked', false);
        });

    });

   
</script>