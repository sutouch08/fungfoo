<?php
$this->load->helper('print');

$doc = doc_type($order->role); //--- print_helper

$page  = '';
$page .= $this->printer->doc_header();

$this->printer->add_title($doc['title']);

$header		= get_header($order);

$this->printer->add_header($header);

//--- ถ้าเป็นฝากขาย(2) หรือ เบิกแปรสภาพ(5) หรือ ยืมสินค้า(6)
//--- รายการพวกนี้ไม่มีการบันทึกขาย ใช้การโอนสินค้าเข้าคลังแต่ละประเภท
//--- ฝากขาย โอนเข้าคลังฝากขาย เบิกแปรสภาพ เข้าคลังแปรสภาพ  ยืม เข้าคลังยืม
//--- รายการที่จะพิมพ์ต้องเอามาจากการสั่งสินค้า เปรียบเทียบ กับยอดตรวจ ที่เท่ากัน หรือ ตัวที่น้อยกว่า

$total_row 	= count($details);
$shipping_row = $order->shipping_fee > 0 ? 1 : 0;
$service_row = $order->service_fee > 0 ? 1 : 0;
$deposit_row = $order->deposit > 0 ? 1 : 0;
$subtotal_row = 4 + $shipping_row + $service_row + $deposit_row;

$config 		= array(
                "total_row" => $total_row,
                "font_size" => 12,
                "header_rows" => 3,
                "sub_total_row" => $subtotal_row
            );

$this->printer->config($config);

$row 		     = $this->printer->row;
$total_page  = $this->printer->total_page;
$total_qty 	 = 0; //--  จำนวนรวม
$total_amount 		= 0;  //--- มูลค่ารวม(หลังหักส่วนลด)
$total_discount 	= 0; //--- ส่วนลดรวม
$total_order  = 0;    //--- มูลค่าราคารวม

$bill_discount		= $order->bDiscAmount;


//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
          array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
          array("บาร์โค้ด", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
          array("สินค้า", "width:35%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
          array("ราคา", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
          array("จำนวน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
          array("ส่วนลด", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
          array("มูลค่า", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
          );

$this->printer->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
            "text-align: center; border-top:0px;",
            "border-left: solid 1px #ccc; border-top:0px; padding: 0px; text-align:center;",
            "border-left: solid 1px #ccc; border-top:0px;",
            "text-align:center; border-left: solid 1px #ccc; border-top:0px;",
            "text-align:center; border-left: solid 1px #ccc; border-top:0px;",
            "text-align:center; border-left: solid 1px #ccc; border-top:0px;",
            "text-align:right; border-left: solid 1px #ccc; border-top:0px;"
            );

$this->printer->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//
$footer	= array(
          array("ผู้รับของ", "ได้รับสินค้าถูกต้องตามรายการแล้ว","วันที่............................."),
          array("ผู้ส่งของ", "","วันที่............................."),
          array("ผู้ตรวจสอบ", "","วันที่............................."),
          array("ผู้อนุมัติ", "","วันที่.............................")
          );

$this->printer->set_footer($footer);


$n = 1;
$index = 0;
while($total_page > 0 )
{
  $page .= $this->printer->page_start();
  $page .= $this->printer->top_page();
  $page .= $this->printer->content_start();
  $page .= $this->printer->table_start();
  $i = 0;

  while($i<$row)
  {
    $rs = isset($details[$index]) ? $details[$index] : FALSE;

    if( ! empty($rs) )
    {
      //--- จำนวนสินค้า ถ้ามีการบันทึกขาย จะได้ข้อมูลจาก tbl_order_sold ซึ่งเป็น qty
      //--- แต่ถ้าไม่มีการบันทึกขายจะได้ข้อมูลจาก tbl_order_detail Join tbl_qc
      //--- ซึ่งได้จำนวน มา 3 ฟิลด์ คือ oreder_qty, prepared, qc
      //--- ต้องเอา order_qty กับ qc มาเปรียบเทียบกัน ถ้าเท่ากัน อันไหนก็ได้ ถ้าไม่เท่ากัน เอาอันที่น้อยกว่า
      $qty = $rs->qty;

      //--- ราคาสินค้า
      $price = $rs->price;

      //--- ส่วนลดสินค้า (ไว้แสดงไม่มีผลในการคำนวณ)
      $discount = $rs->discount_label;

      //--- ส่วนลดสินค้า (มีผลในการคำนวณ)
      //--- ทั้งสองตารางใช้ชือฟิลด์ เดียวกัน
      $discount_amount = $rs->discount_amount;

      //--- มูลค่าสินค้า หลังหักส่วนลดตามรายการสินค้า
      $amount = $rs->total_amount;

      $barcode = $is_barcode === FALSE ? $rs->barcode : barcodeImage($rs->barcode);
      //--- เตรียมข้อมูลไว้เพิ่มลงตาราง
      $data = array(
                    $n,
                    $barcode,
                    inputRow($rs->product_code.' : '.$rs->product_name),
                    number($price, 2),
                    number($qty),
                    inputRow($discount,'text-align:center;'),
                    inputRow(number($amount, 2), 'text-align:right;')
                );

      $total_qty      += $qty;
      $total_amount   += $amount;
      $total_discount += $discount_amount;
      $total_order    += ($qty * $price);
    }
    else
    {
      $data = array("", "", "", "","", "","");
    }

    $page .= $this->printer->print_row($data);

    $n++;
    $i++;
    $index++;
  }

  $page .= $this->printer->table_end();

  if($this->printer->current_page == $this->printer->total_page)
  {
    $qty  = number($total_qty);
    $total_order = number($total_order, 2);
    $total_discount_amount = number(($total_discount + $bill_discount),2);
    $net_amount = number( ($total_amount + $order->shipping_fee + $order->service_fee) - $bill_discount - $order->deposit, 2);
    $service_fee = number($order->service_fee, 2);
    $shipping_fee = number($order->shipping_fee, 2);
    $deposit = number($order->deposit, 2);
    $remark = $order->remark;
  }
  else
  {
    $qty = "";
    $amount = "";
    $shipping_fee = "";
    $service_fee = "";
    $deposit = '';
    $total_discount_amount = "";
    $net_amount = "";
    $remark = "";
  }

  $subTotal = array();

  //--- จำนวนรวม   ตัว
  $sub_qty  = '<td class="width-60 subtotal-first text-center" style="height:'.$this->printer->row_height.'mm;">';
  //$sub_qty .=  '**** ส่วนลดท้ายบิล '.$bill_discount.' ****';
  $sub_qty .= '</td>';
  $sub_qty .= '<td class="width-20 subtotal">';
  $sub_qty .=  '<strong>จำนวนรวม</strong>';
  $sub_qty .= '</td>';
  $sub_qty .= '<td class="width-20 subtotal text-right">';
  $sub_qty .=    $qty;
  $sub_qty .= '</td>';

  array_push($subTotal, array($sub_qty));

  //--- ราคารวม
  $sub_price  = '<td rowspan="'.($subtotal_row).'" class="subtotal-first font-size-10" style="height:'.$this->printer->row_height.'mm;">';
  $sub_price .=  '<strong>หมายเหตุ : </strong> '.$order->remark;
  $sub_price .= '</td>';
  $sub_price .= '<td class="subtotal">';
  $sub_price .=  '<strong>ราคารวม</strong>';
  $sub_price .= '</td>';
  $sub_price .= '<td class="subtotal text-right">';
  $sub_price .=  $total_order;
  $sub_price .= '</td>';
  array_push($subTotal, array($sub_price));

  //--- ส่วนลดรวม
  $sub_disc  = '<td class="subtotal" style="height:'.$this->printer->row_height.'mm;">';
  $sub_disc .=  '<strong>ส่วนลดรวม</strong>';
  $sub_disc .= '</td>';
  $sub_disc .= '<td class="subtotal text-right"> -';
  $sub_disc .=  $total_discount_amount;
  $sub_disc .= '</td>';
  array_push($subTotal, array($sub_disc));

  //--- shipping_fee
  if($order->shipping_fee > 0)
  {
    $sub_ship  = '<td class="subtotal" style="height:'.$this->printer->row_height.'mm;">';
    $sub_ship .=  '<strong>ค่าจัดส่ง</strong>';
    $sub_ship .= '</td>';
    $sub_ship .= '<td class="subtotal text-right">';
    $sub_ship .=  $shipping_fee;
    $sub_ship .= '</td>';
    array_push($subTotal, array($sub_ship));
  }

  //--- service_fee
  if($order->service_fee > 0)
  {
    $sub_serv  = '<td class="subtotal" style="height:'.$this->printer->row_height.'mm;">';
    $sub_serv .=  '<strong>อื่นๆ</strong>';
    $sub_serv .= '</td>';
    $sub_serv .= '<td class="subtotal text-right">';
    $sub_serv .=  $service_fee;
    $sub_serv .= '</td>';
    array_push($subTotal, array($sub_serv));
  }

  //--- deposit
  if($order->deposit > 0)
  {
    $sub_depo  = '<td class="subtotal" style="height:'.$this->printer->row_height.'mm;">';
    $sub_depo .=  '<strong>ชำระแล้ว</strong>';
    $sub_depo .= '</td>';
    $sub_depo .= '<td class="subtotal text-right"> -';
    $sub_depo .=  $deposit;
    $sub_depo .= '</td>';
    array_push($subTotal, array($sub_depo));
  }

  //--- ยอดสุทธิ
  $sub_net  = '<td class="subtotal" style="height:'.$this->printer->row_height.'mm;">';
  $sub_net .=  '<strong>ยอดเงินสุทธิ</strong>';
  $sub_net .= '</td>';
  $sub_net .= '<td class="subtotal text-right">';
  $sub_net .=  $net_amount;
  $sub_net .= '</td>';

  array_push($subTotal, array($sub_net));

  $page .= $this->printer->print_sub_total($subTotal);
  $page .= $this->printer->content_end();
  $page .= $this->printer->footer;
  $page .= $this->printer->page_end();

  $total_page --;
  $this->printer->current_page++;
}

$page .= $this->printer->doc_footer();

echo $page;
 ?>
