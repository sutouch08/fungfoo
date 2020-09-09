<?php
$paid		= $order->is_paid == 1 ? 'จ่ายแล้ว' : 'รอชำระเงิน';

/*********  Sender  ***********/
$sender	 = '<div class="col-sm-12" style="font-size:14px; font-weight: bold; border:solid 2px #ccc; border-radius:10px; padding:10px;">';
$sender	.= '<span style="display:block; font-size: 20px; font-weight:bold; padding-bottom:10px; border-bottom:solid 2px #ccc; margin-bottom:15px;">ผู้ส่ง &nbsp; |  &nbsp; ';
$sender .= '<span style="font-size:16px; font-weight:500">โทร. '.$cPhone.'</span></span>';
$sender	.= '<span style="display:block;">'.$cName.'</span>';
$sender	.= '<span style="display:block;">'.$cAddress.' '.$cPostCode.'</span>';
$sender	.= '</div>';
/********* / Sender *************/
$cod = '';
if($order->payment_role == 4)
{
  $cod .= '<div class="col-sm-12" style="font-size:35px; text-align:center; vertical-align:text-middle; font-weight: bold;">';
  $cod .= 'COD  : '.number($order->balance, 2).'.-';
  $cod .= '</div>';
}

$shipper = '';
if($order->sender_name !== NULL)
{
  $sender .= '<span style="padding:10px; border:solid 1px #ccc; position:relative; top:25px;">'.$order->sender_name.'</span>';
}
/*********** Receiver  **********/
$receiver	 = '<div class="col-sm-12" style="font-size:24px; border:solid 2px #ccc; border-radius:10px; padding:10px;">';
$receiver	.=  '<span style="display:block; font-size: 20px; font-weight:bold; padding-bottom:10px; border-bottom:solid 2px #ccc; margin-bottom:15px;">ผู้รับ &nbsp; |  &nbsp; ';
$receiver	.=  '<span style="font-size:16px; font-weight:500">โทร. '.$cusPhone.'</span></span>';
$receiver	.=  '<span style="display:block; font-size:18px;">'.$cusName.'</span>';
$receiver	.=  '<span style="display:block; font-size:18px;">'.$cusAdr1.'</span>';
$receiver	.=  '<span style="display:block; font-size:18px;">'.$cusAdr2.'</span>';
//$receiver	.=  '<span style="display:block;">'.$cusDistr.'</span>';
$receiver	.=  '<span style="display:block; font-size:18px;">'.$cusProv.'</span>';
$receiver	.=  '<span style="display:block; margin-top:15px; font-size:18px;">รหัสไปรษณีย์  <span style="font-size:30px;">'.$cusPostCode.'</span></span>';
$receiver	.= '</div>';
/********** / Receiver ***********/

//----------------------------  order detail ---------------------------//
$leftCol = '';
$rightCol = '';

if(!empty($details))
{

  //--------- Left column -----------------//
  $leftCol	.= '<div class="row">';
  $leftCol	.= 		'<div class="col-sm-12">';
  //$leftCol	.= 			$link === FALSE ? '' : '<span style="display:block; margin-bottom:10px;"><img src="'.$link.'" width="50px;" /></span>';
  $leftCol	.= 			'<span style="font-size:12px; font-weight:bold; display:block;">'.$cName.'</span>';
  $leftCol	.= 			'<span style="font-size:12px; display:block;">'.$cAddress.' '.$cPostCode.'</span>';
  $leftCol	.= 		'</div>';
  $leftCol	.= 		'<div class="col-sm-12" style="margin-top:50px;">';
  $leftCol	.= 			'<span style="font-size:12px; font-weight:bold; display:block;">ชื่อ - ที่อยู่จัดส่งลูกค้า</span>';
  $leftCol 	.=			'<span style="font-size:12px; display:block;">รหัสลูกค้า : '.$cusCode.'</span>';
  $leftCol 	.=			'<span style="font-size:12px; display:block;">'.$cusName.'</span>';
  $leftCol	.=			'<span style="font-size:12px; display:bolck;">'.$cusAdr1.' '.$cusAdr2.' '.$cusProv.' '.$cusPostCode.'</span>';
  $leftCol	.= 		'</div>';
  $leftCol	.= '</div>';

  //---------/ Left column --------------//


  //----------- Right column ------------//
  $rightCol	.=	'<div class="row">';
  $rightCol	.= 	'<div class="col-sm-12">';
  $rightCol	.= 		'<p class="pull-right" style="font-size:16px;"><strong>ใบเสร็จ / ใบส่งของ</strong></p>';
  $rightCol	.=	'</div>';
  $rightCol	.= 	'<div class="col-sm-12" style="margin-top:30px; font-size:12px;">';
  $rightCol	.= 		'<p style="float:left; width:20%;">เลขที่บิล</p><p style="float:left; width:35%;">'.$order->code.'</p>';
  $rightCol	.= 		'<p style="float:left; width:45%; text-align:right;">สถานะ <span style="padding-left:15px;">'.$paid.'</span></p>';
  $rightCol	.= 		'<p style="float:left; width:20%;">วันที่สั่งซื้อ</p><p style="float:left; width:35%;">'.thai_short_text_date($order->date_add, TRUE).'</p>';
  $rightCol	.= 		'<p style="float:left; width:45%; text-align:right;">จำนวน<span style="padding-left:10px; padding-right:10px;">';
  $rightCol .=      number($order->total_qty);
  $rightCol .=      '</span>Pcs.</p>';
  $rightCol	.=	'</div>';

  $rightCol	.= 	'<div class="col-sm-12" style="font-size:12px;">';
  $rightCol	.= 	  '<table class="table table-bordered">';
  $rightCol	.= 			'<tr style="font-size:12px">';
  $rightCol	.=				'<td align="center" width="10%">ลำดับ</td>';
  $rightCol	.=				'<td width="30%">สินค้า</td>';
  $rightCol	.=				'<td width="15%" align="center">ราคา</td>';
  $rightCol	.=				'<td width="15%" align="center">จำนวน</td>';
  $rightCol	.=				'<td width="20%" align="right">มูลค่า</td>';
  $rightCol	.=			'</tr>';


  $totalAmount 	= 0;
  $totalDisc		= 0;

  //--- ค่าจัดส่ง
  $shipping_fee	= $order->shipping_fee;

  //--- ค่าบริการอื่นๆ เช่น ติดชื่อ เบอร์ ปักโลโก้
  $service_fee = $order->service_fee;

  $n	= 1;
  foreach($details as $rs)
  {
    //--- ส่วนลด
    $disc				= $rs->discount_amount;

    //--  มูลค่าเต็มราคา
    $amount			= $rs->qty * $rs->price;

    $rightCol	.= 	'<tr style="font-size:10px;">';
    $rightCol	.= 		'<td align="center">'.$n.'</td>';
    $rightCol	.=		'<td>'.$rs->product_code.'</td>';
    $rightCol	.=		'<td align="center">'.number_format($rs->price, 2).'</td>';
    $rightCol	.=		'<td align="center">'.number_format($rs->qty).'</td>';
    $rightCol	.=		'<td align="right">'.number_format($amount, 2).'</td>';
    $rightCol	.=	'</tr>';

    $totalAmount	+= $amount;
    $totalDisc		+= $disc;
    $n++;
  }


  //--- ส่วนลดท้ายบิล
  $totalDisc += $order->bDiscAmount;

  //--- ถ้ามีค่าบริการอื่นๆ ต้องเพิ่มอีก 1 บรรทัด
  $rowSpan = 6;

  $rightCol	.= '<tr style="font-size:10px;">';
  $rightCol .=  '<td colspan="3" rowspan="'.$rowSpan.'"> หมายเหตุ : '.$order->remark.'</td>';
  $rightCol .=  '<td align="right">สินค้า</td>';
  $rightCol .=  '<td align="right">'.number_format($totalAmount, 2).'</td>';
  $rightCol .= '</tr>';

  $rightCol	.= '<tr style="font-size:10px;">';
  $rightCol .=  '<td align="right">ส่วนลด</td>';
  $rightCol .=  '<td align="right"> -'.number_format($totalDisc, 2).'</td>';
  $rightCol .= '</tr>';



  if($order->shipping_fee > 0)
  {
    $rightCol	.= '<tr style="font-size:10px;">';
    $rightCol .=  '<td align="right">ค่าจัดส่ง</td>';
    $rightCol .=  '<td align="right">'.number_format($shipping_fee, 2).'</td>';
    $rightCol .= '</tr>';
  }


  if( $order->service_fee > 0)
  {
    $rightCol	.= '<tr style="font-size:10px;">';
    $rightCol .=  '<td align="right">ค่าบริการอืนๆ</td>';
    $rightCol .=  '<td align="right">'.number_format($service_fee, 2).'</td>';
    $rightCol .= '</tr>';
  }

  if($order->deposit > 0)
  {
    $rightCol	.= '<tr style="font-size:10px;">';
    $rightCol .=  '<td align="right">ชำระแล้ว</td>';
    $rightCol .=  '<td align="right">-'.number_format($shipping_fee, 2).'</td>';
    $rightCol .= '</tr>';
  }

  $rightCol	.= '<tr style="font-size:10px;">';
  $rightCol .=  '<td align="right">รวมสุทธิ</td>';
  $rightCol .=  '<td align="right">'.number_format(($totalAmount - $totalDisc) + $shipping_fee + $service_fee, 2).'</td>';
  $rightCol .= '</tr>';


  $rightCol	.= '</table>';
  $rightCol	.= '</div>';
  $rightCol	.= '</div>';

  //------------/ Right column ----------------//
}
//------------------------------/ order detail --------------------------//


$Page = '';


$config   = array(
              "row" => 13,
              "total_row" => 1,
              "header_row" => 0,
              "footer_row" => 0,
              "sub_total_row" => 0,
              "content_border" => 0
            );

$this->printer->config($config);

$barcode	= "<img src='".base_url()."assets/barcode/barcode.php?text=".$order->code."' style='height:15mm;' />";

$Page .= $this->printer->doc_header();
$Page .= $this->printer->page_start();
$Page .= $this->printer->content_start();
$Page .= '<table style="width:100%; border:0px;">';

$Page .= 	'<tr>';
$Page .= 		'<td valign="top" style="width:40%; padding:10px; white-space:normal;">'.$sender.'</td>';
$Page .=		'<td valign="top" style="padding:10px; white-space:normal;">'.$receiver.'</td>';
$Page .= 	'</tr>';

$Page	 .= '<tr>';
$Page  .=   '<td style="padding:10px;" align="center">'.$cod.'</td>';
$Page  .=   '<td style="padding:10px;" align="center">'.$barcode.'</td>';
$Page  .= '</tr>';

$Page .= '</table>';

$Page .= '<hr style="border: 1px dashed #ccc;" />';

$Page .= '<div class="row">';
$Page	.=  '<table style="width:100%; border:0px;">';
$Page .= 	  '<tr>';
$Page .=			'<td width="35%" style="vertical-align:text-top; padding:15px;">'.$leftCol.'</td>';
$Page .= 		  '<td width="65%" style="vertical-align:text-top; padding:15px;">'.$rightCol.'</td>';
$Page	.=		'</tr>';
$Page	.=   '</table>';
$Page .= '</div>';

$Page .= $this->printer->content_end();
$Page .= $this->printer->page_end();
$Page .= $this->printer->doc_footer();

echo $Page;

?>
