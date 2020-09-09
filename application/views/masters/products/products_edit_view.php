<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr style="margin-bottom:0px;"/>
<script src="<?php echo base_url(); ?>assets/js/dropzone.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.colorbox.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/dropzone.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/colorbox.css" />
<?php
$tab1 = $tab == 'styleTab' ? 'active in' : '';
$tab2 = $tab == 'itemTab' ? 'active in' : '';
$tab3 = $tab == 'imageTab' ? 'active in' : '';

?>




<div class="row">
<div class="col-sm-1 col-1-harf padding-right-0 padding-top-15">
	<ul id="myTab1" class="setting-tabs width-100" style="margin-left:0px;">
	  <li class="li-block <?php echo $tab1; ?>" onclick="changeURL('<?php echo $style->code; ?>','styleTab')" >
			<a href="#styleTab" data-toggle="tab" style="text-decoration:none;">ข้อมูลสินค้า</a>
		</li>
		<li class="li-block <?php echo $tab2; ?>" onclick="changeURL('<?php echo $style->code; ?>','itemTab')" >
			<a href="#itemTab" data-toggle="tab" style="text-decoration:none;">รายการ</a>
		</li>
		<li class="li-block <?php echo $tab3; ?>" onclick="changeURL('<?php echo $style->code; ?>','imageTab')" >
			<a href="#imageTab" data-toggle="tab" style="text-decoration:none;" >รูปภาพ</a>
		</li>
	</ul>
</div>

<div class="col-sm-10" style="padding-top:15px; border-left:solid 1px #ccc; min-height:600px; ">
<div class="tab-content" style="border:0">
	<div class="tab-pane fade <?php echo $tab1; ?>" id="styleTab">
		<?php $this->load->view('masters/products/product_edit_info'); ?>
	</div>
	<div class="tab-pane fade <?php echo $tab2; ?>" id="itemTab">
		<?php $this->load->view('masters/products/product_items'); ?>
	</div>
	<div class="tab-pane fade <?php echo $tab3; ?>" id="imageTab">
		<?php $this->load->view('masters/products/product_image'); ?>
	</div>
</div>
</div><!--/ col-sm-9  -->
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/masters/products.js"></script>
<script src="<?php echo base_url(); ?>scripts/masters/product_info.js"></script>
<script src="<?php echo base_url(); ?>scripts/masters/product_image.js"></script>
<script src="<?php echo base_url(); ?>scripts/masters/product_items.js"></script>
<?php $this->load->view('include/footer'); ?>
