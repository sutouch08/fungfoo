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
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" class="width-100" required />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ที่อยู่ 1</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="address1" class="width-100" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ที่อยู่ 2</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="address2" class="width-100" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เบอร์โทร</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="phone" class="width-100" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เวลาทำการ</label>
    <div class="col-xs-12 col-sm-3">
      <select class="form-control input-sm input-small" name="open" style="display:inline-block;">
      <?php echo selectTime(); ?>
      </select>
      -
      <select class="form-control input-sm input-small" name="close" style="display:inline-block;">
      <?php echo selectTime(); ?>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เงื่อนไข</label>
    <div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="type">
        <option value="เก็บเงินปลายทาง">เก็บเงินปลายทาง</option>
        <option value="เก็บเงินต้นทาง">เก็บเงินต้นทาง</option>
      </select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
			<label>
				<input type="checkbox" name="in_list" id="in_list" class="ace" />
				<span class="lbl">  แสดงในตัวเลือก</span>
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
</form>

<script src="<?php echo base_url(); ?>scripts/masters/sender.js"></script>
<?php $this->load->view('include/footer'); ?>
