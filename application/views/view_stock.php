<?php $this->load->view('include/header');  ?>

<div class="row">
	<div class="col-sm-6">
    	<h4 class="title"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
      </p>
    </div>
</div>
<hr class="margin-bottom-15" />
<!--  Search Product -->
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <select class="form-control input-sm" name="warehouse" id="warehouse">
      <option value="">เลือกคลัง</option>
      <?php echo select_sell_warehouse(); ?>
    </select>
  </div>
	<div class="col-sm-3 padding-5">
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="ค้นรหัสสินค้า" />
  </div>

  <div class="col-sm-2 padding-5">
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()"><i class="fa fa-tags"></i> แสดงสินค้า</button>
  </div>
</div>
<hr class="margin-top-15 margin-bottom-0" />
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

<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal">
		<div class="modal-content" style="position:relative; max-height:900px; max-width:1200px; min-width: 250px; overflow-x:scroll; overflow-y:scroll;">
  			<div class="modal-header">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
				<h4 class="modal-title inline margin-left-15" id="modalTitle" style="font-size:28px; font-weight:bolder; width:50%">title</h4>
				<div class="pull-right margin-right-15 margin-bottom-10" id="image-cover"></id>
			 </div>
			 <div class="modal-body" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			 </div>
		</div>
	</div>
</div>
</form>


<input type="hidden" name="view" id="view" value="1">

<script src="<?php echo base_url(); ?>scripts/orders/orders.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js"></script>
<?php $this->load->view('include/footer'); ?>
