<?php
$allPayment = $rule->all_payment == 0 ? 'N' : 'Y';

$payment = $this->discount_rule_model->getRulePayment($rule->id);
$paymentNo = count($payment);
 ?>
<div class="tab-pane fade" id="payment">

	<div class="row">
        <div class="col-sm-8 top-col">
            <h4 class="title">กำหนดเงื่อนไขช่องทางการชำระเงิน</h4>
        </div>

        <div class="divider margin-top-5"></div>

				<div class="col-sm-2 col-2-harf">
					<span class="form-control left-label text-right">ช่องทางการชำระเงิน</span>
				</div>
        <div class="col-sm-2">
					<div class="btn-group width-100">
						<button type="button" class="btn btn-sm width-50" id="btn-all-payment" onclick="togglePayment('Y')" >ทั้งหมด</button>
						<button type="button" class="btn btn-sm width-50" id="btn-select-payment" onclick="togglePayment('N')" >ระบุ</button>
					</div>
        </div>
				<div class="col-sm-3 padding-5">
					<button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-show-payment" onclick="showSelectPayment()" >
						เลือกช่องทางการชำระเงิน <span class="badge pull-right" id="badge-payment"><?php echo $paymentNo; ?></span>
					</button>
				</div>

        <div class="divider-hidden"></div>
				<div class="col-sm-2 col-2-harf">&nbsp;</div>
				<div class="col-sm-3">
					<button type="button" class="btn btn-sm btn-success btn-block" onclick="savePayment()"><i class="fa fa-save"></i> บันทึก</button>
				</div>
    </div>

		<input type="hidden" id="all_payment" value="<?php echo $allPayment; ?>" />


</div><!--- Tab-pane --->

<?php $this->load->view('discount/rule/payment_rule_modal'); ?>
