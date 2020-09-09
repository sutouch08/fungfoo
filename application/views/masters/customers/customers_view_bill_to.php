<?php
$em = empty($bill) ? TRUE : FALSE;

$branch_code = $em ? '0000' : $bill->branch_code;
$branch_name = $em ? 'สำนักงานใหญ่' : $bill->branch_name;
$address = $em ? '' : $bill->address;
$sub_district = $em ? '' : $bill->sub_district;
$district = $em ? '' : $bill->district;
$province = $em ? '' : $bill->province;
$postcode = $em ? '' : $bill->postcode;
$country = $em ? 'TH' : $bill->country;
$phone = $em ? '' : $bill->phone;
?>


<form class="form-horizontal">
	<div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">รหัสสาขา</label>
    <div class="col-xs-6 col-sm-1 col-1-harf">
      <input type="text" class="form-control input-sm" value="<?php echo $branch_code; ?>" disabled/>
    </div>
  </div>


  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">ชื่อสาขา</label>
    <div class="col-xs-6 col-sm-3">
      <input type="text" class="form-control input-sm" value="<?php echo $branch_name; ?>" disabled/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">ที่อยู่</label>
    <div class="col-xs-12 col-sm-10">
      <input type="text" class="form-control input-sm" value="<?php echo $address; ?>" disabled/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">ตำบล/แขวง</label>
    <div class="col-xs-12 col-sm-4">
      <input type="text" class="form-control input-sm" value="<?php echo $sub_district; ?>" disabled/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">อำเภอ/เขต</label>
    <div class="col-xs-12 col-sm-4">
      <input type="text" class="form-control input-sm" value="<?php echo $district; ?>" disabled/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">จังหวัด</label>
    <div class="col-xs-12 col-sm-4">
      <input type="text" class="form-control input-sm" value="<?php echo $province; ?>" disabled/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">รหัสไปรษณีย์</label>
    <div class="col-xs-6 col-sm-1 col-1-harf">
      <input type="text" class="form-control input-sm" value="<?php echo $postcode; ?>" disabled/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">รหัสประเทศ</label>
    <div class="col-xs-6 col-sm-1 col-1">
      <input type="text" class="form-control input-sm text-center" value="<?php echo $country; ?>" disabled/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">โทรศัพท์</label>
    <div class="col-xs-6 col-sm-3">
      <input type="text" class="form-control input-sm" value="<?php echo $phone; ?>" disabled/>
    </div>
  </div>
  <div class="divider-hidden">

	</div>

</form>
