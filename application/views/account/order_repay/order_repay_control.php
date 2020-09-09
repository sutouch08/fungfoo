
<form id="updateForm" method="post" action="<?php echo $this->home; ?>/save/<?php echo $doc->code; ?>">
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">ลำดับ</th>
          <th class="">เลขที่</th>
          <th class="width-10">ครบกำหนด</th>
          <th class="width-10 text-right">จำนวนเงิน</th>
          <th class="width-10 text-right">ค้างชำระ</th>
          <th class="width-15 text-right">ยอดชำระ</th>
          <th class="width-5 text-right"></th>
        </tr>
      </thead>
      <tbody id="details">
        <?php $amount = 0; ?>
        <?php $balance = 0; ?>
        <?php $pay_amount = 0; ?>
  <?php if(!empty($details)) : ?>
    <?php $no = 1; ?>
    <?php foreach($details as $rs) : ?>
        <tr id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->reference; ?></td>
          <td class="middle"><?php echo thai_date($rs->due_date); ?></td>
          <td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
          <td class="middle text-right"><?php echo number($rs->balance, 2); ?></td>
          <td class="middle text-right">
            <?php if($this->pm->can_edit && $doc->status == 0) : ?>
            <input type="number" step="any" class="form-control input-sm input-small pull-right text-right input-amount" name="pay_amount[<?php echo $rs->id; ?>]" id="pay_amount_<?php echo $rs->id; ?>" value="<?php echo $rs->pay_amount; ?>">
            <?php else : ?>
              <?php echo number($rs->pay_amount, 2); ?>
            <?php endif; ?>
          </td>
          <td class="middle text-right">
            <?php if($this->pm->can_edit && $doc->status == 0) : ?>
              <button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->reference; ?>', <?php echo $rs->id; ?>)">
                <i class="fa fa-trash"></i>
              </button>
            <?php endif; ?>
          </td>
        </tr>
        <?php $no++; ?>
        <?php $amount += $rs->amount; ?>
        <?php $balance += $rs->balance; ?>
        <?php $pay_amount += $rs->pay_amount; ?>
    <?php endforeach; ?>
  <?php endif; ?>
        <tr>
          <td colspan="3" class="text-right">รวม</td>
          <td class="text-right" id="total_amount"><?php echo number($amount, 2); ?></td>
          <td class="text-right" id="total_balance"><?php echo number($balance,2); ?></td>
          <td class="text-right" id="total_pay_amount"><?php echo number($pay_amount,2); ?></td>
          <td></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>


<script id="detail-template" type="text/x-handlebarsTemplate">
{{#each this}}
{{#if @last}}
<tr>
  <td colspan="3" class="text-right">รวม</td>
  <td class="text-right" id="total_amount">{{total_amount}}</td>
  <td class="text-right" id="total_balance">{{total_balance}}</td>
  <td class="text-right" id="total_pay_amount">{{total_pay_amount}}</td>
  <td></td>
</tr>
{{else}}
<tr id="row-{{id}}">
  <td class="middle text-center no">{{no}}</td>
  <td class="middle">{{reference}}</td>
  <td class="middle">{{due_date}}</td>
  <td class="middle text-right">{{amount}}</td>
  <td class="middle text-right">{{balance}}</td>
  <td class="middle text-center">
    <?php if($this->pm->can_edit && $doc->status == 0) : ?>
    <input type="number" step="any" class="form-control input-sm input-small pull-right text-right input-amount" name="pay_amount[{{id}}]" id="pay_amount_{{id}}" value="{{pay_amount}}">
    <?php else : ?>
      {{pay_amount}}
    <?php endif; ?>
  </td>
  <td class="middle text-right">
    <?php if($this->pm->can_edit && $doc->status == 0) : ?>
      <button type="button" class="btn btn-mini btn-danger" onclick="getDelete('{{reference}}', {{id}})">
        <i class="fa fa-trash"></i>
      </button>
    <?php endif; ?>
  </td>
</tr>
{{/if}}
{{/each}}
</script>
