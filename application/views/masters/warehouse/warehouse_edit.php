<?php $this->load->view('include/header'); ?>
<?php
	$sell_yes = $ds->sell == 1 ? 'btn-success' : '';
	$sell_no = $ds->sell == 0 ? 'btn-danger' : '';
	$prepare_yes = $ds->prepare == 1 ? 'btn-success' : '';
	$prepare_no = $ds->prepare == 0 ? 'btn-danger' : '';
	$auz_yes = $ds->auz == 1 ? 'btn-success' : '';
	$auz_no = $ds->auz == 0 ? 'btn-danger' : '';
	$active_yes = $ds->active == 1 ? 'btn-success' : '';
	$active_no = $ds->active == 0 ? 'btn-danger' : '';
 ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> <?php label('back'); ?></button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="title-block"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update"; ?>">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('code'); ?></label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" class="form-control input-sm code" name="code" id="code" value="<?php echo $ds->code; ?>" />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('name'); ?></label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $ds->name; ?>" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right"><?php label('wh_type'); ?></label>
 	 <div class="col-xs-12 col-sm-3">
 		 <select class="form-control input-sm" name="role" id="role" required>
 		 	<option value=""><?php label('please_select'); ?></option>
			<?php echo select_warehouse_role($ds->role); ?>
 		 </select>
 	 </div>
	 <div class="help-block col-xs-12 col-sm-reset inline red" id="role-error"></div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right"><?php label('allow_sell'); ?></label>
 	 <div class="col-xs-12 col-sm-2">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 <?php echo $sell_yes; ?>" id="btn-sell-yes" onclick="toggleSell(1)"><?php label('yes'); ?></button>
			<button type="button" class="btn btn-sm width-50 <?php echo $sell_no; ?>" id="btn-sell-no" onclick="toggleSell(0)"><?php label('no'); ?></button>
 		</div>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right"><?php label('allow_prepare'); ?></label>
 	 <div class="col-xs-12 col-sm-2">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 <?php echo $prepare_yes; ?>" id="btn-prepare-yes" onclick="togglePrepare(1)"><?php label('yes'); ?></button>
			<button type="button" class="btn btn-sm width-50 <?php echo $prepare_no; ?>" id="btn-prepare-no" onclick="togglePrepare(0)"><?php label('no'); ?></button>
 		</div>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right"><?php label('allow_negative'); ?></label>
 	 <div class="col-xs-12 col-sm-2">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 <?php echo $auz_yes; ?>" id="btn-auz-yes" onclick="toggleAuz(1)"><?php label('yes'); ?></button>
			<button type="button" class="btn btn-sm width-50 <?php echo $auz_no; ?>" id="btn-auz-no" onclick="toggleAuz(0)"><?php label('no'); ?></button>
 		</div>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right"><?php label('active'); ?></label>
 	 <div class="col-xs-12 col-sm-2">
 		<div class="btn-group width-100">
 			<button type="button" class="btn btn-sm width-50 <?php echo $active_yes; ?>" id="btn-active-yes" onclick="toggleActive(1)"><?php label('yes'); ?></button>
			<button type="button" class="btn btn-sm width-50 <?php echo $active_no; ?>" id="btn-active-no" onclick="toggleActive(0)"><?php label('no'); ?></button>
 		</div>
 	 </div>
  </div>



	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="checkUpdate()"><i class="fa fa-save"></i> <?php label('save'); ?></button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" name="old_code" id="old_code" value="<?php echo $ds->code; ?>">
	<input type="hidden" name="old_name" id="old_name" value="<?php echo $ds->name; ?>">
	<input type="hidden" name="sell" id="sell" value="<?php echo $ds->sell; ?>">
	<input type="hidden" name="prepare" id="prepare" value="<?php echo $ds->prepare; ?>">
	<input type="hidden" name="auz" id="auz" value="<?php echo $ds->auz; ?>">
	<input type="hidden" name="active" id="active" value="<?php echo $ds->active; ?>">
</form>

<script src="<?php echo base_url(); ?>scripts/masters/warehouse.js"></script>
<?php $this->load->view('include/footer'); ?>
