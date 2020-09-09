<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update"; ?>">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('code'); ?></label>
    <div class="col-xs-12 col-sm-4">
      <input type="text" name="code" id="code" class="form-control input-sm code" value="<?php echo $ds->code; ?>" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('name'); ?></label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="name" id="name" class="form-control input-sm" value="<?php echo $ds->name; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('id'); ?>/<?php label('tax_id'); ?></label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="Tax_Id" id="Tax_Id" class="form-control input-sm" value="<?php echo $ds->Tax_Id; ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('customer_group'); ?></label>
    <div class="col-xs-12 col-sm-4">
			<select name="group" id="group" class="form-control" required>
				<option value=""><?php label('choose'); ?></option>
				<?php echo select_customer_group($ds->group_code); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('customer_kind'); ?></label>
    <div class="col-xs-12 col-sm-4">
			<select name="kind" id="kind" class="form-control" required>
				<option value=""><?php label('choose'); ?></option>
				<?php echo select_customer_kind($ds->kind_code); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('customer_type'); ?></label>
    <div class="col-xs-12 col-sm-4">
			<select name="type" id="type" class="form-control" required>
				<option value=""><?php label('choose'); ?></option>
				<?php echo select_customer_type($ds->type_code); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
  </div>



	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('customer_class'); ?></label>
    <div class="col-xs-12 col-sm-4">
			<select name="class" id="class" class="form-control" required>
				<option value=""><?php label('choose'); ?></option>
				<?php echo select_customer_class($ds->class_code); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="class-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('customer_area'); ?></label>
    <div class="col-xs-12 col-sm-4">
			<select name="area" id="area" class="form-control" required>
				<option value=""><?php label('choose'); ?></option>
				<?php echo select_customer_area($ds->area_code); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
  </div>


	<div class="form-group">
	 <label class="col-sm-3 control-label no-padding-right"><?php label('saleman'); ?></label>
	 <div class="col-xs-12 col-sm-4">
		 <select name="sale" id="sale" class="form-control">
			 <?php echo select_sale($ds->sale_code); ?>
		 </select>
	 </div>
	</div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เครดิตเทอม</label>
    <div class="col-xs-12 col-sm-4">
			<input type="number" name="credit_term" id="credit_term" class="form-control input-sm width-50" value="<?php echo $ds->credit_term; ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">วงเงินเครติด</label>
    <div class="col-xs-12 col-sm-4">
			<input type="number" name="CreditLine" id="CreditLine" class="form-control input-sm width-50" value="<?php echo $ds->amount; ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">วงเงินใช้ไป</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" class="form-control input-sm width-50" value="<?php echo number($ds->used,2); ?>" disabled/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">วงเงินคงเหลือ</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" class="form-control input-sm width-50 " value="<?php echo number($ds->balance, 2); ?>" disabled/>
    </div>
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
	<input type="hidden" name="customers_code" id="customers_code" value="<?php echo $ds->code; ?>" />
	<input type="hidden" name="customers_name" value="<?php echo $ds->name; ?>" />
</form>
