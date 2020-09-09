<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"> <?php echo $this->title; ?> </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
				<?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="title-block"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm search-box" name="name" value="<?php echo $name; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="active" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $active); ?>>ใช้งาน</option>
      <option value="0" <?php echo is_selected('0', $active); ?>>ไม่ใช้งาน</option>
    </select>
  </div>

  <div class="col-sm-2 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-2 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-10 middle">รหัส</th>
					<th class="middle">พนักงานขาย</th>
					<th class="width-10 middle">Status</th>
					<th class="width-10 middle"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<?php $active = $rs->active == 1 ? '' : 'hide'; ?>
					<?php $disActive = $rs->active == 0 ? '' : 'hide'; ?>
					<tr id="<?php echo $rs->code; ?>">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle">
							<span class="label label-sm label-success arrowed <?php echo $active; ?>" id="label-active-<?php echo $rs->code; ?>">Actived</span>
							<span class="label labes-sm label-warning arrowed <?php echo $disActive; ?>" id="label-disActive-<?php echo $rs->code; ?>">Suspended</span>
						</td>
						<td class="middle text-right">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>')">
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

<script src="<?php echo base_url(); ?>scripts/masters/saleman.js"></script>

<?php $this->load->view('include/footer'); ?>
