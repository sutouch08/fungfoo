<?php $this->load->view('include/header'); ?>
<div class="row top-row">
  <div class="col-sm-6 col-xs-6">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-sm-6 col-xs-6">
    <p class="pull-right top-p">
<?php if( $this->pm->can_add ) : ?>
      <button type="button" class="btn btn-xs btn-success" onclick="goAdd()">
        <i class="fa fa-plus"></i> เพิ่มใหม่
      </button>
<?php endif; ?>
    </p>
  </div>
</div>

<hr/>

<form id="searchForm" method="post">
<div class="row">
  <div class="col-sm-2 col-xs-6 padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search text-center" name="code" id="code" value="<?php echo $code; ?>" autofocus />
  </div>

  <div class="col-sm-3 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search text-center" name="customer" id="customer" value="<?php echo $customer; ?>" />
  </div>

  <div class="col-sm-2 col-xs-6 padding-5 last">
    <label class="display-block">วันที่</label>
    <div class="input-daterange input-group">
        <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>">
        <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>">
      </div>
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" id="status" onchange="getSearch()">
      <option value="all" <?php echo is_selected($status, 'all'); ?>>ทั้งหมด</option>
      <option value="0" <?php echo is_selected($status, '0'); ?>>ยังไม่บันทึก</option>
      <option value="1" <?php echo is_selected($status, 1); ?>>บันทึกแล้ว</option>
      <option value="2" <?php echo is_selected($status, 2); ?>>ยกเลิก</option>
    </select>
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>ชำระโดย</label>
    <select class="form-control input-sm" name="pay_type" id="pay_type" onchange="getSearch()">
      <option value="">ทั้งหมด</option>
      <?php echo select_payment_type($pay_type); ?>
    </select>
  </div>


  <div class="col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">search</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
  </div>

  <div class="col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">Reset</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
</form>
<hr class="margin-top-15 margin-bottom-15"/>

<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-sm-12 col-xs-12 last">
    <p class="pull-right top-p">
      <span>ว่าง</span><span class="margin-right-15"> = ปกติ</span>
      <span class="blue">NC</span><span class="margin-right-15"> = ยังไม่บันทึก</span>
      <span class="red">CN</span><span class=""> = ยกเลิก</span>
    </p>
  </div>
</div>

<div class="row">
   <div class="col-sm-12 col-xs-12 first last table-responsive">
     <table class="table table-striped border-1">
       <thead>
         <tr class="font-size-12">
           <th class="width-5 text-center">ลำดับ</th>
           <th class="width-10">วันที่</th>
           <th class="width-15">เลขที่เอกสาร</th>
           <th class="width-30">ลูกค้า</th>
           <th class="width-10 text-right">มูลค่า</th>
           <th class="width-5 text-center">สถานะ</th>
           <th class="width-10 text-center">ชำระโดย</th>
           <th class=""></th>
         </tr>
       </thead>
       <tbody>
<?php if(!empty($docs)) : ?>
<?php   $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($docs as $rs) : ?>
        <tr class="font-size-12" id="row-<?php echo $rs->code; ?>">
          <td class="middle text-center no">
            <?php echo $no; ?>
          </td>
          <td class="middle">
            <?php echo thai_date($rs->date_add, FALSE); ?>
          </td>
          <td class="middle">
            <?php echo $rs->code; ?>
          </td>
          <td class="middle">
            <?php echo $rs->customer_name; ?>
          </td>

          <td class="middle text-right">
            <?php echo number($rs->amount, 2); ?>
          </td>

          <td class="middle text-center">
            <?php if($rs->status == 2) : ?>
              <span class="red">CN</span>
            <?php endif; ?>
            <?php if($rs->status == 0) : ?>
              <span class="blue">NC</span>
            <?php endif; ?>
          </td>

          <td class="middle text-center">
            <?php echo $rs->payment_type; ?>
          </td>

          <td class="middle text-right">

              <button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
            
            <?php if($rs->status == 0 && $this->pm->can_edit) : ?>
              <button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
            <?php endif; ?>
            <?php if($this->pm->can_delete && $rs->status != 2) : ?>
              <button type="button" class="btn btn-minier btn-danger" onclick="getCancle('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
            <?php endif; ?>

          </td>
        </tr>
<?php    $no++; ?>
<?php   endforeach; ?>
<?php else : ?>
        <tr>
          <td colspan="8" class="middle text-center">---- ไม่พบรายการ ----</td>
        </tr>
<?php endif; ?>
       </tbody>
     </table>
   </div>
 </div>
<script src="<?php echo base_url(); ?>scripts/account/order_repay/order_repay.js"></script>
<?php $this->load->view('include/footer'); ?>
