<div class="row">
  <div class="col-sm-3 padding-5 first">
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="ค้นรหัสสินค้า" />
  </div>
  <div class="col-sm-2 padding-5">
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()"><i class="fa fa-tags"></i> แสดงสินค้า</button>
  </div>
</div>
<hr class="margin-top-15">
<!--- Category Menu ---------------------------------->
<div class='row'>
	<div class='col-sm-12'>
		<ul class='nav navbar-nav' role='tablist' style='background-color:#EEE'>
		<?php echo productTabMenu('order'); ?>
		</ul>
	</div><!---/ col-sm-12 ---->
</div><!---/ row -->
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<div class='row'>
	<div class='col-sm-12'>
		<div class='tab-content' style="min-height:1px; padding:0px; border:0px;">
		<?php echo getProductTabs(); ?>
		</div>
	</div>
</div>
<!-- End Category Menu ------------------------------------>
<hr class="margin-top-15">
