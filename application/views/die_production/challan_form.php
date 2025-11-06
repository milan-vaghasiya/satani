<form id="vendorChallanForm" data-res_function="challanResponse">
    <div class="row">
        <div class="col-md-2 form-group">
            <label for="ch_number">Challan Date</label>
            <input type="text" name="ch_number" id="ch_number" class="form-control req" value="<?=$ch_prefix.str_pad($ch_no,2,0,STR_PAD_LEFT)?>" readonly>
        </div>
        <div class="col-md-2 form-group">
            <label for="ch_date">Challan Date</label>
            <input type="date" name="ch_date" id="ch_date" class="form-control req" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-md-8 form-group">
            <label for="party_id">Vendor</label>
            <select name="party_id" id="party_id" class="form-control select2 req">
                <option value="">Select Vendor</option>
                <?php
                if(!empty($vendorList)){
                    foreach($vendorList as $row){
                        $selected = (!empty($vendor_id) && $vendor_id== $row->id)?'selected':'';
                        ?>
                        <option value="<?=$row->id?>" <?=$selected?> data-mhr="<?=$row->mhr?>"><?=$row->party_name?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="error general_error"></div><br>
                <table id='outsourceTransTable' class="table table-bordered jpDataTable colSearch">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th class="text-center" style="width:5%;">#</th>
                            <th class="text-center" style="width:10%;">Date</th>
                            <th class="text-center" style="width:10%;">Wo No.</th>
                            <!-- <th class="text-center" style="width:15%;">Die Number</th> -->
                            <th class="text-center" style="width:15%;">Die Code</th>
                            <th class="text-center" style="width:15%;">Process</th>
                            <th class="text-center" style="width:15%;">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($dieProdList)) {
                            $i=1;
                            foreach ($dieProdList as $row) {
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" id="md_checkbox_<?= $i ?>" name="dp_id[]" class="filled-in chk-col-success challanCheck" data-rowid="<?= $i ?>" value="<?= $row->id ?>"  checked><label for="md_checkbox_<?= $i ?>" class="mr-3"></label>
                                    </td>
                                    <td><?=formatDate($row->trans_date)?></td>
                                    <td><?=$row->trans_number?></td>
                                    <!-- <td><?=$row->die_number?></td> -->
                                    <td><?=$row->die_code?></td>
                                    <td><?=$row->process_name?></td>
                                    <td><?=$row->qty?></td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="6" class="text-center">No data available in table</td>
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
    });
</script>