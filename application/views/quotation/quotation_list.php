<?php $this->load->view('include/header'); ?>
<?php $can_upload = getConfig('ALLOW_UPLOAD_ORDER'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> สร้างใหม่</button>
      <?php endif; ?>

      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search code" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer_code; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>ผู้ติดต่อ</label>
    <input type="text" class="form-control input-sm search" name="contact" value="<?php echo $contact; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>พนักงาน</label>
    <input type="text" class="form-control input-sm search" name="user" value="<?php echo $user; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>อ้างอิง</label>
		<input type="text" class="form-control input-sm search" name="reference" value="<?php echo $reference; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
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
	<div class="col-sm-12 table-responsive">
		<p  class="pull-right top-p">
			ว่างๆ = ปกติ, &nbsp; <span class="blue">NC</span> = ยังไม่บันทึก, &nbsp; <span class="red">CN</span> = ยกเลิก
		</p>
		<table class="table table-striped table-hover border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-10 middle text-center">วันที่</th>
					<th class="width-15 middle">เลขที่เอกสาร</th>
					<th class="middle">ลูกค้า</th>
					<th class="width-10 middle">ยอดรวม</th>
					<th class="width-10 middle">สถานะ</th>
					<th class="width-10 middle"></th>
				</tr>
			</thead>
			<tbody>
	<?php if(!empty($data)) : ?>
		<?php $no = $this->uri->segment(4) + 1; ?>
		<?php foreach($data as $rs) : ?>
			<tr>
				<td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
				<td class="middle"><?php echo $rs->code; ?></td>
				<td class="middle"><?php echo $rs->customer_name; ?></td>
				<td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
				<td class="middle text-center">
					<?php if($rs->status == 2) : ?>
						<span class="red">CN</span>
					<?php endif; ?>
					<?php if($rs->status == 0) : ?>
						<span class="blue">NC</span>
					<?php endif; ?>
				</td>
				<td class="middle text-right">
					<button type="button" class="btn btn-minier btn-info" onclick="goDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
					<?php if($rs->status == 0 && $this->pm->can_edit) : ?>
						<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
					<?php endif; ?>
					<?php if($rs->status != 2 && $this->pm->can_delete) : ?>
						<button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>', <?php echo $rs->status; ?>)"><i class="fa fa-trash"></i></button>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php else : ?>
			<tr>
				<td colspan="7" class="text-center">--- ไม่พบข้อมูล ---</td>
			</tr>
	<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/quotation/quotation.js"></script>

<?php $this->load->view('include/footer'); ?>
