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
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update"; ?>">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100 code" value="<?php echo $code; ?>" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" value="<?php echo $name; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ประเภท</label>
    <div class="col-xs-12 col-sm-3">
			<select name="role" id="role" class="form-control input-sm" required>
				<option value="">โปรดเลือก</option>
				<?php echo select_payment_role($role); ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
			<label>
				<input type="checkbox" class="ace" name="term" id="term" value="1" <?php echo is_checked(1, $term); ?> />
				<span class="lbl">&nbsp; &nbsp;เครติดเทอม</span>
			</label>
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
	<input type="hidden" name="payment_methods_code" value="<?php echo $code; ?>" />
	<input type="hidden" name="payment_methods_name" value="<?php echo $name; ?>" />
</form>

<script src="<?php echo base_url(); ?>scripts/masters/payment_methods.js"></script>
<?php $this->load->view('include/footer'); ?>
