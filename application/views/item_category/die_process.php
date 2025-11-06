<form>
    <input type="hidden" name="category_id" value="<?=$category_id?>">
    <div class="row">
        <div class="error general_error"></div>
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th>#</th>
                        <th>Process</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(!empty($processList)){
                        $dieProcess = array_reduce($dieProcessList, function($dieProcess, $process) { $dieProcess[$process->process_id] = $process; return $dieProcess; }, []);
                        foreach($processList AS $row){
                            $trans_id = ((!empty($dieProcess[$row->id]))?$dieProcess[$row->id]->id:"");
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" id="process_id_<?= $row->id ?>" name="process_id[]" class="filled-in chk-col-success checkProcess" data-rowid="<?=$row->id ?>" value="<?= $row->id ?>"  <?=(!empty($trans_id))?'checked':''?>><label for="process_id_<?= $row->id ?>" class="mr-3"></label>
                                    <input type="hidden" name="id[]" class="checkRow<?=$row->id?>" value="<?=$trans_id?>" <?=(empty($trans_id))?'disabled':''?>>
                                </td>
                                <td><?=$row->process_name?></td>
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
<script>
    $(document).ready(function() {
        $(document).on("click", ".checkProcess", function() {
            var id = $(this).data('rowid');
            $(".error").html("");
            if (this.checked) {
                $(".checkRow" + id).removeAttr('disabled');
            } else {
                $(".checkRow" + id).attr('disabled', 'disabled');
            }
        });
    });
</script>