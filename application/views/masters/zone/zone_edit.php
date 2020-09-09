<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="margin-bottom-15"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update"; ?>">
<div class="row">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('code'); ?></label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100 code" value="<?php echo $ds->code; ?>" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('name'); ?></label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" value="<?php echo $ds->name; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"><?php label('warehouse'); ?></label>
    <div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="warehouse" id="warehouse">
				<option value=""><?php label('please_select'); ?></option>
				<?php echo select_warehouse($ds->warehouse_code); ?>
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
				<button type="button" class="btn btn-sm btn-success" onclick="checkUpdate()"><i class="fa fa-save"></i> <?php label('update'); ?></button>
			</p>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline">
			&nbsp;
		</div>
	</div>
</div>
	<input type="hidden" name="old_code" id="old_code" value="<?php echo $ds->code; ?>">
	<input type="hidden" name="old_name" id="old_name" value="<?php echo $ds->name; ?>">
</form>
<hr class="margin-top-10 margin-bottom-15">
<div class="row">
	<div class="col-sm-4 padding-5 first">
		<input type="text" class="form-control input-sm" id="search-box" placeholder="<?php label('search'); label('customer'); ?>" autofocus>
	</div>
	<div class="col-sm-1 padding-5">
		<button type="button" class="btn btn-xs btn-primary" onclick="addCustomer()">
			<i class="fa fa-plus"></i> <?php label('customer'); ?>
		</button>
	</div>
</div>
<hr class="margin-top-10 margin-bottom-15">
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 text-center"><?php label('Num'); ?></th>
					<th class="width-15"><?php label('customer_code'); ?></th>
					<th class=""><?php label('customer_name'); ?></th>
					<th class="width-10"></th>
				</tr>
			</thead>
			<tbody id="cust-table">
<?php if(!empty($customers)) : ?>
	<?php $no = 1; ?>
	<?php foreach($customers as $rs) : ?>
				<tr id="row-<?php echo $rs->id; ?>">
					<td class="middle text-center"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->customer_code; ?></td>
					<td class="middle"><?php echo $rs->customer_name; ?></td>
					<td class="middle text-right">
			<?php if($this->pm->can_edit) : ?>
						<button type="button" class="btn btn-xs btn-danger" onclick="deleteCustomer(<?php echo $rs->id; ?>, '<?php echo $rs->customer_code; ?>')">
							<i class="fa fa-trash"></i>
						</button>
			<?php endif; ?>
					</td>
				</tr>
		<?php $no++; ?>
	<?php endforeach; ?>
<?php else : ?>
				<tr>
					<td colspan="4" class="text-center">--- No customer ---</td>
				</tr>
<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<input type="hidden" id="customer_code" value="" >
<input type="hidden" id="zone_code" value="<?php echo $ds->code; ?>">
<script src="<?php echo base_url(); ?>scripts/masters/zone.js"></script>
<?php $this->load->view('include/footer'); ?>
