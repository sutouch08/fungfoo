<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="title-block"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update/".$id; ?>">

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ลูกค้า</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="customer_name" id="customer_name" value="<?php echo $customer_name; ?>" class="width-100" required />
			<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $customer_code; ?>">
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ขนส่งหลัก</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="main_sender" id="main_sender" class="width-100" value="<?php echo $main_sender_name; ?>" required />
			<input type="hidden" name="main_sender_id" id="main_sender_id" value="<?php echo $main_sender; ?>">
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ขนส่งสำรอง 1</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="second_sender" id="second_sender" class="width-100" value="<?php echo $second_sender_name; ?>" />
			<input type="hidden" name="second_sender_id" id="second_sender_id" value="<?php echo $second_sender; ?>">
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ขนส่งสำรอง 2</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="third_sender" id="third_sender" class="width-100" value="<?php echo $third_sender_name; ?>" />
			<input type="hidden" name="third_sender_id" id="third_sender_id" value="<?php echo $third_sender; ?>">
    </div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
</form>

<script src="<?php echo base_url(); ?>scripts/masters/transport.js"></script>
<?php $this->load->view('include/footer'); ?>
