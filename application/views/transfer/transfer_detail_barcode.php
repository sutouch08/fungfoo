<div class="row">
	<div class="col-sm-12 hide" id="zone-table">
    	<table class="table table-striped table-bordered">
      	<thead>
					<tr>
						<th colspan="4" class="text-center">
							<h4 class="title" id="zoneName"></h4>
						</th>
					</tr>
          <tr>
          	<th class="width-10 text-center">ลำดับ</th>
            <th class="width-20 text-center">บาร์โค้ด</th>
            <th class="text-center">สินค้า</th>
            <th class="width-10 text-center">จำนวน</th>
          </tr>
          </thead>

          <tbody id="zone-list"> </tbody>

        </table>
    </div>


		<div class="col-sm-12 hide" id="temp-table">
    	<table class="table table-striped table-bordered">
      	<thead>
          <tr>
          	<th colspan="5" class="text-center">
             รายการใน Temp
            </th>
            </tr>
          	<tr>
            	<th class="width-5 text-center">ลำดับ</th>
              <th class="width-15 text-center">บาร์โค้ด</th>
              <th class="width-45 text-center">สินค้า</th>
              <th class="width-25 text-center">ต้นทาง</th>
              <th class="width-10 text-center">จำนวน</th>
            </tr>
          </thead>
          <tbody id="temp-list">

          </tbody>
        </table>
    </div>


	<div class="col-sm-12" id="transfer-table">
  	<table class="table table-striped border-1">
    	<thead>
      	<tr>
        	<th colspan="7" class="text-center">รายการโอนย้าย</th>
        </tr>

				<tr>
        	<th class="width-5 text-center">ลำดับ</th>
          <th class="width-15">บาร์โค้ด</th>
          <th class="width-20">สินค้า</th>
          <th class="width-25">ต้นทาง</th>
          <th class="width-25">ปลายทาง</th>
          <th class="width-10 text-center">จำนวน</th>
          <th class="width-5"></th>
        </tr>
      </thead>

      <tbody id="transfer-list">
<?php if(!empty($details)) : ?>
<?php		$no = 1;						?>
<?php   $total_qty = 0;  ?>
<?php		foreach($details as $rs) : 	?>
				<tr class="font-size-12" id="row-<?php echo $rs->id; ?>">

	      	<td class="middle text-center no">
						<?php echo $no; ?>
					</td>

					<!--- บาร์โค้ดสินค้า --->
	        <td class="middle">
						<?php echo $rs->barcode; ?>
					</td>

					<!--- รหัสสินค้า -->
	        <td class="middle">
						<?php echo $rs->product_code; ?>
					</td>

					<!--- โซนต้นทาง --->
	        <td class="middle">
						<?php echo $rs->from_zone_name; ?>
	        </td>


	        <td class="middle" id="row-label-<?php echo $rs->id; ?>">
	        	<?php echo $rs->to_zone_name; ?>
	        </td>

	        <td class="middle text-center qty" >
						<?php echo number($rs->qty); ?>
					</td>

					<td class="middle text-center">
	          <?php if($this->pm->can_edit && $rs->valid == 0) : ?>
	          	<button type="button" class="btn btn-minier btn-danger"
							onclick="deleteMoveItem(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
								<i class="fa fa-trash"></i>
							</button>
	          <?php endif; ?>
	        </td>

	      </tr>
<?php			$no++;			?>
<?php 	  $total_qty += $rs->qty; ?>
<?php		endforeach;			?>
				<tr>
					<td colspan="5" class="middle text-right"><strong>รวม</strong></td>
					<td class="middle text-center" id="total"><?php echo number($total_qty); ?></td>
					<td></td>
				</tr>
<?php	else : ?>
 				<tr>
        	<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
        </tr>
<?php	endif; ?>
      </tbody>
    </table>
  </div>
</div>


<script id="zoneTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
		<tr>
			<td colspan="6" class="text-center">
				<h4>ไม่พบสินค้าในโซน</h4>
			</td>
		</tr>
	{{else}}
		<tr>
			<td align="center">{{ no }}</td>
		  <td align="center">{{ barcode }}</td>
		  <td>
				{{ products }}
				<input type="hidden" id="qty_{{barcode}}" value="{{qty}}" />
			</td>
		  <td align="center" id="qty-label_{{barcode}}">{{ qty }}	</td>
		</tr>
	{{/if}}
{{/each}}
</script>



<script id="tempTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="6" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}

		<tr class="font-size-12" id="row-temp-{{ id }}">
			<td class="middle text-center">{{ no }}</td>
			<td class="middle">{{ barcode }}</td>
			<td class="middle">{{ products }}</td>
			<td class="middle text-center">
				<input type="hidden" id="qty-{{barcode}}" value="{{qty}}" />
				{{ fromZone }}
			</td>

			<td class="middle text-center" id="qty-label-{{barcode}}">
				{{ qty }}
			</td>
		</tr>
	{{/if}}
{{/each}}
</script>



<script id="transferTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}
		{{#if @last}}
			<tr>
				<td colspan="5" class="text-right"><strong>รวม</strong></td>
				<td class="middle text-center" id="total">{{ total }}</td>
				<td></td>
			</tr>
		{{else}}
		<tr class="font-size-12" id="row-{{ id }}">
			<td class="middle text-center no">{{ no }}</td>
			<td class="middle">{{ barcode }}</td>
			<td class="middle">{{ products }}</td>
			<td class="middle">{{ from_zone }}</td>
			<td class="middle">{{{ to_zone }}}</td>
			<td class="middle text-center qty">{{ qty }}</td>
			<td class="middle text-center">{{{ btn_delete }}}</td>
		</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>
