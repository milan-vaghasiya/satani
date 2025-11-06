<?php
$prcProcess = '';

if(!empty($prcProcessData))
{ ?>
    <div class="table-responsive">
        <table class="table jpExcelTable" style="margin-bottom:30px !important">
            <thead class="bg-light-peach">
                <tr  class="text-center">
                    <th style=" padding: 8px !important; ">#</th>
                    <th  class="text-left">Process</th>
                    <th >Unaccepted</th>
                    <th >In</th>
                    <th >Ok</th>
                    <th >Rej. Found</th>
                    <th >Rej.</th>
                    <th >Pending Prod.</th>
                    <th>Ready For Next Process</th>
                </tr>
            </thead>
            <tbody>
            <?php
                
                $i=1;$index=0;
                foreach($prcProcessData as $row){
                    $currentProcess = $row->process_name;
                    $inward_qty = (!empty($row->inward_qty)?$row->inward_qty:0);
                    $in_qty = (!empty($row->accepted_qty)?$row->accepted_qty:0);
                    $ok_qty = !empty($row->ok_qty)?$row->ok_qty:0;
                    $rej_found_qty = !empty($row->rej_found)?$row->rej_found:0;
                    $rej_qty = !empty($row->rej_qty)?$row->rej_qty:0;
                    $rw_qty = !empty($row->rw_qty)?$row->rw_qty:0;
                    $pendingReview = $rej_found_qty - $row->review_qty;
                    $pending_production =($in_qty * $row->output_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview);
                    $movement_qty =!empty($row->move_qty)?$row->move_qty:0;
                    $pending_movement = $ok_qty - ($movement_qty);
                    $pending_accept =$inward_qty - $in_qty;

                    $productionQty = $ok_qty + $rej_found_qty;
                    
                    
                    ?>
                    <tr class="text-center">
                        <td><?=$i++?></td>
                        
                        <td class="text-left" style="width:22%"><?=$currentProcess?> <br><small>(Output : <?=$row->output_qty?>)</small> </td>
                        <td style="width:10%"><?=floatval($pending_accept)?> </td>
                        <td style="width:10%"><?=floatval($in_qty)?><?=(!empty($row->ch_qty) && $row->ch_qty > 0)?'<hr style="margin:0px">'.floatval($row->ch_qty).'(Out)':''?></td>
                        <td style="width:10%"><?=floatval($ok_qty)?> </td>
                        <td style="width:10%"><?=floatval($rej_found_qty)?> </td>
                        <td style="width:10%"><?=floatval($rej_qty)?> </td>
                        <td style="width:10%"><?=floatval($pending_production)?> </td>
                        <td style="width:10%"><?=floatval($pending_movement)?> </td>
                    </tr>
                    <?php
                    
                }
            ?>
            </tbody>
        </table>
    </div> <?php
}


echo $prcProcess;

?>