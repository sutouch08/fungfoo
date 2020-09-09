<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6">
			<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
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
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" onchange="getSearch()">
      <option value="">ทั้งหมด</option>
      <?php echo select_channels($channels); ?>
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
<?php //echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-15 middle">เลขที่เอกสาร</th>
					<th class="width-30 middle">ลูกค้า</th>
          <th class="width-10 middle">ช่องทาง</th>
					<th class="width-15 middle text-center">วันที่</th>
					<th class="middle"></th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name; ?>
            <tr id="row-<?php echo $rs->code; ?>">
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $customer_name; ?></td>
              <td class="middle"><?php echo $rs->channels_name; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE,'/'); ?></td>
              <td class="middle text-right">
          <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
                <button type="button" class="btn btn-xs btn-info" onClick="goQc('<?php echo $rs->code; ?>')">ตรวจสินค้า</button>
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

<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_list.js"></script>

<?php $this->load->view('include/footer'); ?>
