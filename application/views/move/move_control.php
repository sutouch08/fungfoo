<?php if($barcode) : ?>

  <div class="row">
  	<div class="col-sm-2">
      	<button type="button" class="btn btn-xs btn-default btn-block" onclick="showMoveTable()">แสดงรายการ</button>
      </div>
  	<div class="col-sm-2 control-btn">
      	<button type="button" class="btn btn-xs btn-danger btn-block" onclick="getMoveOut()">ย้ายสินค้าออก</button>
      </div>
      <div class="col-sm-2 control-btn">
      	<button type="button" class="btn btn-xs btn-info btn-block" onclick="getMoveIn()">ย้ายสินค้าเข้า</button>
      </div>
  </div>

  <hr id="barcode-hr" class="margin-top-15 margin-bottom-15 hide" />

  <div class="row">
  	<div class="col-sm-12 moveOut-zone hide">
      <div class="row">
        <div class="col-sm-3 padding-5 first">
          <label>โซนต้นทาง</label>
          <input type="text" class="form-control input-sm" id="fromZone-barcode" placeholder="ยิงบาร์โค้ดโซน" />
        </div>

        <div class="col-sm-1 padding-5">
          <label class="display-block not-show">newZone</label>
          <button type="button" class="btn btn-xs btn-info btn-block" id="btn-new-zone" onclick="newFromZone()" disabled >โซนใหม่</button>
        </div>

        <div class="col-sm-1 padding-5">
          <label>จำนวน</label>
          <input type="number" class="form-control input-sm text-center" id="qty-from" value="1" disabled />
        </div>

        <div class="col-sm-3 padding-5">
          <label>บาร์โค้ดสินค้า</label>
          <input type="text" class="form-control input-sm" id="barcode-item-from" placeholder="ยิงบาร์โค้ดเพื่อย้ายสินค้าออก" disabled />
        </div>
      </div>
    </div>

    <div class="col-sm-12 moveIn-zone hide">
      <div class="row">
        <div class="col-sm-3 padding-5 first">
          <label>บาร์โค้ดโซน</label>
          <input type="text" class="form-control input-sm" id="toZone-barcode" placeholder="ยิงบาร์โค้ดโซนปลายทาง" />
        </div>
        <div class="col-sm-4 padding-5">
          <label class="display-block not-show">zoneName</label>
          <input type="text" class="form-control input-sm" id="zoneName-label" disabled />
        </div>
        <div class="col-sm-1 padding-5">
          <label class="display-block not-show">newzone</label>
        	<button type="button" class="btn btn-xs btn-info btn-block" id="btn-new-to-zone" onclick="newToZone()" disabled>โซนใหม่</button>
        </div>
        <div class="col-sm-1 padding-5">
          <label>จำนวน</label>
          <input type="number" class="form-control input-sm text-center" id="qty-to" value="1" disabled />
        </div>
        <div class="col-sm-3 padding-5 last">
          <label>บาร์โค้ดสินค้า</label>
          <input type="text" class="form-control input-sm" id="barcode-item-to" placeholder="ยิงบาร์โค้ดเพื่อย้ายสินค้าออก" disabled />
        </div>
      </div>
    </div>
  </div>

<?php else : ?>

<div class="row">
  <div class="col-sm-4 padding-5 first">
    <label>ต้นทาง</label>
    <input type="text" class="form-control input-sm" id="from-zone" placeholder="ค้นหาชื่อโซน" autofocus />
  </div>
  <!--
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">ok</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductInZone()">แสดงสินค้า</button>
  </div>
-->
  <div class="col-sm-4 padding-5">
    <label>ปลายทาง</label>
    <input type="text" class="form-control input-sm" id="to-zone" placeholder="ค้นหาชื่อโซน" />
  </div>

  <div class="col-sm-2">
    <label class="display-block not-show">ok</label>
    <button type="button" class="btn btn-xs btn-default btn-block" onclick="showMoveTable()">แสดงรายการ</button>
  </div>


</div>
<?php endif; ?>
<input type="hidden" name="from_zone_code" id="from_zone_code" value="" />
<input type="hidden" name="to_zone_code" id="to_zone_code" value="" />
<hr class="margin-top-15 margin-bottom-15" />
