<?php $this->load->view('include/header'); ?>
<div class="row top-row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="title-block"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>เลขที่นโยบาย</label>
    <input type="text" class="form-control input-sm" name="policy_code" id="policy_code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>ชื่อนโยบาย</label>
    <input type="text" class="form-control input-sm" name="policy_name" id="policy_name" value="<?php echo $name; ?>" />
  </div>
  <div class="col-sm-2 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="active" id="active" onchange="getSearch()">
      <option value="2" <?php echo is_selected(2, $active); ?>>ทั้งหมด</option>
      <option value="1" <?php echo is_selected(1, $active); ?>>ใช้งาน</option>
      <option value="0" <?php echo is_selected(0, $active); ?>>ไม่ใช้งาน</option>
    </select>
  </div>

  <div class="col-sm-2 padding-5">
    <label>ช่วงวันที่</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 text-center from-date" name="start_date" id="fromDate" value="<?php echo $start_date; ?>" />
			<input type="text" class="form-control input-sm width-50 text-center" name="end_date" id="toDate" value="<?php echo $end_date; ?>" />
		</div>

  </div>

  <div class="col-sm-2 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-2 padding-5 last">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
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
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-15 middle">รหัส</th>
					<th class="width-40 middle">ชื่อ</th>
					<th class="width-10 middle text-center">เริ่มต้น</th>
					<th class="width-10 middle text-center">สิ้นสุด</th>
					<th class="width-5 middle text-center">สถานะ</th>
					<th class=""></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $rs->id; ?>">
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->start_date); ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->end_date); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="text-right">
							
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->id; ?>', '<?php echo addslashes($rs->code); ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="7" class="text-center">--- No content ---</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/discount/policy/policy.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/policy/policy_list.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/policy/policy_add.js"></script>

<?php $this->load->view('include/footer'); ?>
