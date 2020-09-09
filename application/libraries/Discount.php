<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Discount
{
  protected $ci;

	public function __construct()
	{
    // Assign the CodeIgniter super-object
    $this->ci =& get_instance();
	}


	public function getItemDiscount($code, $customer_code, $qty, $payment_code, $channels_code, $date = "")
	{
    $ci->load->model('masters/products');
    $ci->load->model('masters/customers');
		$date = $date == "" ? date('Y-m-d') : $date;
		$pd = new product($code);
		$cs = new customer($customer_code);
		$order = new order();
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

		if( $pd->id != "" && $cs->id != "" )
		{
			$qr  = "SELECT DISTINCT
							r.id, r.item_price,
							r.item_disc, r.item_disc_unit,
							r.item_disc_2, r.item_disc_2_unit,
							r.item_disc_3, r.item_disc_3_unit,
							r.qty, r.amount, r.canGroup ";

			$qr .= "FROM tbl_discount_policy AS p ";

			//----- รายการกฏต่างๆ
			$qr .= "LEFT JOIN tbl_discount_rule AS r ON p.id = r.id_discount_policy ";

			//---- get list of product if specific SKU
			$qr .= "LEFT JOIN tbl_discount_rule_items AS i ON r.id = i.id_rule ";

			//--- เลือกรายการตาม รุ่นสินค้า
			$qr .= "LEFT JOIN tbl_discount_rule_product_style AS ps ON r.id = ps.id_rule ";

			//--- รายการตามกลุ่มสินค้า
			$qr .= "LEFT JOIN tbl_discount_rule_product_group AS pg ON r.id = pg.id_rule ";

			//--- รายการตามกลุ่มย่อยสินค้า
			$qr .= "LEFT JOIN tbl_discount_rule_product_sub_group AS psg ON r.id = psg.id_rule ";

			//---
			$qr .= "LEFT JOIN tbl_discount_rule_product_type AS pt ON r.id = pt.id_rule ";

			//---
			$qr .= "LEFT JOIN tbl_discount_rule_product_kind AS pk ON r.id = pk.id_rule ";

			//---
			$qr .= "LEFT JOIN tbl_discount_rule_product_category AS pc ON r.id = pc.id_rule ";

			//---
			$qr .= "LEFT JOIN tbl_discount_rule_product_brand AS pb ON r.id = pb.id_rule ";

			//---
			$qr .= "LEFT JOIN tbl_discount_rule_product_year AS py ON r.id = py.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customers AS c ON r.id = c.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customer_group AS cg ON r.id = cg.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customer_area AS ca ON r.id = ca.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customer_kind AS ck ON r.id = ck.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customer_type AS ct ON r.id = ct.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customer_class AS cc ON r.id = cc.id_rule ";

			//--- get list of sales channels if specific channels
			$qr .= "LEFT JOIN tbl_discount_rule_channels AS n ON r.id = n.id_rule ";

			//---- get list of payment thod if specific payment method
			$qr .= "LEFT JOIN tbl_discount_rule_payment AS m ON r.id = m.id_rule ";

			//----- Check preriod and approval first
			$qr .= "WHERE p.date_start <= '".$date."' AND p.date_end >= '".$date."' AND p.isApproved = 1 AND p.active = 1 AND p.isDeleted = 0 ";

			//----- now check product
			$qr .= "AND (all_product = 1 OR all_product = 0) ";
			$qr .= "AND (id_product IS NULL OR id_product = '".$pd->id."') ";
			$qr .= "AND (id_product_style IS NULL OR id_product_style = '".$pd->id_style."') ";
			$qr .= "AND (id_product_group IS NULL OR id_product_group = '".$pd->id_group."') ";
			$qr .= "AND (id_product_sub_group IS NULL OR id_product_sub_group = '".$pd->id_sub_group."') ";
			$qr .= "AND (id_product_type IS NULL OR id_product_type = '".$pd->id_type."') ";
			$qr .= "AND (id_product_kind IS NULL OR id_product_kind = '".$pd->id_kind."') ";
			$qr .= "AND (id_product_category IS NULL OR id_product_category = '".$pd->id_category."') ";
			$qr .= "AND (id_product_brand IS NULL OR id_product_brand = '".$pd->id_brand."') ";
			$qr .= "AND (year IS NULL OR year = '".$pd->year."') ";

			//---- now check customer
			$qr .= "AND (all_customer = 1 OR all_customer = 0) ";
			$qr .= "AND (id_customer IS NULL OR id_customer = '".$cs->id."') ";
			$qr .= "AND (id_customer_group IS NULL OR id_customer_group = '".$cs->id_group."') ";
			$qr .= "AND (id_customer_area IS NULL OR id_customer_area = '".$cs->id_area."') ";
			$qr .= "AND (id_customer_kind IS NULL OR id_customer_kind = '".$cs->id_kind."') ";
			$qr .= "AND (id_customer_type IS NULL OR id_customer_type = '".$cs->id_type."') ";
			$qr .= "AND (id_customer_class IS NULL OR id_customer_class = '".$cs->id_class."') ";

			//---- now check  payment method
			$qr .= "AND ( all_payment = 1 OR all_payment = 0) ";
			$qr .= "AND ( id_payment IS NULL OR id_payment = '".$payment_code."') ";

			//---- now check sales channels
			$qr .= "AND ( all_channels = 1 OR all_channels = 0) ";
			$qr .= "AND (id_channels IS NULL OR id_channels = '".$channels_code."') ";

			//--- now check qty options
			$qr .= "AND (qty = 0 OR  qty <= ".$qty.") ";

			//--- now check amount options
			$qr .= "AND ( amount = 0 OR amount <= ".( $qty * $price ).") ";

			$qr .= "AND r.active = 1 AND r.isDeleted = 0 ";

			//echo $qr;


			$qs = dbQuery($qr);

			if( dbNumRows($qs) > 0 )
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

				$dis_rule = 0;


				//---- วนรอบจนหมดเงื่อนไข
				//--- หากเงื่อนไขถัดไปได้ส่วนลดรวมมากกว่าเงื่อนไขก่อนหน้า ตัวแปรด้านบนจะถูกแทนค่าใหม่ ถ้าไม่ดีกว่าจะได้ค่าเดิม
				while( $rs = dbFetchObject($qs) )
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
						$qty = $order->getSumOrderStyleQty($order_code, $pd->id_style);
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
	public function getItemRecalDiscount($order_code, $code, $price, $customer_code, $qty, $payment_code, $channels_code, $date = "")
	{
		$date = $date == "" ? date('Y-m-d') : $date;
		$pd = new product($code);
		$cs = new customer($customer_code);
		$order = new order();

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

		if( $pd->id != "" && $cs->id != "" )
		{
			//$qr  = "SELECT DISTINCT r.id, r.item_price, r.item_disc, r.item_disc_unit, r.qty, r.amount, r.canGroup ";
			$qr  = "SELECT DISTINCT
							r.id, r.item_price,
							r.item_disc, r.item_disc_unit,
							r.item_disc_2, r.item_disc_2_unit,
							r.item_disc_3, r.item_disc_3_unit,
							r.qty, r.amount, r.canGroup ";

			$qr .= "FROM tbl_discount_policy AS p ";

			//----- รายการกฏต่างๆ
			$qr .= "LEFT JOIN tbl_discount_rule AS r ON p.id = r.id_discount_policy ";

			//---- get list of product if specific SKU
			$qr .= "LEFT JOIN tbl_discount_rule_items AS i ON r.id = i.id_rule ";

			//--- เลือกรายการตาม รุ่นสินค้า
			$qr .= "LEFT JOIN tbl_discount_rule_product_style AS ps ON r.id = ps.id_rule ";

			//--- รายการตามกลุ่มสินค้า
			$qr .= "LEFT JOIN tbl_discount_rule_product_group AS pg ON r.id = pg.id_rule ";

			//--- รายการตามกลุ่มย่อยสินค้า
			$qr .= "LEFT JOIN tbl_discount_rule_product_sub_group AS psg ON r.id = psg.id_rule ";

			//---
			$qr .= "LEFT JOIN tbl_discount_rule_product_type AS pt ON r.id = pt.id_rule ";

			//---
			$qr .= "LEFT JOIN tbl_discount_rule_product_kind AS pk ON r.id = pk.id_rule ";

			//---
			$qr .= "LEFT JOIN tbl_discount_rule_product_category AS pc ON r.id = pc.id_rule ";

			//---
			$qr .= "LEFT JOIN tbl_discount_rule_product_brand AS pb ON r.id = pb.id_rule ";

			//---
			$qr .= "LEFT JOIN tbl_discount_rule_product_year AS py ON r.id = py.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customers AS c ON r.id = c.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customer_group AS cg ON r.id = cg.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customer_area AS ca ON r.id = ca.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customer_kind AS ck ON r.id = ck.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customer_type AS ct ON r.id = ct.id_rule ";

			//---- get list of customer if specific personaly customer
			$qr .= "LEFT JOIN tbl_discount_rule_customer_class AS cc ON r.id = cc.id_rule ";

			//--- get list of sales channels if specific channels
			$qr .= "LEFT JOIN tbl_discount_rule_channels AS n ON r.id = n.id_rule ";

			//---- get list of payment thod if specific payment method
			$qr .= "LEFT JOIN tbl_discount_rule_payment AS m ON r.id = m.id_rule ";

			//----- Check preriod and approval first
			$qr .= "WHERE p.date_start <= '".$date."' AND p.date_end >= '".$date."' AND p.isApproved = 1 AND p.active = 1 AND p.isDeleted = 0 ";

			//----- now check product
			$qr .= "AND (all_product = 1 OR all_product = 0) ";
			$qr .= "AND (id_product IS NULL OR id_product = '".$pd->id."') ";
			$qr .= "AND (id_product_style IS NULL OR id_product_style = '".$pd->id_style."') ";
			$qr .= "AND (id_product_group IS NULL OR id_product_group = '".$pd->id_group."') ";
			$qr .= "AND (id_product_sub_group IS NULL OR id_product_sub_group = '".$pd->id_sub_group."') ";
			$qr .= "AND (id_product_type IS NULL OR id_product_type = '".$pd->id_type."') ";
			$qr .= "AND (id_product_kind IS NULL OR id_product_kind = '".$pd->id_kind."') ";
			$qr .= "AND (id_product_category IS NULL OR id_product_category = '".$pd->id_category."') ";
			$qr .= "AND (id_product_brand IS NULL OR id_product_brand = '".$pd->id_brand."') ";
			$qr .= "AND (year IS NULL OR year = '".$pd->year."') ";

			//---- now check customer
			$qr .= "AND (all_customer = 1 OR all_customer = 0) ";
			$qr .= "AND (id_customer IS NULL OR id_customer = '".$cs->id."') ";
			$qr .= "AND (id_customer_group IS NULL OR id_customer_group = '".$cs->id_group."') ";
			$qr .= "AND (id_customer_area IS NULL OR id_customer_area = '".$cs->id_area."') ";
			$qr .= "AND (id_customer_kind IS NULL OR id_customer_kind = '".$cs->id_kind."') ";
			$qr .= "AND (id_customer_type IS NULL OR id_customer_type = '".$cs->id_type."') ";
			$qr .= "AND (id_customer_class IS NULL OR id_customer_class = '".$cs->id_class."') ";

			//---- now check  payment method
			$qr .= "AND ( all_payment = 1 OR all_payment = 0) ";
			$qr .= "AND ( id_payment IS NULL OR id_payment = '".$payment_code."') ";

			//---- now check sales channels
			$qr .= "AND ( all_channels = 1 OR all_channels = 0) ";
			$qr .= "AND (id_channels IS NULL OR id_channels = '".$channels_code."') ";

			//--- now check qty options
			$qr .= "AND (qty = 0 OR  qty <= ".$qty.") ";

			//--- now check amount options
			$qr .= "AND ( amount = 0 OR amount <= ".( $qty * $price ).") ";

			$qr .= "AND r.active = 1 AND r.isDeleted = 0";

			//echo $qr;
			$qs = dbQuery($qr);

			if( dbNumRows($qs) > 0 )
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

				$dis_rule = 0;

				while( $rs = dbFetchObject($qs) )
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
						$qty = $order->getSumOrderStyleQty($order_code, $pd->id_style);
						$amount = $qty * $price;
					}

					//---- ถ้ามีการกำหนดราคาขาย
					if( $rs->item_price > 0 )
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

}//-- end class

?>
