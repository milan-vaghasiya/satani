<style>table, .table{width:100% !important;border-collapse:collapse !important;}td,th{border-collapse:collapse !important;}</style>

<div class="row">
	<div class="col-12">
		<table>
			<tr>
				<td><img src="<?=$letter_head?>" class="img"></td>
			</tr>
		</table>
		<table class="table" style="border-bottom:1px solid #000000;">
			<tr>
			    <td style="width:20%;"></td>
				<td class="text-uppercase text-center" style="font-size:1.3rem;font-weight:bold;width:40%;">PROCESS ROUTE CARD</td>
				<td class="text-uppercase text-right" style="font-size:1.3rem;font-weight:bold;width:20%;"></td>
			</tr>
		</table>
		<table class="table item-list-bb">
			<tr class="text-left">
				<th style="width:15%" class="bg-light">PRC No.</th>
				<td style="width:25%"><?= $prcData->prc_number ?></td>
				<th style="width:15%" class="bg-light">PRC Quantity</th>
				<td style="width:15%"><?= floatval($prcData->prc_qty) ?> NOS</td>
				<th style="width:15%" class="bg-light">PRC Date</th>
				<td style="width:15%"><?= formatDate($prcData->prc_date) ?></td>
			</tr>
		    <tr class="text-left">
				<th class="bg-light">Product Name</th>
				<td colspan="3"><?= '['.$prcData->item_code .'] '.$prcData->item_name ?></td>
				<th class="bg-light">Drw/Rev No</th>
				<td><?=$prcData->drg_no.' / '.(!empty($prcData->rev_no)?$prcData->rev_no:'00') ?></td>
			</tr>
			
			<tr class="text-left">
				<th class="bg-light">Created By</th>
				<td><?= $prcData->emp_name ?></td>
                <th class="bg-light">Remark</th>
				<td colspan="3"><?= $prcData->remark ?></td>
			</tr>
           
		</table>
		<h4 class="row-title" style="margin-top:10px">Material Detail:</h4>
		<table class="table item-list-bb">
			<tr class="thead-gray">
				<th>Item Description</th>
				<th style="width:15%;">Batch No.</th>
			</tr>
			<?php
			if (!empty($prcMaterialData)) :
                $i = 1;
                foreach ($prcMaterialData as $row) :
                    echo '<tr>';
                    echo '<td>' . $row->item_code.' '.$row->item_name . '</td>';
                    echo '<td class="text-center">' . $row->trans_number. '</td>';
                    echo '</tr>';
                endforeach;
            else :
                echo '<tr><th class="text-center" colspan="2">Record Not Found !</th></tr>';
            endif;
			
			?>
		</table>
		<h4 class="row-title" style="margin-top:10px">Process Detail:</h4>
		<table class="table item-list-bb">
			<tr class="text-center thead-gray">
				<th style="width:5%;">No.</th>
				<th class="text-left">Process Detail</th>
				<th style="width:12%;">Inward Qty</th>
				<th style="width:12%;">Production Qty</th>
				<th style="width:12%;">Rej. Found</th>
				<th style="width:12%;">RW. Done</th>
				<th style="width:12%;">OK Qty</th>
				<th style="width:12%;">Pending Qty</th>
			</tr>
			<?php
			if (!empty($prcProcessData)) :
				$i = 1;
                if($prcData->status > 1):
                    foreach ($prcProcessData as $row) :
                        $in_qty = (!empty($row->inward_qty)?$row->inward_qty:0);
                        $ok_qty = (!empty($row->ok_qty)?$row->ok_qty:0);
                        $rej_found_qty = (!empty($row->rej_found)?$row->rej_found:0);
                        $rej_qty = (!empty($row->rej_qty)?$row->rej_qty:0);
                        $rw_qty = (!empty($row->rw_qty)?$row->rw_qty:0);
                        $rw_ok_qty = (!empty($row->rw_ok_qty)?$row->rw_ok_qty:0);
                        $pendingReview = $rej_found_qty - $row->review_qty;
                        $pending_production =($in_qty * $row->output_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview);

                        echo '<tr>';
                        echo '<td class="text-center">' . $i++ . '</td>';
                        echo '<td class="text-left">' . $row->process_name . '</td>';
                        echo '<td class="text-center">' . floatVal($in_qty) . '</td>';
                        echo '<td class="text-center">' . floatVal($ok_qty) . '</td>';
                        echo '<td class="text-center">' . floatVal($rej_found_qty) . '</td>';
                        echo '<td class="text-center">' . floatVal($rw_ok_qty) . '</td>';
                        echo '<td class="text-center">' . floatVal(($ok_qty-$rw_ok_qty)) . '</td>';
                        echo '<td class="text-center">' . floatVal($pending_production) . '</td>';
                        echo '</tr>';
                    endforeach;
                else:
                    foreach($prcProcessData as $key=>$row){
                        echo '<tr>';
							echo '<td class="text-center">' . $i++ . '</td>';
							echo '<td class="text-left">' . $row->process_name . '</td>';
							echo '<td class="text-center">0</td>';
							echo '<td class="text-center">0</td>';
							echo '<td class="text-center">0</td>';
							echo '<td class="text-center">0</td>';
                        echo '</tr>';
                    }
                endif;
			else :
				echo '<tr><th class="text-center" colspan="6">Record Not Found !</th></tr>';
			endif;
			?>
		</table>
		<?php
		$prcLogs = array_reduce($logData, function($prcLogs, $log) { $prcLogs[$log->process_name][] = $log; return $prcLogs; }, []);
		foreach ($prcLogs as $process_name=>$logs):
			?>
			<hr>
			<table class="table item-list-bb">
				<thead>
					<tr class="bg-light">
						<th colspan="10" class="text-left"><?=$process_name?> : </th>
					</tr>
					<tr style="background:#f9fafb">
						<th style="width:5%">#</th>
						<th style="width:10%">Date</th>
						<th style="width:10%">Challan No</th>
						<th style="width:10%">Operator</th>
						<th style="width:10%">Machine</th>
						<th style="width:10%">Ok</th>
						<th style="width:10%">Rej</th>
						<th style="width:10%">Rw</th>
						<th style="width:10%">Production Time</th>
						<th style="width:15%">Remark</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					foreach($logs AS $row):
						?>
						<tr class="text-center">
							<td><?=$i++?></td>
							<td><?=formatDate($row->trans_date)?></td>
							<td><?=$row->in_challan_no?></td>
							<td><?=(($row->process_by == 3)? $row->processor_name : $row->emp_name)?></td>
							<td><?=(($row->process_by != 3)? (!empty($row->machine_code) ? $row->machine_code : $row->machine_name) : '' )?></td>
							<td><?=number_format($row->qty)?></td>
							<td><?=number_format($row->rej_found)?></td>
							<td><?=number_format($row->rw_qty)?></td>
							<td><?=$row->production_time?></td>
							<td><?=$row->remark?></td>
						</tr>
						<?php
					endforeach;
					?>
				</tbody>
			</table>
			<?php
		endforeach;
		?>
		
		
	</div>
</div>