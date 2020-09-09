<?php $this->load->view('include/header'); ?>
<div class="row top-row">
  <div class="col-sm-6 col-xs-6">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-sm-6 col-xs-6">
    <p class="pull-right top-p">
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

  <div class="col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search text-center" name="customer" id="customer" value="<?php echo $customer; ?>" />
  </div>

  <div class="col-sm-2 col-xs-6 padding-5">
    <label class="display-block">วันที่</label>
    <div class="input-daterange input-group width-100">
        <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>">
        <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>">
      </div>
  </div>

  <div class="col-sm-2 col-xs-6 padding-5">
    <label class="display-block">วันที่ครบกำหนด</label>
    <div class="input-daterange input-group width-100">
        <input type="text" class="form-control input-sm width-50 text-center from-date" name="due_from_date" id="dueFromDate" value="<?php echo $due_from_date; ?>">
        <input type="text" class="form-control input-sm width-50 text-center" name="due_to_date" id="dueToDate" value="<?php echo $due_to_date; ?>">
      </div>
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="valid" id="valid" onchange="getSearch()">
      <option value="0" <?php echo is_selected('0', $valid); ?>>ค้างจ่าย</option>
      <option value="1" <?php echo is_selected('1', $valid); ?>>จ่ายแล้ว</option>
      <option value="2" <?php echo is_selected('2', $valid); ?>>ทั้งหมด</option>
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
   <div class="col-sm-12 col-xs-12 first last table-responsive">
     <table class="table table-striped border-1">
       <thead>
         <tr class="font-size-12">
           <th class="width-5 text-center">ลำดับ</th>
           <th class="width-10">วันที่</th>
           <th class="width-15">เลขที่เอกสาร</th>
           <th class="">ลูกค้า</th>
           <th class="width-10 text-center">ครบกำหนด</th>
           <th class="width-10 text-right">ยอดเงิน</th>
           <th class="width-10 text-right">จ่ายแล้ว</th>
           <th class="width-10 text-right">ค้างจ่าย</th>
         </tr>
       </thead>
       <tbody>
<?php if(!empty($docs)) : ?>
<?php   $no = $this->uri->segment(4) + 1;
        $total_amount = 0;
        $total_paid = 0;
        $total_balance = 0;
?>
<?php   foreach($docs as $rs) : ?>
  <?php $Today = date('Y-m-d'); ?>
  <?php $hilight = ($rs->valid == 0 && ($Today > $rs->over_due_date)) ? 'red' : ($rs->valid == 1 ? 'green' : ''); ?>
        <tr class="font-size-12 <?php echo $hilight; ?>">
          <td class="middle text-center no">
            <?php echo $no; ?>
          </td>
          <td class="middle">
            <?php echo thai_date($rs->delivery_date, FALSE); ?>
          </td>
          <td class="middle">
            <?php echo $rs->order_code; ?>
          </td>
          <td class="middle">
            <?php echo $rs->customer_name; ?>
          </td>

          <td class="middle text-center">
            <?php echo thai_date($rs->due_date, FALSE); ?>
          </td>
          <td class="middle text-right">
            <?php echo number($rs->amount, 2); ?>
          </td>
          <td class="middle text-right">
            <?php echo number($rs->paid, 2); ?>
          </td>

          <td class="middle text-right">
            <?php echo number($rs->balance, 2); ?>
          </td>
        </tr>
<?php    $no++; ?>
<?php    $total_amount += $rs->amount; ?>
<?php    $total_paid += $rs->paid; ?>
<?php    $total_balance += $rs->balance; ?>
<?php   endforeach; ?>
        <tr>
          <td colspan="5" class="text-right">รวม</td>
          <td class="text-right"><?php echo number($total_amount, 2); ?></td>
          <td class="text-right"><?php echo number($total_paid, 2); ?></td>
          <td class="text-right"><?php echo number($total_balance, 2); ?></td>
        </tr>
<?php else : ?>
        <tr>
          <td colspan="8" class="middle text-center">---- ไม่พบรายการ ----</td>
        </tr>
<?php endif; ?>
       </tbody>
     </table>
   </div>
 </div>
<script src="<?php echo base_url(); ?>scripts/account/order_credit/order_credit.js"></script>
<?php $this->load->view('include/footer'); ?>
