<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getProductionDtHeader($page){

    /* Process Header */
    $data['process'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['process'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['process'][] = ["name"=>"Process Name"];
    $data['process'][] = ["name"=>"Remark"];

    /* Rejection Header */
    $data['rejectionComments'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['rejectionComments'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['rejectionComments'][] = ["name"=>"Code"];
    $data['rejectionComments'][] = ["name"=>"Reason"];

    /* Estimation & Design Header */
    $data['estimation'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['estimation'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
	$data['estimation'][] = ["name"=>"Job No."];
	$data['estimation'][] = ["name"=>"Job Date"];
	$data['estimation'][] = ["name"=>"Customer Name"];
	$data['estimation'][] = ["name"=>"Item Name"];
    $data['estimation'][] = ["name"=>"Order Qty"];
    $data['estimation'][] = ["name"=>"Bom Status"];
    $data['estimation'][] = ["name"=>"Priority"];
    $data['estimation'][] = ["name"=>"FAB. PRODUCTION NOTE"];
    $data['estimation'][] = ["name"=>"POWER COATING NOTE"];
    $data['estimation'][] = ["name"=>"ASSEMBALY NOTE"];
    $data['estimation'][] = ["name"=>"GENERAL NOTE"];

   

    /* Product Option Header */
    $data['productOption'][] = ["name"=>"#","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center", "srnoPosition" => 0];
    $data['productOption'][] = ["name"=>"Part Code"];
    $data['productOption'][] = ["name"=>"Part Name"];
    $data['productOption'][] = ["name"=>"BOM","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Process","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Cycle Time","textAlign"=>"center"]; 
    $data['productOption'][] = ["name"=>"Action","textAlign"=>"center"];

    /* Pending Rejection Review */
    $data['pendingReview'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['pendingReview'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"PRC No.","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Product","textAlign"=>"left"];
    $data['pendingReview'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Process","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Machine/Vendor","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Operator/Inspector","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Pending Qty","textAlign"=>"center"];


    /* Pending Rejection Review */
    $data['rejectionReview'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['rejectionReview'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['rejectionReview'][] = ["name"=>"PRC No.","textAlign"=>"center"];
    $data['rejectionReview'][] = ["name"=>"Product","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Decision Date","textAlign"=>"center"];
    $data['rejectionReview'][] = ["name"=>"Decision","textAlign"=>"center"];
    $data['rejectionReview'][] = ["name"=>"Rej/Rw Found","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Rej/Rw Belongs To","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];
    $data['rejectionReview'][] = ["name"=>"Rej/Rw By","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Machine","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Operator","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Note","textAlign"=>"left"];

    /* SOP Header */
    $data['productionShortage'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['productionShortage'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"Customer","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"SO Number","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"Qty.","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"Total Qty.","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"WIP Qty"];
    $data['productionShortage'][] = ["name"=>"Production Finished"];
    $data['productionShortage'][] = ["name"=>"RTD Qty"];
    $data['productionShortage'][] = ["name"=>"Shortage Qty"];

  
    /* GRN Pending Rejection Review */
    $data['grnPendingReview'][] = ["name" => "Action", "textAlign" => "center","class"=>"no_filter noExport","sortable"=>FALSE];
    $data['grnPendingReview'][] = ["name"=>"#","textAlign"=>"center","class"=>"no_filter","sortable"=>FALSE];
    $data['grnPendingReview'][] = ["name"=>"GRN No.","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"GRN Date","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"Party","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"Pending Qty","textAlign"=>"center"];

    /* GRN Reviewed Rejection */
    $data['grnRejectionReview'][] = ["name" => "Action", "textAlign" => "center","class"=>"no_filter noExport","sortable"=>FALSE];
    $data['grnRejectionReview'][] = ["name"=>"#","textAlign"=>"center","class"=>"no_filter","sortable"=>FALSE];
    $data['grnRejectionReview'][] = ["name"=>"Source","textAlign"=>"center"];
    $data['grnRejectionReview'][] = ["name"=>"GRN No.","textAlign"=>"center"];
    $data['grnRejectionReview'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['grnRejectionReview'][] = ["name"=>"Decision Date","textAlign"=>"center"];
    $data['grnRejectionReview'][] = ["name"=>"Decision","textAlign"=>"center"];
    $data['grnRejectionReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];

    /* Manual Pending Rejection */
    $data['manualPendingReview'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['manualPendingReview'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['manualPendingReview'][] = ["name"=>"Rej. Date","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Location","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Batch No.","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Pending Qty","textAlign"=>"center"];

    /* Manual Reviewed Rejection */
    $data['manualRejectionReview'][] = ["name" => "Action", "textAlign" => "center","class"=>"no_filter noExport","sortable"=>FALSE];
    $data['manualRejectionReview'][] = ["name"=>"#","textAlign"=>"center","class"=>"no_filter","sortable"=>FALSE];
    $data['manualRejectionReview'][] = ["name"=>"Source","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Location","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Batch No.","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Decision Date","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Decision","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];

	/* Jobwork Order Header */
    $data['jobworkOrder'][] = ["name"=>"Action","class"=>"text-center no_filter noExport"];
    $data['jobworkOrder'][] = ["name"=>"#","class"=>"text-center no_filter noExport"];
    $data['jobworkOrder'][] = ["name"=>"Order Date"];
    $data['jobworkOrder'][] = ["name"=>"Order No."];
    $data['jobworkOrder'][] = ["name"=>"Vendor Name"];
    $data['jobworkOrder'][] = ["name"=>"Product"];
    $data['jobworkOrder'][] = ["name"=>"Process"];
    $data['jobworkOrder'][] = ["name"=>"Rate/Unit"];
    $data['jobworkOrder'][] = ["name"=>"Rate"];
    $data['jobworkOrder'][] = ["name"=>"Remark"];

    /* Jobwork Bill Header */
    $data['jwbill'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['jwbill'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['jwbill'][] = ["name"=>"In Ch. Date"];
    $data['jwbill'][] = ["name"=>"In Ch. No"];
    $data['jwbill'][] = ["name"=>"Challan No"];
    $data['jwbill'][] = ["name"=>"Vendor"];


    /* Die Production Header */
    $data['dieProduction'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"WO Date","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"WO No","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"WO Type","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Tool Code","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Tool Name","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Work Order Qty","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Complete Qty","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Raw Material","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Issued Material","textAlign"=>"center"];
    
    /* Die Outsource Header */
    $chCheckBox = '<input type="checkbox" id="masterChSelect" class="filled-in chk-col-success BulkChallan" value=""><label for="masterChSelect">ALL</label>';
    $data['dieOutsource'][] = ["name" => $chCheckBox,"class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['dieOutsource'][] = ["name"=>"Wo No","textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"Wo Date","textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"Tool Code","textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"Tool Name","textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"Process","textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"Qty","textAlign"=>"center"];

    /* Die Outsource Challan Header */
    $data['dieOutsourceChallan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['dieOutsourceChallan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['dieOutsourceChallan'][] = ["name"=>"Challan No"];
	$data['dieOutsourceChallan'][] = ["name"=>"Challan Date"];
	$data['dieOutsourceChallan'][] = ["name"=>"Party"];
	$data['dieOutsourceChallan'][] = ["name"=>"Wo No","textAlign"=>"center"];
    $data['dieOutsourceChallan'][] = ["name"=>"Wo Date","textAlign"=>"center"];
    $data['dieOutsourceChallan'][] = ["name"=>"Tool Code","textAlign"=>"center"];
    $data['dieOutsourceChallan'][] = ["name"=>"Tool Name","textAlign"=>"center"];
    $data['dieOutsourceChallan'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['dieOutsourceChallan'][] = ["name"=>"Process","textAlign"=>"center"];
    $data['dieOutsourceChallan'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['dieOutsourceChallan'][] = ["name"=>"Receive Qty","textAlign"=>"center"];

    /* Die Production Header */
    $data['dieRegister'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieRegister'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieRegister'][] = ["name"=>"Register Date","textAlign"=>"center"];
    $data['dieRegister'][] = ["name"=>"Tool No","textAlign"=>"center"];
    $data['dieRegister'][] = ["name"=>"Tool Name","textAlign"=>"center"];
    $data['dieRegister'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['dieRegister'][] = ["name"=>"Tool Type","textAlign"=>"center"];
    $data['dieRegister'][] = ["name"=>"Material Grade","textAlign"=>"center"];
    $data['dieRegister'][] = ["name"=>"PRC No.","textAlign"=>"center"];

    /* Die Production Header */
    $data['freshDie'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['freshDie'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['freshDie'][] = ["name"=>"Register Date","textAlign"=>"center"];
    $data['freshDie'][] = ["name"=>"Tool WO","textAlign"=>"center"];
    $data['freshDie'][] = ["name"=>"Tool Code","textAlign"=>"center"];
    $data['freshDie'][] = ["name"=>"Tool Name","textAlign"=>"center"];
    $data['freshDie'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['freshDie'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['freshDie'][] = ["name"=>"Tool Type","textAlign"=>"center"];
    $data['freshDie'][] = ["name"=>"Material Grade","textAlign"=>"center"];

    /*** Production */
    $data['prc'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['prc'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['prc'][] = ["name"=>"PRC No."];
    $data['prc'][] = ["name"=>"PRC Date"];
    $data['prc'][] = ["name"=>"Product"];
    $data['prc'][] = ["name"=>"Qty.","textAlign"=>"center"];
    $data['prc'][] = ["name"=>"Target Date."];
    $data['prc'][] = ["name"=>"Remark"];

    /** PRC Inward */
    $data['prcInward'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['prcInward'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['prcInward'][] = ["name"=>"Movement Type","textAlign"=>"center"];
    $data['prcInward'][] = ["name"=>"PRC No","textAlign"=>"center"];
    $data['prcInward'][] = ["name"=>"PRC Date","textAlign"=>"center"];
    $data['prcInward'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['prcInward'][] = ["name"=>"Completed Process","textAlign"=>"center"];
    $data['prcInward'][] = ["name"=>"Unaccepted","textAlign"=>"center"];
    $data['prcInward'][] = ["name"=>"In","textAlign"=>"center"];

    /** PRC Log */
    $data['prcLog'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['prcLog'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"Log Type","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"PRC No","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"PRC Date","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"Completed Process","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"In","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"Ok","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"Rej. Found"];
    $data['prcLog'][] = ["name"=>"Rej."];
    $data['prcLog'][] = ["name"=>"Pending Prod."];

    /** PRC Movement */
    $data['prcMovement'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['prcMovement'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['prcMovement'][] = ["name"=>"Type","textAlign"=>"center"];
    $data['prcMovement'][] = ["name"=>"PRC No","textAlign"=>"center"];
    $data['prcMovement'][] = ["name"=>"PRC Date","textAlign"=>"center"];
    $data['prcMovement'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['prcMovement'][] = ["name"=>"Completed Process","textAlign"=>"center"];
    $data['prcMovement'][] = ["name"=>"Stock","textAlign"=>"center"];
    $data['prcMovement'][] = ["name"=>"Movement Qty","textAlign"=>"center"];
    $data['prcMovement'][] = ["name"=>"Pending Movement"];

    /* Vendor Price Report */
    $data['vendorPrice'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['vendorPrice'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['vendorPrice'][] = ["name"=>"Date"];
    $data['vendorPrice'][] = ["name"=>"Vendor"];
    $data['vendorPrice'][] = ["name"=>"Trans Number"];
    $data['vendorPrice'][] = ["name"=>"Product"];
    $data['vendorPrice'][] = ["name"=>"Process"];
    $data['vendorPrice'][] = ["name"=>"Rate"];
    $data['vendorPrice'][] = ["name"=>"Rate Per Unit"];
    $data['vendorPrice'][] = ["name"=>"Apprve By"];

    /** Outsource */
    $data['outsource'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center", "srnoPosition" => ''];
    $data['outsource'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Challan Date", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Challan No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Batch No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Vendor"];
    $data['outsource'][] = ["name" => "Product"];
    $data['outsource'][] = ["name" => "Process"];
    $data['outsource'][] = ["name" => "Challan Qty", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Received Qty", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Without Process Return", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Pending Qty", "textAlign" => "center"];

    /* Tool Method Header */
    $data['toolMethod'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['toolMethod'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['toolMethod'][] = ["name"=>"Code","textAlign"=>"center"];
    $data['toolMethod'][] = ["name"=>"Tool Method","textAlign"=>"center"];
    $data['toolMethod'][] = ["name"=>"Die","textAlign"=>"center"];
   
    $data['prcBatch'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['prcBatch'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['prcBatch'][] = ["name" => "Issue Date"];
    $data['prcBatch'][] = ["name" => "Batch No"];
    $data['prcBatch'][] = ["name" => "Item Name"];
    $data['prcBatch'][] = ["name" => "Issue Qty"];
    $data['prcBatch'][] = ["name" => "Issue To"];
    $data['prcBatch'][] = ["name" => "Created By/At"];
	$data['prcBatch'][] = ["name" => "Remark"];
	
    /* Accepted Log Header Data */
    $data['acceptedLog'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['acceptedLog'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['acceptedLog'][] = ["name"=>"PRC No","textAlign"=>"center"];
    $data['acceptedLog'][] = ["name"=>"PRC Date","textAlign"=>"center"];
    $data['acceptedLog'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['acceptedLog'][] = ["name"=>"Qty","textAlign"=>"center"];
	
	/* Production Log Header Data */
    $data['productionLog'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"PRC No","textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"PRC Date","textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"Production Time","textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"Department/Machine","textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"Operator","textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"Shift","textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"OK Qty","textAlign"=>"center"];
    $data['productionLog'][] = ["name"=>"Rej. Found","textAlign"=>"center"];
	
	/* Challan Request Header Data */
    $data['challanLog'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['challanLog'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['challanLog'][] = ["name"=>"PRC No","textAlign"=>"center"];
    $data['challanLog'][] = ["name"=>"PRC Date","textAlign"=>"center"];
    $data['challanLog'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['challanLog'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['challanLog'][] = ["name"=>"Vendor","textAlign"=>"center"];
    $data['challanLog'][] = ["name"=>"Request Qty","textAlign"=>"center"];
	
	/* Movement Header Data */
    $data['movement'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['movement'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['movement'][] = ["name"=>"PRC No","textAlign"=>"center"];
    $data['movement'][] = ["name"=>"PRC Date","textAlign"=>"center"];
    $data['movement'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['movement'][] = ["name"=>"Qty","textAlign"=>"center"];

    return tableHeader($data[$page]);
}

/* Process Table Data */
function getProcessData($data){
    $deleteButton = $editButton = '';
    if($data->is_system == 0 ){
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Process'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    
        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editProcess', 'title' : 'Update Process','call_function':'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    }
    
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->process_name,$data->remark];
}

/* Rejection Comment Table Data */
function getRejectionCommentData($data){
    $rejection_type = ($data->type == 1 ? "Rejection Reason": ($data->type == 2 ? "Idle Reason":"Rework Reason"));

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : '".$rejection_type."'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editRejection', 'title' : 'Update  ".$rejection_type."','call_function':'edit'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->code,$data->remark];
}

function getEstimationData($data){

    $soBomParam = "{'postData':{'trans_main_id' : ".$data->trans_main_id.",'trans_child_id':".$data->trans_child_id."},'modal_id' : 'modal-xxl', 'form_id' : 'addOrderBom', 'fnedit':'orderBom', 'fnsave':'saveOrderBom','title' : 'Order Bom','res_function':'resSaveOrderBom','js_store_fn':'customStore'}";
    $soBom = '<a class="btn btn-info btn-delete permission-write" href="javascript:void(0)" onclick="edit('.$soBomParam.');" datatip="SO Bom" flow="down"><i class="fa fa-database"></i></a>';

    $viewBomParam = "{'postData':{'trans_child_id':".$data->trans_child_id."},'modal_id' : 'modal-xl','fnedit':'viewOrderBom','title' : 'View Bom [Item Name : ".$data->item_name."]','button':'close'}";
    $viewBom = '<a class="btn btn-primary permission-read" href="javascript:void(0)" onclick="edit('.$viewBomParam.');" datatip="View Item Bom" flow="down"><i class="fa fa-eye"></i></a>';

    $reqParam = "{'postData':{'trans_child_id':".$data->trans_child_id.",'trans_number':'".$data->trans_number."','item_name':'".$data->item_name."'},'modal_id' : 'modal-xl', 'form_id' : 'addOrderBom', 'fnedit':'purchaseRequest', 'fnsave':'savePurchaseRequest','title' : 'Send Purchase Request'}";
    $reqButton = '<a class="btn btn-info btn-delete permission-write" href="javascript:void(0)" onclick="edit('.$reqParam.');" datatip="Purchase Request" flow="down"><i class="fa fa-paper-plane"></i></a>';

    $estimationParam = "{'postData':{'id':'".$data->id."','trans_child_id':".$data->trans_child_id.",'trans_main_id':'".$data->trans_main_id."'},'modal_id' : 'modal-xl', 'form_id' : 'estimation', 'fnedit':'addEstimation', 'fnsave':'saveEstimation','title' : 'Estimation & Design'}";
    $estimationButton = '<a class="btn btn-success permission-write" href="javascript:void(0)" onclick="edit('.$estimationParam.');" datatip="Estimation" flow="down"><i class="fa fa-plus"></i></a>';

    if($data->priority == 1):
        $data->priority_status = '<span class="badge badge-pill badge-danger m-1">'.$data->priority_status.'</span>';
    elseif($data->priority == 2):
        $data->priority_status = '<span class="badge badge-pill badge-warning m-1">'.$data->priority_status.'</span>';
    elseif($data->priority == 3):
        $data->priority_status = '<span class="badge badge-pill badge-info m-1">'.$data->priority_status.'</span>';
    endif;

    $data->bom_status = '<span class="badge badge-pill badge-'.(($data->bom_status == "Generated")?"success":"danger").' m-1">'.$data->bom_status.'</span>';

    $action = getActionButton($soBom.$viewBom.$reqButton.$estimationButton);

    return [$action,$data->sr_no,$data->job_number,$data->trans_date,$data->party_name,$data->item_name,$data->qty,$data->bom_status,$data->priority_status,$data->fab_dept_note,$data->pc_dept_note,$data->ass_dept_note,$data->remark];
}

/* Product Option Data */
function getProductOptionData($data){
    $bomParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addProductKitItems', 'title' : 'Create Material BOM [ ".htmlentities($data->item_name)." ]','call_function':'addProductKitItems','button':'close'}";

    $itemProcessParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'viewProductProcess', 'title' : 'Set Product Process [ ".htmlentities($data->item_name)." ]','call_function':'viewProductProcess','button':'close'}";

    $cycleTimeParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'cycleTime', 'title' : 'Set Cycle Time [ ".htmlentities($data->item_name)." ]','call_function':'addCycleTime','button':'both','fnsave':'saveCT'}";

    $dieSetParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-xl-modal', 'title' : 'Die Set [ ".(!empty($data->item_code) ? $data->item_code." - " : "").$data->item_name." ]','call_function':'addDieSet','button':'close','fnsave':'saveDieSet'}";

    $dieBomParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addDieBom', 'title' : 'Create Die BOM [ ".(!empty($data->item_code) ? $data->item_code." - " : "").$data->item_name." ]','call_function':'addDieBom','button':'close'}";

    $packingParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addPackingStandard', 'title' : 'Add Packing Standard [ ".htmlentities($data->item_name)." ]','call_function':'addPackingStandard','button':'close'}";
	$btn = '<div class="btn-group" role="group" aria-label="Basic example">
				<a href="'.base_url('productOption/productOptionPrint/'.$data->id).'" type="button" class="btn btn-info" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>
	
				<button type="button" class="btn btn-twitter permission-modify" onclick="modalAction('.$bomParam .')" datatip="BOM" flow="down" ><i class="fas fa-dolly-flatbed"></i></button>

                <button type="button" class="btn btn-info permission-modify" onclick="addProcess('.$itemProcessParam .')"  datatip="View Process" flow="down"><i class="fa fa-list"></i></button>

                <button type="button" class="btn btn-twitter permission-modify" onclick="modalAction('.$cycleTimeParam .')" datatip="Cycle Time" flow="down"><i class="fa fa-clock"></i></button>

                <button type="button" class="btn btn-info permission-modify" onclick="modalAction('.$dieBomParam .')" datatip="Die BOM" flow="down"><i class="fas fa-dolly-flatbed"></i></button>

                <button type="button" class="btn btn-twitter permission-modify" onclick="modalAction('.$packingParam .')" datatip="Packing Standard" flow="down"><i class="fas fa-plus"></i></button>
                
                <!--<button type="button" class="btn btn-twitter permission-modify" onclick="modalAction('.$dieSetParam .')" datatip="Die Set" flow="down"><i class="fa fa-list"></i></button>-->
            </div>';

    return [$data->sr_no,$data->item_code,$data->item_name,$data->bom,$data->process,$data->cycleTime,$btn];
}

/* Get Pending Rejection Review Data */
function getPendingReviewData($data){
    $rwBtn=$rejTag ="";
    $title = '[ Pending Decision : '.floatval($data->pending_qty).' ]';
    $okBtnParam="{'postData':{'id' : " . $data->id . ",'source':'".$data->source."'} ,'modal_id' : 'bs-right-md-modal', 'form_id' : 'okOutWard', 'title' : 'Ok ".$title."','button' : 'both','call_function' : 'convertToOk','fnsave' : 'saveReview'}";
    $rejBtnParam="{'postData':{'id' : " . $data->id . ",'source':'".$data->source."'} ,'modal_id' : 'bs-right-lg-modal', 'form_id' : 'rejOutWard', 'title' : 'Rejection ".$title." ','button' : 'both','call_function' : 'convertToRej','fnsave' : 'saveReview', 'js_store_fn' : 'customStore'}";
    $rwBtnParam="{'postData':{'id' : " . $data->id . ",'source':'".$data->source."'} ,'modal_id' : 'bs-right-lg-modal', 'form_id' : 'rwOutWard', 'title' : 'Rework ".$title." ','button' : 'both','call_function' : 'convertToRw','fnsave' : 'saveReview', 'js_store_fn' : 'customStore'}";

	$okBtn = '<a  onclick="modalAction('. $okBtnParam . ')"  class="btn btn-success btn-edit permission-modify" datatip="Ok" flow="down"><i class="mdi mdi-check"></i></a>';
    $rejBtn = '<a onclick="modalAction(' . $rejBtnParam . ')"  class="btn btn-danger btn-edit permission-modify" datatip="Rejection" flow="down"><i class="mdi mdi-close"></i></a>';
    if($data->source == 'MFG'){
        $rwBtn = '<a  onclick="modalAction('. $rwBtnParam . ')"  class="btn btn-info btn-edit permission-modify" datatip="Rework" flow="down"><i class=" fas fa-retweet"></i></a>';

        $rejTag = '<a href="' . base_url('pos/printPRCRejLog/' . $data->id) . '" target="_blank" class="btn btn-sm btn-dark waves-effect waves-light mr-1" title="Rejection Tag"><i class="fas fa-print"></i></a>';
    }
	
	
    $action = getActionButton($okBtn.$rejBtn.$rwBtn.$rejTag);

    if($data->source == 'GRN'){
        return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,(!empty($data->item_code) ? '['.$data->item_code.'] ' : '').$data->item_name,floatval($data->qty),floatval($data->review_qty),floatval($data->pending_qty)];
    }elseif($data->source == 'Manual'){
        return [$action,$data->sr_no,formatDate($data->trans_date),(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,$data->location,$data->batch_no,floatval($data->qty),floatval($data->review_qty),floatval($data->pending_qty)]; 
    }else{
        $process_name = ($data->source == 'FIR')?'Final Inspection':$data->process_name;
        return [$action,$data->sr_no,$data->prc_number,'['.$data->item_code.'] '.$data->item_name,formatDate($data->trans_date),$process_name,(!empty($data->processor_name)?$data->processor_name:''),$data->emp_name,$data->rej_found,$data->review_qty,$data->pending_qty];
    }
}

/* Get Rejection Review Data */
function getRejectionReviewData($data){
    
    $deleteParam = "{'postData':{'id' : ".$data->id."},'fndelete':'deleteReview'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    $rejTag = "";
    if($data->decision_type == 1 && $data->source == 'MFG'){
        $rejTag = '<a href="' . base_url('rejectionReview/printRejTag/' . $data->id) . '" target="_blank" class="btn btn-sm btn-dark waves-effect waves-light mr-1" title="Rejection Tag"><i class="fas fa-print"></i></a>';
    }
    if($data->decision_type == 2 && $data->source == 'MFG'){
        $rejTag = '<a href="' . base_url('pos/printPRCMovement/' . $data->movement_id) . '" target="_blank" class="btn btn-sm btn-dark waves-effect waves-light mr-1" title="Tag"><i class="fas fa-print"></i></a>';
    }
    $action = getActionButton($rejTag.$deleteButton);

    if($data->source == 'GRN'){
        return [$action,$data->sr_no,$data->source,$data->trans_number,(!empty($data->item_code) ? '['.$data->item_code.'] ' : '').$data->item_name,formatDate($data->created_at),$data->decision,floatval($data->qty)];
    }elseif($data->source == 'Manual'){
        return [$action,$data->sr_no,$data->source,(!empty($data->item_code) ? '['.$data->item_code.'] ' : '').$data->item_name,$data->location,$data->batch_no,formatDate($data->created_at),$data->decision,floatval($data->qty)];
    }else{
        return [$action,$data->sr_no,$data->prc_number,'['.$data->item_code.'] '.$data->item_name,formatDate($data->created_at),$data->decision,$data->process_name,$data->rr_process_name,$data->qty,$data->rr_by_name,$data->rej_mc_code,$data->rej_emp_name,$data->rr_comment];
    }
}


function getProductionShortageData($data){
    $addParam = "{'postData':{'item_id' : ".$data->item_id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'addPRC', 'form_id' : 'addPRC', 'title' : 'New PRC', 'fnsave' : 'savePRC'}";
    $prcBtn= '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Add PRC" flow="down" onclick="modalAction(' . $addParam . ');"><i class="far fa-plus-square"></i> </a>';
    
	$sort_qty = ($data->total_qty - ($data->total_dispatch_qty+$data->wip_qty+$data->prd_finish_Stock+$data->rtd_Stock));
	$sortage_qty = (($sort_qty>0)?$sort_qty:0);
	
	
	$action = getActionButton($prcBtn);
    return [$action,$data->sr_no,$data->item_code.' '.$data->item_name,$data->party_name,$data->so_number,$data->qty,floatval($data->total_qty),floatval($data->total_dispatch_qty),floatval($data->wip_qty),floatval($data->prd_finish_Stock),floatval($data->rtd_Stock),floatval($sortage_qty)];
}

/* Part List Table Data */
function getPartListData($data) {
    $rejectBtn = $recutBtn = $popReportBtn = $approveBtn = '';

    $deleteParam = "{'postData':{'id' : ".$data->id."}, 'message' : 'Component', 'fndelete' : 'deleteComponent'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    if(empty($data->status) && $data->is_inspection == 1){
        $popReportParam = "{'postData':{'id' : ".$data->id.", 'type' : 'Component', 'item_id' : ".$data->fg_id.", 'category_id' : ".$data->category_id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addPopReport', 'call_function' : 'addPopReport', 'fnsave' : 'savePopReport', 'title' : 'POP Report', 'controller' : 'dieProduction'}";
        $popReportBtn = '<a class="btn btn-dribbble permission-modify" href="javascript:void(0)" datatip="POP Report" flow="down" onclick="modalAction('.$popReportParam.');"><i class="fas fa-file-alt"></i></a>';
    }
    elseif($data->status == 5 || (empty($data->status) && $data->is_inspection == 0)){
        $approveParam = "{'postData':{'id' : ".$data->id.", 'status' : '1', 'type' : 'Component', 'item_id' : ".$data->fg_id.", 'category_id' : ".$data->category_id."}, 'modal_id' : 'bs_approval_modal', 'form_id' : 'approveProduction', 'call_function' : 'approveProduction', 'fnsave' : 'changeStatus', 'title' : 'Approve', 'controller' : 'dieProduction'}";
        $approveBtn = '<a class="btn btn-success btn-start permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalApproveAction('.$approveParam.');"><i class="fas fa-paper-plane" ></i></a>';
    }
    elseif($data->status == 1){
        $deleteButton = '';
        $rejectParam = "{'postData' : {'id' : '".$data->id."'}, 'modal_id' : 'modal-md', 'form_id' : 'rejectPart', 'title' : 'Reject Part','call_function':'rejectPart', 'fnsave' : 'changePartStatus'}";
        $rejectBtn = '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Reject" flow="down" onclick="modalAction('.$rejectParam.');"><i class="mdi mdi-close"></i></a>';   
        
        $recutParam = "{'postData':{'id' : ".$data->id.", 'fg_id' : '".$data->fg_id."', 'category_id' : '".$data->category_id."', 'status' : 3, 'msg' : 'Recut'},'fnsave':'recutDie','message':'Are you sure want to Recut this Part?'}";
        $recutBtn = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Recut" flow="down" onclick="confirmStore('.$recutParam.');"><i class="fas fa-cogs"></i></a>'; 
    }
	
	$historyParam = "{'postData':{'die_id' : ".$data->id.", 'die_job_id' : '".$data->die_job_id."'}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'viewDieHistory', 'call_function' : 'viewDieHistory', 'title' : '".$data->die_code.' ('.$data->category_name.")', 'button' : 'close'}";
	$historyBtn = '<a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="View Die History" flow="down" onclick="modalAction('.$historyParam.');"><i class="fas fa-eye"></i></a>';
    
    $action = getActionButton($approveBtn.$popReportBtn.$historyBtn.$recutBtn.$rejectBtn.$deleteButton);
    return [$action,$data->sr_no,$data->die_code,$data->category_name,$data->fg_item_code.' - '.$data->fg_item_name];
}


/* Jobwork Order Data */
function getJobWorkOrderData($data){
    $approveBtn = $editButton = $deleteButton = $closeBtn =  '';
    if($data->trans_status == 1){
		$approveParam = "{'postData':{'id' : ".$data->id.", 'msg' : 'Approved'},'fnsave':'approveOrder','message':'Are you sure want to Approve this Order?'}";
		$approveBtn = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="confirmStore('.$approveParam.');"><i class="mdi mdi-check"></i></a>';
		
		$editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('jobworkOrder/edit/'.$data->jwo_id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

		$deleteParam = "{'postData':{'id' : ".$data->id.",'jwo_id' : ".$data->jwo_id."},'message' : 'Jobwork Order'}";
		$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
        
    }
	if($data->trans_status == 3){
		$closeParam = "{'postData':{'id' : ".$data->id.", 'msg' : 'Close'},'fnsave':'shortCloseOrder','message':'Are you sure want to Close this Order?'}";
		$closeBtn = '<a class="btn btn-danger permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" onclick="confirmStore('.$closeParam.');"><i class="mdi mdi-close-circle-outline"></i></a>';
	}
    $action = getActionButton($closeBtn.$approveBtn.$editButton.$deleteButton);  
    return [$action,$data->sr_no,formatDate($data->order_date),$data->trans_number,$data->party_name,$data->item_code.' '.$data->item_name,$data->process_name,$data->rate_per_unit,sprintf('%0.2f',$data->rate),$data->remark];
}

/*Vendor Challan Table Data */
function getjobWorkBillData($data){

    $billParam = "{'postData':{'log_ids':'".$data->log_ids."'},'modal_id' : 'modal-md', 'form_id' : 'billForm', 'title' : 'Vendor Challan','call_function':'jobWorkBill','fnsave':'saveJobWorkBill'}";
    $billBtn = '<a href="javascript:void(0)" type="button" class="btn btn-success permission-modify" datatip="Vendor Challan" flow="down" onclick="modalAction('.$billParam.');"><i class="fa fa-file-alt"></i></a>';

    $action = getActionButton($billBtn);  
    return[$action,$data->sr_no,formatDate($data->trans_date),$data->in_challan_no,$data->challan_no,$data->party_name];
}

/* Die Production Table Data */
function getDieProductionData($data) { 
    $mtrIssue = "";$logBtn = "";$chBtn = '';$completeBtn = '';$deleteButton="";
    if($data->status == 1){
        if($data->die_block_id == 0 && $data->trans_type == 1){
            $issueParam = "{'postData':{'id' : ".$data->id.",'die_id':".$data->die_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'materialIssue', 'title' : 'Material Isuue [ ".$data->trans_number." ]', 'fnsave' : 'saveMaterialIssue','call_function':'materialIssue'}";
            $mtrIssue = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Material Issue" flow="down" onclick="modalAction('.$issueParam.');"><i class="fas fa-clipboard-check"></i></a>';

            $deleteParam = "{'postData':{'id' : ".$data->id."}, 'message' : 'Work Order', 'fndelete' : 'delete'}";

            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
        }
        
        if($data->die_block_id > 0 || $data->trans_type == 2){
            $logParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'addDieLog', 'form_id' : 'addDieLog', 'title' : 'Add Die Log', 'fnsave' : 'saveDieLog','button' : 'close'}";
            $logBtn = '<a href="javascript:void(0)" onclick="modalAction('.$logParam.')" class="btn btn-info permission-modify" datatip="Add Log" flow="down"><i class="fas fa-clipboard-list"></i></a>';

            $chParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'addChallanRequest', 'form_id' : 'addChallanRequest', 'title' : 'Challan Request', 'fnsave' : 'saveChallanRequest'}";
            $chBtn = '<a href="javascript:void(0)" onclick="modalAction('.$chParam.')" class="btn btn-primary permission-modify" datatip="Challan Request" flow="down"><i class="fab fa-telegram-plane"></i></a>';
        
        }

        if(!empty($data->log_count)){
            $completeParam = "{'postData':{'id' : ".$data->id."},'message' : 'Are you sure you want to Complete this Work Order ?','modal_id' : 'modal-md', 'call_function':'completeProductionView', 'fnsave' : 'completeProduction', 'form_id' : 'completeProductionView', 'title' : 'Completed Die'}";
            $completeBtn = ' <a class="btn btn-primary permission-modify " href="javascript:void(0)" datatip="Complete" flow="down" onclick="modalAction('.$completeParam.')"><i class="mdi mdi-check-decagram"></i></a>';
        }
    }else{
        $logParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'dieLogDetail', 'form_id' : 'dieLogDetail', 'title' : 'Process Detail','button' : 'close'}";
        $logBtn = '<a href="javascript:void(0)" onclick="modalAction('.$logParam.')" class="btn btn-facebook permission-modify" datatip="Process Detail" flow="down"><i class="fas fa-clipboard-list"></i></a>';

    }
    $action = getActionButton($completeBtn.$mtrIssue.$logBtn.$chBtn.$deleteButton);
   
    return [$action,$data->sr_no,formatDate($data->trans_date),$data->trans_number,$data->wo_type,$data->die_code,$data->die_name,(($data->item_code) ? $data->item_code." " : "").$data->item_name,$data->qty,$data->ok_qty,($data->qty - $data->ok_qty),$data->rm_code.' '.$data->rm_name,$data->mtr_qty]; 
}

/* Die Production Table Data */
function getDieOutsourceData($data) {
    $selectBox = '<input type="checkbox" name="dp_id[]" id="dp_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkChallan" value="'.$data->id.'"><label for="dp_id_'.$data->sr_no.'"></label>';
    return [ $selectBox,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->die_code,$data->die_name,$data->item_code.' '.$data->item_name,$data->process_name,$data->qty]; 
}

/* Die Production Table Data */
function getDieChallanData($data) {
    $deleteButton = "";$logBtn="";$detailBtn = "";
    $logParam = "{'postData':{'id' : ".$data->wo_id.",'ref_trans_id':".$data->id.",'challan_id':".$data->challan_id.",'party_id':".$data->party_id.",'process_id':".$data->process_id.",'process_by':'2'},'modal_id' : 'bs-right-lg-modal', 'call_function':'addDieLog', 'form_id' : 'addDieLog', 'title' : 'Receive Challan', 'fnsave' : 'saveProductionLog','controller':'dieProduction','button':'close'}";
    $logBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Receive Challan" flow="down" onclick="modalAction('.$logParam.')"><i class="fas fa-paper-plane"></i></a>';

    if($data->receive_qty == 0){
        $deleteParam = "{'postData':{'id' : ".$data->challan_id."}, 'message' : 'Challan', 'fndelete' : 'deleteChallan'}";

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }
    
    
    $print = '<a href="'.base_url('dieProduction/dieOutSourcePrint/'.$data->challan_id).'" type="button" class="btn btn-primary" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    $action = getActionButton($print.$detailBtn.$logBtn.$deleteButton);
    return [ $action,$data->sr_no,$data->ch_number,formatDate($data->ch_date ),$data->party_name,$data->trans_number,formatDate($data->trans_date),$data->die_code,$data->die_name,$data->item_code.' '.$data->item_name,$data->process_name,$data->qty,$data->receive_qty]; 
}

/* Die Register Table Data */
function getDieRegisterData($data) { 
    $rejBtn = $rwBtn = '';$issueButton = '';$returnBtn = '';$convertButton='';
   
    $action = getActionButton($issueButton.$rejBtn.$rwBtn.$returnBtn.$convertButton);
   
    return [$action,$data->sr_no,formatDate($data->trans_date),$data->die_number,$data->die_name,(($data->item_code) ? $data->item_code." " : "").$data->item_name,$data->category_name,$data->material_grade,$data->prc_number]; 
}

/* Die Register Table Data */
function getFreshDieData($data) { 
    $pending_qty = $data->ok_qty - $data->stock_qty;
    $stockBtn="";
    $stockParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'modal-md', 'call_function':'addDieInStock', 'fnsave' : 'saveDieStock', 'form_id' : 'addDieInStock', 'title' : 'Add Die Stock'}";
    $stockBtn = '<a class="btn btn-primary permission-modify " href="javascript:void(0)" datatip="Add To Stock" flow="down" onclick="modalAction('.$stockParam.')"><i class="mdi mdi-check-decagram"></i></a>';
    $action = getActionButton($stockBtn);
   
    return [$action,$data->sr_no,formatDate($data->trans_date),$data->trans_number,$data->die_code,$data->die_name,$pending_qty,(($data->item_code) ? $data->item_code." " : "").$data->item_name,$data->category_name,$data->material_grade]; 
}

function getPRCData($data){
    $startButton = $editButton = $deleteButton = $holdBtn = $shortBtn = $restartBtn = $updateQty = $toolBtn = $toolReleaseBtn = "";
	$prc_number = '<a href="'.base_url("sopDesk/prcDetail/".$data->id).'">'.$data->prc_number.'</a>';


    if($data->status == 1 ){
        $startTitle = 'Start PRC : '.$data->prc_number;
        $startParam = "{'postData':{'id' : ".$data->id.",'item_id':".$data->item_id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editPrc', 'title' : '".$startTitle."', 'fnsave' : 'startPrc', 'js_store_fn' : 'confirmStore','call_function':'setPrcProcesses'}";
        $startButton = ' <a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Start" flow="down" onclick="modalAction('.$startParam.')"><i class="fas fa-play-circle"></i></a>';

        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editPrc', 'title' : 'Update PRC', 'fnsave' : 'savePRC'}";
        $editButton= ' <a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.')"><i class="far fa-edit"></i></a>';

        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'PRC'}";
        $deleteButton = ' <a class="btn btn-danger permission-remove" href="javascript:void(0)" datatip="Delete" flow="down" onclick="trash('.$deleteParam.')"><i class="mdi mdi-trash-can-outline"></i></a>';
    }elseif($data->status == 2){

        /*** IF PRC IS IN PROGRSS THEN PROCESS BUTTON */
        $startTitle = 'PRC Process: '.$data->prc_number;
        $startParam = "{'postData':{'id' : ".$data->id.",'item_id':".$data->item_id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editPrc', 'title' : '".$startTitle."', 'fnsave' : 'startPrc', 'js_store_fn' : 'confirmStore','call_function':'setPrcProcesses'}";
        $startButton = ' <a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="PRC Process" flow="down" onclick="modalAction('.$startParam.')"><i class="fas fa-clipboard-list"></i></a>';
        
        $updateQtyParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'modal-md', 'form_id' : 'updatePrcQty', 'title' : 'Update PRC Qty [".$data->prc_number."] ', 'call_function' : 'updatePrcQty', 'button' : 'close'}";
        $updateQty= '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Update PRC Qty." flow="down" onclick="modalAction(' . $updateQtyParam . ');"><i class="far fa-plus-square"></i> </a>';
        
        $holdParam = "{'postData':{'id' : ".$data->id.", 'status' : 4},'message' : 'Are you sure want to Hold this PRC ?', 'fnsave' : 'changePrcStage'}";
        $holdBtn= ' <a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Hold" flow="down" onclick="confirmStore('.$holdParam.')"><i class="far fa-pause-circle"></i></a>';
        
        $shortParam = "{'postData':{'id' : ".$data->id.", 'status' : 5},'message' : 'Are you sure want to Short Close this PRC ?', 'fnsave' : 'changePrcStage'}";
        $shortBtn = ' <a class="btn btn-danger permission-modify " href="javascript:void(0)" datatip="Short Close" flow="down" onclick="confirmStore('.$shortParam.')"><i class="fas mdi mdi-close-circle-outline"></i></a>';
    }
    elseif($data->status == 4 || $data->status == 5){
        $restartParam = "{'postData':{'id' : ".$data->id.", 'status' : 2},'message' : 'Are you sure want to Restart this PRC ?', 'fnsave' : 'changePrcStage'}";
        $restartBtn = ' <a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Restart" flow="down" onclick="confirmStore('.$restartParam.')"><i class="mdi mdi-restart"></i></a>';
    }
	
	if($data->tool_status == 0){
        $startButton = "";$restartBtn="";
    }else{
        $toolReleaseParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'releaseTool', 'title' : 'Tool Release For : ".$data->prc_number."', 'fnsave' : 'saveReleaseTool', 'call_function' : 'releaseTool'}";
        $toolReleaseBtn= ' <a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Tool Release" flow="down" onclick="modalAction('.$toolReleaseParam.')"><i class="fas fa-sign-out-alt"></i></a>';
    }

    if(in_array($data->status,[1,2])){
        $toolParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'toolIssue', 'title' : 'Tool Issue For : ".$data->prc_number."', 'fnsave' : 'saveIssuedTool', 'call_function' : 'toolIssue'}";
        $toolBtn= ' <a class="btn btn-facebook permission-modify" href="javascript:void(0)" datatip="Tool Issue" flow="down" onclick="modalAction('.$toolParam.')"><i class="fas fa-wrench"></i></a>';
    }
    $action = getActionButton($startButton.$holdBtn.$shortBtn.$restartBtn.$updateQty.$toolBtn.$editButton.$deleteButton.$toolReleaseBtn);
    return [$action,$data->sr_no,$prc_number,formatDate($data->prc_date),$data->item_code.' '.$data->item_name,floatval($data->prc_qty),$data->target_date,$data->remark];
}

function getPrcInwardData($data){
    $data->accepted_qty= (!empty($data->accepted_qty)?$data->accepted_qty:0);
   
    $pending_accept =$data->inward_qty - $data->accepted_qty;
	$moveType = (($data->move_type == 1)?"Regular":"Rework");
	$item_name = ((!empty($data->item_code) ? "[ ".$data->item_code." ] " : "").$data->item_name);
    $acceptBtn="";
    $acceptParam = "{'postData':{'prc_id':".$data->prc_id.",'accepted_process_id':".$data->accepted_process_id.",'trans_type':".$data->move_type.",'process_from':".$data->process_from.",'completed_process':'".$data->completed_process."','movement_type':'".$moveType."','prc_number':'".$data->prc_number."','item_name':'".$item_name."'},'modal_id' : 'bs-right-lg-modal', 'call_function':'prcAccept', 'form_id' : 'addPrcAccept', 'title' : 'Accept Form', 'js_store_fn' : 'storeSop', 'fnsave' : 'saveAcceptedQty','button':'close'}";
    $acceptBtn = '<a href="javascript:void(0)" class=" btn btn-dark permission-modify" datatip="Accept" flow="down" onclick="modalAction('.$acceptParam .')"><i class="far fa-check-circle"></i></a>';
    $action = getActionButton($acceptBtn);
    // $moveType = (($data->move_type == 1)?"Regular":"Rework");
    return [$action ,$data->sr_no,$moveType,$data->prc_number,formatDate($data->prc_date),$item_name,$data->completed_process_name,floatval($pending_accept),floatval($data->accepted_qty)];
}

function getPRCLogData($data){
    $in_qty = (!empty($data->in_qty)?$data->in_qty:0);
    $ok_qty = !empty($data->ok_qty)?$data->ok_qty:0;
    $rej_found_qty = !empty($data->rej_found)?$data->rej_found:0;
    $rej_qty = !empty($data->rej_qty)?$data->rej_qty:0;
    $rw_qty = !empty($data->rw_qty)?$data->rw_qty:0;
    $pendingReview = $rej_found_qty - $data->review_qty;
    $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview);
    $productionQty = ($ok_qty + $rej_found_qty );
    $logBtn = "";$chReqBtn="";
	$moveType = (($data->trans_type == 1)?"Regular":"Rework");
	$item_name = ((!empty($data->item_code) ? "[ ".$data->item_code." ] " : "").$data->item_name);
    if($data->process_id == 2){
        $reportParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id':".$data->prc_id.",'trans_type':".$data->trans_type.",'process_from':".$data->process_from.",'completed_process':'".$data->completed_process."'},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'firInsp', 'title' : 'Final Inspection','call_function':'addFinalInspection','fnsave':'savePrcLog', 'js_store_fn' : 'customStore'}";
	    $logBtn = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Final Inspection" flow="down" onclick="modalAction('.$reportParam.');"><i class="fa fa-file-alt"></i></a>';
    }else{
        $logParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id':".$data->prc_id.",'trans_type':".$data->trans_type.",'process_from':".$data->process_from.",'completed_process':'".$data->completed_process."','movement_type':'".$moveType."','prc_number':'".$data->prc_number."','item_name':'".$item_name."'},'modal_id' : 'bs-right-lg-modal', 'call_function':'prcLog', 'form_id' : 'addPrcLog', 'title' : 'PRC LOG', 'fnsave' : 'savePRCLog','button':'close'}";
        $logBtn = '<a href="javascript:void(0)" onclick="modalAction('.$logParam.')" class="btn btn-success permission-modify" datatip="Add Log" flow="down"><i class="fas fa-clipboard-list"></i></a>';
    }
    

    $title = '';//'[Pending Qty : '.floatval($pending_production).']';
    $chReqParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id':".$data->prc_id.",'trans_type':".$data->trans_type.",'process_from':".$data->process_from.",'completed_process':'".$data->completed_process."'},'modal_id' : 'bs-right-md-modal', 'call_function':'challanRequest', 'form_id' : 'addChallanRequest', 'title' : 'Challan Request ".$title ."', 'js_store_fn' : 'storeSop', 'fnsave' : 'saveAcceptedQty','button':'close'}";
    $chReqBtn = '<a href="javascript:void(0)" class=" btn btn-warning permission-modify" datatip="Challan Request" flow="down" onclick="modalAction('.$chReqParam .')"><i class="fab fa-telegram-plane"></i></a>';
   
    // $moveType = (($data->trans_type == 1)?"Regular":"Rework");
    $action = getActionButton($logBtn.$chReqBtn);
    return [$action,$data->sr_no,$moveType,$data->prc_number,formatDate($data->prc_date),$item_name,$data->completed_process_name,floatval($in_qty),floatval($ok_qty),floatval($rej_found_qty),floatval($rej_qty),floatval($pending_production)];
}

function getPRCMovementData($data){
    $ok_qty = !empty($data->ok_qty)?$data->ok_qty:0;
    $movement_qty =!empty($data->movement_qty)?$data->movement_qty:0;
    $pending_movement = ($ok_qty - $movement_qty);
	$moveType = (($data->trans_type == 1)?"Regular":"Rework");
	$item_name = ((!empty($data->item_code) ? "[ ".$data->item_code." ] " : "").$data->item_name);
    $movementBtn="";
    $movementParam = "{'postData':{'process_id' : ".$data->process_id.",'trans_type':".$data->trans_type.",'process_from':".$data->process_from.",'completed_process':'".$data->completed_process."','prc_id':'".$data->prc_id."','movement_type':'".$moveType."','prc_number':'".$data->prc_number."','item_name':'".$item_name."'},'modal_id' : 'bs-right-lg-modal', 'call_function':'prcMovement', 'form_id' : 'addPrcMovement', 'title' : 'PRC Movement','button':'close'}";
    $movementBtn = '<a href="javascript:void(0)" class=" btn btn-danger permission-modify" datatip="Movement" flow="down" onclick="modalAction('.$movementParam.')"><i class="fa fa-step-forward"></i></a>';
    // $moveType = (($data->trans_type == 1)?"Regular":"Rework");
    $action = getActionButton($movementBtn);
    return [$action,$data->sr_no,$moveType,$data->prc_number,formatDate($data->prc_date),$item_name,$data->completed_process_name,floatval($ok_qty),floatval($movement_qty),floatval($pending_movement)];
}

/* Production Opration Data */
function getVendorPriceData($data){
   
    $editButton='';$deleteButton="";$approveBtn="";$rejectBtn="";
    if(empty($data->status)){
		$editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editPrice', 'title' : 'Update Vendor Price','fnsave' : 'save','js_store_fn':'storePrice'}";
		$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="editPrice('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Price'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    
        $approveBtn = '<a class="btn btn-primary permission-approve1" href="javascript:void(0)" onclick="approvePrice('.$data->id.');" datatip="Approve" flow="down"><i class="fa fa-check"></i></a>';
        
        $rejectBtn = '<a class="btn btn-dark btn-reject permission-modify" href="javascript:void(0)" onclick="rejectPrice('.$data->id.');" datatip="Reject" flow="down"><i class="mdi mdi-close"></i></a>';
    }
    
	$action = getActionButton($approveBtn.$rejectBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->created_at)),$data->party_name,$data->item_code,$data->item_name,$data->process_name,$data->rate,(($data->rate_unit == 1)?'Per Piece':'Per Kg'),$data->emp_name];
}

/* Outsource Table Data */
function getOutsourceData($data){
    
    $logParam = "{'postData':{'prc_id' : ".$data->prc_id.",'process_id' : ".$data->process_id.",'process_from' : ".$data->process_from.",'completed_process' : '".$data->completed_process."','ref_trans_id':".$data->id.",'challan_id':".$data->challan_id.",'wt_nos':".$data->wt_nos.",'processor_id':".$data->party_id.",'challan_process':'".$data->challan_process."','process_by':'3'},'modal_id' : 'bs-right-lg-modal', 'call_function':'addLog', 'form_id' : 'addLog', 'title' : 'Receive Challan', 'js_store_fn' : 'customStore', 'fnsave' : 'saveLog','controller':'outsource','button':'close'}";
    $logBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Receive Challan" flow="down" onclick="modalAction('.$logParam.')"><i class=" fas fa-paper-plane"></i></a>';

    $pending_qty = $data->qty - ($data->ok_qty+$data->rej_qty+$data->without_process_qty);
    $deleteButton = "";
    if($pending_qty > 0){
        $deleteParam = "{'postData':{'id' : ".$data->challan_id."}}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }
    
    $print = '<a href="'.base_url('outsource/outSourcePrint/'.$data->out_id).'" type="button" class="btn btn-primary" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';

    $action = getActionButton($print.$logBtn.$deleteButton);

    return [$action,$data->sr_no,date('d-m-Y',strtotime($data->ch_date)),$data->ch_number,$data->prc_number,$data->party_name,$data->item_name,$data->process_names,floatVal($data->qty),floatVal($data->ok_qty+$data->rej_qty),floatval($data->without_process_qty),floatVal($pending_qty)];
}

/* Die Register Table Data */
function getToolMethodData($data) { 
    $deleteParam = "{'postData':{'id' : ".$data->id."}, 'message' : 'Tool Method', 'fndelete' : 'delete'}";

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    $action = getActionButton($deleteButton);
   
    return [$action,$data->sr_no,$data->method_code,$data->method_name,$data->die_names]; 
}

/* Prc Batch Issue Table Data */
function getPrcBatchIssueData($data){
    $prcButton = "";
    $addParam = "{'postData':{'item_id' : ".$data->item_id.",'batch_id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'addPRC', 'form_id' : 'addPRC', 'title' : 'New PRC', 'fnsave' : 'savePRC'}";
    $prcButton= '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Add PRC" flow="down" onclick="modalAction(' . $addParam . ');"><i class="far fa-plus-square"></i> </a>';

    $action = getActionButton($prcButton);
    $createdBy = $data->created_name.(!empty($data->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->created_at)) : '');
    return [$action,$data->sr_no,formatDate($data->trans_date),$data->trans_number,$data->item_code.' '.$data->item_name,$data->issue_qty,$data->issue_name,$createdBy,$data->remark];
}

/* Accept Log Table Data */
function getAcceptedLogData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Record','fndelete' : 'deletePrcAccept','controller':'sopDesk'}";
    $deleteBtn = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class="fas fa-trash-alt"></i></a>';

    $action = getActionButton($deleteBtn);
    return [$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),(!empty($data->item_code) ? "[ ".$data->item_code." ] " : "").$data->item_name.(!empty($data->part_no) ? " - ".$data->part_no : ""),floatval($data->accepted_qty)];
}

/* Challan Request Table Data */
function getChallanLogData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Record','fndelete' : 'deleteChallanRequest','controller':'sopDesk'}";
	$deleteBtn = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class="fas fa-trash-alt"></i></a>';

    $action = getActionButton($deleteBtn);
    return [$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),(!empty($data->item_code) ? "[ ".$data->item_code." ] " : "").$data->item_name.(!empty($data->part_no) ? " - ".$data->part_no : ""),formatDate($data->trans_date),$data->party_name,floatval($data->qty)];
}

/* Production Log Table Data */
function getProductionLogData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Record','fndelete' : 'deletePRCLog','controller':'sopDesk'}";
	$deleteBtn = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class="fas fa-trash-alt"></i></a>';

    $rejTag = ''; $rejQty = floatval($data->rej_found);
    if(!empty($rejQty)){
        $rejTag .= '<a href="' . base_url('pos/printPRCLog/' . $data->id) . '" target="_blank" class="btn btn-sm btn-dark waves-effect waves-light" datatip="Rejection Tag" flow="down"> <i class="fas fa-print"></i></a>';
    }
    $action = getActionButton($rejTag.$deleteBtn);
    return [$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),(!empty($data->item_code) ? "[ ".$data->item_code." ] " : "").$data->item_name.(!empty($data->part_no) ? " - ".$data->part_no : ""),formatDate($data->trans_date),$data->production_time,$data->processor_name,$data->emp_name,$data->shift_name,floatval($data->qty),floatval($data->rej_found)];
}

/* Movement Log Table Data */
function getMovementData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Record','fndelete' : 'deletePRCMovement','controller':'sopDesk'}";
	$deleteBtn = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class="fas fa-trash-alt"></i></a>';

    $printTag = '<a href="' . base_url('pos/printPRCMovement/' . $data->id) . '" target="_blank" class="btn btn-sm btn-info waves-effect waves-ligh" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';

    $action = getActionButton($printTag.$deleteBtn);
    return [$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),(!empty($data->item_code) ? "[ ".$data->item_code." ] " : "").$data->item_name.(!empty($data->part_no) ? " - ".$data->part_no : ""),floatval($data->qty)];
}
?>