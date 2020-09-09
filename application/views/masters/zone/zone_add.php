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
<hr/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home.'/add'; ?>">
<div class="row">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('code'); ?></label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100 code" value="" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('name'); ?></label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" value="" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('warehouse'); ?></label>
    <div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="warehouse" id="warehouse">
				<option value=""><?php label('please_select'); ?></option>
				<?php echo select_warehouse(); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="warehouse-error"></div>
  </div>

	<div class="divider-hidden">

	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right"></label>
		<div class="col-xs-12 col-sm-3">
			<p class="pull-right">
				<button type="button" class="btn btn-sm btn-success" onclick="checkAdd()"><i class="fa fa-save"></i> <?php label('save'); ?></button>
			</p>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline">
			&nbsp;
		</div>
	</div>
</div>
</form>

<script src="<?php echo base_url(); ?>scripts/masters/zone.js"></script>
<?php $this->load->view('include/footer'); ?>
