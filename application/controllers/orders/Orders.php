<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends PS_Controller
{
  public $menu_code = 'SOODSO';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ออเดอร์';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/orders';
    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/discount_model');

    $this->load->helper('order');
    $this->load->helper('channels');
    $this->load->helper('payment_method');
    $this->load->helper('sender');
    $this->load->helper('customer');
    $this->load->helper('users');
    $this->load->helper('state');
    $this->load->helper('product_images');
    $this->load->helper('discount');

    $this->filter = getConfig('STOCK_FILTER');
  }


  public function index()
  {
    $filter = array(
      'code'          => get_filter('code', 'code', ''),
      'customer'      => get_filter('customer', 'customer', ''),
      'user'          => get_filter('user', 'user', ''),
      'reference'     => get_filter('reference', 'reference', ''),
      'ship_code'     => get_filter('shipCode', 'shipCode', ''),
      'channels'      => get_filter('channels', 'channels', ''),
      'payment'       => get_filter('payment', 'payment', ''),
      'from_date'     => get_filter('fromDate', 'fromDate', ''),
      'to_date'       => get_filter('toDate', 'toDate', ''),
      'is_paid'       => get_filter('is_paid', 'is_paid', 'all'),
      'order_by'      => get_filter('order_by', 'order_by', 'code'),
      'sort_by'       => get_filter('sort_by', 'sort_by', 'DESC')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->orders_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->orders_model->get_data($filter, $perpage, $this->uri->segment($segment));
    $ds       = array();
    if(!empty($orders))
    {
      foreach($orders as $rs)
      {
        $rs->channels_name = $this->channels_model->get_name($rs->channels_code);
        $rs->payment_name  = $this->payment_methods_model->get_name($rs->payment_code);
        $rs->payment_role  = $this->payment_methods_model->get_role($rs->payment_code);
        $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
        $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code) + $rs->shipping_fee + $rs->service_fee;
        $rs->state_name    = get_state_name($rs->state);
        $ds[] = $rs;
      }
    }

    $filter['orders'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('orders/orders_list', $filter);
  }



  public function add_new()
  {
    $this->load->view('orders/orders_add');
  }



  public function add()
  {
    if($this->input->post('customerCode'))
    {
      $this->load->model('inventory/invoice_model');

      $book_code = getConfig('BOOK_CODE_ORDER');
      $date_add = db_date($this->input->post('date'));
      $code = $this->get_new_code($date_add);
      $role = 'S'; //--- S = ขาย
      $has_term = $this->payment_methods_model->has_term($this->input->post('payment'));
      $sale_code = $this->customers_model->get_sale_code($this->input->post('customerCode'));
      $sender_id = get_null($this->input->post('sender_id'));

      //--- check over due
      $is_strict = getConfig('STRICT_OVER_DUE') == 1 ? TRUE : FALSE;
      $overDue = $is_strict ? $this->invoice_model->is_over_due($this->input->post('customerCode')) : FALSE;

      //--- ถ้ามียอดค้างชำระ และ เป็นออเดอร์แบบเครดิต
      //--- ไม่ให้เพิ่มออเดอร์
      if($overDue && $has_term)
      {
        set_error('มียอดค้างชำระเกินกำหนดไม่อนุญาติให้ขาย');
        redirect($this->home.'/add_new');
      }
      else
      {
        $ds = array(
          'code' => $code,
          'role' => $role,
          'bookcode' => $book_code,
          'reference' => $this->input->post('reference'),
          'customer_code' => $this->input->post('customerCode'),
          'customer_ref' => $this->input->post('cust_ref'),
          'channels_code' => $this->input->post('channels'),
          'payment_code' => $this->input->post('payment'),
          'sale_code' => $sale_code,
          'is_term' => ($has_term === TRUE ? 1 : 0),
          'user' => get_cookie('uname'),
          'sender_id' => $sender_id,
          'remark' => addslashes($this->input->post('remark'))
        );

        if($this->orders_model->add($ds) === TRUE)
        {
          $arr = array(
            'order_code' => $code,
            'state' => 1,
            'update_user' => get_cookie('uname')
          );

          $this->order_state_model->add_state($arr);

          redirect($this->home.'/edit_detail/'.$code);
        }
        else
        {
          set_error('เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
          redirect($this->home.'/add_new');
        }
      }
    }
    else
    {
      set_error('ไม่พบข้อมูลลูกค้า กรุณาตรวจสอบ');
      redirect($this->home.'/add_new');
    }
  }




  public function add_detail($order_code)
  {
    $result = TRUE;
    $err = "";
    $auz = get_auz(); //--- Allow under zero stock : return TRUE or FALSE;
    $err_qty = 0;
    $data = $this->input->post('data');
    $order = $this->orders_model->get($order_code);
    if(!empty($data))
    {
      foreach($data as $rs)
      {
        $code = $rs['code']; //-- รหัสสินค้า
        $qty = $rs['qty'];
        $item = $this->products_model->get($code);

        if( $qty > 0 )
        {
          //$qty = ceil($qty);

          //---- ยอดสินค้าที่่สั่งได้
          $sumStock = $this->get_sell_stock($code);


          //--- ถ้ามีสต็อกมากว่าที่สั่ง หรือ เป็นสินค้าไม่นับสต็อก หรือ ติดลบได้
          if( $sumStock >= $qty OR $item->count_stock == 0 OR $auz)
          {

            //---- ถ้ายังไม่มีรายการในออเดอร์
            if( $this->orders_model->is_exists_detail($order_code, $code) === FALSE )
            {
              //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
              $discount = array(
                'amount' => 0,
                'id_rule' => NULL,
                'discLabel1' => 0,
                'discLabel2' => 0,
                'discLabel3' => 0
              );

              if($order->role == 'S')
              {
                $discount = $this->discount_model->get_item_discount($item->code, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add);
              }

              if($order->role == 'C' OR $order->role == 'N')
              {
                $gp = $order->gp;
                //------ คำนวณส่วนลดใหม่
      					$step = explode('+', $gp);
      					$discAmount = 0;
      					$discLabel = array(0, 0, 0);
      					$price = $item->price;
      					$i = 0;
      					foreach($step as $discText)
      					{
      						if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
      						{
      							$disc = explode('%', $discText);
      							$disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
      							$amount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
      							$discLabel[$i] = count($disc) == 1 ? $disc[0] : $disc[0].'%';
      							$discAmount += $amount;
      							$price -= $amount;
      						}

      						$i++;
      					}

                $total_discount = $qty * $discAmount; //---- ส่วนลดรวม
      					//$total_amount = ( $qty * $price ) - $total_discount; //--- ยอดรวมสุดท้าย
                $discount['amount'] = $total_discount;
                $discount['discLabel1'] = $discLabel[0];
                $discount['discLabel2'] = $discLabel[1];
                $discount['discLabel3'] = $discLabel[2];
              }

              $arr = array(
                      "order_code"	=> $order_code,
                      "style_code"		=> $item->style_code,
                      "product_code"	=> $item->code,
                      "product_name"	=> addslashes($item->name),
                      "cost"  => $item->cost,
                      "price"	=> $item->price,
                      "qty"		=> $qty,
                      "discount1"	=> $discount['discLabel1'],
                      "discount2" => $discount['discLabel2'],
                      "discount3" => $discount['discLabel3'],
                      "discount_amount" => $discount['amount'],
                      "total_amount"	=> ($item->price * $qty) - $discount['amount'],
                      "id_rule"	=> $discount['id_rule'],
                      "is_count" => $item->count_stock
                    );

              if( $this->orders_model->add_detail($arr) === FALSE )
              {
                $result = FALSE;
                $error = "Error : Insert fail";
                $err_qty++;
              }
              else
              {
                if($item->count_stock == 1 && $item->is_api == 1)
                {
                  $this->update_api_stock($item->code);
                }
              }

            }
            else  //--- ถ้ามีรายการในออเดอร์อยู่แล้ว
            {
              $detail 	= $this->orders_model->get_order_detail($order_code, $item->code);
              $qty			= $qty + $detail->qty;

              $discount = array(
                'amount' => 0,
                'id_rule' => NULL,
                'discLabel1' => 0,
                'discLabel2' => 0,
                'discLabel3' => 0
              );

              //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
              if($order->role == 'S')
              {
                $discount 	= $this->discount_model->get_item_discount($item->code, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add);
              }


              $arr = array(
                        "qty"		=> $qty,
                        "discount1"	=> $discount['discLabel1'],
                        "discount2" => $discount['discLabel2'],
                        "discount3" => $discount['discLabel3'],
                        "discount_amount" => $discount['amount'],
                        "total_amount"	=> ($item->price * $qty) - $discount['amount'],
                        "id_rule"	=> $discount['id_rule'],
                        "valid" => 0
                        );

              if( $this->orders_model->update_detail($detail->id, $arr) === FALSE )
              {
                $result = FALSE;
                $error = "Error : Update Fail";
                $err_qty++;
              }
              else
              {
                if($item->count_stock == 1 && $item->is_api == 1)
                {
                  $this->update_api_stock($item->code);
                }
              }

            }	//--- end if isExistsDetail
          }
          else 	// if getStock
          {
            $result = FALSE;
            $error = "Error : สินค้าไม่เพียงพอ";
          } 	//--- if getStock
        }	//--- if qty > 0
      }

      if($result === TRUE)
      {
        $this->orders_model->set_status($order_code, 0);
      }
    }

    echo $result === TRUE ? 'success' : ( $err_qty > 0 ? $error.' : '.$err_qty.' item(s)' : $error);
  }




  public function remove_detail($id)
  {
    $detail = $this->orders_model->get_detail($id);
    $item = $this->products_model->get($detail->product_code);
    $rs = $this->orders_model->remove_detail($id);
    if($rs)
    {
      if($detail->is_count == 1 && $item->is_api == 1)
      {
        $this->update_api_stock($item->code);
      }

    }

    echo $rs === TRUE ? 'success' : 'Can not delete please try again';
  }



  public function edit_order($code)
  {
    $this->load->model('address/address_model');
    $this->load->model('masters/bank_model');
    $this->load->model('orders/order_payment_model');
    $this->load->helper('bank');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if(!empty($rs))
    {
      $rs->channels_name = $this->channels_model->get_name($rs->channels_code);
      $rs->payment_name  = $this->payment_methods_model->get_name($rs->payment_code);
      $rs->payment_role  = $this->payment_methods_model->get_role($rs->payment_code);
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
      $rs->user          = $this->user_model->get_name($rs->user);
      $rs->state_name    = get_state_name($rs->state);
      $rs->has_payment   = $this->order_payment_model->is_exists($code);
    }

    $state = $this->order_state_model->get_order_state($code);
    $ost = array();
    if(!empty($state))
    {
      foreach($state as $st)
      {
        $ost[] = $st;
      }
    }

    $details = $this->orders_model->get_order_details($code);
    $ship_to = $this->address_model->get_shipping_address($rs->customer_ref);
    $banks = $this->bank_model->get_active_bank();
    $ds['state'] = $ost;
    $ds['order'] = $rs;
    $ds['details'] = $details;
    $ds['addr']  = $ship_to;
    $ds['banks'] = $banks;
    $ds['payments'] = $this->order_payment_model->get_payments($code);
    $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
    $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
    $ds['edit_order'] = TRUE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
    $this->load->view('orders/order_edit', $ds);
  }



  public function update_order()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $this->load->model('inventory/invoice_model');

      $code = $this->input->post('order_code');
      $recal = $this->input->post('recal');
      $has_term = $this->payment_methods_model->has_term($this->input->post('payment_code'));
      $sale_code = $this->customers_model->get_sale_code($this->input->post('customer_code'));

      //--- check over due
      $is_strict = is_true(getConfig('STRICT_OVER_DUE'));
      $overDue = $is_strict ? $this->invoice_model->is_over_due($this->input->post('customerCode')) : FALSE;


      //--- ถ้ามียอดค้างชำระ และ เป็นออเดอร์แบบเครดิต
      //--- ไม่ให้เพิ่มออเดอร์
      if($overDue && $has_term)
      {
        $sc = FALSE;
        $message = 'มียอดค้างชำระเกินกำหนดไม่อนุญาติให้แก้ไขการชำระเงิน';
      }
      else
      {
        $ds = array(
          'reference' => $this->input->post('reference'),
          'customer_code' => $this->input->post('customer_code'),
          'customer_ref' => $this->input->post('customer_ref'),
          'channels_code' => $this->input->post('channels_code'),
          'payment_code' => $this->input->post('payment_code'),
          'sale_code' => $sale_code,
          'is_term' => $has_term,
          'sender_id' => get_null($this->input->post('sender_id')),
          'date_add' => db_date($this->input->post('date_add')),
          'remark' => $this->input->post('remark'),
          'status' => 0
        );

        $rs = $this->orders_model->update($code, $ds);

        if($rs === TRUE)
        {
          if($recal == 1)
          {
            $order = $this->orders_model->get($code);

            //---- Recal discount
            $details = $this->orders_model->get_order_details($code);
            if(!empty($details))
            {
              foreach($details as $detail)
              {
                $qty	= $detail->qty;

                //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
                $discount 	= $this->discount_model->get_item_recal_discount($detail->order_code, $detail->product_code, $detail->price, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add);

                $arr = array(
                  "qty"		=> $qty,
                  "discount1"	=> $discount['discLabel1'],
                  "discount2" => $discount['discLabel2'],
                  "discount3" => $discount['discLabel3'],
                  "discount_amount" => $discount['amount'],
                  "total_amount"	=> ($detail->price * $qty) - $discount['amount'],
                  "id_rule"	=> $discount['id_rule']
                );

                $this->orders_model->update_detail($detail->id, $arr);
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $message = 'ปรับปรุงรายการไม่สำเร็จ';
        }
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function edit_detail($code)
  {
    $this->load->helper('product_tab');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if($rs->state <= 3)
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $ds['order'] = $rs;

      $details = $this->orders_model->get_order_details($code);
      $ds['details'] = $details;
      $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = FALSE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('orders/order_edit_detail', $ds);
    }
    else
    {
      $ds['order'] = $rs;
      $this->load->view('orders/invalid_state', $ds);
    }

  }



  public function save($code)
  {
    $sc = TRUE;
    $order = $this->orders_model->get($code);
    //--- ถ้าออเดอร์เป็นแบบเครดิต
    if($order->is_term == 1)
    {
      //---- check credit balance
      $amount = $this->orders_model->get_order_total_amount($code) + $order->shipping_fee + $order->service_fee;

      //--- credit balance from sap
      $credit_balance = $this->customers_model->get_credit_balance($order->customer_code);

      $control = getConfig('CONTROL_CREDIT');

      if($control == 1)
      {
        if($amount > $credit_balance)
        {
          $diff = $amount - $credit_balance;
          $sc = FALSE;
          $message = 'เครดิตคงเหลือไม่พอ (ขาด : '.number($diff, 2).')';
        }
      }
    }

    if($sc === TRUE)
    {
      update_order_total_amount($code);
    }

    if($sc === TRUE)
    {
      $rs = $this->orders_model->set_status($code, 1);
      if($rs === FALSE)
      {
        $sc = FALSE;
        $message = 'บันทึกออเดอร์ไม่สำเร็จ';
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function get_product_order_tab()
  {
    $ds = "";
  	$id_tab = $this->input->post('id');
  	$qs     = $this->product_tab_model->getStyleInTab($id_tab);
  	if( $qs->num_rows() > 0 )
  	{
  		foreach( $qs->result() as $rs)
  		{
        $style = $this->product_style_model->get($rs->style_code);

  			if( $style->active == 1 && $this->products_model->is_disactive_all($style->code) === FALSE)
  			{
  				$ds 	.= 	'<div class="col-lg-1 col-md-2 col-sm-3 col-xs-4"	style="text-align:center;">';
  				$ds 	.= 		'<div class="product" style="padding:5px;">';
  				$ds 	.= 			'<div class="image">';
  				$ds 	.= 				'<a href="javascript:void(0)" onClick="getOrderGrid(\''.$style->code.'\')">';
  				$ds 	.=					'<img class="img-responsive" src="'.get_cover_image($style->code, 'default').'" />';
  				$ds 	.= 				'</a>';
  				$ds	.= 			'</div>';
  				$ds	.= 			'<div class="description" style="font-size:10px; min-height:50px;">';
  				$ds	.= 				'<a href="javascript:void(0)" onClick="getOrderGrid(\''.$style->code.'\')">';
  				$ds	.= 			$style->code.'<br/>'. number($style->price,2);
  				$ds 	.=  		$style->count_stock == 1 ? ' | <span style="color:red;">'.$this->get_style_sell_stock($style->code).'</span>' : '';
  				$ds	.= 				'</a>';
  				$ds 	.= 			'</div>';
  				$ds	.= 		'</div>';
  				$ds 	.=	'</div>';
  			}
  		}
  	}
  	else
  	{
  		$ds = "no_product";
  	}

  	echo $ds;
  }



  public function get_style_sell_stock($style_code)
  {
    $sell_stock = $this->stock_model->get_style_sell_stock($style_code);
    $reserv_stock = $this->orders_model->get_reserv_stock_by_style($style_code);

    $available = $sell_stock - $reserv_stock;

    return $available >= 0 ? $available : 0;
  }

  public function get_order_grid()
  {
    //----- Attribute Grid By Clicking image
    $style_code = $this->input->get('style_code');
    $style = $this->product_style_model->get($style_code);
    $warehouse = get_null($this->input->get('warehouse_code'));
    $zone = get_null($this->input->get('zone_code'));
  	$sc = 'not exists';
    $view = $this->input->get('isView') == '0' ? FALSE : TRUE;
  	$sc = $this->getOrderGrid($style_code, $view, $warehouse, $zone);
  	$tableWidth	= $this->products_model->countAttribute($style_code) == 1 ? 600 : $this->getOrderTableWidth($style_code);
  	$sc .= ' | '.$tableWidth;
  	$sc .= ' | ' . $style_code;
  	$sc .= ' | ' . $style_code;
    $sc .= ' | ' . get_cover_image($style_code, 'mini');
    $sc .= ' | ' . number($style->price, 2);
  	echo $sc;
  }




  public function getOrderGrid($style_code, $view = FALSE)
	{
		$sc = '';
    $style = $this->product_style_model->get($style_code);
		$isVisual = $style->count_stock == 1 ? FALSE : TRUE;
		$attrs = $this->getAttribute($style->code);

		if( count($attrs) == 1  )
		{
			$sc .= $this->orderGridOneAttribute($style, $attrs[0], $isVisual, $view);
		}
		else if( count( $attrs ) == 2 )
		{
			$sc .= $this->orderGridTwoAttribute($style, $isVisual, $view);
		}
		return $sc;
	}



  public function get_item_grid()
  {
    $sc = "";
    $item_code = $this->input->get('itemCode');
    $warehouse_code = get_null($this->input->get('warehouse_code'));
    $filter = getConfig('MAX_SHOW_STOCK');
    $item = $this->products_model->get($item_code);
    if(!empty($item))
    {
      $qty = $item->count_stock == 1 ? ($item->active == 1 ? $this->showStock($this->get_sell_stock($item->code, $warehouse_code)) : 0) : 1000000;
      $sc = "success | {$item_code} | {$qty}";
    }
    else
    {
      $sc = "Error | ไม่พบสินค้า | {$item_code}";
    }

    echo $sc;
  }



  //--- Po
  public function get_product_grid()
  {
    $style_code = $this->input->get('style_code');
    $sc = label_value('invalid_code');
    $view = FALSE;
    if($this->products_model->is_exists_style($style_code))
    {
      $sc = $this->getProductGrid($style_code);
    	$tableWidth	= $this->products_model->countAttribute($style_code) == 1 ? 600 : $this->getOrderTableWidth($style_code);
    	$sc .= ' | '.$tableWidth;
    	$sc .= ' | ' . $style_code;
    	$sc .= ' | ' . $style_code;
    }

  	echo $sc;
  }

  //---- PO
  public function getProductGrid($style_code)
	{
		$sc = '';
    $style = $this->product_style_model->get($style_code);
		$isVisual = $style->count_stock == 1 ? FALSE : TRUE;
    $showStock = TRUE;
    $view = FALSE;
		$attrs = $this->getAttribute($style->code);

		if( count($attrs) == 1  )
		{
			$sc .= $this->orderGridOneAttribute($style, $attrs[0], $isVisual, $view, $showStock);
		}
		else if( count( $attrs ) == 2 )
		{
			$sc .= $this->orderGridTwoAttribute($style, $isVisual, $view, $showStock);
		}
		return $sc;
	}


  public function showStock($qty)
	{
		return $this->filter == 0 ? $qty : ($this->filter < $qty ? $this->filter : $qty);
	}



  public function orderGridOneAttribute($style, $attr, $isVisual, $view, $is_po = FALSE)
	{
		$sc 		= '';
		$data 	= $attr == 'color' ? $this->getAllColors($style->code) : $this->getAllSizes($style->code);
		$items	= $this->products_model->get_style_items($style->code);
		$sc 	 .= "<table class='table table-bordered'>";
		$i 		  = 0;
    $auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;

    foreach($items as $item )
    {
      $id_attr	= $item->size_code === NULL OR $item->size_code === '' ? $item->color_code : $item->size_code;
      $sc 	.= $i%2 == 0 ? '<tr>' : '';
      $active	= $item->active == 0 ? 'Disactive' : ( $item->can_sell == 0 ? 'N/S' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );
      //$stock	= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->stock_model->get_stock($item->code) )  : 0 ) : 0; //---- สต็อกทั้งหมดทุกคลัง
			$qty 		= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->get_sell_stock($item->code) ) : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้
			//$disabled  = $isVisual === TRUE  && $active == TRUE ? '' : ( ($active !== TRUE OR $qty < 1 ) ? 'disabled' : '');
      $disabled  = ($isVisual === TRUE OR $auz === TRUE OR $is_po === TRUE) && $active == TRUE ? '' : ( ($active !== TRUE OR $qty < 1 ) ? 'disabled' : '');

      if( $qty < 1 && $active === TRUE )
      {
        $txt = $auz === TRUE ? '<span class="font-size-12 red">'.$qty.'</span>' : '<span class="font-size-12 red">Sold out</span>';
        $txt = $qty == 0 ? '<span class="font-size-12 red">Sold out</span>' : $txt;
      }
      else
      {
        $txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
      }

      $available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $limit = $qty === FALSE ? 1000000 : (($auz === TRUE OR $is_po === TRUE) ? 1000000 : $qty);

      // if( $qty < 1 && $active === TRUE )
			// {
			// 	//$txt = '<p class="pull-right red">Sold out</p>';
      //   $txt = $auz === TRUE ? '<span class="font-size-12 red">'.$qty.'</span>' : '<span class="font-size-12 red">Sold out</span>';
      //   $txt = $qty == 0 ? '<span class="font-size-12 red">Sold out</span>' : $txt;
			// }
			// else if( $qty > 0 && $active === TRUE )
			// {
			// 	$txt = '<p class="pull-right green">'. $qty .'  in stock</p>';
			// }
			// else
			// {
			// 	$txt = $active === TRUE ? '' : '<p class="pull-right blue">'.$active.'</p>';
			// }

      //$limit		= $qty === FALSE ? 1000000 : $qty;
      $code = $attr == 'color' ? $item->color_code : $item->size_code;

			$sc 	.= '<td class="middle text-center width-25" style="border-right:0px;">';
			$sc 	.= '<strong>' .	$code. '</strong>';
			$sc 	.= '</td>';

			$sc 	.= '<td class="middle width-25" class="one-attribute">';
			//$sc 	.= $isVisual === FALSE ? '<center><span class="font-size-10 blue">('.($stock < 0 ? 0 : $stock).')</span></center>':'';

      if( $view === FALSE )
			{
			$sc 	.= '<input type="number" class="form-control input-sm order-grid input-qty display-block text-center" name="qty[0]['.$item->code.']" id="'.$item->code.'" onkeyup="valid_qty($(this), '.($qty === FALSE ? 1000000 : $qty).')" '.$disabled.' />';
			}

      $sc 	.= 	'<center>';
      $sc   .= '<span class="font-size-10">';
      $sc   .= $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $sc   .= '</span></center>';
			$sc 	.= '</td>';

			$i++;

			$sc 	.= $i%2 == 0 ? '</tr>' : '';

    }

		$sc	.= "</table>";

		return $sc;
	}





  public function orderGridTwoAttribute($style, $isVisual = FALSE, $view = FALSE, $is_po = FALSE)
	{
    $auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;
		$colors	= $this->getAllColors($style->code);
		$sizes 	= $this->getAllSizes($style->code);
		$sc 		= '';
		$sc 		.= '<table class="table table-bordered">';
		$sc 		.= $this->gridHeader($colors);

		foreach( $sizes as $size_code => $size )
		{
			$sc 	.= '<tr style="font-size:14px;">';
			$sc 	.= '<td class="text-center middle" style="width:80px;"><strong>'.$size_code.'</strong></td>';

			foreach( $colors as $color_code => $color )
			{
        $item = $this->products_model->get_item_by_color_and_size($style->code, $color_code, $size_code);

				if( !empty($item) )
				{
					$active	= $item->active == 0 ? 'ปิด' : ( $item->can_sell == 0 ? 'ไม่มี' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );
					//$stock	= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->stock_model->get_stock($item->code) )  : 0 ) : 0; //---- สต็อกทั้งหมดทุกคลัง
					$qty 		= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->get_sell_stock($item->code) ) : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้
					$disabled  = ($isVisual === TRUE OR $auz === TRUE OR $is_po === TRUE) && $active == TRUE ? '' : ( ($active !== TRUE OR $qty < 1 ) ? 'disabled' : '');
					if( $qty < 1 && $active === TRUE )
					{
						$txt = $auz === TRUE ? '<span class="font-size-12 red">'.$qty.'</span>' : '<span class="font-size-12 red">หมด</span>';
            $txt = $qty == 0 ? '<span class="font-size-12 red">หมด</span>' : $txt;
					}
					else
					{
						$txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
					}

					$available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
					$limit = $qty === FALSE ? 1000000 : (($auz === TRUE OR $is_po === TRUE) ? 1000000 : $qty);


					$sc 	.= '<td class="order-grid">';
					//$sc 	.= $isVisual === FALSE ? '<center><span class="font-size-10 blue">('.$stock.')</span></center>' : '';
					if( $view === FALSE )
					{
						$sc 	.= '<input type="number" min="1" max="'.$limit.'" class="form-control order-grid input-qty text-center" name="qty['.$item->color_code.']['.$item->code.']" id="'.$item->code.'" onkeyup="valid_qty($(this), '.$limit.')" '.$disabled.' />';
					}

					$sc 	.= ($isVisual === FALSE OR $is_po === TRUE) ? '<center style="font-weight:bold; color:#0F00FF"><strong>'.$available.'</strong></center>' : '';
					$sc 	.= '</td>';
				}
				else
				{
					$sc .= '<td class="order-grid">N/A</td>';
				}
			} //--- End foreach $colors

			$sc .= '</tr>';
		} //--- end foreach $sizes
	$sc .= '</table>';
	return $sc;
	}







  public function getAttribute($style_code)
  {
    $sc = array();
    $color = $this->products_model->count_color($style_code);
    $size  = $this->products_model->count_size($style_code);
    if( $color > 0 )
    {
      $sc[] = "color";
    }

    if( $size > 0 )
    {
      $sc[] = "size";
    }
    return $sc;
  }





  public function gridHeader(array $colors)
  {
    $sc = '<tr class="font-size-12"><td>&nbsp;</td>';
    foreach( $colors as $code => $name )
    {
      $sc .= '<td class="text-center middle"><strong>'.$code.'<br>'. $name.'</strong></td>';
    }
    $sc .= '</tr>';
    return $sc;
  }





  public function getAllColors($style_code)
	{
		$sc = array();
    $colors = $this->products_model->get_all_colors($style_code);
    if($colors !== FALSE)
    {
      foreach($colors as $color)
      {
        $sc[$color->code] = $color->name;
      }
    }

    return $sc;
	}




  public function getAllSizes($style_code)
	{
		$sc = array();
		$sizes = $this->products_model->get_all_sizes($style_code);
		if( $sizes !== FALSE )
		{
      foreach($sizes as $size)
      {
        $sc[$size->code] = $size->name;
      }
		}
		return $sc;
	}



  public function getOrderTableWidth($style_code)
  {
    $sc = 800; //--- ชั้นต่ำ
    $tdWidth = 70;  //----- แต่ละช่อง
    $padding = 100; //----- สำหรับช่องแสดงไซส์
    $color = $this->products_model->count_color($style_code);
    if($color > 0)
    {
      $sc = $color * $tdWidth + $padding;
    }

    return $sc;
  }



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER');
    $run_digit = getConfig('RUN_DIGIT_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);
    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }



  public function print_order_sheet($code, $barcode = '')
  {
    $this->load->model('masters/products_model');

    $this->load->library('printer');
    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
    $details = $this->orders_model->get_order_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $ds['order'] = $order;
    $ds['details'] = $details;
    $ds['is_barcode'] = $barcode != '' ? TRUE : FALSE;
    $this->load->view('print/print_order_sheet', $ds);
  }

  public function get_sell_stock($item_code)
  {
    $auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;
    $sell_stock = $this->stock_model->get_sell_stock($item_code);
    $reserv_stock = $this->orders_model->get_reserv_stock($item_code);
    $availableStock = $sell_stock - $reserv_stock;
		return $auz === TRUE ? $availableStock : ($availableStock < 0 ? 0 : $availableStock);
  }




  public function get_detail_table($order_code)
  {
    $sc = "no data found";
    $order = $this->orders_model->get($order_code);
    $details = $this->orders_model->get_order_details($order_code);
    if($details != FALSE )
    {
      $no = 1;
      $total_qty = 0;
      $total_discount = 0;
      $total_amount = 0;
      $total_order = 0;
      $ds = array();
      foreach($details as $rs)
      {
        $arr = array(
                "id"		=> $rs->id,
                "no"	=> $no,
                "imageLink"	=> get_product_image($rs->product_code, 'mini'),
                "productCode"	=> $rs->product_code,
                "productName"	=> $rs->product_name,
                "cost"				=> $rs->cost,
                "price"	=> number_format($rs->price, 2),
                "qty"	=> number_format($rs->qty),
                "discount"	=> discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
                "amount"	=> number_format($rs->total_amount, 2)
                );
        array_push($ds, $arr);
        $total_qty += $rs->qty;
        $total_discount += $rs->discount_amount;
        $total_amount += $rs->total_amount;
        $total_order += $rs->qty * $rs->price;
        $no++;
      }

      $netAmount = ( $total_amount - $order->bDiscAmount ) + $order->shipping_fee + $order->service_fee;

      $arr = array(
            "total_qty" => number($total_qty),
            "order_amount" => number($total_order, 2),
            "total_discount" => number($total_discount, 2),
            "shipping_fee"	=> number($order->shipping_fee,2),
            "service_fee"	=> number($order->service_fee, 2),
            "total_amount" => number($total_amount, 2),
            "net_amount"	=> number($netAmount,2)
          );
      array_push($ds, $arr);
      $sc = json_encode($ds);
    }
    echo $sc;

  }


  public function get_pay_amount()
  {
    $pay_amount = 0;

    if($this->input->get('order_code'))
    {
      $code = $this->input->get('order_code');

      //--- ยอดรวมหลังหักส่วนลด ตาม item
      // $amount = $this->orders_model->get_order_total_amount($code);
      // //--- ส่วนลดท้ายบิล
      // $bDisc = $this->orders_model->get_bill_discount($code);
      // $pay_amount = $amount - $bDisc;

      $pay_amount = $this->orders_model->get_order_balance($code);
    }

    echo $pay_amount;
  }



  public function get_account_detail($id)
  {
    $sc = 'fail';
    $this->load->model('masters/bank_model');
    $this->load->helper('bank');
    $rs = $this->bank_model->get_account_detail($id);
    if($rs !== FALSE)
    {
      $ds = bankLogoUrl($rs->bank_code).' | '.$rs->bank_name.' สาขา '.$rs->branch.'<br/>เลขที่บัญชี '.$rs->acc_no.'<br/> ชื่อบัญชี '.$rs->acc_name;
      $sc = $ds;
    }

    echo $sc;
  }



  public function confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $this->load->helper('bank');
      $this->load->model('orders/order_payment_model');

      $file = isset( $_FILES['image'] ) ? $_FILES['image'] : FALSE;
      $order_code = $this->input->post('order_code');
      $date = $this->input->post('payDate');
      $h = $this->input->post('payHour');
      $m = $this->input->post('payMin');
      $dhm = $date.' '.$h.':'.$m.':00';
      $pay_date = date('Y-m-d H:i:s', strtotime($dhm));
      $img_name = $order_code.'-'.date('Ymdhis');
      $arr = array(
        'order_code' => $order_code,
        'order_amount' => $this->input->post('orderAmount'),
        'pay_amount' => $this->input->post('payAmount'),
        'pay_date' => $pay_date,
        'id_account' => $this->input->post('id_account'),
        'acc_no' => $this->input->post('acc_no'),
        'user' => get_cookie('uname'),
        'is_deposit' => $this->input->post('is_deposit'),
        'img' => $img_name
      );

      //--- บันทึกรายการ
      if($this->order_payment_model->add($arr))
      {
        $arr = array(
          'order_code' => $order_code,
          'state' => 2,
          'update_user' => get_cookie('uname')
        );
        $this->order_state_model->add_state($arr);
      }
      else
      {
        $sc = FALSE;
        $message = 'บันทึกรายการไม่สำเร็จ';
      }

      if($file !== FALSE)
      {
        $rs = $this->do_upload($file, $img_name);
        if($rs !== TRUE)
        {
          $sc = FALSE;
          $message = $sc;
        }
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function do_upload($file, $img_name)
	{
    $this->load->library('upload');
    $sc = TRUE;

		$image_path = $this->config->item('image_path').'payments/';
    $image 	= new upload($file);
    if( $image->uploaded )
    {
      $image->file_new_name_body = $img_name; 		//--- เปลี่ยนชือ่ไฟล์ตาม order_code
      $image->image_resize			 = TRUE;		//--- อนุญาติให้ปรับขนาด
      $image->image_retio_fill	 = TRUE;		//--- เติกสีให้เต็มขนาดหากรูปภาพไม่ได้สัดส่วน
      $image->file_overwrite		 = TRUE;		//--- เขียนทับไฟล์เดิมได้เลย
      $image->auto_create_dir		 = TRUE;		//--- สร้างโฟลเดอร์อัตโนมัติ กรณีที่ไม่มีโฟลเดอร์
      $image->image_x					   = 500;		//--- ปรับขนาดแนวนอน
      //$image->image_y					   = 800;		//--- ปรับขนาดแนวตั้ง
      $image->image_ratio_y      = TRUE;  //--- ให้คงสัดส่วนเดิมไว้
      $image->image_background_color	= "#FFFFFF";		//---  เติมสีให้ตามี่กำหนดหากรูปภาพไม่ได้สัดส่วน
      $image->image_convert			= 'jpg';		//--- แปลงไฟล์

      $image->process($image_path);						//--- ดำเนินการตามที่ได้ตั้งค่าไว้ข้างบน

      if( ! $image->processed )	//--- ถ้าไม่สำเร็จ
      {
        $sc 	= $image->error;
      }
    } //--- end if

    $image->clean();	//--- เคลียร์รูปภาพออกจากหน่วยความจำ

		return $sc;
	}




  public function view_payment_detail($id)
  {
    $this->load->model('orders/order_payment_model');
    $this->load->model('masters/bank_model');
    $sc = TRUE;
    $code = $this->input->post('order_code');
    $rs = $this->order_payment_model->get($id);

    if(!empty($rs))
    {
      $bank = $this->bank_model->get_account_detail($rs->id_account);
      $img  = payment_image_url($rs->img); //--- order_helper
      $ds   = array(
        'order_code' => $code,
        'orderAmount' => number($rs->order_amount, 2),
        'payAmount' => number($rs->pay_amount, 2),
        'payDate' => thai_date($rs->pay_date, TRUE, '/'),
        'bankName' => $bank->bank_name,
        'branch' => $bank->branch,
        'accNo' => $bank->acc_no,
        'accName' => $bank->acc_name,
        'date_add' => thai_date($rs->date_upd, TRUE, '/'),
        'imageUrl' => $img === FALSE ? '' : $img,
        'valid' => "no"
      );
    }
    else
    {
      $sc = FALSE;
    }

    echo $sc === TRUE ? json_encode($ds) : 'fail';
  }


  public function update_shipping_code()
  {
    $order_code = $this->input->post('order_code');
    $ship_code  = $this->input->post('shipping_code');
    if($order_code && $ship_code)
    {
      $rs = $this->orders_model->update_shipping_code($order_code, $ship_code);
      echo $rs === TRUE ? 'success' : 'fail';
    }
  }


  public function update_shipping_fee()
  {
    $order_code = $this->input->post('order_code');
    $shipping_fee = $this->input->post('shipping_fee');
    if($this->orders_model->update_shipping_fee($order_code, $shipping_fee))
    {
      update_order_total_amount($order_code);
      echo 'success';
    }
    else
    {
      echo 'failed';
    }
  }


  public function update_service_fee()
  {
    $order_code = $this->input->post('order_code');
    $service_fee = $this->input->post('service_fee');
    if($this->orders_model->update_service_fee($order_code, $service_fee))
    {
      update_order_total_amount($order_code);
      echo 'success';
    }
    else
    {
      echo 'failed';
    }
  }



  public function save_address()
  {
    $sc = TRUE;
    if($this->input->post('customer_ref'))
    {
      $this->load->model('address/address_model');
      $id = $this->input->post('id_address');
      $arr = array(
        'code' => trim($this->input->post('customer_ref')),
        'name' => trim($this->input->post('name')),
        'address' => trim($this->input->post('address')),
        'sub_district' => trim($this->input->post('sub_district')),
        'district' => trim($this->input->post('district')),
        'province' => trim($this->input->post('province')),
        'postcode' => trim($this->input->post('postcode')),
        'phone' => trim($this->input->post('phone')),
        'email' => trim($this->input->post('email')),
        'alias' => trim($this->input->post('alias'))
      );

      if(!empty($id))
      {
        $rs = $this->address_model->update_shipping_address($id, $arr);
      }
      else
      {
        $rs = $this->address_model->add_shipping_address($arr);
      }

      if($rs === FALSE)
      {
        $sc = FALSE;
        $message = 'เพิ่มที่อยู่ไม่สำเร็จ';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไมพบชื่อลูกค้าออนไลน์';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function get_address_table()
  {
    $sc = TRUE;
    if($this->input->post('customer_ref'))
    {
      $code = $this->input->post('customer_ref');
      $order_code = $this->input->post('order_code');
      if(!empty($code))
      {
        $order = empty($order_code) ? NULL : $this->orders_model->get($order_code);

        $ds = array();
        $this->load->model('address/address_model');
        $adrs = $this->address_model->get_shipping_address($code);
        if(!empty($adrs))
        {
          foreach($adrs as $rs)
          {
            $arr = array(
              'id' => $rs->id,
              'name' => $rs->name,
              'address' => $rs->address.' '.$rs->sub_district.' '.$rs->district.' '.$rs->province.' '.$rs->postcode,
              'phone' => $rs->phone,
              'email' => $rs->email,
              'alias' => $rs->alias,
              'default' => empty($order_code) ? ($rs->is_default == 1? 1 : 0) : ($rs->id == $order->address_id) ? 1 : ''
            );
            array_push($ds, $arr);
          }
        }
        else
        {
          $sc = FALSE;
        }
      }
      else
      {
        $sc = FALSE;
      }
    }

    echo $sc === TRUE ? json_encode($ds) : 'noaddress';
  }



  public function set_order_address()
  {
    $id = $this->input->post('id_address');
    $code = $this->input->post('order_code');
    //--- set new default
    $rs = $this->orders_model->set_address_id($code, $id);
    echo $rs === TRUE ? 'success' :'fail';
  }



  public function set_default_address()
  {
    $this->load->model('address/address_model');
    $id = $this->input->post('id_address');
    $code = $this->input->post('customer_ref');
    //--- drop current
    $this->address_model->unset_default_shipping_address($code);

    //--- set new default
    $rs = $this->address_model->set_default_shipping_address($id);
    echo $rs === TRUE ? 'success' :'fail';
  }


  public function get_shipping_address()
  {
    $this->load->model('address/address_model');
    $id = $this->input->post('id_address');
    $rs = $this->address_model->get_shipping_detail($id);
    if(!empty($rs))
    {
      $arr = array(
        'id' => $rs->id,
        'code' => $rs->code,
        'name' => $rs->name,
        'address' => $rs->address,
        'sub_district' => $rs->sub_district,
        'district' => $rs->district,
        'province' => $rs->province,
        'postcode' => $rs->postcode,
        'phone' => $rs->phone,
        'email' => $rs->email,
        'alias' => $rs->alias,
        'is_default' => $rs->is_default
      );

      echo json_encode($rs);
    }
    else
    {
      echo 'nodata';
    }
  }



  public function delete_shipping_address()
  {
    $this->load->model('address/address_model');
    $id = $this->input->post('id_address');
    $rs = $this->address_model->delete_shipping_address($id);
    echo $rs === TRUE ? 'success' : 'fail';
  }



  public function set_never_expire()
  {
    $code = $this->input->post('order_code');
    $option = $this->input->post('option');
    $rs = $this->orders_model->set_never_expire($code, $option);
    echo $rs === TRUE ? 'success' : 'ทำรายการไม่สำเร็จ';
  }


  public function un_expired()
  {
    $code = $this->input->post('order_code');
    $rs = $this->orders_model->un_expired($code);
    echo $rs === TRUE ? 'success' : 'ทำรายการไม่สำเร็จ';
  }



  public function order_state_change()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $code = $this->input->post('order_code');
      $state = $this->input->post('state');
      $order = $this->orders_model->get($code);
      $details = $this->orders_model->get_order_details($code);
      if(!empty($order))
      {
        //--- ถ้าเป็นเบิกแปรสภาพ จะมีการผูกสินค้าไว้
        if($order->role == 'T')
        {
          $this->load->model('inventory/transform_model');
          //--- หากมีการรับสินค้าที่ผูกไว้แล้วจะไม่อนุญาติให้เปลี่ยนสถานะใดๆ
          $is_received = $this->transform_model->is_received($code);
          if($is_received === TRUE)
          {
            $sc = FALSE;
            $message = 'ใบเบิกมีการรับสินค้าแล้วไม่อนุญาติให้ย้อนสถานะ';
          }
        }

        //--- ถ้าเป็นยืมสินค้า
        if($order->role == 'L')
        {
          $this->load->model('inventory/lend_model');
          //--- หากมีการรับสินค้าที่ผูกไว้แล้วจะไม่อนุญาติให้เปลี่ยนสถานะใดๆ
          $is_received = $this->lend_model->is_received($code);
          if($is_received === TRUE)
          {
            $sc = FALSE;
            $message = 'ใบเบิกมีการรับคืนสินค้าแล้วไม่อนุญาติให้ย้อนสถานะ';
          }
        }


        if($sc === TRUE)
        {
          $this->db->trans_start();

          //--- ถ้าเปิดบิลแล้ว
          if($sc === TRUE && $order->state == 8)
          {
            if($state < 8)
            {
              $this->roll_back_action($order);
            }

            if($state == 9)
            {
              $this->roll_back_action($order);
              $this->cancle_order($code, $order->role);
            }
          }

          else if($sc === TRUE && $order->state != 8)
          {
            if($state == 9)
            {
              $this->cancle_order($code, $order->role);
            }
          }

          if($sc === TRUE)
          {
            $rs = $this->orders_model->change_state($code, $state);

            if($rs)
            {
              $arr = array(
                'order_code' => $code,
                'state' => $state,
                'update_user' => get_cookie('uname')
              );

              $this->order_state_model->add_state($arr);
            }
          }

          $this->rollback_unvalid_details($code);

          $this->db->trans_complete();

          if($this->db->trans_status() === FALSE)
          {
            $sc = FALSE;
          }
        }
      }

      echo $sc === TRUE ? 'success' : $message;
    }
    else
    {
      echo 'ไม่พบข้อมูลออเดอร์';
    }
  }


  public function rollback_unvalid_details($code)
  {
    $this->load->model('inventory/prepare_model');

    $details = $this->orders_model->get_order_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $prepared = $this->prepare_model->get_prepared($code, $rs->product_code);
        if($prepared < $rs->qty)
        {
          $this->orders_model->unvalid_detail($rs->id);
        }
      }
    }
  }

  public function roll_back_action($order)
  {
    $this->load->model('inventory/movement_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('inventory/transform_model');
    $this->load->model('inventory/lend_model');
    $this->load->model('stock/stock_model');

    //---- set is_complete = 0
    $this->orders_model->un_complete($order->code);

    //---- move cancle product back to  buffer
    $this->cancle_model->restore_buffer($order->code);

    //--- remove movement
    $this->movement_model->drop_movement($order->code);

    //--- restore sold product back to buffer
    $sold = $this->invoice_model->get_details($order->code);

    if(!empty($sold))
    {
      foreach($sold as $rs)
      {
        if($rs->is_count == 1)
        {
          //---- restore_buffer
          if($this->buffer_model->is_exists($rs->reference, $rs->product_code, $rs->zone_code) === TRUE)
          {
            $this->buffer_model->update($rs->reference, $rs->product_code, $rs->zone_code, $rs->qty);
          }
          else
          {
            $ds = array(
              'order_code' => $rs->reference,
              'product_code' => $rs->product_code,
              'warehouse_code' => $rs->warehouse_code,
              'zone_code' => $rs->zone_code,
              'qty' => $rs->qty,
              'user' => $rs->user
            );

            $this->buffer_model->add($ds);
          }

          if($order->role === 'N' OR $order->role === 'L')
          {
            //--- remove stock from zone
            $this->stock_model->update_stock_zone($order->zone_code, $rs->product_code, (-1) * $rs->qty);
          }
        }

        $this->invoice_model->drop_sold($rs->id);

        //------ หากเป็นออเดอร์เบิกแปรสภาพ
        if($order->role == 'T')
        {
          $this->transform_model->reset_sold_qty($order->code);
        }

        //-- หากเป็นออเดอร์ยืม
        if($order->role == 'L')
        {
          $this->lend_model->drop_backlogs_list($order->code);
        }

        if($order->role === 'N' OR $order->role === 'C')
        {

        }
      } //--- end foreach
    } //---- end sold

  }


  public function cancle_order($code, $role)
  {
    $this->load->model('inventory/prepare_model');
    $this->load->model('inventory/qc_model');
    $this->load->model('inventory/transform_model');
    $this->load->model('orders/order_payment_model');

    $order = $this->orders_model->get($code);

    $use_qc = getConfig('USE_QC');
    //---- เมื่อมีการยกเลิกออเดอร์
    //--- 1. เคลียร์ buffer เข้า cancle
    $this->clear_buffer($code);

    //--- 2. ลบประวัติการจัดสินค้า
    $this->prepare_model->clear_prepare($code);

    //--- 3. ลบประวัติการตรวจสินค้า
    if($use_qc)
    {
      $this->qc_model->clear_qc($code);
    }

    //--- 4. ลบรายการสั่งซื้อ
    $this->orders_model->clear_order_detail($code);

    //--- 5. ยกเลิกออเดอร์
    $this->orders_model->set_status($code, 2);

    if($role == 'S')
    {

      if($order->is_term == 1)
      {
        //--- clear order_credit
        $this->clear_credit_payment($order);
      }
      else
      {
        //---- clear payment
        $this->order_payment_model->clear_payment($code);
      }

    }


    //--- 6. ลบรายการที่ผู้ไว้ใน order_transform_detail (กรณีเบิกแปรสภาพ)
    if($role == 'T')
    {
      $this->transform_model->clear_transform_detail($code);
      $this->transform_model->close_transform($code);
    }

  }

  //--- รับ obj
  public function clear_credit_payment($order)
  {
    $this->load->model('masters/customers_model');
    $this->load->model('account/order_credit_model');
    //--- ดึงยอดที่เคยตั้งหนี้ไว้ แล้วทำให้เป็นค่าลบเพื่อบวกกลับเข้ายอดใช้ไป
    $credit = $this->order_credit_model->get($order->code);
    $amount = $credit->amount * (-1);
    //--- คืนยอดใช้ไป
    if($this->customers_model->update_used($order->customer_code, $amount))
    {
      //--- ลบรายการตั้งหนี้
      return $this->order_credit_model->delete($order->code);
    }

    return FALSE;
  }

  //--- เคลียร์ยอดค้างที่จัดเกินมาไปที่ cancle หรือ เคลียร์ยอดที่เป็น 0
  public function clear_buffer($code)
  {
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');

    $buffer = $this->buffer_model->get_all_details($code);
    //--- ถ้ายังมีรายการที่ค้างอยู่ใน buffer เคลียร์เข้า cancle
    if(!empty($buffer))
    {
      foreach($buffer as $rs)
      {
        if($rs->qty != 0)
        {
          $arr = array(
            'order_code' => $rs->order_code,
            'product_code' => $rs->product_code,
            'warehouse_code' => $rs->warehouse_code,
            'zone_code' => $rs->zone_code,
            'qty' => $rs->qty,
            'user' => get_cookie('uname')
          );
          //--- move buffer to cancle
          $this->cancle_model->add($arr);
        }
        //--- delete cancle
        $this->buffer_model->delete($rs->id);
      }
    }
  }


  public function update_discount()
  {
    $code = $this->input->post('order_code');
    $discount = $this->input->post('discount');
    $approver = $this->input->post('approver');
    $order = $this->orders_model->get($code);
    $user = get_cookie('uname');
    $this->load->model('orders/discount_logs_model');
  	if(!empty($discount))
  	{
  		foreach( $discount as $id => $value )
  		{
  			//----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
  			if( $value != "")
  			{
  				//--- ได้ Obj มา
  				$detail = $this->orders_model->get_detail($id);

  				//--- ถ้ารายการนี้มีอยู่
  				if( $detail !== FALSE )
  				{
  					//------ คำนวณส่วนลดใหม่
  					$step = explode('+', $value);
  					$discAmount = 0;
  					$discLabel = array(0, 0, 0);
  					$price = $detail->price;
  					$i = 0;
  					foreach($step as $discText)
  					{
  						if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
  						{
  							$disc = explode('%', $discText);
  							$disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
  							$discount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
  							$discLabel[$i] = count($disc) == 1 ? $disc[0] : $disc[0].'%';
  							$discAmount += $discount;
  							$price -= $discount;
  						}
  						$i++;
  					}

  					$total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
  					$total_amount = ( $detail->qty * $detail->price ) - $total_discount; //--- ยอดรวมสุดท้าย

  					$arr = array(
  								"discount1" => $discLabel[0],
  								"discount2" => $discLabel[1],
  								"discount3" => $discLabel[2],
  								"discount_amount"	=> $total_discount,
  								"total_amount" => $total_amount ,
  								"id_rule"	=> NULL,
                  "update_user" => $user
  							);

  					$cs = $this->orders_model->update_detail($id, $arr);
            if($cs)
            {
              $log_data = array(
    												"order_code"		=> $code,
    												"product_code"	=> $detail->product_code,
    												"old_discount"	=> discountLabel($detail->discount1, $detail->discount2, $detail->discount3),
    												"new_discount"	=> discountLabel($discLabel[0], $discLabel[1], $discLabel[2]),
    												"user"	=> $user,
    												"approver"		=> $approver
    												);
    					$this->discount_logs_model->logs_discount($log_data);
            }

  				}	//--- end if detail
  			} //--- End if value
  		}	//--- end foreach

      $this->orders_model->set_status($code, 0);
  	}
    echo 'success';
  }


  public function update_non_count_price()
  {
    $code = $this->input->post('order_code');
    $id = $this->input->post('id_order_detail');
    $price = $this->input->post('price');
    $user = get_cookie('uname');

    $order = $this->orders_model->get($code);
    if($order->state == 8) //--- ถ้าเปิดบิลแล้ว
    {
      echo 'ไม่สามารถแก้ไขราคาได้ เนื่องจากออเดอร์ถูกเปิดบิลไปแล้ว';
    }
    else
    {
        //----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
        if( $price != "" )
        {
          //--- ได้ Obj มา
          $detail = $this->orders_model->get_detail($id);

          //--- ถ้ารายการนี้มีอยู่
          if( $detail !== FALSE )
          {
            //------ คำนวณส่วนลดใหม่
            $price_c = $price;
  					$discAmount = 0;
            $step = array($detail->discount1, $detail->discount2, $detail->discount3);
            foreach($step as $discount)
            {
              $disc 	= explode('%', $discount);
              $disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
              $discount = count($disc) == 1 ? $disc[0] : $price_c * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
              $discAmount += $discount;
              $price_c -= $discount;
            }

            $total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
  					$total_amount = ( $detail->qty * $price ) - $total_discount; //--- ยอดรวมสุดท้าย

            $arr = array(
                  "price"	=> $price,
                  "discount_amount"	=> $total_discount,
                  "total_amount" => $total_amount,
                  "update_user" => $user
                );
            $cs = $this->orders_model->update_detail($id, $arr);
          }	//--- end if detail
        } //--- End if value

        $this->orders_model->set_status($code, 0);

      echo 'success';
    }
  }



  public function update_price()
  {
    $code = $this->input->post('order_code');
    $ds = $this->input->post('price');
  	$approver	= $this->input->post('approver');
  	$user = get_cookie('uname');
    $this->load->model('orders/discount_logs_model');
  	foreach( $ds as $id => $value )
  	{
  		//----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
  		if( $value != "" )
  		{
  			//--- ได้ Obj มา
  			$detail = $this->orders_model->get_detail($id);

  			//--- ถ้ารายการนี้มีอยู่
  			if( $detail !== FALSE )
  			{
          if($detail->price != $value)
          {
            //------ คำนวณส่วนลดใหม่
    				$price 	= $value;
            $discAmount = 0;
            $step = array($detail->discount1, $detail->discount2, $detail->discount3);
            foreach($step as $discount_text)
            {
              $disc 	= explode('%', $discount_text);
              $disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
              $discount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
              $discAmount += $discount;
              $price -= $discount;
            }

            $total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
  					$total_amount = ( $detail->qty * $value ) - $total_discount; //--- ยอดรวมสุดท้าย

            $arr = array(
              'price' => $value,
              'discount_amount' => $total_discount,
              'total_amount' => $total_amount,
              'update_user' => $user
            );

            $cs = $this->orders_model->update_detail($id, $arr);
            if($cs)
            {
              $log_data = array(
                "order_code"		=> $code,
                "product_code"	=> $detail->product_code,
                "old_price"	=> $detail->price,
                "new_price"	=> $value,
                "user"	=> $user,
                "approver"		=> $approver
              );
              $this->discount_logs_model->logs_price($log_data);
            }
          }

  			}	//--- end if detail
  		} //--- End if value
  	}	//--- end foreach

    $this->orders_model->set_status($code, 0);

  	echo 'success';
  }


  public function paid_order($code)
  {
    $sc = TRUE;
    $this->load->model('account/payment_receive_model');
    $order = $this->orders_model->get($code);
    if($order->is_paid == 0)
    {
      //--- บันทึกรับเงิน
      //--- เพิ่มรายการเข้า payment_receive
      $payment = array(
        'reference' => $order->code,
        'customer_code' => $order->customer_code,
        'pay_date' => now(),
        'amount' => $order->balance,
        'payment_type' => 'TR',
        'valid' => 1
      );

      $this->db->trans_begin();

      if(! $this->payment_receive_model->add($payment) )
      {
        $sc = FALSE;
        $this->error = 'เพิ่มรายการเงินเข้าไม่สำเร็จ';
      }

      if( ! $this->orders_model->paid($code, TRUE))
      {
        $sc = FALSE;
        $this->error = 'เปลี่ยนสถานะออเดอร์เป็นชำระแล้วไม่สำเร็จ';
      }

      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }

    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function unpaid_order($code)
  {
    $sc = TRUE;
    $this->load->model('account/payment_receive_model');
    $order = $this->orders_model->get($code);
    if($order->is_paid == 1)
    {
      //--- บันทึกรับเงิน
      //--- เพิ่มรายการเข้า payment_receive
      $payment = array(
        'reference' => $order->code,
        'customer_code' => $order->customer_code,
        'pay_date' => now(),
        'amount' => (-1) * $order->balance,
        'payment_type' => 'TR',
        'valid' => 1
      );

      $this->db->trans_begin();

      if(! $this->payment_receive_model->add($payment) )
      {
        $sc = FALSE;
        $this->error = 'เพิ่มรายการเงินเข้าไม่สำเร็จ';
      }

      if( ! $this->orders_model->paid($code, FALSE))
      {
        $sc = FALSE;
        $this->error = 'ยกเลิกสถานะออเดอร์ไม่สำเร็จ';
      }

      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }

    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function get_summary()
  {
    $this->load->model('masters/bank_model');
    $code = $this->input->post('order_code');
    $order = $this->orders_model->get($code);
    $details = $this->orders_model->get_order_details($code);
    $bank = $this->bank_model->get_active_bank();
    if(!empty($details))
    {
      echo get_summary($order, $details, $bank); //--- order_helper;
    }
  }



  public function clear_filter()
  {
    $filter = array(
      'code',
      'customer',
      'user',
      'reference',
      'shipCode',
      'channels',
      'payment',
      'fromDate',
      'toDate',
      'is_paid',
      'order_by',
      'sort_by'
    );

    clear_filter($filter);
  }
}
?>
