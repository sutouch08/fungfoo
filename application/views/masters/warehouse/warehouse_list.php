<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
				<?php if($this->pm->can_add): ?>
				<button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> <?php label('add_new'); ?></button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label><?php label('code'); ?></label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-3 padding-5">
    <label><?php label('name'); ?></label>
    <input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label><?php label('wh_type'); ?></label>
    <select class="form-control input-sm filter" name="role" id="role" onchange="getSearch()">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_warehouse_role($role); ?>
		</select>
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label class="display-block not-show">buton</label>
		<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label class="display-block not-show">buton</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>

</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover border-1">
			<thead>
				<tr style="font-size:11px;">
					<th class="width-5 middle text-center"><?php label('num'); ?></th>
					<th class="width-10 middle"><?php label('code'); ?></th>
					<th class="width-25 middle"><?php label('name'); ?></th>
					<th class="width-10 middle"><?php label('wh_type'); ?></th>
					<th class="width-5 middle text-center"><?php label('zone'); ?></th>
					<th class="width-5 middle text-center"><?php label('sell'); ?></th>
					<th class="width-5 middle text-center"><?php label('pick'); ?></th>
					<th class="width-5 middle text-center"><?php label('negative'); ?></th>
					<th class="width-5 middle text-center"><?php label('active'); ?></th>
					<th class="width-15 middle text-center"><?php label('edit_by'); ?></th>
					<th class=""></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($list)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($list as $rs) : ?>
					<tr style="font-size:11px;" id="row-<?php echo $rs->code; ?>">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->role_name; ?></td>
						<td class="middle text-center"><?php echo number($rs->zone_count); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->sell); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->prepare); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->auz); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle text-center"><?php echo $rs->update_user; ?></td>
						<td class="text-right">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>')" <?php echo ($rs->zone_count > 0 ? 'disabled' :''); ?>>
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

<script src="<?php echo base_url(); ?>scripts/masters/warehouse.js"></script>

<?php $this->load->view('include/footer'); ?>
