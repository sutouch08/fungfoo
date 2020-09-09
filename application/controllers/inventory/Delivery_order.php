
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_order extends PS_Controller
{
  public $menu_code = 'ICODDO';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'รายการรอเปิดบิล';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/delivery_order';
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
  }


  public function index()
  {
    $this->load->model('masters/customers_model');
    $this->load->helper('channels');
    $this->load->helper('payment_method');
    $this->load->helper('order');
    $filter = array(
      'code'          => get_filter('code', 'code', ''),
      'customer'      => get_filter('customer', 'customer', ''),
      'payment'       => get_filter('payment', 'payment', ''),
      'role'          => get_filter('role', 'role', ''),
      'channels'      => get_filter('channels', 'channels', ''),
      'from_date'     => get_filter('from_date', 'from_date', ''),
      'to_date'       => get_filter('to_date', 'to_date', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->delivery_order_model->count_rows($filter, 7);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->delivery_order_model->get_data($filter, $perpage, $this->uri->segment($segment), 7);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('inventory/delivery_order/delivery_list', $filter);
  }


  public function confirm_order()
  {
    $sc = TRUE;
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('masters/customers_model');
    $this->load->model('stock/stock_model');
    $this->load->helper('discount');
    $code = $this->input->post('order_code');
    $use_qc = getConfig('USE_QC') == 1 ? TRUE : FALSE;
    if($code)
    {
      $order = $this->orders_model->get($code);
      if($order->role == 'T')
      {
        $this->load->model('inventory/transform_model');
      }

      //--- กรณียืมสินค้า
      if($order->role == 'L')
      {
        $this->load->model('inventory/lend_model');
      }

      //---- กรณีฝากขาย (โอนคลัง)
      if($order->role == 'N')
      {
        $this->load->model('orders/consign_model');
      }

      if($order->state == 7)
      {
        $this->db->trans_begin();

        //--- change state
       $this->orders_model->change_state($code, 8);

        //--- add state event
        $arr = array(
          'order_code' => $code,
          'state' => 8,
          'update_user' => get_cookie('uname')
        );

        $this->order_state_model->add_state($arr);

        //---- รายการทีรอการเปิดบิล
        $bill = $this->delivery_order_model->get_bill_detail($code, $use_qc);

        if(!empty($bill))
        {
          foreach($bill as $rs)
          {
            //--- ถ้ามีรายการที่ไมสำเร็จ ออกจาก loop ทันที
            if($sc === FALSE)
            {
              break;
            }


            //--- ถ้ายอดตรวจ น้อยกว่า หรือ เท่ากับ ยอดสั่ง ใช้ยอดตรวจในการตัด buffer
            //--- ถ้ายอดตวจ มากกว่า ยอดสั่ง ให้ใช้ยอดสั่งในการตัด buffer (บางทีอาจมีการแก้ไขออเดอร์หลังจากมีการตรวจสินค้าแล้ว)
            if($use_qc)
            {
              $sell_qty = ($rs->order_qty >= $rs->qc) ? $rs->qc : $rs->order_qty;
            }
            else
            {
              $sell_qty = ($rs->order_qty >= $rs->prepared) ? $rs->prepared : $rs->order_qty;
            }


            //--- ดึงข้อมูลสินค้าที่จัดไปแล้วตามสินค้า
            $buffers = $this->buffer_model->get_details($code, $rs->product_code);
            if(!empty($buffers))
            {
              $no = 0;
              foreach($buffers as $rm)
              {
                if($sell_qty > 0)
                {
                //--- ถ้ายอดใน buffer น้อยกว่าหรือเท่ากับยอดสั่งซื้อ (แยกแต่ละโซน น้อยกว่าหรือเท่ากับยอดสั่ง (ซึ่งควรเป็นแบบนี้))
                  $buffer_qty = $rm->qty <= $sell_qty ? $rm->qty : $sell_qty;

                  //--- ทำยอดให้เป็นลบเพื่อตัดยอดออก เพราะใน function  ใช้การบวก
                  $qty = $buffer_qty * (-1);

                  //--- 1. ตัดยอดออกจาก buffer
                  //--- นำจำนวนติดลบบวกกลับเข้าไปใน buffer เพื่อตัดยอดให้น้อยลง

                  if($this->buffer_model->update($rm->order_code, $rm->product_code, $rm->zone_code, $qty) !== TRUE)
                  {
                    $sc = FALSE;
                    $message = 'ปรับยอดใน buffer ไม่สำเร็จ';
                    break;
                  }

                  //--- ลดยอด sell qty ลงตามยอด buffer ทีลดลงไป
                  $sell_qty += $qty;

                  //--- 2. update movement
                  $arr = array(
                    'reference' => $order->code,
                    'warehouse_code' => $rm->warehouse_code,
                    'zone_code' => $rm->zone_code,
                    'product_code' => $rm->product_code,
                    'move_in' => 0,
                    'move_out' => $buffer_qty,
                    'date_add' => $order->date_add
                  );

                  if($this->movement_model->add($arr) === FALSE)
                  {
                    $sc = FALSE;
                    $message = 'บันทึก movement ขาออกไม่สำเร็จ';
                    break;
                  }


                  //--- กรณีฝากขาย
                  if($order->role === 'N' OR $order->role === 'C')
                  {
                    //--- 1. เพิ่มสต็อกเข้าโซนปลายทาง
                    if($this->stock_model->update_stock_zone($order->zone_code, $rm->product_code, $buffer_qty) !== TRUE)
                    {
                      $sc = FALSE;
                      $message = 'โอนสินค้าเข้าโซนปลายทางไม่สำเร็จ';
                    }

                    //--- 2. เพิ่ม movement เข้าปลายทาง
                    $arr = array(
                      'reference' => $order->code,
                      'warehouse_code' => $order->warehouse_code,
                      'zone_code' => $order->zone_code,
                      'product_code' => $rm->product_code,
                      'move_in' => $buffer_qty,
                      'move_out' => 0,
                      'date_add' => $order->date_add
                    );

                    if($this->movement_model->add($arr) === FALSE)
                    {
                      $sc = FALSE;
                      $message = 'บันทึก movement ขาเข้าไม่สำเร็จ';
                      break;
                    }
                  }

                  $total_amount = $rs->final_price * $buffer_qty;
                  //--- 4. update credit used
                  if($sc === TRUE && $order->role == 'S' && $order->is_term == 1)
                  {
                    $credit_balance = $this->customers_model->get_credit_balance($order->customer_code);

                    if($credit_balance < $total_amount)
                    {
                      $sc = FALSE;
                      $message = 'เครดิตคงเหลือไม่เพียงพอ';
                    }

                    if($sc === TRUE && $this->customers_model->update_used($order->customer_code, ($rs->final_price * $buffer_qty)))
                    {
                      $this->customers_model->update_balance($order->customer_code);
                    }
                  }

                  //--- ข้อมูลสำหรับบันทึกยอดขาย
                  $arr = array(
                          'reference' => $order->code,
                          'role'   => $order->role,
                          'payment_code'   => $order->payment_code,
                          'channels_code'  => $order->channels_code,
                          'product_code'  => $rs->product_code,
                          'product_name'  => $rs->product_name,
                          'product_style' => $rs->style_code,
                          'cost'  => $rs->cost,
                          'price'  => $rs->price,
                          'sell'  => $rs->final_price,
                          'qty'   => $buffer_qty,
                          'discount_label'  => discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
                          'discount_amount' => ($rs->discount_amount * $buffer_qty),
                          'total_amount'   => $rs->final_price * $buffer_qty,
                          'total_cost'   => $rs->cost * $buffer_qty,
                          'margin'  =>  ($rs->final_price * $buffer_qty) - ($rs->cost * $buffer_qty),
                          'id_policy'   => $rs->id_policy,
                          'id_rule'     => $rs->id_rule,
                          'customer_code' => $order->customer_code,
                          'customer_ref' => $order->customer_ref,
                          'sale_code'   => $order->sale_code,
                          'user' => $order->user,
                          'date_add'  => $order->date_add,
                          'zone_code' => $rm->zone_code,
                          'warehouse_code'  => $rm->warehouse_code,
                          'update_user' => get_cookie('uname'),
                          'budget_code' => $order->budget_code
                  );

                  //--- 3. บันทึกยอดขาย
                  if($this->delivery_order_model->sold($arr) !== TRUE)
                  {
                    $sc = FALSE;
                    $message = 'บันทึกขายไม่สำเร็จ';
                    break;
                  }
                } //--- end if sell_qty > 0
              } //--- end foreach $buffers
            } //--- end if wmpty ($buffers)


            //------ ส่วนนี้สำหรับโอนเข้าคลังระหว่างทำ
            //------ หากเป็นออเดอร์เบิกแปรสภาพ
            if($order->role == 'T')
            {
              //--- ตัวเลขที่มีการเปิดบิล
              if($use_qc)
              {
                $sold_qty = ($rs->order_qty >= $rs->qc) ? $rs->qc : $rs->order_qty;
              }
              else
              {
                $sold_qty = ($rs->order_qty >= $rs->prepared) ? $rs->prepared : $rs->order_qty;
              }

              //--- ยอดสินค้าที่มีการเชื่อมโยงไว้ในตาราง tbl_order_transform_detail (เอาไว้โอนเข้าคลังระหว่างทำ รอรับเข้า)
              //--- ถ้ามีการเชื่อมโยงไว้ ยอดต้องมากกว่า 0 ถ้ายอดเป็น 0 แสดงว่าไม่ได้เชื่อมโยงไว้
              $trans_list = $this->transform_model->get_transform_product($rs->id);

              if(!empty($trans_list))
              {
                //--- ถ้าไม่มีการเชื่อมโยงไว้
                foreach($trans_list as $ts)
                {
                  //--- ถ้าจำนวนที่เชื่อมโยงไว้ น้อยกว่า หรือ เท่ากับ จำนวนที่ตรวจได้ (ไม่เกินที่สั่งไป)
                  //--- แสดงว่าได้ของครบตามที่ผูกไว้ ให้ใช้ตัวเลขที่ผูกไว้ได้เลย
                  //--- แต่ถ้าได้จำนวนที่ผูกไว้มากกว่าที่ตรวจได้ แสดงว่า ได้สินค้าไม่ครบ ให้ใช้จำนวนที่ตรวจได้แทน
                  $move_qty = $ts->order_qty <= $sold_qty ? $ts->order_qty : $sold_qty;

                  if( $move_qty > 0)
                  {
                    //--- update ยอดเปิดบิลใน tbl_order_transform_detail field sold_qty
                    if($this->transform_model->update_sold_qty($ts->id, $move_qty) === TRUE )
                    {
                      $sold_qty -= $move_qty;
                    }
                    else
                    {
                      $sc = FALSE;
                      $message = 'ปรับปรุงยอดรายการค้างรับไม่สำเร็จ';
                    }
                  }
                }
              }
            }


            //--- if lend
            if($order->role == 'L')
            {
              //--- ตัวเลขที่มีการเปิดบิล
              if($use_qc)
              {
                $sold_qty = ($rs->order_qty >= $rs->qc) ? $rs->qc : $rs->order_qty;
              }
              else
              {
                $sold_qty = ($rs->order_qty >= $rs->prepared) ? $rs->prepared : $rs->order_qty;
              }


              $arr = array(
                'order_code' => $code,
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'qty' => $sold_qty,
                'customer_code' => $order->customer_code
              );

              if($this->lend_model->add_detail($arr) === FALSE)
              {
                $sc = FALSE;
                $message = 'เพิ่มรายการค้างรับไม่สำเร็จ';
              }
            }

          } //--- end foreach $bill
        } //--- end if empty($bill)




        //--- เคลียร์ยอดค้างที่จัดเกินมาไปที่ cancle หรือ เคลียร์ยอดที่เป็น 0
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

              if($this->cancle_model->add($arr) === FALSE)
              {
                $sc = FALSE;
                $message = 'เคลียร์ยอดค้างเข้า cancle ไม่สำเร็จ';
                break;
              }
            }

            if($this->buffer_model->delete($rs->id) === FALSE)
            {
              $sc = FALSE;
              $message = 'ลบ Buffer ที่ค้างอยู่ไม่สำเร็จ';
              break;
            }
          }
        }


        //--- บันทึกขายรายการที่ไม่นับสต็อก
        $bill = $this->delivery_order_model->get_non_count_bill_detail($order->code);
        if(!empty($bill))
        {
          foreach($bill as $rs)
          {

            $total_amount = $rs->final_price * $rs->qty;
            //--- 4. update credit used
            if($sc === TRUE && $order->role == 'S' && $order->is_term == 1)
            {
              $credit_balance = $this->customers_model->get_credit_balance($order->customer_code);

              if($credit_balance < $total_amount)
              {
                $sc = FALSE;
                $message = 'เครดิตคงเหลือไม่เพียงพอ';
              }

              if($sc === TRUE && $this->customers_model->update_used($order->customer_code, ($rs->final_price * $buffer_qty)))
              {
                $this->customers_model->update_balance($order->customer_code);
              }
            }

            //--- ข้อมูลสำหรับบันทึกยอดขาย
            $arr = array(
                    'reference' => $order->code,
                    'role'   => $order->role,
                    'payment_code'   => $order->payment_code,
                    'channels_code'  => $order->channels_code,
                    'product_code'  => $rs->product_code,
                    'product_name'  => $rs->product_name,
                    'product_style' => $rs->style_code,
                    'cost'  => $rs->cost,
                    'price'  => $rs->price,
                    'sell'  => $rs->final_price,
                    'qty'   => $rs->qty,
                    'discount_label'  => discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
                    'discount_amount' => ($rs->discount_amount * $rs->qty),
                    'total_amount'   => $rs->final_price * $rs->qty,
                    'total_cost'   => $rs->cost * $rs->qty,
                    'margin'  => ($rs->final_price * $rs->qty) - ($rs->cost * $rs->qty),
                    'id_policy'   => $rs->id_policy,
                    'id_rule'     => $rs->id_rule,
                    'customer_code' => $order->customer_code,
                    'customer_ref' => $order->customer_ref,
                    'sale_code'   => $order->sale_code,
                    'user' => $order->user,
                    'date_add'  => $order->date_add,
                    'zone_code' => NULL,
                    'warehouse_code'  => NULL,
                    'update_user' => get_cookie('uname'),
                    'budget_code' => $order->budget_code,
                    'is_count' => 0
            );

            //--- 3. บันทึกยอดขาย
            if($this->delivery_order_model->sold($arr) !== TRUE)
            {
              $sc = FALSE;
              $message = 'บันทึกขายไม่สำเร็จ';
              break;
            }
          }

        }

        if($sc === TRUE)
        {
          //--- set is_complete
          $this->orders_model->set_completed($code);

          //--- ถ้าเป็นออเดอร์แบบขาย และ เป็นเครดิต ให้ตั้งหนี้
          if($order->role === 'S' && $order->is_term == 1)
          {
            $this->load->model('account/order_credit_model');

            $sold_amount = $this->invoice_model->get_total_sold_amount($code);
            $customer = $this->customers_model->get($order->customer_code);
            $arr = array(
              'order_code' => $code,
              'customer_code' => $order->customer_code,
              'delivery_date' => date('Y-m-d'),
              'due_date' => added_date(date('Y-m-d'), $customer->credit_term),
              'over_due_date' => added_date(date('Y-m-d'), $customer->credit_term + getConfig('OVER_DUE_DATE')),
              'amount' => $sold_amount
            );

            if($this->order_credit_model->is_exists($code))
            {
              $this->order_credit_model->update($code, $arr);
            }
            else
            {
              $this->order_credit_model->add($arr);
            }
            //--- recal balance
            $this->order_credit_model->recal_balance($code);
          }
        }

        if($sc === TRUE)
        {
          $this->db->trans_commit();
        }
        else
        {
          $this->db->trans_rollback();
        }

      } //--- end if state == 7

    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function view_detail($code)
  {
    $this->load->model('masters/customers_model');
    $this->load->model('inventory/qc_model');
    $this->load->helper('order');
    $this->load->helper('discount');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/sender_model');
    $use_qc = getConfig('USE_QC') == 1 ? TRUE : FALSE;
    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
    if($order->role == 'C' OR $order->role == 'N')
    {
      $this->load->model('masters/zone_model');
      $order->zone_name = $this->zone_model->get_name($order->zone_code);
    }

    $details = $this->delivery_order_model->get_billed_detail($code);

    $box_list = $use_qc ? $this->qc_model->get_box_list($code) : FALSE;

    $order->channels_name = $this->channels_model->get_name($order->channels_code);
    $order->payment_name = $this->payment_methods_model->get_name($order->payment_code);
    $order->payment_role = $this->payment_methods_model->get_role($order->payment_code);
    $order->sender_name = $this->sender_model->get_name($order->sender_id);

    $ds['order'] = $order;
    $ds['details'] = $details;
    $ds['box_list'] = $box_list;
    $ds['use_qc'] = $use_qc;
    $this->load->view('inventory/delivery_order/bill_detail', $ds);
  }



  public function get_state()
  {
    $code = $this->input->get('order_code');
    $state = $this->orders_model->get_state($code);
    echo $state;
  }


  public function clear_filter()
  {
    $filter = array('code','customer','user','role','channels','from_date','to_date');
    clear_filter($filter);
  }

} //--- end class
?>
