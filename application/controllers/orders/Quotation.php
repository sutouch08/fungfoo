<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends PS_Controller
{
  public $menu_code = 'SOODQT';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ใบเสนอราคา';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/quotation';
    $this->load->model('orders/quotation_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/products_model');

    $this->load->helper('product_images');
    $this->load->helper('discount');
  }


  public function index()
  {
    $filter = array(
      'code'          => get_filter('code', 'qu_code', ''),
      'customer_code'      => get_filter('customer', 'qu_customer_code', ''),
      'contact' => get_filter('contact', 'qu_contact', ''),
      'user'          => get_filter('user', 'qu_user', ''),
      'reference'     => get_filter('reference', 'qu_reference', ''),
      'from_date'     => get_filter('fromDate', 'qu_fromDate', ''),
      'to_date'       => get_filter('toDate', 'qu_toDate', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->quotation_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$data = $this->quotation_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if(!empty($data))
    {
      foreach($data as $rs)
      {
        $rs->amount = $this->quotation_model->get_sum_total_amount($rs->code) - $rs->bDiscAmount;
      }
    }


    $filter['data'] = $data;

		$this->pagination->initialize($init);
    $this->load->view('quotation/quotation_list', $filter);
  }



  public function add_new()
  {
    $data['code'] = $this->get_new_code();
    $this->load->view('quotation/quotation_add', $data);
  }



  public function add()
  {
    if($this->pm->can_add)
    {
      $customer_code = $this->input->post('customerCode');
      if($customer_code != '')
      {
        $customer = $this->customers_model->get($customer_code);
        if(!empty($customer))
        {
          $date = db_date($this->input->post('date_add'));
          $code = $this->get_new_code($date);

          $arr = array(
            'code' => $code,
            'customer_code' => $customer->code,
            'contact' => get_null($this->input->post('contact')),
            'is_term' => $this->input->post('is_term'),
            'credit_term' => $this->input->post('credit_term'),
            'user' => get_cookie('uname'),
            'remark' => $this->input->post('remark'),
            'date_add' => $date
          );

          if($this->quotation_model->add($arr))
          {
            redirect($this->home.'/edit/'.$code);
          }
        }
        else
        {
          set_error("รหัสลูกค้าไม่ถูกต้อง");
          redirect($this->home ."/add_new");
        }
      }
      else
      {
        set_error("ไม่พบรหัสลูกค้า");
        redirect($this->home ."/add_new");
      }
    }
    else
    {
      set_error("คุณไม่มีสิทธิ์เพิ่มเอกสาร");
      redirect($this->home ."/add_new");
    }

  }


  public function edit($code)
  {
    if($this->pm->can_add)
    {
      $this->load->helper('product_tab');
      $data = $this->quotation_model->get($code);
      $data->customer_name = $this->customers_model->get_name($data->customer_code);
      $ds = array(
        'data' => $data,
        'details' => $this->quotation_model->get_details($code)
      );

      $this->load->view('quotation/quotation_edit', $ds);
    }
    else
    {
      $this->load->view('deny_page');
    }
  }


  public function update()
  {
    $sc = TRUE;
    if(! $this->pm->can_edit)
    {
      $sc = FALSE;
      $this->error = "No Permission";
    }
    else
    {
      if($this->input->post('code'))
      {
        $code = $this->input->post('code');
        $date = db_date($this->input->post('date_add'), TRUE);
        $customer = $this->customers_model->get($this->input->post('customer_code'));
        if(!empty($customer))
        {
          $arr = array(
            'customer_code' => $customer->code,
            'contact' => get_null($this->input->post('contact')),
            'is_term' => $this->input->post('is_term'),
            'credit_term' => $this->input->post('credit_term'),
            'update_user' => get_cookie('uname'),
            'remark' => $this->input->post('remark'),
            'date_add' => $date
          );

          if(! $this->quotation_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "แก้ไขข้อมูลไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "รหัสลูกค้าไม่ถูกต้อง";
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร";
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function add_details()
  {
    $sc = TRUE;
    if($this->input->post('data'))
    {
      $this->load->helper('discount');
      $data = json_decode($this->input->post('data'));
      $code = $this->input->post('code');
      $discLabel = $this->input->post('discountLabel');
      $qt = $this->quotation_model->get($code);
      if(!empty($data))
      {
        foreach($data as $rs)
        {
          $pd_code = $rs->product_code; //-- รหัสสินค้า
          $qty = $rs->qty;
          $item = $this->products_model->get($pd_code);
          if($qty > 0)
          {
            //---
            $ds = $this->quotation_model->get_detail($code, $item->code);
            $disc = parse_discount_text($discLabel, $item->price);
            if(!empty($ds))
            {
              $new_qty = $ds->qty + $qty;
              $final_price = $item->price - $disc['discount_amount'];
              $discount_amount = $disc['discount_amount'] * $new_qty;
              $total_amount = $final_price * $new_qty;

              $arr = array(
                'qty' => $new_qty,
                'discount1' => $disc['discount1'],
                'discount2' => $disc['discount2'],
                'discount3' => $disc['discount3'],
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount
              );

              //--- Update
              if(! $this->quotation_model->update_detail($ds->id, $arr))
              {
                $sc = FALSE;
                $this->error = "Update failed";
              }
            }
            else
            {
              $arr = array(
                'quotation_code' => $code,
                'style_code' => $item->style_code,
                'product_code' => $item->code,
                'product_name' => $item->name,
                'price' => $item->price,
                'qty' => $qty,
                'discount1' => $disc['discount1'],
                'discount2' => $disc['discount2'],
                'discount3' => $disc['discount3'],
                'discount_amount' => $disc['discount_amount'] * $qty,
                'total_amount' => ($item->price - $disc['discount_amount']) * $qty,
                'date_add' => $qt->date_add
              );
              //---- add
              if(! $this->quotation_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = "Insert failed";
              }
            }
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูล";
    }

    echo $sc === TRUE ? 'success' : $this->error;    
  }



  public function add_detail($code)
  {
    $sc = TRUE;
    $data = $this->input->post('data');
    $disc = $this->input->post('discountLabel');
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



  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_QUOTATION');
    $run_digit = getConfig('RUN_DIGIT_QUOTATION');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->quotation_model->get_max_code($pre);
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
    $sell_stock = $this->stock_model->get_sell_stock($item_code);
    $reserv_stock = $this->orders_model->get_reserv_stock($item_code);
    $availableStock = $sell_stock - $reserv_stock;
		return $availableStock < 0 ? 0 : $availableStock;
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
      'qu_code',
      'qu_customer_code',
      'qu_contact',
      'qu_user',
      'qu_reference',
      'qu_fromDate',
      'qu_toDate',
    );

    clear_filter($filter);
  }
}
?>
