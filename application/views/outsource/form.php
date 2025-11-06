<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Outsource</u></h4>
                    </div>
                    <div class="card-body">
                        <form id="vendorChallanForm" data-res_function="challanResponse">
                            <input type="hidden" name="challan_id" id="challan_id" value="0" />
                            <div class="row">
                                <div class="col-md-2 form-group">
                                    <label for="ch_number">Challan Date</label>
                                    <input type="text" name="ch_number" id="ch_number" class="form-control req" value="<?=$ch_prefix.$ch_no?>" readonly>
                                </div>
								<div class="col-md-2 form-group">
                                    <label for="ch_date">Challan Date</label>
                                    <input type="date" name="ch_date" id="ch_date" class="form-control req" value="<?= date('Y-m-d') ?>">
                                </div>
								<div class="col-md-5 form-group">
                                    <label for="party_id">Vendor</label>
                                    <select name="party_id" id="party_id" class="form-control select2 req">
                                        <option value="">Select Vendor</option>
                                        <?php
                                        if(!empty($vendorList)){
                                            foreach($vendorList as $row){
                                                $selected = (!empty($vendor_id) && $vendor_id== $row->id)?'selected':'';
                                                ?>
                                                <option value="<?=$row->id?>" <?=$selected?>><?=$row->party_name?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="vehicle_no">Vehicle No.</label>
                                    <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" value="<?=(!empty($dataRow->vehicle_no)) ? $dataRow->vehicle_no : '' ?>" />
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="remark">Remark</label>
                                    <input type="text" name="remark" id="remark" class="form-control" value="">
                                </div>
                               
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <div class="error general_error"></div><br>
                                        <table id='outsourceTransTable' class="table table-bordered jpDataTable colSearch">
                                            <thead class="thead-info">
                                                <tr class="text-center">
                                                    <th class="text-center" style="width:5%;">#</th>
                                                    <th class="text-center" style="width:10%;">Batch No.</th>
                                                    <th class="text-center" style="width:10%;">Batch Date</th>
                                                    <th class="text-center" style="width:15%;">Product</th>
                                                    <th class="text-center" style="width:15%;">Process</th>
                                                    <th class="text-center" style="width:5%;">Request Qty.</th>
                                                    <th style="width:10%;">Challan Process</th>
                                                    <th style="width:12%;">Challan Qty.</th>
                                                    <!-- <th style="width:12%;">Price</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (!empty($requestData)) {
                                                    $i=1;
                                                    $masterProcess = array_reduce($processList, function($masterProcess, $process) { 
                                                                                        $masterProcess[$process->id] = $process; 
                                                                                        return $masterProcess; 
                                                                                    }, []);
                                                    foreach ($requestData as $row) {
                                                        $process = explode(",",$row->process_ids);
				                                        $processKey = array_search($row->process_id,$process);
                                                        $processOptions = '';
                                                        foreach($process as $key => $pid):
                                                            if($key >= $processKey):
                                                                $selected = (($processKey == $key)?'selected readonly':'');
                                                                $processOptions .= '<option value="'.$pid.'" '.$selected.'>'.$masterProcess[$pid]->process_name.'</option>';
                                                            endif;
                                                        endforeach;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" id="md_checkbox_<?= $i ?>" name="id[]" class="filled-in chk-col-success challanCheck" data-rowid="<?= $i ?>" value="<?= $row->id ?>"  ><label for="md_checkbox_<?= $i ?>" class="mr-3"></label>
                                                            </td>
                                                            <td><?=$row->prc_number?></td>
                                                            <td><?=formatDate($row->prc_date)?></td>
                                                            <td><?=$row->item_code.' '.$row->item_name?></td>
                                                            <td><?=$row->process_name?></td>
                                                            <td><?=$row->qty?></td>
                                                            <td>
                                                                <select name="process_ids[<?=$row->id ?>][]" id="process_ids<?=$row->id ?>" class="form-control select2 floatOnly text-center p-100 challanInput checkRow<?=$i?>" multiple disabled>
                                                                    <?=$processOptions?>
                                                                </select>
                                                                <div class="error process_ids<?=$row->id?>"></div>
                                                            </td>
                                                            <td>
                                                                <input type="text" id="ch_qty<?=$row->id ?>" name="ch_qty[]" data-req_qty="<?=$row->qty?>" data-rowid="<?= $row->id ?>" class="form-control challanQty floatOnly checkRow<?=$i?>" value="<?=$row->qty?>" disabled>
                                                                <div class="error chQty<?=$row->id?>"></div>
                                                            </td>
                                                            <!-- <td>
                                                                <input type="text" id="price<?=$row->id ?>" name="price[]"  data-rowid="<?= $row->id ?>" class="form-control floatOnly checkRow<?=$i?>" value="<?=$row->price?>" disabled>
                                                            </td> -->
                                                        </tr>
                                                        <?php
                                                        $i++;
                                                    }
                                                } else {
                                                ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center">No data available in table</td>
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
                    </div>
                    <div class="card-footer">
						<div class="row">
							<div class="col-md-12">
                                <?php  $param = "{'formId':'vendorChallanForm','fnsave':'save','res_function':'challanResponse'}"; ?>
								<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="customStore(<?=$param?>);"><i class="fa fa-check"></i> Save</button>
								<a href="<?= base_url('outsource/index') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function() {
        
        $(document).on("click", ".challanCheck", function() {
            var id = $(this).data('rowid');
            $(".error").html("");
            if (this.checked) {
                $(".checkRow" + id).removeAttr('disabled');
            } else {
                $(".checkRow" + id).attr('disabled', 'disabled');
            }
        });

        
        $(document).on("keyup", ".challanQty", function() {
            var id = $(this).data('rowid');
            var req_qty = $(this).data('req_qty');
            var ch_qty = $("#ch_qty" + id).val();
            if (parseFloat(ch_qty) > parseFloat(req_qty)) {
                $("#ch_qty" + id).val('0');
            }
        });

    });

    function challanResponse(data,formId="vendorChallanForm"){ 
        if(data.status==1){
            $('#'+formId)[0].reset();
            window.location.href = base_url +controller;
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});
            }else{
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }
    }

</script>