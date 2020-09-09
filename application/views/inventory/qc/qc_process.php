<?php $this->load->view('include/header'); ?>
<div class="row top-row">
  <div class="col-sm-6 top-col">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> รอตรวจ</button>
      <button type="button" class="btn btn-sm btn-yellow" onclick="viewProcess()"><i class="fa fa-arrow-left"></i> กำลังตรวจ</button>
    </p>
  </div>

</div>

<hr class="margin-top-10 margin-bottom-10" />
<?php if($order->state != 6) : ?>
<?php   $this->load->view('inventory/qc/invalid_state'); ?>
<?php else : ?>

  <div class="row">
    <div class="col-sm-2 padding-5 first">
      <label>เลขที่ : <?php echo $order->code; ?></label>
    </div>
    <div class="col-sm-5 padding-5">
      <label>ลูกค้า/ผู้เบิก/ผู้ยืม : &nbsp;
    <?php echo ($order->customer_ref == '' ? $order->customer_name : $order->customer_ref);  ?>
      </label>
    </div>
    <div class="col-sm-3">
      <label>ช่องทาง : <?php echo $order->channels_name; ?></label>
    </div>
    <div class="col-sm-2 padding-5 last text-right">
      <label>วันที่ : <?php echo thai_date($order->date_add); ?></label>
    </div>
  <?php if($order->remark != '') : ?>
    <div class="col-sm-12 margin-top-10">
      <label>หมายเหตุ : <?php echo $order->remark; ?></label>
    </div>
  <?php endif; ?>

    <input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
    <input type="hidden" id="id_box" value="" />
  </div>
  <hr />
<?php
    $this->load->view('inventory/qc/qc_box');
    $this->load->view('inventory/qc/qc_control');
    $this->load->view('inventory/qc/qc_incomplete_list');
    $this->load->view('inventory/qc/qc_complete_list');
?>

<?php endif; ?>

  <!--************** Address Form Modal ************-->
  <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="addressModal" aria-hidden="true">
    <div class="modal-dialog" style="width:500px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="colse" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" id="info_body">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-primary" onclick="printSelectAddress()"><i class="fa fa-print"></i> พิมพ์</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="optionModal" aria-hidden="true">
    <div class="modal-dialog" style="width:500px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="edit-title"></h4>
        </div>
        <div class="modal-body" id="edit-body">

        </div>
      </div>

    </div>
  </div>

<script id="edit-template" type="text/x-handlebarsTemplate">
  <div class="row">
    <div class="col-sm-12">
      <table class="table table-striped">
        <thead>
          <tr>
            <th class="width-20">รหัส</th>
            <th class="width-40">กล่อง</th>
            <th class="width-15 text-center">ในกล่อง</th>
            <th class="width-15 text-center">เอาออก</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
      {{#each this}}
        <tr>
          <td>{{barcode}}</td>
          <td>กล่องที่ {{box_no}}</td>
          <td class="text-center"><span id="label-{{id_qc}}">{{qty}}</span></td>
          <td class="text-center">
            <input type="number" class="form-control input-sm text-center" id="input-{{id_qc}}" />
          </td>
          <td class="text-right">
          <?php if($this->pm->can_delete) : ?>
            <button type="button" class="btn btn-sm btn-danger" onclick="updateQty({{id_qc}})">Update</button>
          <?php endif; ?>
          </td>
        </tr>
      {{/each}}
        </tbody>
      </table>
    </div>
  </div>
  </script>

<?php
if(!empty($barcode_list))
{
  foreach($barcode_list as $id => $barcode)
  {
    echo '<input type="hidden" class="'.$barcode.'" id="'.$id.'" value="1" />';
  }
}
 ?>

<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_process.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_control.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
<?php $this->load->view('include/footer'); ?>
