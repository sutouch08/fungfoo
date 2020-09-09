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
				<?php //if($can_upload == 1) : ?>
					<!--<button type="button" class="btn btn-sm btn-purple" onclick="getUploadFile()">นำเข้าออเดอร์</button> -->
				<?php //endif;?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>

      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label>พนักงาน</label>
    <input type="text" class="form-control input-sm search" name="user" value="<?php echo $user; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label>เลขที่อ้างอิง</label>
		<input type="text" class="form-control input-sm search" name="reference" value="<?php echo $reference; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label>เลขที่จัดส่ง</label>
		<input type="text" class="form-control input-sm search" name="shipCode" value="<?php echo $ship_code; ?>" />
  </div>

	<div class="col-sm-2 padding-5 last">
    <label>ช่องทางการขาย</label>
		<select class="form-control input-sm" name="channels" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_channels($channels); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5 first">
    <label>ช่องทางการชำระเงิน</label>
		<select class="form-control input-sm" name="payment" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_payment_method($payment); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
		<label>สถานะการชำระเงิน</label>
		<select class="form-control input-sm" name="is_paid" onchange="getSearch()">
			<option value="all" <?php echo is_selected('all', $is_paid); ?>>ทั้งหมด</option>
			<option value="paid" <?php echo is_selected('paid', $is_paid); ?>>จ่ายแล้ว</option>
			<option value="not_paid" <?php echo is_selected('not_paid', $is_paid); ?>>ยังไม่จ่าย</option>
		</select>
	</div>

  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>

<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<?php $sort_date = $order_by === 'date_add' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''; ?>
<?php $sort_code = $order_by === 'code' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''; ?>

<div class="row">
	<div class="col-sm-12 table-responsive">
		<table class="table table-striped table-bordered table-hover dataTable">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-10 middle text-center sorting <?php echo $sort_date; ?>" id="sort_date_add" onclick="sort('date_add')">วันที่</th>
					<th class="width-15 middle sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">เลขที่เอกสาร</th>
					<th class="middle">ลูกค้า</th>
					<th class="width-10 middle">ยอดเงิน</th>
					<th class="width-10 middle">ช่องทางขาย</th>
					<th class="width-10 middle">การชำระเงิน</th>
					<th class="width-10 middle">สถานะ</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
						<?php $cod_txt = ($rs->payment_role == 4 && $rs->state != 9) ? ($rs->is_paid == 1 ? '' : '<span class="label label-danger">รอเงินเข้า</span>') : ''; ?>
						<?php $ref = empty($rs->reference) ? '' :' ['.$rs->reference.']'; ?>
						<?php $c_ref = empty($rs->customer_ref) ? '' : ' ['.$rs->customer_ref.']'; ?>
            <tr id="row-<?php echo $rs->code; ?>" style="<?php echo state_color($rs->state, $rs->status, $rs->is_expired); ?>">
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $no; ?></td>
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->code.$ref . $cod_txt; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->customer_name . $c_ref; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo number($rs->total_amount, 2); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->channels_name; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->payment_name; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->state_name; ?></td>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php
if($can_upload == 1) :
	 $this->load->view('orders/import_order');
endif;
?>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js"></script>

<?php $this->load->view('include/footer'); ?>
