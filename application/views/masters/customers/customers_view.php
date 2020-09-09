<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> <?php label('add_new'); ?></button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="title-block"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
    <label><?php label('code'); ?></label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label><?php label('name'); ?></label>
    <input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label><?php label('customer_group'); ?></label>
    <select class="form-control input-sm filter" name="group" id="customer_group">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_customer_group($group); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label><?php label('customer_kind'); ?></label>
    <select class="form-control input-sm filter" name="kind" id="customer_kind">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_customer_kind($kind); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label><?php label('customer_type'); ?></label>
    <select class="form-control input-sm filter" name="type" id="customer_type">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_customer_type($type); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label><?php label('customer_class'); ?></label>
    <select class="form-control input-sm filter" name="class" id="customer_class">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_customer_class($class); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label><?php label('customer_area'); ?></label>
    <select class="form-control input-sm filter" name="area" id="customer_area">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_customer_area($area); ?>
		</select>
  </div>

  <div class="col-sm-1 col-1-harf padding-5 last">
    <label class="display-block not-show">buton</label>
		<div class="btn-group width-100">
			<button type="submit" class="btn btn-sm btn-primary width-50"><?php label('search'); ?></button>
			<button type="button" class="btn btn-sm btn-warning width-50" onclick="clearFilter()"><?php label('reset'); ?></button>
		</div>
  </div>

</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">No.</th>
					<th class="width-10 middle"><?php label('code'); ?></th>
					<th class="width-35 middle"><?php label('name'); ?></th>
					<th class="width-10 middle"><?php label('customer_group'); ?></th>
					<th class="width-10 middle"><?php label('customer_kind'); ?></th>
					<th class="width-10 middle"><?php label('customer_type'); ?></th>
					<th class="width-10 middle"><?php label('customer_class'); ?></th>
					<th class="width-10"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr style="font-size:11px;">
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->group; ?></td>
						<td class="middle"><?php echo $rs->kind; ?></td>
						<td class="middle"><?php echo $rs->type; ?></td>
						<td class="middle"><?php echo $rs->class; ?></td>
						<td class="text-right">
							<button type="button" class="btn btn-mini btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')">
								<i class="fa fa-eye"></i>
							</button>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/customers.js"></script>

<?php $this->load->view('include/footer'); ?>
