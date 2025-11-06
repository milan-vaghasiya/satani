<form>
    <input type="hidden" name="prc_id" value="<?=$prcData->id?>">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <th></th>
                    <th>Tool No</th>
                    <th>Tool Name</th>
                    <th>Tool Type</th>
                    <th>Status</th>
                </thead>
                <tbody>
                    <?php
                    if(!empty($dieList)){
                        foreach($dieList AS $row){
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" id="log_id<?=$row->id?>" name="log_id[]" class="filled-in dieCheck chk-col-success dieCheck<?=$row->id?>" value="<?=$row->id?>" data-rowid="<?=$row->id?>">
                                    <label for="log_id<?=$row->id?>" class="mr-3"></label>
                                    <input type="hidden" name="die_reg_id[<?=$row->id?>]" value="<?=$row->die_reg_id?>">
                                </td>
                                <td><?=$row->die_number?></td>
                                <td><?=$row->die_name?></td>
                                <td><?=$row->category_name?></td>
                                <td>
                                    <select name="status[<?=$row->id?>]" id="status" class="form-control">
                                        <option value="1">Release</option>
                                        <option value="3">Reject</option>
                                        <option value="5">Rework</option>
                                        <option value="4">Convertable</option>
                                    </select>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</form>