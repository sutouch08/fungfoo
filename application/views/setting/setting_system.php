<div class="tab-pane fade" id="system">
<?php
    $open     = $CLOSE_SYSTEM == 0 ? 'btn-success' : '';
    $close    = $CLOSE_SYSTEM == 1 ? 'btn-danger' : '';
?>

  <form id="systemForm">
    <div class="row">
  	<?php if( $cando === TRUE ): //---- ถ้ามีสิทธิ์ปิดระบบ ---//	?>
    	<div class="col-sm-3"><span class="form-control left-label">ปิดระบบ</span></div>
      <div class="col-sm-9">
      	<div class="btn-group input-medium">
        	<button type="button" class="btn btn-sm <?php echo $open; ?>" style="width:50%;" id="btn-open" onClick="openSystem()">เปิด</button>
          <button type="button" class="btn btn-sm <?php echo $close; ?>" style="width:50%;" id="btn-close" onClick="closeSystem()">ปิด</button>
        </div>
        <span class="help-block">กรณีปิดระบบจะไม่สามารถเข้าใช้งานระบบได้ในทุกส่วน โปรดใช้ความระมัดระวังในการกำหนดค่านี้</span>
      	<input type="hidden" name="CLOSE_SYSTEM" id="closed" value="<?php echo $CLOSE_SYSTEM; ?>" />
      </div>
      <div class="divider-hidden"></div>

    <?php endif; ?>


      <div class="col-sm-9 col-sm-offset-3">
      	<button type="button" class="btn btn-sm btn-success" onClick="updateConfig('systemForm')"><i class="fa fa-save"></i> บันทึก</button>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/row-->
  </form>
</div><!--/ tab pane -->
