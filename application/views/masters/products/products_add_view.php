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
<link rel="stylesheet" href="<?php  echo base_url();?>assets/css/dropzone.css" />
<div class="row">
<div class="col-sm-1 col-1-harf padding-right-0 padding-top-15">
	<ul id="myTab1" class="setting-tabs width-100" style="margin-left:0px;">
	  <li class="li-block active in"><a href="#styleTab" data-toggle="tab">รุ่นสินค้า</a></li>
		<li class="li-block not-show"><a href="#itemTab" data-toggle="tab" style="text-decoration:none;">รายการสินค้า</a></li>
		<li class="li-block not-show"><a href="#imageTab" data-toggle="tab" style="text-decoration:none;" >รูปภาพ</a></li>
	</ul>
</div>
<div class="col-sm-10" style="padding-top:15px; border-left:solid 1px #ccc; min-height:600px; ">
<div class="tab-content" style="border:0">
	<div class="tab-pane fade active in" id="styleTab">
		<?php $this->load->view('masters/products/product_info'); ?>
	</div>
</div>
</div><!--/ col-sm-9  -->
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/masters/products.js"></script>
<script src="<?php echo base_url(); ?>scripts/masters/product_info.js"></script>
<?php $this->load->view('include/footer'); ?>
