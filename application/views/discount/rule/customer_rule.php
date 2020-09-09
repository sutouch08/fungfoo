<?php
$allCustomer = $rule->all_customer == 0 ? 'N' : 'Y';
$id = $rule->id;
//--- ระบุชื่อลูกค้า
$cusList = $this->discount_rule_model->getRuleCustomerId($id);
$cusListNo = count($cusList);
$customer_id = ($allCustomer == 'N' && $cusListNo > 0 ) ? 'Y' : 'N';

//--- กำหนดกลุ่มลูกค้า
$custGroup = $this->discount_rule_model->getRuleCustomerGroup($id);
$custGroupNo = count($custGroup);
$customer_group = ($custGroupNo > 0 && $allCustomer == 'N' && $customer_id == 'N') ? 'Y' : 'N';

//--- กำหนดชนิดลูกค้า
$custType = $this->discount_rule_model->getRuleCustomerType($id);
$custTypeNo = count($custType);
$customer_type = ($custTypeNo > 0 && $allCustomer == 'N' && $customer_id == 'N') ? 'Y' : 'N';

//--- กำหนดประเภทลูกค้า
$custKind = $this->discount_rule_model->getRuleCustomerKind($id);
$custKindNo = count($custKind);
$customer_kind = ($custKindNo > 0 && $allCustomer == 'N' && $customer_id == 'N') ? 'Y' : 'N';

//--- กำหนดเขตลูกค้า
$custArea = $this->discount_rule_model->getRuleCustomerArea($id);
$custAreaNo = count($custArea);
$customer_area = ($custAreaNo > 0 && $allCustomer == 'N' && $customer_id == 'N') ? 'Y' : 'N';

//--- กำหนดเกรดลูกค้า
$custClass = $this->discount_rule_model->getRuleCustomerClass($id);
$custClassNo = count($custClass);
$customer_class = ($custClassNo > 0 && $allCustomer == 'N' && $customer_id == 'N') ? 'Y' : 'N';
 ?>
<div class="tab-pane fade" id="customer">

	<div class="row">
        <div class="col-sm-8 top-col">
            <h4 class="title">กำหนดเงื่อนไขตามคุณสมบัติลูกค้า</h4>
        </div>

        <div class="divider margin-top-5"></div>
        <div class="col-sm-2">
					<span class="form-control left-label text-right">ลูกค้าทั้งหมด</span>
				</div>
        <div class="col-sm-2">
          <div class="btn-group width-100">
          	<button type="button" class="btn btn-sm width-50 btn-primary" id="btn-cust-all-yes" onclick="toggleAllCustomer('Y')">YES</button>
						<button type="button" class="btn btn-sm width-50" id="btn-cust-all-no" onclick="toggleAllCustomer('N')">NO</button>
          </div>
        </div>
				<div class="divider-hidden"></div>


        <div class="col-sm-2">
					<span class="form-control left-label text-right">ระบุลูกค้า</span>
				</div>
        <div class="col-sm-2">
					<div class="btn-group width-100">
						<button type="button" class="not-all btn btn-sm width-50" id="btn-cust-id-yes" onclick="toggleCustomerId('Y')" disabled>YES</button>
						<button type="button" class="not-all btn btn-sm width-50 btn-primary" id="btn-cust-id-no" onclick="toggleCustomerId('N')" disabled>NO</button>
					</div>
        </div>
				<div class="col-sm-4 padding-5">
					<input type="text" class="option form-control input-sm text-center" id="txt-cust-id-box" placeholder="ค้นหาชื่อลูกค้า" disabled />
					<input type="hidden" id="id_customer" />
				</div>
				<div class="col-sm-1 padding-5">
					<button type="button" class="option btn btn-xs btn-info btn-block" id="btn-cust-id-add" onclick="addCustId()" disabled><i class="fa fa-plus"></i> เพิ่ม</button>
				</div>
				<div class="col-sm-2 padding-5">
					<span class="form-control input-sm text-center"><span id="count"><?php echo $cusListNo; ?></span>  รายการ</span>
				</div>
				<div class="col-sm-1 padding-5">
					<button type="button" class="option btn btn-xs btn-primary btn-block" id="btn-show-cust-name" onclick="showCustomerList()">
						แสดง
					</button>
				</div>
				<div class="divider-hidden"></div>



				<div class="col-sm-2">
					<span class="form-control left-label text-right">กลุ่มลูกค้า</span>
				</div>
        <div class="col-sm-2">
					<div class="btn-group width-100">
						<button type="button" class="not-all btn btn-sm width-50" id="btn-cust-group-yes" onclick="toggleCustomerGroup('Y')" disabled>YES</button>
						<button type="button" class="not-all btn btn-sm width-50 btn-primary" id="btn-cust-group-no" onclick="toggleCustomerGroup('N')" disabled>NO</button>
					</div>
        </div>
				<div class="col-sm-2 col-2-harf padding-5">
					<button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-cust-group" onclick="showCustomerGroup()" disabled>
						เลือกกลุ่มลูกค้า <span class="badge pull-right" id="badge-group"><?php echo $custGroupNo; ?></span>
					</button>
				</div>
				<div class="divider-hidden"></div>



				<div class="col-sm-2">
					<span class="form-control left-label text-right">ชนิดลูกค้า</span>
				</div>
        <div class="col-sm-2">
					<div class="btn-group width-100">
						<button type="button" class="not-all btn btn-sm width-50" id="btn-cust-type-yes" onclick="toggleCustomerType('Y')" disabled>YES</button>
						<button type="button" class="not-all btn btn-sm width-50 btn-primary" id="btn-cust-type-no" onclick="toggleCustomerType('N')" disabled>NO</button>
					</div>
        </div>
				<div class="col-sm-2 col-2-harf padding-5">
					<button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-cust-type" onclick="showCustomerType()" disabled>
						เลือกชนิดลูกค้า <span class="badge pull-right" id="badge-type"><?php echo $custTypeNo; ?></span>
					</button>
				</div>
				<div class="divider-hidden"></div>



				<div class="col-sm-2">
					<span class="form-control left-label text-right">ประเภทลูกค้า</span>
				</div>
        <div class="col-sm-2">
					<div class="btn-group width-100">
						<button type="button" class="not-all btn btn-sm width-50" id="btn-cust-kind-yes" onclick="toggleCustomerKind('Y')" disabled>YES</button>
						<button type="button" class="not-all btn btn-sm width-50 btn-primary" id="btn-cust-kind-no" onclick="toggleCustomerKind('N')" disabled>NO</button>
					</div>
        </div>
				<div class="col-sm-2 col-2-harf padding-5">
					<button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-cust-kind" onclick="showCustomerKind()" disabled>
						เลือกประเภทลูกค้า <span class="badge pull-right" id="badge-kind"><?php echo $custKindNo; ?></span>
					</button>
				</div>
				<div class="divider-hidden"></div>


				<div class="col-sm-2">
					<span class="form-control left-label text-right">เขตลูกค้า</span>
				</div>
        <div class="col-sm-2">
					<div class="btn-group width-100">
						<button type="button" class="not-all btn btn-sm width-50" id="btn-cust-area-yes" onclick="toggleCustomerArea('Y')" disabled>YES</button>
						<button type="button" class="not-all btn btn-sm width-50 btn-primary" id="btn-cust-area-no" onclick="toggleCustomerArea('N')" disabled>NO</button>
					</div>
        </div>
				<div class="col-sm-2 col-2-harf padding-5">
					<button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-cust-area" onclick="showCustomerArea()" disabled>
						เลือกเขตลูกค้า <span class="badge pull-right" id="badge-area"><?php echo $custAreaNo; ?></span>
					</button>
				</div>
				<div class="divider-hidden"></div>


				<div class="col-sm-2">
					<span class="form-control left-label text-right">เกรดลูกค้า</span>
				</div>
        <div class="col-sm-2">
					<div class="btn-group width-100">
						<button type="button" class="not-all btn btn-sm width-50" id="btn-cust-class-yes" onclick="toggleCustomerClass('Y')" disabled>YES</button>
						<button type="button" class="not-all btn btn-sm width-50 btn-primary" id="btn-cust-class-no" onclick="toggleCustomerClass('N')" disabled>NO</button>
					</div>
        </div>
				<div class="col-sm-2 col-2-harf padding-5">
					<button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-cust-class" onclick="showCustomerClass()" disabled>
						เลือกเกรดลูกค้า <span class="badge pull-right" id="badge-class"><?php echo $custClassNo; ?></span>
					</button>
				</div>
        <div class="divider-hidden"></div>
				<div class="col-sm-2">&nbsp;</div>
				<div class="col-sm-3">
					<button type="button" class="btn btn-xs btn-success btn-block" onclick="saveCustomer()"><i class="fa fa-save"></i> บันทึก</button>
				</div>


    </div>

		<input type="hidden" id="all_customer" value="<?php echo $allCustomer; ?>" />
		<input type="hidden" id="customer_id" value="<?php echo $customer_id; ?>" />
		<input type="hidden" id="customer_group" value="<?php echo $customer_group; ?>" />
		<input type="hidden" id="customer_type" value="<?php echo $customer_type; ?>" />
		<input type="hidden" id="customer_kind" value="<?php echo $customer_kind; ?>" />
		<input type="hidden" id="customer_area" value="<?php echo $customer_area; ?>" />
		<input type="hidden" id="customer_class" value="<?php echo $customer_class; ?>" />

</div><!--- Tab-pane --->
<?php $this->load->view('discount/rule/customer_rule_modal'); ?>
