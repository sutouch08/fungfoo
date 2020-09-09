<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    	<h4 class="title"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onClick="editOrder('<?php echo $order->code; ?>')"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
    </div>
</div>
<hr class="margin-bottom-15" />
<div class="row">
	<div class="col-sm-12">
    	<center><h1><i class="fa fa-frown-o"></i></h1></center>
        <center><h3>Oops.. Something went wrong.</h3></center>
        <center><h4>สถานะออเดอร์ถูกเปลี่ยนไปแล้ว ไม่สามารถทำรายการได้</h4></center>
    </div>
</div>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js"></script>
<?php $this->load->view('include/footer'); ?>
<?php exit(); ?>
