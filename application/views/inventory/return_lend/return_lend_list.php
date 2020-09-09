<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
      <?php endif; ?>
    </p>
  </div>
</div>
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-sm-1 col-1-harf padding-5 first">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center search" name="code" value="<?php echo $code; ?>" />
    </div>
    <div class="col-sm-1 col-1-harf padding-5">
      <label>ใบยืมสินค้า</label>
      <input type="text" class="form-control input-sm text-center search" name="lend_code" value="<?php echo $lend_code; ?>" />
    </div>
    <div class="col-sm-2 padding-5">
      <label>ผู้ยืม</label>
      <input type="text" class="form-control input-sm text-center search" name="customer_code" value="<?php echo $customer_code; ?>" />
    </div>
    <div class="col-sm-1 col-1-harf padding-5">
      <label>สถานะ</label>
      <select class="form-control input-sm" name="status" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php if($status == '0'){ echo 'selected'; } ?>>ยังไม่บันทึก</option>
  			<option value="1" <?php echo is_selected(1, $status); ?>>บันทึกแล้ว</option>
  			<option value="2" <?php echo is_selected(2, $status); ?>>ยกเลิก</option>
  		</select>
    </div>
    <div class="col-sm-2 padding-5">
      <label>วันที่</label>
      <div class="input-daterange input-group">
        <input type="text" class="form-control input-sm width-50 from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
        <input type="text" class="form-control input-sm width-50" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
      </div>
    </div>
    <div class="col-sm-1 padding-5">
      <label class="display-block not-show">btn</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-sm-1 padding-5 last">
      <label class="display-block not-show">btn</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>
</form>
<hr class="margin-top-15"/>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-sm-12">
    <p class="pull-right top-p">สถานะ : ว่างๆ = ปกติ,&nbsp;  <span class="blue">NC</span> = ยังไม่บันทึก,&nbsp;  <span class="red">CN</span> = ยกเลิก</p>
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-10 text-center">วันที่</th>
          <th class="width-10">เลขที่เอกสาร</th>
          <th class="width-10">เลขที่บิล</th>
          <th class="width-25">ผู้ยืม</th>
          <th class="width-10 text-right">จำนวน</th>
          <th class="width-10 text-right">มลูค่า</th>
          <th class="width-5 text-center">สถานะ</th>
          <th class=""></th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($docs)) : ?>
<?php   $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($docs as $rs) : ?>
          <tr class="font-size-12" id="row-<?php $rs->code; ?>">
            <td class="middle text-center no"><?php echo $no; ?></td>
            <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE); ?></td>
            <td class="middle"><?php echo $rs->code; ?></td>
            <td class="middle"><?php echo $rs->lend_code; ?></td>
            <td class="middle"><?php echo $rs->customer_name; ?></td>
            <td class="middle text-right"><?php echo number($rs->qty); ?></td>
            <td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
            <td class="middle text-center">
              <?php if($rs->status == 2) : ?>
                <span class="red">CN</span>
              <?php endif;?>
              <?php if($rs->status == 0) : ?>
                <span class="blue">NC</span>
              <?php endif; ?>
            </td>
            <td class="middle text-right">
              <button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
          <?php if($this->pm->can_edit && $rs->status == 0) : ?>
              <button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
          <?php endif; ?>
          <?php if($this->pm->can_delete && $rs->status != 2) : ?>
              <button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
          <?php endif; ?>
            </td>
          </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php else : ?>
        <tr>
          <td colspan="9" class="text-center">
            --- ไม่พบรายการ ---
          </td>
        </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend.js"></script>
<?php $this->load->view('include/footer'); ?>
