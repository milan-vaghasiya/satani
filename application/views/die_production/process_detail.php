<form>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th style="min-width:20px">#</th>
                            <th style="min-width:100px">Date</th>
                            <th style="min-width:100px">Process</th>
                            <th>Production Time</th>
                            <th>Machine/ Vendor</th>
                            <th>In Challan No</th>
                            <th>Operator</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($logData)){
                            $i=1;
                            foreach($logData AS $row){
                                ?>
                                <tr>
                                    <td><?=$i++?></td>
                                    <td><?=formatDate($row->trans_date)?></td>
                                    <td><?=$row->process_name?></td>
                                    <td><?=$row->production_time?></td>
                                    <td><?=$row->processor_name?></td>
                                    <td><?=$row->in_challan_no?></td>
                                    <td><?=$row->emp_name?></td>
                                    <td><?=$row->remark?></td>
                                </tr>
                                <?php
                            }
                        }else{
                            ?>
                            <tr>
                                <th colspan="8" class="text-center">No data avalable.</th>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>    
            </div>
        </div>
    </div>
</form>