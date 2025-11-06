
<html>
    <head>
        <title>Die Outsource</title>
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
                        <td>Challan No.</td><?=$dieOutSourceData[0]->ch_number?>
                        <td style="text-align:center; background-color:#D2D8E0;"><b><?=$dieOutSourceData[0]->ch_number?></b></td>
                    </tr>
                    <tr>
                        <td>Challan Date</td><?=formatDate($dieOutSourceData[0]->ch_date)?>
                        <td style="text-align:center; background-color:#D2D8E0;"><b><?=formatDate($dieOutSourceData[0]->ch_date)?></b></td>
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
                        <td colspan ="2"><?= $dieOutSourceData[0]->party_name?></td>
                    </tr>
                    <tr>
                        <td><?=$companyData->company_address?></td>
                        <td colspan ="2"><?= $dieOutSourceData[0]->party_address?></td>
                    </tr>
                    <tr>
                        <td>GSTIN : <?=$companyData->company_gst_no?></td>
                        <td colspan ="2">GSTIN : <?=$dieOutSourceData[0]->gstin?></td>
                    </tr>
                </table>
                <table class="table item-list-bb" style="margin-top:10px;">
                    <thead>
                        <tr style="background-color:#D2D8E0;">
                            <th style="width:40px;">No.</th>
                            <th class="text-left">Item Description</th>
                            <th class="text-left">Process</th>
                            <th class="text-left">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i=1;$totalQty = 0;
                        if(!empty($dieOutSourceData)):
                            foreach($dieOutSourceData as $row):
                                $production =(!empty($row->trans_number))?' | Production No. : '.$row->trans_number:'';
                                ?>
                                <tr>
                                    <td><?=$i++?></td>
                                    <td><?=$row->die_code.' '.$row->die_name.$production?></td>
                                    <td><?=$row->process_name?></td>
                                    <td><?=$row->qty?></td>
                                </tr>
                                <?php
                                $totalQty++;
                            endforeach;
                        endif;

                        $blankLines = (10 - $i);
                        if($blankLines > 0):
                            for($j=1;$j<=$blankLines;$j++):
                                echo '<tr>
                                        <td >&nbsp;</td>
                                        <td ></td>
                                        <td ></td>
                                        <td ></td>
                                        </tr>';
                            endfor;
                        endif;
                        ?>
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
                        
                    </table>
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">Challan No. & Date : <?=$dieOutSourceData[0]->ch_number.' ['.formatDate($dieOutSourceData[0]->ch_date).']'?></td>
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