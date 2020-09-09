<?php $this->load->view('include/header'); ?>
<div class="row top-row">
  <div class="col-sm-6 top-col">
    <h4 class="title"><i class="fa fa-file-text-o"></i> รายการรอเปิดบิล</h4>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr/>


<?php
  if( $order->state == 7)
  {
    $this->load->view('inventory/delivery_order/bill_confirm_detail');
  }
  else if( $order->state == 8)
  {
    $this->load->view('inventory/delivery_order/bill_closed_detail');
  }
  else
  {
    $this->load->view('inventory/delivery_order/invalid_state');
  }
?>

<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill_detail.js"></script>
<?php $this->load->view('include/footer'); ?>
