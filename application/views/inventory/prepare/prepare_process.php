<?php $this->load->view('include/header'); ?>
<div class="row top-row">
  <div class="col-sm-6 top-col">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> รอจัด</button>
      <button type="button" class="btn btn-sm btn-yellow" onclick="goProcess()"><i class="fa fa-arrow-left"></i> กำลังจัด</button>
    </p>
  </div>

</div>

<hr class="margin-top-10 margin-bottom-10" />
<?php if($order->state != 4) : ?>
<?php   $this->load->view('inventory/prepare/invalid_state'); ?>
<?php else : ?>

  <div class="row">
    <div class="col-sm-2 padding-5 first">
      <label>เลขที่ : <?php echo $order->code; ?></label>
    </div>
    <div class="col-sm-5 padding-5">
      <label>ลูกค้า/ผู้เบิก/ผู้ยืม : &nbsp;
    <?php echo ($order->customer_ref == '' ? $order->customer_name : $order->customer_ref);  ?>
      </label>
    </div>
    <div class="col-sm-3">
      <label>ช่องทาง : <?php echo $order->channels_name; ?></label>
    </div>
    <div class="col-sm-2 padding-5 last text-right">
      <label>วันที่ : <?php echo thai_date($order->date_add); ?></label>
    </div>
  <?php if($order->remark != '') : ?>
    <div class="col-sm-12 margin-top-10">
      <label>หมายเหตุ : <?php echo $order->remark; ?></label>
    </div>
  <?php endif; ?>

    <input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
  </div>


  <hr class="margin-top-10 margin-bottom-10"/>

  <?php $this->load->view('inventory/prepare/prepare_control'); ?>

  <hr class="margin-top-10 margin-bottom-10"/>

  <?php $this->load->view('inventory/prepare/prepare_incomplete_list');  ?>

  <?php $this->load->view('inventory/prepare/prepare_completed_list'); ?>

<?php endif; //--- endif order->state ?>

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_process.js?"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
