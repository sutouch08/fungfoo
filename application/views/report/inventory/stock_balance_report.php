<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6">
			<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> <?php label('report'); ?></button>
				<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> <?php label('export'); ?></button>
				<button type="button" class="btn btn-sm btn-info" onclick="print()"><i class="fa fa-print"></i> <?php label('print'); ?></button>
			</p>
		</div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label class="display-block"><?php label('items'); ?></label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)"><?php label('all'); ?></button>
      <button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)"><?php label('specify'); ?></button>
    </div>
  </div>
  <div class="col-sm-2 padding-5">
    <label class="display-block not-show">start</label>
    <input type="text" class="form-control input-sm text-center" id="pdFrom" name="pdFrom" disabled>
  </div>
  <div class="col-sm-2 padding-5">
    <label class="display-block not-show">End</label>
    <input type="text" class="form-control input-sm text-center" id="pdTo" name="pdTo" disabled>
  </div>
  <div class="col-sm-2 padding-5">
    <label class="display-block"><?php label('warehouse'); ?></label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)"><?php label('all'); ?></button>
      <button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)"><?php label('specify'); ?></button>
    </div>
  </div>
  <div class="col-sm-2 padding-5">
    <label class="display-block"><?php label('date'); ?></label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-date-now" onclick="toggleDate(1)"><?php label('current'); ?></button>
      <button type="button" class="btn btn-sm width-50" id="btn-date-range" onclick="toggleDate(0)"><?php label('specify'); ?></button>
    </div>
  </div>
  <div class="col-sm-2 padding-5 last">
    <label class="display-block not-show">start</label>
    <input type="text" class="form-control input-sm text-center" id="date" name="date" readonly disabled>
  </div>

  <input type="hidden" id="allProduct" name="allProduct" value="1">
  <input type="hidden" id="allWarehouse" name="allWhouse" value="1">
  <input type="hidden" id="currentDate" name="currentDate" value="1">
</div>


<div class="modal fade" id="wh-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:500px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='title' id='modal_title'>เลือกคลัง</h4>
            </div>
            <div class='modal-body' id='modal_body' style="padding:0px;">
        <?php if(!empty($whList)) : ?>
          <?php foreach($whList as $rs) : ?>
            <div class="col-sm-12">
              <label>
                <input type="checkbox" class="chk" id="<?php echo $rs->code; ?>" name="warehouse[<?php echo $rs->code; ?>]" value="<?php echo $rs->code; ?>" style="margin-right:10px;" />
                <?php echo $rs->code; ?> | <?php echo $rs->name; ?>
              </label>
            </div>
          <?php endforeach; ?>
        <?php endif;?>

        		<div class="divider" ></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-block' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>
<hr>
</form>

<div class="row">
	<div class="col-sm-12" id="rs">

    </div>
</div>




<script id="template" type="text/x-handlebars-template">
  <table class="table table-bordered table-striped">
    <tr>
      <th colspan="7" class="text-center">รายงานสินค้าคงเหลือ ณ วันที่ {{ reportDate }}</th>
    </tr>
    <tr>
      <th colspan="7" class="text-center"> คลัง : {{ whList }} </th>
    </tr>
    <tr>
      <th colspan="7" class="text-center"> สินค้า : {{ productList }} </th>
    </tr>
    <tr class="font-size-12">
      <th class="width-5 middle text-center">ลำดับ</th>
      <th class="width-15 middle text-center">บาร์โค้ด</th>
      <th class="width-15 middle text-center">รหัส</th>
      <th class="width-30 middle text-center">สินค้า</th>
      <th class="width-10 middle text-right">ทุน</th>
      <th class="width-10 text-right middle">คงเหลือ</th>
      <th class="width-15 text-right middle">มูลค่า</th>
    </tr>
{{#each bs}}
  {{#if nodata}}
    <tr>
      <td colspan="7" align="center"><h4>-----  ไม่พบสินค้าคงเหลือตามเงื่อนไขที่กำหนด  -----</h4></td>
    </tr>
  {{else}}
    {{#if @last}}
    <tr class="font-size-14">
      <td colspan="5" class="text-right">รวม</td>
      <td class="text-right">{{ totalQty }}</td>
      <td class="text-right">{{ totalAmount }}</td>
    </tr>
    {{else}}
    <tr class="font-size-12">
      <td class="middle text-center">{{no}}</td>
      <td class="middle text-center">{{ barcode }}</td>
      <td class="middle">{{ pdCode }}</td>
      <td class="middle">{{ pdName }}</td>
      <td class="middle text-right">{{ cost }}</td>
      <td class="middle text-right">{{ qty }}</td>
      <td class="middle text-right">{{ amount }}</td>
    </tr>
    {{/if}}
  {{/if}}
{{/each}}
  </table>
</script>

<script src="<?php echo base_url(); ?>scripts/report/inventory/stock_balance.js"></script>
<?php $this->load->view('include/footer'); ?>
