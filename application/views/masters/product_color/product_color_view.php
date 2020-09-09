<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="title-block"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 col-xs-4 padding-5 first">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 col-xs-4 padding-5">
    <label>สี</label>
    <input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-2 col-xs-4 padding-5">
    <label>Gen code</label>
    <input type="text" class="form-control input-sm" name="gen_code" value="<?php echo $gen_code; ?>" />
  </div>
	<div class="col-sm-2 col-xs-4 padding-5">
		<label>กลุ่มสี</label>
		<select class="form-control input-sm" name="color_group" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_color_group($group_code); ?>
		</select>
	</div>

	<div class="col-sm-2 col-xs-4 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" id="status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>ใช้งาน</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>ไม่ใช้งาน</option>
		</select>
  </div>

  <div class="col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 col-xs-6 padding-5 last">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-15 middle">รหัส</th>
					<th class="width-15 middle">สี</th>
					<th class="width-15 middle">กลุ่มสี</th>
					<th class="width-15 middle">Gen code</th>
					<th class="width-15 middle text-center">สินค้า</th>
					<th class="width-5 middle text-center">สถานะ</th>
					<th class=""></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->group_name; ?></td>
						<td class="middle"><?php echo $rs->gen_code; ?></td>
						<td class="middle text-center"><?php echo number($rs->menber); ?></td>
						<td class="middle text-center" id="<?php echo $rs->code; ?>">
							<?php if($this->pm->can_edit): ?>
								<span class="pointer" onclick="toggleActive(<?php echo $rs->active; ?>, '<?php echo $rs->code; ?>')">
									<?php echo is_active($rs->active); ?>
								</span>
							<?php else : ?>
								<?php echo is_active($rs->active); ?>
							<?php endif; ?>
						</td>
						<td class="text-right">
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

<script src="<?php echo base_url(); ?>scripts/masters/product_color.js"></script>

<?php $this->load->view('include/footer'); ?>
