
<div class="row">
	<div class="col-sm-12">
		<p class="pull-right red">** เชื่อมโยงสินค้าได้เฉพาะสถานะ "รอดำเนินการ" เท่านั้น</p>
    	<table class="table table-striped border-1">
        <thead>
        	<tr class="font-size-12">
          	<th class="width-5 text-center">No.</th>
            <th class="width-5 text-center"></th>
            <th class="width-15">รหัสสินค้า</th>
						<th class="width-30">ชื่อสินค้า</th>
						<th class="width-10 text-center">จำนวน</th>
						<th class="width-5 text-center">ไม่คืน</th>
						<th class="width-15">สินค้าแปรสภาพ</th>
						<th class="width-10 text-center"></th>
            <th class="width-5 text-center"></th>
          </tr>
        </thead>
        <tbody id="detail-table">

<?php if(!empty($details)) : ?>
<?php 	$no = 1; 							?>
<?php 	$total_qty = 0;		?>
<?php	foreach($details as $rs) : ?>

<?php 	$checked = $rs->hasTransformProduct === FALSE ? 'checked' : ''; ?>
			<tr class="font-size-10" id="row_<?php echo $rs->id; ?>">
      	<td class="middle text-center">
					<?php echo $no; ?>
				</td>

        <td class="middle text-center padding-0">
        	<img src="<?php echo get_product_image($rs->product_code, 'mini'); ?>" width="40px" height="40px"  />
        </td>

        <td class="middle">
					<?php echo $rs->product_code; ?>
				</td>

				<td class="middle">
					<?php echo $rs->product_name; ?>
				</td>

				<td class="middle text-center qty" id="qty-<?php echo $rs->id; ?>">
					<?php echo number($rs->qty); ?>
				</td>


				<td class="middle text-center">
				<?php if( $order->is_expired == 0) : ?>
				<?php  $active = $order->state < 3 ? '' : 'disabled'; ?>
					<input type="checkbox"
					class="ace not-return"
					id="chk-<?php echo $rs->id; ?>"
					onchange="toggleReturn(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')"
					<?php echo $checked; ?>
					<?php echo $active;?>
					/>
          <span class="lbl"></span>
				<?php else : ?>
					<?php echo $rs->hasTransformProduct === TRUE ? '' : is_active(1); ?>
				<?php endif; ?>
				</td>


        <td class="middle" id="transform-box-<?php echo $rs->id; ?>">
					<?php
						//---	รายการสินค้าที่เชื่อมโยงแล้ว
						echo getTransformProducts($rs->transform_product, $order->state, $order->is_expired);
					 ?>
					<!--- ยอดรวมของสินค้าที่เชื่อมโยงแล้ว -->
					<input type="hidden" id="transform-qty-<?php echo $rs->id; ?>" value="<?php echo $rs->sum_transform_product_qty; ?>" />
				</td>

        <td class="text-center" id="connect-box-<?php echo $rs->id; ?>">
				<?php if( $order->is_expired == 0 && $rs->hasTransformProduct === TRUE && $order->state < 3 ) : ?>
					<button type="button" class="btn btn-xs btn-success btn-block connect" id="btn-connect-<?php echo $rs->id; ?>" onclick="addTransformProduct(<?php echo $rs->id; ?>,'<?php echo $rs->product_code; ?>')"><i class="fa fa-plus"></i> เชื่อมโยง</button>
				<?php endif; ?>
        </td>

        <td class="middle text-right">
        <?php if( $order->is_expired == 0 && ($this->pm->can_edit OR $this->pm->can_add) && $order->state < 3 ) : ?>
        	<button type="button" class="btn btn-xs btn-danger" onclick="removeDetail(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')"><i class="fa fa-trash"></i></button>
        <?php endif; ?>
        </td>
			</tr>

<?php	$total_qty += $rs->qty;	?>
<?php		$no++; ?>
<?php 	endforeach; ?>
			<tr class="font-size-12">
        <td colspan="7" class="text-right"><b>จำนวนรวม</b></td>
        <td class="text-right"><b><?php echo number($total_qty); ?></b></td>
        <td class="text-center"><b>Pcs.</b></td>
      </tr>
<?php else : ?>
			<tr>
        <td colspan="9" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>

        </tbody>
        </table>
    </div>
</div>
<!---  End Order Detail --->

<div class="modal fade" id="transform-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:500px;">
		<div class="modal-content">
  		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" >เพิ่มการเชื่อมโยง</h4>
        <input type="hidden" id="id_order_detail" value="" />
				<input type="hidden" id="detail-qty" value="" />
				<input type="hidden" id="transform_product" value="" />
				<input type="hidden" id="original_product" value="" />
			 </div>
			 <div class="modal-body">
				 <div class="row">
				 	<div class="col-sm-3">
				 		<label>จำนวน</label>
						<input type="text" class="form-control input-sm text-center" id="trans-qty" value="" />
						<span class="help-block red not-show" id="qty-error">error</span>
				 	</div>
					<div class="col-sm-9">
						<label>สินค้าแปรสภาพ</label>
						<input type="text" class="form-control input-sm" id="trans-product" value="" />
						<span class="help-block red not-show" id="product-error">error</span>
					</div>
					<div class="col-sm-12">

					</div>
				 </div>

			 </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToTransform()" >เชื่อมโยง</button>
			 </div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:500px;">
		<div class="modal-content">
  		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" >แก้ไขการเชื่อมโยง</h4>
        <input type="hidden" id="id-order-detail" value="" />
				<input type="hidden" id="max-qty" value="" />
				<input type="hidden" id="id-product" value="" />
			 </div>
			 <div class="modal-body">
				 <div class="row">
				 	<div class="col-sm-3">
				 		<label>จำนวน</label>
						<input type="number" class="form-control input-sm text-center" id="edit-trans-qty" value="" />
						<span class="help-block red not-show" id="edit-qty-error">error</span>
				 	</div>
					<div class="col-sm-9">
						<label>สินค้าแปรสภาพ</label>
						<input type="text" class="form-control input-sm" id="tr-product" value="" disabled />
					</div>
					<div class="col-sm-12">

					</div>
				 </div>

			 </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="updateTransformQty()" >เชื่อมโยง</button>
			 </div>
		</div>
	</div>
</div>


<!--- order detail template ------>
<script id="detail-table-template" type="text/x-handlebars-template">
{{#each this}}
	{{#if @last}}
        <tr>
        	<td colspan="7" class="text-right" ><b>จำนวนรวม</b></td>
          <td class="text-right"><b>{{ total_qty }}</b></td>
          <td class="text-center"><b>Pcs.</b></td>
        </tr>
	{{else}}
        <tr class="font-size-10" id="row_{{ id }}">
            <td class="middle text-center">{{ no }}</td>
            <td class="middle text-center padding-0">
            	<img src="{{ imageLink }}" width="40px" height="40px"  />
            </td>
            <td class="middle">{{ productCode }}</td>

            <td class="middle">{{ productName }}</td>

            <td class="middle text-center qty" id="qty-{{ id }}">{{ qty }}</td>

						<td class="middle text-center">
							<input type="checkbox" class="not-return" id="chk-{{ id }}" onchange="toggleReturn({{ id }},'{{productCode}}')" {{checkbox}} />
						</td>

            <td class="middle" id="transform-box-{{ id }}">
							{{{transProduct }}}
							<input type="hidden" id="transform-qty-{{ id }}" value="{{ trans_qty }}" />
						</td>

            <td class="middle text-right" id="connect-box-{{ id }}">
							{{#if button}}
							<button type="button" class="btn btn-xs btn-success btn-block connect" id="btn-connect-{{ id }}" onclick="addTransformProduct({{ id }}, '{{productCode}}')"><i class="fa fa-plus"></i> เชื่อมโยง</button>
							{{/if}}
						</td>

            <td class="middle text-right">
            <?php if($this->pm->can_edit OR $this->pm->can_add ) : ?>
            	<button type="button" class="btn btn-xs btn-danger" onclick="removeDetail({{ id }}, '{{ productCode }}')"><i class="fa fa-trash"></i></button>
            <?php endif; ?>
            </td>
        </tr>
	{{/if}}
{{/each}}
</script>

<script id="nodata-template" type="text/x-handlebars-template">
	<tr>
          <td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
    </tr>
</script>
