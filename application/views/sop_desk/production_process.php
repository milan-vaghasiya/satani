<?php $this->load->view('includes/header'); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
			<div class="col-sm-12 mt-3">
				<div class="float-end" style="width:30%;">
					<div class="input-group" id="qs">                               
						<input type="text" id="quick_search" class="form-control qs quicksearch form-control-sm" style="width:80%;" placeholder="Search">
						<button type="button" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
					</div>
				</div>
			</div>
		</div>
        <div class="card-body1 row grid" data-isotope='{ "itemSelector": ".grid_item" }'>
            <?php
            if(!empty($processList)){
                foreach($processList as $row){
                    $in_qty = (!empty($row->inward_qty)?$row->inward_qty:0);
                    $ok_qty = !empty($row->ok_qty)?$row->ok_qty:0;
                    $rej_found_qty = !empty($row->rej_found)?$row->rej_found:0;
                    $rej_qty = !empty($row->rej_qty)?$row->rej_qty:0;
                    $rw_qty = !empty($row->rw_qty)?$row->rw_qty:0;
                    $pendingReview = $rej_found_qty - $row->review_qty;
                    $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview);
                    $movement_qty =!empty($row->movement_qty)?$row->movement_qty:0;
                    $pending_movement = $ok_qty - ($movement_qty);
                    ?>
                    <div class="col-md-6 col-lg-3 grid_item">
                        <div class="card report-card sh-perfect">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col">
                                        <div class="bot_style">
                                            <h2 class="heading"><?=$row->process_name?></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="stage-footer">
                                <div class="col-md-3 text-center badge bg-light-sky flex-fill br-0">
                                    <h2 class="font-light fs-18 m-0"><?=floatval($pendingReview)?></h2>
                                    <h4 class="text-uppercase fs-15 m-0">Pending QC</h4>
                                </div>
                                <div class="col-md-3 text-center badge bg-light-raspberry flex-fill br-0">
                                    <h2 class="font-light fs-18 m-0"><?=(($row->id != 1)?floatval($pending_production):"0")?></h2>
                                    <h4 class="text-uppercase fs-15 m-0">Pending</h4>
                                </div>
                                <div class="col-md-3 text-center badge bg-light-cream flex-fill br-0">
                                    <h2 class="font-light fs-18 m-0"><?=($row->id != 1)?floatval($pending_movement):'0'?></h2>
                                    <h4 class="text-uppercase fs-15 m-0">Stock</h4>
                                </div>
                            </div>
                            <div class="stage-footer">
								<a role="button" href="<?=base_url($headData->controller.'/productionLog/'.$row->id)?>" target="_blank" class="stage-btn mfg-btn m-0 p-3" datatip="Manufacturing" flow="down" >
								    <i class="fas fa-cogs fs-13"></i> <span class="lable">Manufacturing</span>
								</a>
								<a href="<?= base_url($this->data['headData']->controller.'/productionLogsDetail/'.$row->id);?>"  target="_blank" class="stage-btn stk-btn m-0 p-3" datatip="Stock" flow="down">
									<i class="fas fa-eye fs-13"></i> <span class="lable">Logs</span>
								</a>
								<!-- <a href="<?=base_url($headData->controller.'/mfgStore/'.$row->id)?>" target="_blank" class="stage-btn stk-btn m-0 p-3" datatip="Stock" flow="down">
								    <i class="fas fa-eye fs-13"></i> <span class="lable">View Stock</span>
								</a> -->
							</div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/plugins/isotop/isotope.pkgd.min.js"></script>
<script>
var buttonFilter;
var qsRegex;
var isoOptions ={};
var $grid = '';
$(document).ready(function(){
	isoOptions = {
		itemSelector: '.grid_item',
		percentPosition: true,
		layoutMode: 'fitRows',
		filter: function() {
			var $this = $(this);
			var searchResult = qsRegex ? $this.text().match( qsRegex ) : true;
			var buttonResult = buttonFilter ? $this.is( buttonFilter ) : true;
			return searchResult && buttonResult;
		}
	};
	
	// init isotope
	$grid  = $('.grid').isotope( isoOptions );
	var $qs = $('.qs').keyup( debounce(function() {qsRegex = new RegExp( $qs.val(), 'gi' );$grid.isotope();}, 200 ) );
	
	// bind filter button click
	$('#buttonFilter').on( 'click', 'button', function() {
		var filterValue = $( this ).attr('data-filter');
		buttonFilter = filterValue;
		$grid.isotope();
	});
	
});

function debounce(fn,threshold ) {
	var timeout;
	threshold = threshold || 100;
	return function debounced() {
		clearTimeout( timeout );
		var args = arguments;
		var _this = this;
		function delayed() {fn.apply( _this, args );}
		timeout = setTimeout( delayed, threshold );
	};
}
</script>