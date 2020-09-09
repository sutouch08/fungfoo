<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6">
			<p class="pull-right top-p">
			<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
			<?php endif; ?>
			</p>
		</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>อ้างถึง</label>
    <input type="text" class="form-control input-sm search" name="reference" value="<?php echo $reference; ?>" />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>พนักงาน</label>
    <input type="text" class="form-control input-sm search" name="user" value="<?php echo $user; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm search" name="remark" value="<?php echo $remark; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>สถานะ</label>
		<select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all" <?php echo is_selected($status, 'all'); ?>>ทั้งหมด</option>
			<option value="0" <?php echo is_selected($status, '0'); ?>>ยังไม่บันทึก</option>
      <option value="1" <?php echo is_selected($status, '1'); ?>>บันทึกแล้ว</option>
      <option value="2" <?php echo is_selected($status, '2'); ?>>ยกเลิก</option>
		</select>
  </div>

	<div class="col-sm-2 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>

  </div>

  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-sm-12 first last">
		<p class="pull-right">
      สถานะ : ว่างๆ = ปกติ, &nbsp;
      <span class="red">CN</span> = ยกเลิก, &nbsp;
      <span class="blue">NC</span> = ยังไม่บันทึก
    </p>
  </div>
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-10">วันที่</th>
          <th class="width-15">เลขที่เอกสาร</th>
          <th class="width-15">อ้างถึง</th>
          <th class="width-15">พนักงาน</th>
          <th class="">หมายเหตุ</th>
          <th class="width-5 text-center">สถานะ</th>
					<th class="width-10"></th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($list))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($list as $rs)  : ?>

        <tr class="font-size-12">

          <td class="middle text-center"><?php echo $no; ?></td>

          <td class="middle"><?php echo thai_date($rs->date_add); ?></td>

          <td class="middle"><?php echo $rs->code; ?></td>

          <td class="middle"><?php echo $rs->reference; ?></td>

          <td class="middle hide-text"><?php echo get_display_name($rs->user); ?></td>

          <td class="middle hide-text"><?php echo $rs->remark; ?></td>

          <td class="middle text-center hide-text">
						<?php if($rs->status == 0) : ?>
							<span class="blue">NC</span>
						<?php endif; ?>
						<?php if($rs->status == 2) : ?>
							<span class="red">CN</span>
						<?php endif; ?>
          </td>

					<td class="middle text-right">
						<button type="button" class="btn btn-mini btn-info" onclick="goDetail('<?php echo $rs->code; ?>')">
							<i class="fa fa-eye"></i>
						</button>

						<?php if($rs->status == 0 && $this->pm->can_edit) : ?>
							<button type="button" class="btn btn-mini btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')">
								<i class="fa fa-pencil"></i>
							</button>
						<?php endif; ?>

						<?php if($rs->status != 2 && $this->pm->can_delete) : ?>
							<button type="button" class="btn btn-mini btn-danger" onclick="goCancle('<?php echo $rs->code; ?>')">
								<i class="fa fa-trash"></i>
							</button>
						<?php endif; ?>
					</td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_list.js"></script>

<?php $this->load->view('include/footer'); ?>
