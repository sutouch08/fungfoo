
<!--  สรุปยอดส่ง Line --------->
<div class="modal fade" id="orderSummaryTab" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:300px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" >
            <div id="summaryText"></div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-sm btn-info btn-block" data-dismiss="modal" data-clipboard-action="copy" data-clipboard-target="#summaryText">Copy</button>
            </div>
        </div>
    </div>
</div>


<!--  Add New Address Modal  --------->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center" >เพิ่ม/แก้ไข ที่อยู่สำหรับจัดส่ง</h4>
            </div>
            <div class="modal-body">
            <form id="addAddressForm"	>
            <input type="hidden" name="id_address" id="id_address" />
            <div class="row">
            	<div class="col-sm-12 col-xs-12">
                	<label class="input-label">ชื่อ</label>
                    <input type="text" class="form-control input-sm" name="Fname" id="Fname" placeholder="ชื่อผู้รับ (จำเป็น)" />
                </div>
                <div class="col-sm-12 col-xs-12">
                	<label class="input-label">ที่อยู่</label>
                    <input type="text" class="form-control input-sm" name="address" id="address1" placeholder="เลขที่, หมู่บ้าน, ถนน (จำเป็น)" />
                </div>

                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">ตำบล/แขวง</label>
                    <input type="text" class="form-control input-sm" name="sub_district" id="sub_district" placeholder="ตำบล" />
                </div>
                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">อำเภอ/เขต</label>
                    <input type="text" class="form-control input-sm" name="district" id="district" placeholder="อำเภอ (จำเป็น)" />
                </div>
                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">จังหวัด</label>
                    <input type="text" class="form-control input-sm" name="province" id="province" placeholder="จังหวัด (จำเป็น)" />
                </div>
                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">รหัสไปรษณีย์</label>
                    <input type="text" class="form-control input-sm" name="postcode" id="postcode" placeholder="รหัสไปรษณีย์" />
                </div>
                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">เบอร์โทรศัพท์</label>
                    <input type="text" class="form-control input-sm" name="phone" id="phone" placeholder="000 000 0000" />
                </div>
                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">อีเมล์</label>
                    <input type="text" class="form-control input-sm" name="email" id="email" placeholder="someone@somesite.com" />
                </div>
                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">ชื่อเรียก</label>
                    <input type="text" class="form-control input-sm" name="alias" id="alias" placeholder="ใช้เรียกที่อยู่ เช่น บ้าน, ที่ทำงาน (จำเป็น)" />
                </div>
            </div>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" onClick="saveAddress()" ><i class="fa fa-save"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>



<!---  เลือกธนาคารที่แจ้งชำระ  --------->
<div class="modal fade" id="selectBankModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center" >เลือกช่องทางการชำระเงิน</h4>
            </div>
            <div class="modal-body">
            	<div class="row">
	<?php if(!empty($banks)) : ?>
    <?php	foreach($banks as $rs) : ?>
    				    <div class="col-sm-12" style="padding-top:15px; padding-bottom:15px; border-top:solid 1px #ccc; ">
                	<table style="width:100%; border:0px;">
                    <tr>
                    	<td width="25%" style="vertical-align:text-top;">
                        <img src="<?php echo bankLogoUrl($rs->bank_code); ?>" height="50px"/>
                      </td>
                      <td>
									       <?php echo $rs->bank_name; ?>
                         สาขา  <?php echo $rs->branch; ?> <br/>
                         เลขที่บัญชี <?php echo $rs->acc_no; ?> <br/>
                         ชื่อบัญชี  <?php echo $rs->acc_name; ?> <br/>
                         <button type="button" class="btn btn-sm btn-primary" style="margin-top:10px;" onClick="payOnThis(<?php echo $rs->id; ?>,'<?php echo $rs->acc_no; ?>')">ชำระด้วยช่องทางนี้</button>
							         </td>
                      </tr>
                    </table>
                  </div>
	<?php	endforeach; ?>
    <?php endif; ?>
				</div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<!---  แจ้งชำระเงิน  --------->
<form id="paymentForm" name="paymentForm" enctype="multipart/form-data" method="post">
	<input type="hidden" name="id_account" id="id_account"/>
  <input type="hidden" name="acc_no" id="acc_no"/>
	<input type="hidden" name="orderAmount" id="orderAmount" value="" />
	<input type="file" name="image" id="image" accept="image/*" style="display:none;" />
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:400px;">
    <div class="modal-content" style="position:relative; max-height:600px; overflow-y:scroll;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row">
        <div class="col-sm-12" style="padding-bottom:15px; margin-bottom:15px; border-bottom:solid 1px #eee;">
        	<span id="payAmountLabel" style="font-size:25px; color:#75ce66;"></span>
        </div>
        <div class="col-sm-12" style="padding-bottom:15px; margin-bottom:15px; border-bottom:solid 1px #eee;">
          <div class="row">
            <div class="col-sm-3" id="logo" style="padding-top:5px;"></div>
            <div class="col-sm-9" id="detail"></div>
          </div>
        </div>
        <div class="col-sm-12" style="padding-bottom:15px; margin-bottom:15px; border-bottom:solid 1px #eee;">
          <div class="row">
          	<div class="col-sm-12" style="margin-bottom:20px;">
            	<span style="font-size:18px; color:#888; font-weight:500">แจ้งหลักฐานการโอนเงิน</span>
            </div>
          	<div class="col-sm-4 label-left" style="padding-right:15px; padding-top:10px;">
            	<span style="font-weight:bold; color:#888;">แนบสลิป</span>
            </div>
            <div class="col-sm-8">
            	<button type="button" class="btn btn-block btn-primary" id="btn-select-file" onClick="selectFile()"><i class="fa fa-file-image-o"></i> เลือกรูปภาพ</button>
              <div id="block-image" style="opacity:0;">
              	<div id="previewImg" ></div>
                <span onClick="removeFile()" style="position:absolute; left:190px; top:0px; cursor:pointer; color:red;"><i class="fa fa-times fa-2x"></i></span>
              </div>
            </div>
            <div class="col-sm-4 label-left" style="padding-right:15px; padding-top:10px;">
            	  <span style="font-weight:bold; color:#888;">ยอดเงินที่โอน</span>
            </div>
            <div class="col-sm-8 top-col">
            	<div class="input-group">
              	<input type="number" step="any" class="form-control input-sm input-lagre" name="payAmount" id="payAmount" />
                  <span class="input-group-addon">บาท</span>
              </div>
            </div>
            <div class="col-sm-4 label-left" style="padding-right:15px; padding-top:10px;">
            	<span style="font-weight:bold; color:#888;">วันที่โอน</span>
            </div>
            <div class="col-sm-8 top-col">
            	<div class="input-group">
            		<input type="text" class="form-control input-sm" name="payDate" id="payDate" style="z-index:2000" />
              	<span class="input-group-btn">
                  <button type="button" class="btn btn-xs btn-default" onClick="dateClick()"><i class="fa fa-calendar"></i></button>
                </span>
              </div>
            </div>
            <div class="col-sm-4 label-left" style="padding-right:15px; padding-top:10px;">
            	<span style="font-weight:bold; color:#888;">เวลา</span>
            </div>
            <div class="col-sm-4 top-col">
            	<select id="payHour" name="payHour" class="form-control input-sm">
              <?php echo selectHour(); ?>
              </select>
            </div>
            <div class="col-sm-4 top-col">
            	<select id="payMin" name="payMin" class="form-control input-sm">
                <?php echo selectMin(); ?>
              </select>
            </div>
          </div><!--/ row -->
        </div>
        <div class="col-sm-12" style="padding-bottom:15px; margin-bottom:15px; border-bottom:solid 1px #eee;">
          <div class="row">
            <div class="col-sm-10" style="padding-top:5px;">
              <label>
                <input type="checkbox" class="ace" name="deposit" id="deposit" onchange="toggleDeposit()">
                <span class="lbl">  บันทึกเป็นเงินมัดจำ</span>
              </label>
            </div>
          </div>
        </div>
      </div>
      </div>

      <div class="modal-footer">
      	<button type="button" class="btn btn-sm btn-primary" onClick="submitPayment()" ><i class="fa fa-save"></i> บันทึก</button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" name="is_deposit" id="is_deposit" value="0">
</form>



<div class="modal fade" id="deliveryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog " style="max-width:350px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
				<h4 class="modal-title-site" >บันทึกเลขที่การจัดส่ง</h4>
			</div>
			<div class="modal-body">
				<div class="row">
          <div class="col-sm-12 col-xs-12">
            <input type="text" class="form-control input-sm text-center" name="emsNo" id="emsNo" placeholder="เลขที่ EMS หรือ เลขที่การจัดส่ง" />
          </div>
        </div>
			</div>
			<div class="modal-footer">
            	<button type="button" class="btn btn-sm btn-primary btn-block" onClick="saveDeliveryNo()">บันทึก</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="paymentDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog " style="max-width:400px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
				<h4 class="modal-title-site" >ข้อมูลการชำระเงิน</h4>
			</div>
			<div class="modal-body" id="paymentDetailBody">

			</div>
			<div class="modal-footer">
            	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal" >Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:350px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="detailBody">

            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body" id="imageBody">

            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>



<script id="addressTemplate" type="text/x-handlebars-template">
<tr style="font-size:12px;" id="{{ id }}">
	<td align="center">{{ alias }}</td>
	<td>{{ name }}</td>
	<td>{{ address }}</td>
	<td>{{ email }}</td>
	<td>{{ phone }}</td>
	<td align="right">
	{{#if default}}
		<button type="button" class="btn btn-xs btn-success btn-address" id="btn-{{ id }}" onClick="setDefault({{ id }})"><i class="fa fa-check"></i></button>
	{{else}}
		<button type="button" class="btn btn-xs btn-address" id="btn-{{ id }}" onClick="setDefault({{ id }})"><i class="fa fa-check"></i></button>
	{{/if}}
		<button type="button" class="btn btn-xs btn-warning" onClick="editAddress({{ id }})"><i class="fa fa-pencil"></i></button>
		<button type="button" class="btn btn-xs btn-danger" onClick="removeAddress({{ id }})"><i class="fa fa-trash"></i></button>
	</td>
</tr>
</script>



<script id="addressTableTemplate" type="text/x-handlebars-template">
{{#each this}}
<tr style="font-size:12px;" id="{{ id }}">
	<td align="center">{{ alias }}</td>
	<td>{{ name }}</td>
	<td>{{ address }}</td>
	<td>{{ email }}</td>
	<td>{{ phone }}</td>
	<td align="right">
	{{#if default}}
		<button type="button" class="btn btn-xs btn-success btn-address" id="btn-{{ id }}" onClick="setDefault({{ id }})"><i class="fa fa-check"></i></button>
	{{else}}
		<button type="button" class="btn btn-xs btn-address" id="btn-{{ id }}" onClick="setDefault({{ id }})"><i class="fa fa-check"></i></button>
	{{/if}}
		<button type="button" class="btn btn-xs btn-warning" onClick="editAddress({{ id }})"><i class="fa fa-pencil"></i></button>
		<button type="button" class="btn btn-xs btn-danger" onClick="removeAddress({{ id }})"><i class="fa fa-trash"></i></button>
	</td>
</tr>
{{/each}}
</script>



<script id="detailTemplate" type="text/x-handlebars-template">
<div class="row">
	<div class="col-sm-12 text-center">ข้อมูลการชำระเงิน</div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-4 col-xs-4 label-left">ยอดที่ต้องชำระ :</div><div class="col-sm-8 col-xs-8">{{ orderAmount }}</div>
	<div class="col-sm-4 col-xs-4 label-left">ยอดโอนชำระ : </div><div class="col-sm-8 col-xs-8"><span style="font-weight:bold; color:#E9573F;">฿ {{ payAmount }}</span></div>
	<div class="col-sm-4 col-xs-4 label-left">วันที่โอน : </div><div class="col-sm-8 col-xs-8">{{ payDate }}</div>
	<div class="col-sm-4 col-xs-4 label-left">ธนาคาร : </div><div class="col-sm-8 col-xs-8">{{ bankName }}</div>
	<div class="col-sm-4 col-xs-4 label-left">สาขา : </div><div class="col-sm-8 col-xs-8">{{ branch }}</div>
	<div class="col-sm-4 col-xs-4 label-left">เลขที่บัญชี : </div><div class="col-sm-8 col-xs-8"><span style="font-weight:bold; color:#E9573F;">{{ accNo }}</span></div>
	<div class="col-sm-4 col-xs-4 label-left">ชื่อบัญชี : </div><div class="col-sm-8 col-xs-8">{{ accName }}</div>
	<div class="col-sm-4 col-xs-4 label-left">เวลาแจ้งชำระ : </div><div class="col-sm-8 col-xs-8">{{date_add}}</div>
	{{#if imageUrl}}
		<div class="col-sm-12 col-xs-12 top-row top-col text-center">
      <a href="javascript:void(0)" onClick="viewImage('{{ imageUrl }}')">รูปสลิปแนบ <i class="fa fa-paperclip fa-rotate-90"></i></a> </div>
	{{else}}
		<div class="col-sm-12 col-xs-12 top-row top-col text-center">---  ไม่พบไฟล์แนบ  ---</div>
	{{/if}}
</div>
</script>
