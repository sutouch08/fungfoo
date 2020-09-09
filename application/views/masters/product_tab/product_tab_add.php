<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<style>
.lbl::before {
	margin-right:10px !important;
}
</style>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
  <div class="row">
  	<div class="col-sm-12">
      <div class="form-group">
    		<label class="col-sm-2 control-label no-padding-right text-right">ชื่อแถบ</label>
    		<div class="col-sm-4">
    			<input type="text" class="form-control input-sm" name="tab_name" id="tab_name" required>
    		</div>
    	</div>
    </div>
    <div class="col-sm-12">
      <div class="form-group">
    		<label class="col-sm-2 control-label no-padding-right text-right">แถบหลัก</label>
    		<div class="col-xs-12 col-sm-reset">
    			<?php echo getTabsTree(); ?>
    		</div>
    	</div>
  	</div>

		<div class="col-sm-12 text-center">
			<div class="form-group">
    		<label class="col-sm-2 control-label no-padding-right">&nbsp;</label>
    		<div class="col-sm-4 text-right">
					<?php if($this->pm->can_add) : ?>
		        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> บันทึก</button>
		      <?php endif; ?>
    		</div>
    	</div>

  	</div>
  </div>
</form>



<script src="<?php echo base_url(); ?>scripts/masters/product_tab.js"></script>

<?php $this->load->view('include/footer'); ?>
