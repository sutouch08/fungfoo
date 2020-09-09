<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-sm-6">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> <?php label('back'); ?></button>
		</p>
	</div>
</div><!-- End Row -->
<hr style="margin-bottom:0px;"/>
<?php
$tab1 = $tab == 'infoTab' ? 'active in' : '';
$tab2 = $tab == 'billTab' ? 'active in' : '';
$tab3 = $tab == 'shipTab' ? 'active in' : '';
?>

<div class="row">
<div class="col-sm-1 col-1-harf padding-right-0 padding-top-15">
	<ul id="myTab1" class="setting-tabs width-100" style="margin-left:0px;">
	  <li class="li-block <?php echo $tab1; ?>" onclick="changeView('<?php echo $ds->code; ?>','infoTab')" >
			<a href="#infoTab" data-toggle="tab" style="text-decoration:none;"><?php label('customer_info'); ?></a>
		</li>
		<li class="li-block <?php echo $tab2; ?>" onclick="changeView('<?php echo $ds->code; ?>','billTab')" >
			<a href="#billTab" data-toggle="tab" style="text-decoration:none;"><?php label('bill_to'); ?></a>
		</li>
		<li class="li-block <?php echo $tab3; ?>" onclick="changeView('<?php echo $ds->code; ?>','shipTab')" >
			<a href="#shipTab" data-toggle="tab" style="text-decoration:none;" ><?php label('ship_to'); ?></a>
		</li>
	</ul>
</div>

<div class="col-sm-10" style="padding-top:15px; border-left:solid 1px #ccc; min-height:600px; ">
<div class="tab-content" style="border:0">
	<div class="tab-pane fade <?php echo $tab1; ?>" id="infoTab">
		<?php $this->load->view('masters/customers/customers_view_info'); ?>
	</div>
	<div class="tab-pane fade <?php echo $tab2; ?>" id="billTab">
		<?php $this->load->view('masters/customers/customers_view_bill_to'); ?>
	</div>
	<div class="tab-pane fade <?php echo $tab3; ?>" id="shipTab">
		<?php $this->load->view('masters/customers/customers_view_ship_to'); ?>
	</div>
</div>
</div><!--/ col-sm-9  -->
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/masters/customers.js"></script>
<script src="<?php echo base_url(); ?>scripts/masters/address.js"></script>
<script src="<?php echo base_url(); ?>scripts/masters/customer_address.js"></script>

<?php $this->load->view('include/footer'); ?>
