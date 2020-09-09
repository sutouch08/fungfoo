<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update_style"; ?>">
<div class="row">
	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">รหัสรุ่นสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<label class="form-control width-100" disabled="disabled"><?php echo $style->code; ?></label>
			<input type="hidden" name="code" id="code" value="<?php echo $style->code; ?>" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ชื่อรุ่นสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" value="<?php echo $style->name; ?>" required />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ทุน</label>
		<div class="col-xs-12 col-sm-3">
			<input type="number" name="cost" id="cost" class="width-100" value="<?php echo $style->cost; ?>" required />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ราคา</label>
		<div class="col-xs-12 col-sm-3">
			<input type="number" name="price" id="price" class="width-100" value="<?php echo $style->price; ?>" required />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ยี่ห้อ</label>
		<div class="col-xs-12 col-sm-3">
			<select name="brand_code" id="brand" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_brand($style->brand_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="brand-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">กลุ่มสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="group_code" id="group" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_group($style->group_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">กลุ่มย่อยสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="sub_group_code" id="subGroup" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_sub_group($style->sub_group_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="subGroup-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">หมวดหมู่สินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="category_code" id="category" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_category($style->category_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="category-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ประเภทสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="kind_code" id="kind" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_kind($style->kind_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ชนิดสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="type_code" id="type" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_type($style->type_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ปีสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="year" id="year" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_years($style->year); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="year-error"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">แถบแสดงสินค้า</label>
		<div class="col-xs-12 col-sm-reset">
			<?php echo productTabsTree($style->code); ?>
		</div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">นับสต็อก</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="count_stock" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($style->count_stock, 1); ?> />
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">อนุญาติให้ขาย</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="can_sell" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($style->can_sell, 1); ?> />
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">เปิดใช้งาน</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($style->active, 1); ?> />
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label not-show">บันทึก</label>
		<div class="col-xs-12 col-sm-3">
			<button type="submit" class="btn btn-sm btn-success btn-block"><i class="fa fa-save"></i> บันทึก</button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>
</div>

<input type="hidden" id="style" value="<?php echo $style->code; ?>" />
</form>
