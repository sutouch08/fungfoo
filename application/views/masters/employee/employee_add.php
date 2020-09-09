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
    <label class="col-sm-3 control-label no-padding-right"><?php label('code'); ?></label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100 code" value="<?php echo $code; ?>" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('name'); ?></label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" value="<?php echo $name; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<?php
		$on = $active == 1 ? 'btn-success' : '';
		$off = $active == 0 ? 'btn-danger' : '';
	?>
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('status'); ?></label>
    <div class="col-xs-12 col-sm-3">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $on; ?> width-50" id="active-on" onclick="toggleActive(1)"><?php label('active'); ?></button>
				<button type="button" class="btn btn-sm <?php echo $off; ?> width-50" id="active-off" onclick="toggleActive(0)"><?php label('deactive'); ?></button>
				<input type="hidden" id="active" name="active" value="<?php echo $active; ?>">
			</div>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> <?php label('save'); ?></button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
</form>

<script src="<?php echo base_url(); ?>scripts/masters/employee.js"></script>
<?php $this->load->view('include/footer'); ?>
