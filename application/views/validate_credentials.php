<div class="modal fade" id="validate-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog input-xlarge" id="modal">
		<div class="modal-content">
  			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="validate-modal-title">รหัสลับ ผู้มีอำนาจ</h4>
                <input type="hidden" id="validateTab" value="" />
                <input type="hidden" id="validateField" value="" />
                <input type="hidden" id="approverName" />
                <input type="hidden" id="approveToken" />
			 </div>
			 <div class="modal-body" id="modal_body">
                 <div class="row" style="padding-bottom:15px;">
                     <div class="col-sm-12">
                        <input type="password" class="form-control input-sm text-center" id="s_key" name="s_key" placeholder="รหัสอนุมัติ" />
                        <span class="help-block red not-show" id="sKey-error">xxx</span>
                     </div>
                     <div class="col-sm-12 text-right">
                     	<button type="button" class="btn btn-primary" id="btn-validate-confirm" onClick="validate_credentials()" >ยืนยัน (Enter)</button>
                     </div>
                 </div>
             </div>

		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/validate_credentials.js"></script>
