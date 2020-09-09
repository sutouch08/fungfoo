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

  <div class="col-sm-3 col-xs-6 padding-5">
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
   <div class="col-sm-12 col-xs-12 first last table-responsive">
     <table class="table table-striped border-1">
       <thead>
         <tr class="font-size-12">
           <th class="width-5 text-center">ลำดับ</th>
           <th class="width-10">วันที่</th>
           <th class="width-15">เลขที่เอกสาร</th>
           <th class="">ลูกค้า</th>
           <th class="width-10 text-center">ชำระโดย</th>
           <th class="width-10 text-right">มูลค่า</th>
           <th class="width-10">วันที่ทำรายการ</th>
         </tr>
       </thead>
       <tbody>
<?php if(!empty($docs)) : ?>
<?php   $no = $this->uri->segment(4) + 1; ?>
<?php   $total_amount = 0; ?>
<?php   foreach($docs as $rs) : ?>
        <tr class="font-size-12" id="row-<?php echo $rs->reference; ?>">
          <td class="middle text-center no">
            <?php echo $no; ?>
          </td>
          <td class="middle">
            <?php echo thai_date($rs->pay_date, FALSE); ?>
          </td>
          <td class="middle">
            <?php echo $rs->reference; ?>
          </td>
          <td class="middle">
            <?php echo $rs->customer_name; ?>
          </td>

          <td class="middle text-center">
            <?php echo $rs->pay_type; ?>
          </td>
          <td class="middle text-right">
            <?php echo number($rs->amount, 2); ?>
          </td>

          <td class="middle">
            <?php echo thai_date($rs->date_upd); ?>
          </td>
        </tr>
<?php    $no++; ?>
<?php    $total_amount += $rs->amount; ?>
<?php   endforeach; ?>
        <tr>
          <td colspan="5" class="text-right">รวม</td>
          <td class="text-right"><?php echo number($total_amount, 2); ?></td>
          <td></td>
        </tr>
<?php else : ?>
        <tr>
          <td colspan="7" class="middle text-center">---- ไม่พบรายการ ----</td>
        </tr>
<?php endif; ?>
       </tbody>
     </table>
   </div>
 </div>
<script src="<?php echo base_url(); ?>scripts/account/payment_receive/payment_receive.js"></script>
<?php $this->load->view('include/footer'); ?>
