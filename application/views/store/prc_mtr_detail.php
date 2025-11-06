<form>
    <div class="row">   
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>#</th>
                            <th>Batch No</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($batchDetail)){
                            $i=1;
                            foreach($batchDetail As $row){
                                ?>
                                <tr>
                                    <td><?=$i++?></td>
                                    <td><?=$row->batch_no?></td>
                                    <td><?=$row->issue_qty?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>