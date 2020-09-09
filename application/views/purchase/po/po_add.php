<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
       <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> <?php label('back'); ?></button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home.'/add'; ?>">
  <div class="row">
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5 first">
      <label><?php label('doc_num'); ?></label>
      <input type="text" class="form-control input-sm text-center" value="" readonly>
    </div>
    <div class="col-sm-1 col-xs-6 padding-5">
      <label><?php label('date'); ?></label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" readonly required>
    </div>
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
      <label><?php label('vender_code'); ?></label>
      <input type="text" class="form-control input-sm text-center" name="vender_code" id="vender_code" value="" autofocus required>
    </div>
    <div class="col-sm-3 col-xs-6 padding-5">
      <label><?php label('vender_name'); ?></label>
      <input type="text" class="form-control input-sm" name="vender_name" id="vender_name" value="" required>
    </div>
    <div class="col-sm-1 col-xs-6 padding-5">
      <label><?php label('require_date'); ?></label>
      <input type="text" class="form-control input-sm text-center" name="require_date" id="require_date" value="<?php echo date('Y-m-d'); ?>" readonly required>
    </div>
    <div class="col-sm-3 col-xs-12 padding-5">
      <label><?php label('remark'); ?></label>
      <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
    </div>
    <div class="col-sm-1 col-xs-4 padding-5 last">
      <label class="display-block not-show">add</label>
      <button type="submit" class="btn btn-xs btn-success btn-block"><i class="fa fa-plus"></i> <?php label('add'); ?></button>
    </div>
  </div>
</form>
<hr class="margin-top-15">
<script src="<?php echo base_url(); ?>scripts/purchase/po.js"></script>
<script src="<?php echo base_url(); ?>scripts/purchase/po_add.js"></script>
<?php $this->load->view('include/footer'); ?>
