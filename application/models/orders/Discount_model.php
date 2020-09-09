<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Discount_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function get_item_discount($item_code, $customer_code, $qty, $payment_code, $channels_code, $date = '')
	{
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/orders_model');

		$date = $date == "" ? date('Y-m-d') : $date;
		$pd   = $this->products_model->get($item_code);
		$cs   = $this->customers_model->get($customer_code);
		$price = $pd->price;

		//--- default value if dont have any discount
		$sc = array(
			'discount1' => 0, //--- ส่วนลดเป็นจำนวนเงิน (ยอดต่อหน่วย)
			'unit1' => 'percent', //--- หน่วยของส่วนลด ('percent', 'amount')
			'discLabel1' => 0, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
			'discount2' => 0,
			'unit2' => 'percent',
			'discLabel2' => 0,
			'discount3' => 0,
			'unit3' => 'percent',
			'discLabel3' => 0,
			'amount' => 0, //--- เอายอดส่วนลดที่ได้ มา คูณ ด้วย จำนวนสั่ง เป้นส่วนลดทั้งหมด
			'id_rule' => NULL
		); //-- end array

		if( $pd->code != "" && $cs->code != "" )
		{
			$qr  = "SELECT DISTINCT
							r.id, r.item_price,
							r.item_disc, r.item_disc_unit,
							r.item_disc_2, r.item_disc_2_unit,
							r.item_disc_3, r.item_disc_3_unit,
							r.qty, r.amount, r.canGroup ";

			$qr .= "FROM discount_policy AS p ";

			//----- รายการกฏต่างๆ
			$qr .= "LEFT JOIN discount_rule AS r ON p.id = r.id_policy ";

      //---
      $qr .= "LEFT JOIN discount_rule_product AS pd ON r.id = pd.id_rule ";

			//--- เลือกรายการตาม รุ่นสินค้า
			$qr .= "LEFT JOIN discount_rule_product_style AS ps ON r.id = ps.id_rule ";

			//--- รายการตามกลุ่มสินค้า
			$qr .= "LEFT JOIN discount_rule_product_group AS pg ON r.id = pg.id_rule ";

			//--- รายการตามกลุ่มย่อยสินค้า
			$qr .= "LEFT JOIN discount_rule_product_sub_group AS psg ON r.id = psg.id_rule ";

			//---
			$qr .= "LEFT JOIN discount_rule_product_type AS pt ON r.id = pt.id_rule ";

			//---
			$qr .= "LEFT JOIN discount_rule_product_kind AS pk ON r.id = pk.id_rule ";

			//---
			$qr .= "LEFT JOIN discount_rule_product_category AS pc ON r.id = pc.id_rule ";

			//---
			$qr .= "LEFT JOIN discount_rule_product_brand AS pb ON r.id = pb.id_rule ";

			//---
			$qr .= "LEFT JOIN discount_rule_product_year AS py ON r.id = py.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer AS c ON r.id = c.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer_group AS cg ON r.id = cg.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer_area AS ca ON r.id = ca.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer_kind AS ck ON r.id = ck.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer_type AS ct ON r.id = ct.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer_class AS cc ON r.id = cc.id_rule ";

			//--- get list of sales channels if specific channels
			$qr .= "LEFT JOIN discount_rule_channels AS n ON r.id = n.id_rule ";

			//---- get list of payment thod if specific payment method
			$qr .= "LEFT JOIN discount_rule_payment AS m ON r.id = m.id_rule ";

			//----- Check preriod and approval first
			$qr .= "WHERE p.start_date <= '".$date."' AND p.end_date >= '".$date."' AND p.active = 1 ";

			//----- now check product
			$qr .= "AND (r.all_product = 1 OR r.all_product = 0) ";
      $qr .= "AND (pd.product_code IS NULL OR pd.product_code = '".$pd->code."') ";
			$qr .= "AND (ps.style_code IS NULL OR ps.style_code = '".$pd->style_code."') ";
			$qr .= "AND (pg.group_code IS NULL OR pg.group_code = '".$pd->group_code."') ";
			$qr .= "AND (psg.sub_group_code IS NULL OR psg.sub_group_code = '".$pd->sub_group_code."') ";
			$qr .= "AND (pt.type_code IS NULL OR pt.type_code = '".$pd->type_code."') ";
			$qr .= "AND (pk.kind_code IS NULL OR pk.kind_code = '".$pd->kind_code."') ";
			$qr .= "AND (pc.category_code IS NULL OR pc.category_code = '".$pd->category_code."') ";
			$qr .= "AND (pb.brand_code IS NULL OR pb.brand_code = '".$pd->brand_code."') ";
			$qr .= "AND (py.year IS NULL OR py.year = '".$pd->year."') ";

			//---- now check customer
			$qr .= "AND (r.all_customer = 1 OR r.all_customer = 0) ";
			$qr .= "AND (c.customer_code IS NULL OR c.customer_code = '".$cs->code."') ";
			$qr .= "AND (cg.group_code IS NULL OR cg.group_code = '".$cs->group_code."') ";
			$qr .= "AND (ca.area_code IS NULL OR ca.area_code = '".$cs->area_code."') ";
			$qr .= "AND (ck.kind_code IS NULL OR ck.kind_code = '".$cs->kind_code."') ";
			$qr .= "AND (ct.type_code IS NULL OR ct.type_code = '".$cs->type_code."') ";
			$qr .= "AND (cc.class_code IS NULL OR cc.class_code = '".$cs->class_code."') ";

			//---- now check  payment method
			$qr .= "AND (r.all_payment = 1 OR r.all_payment = 0) ";
			$qr .= "AND (m.payment_code IS NULL OR m.payment_code = '".$payment_code."') ";

			//---- now check sales channels
			$qr .= "AND (r.all_channels = 1 OR r.all_channels = 0) ";
			$qr .= "AND (n.channels_code IS NULL OR n.channels_code = '".$channels_code."') ";

			//--- now check qty options
			$qr .= "AND (r.qty = 0 OR  r.qty <= {$qty}) ";

			//--- now check amount options
			$qr .= "AND (r.amount = 0 OR r.amount <= ".($qty * $price).") ";

			$qr .= "AND r.active = 1 AND r.isDeleted = 0 ";

			//echo $qr;


			$qs = $this->db->query($qr);

			if( $qs->num_rows() > 0 )
			{
				$discAmount = 0;
				$discLabel = 0;
				$disc_unit = "percent";

				$discAmount2 = 0;
				$discLabel2 = 0;
				$disc_unit2 = "percent";

				$discAmount3 = 0;
				$discLabel3 = 0;
				$disc_unit3 = "percent";

				$totalDiscAmount = 0; //--- ผลรวมของส่วนลดทั้ง 3 สเต็ป

				$dis_rule = NULL;


				//---- วนรอบจนหมดเงื่อนไข
				//--- หากเงื่อนไขถัดไปได้ส่วนลดรวมมากกว่าเงื่อนไขก่อนหน้า ตัวแปรด้านบนจะถูกแทนค่าใหม่ ถ้าไม่ดีกว่าจะได้ค่าเดิม
				foreach($qs->result() as $rs)
				{
					$discount = 0;
					$discount2 = 0;
					$discount3 = 0;
					$amount = $qty * $price;
					$isSetMin = ($rs->qty > 0 OR $rs->amount > 0) ? TRUE : FALSE; //--- มีการกำหนดขั้นต่ำหรือไม่
					$canGroup = $rs->canGroup == 1 ? TRUE : FALSE; //--- รวมยอดได้หรือไม่

					//----- หากมีการกำหนดยอดขั้นต่ำ และ สามารถรวมยอดได้
					if($isSetMin && $canGroup)
					{
						//---- คำนวณยอดสั่งใหม่
						$qty = $this->orders_model->get_sum_style_qty($order_code, $pd->code_style);
						$amount = $qty * $price;
					}

					//---- ถ้ามีการกำหนดราคาขาย
					if( $rs->item_price > 0 )
					{
						//--- step 1
						//--- ถ้ามีการกำหนดราคาขาย จะไม่สนใจส่วนลด ส่วนต่างราคาขาย จะถูกแปลงเป็นส่วนลดแทน
						$discount =	$price - $rs->item_price;
						$rs->item_disc = $discount;
						$disc_unit1 = 'amount';
					} //--- end if

					if($rs->item_disc > 0 && $rs->item_price == 0)
					{
						//--- ส่วนลดเสต็ปแรก (เป็นจำนวนเงิน)
						$discount = $rs->item_disc_unit == 'percent' ? $price * ( $rs->item_disc * 0.01 ) :  $rs->item_disc;
						$disc_unit1 = $rs->item_disc_unit;
					}	//-- end if

					if($rs->item_disc_2 > 0)
					{
						//--- ส่วนลดเสต็ปที่ 2 (เอาราคา ลบด้วยส่วนลดเสต็ปแรกก่อน แล้วจึงคำนวนเสต็ปที่ 2)
						$discount2 = $rs->item_disc_2_unit == 'percent' ? ($price - $discount) * ($rs->item_disc_2 * 0.01) : $rs->item_disc_2;
					} //---- end if

					if($rs->item_disc_3 > 0)
					{
						//--- ส่วนลดเสต็ปที่ 3 (เอาราคา ลบด้วยส่วนลด 1 และ 2 ก่อนแล้วจึงคำนวนเสต็ปที่ 3)
						$discount3 = $rs->item_disc_3_unit == 'percent' ? ($price - ($discount + $discount2)) * ($rs->item_disc_3 * 0.01) : $rs->item_disc_3;
					}//--- end if

					//--- ส่วนลดรวมทั้ง 3 เสต็ป
					$sumDiscount  = $discount + $discount2 + $discount3;

					$disc_unit 	= ( $sumDiscount > $totalDiscAmount ) ? $disc_unit1 : $disc_unit;
					$discLabel 	= ( $sumDiscount > $totalDiscAmount ) ? $rs->item_disc : $discLabel;
					$discAmount = ( $sumDiscount > $totalDiscAmount ) ? $discount : $discAmount;

					$disc_unit2		= ( $sumDiscount > $totalDiscAmount ) ? $rs->item_disc_2_unit : $disc_unit2;
					$discLabel2		= ( $sumDiscount > $totalDiscAmount ) ? $rs->item_disc_2 : $discLabel2;
					$discAmount2 	= ( $sumDiscount > $totalDiscAmount ) ? $discount2 : $discAmount2;

					$disc_unit3 	= ( $sumDiscount > $totalDiscAmount ) ? $rs->item_disc_3_unit : $disc_unit;
					$discLabel3 	= ( $sumDiscount > $totalDiscAmount ) ? $rs->item_disc_3 : $discLabel3;
					$discAmount3 	= ( $sumDiscount > $totalDiscAmount ) ? $discount3 : $discAmount3;

					//--- ถ้าส่วนลดรวมดีกว่าก่อนหน้านี้ เปลี่ยนมาใช้เงื่อนไขนี้แทน
					$dis_rule = ( $sumDiscount > $totalDiscAmount ) ? $rs->id : $dis_rule;

					//---  ถ้าส่วนลดรวมของเงิ่อนไขนี้ ดีกว่าเงื่อนไขก่อนหน้านี้ ให้ใช้ค่าใหม่ ถ้าไม่ดีกว่าให้ใช้ค่าเดิม
					$totalDiscAmount = ($sumDiscount > $totalDiscAmount) ? $sumDiscount : $totalDiscAmount;

				}//--- end while

				//---- ได้ส่วนลดที่ดีที่สุดมาแล้ว
				$sc = array(
					'discount1' => $discAmount, //--- ส่วนลดเป็นจำนวนเงิน (ยอดต่อหน่วย)
					'unit1' => $disc_unit, //--- หน่วยของส่วนลด ('percent', 'amount')
					'discLabel1' => $disc_unit == 'percent' ? $discLabel.' %' : $discLabel, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
					'discount2' => $discAmount2,
					'unit2' => $disc_unit2,
					'discLabel2' => $disc_unit2 == 'percent' ? $discLabel2.' %' : $discLabel2, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
					'discount3' => $discAmount3,
					'unit3' => $disc_unit3,
					'discLabel3' => $disc_unit3 == 'percent' ? $discLabel3.' %' : $discLabel3, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
					'amount' => ($totalDiscAmount * $qty), //--- เอายอดส่วนลดที่ได้ มา คูณ ด้วย จำนวนสั่ง เป้นส่วนลดทั้งหมด
					'id_rule' => $dis_rule
				); //-- end array

			} //--- end if dbNumRows

		}
		return $sc;
	}



	//----- คำนวณส่วนลดใหม่ โดยจำนวนซื้อกับมูลค่าซื้อจะมีผลกับส่วนลด
	public function get_item_recal_discount($order_code, $item_code, $price, $customer_code, $qty, $payment_code, $channels_code, $date = '')
	{
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/orders_model');

		$date = $date == '' ? date('Y-m-d') : $date;
		$pd   = $this->products_model->get($item_code);
		$cs   = $this->customers_model->get($customer_code);

		//--- default value if dont have any discount
		$sc = array(
			'discount1' => 0, //--- ส่วนลดเป็นจำนวนเงิน (ยอดต่อหน่วย)
			'unit1' => 'percent', //--- หน่วยของส่วนลด ('percent', 'amount')
			'discLabel1' => 0, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
			'discount2' => 0,
			'unit2' => 'percent',
			'discLabel2' => 0,
			'discount3' => 0,
			'unit3' => 'percent',
			'discLabel3' => 0,
			'amount' => 0, //--- เอายอดส่วนลดที่ได้ มา คูณ ด้วย จำนวนสั่ง เป้นส่วนลดทั้งหมด
			'id_rule' => NULL
		); //-- end array

    if( $pd->code != "" && $cs->code != "" )
		{
			$qr  = "SELECT DISTINCT
							r.id, r.item_price,
							r.item_disc, r.item_disc_unit,
							r.item_disc_2, r.item_disc_2_unit,
							r.item_disc_3, r.item_disc_3_unit,
							r.qty, r.amount, r.canGroup ";

			$qr .= "FROM discount_policy AS p ";

			//----- รายการกฏต่างๆ
			$qr .= "LEFT JOIN discount_rule AS r ON p.id = r.id_policy ";

      //---
      $qr .= "LEFT JOIN discount_rule_product AS pd ON r.id = pd.id_rule ";

			//--- เลือกรายการตาม รุ่นสินค้า
			$qr .= "LEFT JOIN discount_rule_product_style AS ps ON r.id = ps.id_rule ";

			//--- รายการตามกลุ่มสินค้า
			$qr .= "LEFT JOIN discount_rule_product_group AS pg ON r.id = pg.id_rule ";

			//--- รายการตามกลุ่มย่อยสินค้า
			$qr .= "LEFT JOIN discount_rule_product_sub_group AS psg ON r.id = psg.id_rule ";

			//---
			$qr .= "LEFT JOIN discount_rule_product_type AS pt ON r.id = pt.id_rule ";

			//---
			$qr .= "LEFT JOIN discount_rule_product_kind AS pk ON r.id = pk.id_rule ";

			//---
			$qr .= "LEFT JOIN discount_rule_product_category AS pc ON r.id = pc.id_rule ";

			//---
			$qr .= "LEFT JOIN discount_rule_product_brand AS pb ON r.id = pb.id_rule ";

			//---
			$qr .= "LEFT JOIN discount_rule_product_year AS py ON r.id = py.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer AS c ON r.id = c.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer_group AS cg ON r.id = cg.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer_area AS ca ON r.id = ca.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer_kind AS ck ON r.id = ck.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer_type AS ct ON r.id = ct.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN discount_rule_customer_class AS cc ON r.id = cc.id_rule ";

			//--- get list of sales channels if specific channels
			$qr .= "LEFT JOIN discount_rule_channels AS n ON r.id = n.id_rule ";

			//---- get list of payment thod if specific payment method
			$qr .= "LEFT JOIN discount_rule_payment AS m ON r.id = m.id_rule ";

			//----- Check preriod and approval first
			$qr .= "WHERE p.start_date <= '".$date."' AND p.end_date >= '".$date."' AND p.active = 1 ";

			//----- now check product
			$qr .= "AND (r.all_product = 1 OR r.all_product = 0) ";
      $qr .= "AND (pd.product_code IS NULL OR pd.product_code = '".$pd->code."') ";
			$qr .= "AND (ps.style_code IS NULL OR ps.style_code = '".$pd->style_code."') ";
			$qr .= "AND (pg.group_code IS NULL OR pg.group_code = '".$pd->group_code."') ";
			$qr .= "AND (psg.sub_group_code IS NULL OR psg.sub_group_code = '".$pd->sub_group_code."') ";
			$qr .= "AND (pt.type_code IS NULL OR pt.type_code = '".$pd->type_code."') ";
			$qr .= "AND (pk.kind_code IS NULL OR pk.kind_code = '".$pd->kind_code."') ";
			$qr .= "AND (pc.category_code IS NULL OR pc.category_code = '".$pd->category_code."') ";
			$qr .= "AND (pb.brand_code IS NULL OR pb.brand_code = '".$pd->brand_code."') ";
			$qr .= "AND (py.year IS NULL OR py.year = '".$pd->year."') ";

			//---- now check customer
			$qr .= "AND (r.all_customer = 1 OR r.all_customer = 0) ";
			$qr .= "AND (c.customer_code IS NULL OR c.customer_code = '".$cs->code."') ";
			$qr .= "AND (cg.group_code IS NULL OR cg.group_code = '".$cs->group_code."') ";
			$qr .= "AND (ca.area_code IS NULL OR ca.area_code = '".$cs->area_code."') ";
			$qr .= "AND (ck.kind_code IS NULL OR ck.kind_code = '".$cs->kind_code."') ";
			$qr .= "AND (ct.type_code IS NULL OR ct.type_code = '".$cs->type_code."') ";
			$qr .= "AND (cc.class_code IS NULL OR cc.class_code = '".$cs->class_code."') ";

			//---- now check  payment method
			$qr .= "AND (r.all_payment = 1 OR r.all_payment = 0) ";
			$qr .= "AND (m.payment_code IS NULL OR m.payment_code = '".$payment_code."') ";

			//---- now check sales channels
			$qr .= "AND (r.all_channels = 1 OR r.all_channels = 0) ";
			$qr .= "AND (n.channels_code IS NULL OR n.channels_code = '".$channels_code."') ";

			//--- now check qty options
			$qr .= "AND (r.qty = 0 OR  r.qty <= {$qty}) ";

			//--- now check amount options
			$qr .= "AND (r.amount = 0 OR r.amount <= ".($qty * $price).") ";

			$qr .= "AND r.active = 1 AND r.isDeleted = 0 ";

			//echo $qr;
			$qs = $this->db->query($qr);

			if($qs->num_rows() > 0)
			{
				$discAmount = 0;
				$discLabel = 0;
				$disc_unit = "percent";

				$discAmount2 = 0;
				$discLabel2 = 0;
				$disc_unit2 = "percent";

				$discAmount3 = 0;
				$discLabel3 = 0;
				$disc_unit3 = "percent";

				$totalDiscAmount = 0; //--- ผลรวมของส่วนลดทั้ง 3 สเต็ป

				$dis_rule = NULL;

				foreach($qs->result() as $rs)
				{
					$discount = 0;
					$discount2 = 0;
					$discount3 = 0;
					$amount = $qty * $price;
					$isSetMin = ( $rs->qty > 0 OR $rs->amount > 0 ) ? TRUE : FALSE; //--- มีการกำหนดขั้นต่ำหรือไม่
					$canGroup = $rs->canGroup == 1 ? TRUE : FALSE; //--- รวมยอดได้หรือไม่

					//----- หากมีการกำหนดยอดขั้นต่ำ และ สามารถรวมยอดได้
					if($isSetMin && $canGroup)
					{
						//---- คำนวณยอดสั่งใหม่
						$qty = $order->get_sum_style_qty($order_code, $pd->style_code);
						$amount = $qty * $price;
					}

					//---- ถ้ามีการกำหนดราคาขาย
					if($rs->item_price > 0)
					{
						//--- step 1
						//--- ถ้ามีการกำหนดราคาขาย จะไม่สนใจส่วนลด ส่วนต่างราคาขาย จะถูกแปลงเป็นส่วนลดแทน
						$discount =	$price - $rs->item_price;
						$rs->item_disc = $discount; //--- เปลี่ยน
						$disc_unit1 = 'amount';
					} //--- end if

					if($rs->item_disc > 0 && $rs->item_price == 0)
					{
						//--- ส่วนลดเสต็ปแรก (เป็นจำนวนเงิน)
						$discount = $rs->item_disc_unit == 'percent' ? $price * ( $rs->item_disc * 0.01 ) :  $rs->item_disc;
						$disc_unit1 = $rs->item_disc_unit;
					}	//-- end if

					if($rs->item_disc_2 > 0)
					{
						//--- ส่วนลดเสต็ปที่ 2 (เอาราคา ลบด้วยส่วนลดเสต็ปแรกก่อน แล้วจึงคำนวนเสต็ปที่ 2)
						$discount2 = $rs->item_disc_2_unit == 'percent' ? ($price - $discount) * ($rs->item_disc_2 * 0.01) : $rs->item_disc_2;
					} //---- end if

					if($rs->item_disc_3 > 0)
					{
						//--- ส่วนลดเสต็ปที่ 3 (เอาราคา ลบด้วยส่วนลด 1 และ 2 ก่อนแล้วจึงคำนวนเสต็ปที่ 3)
						$discount3 = $rs->item_disc_3_unit == 'percent' ? ($price - ($discount + $discount2)) * ($rs->item_disc_3 * 0.01) : $rs->item_disc_3;
					}//--- end if

					//--- ส่วนลดรวมทั้ง 3 เสต็ป
					$sumDiscount  = $discount + $discount2 + $discount3;

					$disc_unit 	= ( $sumDiscount > $totalDiscAmount ) ? $disc_unit1 : $disc_unit;
					$discLabel 	= ( $sumDiscount > $totalDiscAmount ) ? $rs->item_disc : $discLabel;
					$discAmount = ( $sumDiscount > $totalDiscAmount ) ? $discount : $discAmount;

					$disc_unit2		= ( $sumDiscount > $totalDiscAmount ) ? $rs->item_disc_2_unit : $disc_unit2;
					$discLabel2		= ( $sumDiscount > $totalDiscAmount ) ? $rs->item_disc_2 : $discLabel2;
					$discAmount2 	= ( $sumDiscount > $totalDiscAmount ) ? $discount2 : $discAmount2;

					$disc_unit3 	= ( $sumDiscount > $totalDiscAmount ) ? $rs->item_disc_3_unit : $disc_unit;
					$discLabel3 	= ( $sumDiscount > $totalDiscAmount ) ? $rs->item_disc_3 : $discLabel3;
					$discAmount3 	= ( $sumDiscount > $totalDiscAmount ) ? $discount3 : $discAmount3;

					//--- ถ้าส่วนลดรวมดีกว่าก่อนหน้านี้ เปลี่ยนมาใช้เงื่อนไขนี้แทน
					$dis_rule = ( $sumDiscount > $totalDiscAmount ) ? $rs->id : $dis_rule;

					//---  ถ้าส่วนลดรวมของเงิ่อนไขนี้ ดีกว่าเงื่อนไขก่อนหน้านี้ ให้ใช้ค่าใหม่ ถ้าไม่ดีกว่าให้ใช้ค่าเดิม
					$totalDiscAmount = ($sumDiscount > $totalDiscAmount) ? $sumDiscount : $totalDiscAmount;

				}//--- end while

				//---- ได้ส่วนลดที่ดีที่สุดมาแล้ว
				$sc = array(
					'discount1' => $discAmount, //--- ส่วนลดเป็นจำนวนเงิน (ยอดต่อหน่วย)
					'unit1' => $disc_unit, //--- หน่วยของส่วนลด ('percent', 'amount')
					'discLabel1' => $disc_unit == 'percent' ? $discLabel.' %' : $discLabel, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
					'discount2' => $discAmount2,
					'unit2' => $disc_unit2,
					'discLabel2' => $disc_unit2 == 'percent' ? $discLabel2.' %' : $discLabel2, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
					'discount3' => $discAmount3,
					'unit3' => $disc_unit3,
					'discLabel3' => $disc_unit3 == 'percent' ? $discLabel3.' %' : $discLabel3, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
					'amount' => ($totalDiscAmount * $qty), //--- เอายอดส่วนลดที่ได้ มา คูณ ด้วย จำนวนสั่ง เป้นส่วนลดทั้งหมด
					'id_rule' => $dis_rule
				); //-- end array

			} //--- end if dbNumRows

		}
		return $sc;
	}



} //--- end class
?>
