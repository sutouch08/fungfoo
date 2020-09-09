<?php
$pm = get_permission('SOREST', get_cookie('uid'), get_cookie('id_profile')); //--- ย้อนสถานะออเดอร์ได้หรือไม่
$px	= get_permission('SORECT', get_cookie('uid'), get_cookie('id_profile')); //--- ย้อนสถานะออเดอร์ที่เปิดบิลแล้วได้หรือไม่
$pc = get_permission('SOREUP', get_cookie('uid'), get_cookie('id_profile')); //--- ปล่อยออเดอร์ที่ยังไม่ชำระเงิน (เงินสด)
$canChange	= ($pm->can_add + $pm->can_edit + $pm->can_delete) > 0 ? TRUE : FALSE;
$canUnbill	= ($px->can_add + $px->can_edit + $px->can_delete) > 0 ? TRUE : FALSE;
$canSkip = ($pc->can_add + $pc->can_edit + $pc->can_delete) > 0 ? TRUE : FALSE;
?>

<div class="row" style="padding:15px;">
	<div class="col-sm-3 padding-5">
    	<table class="table" style="margin-bottom:0px;">
        <?php if( $this->pm->can_add OR $this->pm->can_edit OR $this->pm->can_delete ) : ?>
        	<tr>
            	<td class="width-25 middle text-right" style="border:0px; padding:5px;">สถานะ : </td>
                <td class="width-50" style="border:0px; padding:5px;">
                	<select class="form-control input-sm" style="padding-top:0px; padding-bottom:0px;" id="stateList">
                    	<option value="0">เลือกสถานะ</option>
							<?php if( $order->state != 9 && $order->is_expired == 0 && $order->status == 1) : ?>

                 <?php if( $order->state <=3 && $canChange) : ?>
                        <?php if($order->state != 1): ?>
													<option value="1">รอดำเนินการ</option>
												<?php endif; ?>
												<?php if($order->state != 2 && $order->is_term == 0) : ?>
                        <option value="2">รอชำระเงิน</option>
												<?php endif; ?>
												<?php if($order->state != 3 && $order->role == 'S') : ?>
													<?php if($order->is_paid == 1 OR $order->is_term == 1 OR $order->payment_role == 4 OR $canSkip) : ?>
                        		<option value="3">รอจัดสินค้า</option>
													<?php endif; ?>
												<?php endif; ?>

								 <?php elseif($order->state > 3 && $order->state < 8 && $canChange ) : ?>
											 <option value="1">รอดำเนินการ</option>
											 <option value="2">รอชำระเงิน</option>
											 <option value="3">รอจัดสินค้า</option>
								 <?php elseif($order->state > 3 && $order->state >= 8 && $canUnbill ) : ?>
                       <option value="1">รอดำเนินการ</option>
                       <option value="2">รอชำระเงิน</option>
                       <option value="3">รอจัดสินค้า</option>
								 <?php endif; ?>

                 <?php if( $order->state < 8 && $this->pm->can_delete ) : ?>
                        <option value="9">ยกเลิก</option>
								 <?php elseif( $order->state >= 8 && $canUnbill) : ?>
												<option value="9">ยกเลิก</option>
                 <?php endif; ?>

							<?php elseif($order->is_expired == 1 && $this->pm->can_delete) : ?>
												<option value="9">ยกเลิก</option>
							<?php elseif($order->state == 9 && $this->pm->can_edit) : ?>
												<option value="1">รอดำเนินการ</option>
							<?php endif; ?>
                    </select>
                </td>
                <td class="width-25" style="border:0px; padding:5px;">
                <?php if( $order->status == 1 && $order->is_expired == 0 ) : ?>
                	<button class="btn btn-xs btn-primary btn-block" onclick="changeState()">เปลี่ยนสถานะ</button>
								<?php elseif($order->is_expired == 1 && $$this->pm->can_delete) : ?>
									<button class="btn btn-xs btn-primary btn-block" onclick="changeState()">เปลี่ยนสถานะ</button>
								<?php elseif($order->state == 9 && $this->pm->can_delete) : ?>
									<button class="btn btn-xs btn-primary btn-block" onclick="changeState()">เปลี่ยนสถานะ</button>
                <?php endif; ?>
                </td>
            </tr>
       <?php else : ?>
       <tr>
            	<td class="width-30 text-center" style="border:0px;">สถานะ</td>
                <td class="width-40 text-center" style="border:0px;">พนักงาน</td>
                <td class="width-30 text-center" style="border:0px;">เวลา</td>
            </tr>
       <?php endif; ?>
      </table>
	</div>

<?php if( !empty($state) ) : ?>
  <?php foreach($state as $rs) : ?>
	<div class="col-sm-1 col-1-harf padding-0 font-size-8" style="<?php echo state_color($rs->state); ?>">
    <center><?php echo get_state_name($rs->state); ?></center>
    <center><?php echo $this->user_model->get_name($rs->update_user); ?></center>
    <center><?php echo thai_date($rs->date_upd,TRUE, '/'); ?></center>
  </div>
<?php	endforeach; ?>
<?php endif; ?>
</div>
