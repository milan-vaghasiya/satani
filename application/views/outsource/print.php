
<html>
    <head>
        <title>Out Source</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
                <table>
                    <tr>
                        <td>
                            <img src="<?=$letter_head?>" class="img">
                        </td>
                    </tr>
                </table>
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr>
                        <td rowspan="2" style="text-align:center;width:50%;"><h1>Job Work Challan </h1></td>
                        <td>Challan No.</td><?=$outSourceData->ch_number?>
                        <td style="text-align:center; background-color:#D2D8E0;"><b><?=$outSourceData->ch_number?></b></td>
                    </tr>
                    <tr>
                        <td>Challan Date</td><?=formatDate($outSourceData->ch_date)?>
                        <td style="text-align:center; background-color:#D2D8E0;"><b><?=formatDate($outSourceData->ch_date)?></b></td>
                    </tr>
                    <tr>
				        <td class="text-left">
                            <b>Ship From </b>
                        </td>
                        <td colspan ="2" class="text-left">
                            <b>Ship To </b>
                        </td>
                    </tr>
                    <tr style="background-color:#D2D8E0;">
                        <td><?=$companyData->company_name?></td>
                        <td colspan ="2"><?= $outSourceData->party_name?></td>
                    </tr>
                    <tr>
                        <td><?=$companyData->company_address?></td>
                        <td colspan ="2"><?= $outSourceData->party_address?></td>
                    </tr>
                    <tr>
                        <td>GSTIN : <?=$companyData->company_gst_no?></td>
                        <td colspan ="2">GSTIN : <?=$outSourceData->gstin?></td>
                    </tr>
                </table>
                <table class="table item-list-bb" style="margin-top:10px;">
                    <tr style="background-color:#D2D8E0;">
                        <th style="width:40px;">No.</th>
                        <th class="text-left">Item Description</th>
                        <th style="width:80px;">Qty</th>
                        <th style="width:80px;">Price</th>
                        <th style="width:80px;">Amount</th>
                    </tr>
                    <tbody>
                        <?php
                            $i=1;$totalQty = 0;$totalAmt = 0;
                            if(!empty($reqData)):
                                foreach($reqData as $row):
                                    $process = (!empty($row->process_name)) ? '<br>Process Name : '.$row->process_name : '';
                                    $prc_no =(!empty($row->prc_number))?' | Batch No. : '.$row->prc_number:'';
                                    echo '<tr>';
                                        echo '<td class="text-center">'.$i++.'</td>';
                                        echo '<td>'.$row->item_name.$process. $prc_no.'</td>';
                                        echo '<td class="text-right">'.floatval($row->qty).'</td>';
                                        echo '<td class="text-right">'.$row->price.'</td>';
                                        echo '<td class="text-right">'.sprintf('%0.2f',$row->qty * $row->price).'</td>';
                                    echo '</tr>';
                                    $totalQty += $row->qty;
                                    $totalAmt += $row->qty * $row->price;
                                endforeach;
                            endif;

                            $blankLines = (10 - $i);
                            if($blankLines > 0):
                                for($j=1;$j<=$blankLines;$j++):
                                    echo '<tr>
                                        <td style="border-top:none;border-bottom:none;">&nbsp;</td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                    </tr>';
                                endfor;
                            endif;
                        ?>
                        <tr>
                            <th colspan="2" class="text-right">Total.</th>
                            <th class="text-right"><?=floatval($totalQty)?></th>
                            <th></th>
                            <th class="text-right"><?=sprintf('%.2f',$totalAmt)?></th>
                        </tr>
                    </tbody>
                </table>
                <htmlpagefooter name="lastpage">
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                        <tr>
                            <td style="width:50%;"></td>
                            <td style="width:20%;"></td>
                            <th class="text-center">For, <?=$companyData->company_name?></th>
                        </tr>
                        <tr>
                            <td colspan="3" height="50"></td>
                        </tr>
                        <tr>
                            <td><br>This is a computer-generated order.</td>
                            <td class="text-center"><?=$outSourceData->emp_name?><br>Prepared By</td>
                            <td class="text-center"><br>Authorised By</td>
                        </tr>
                    </table>
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">Challan No. & Date : <?=$outSourceData->ch_number.' ['.formatDate($outSourceData->ch_date).']'?></td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>
                </htmlpagefooter>
				<sethtmlpagefooter name="lastpage" value="on" />                
            </div>
        </div>        
    </body>
</html>