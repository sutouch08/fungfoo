<div class="row">
  <div class="col-sm-12">
    <table class="table table-bordered border-1">
      <thead>
        <tr>
          <th class="width-5 text-center"><?php label('num'); ?></th>
          <th class="width-20"><?php label('item_code'); ?></th>
          <th class=""><?php label('item_name'); ?></th>
          <th class="width-10 text-center"><?php label('price'); ?></th>
          <th class="width-10 text-center"><?php label('qty'); ?></th>
          <th class="width-20 text-center"><?php label('amount'); ?></th>
          <th class="width-5 text-center"></th>
        </tr>
      </thead>
      <tbody id="detail-table">
      <?php if(!empty($details)) : ?>
        <?php $no = 1; ?>
        <?php $total_qty = 0; ?>
        <?php $total_amount = 0; ?>
        <?php foreach($details as $rs) : ?>
        <tr id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle"><?php echo $rs->product_name; ?></td>
          <td class="middle text-right">
            <?php if($po->status > 0) : ?>
              <?php echo number($rs->price, 2); ?>
            <?php else : ?>
            <input class="form-control input-sm text-right price" type="number" step="any" name="price[<?php echo $rs->id; ?>]" id="price-<?php echo $rs->id; ?>" value="<?php echo $rs->price; ?>">
            <?php endif; ?>
          </td>
          <td class="middle text-right">
            <?php if($po->status > 0) : ?>
              <?php echo number($rs->qty, 2); ?>
            <?php else : ?>
            <input class="form-control input-sm text-right qty" type="number" step="1" name="qty[<?php echo $rs->id; ?>]" id="qty-<?php echo $rs->id; ?>" value="<?php echo $rs->qty; ?>">
            <?php endif; ?>
          </td>
          <td class="middle text-right amount" >
            <span id="amount-<?php echo $rs->id; ?>"><?php echo number($rs->total_amount, 2); ?></span>
          </td>
          <td class="width-5 text-center">
            <?php if($po->status == 0 OR ($this->pm->can_edit && $rs->received == 0 )) : ?>
            <button type="button" class="btn btn-minier btn-danger" onclick="removeDetail(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
            <i class="fa fa-trash"></i>
            <?php endif ?>
          </button>
          </td>
        </tr>
          <?php $no++; ?>
          <?php $total_qty += $rs->qty; ?>
          <?php $total_amount += $rs->total_amount; ?>
        <?php endforeach; ?>
        <tr>
          <td colspan="4" class="text-right"><?php label('total'); ?></td>
          <td class="text-right" id="total-qty"><?php echo number($total_qty); ?></td>
          <td class="text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
          <td></td>
        </tr>
      <?php else : ?>

      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<script id="detail-template" type="text/x-handlebars-template">
  {{#each this}}
    {{#if @last}}
    <tr>
      <td colspan="4" class="text-right"><?php label('total'); ?></td>
      <td class="text-right">{{total_qty}}</td>
      <td class="text-right">{{total_amount}} </td>
      <td></td>
    </tr>
    {{else}}
    <tr id="row-{{id}}">
      <td class="middle text-center no">{{no}}</td>
      <td class="middle">{{product_code}}</td>
      <td class="middle">{{product_name}}</td>
      <td class="middle text-center">
        <?php if($po->status > 1) : ?>
          {{price}}
        <?php else : ?>
        <input class="form-control input-sm text-right" type="number" step="any" name="price[{{id}}]" id="price-{{id}}" value="{{price}}">
        <?php endif; ?>
      </td>
      <td class="middle text-center">
        <?php if($po->status > 1) : ?>
          {{qty}}
        <?php else : ?>
        <input class="form-control input-sm text-right" type="number" step="1" name="qty[{{id}}" id="qty-{{id}}" value="{{qty}}">
        <?php endif; ?>
      </td>
      <td class="middle text-right row-amount">
        <span id="amount-{{id}}">{{amount}}</span>
      </td>
      <td class="middle text-center">
        <?php if($po->status < 2) : ?>
        <button type="button" class="btn btn-minier btn-danger" onclick="removeDetail({{id}}, '{{product_code}}')"><i class="fa fa-trash"></i></button>
        <?php endif; ?>
      </td>
    </tr>
    {{/if}}
{{/each}}
</script>
