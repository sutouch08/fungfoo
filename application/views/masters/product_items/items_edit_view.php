<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> <?php label('back'); ?></button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="margin-bottom-15"/>
<div class="row">
	<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update/{$code}"; ?>">
	<div class="row">
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

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('style'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="style" id="style" class="width-100" value="<?php echo $style_code; ?>" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="style-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('color'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="color" id="color" class="width-100" value="<?php echo $color_code; ?>" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="color-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('size'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="size" id="size" class="width-100" value="<?php echo $size_code; ?>" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="size-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('barcode'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="barcode" id="barcode" class="width-100" value="<?php echo $barcode; ?>" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="barcode-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('cost'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<input type="number" step="any" name="cost" id="cost" class="width-100" value="<?php echo $cost; ?>" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="cost-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('price'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<input type="number" step="any" name="price" id="price" class="width-100" value="<?php echo $price; ?>" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="price-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('unit'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<select class="form-control input-sm" name="unit_code" id="unit_code" required>
					<option value=""><?php label('please_select'); ?></option>
					<?php echo select_unit($unit_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="unit-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('brand'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<select name="brand_code" id="brand" class="form-control" required>
					<option value=""><?php label('please_select'); ?></option>
				<?php echo select_product_brand($brand_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="brand-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('group'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<select name="group_code" id="group" class="form-control input-sm" required>
					<option value=""><?php label('please_select'); ?></option>
				<?php echo select_product_group($group_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('sub_group'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<select name="sub_group_code" id="subGroup" class="form-control" required>
					<option value=""><?php label('please_select'); ?></option>
				<?php echo select_product_sub_group($sub_group_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="subGroup-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('category'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<select name="category_code" id="category" class="form-control" required>
					<option value=""><?php label('please_select'); ?></option>
				<?php echo select_product_category($category_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="category-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('kind'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<select name="kind_code" id="kind" class="form-control" required>
					<option value=""><?php label('please_select'); ?></option>
				<?php echo select_product_kind($kind_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('type'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<select name="type_code" id="type" class="form-control" required>
					<option value=""><?php label('please_select'); ?></option>
				<?php echo select_product_type($type_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('year'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<select name="year" id="year" class="form-control" required>
					<option value=""><?php label('please_select'); ?></option>
				<?php echo select_years($year); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="year-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('count_stock'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="count_stock" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($count_stock,1); ?> />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('allow_sell'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="can_sell" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($can_sell,1); ?> />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">API</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="is_api" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($is_api,1); ?>/>
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"><?php label('active'); ?></label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($active,1); ?> />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label not-show">บันทึก</label>
			<div class="col-xs-12 col-sm-3">
				<button type="button" class="btn btn-sm btn-success btn-block" onclick="checkUpdate()"><i class="fa fa-save"></i> <?php label('save'); ?></button>
				<button type="submit" class="btn btn-sm btn-success hide" id="btn-submit"><i class="fa fa-save"></i> <?php label('save'); ?></button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<input type="hidden" name="old_code" id="old_code" value="<?php echo $code; ?>"/>
	</div>
	</form>
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/masters/items.js"></script>
<?php $this->load->view('include/footer'); ?>
