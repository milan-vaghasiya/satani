<div class="card">
    <div class="media align-items-center btn-group process-tags">
        <span class="badge bg-light-peach btn flex-fill">Die : <?=$dataRow->die_code?></span>
        <span class="badge bg-light-cream btn flex-fill" >Wo No :  <?=$dataRow->trans_number?></span>
    </div>                                       
</div>
<form>
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="id" value="<?=$id?>">
            <input type="hidden" name="die_id" value="<?=$dataRow->die_id?>">
            <label for="stock_qty">Qty</label>
            <input type="text" name="stock_qty" id="stock_qty" value="<?=($dataRow->ok_qty - $dataRow->stock_qty)?>" class="form-control req numericOnly">
        </div>
    </div>
</form>