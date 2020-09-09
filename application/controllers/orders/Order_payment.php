<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_payment extends PS_Controller
{
  public $menu_code = 'ACPMCF';
	public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
	public $title = 'ตรวจสอบยอดชำระเงิน';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/order_payment';
    $this->load->model('orders/order_payment_model');
    $this->load->model('masters/bank_model');
    $this->load->helper('bank');
    $this->load->helper('order');
  }



  public function index()
  {
    $this->load->model('orders/orders_model');
    $filter = array(
      'code'  => get_filter('code', 'code', ''),
      'customer' => get_filter('customer', 'customer', ''),
      'account' => get_filter('account', 'account', ''),
      'user'  => get_filter('user', 'user', ''),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date'  => get_filter('to_date', 'to_date', ''),
      'valid' => get_filter('valid', 'valid', 0)
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->order_payment_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->order_payment_model->get_data($filter, $perpage, $this->uri->segment($segment));
    if(!empty($orders))
    {
      foreach($orders as $rs)
      {
        $order = $this->orders_model->get($rs->order_code);
        $rs->state = $order->state;
      }
    }

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('orders/payment/order_payment_list', $filter);
  }




  public function get_payment_detail()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $detail = $this->order_payment_model->get_detail($id);
    if(!empty($detail))
    {
      $img = payment_image_url($detail->img);
      $bank   = $this->bank_model->get_account_detail($detail->id_account);
      $ds  = array(
        'id' => $detail->id,
        'orderAmount' => number($detail->order_amount,2),
        'payAmount' => number($detail->pay_amount,2),
        'payDate' => thai_date($detail->pay_date, TRUE, '/'),
        'bankName' => $bank->bank_name,
        'branch' => $bank->branch,
        'accNo' => $bank->acc_no,
        'accName' => $bank->acc_name,
        'date_add' => thai_date($detail->date_upd, TRUE, '/'),
        'imageUrl' => $img
      );

      if($detail->valid == 0)
      {
        $ds['valid'] = 'no';
      }
    }
    else
    {
      $sc = FALSE;
    }

    echo $sc === TRUE ? json_encode($ds) : 'fail';
  }




  public function confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('id'))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/order_state_model');
      $this->load->model('account/payment_receive_model');
      $id = $this->input->post('id');
      $detail = $this->order_payment_model->get_detail($id);
      $order = $this->orders_model->get($detail->order_code);
      $arr = array(
        'order_code' => $detail->order_code,
        'state' => 3,
        'update_user' => get_cookie('uname')
      );

      //--- start transection
      $this->db->trans_begin();

      //--- mark payment as paid
      if(! $this->order_payment_model->valid_payment($id) )
      {
        $sc = FALSE;
        $this->error = 'เปลี่ยนสภานะรายการไม่สำเร็จ';
      }

      //--- เพิ่มรายการเช้า payment_receive
      $payment = array(
        'reference' => $order->code,
        'customer_code' => $order->customer_code,
        'pay_date' => $detail->pay_date,
        'amount' => $detail->pay_amount,
        'payment_type' => 'TR',
        'valid' => 1
      );

      if(! $this->payment_receive_model->add($payment) )
      {
        $sc = FALSE;
        $this->error = 'เพิ่มรายการเงินเข้าไม่สำเร็จ';
      }

      $this->orders_model->update_deposit($detail->order_code, $detail->pay_amount);

      update_order_total_amount($detail->order_code);

      if($this->orders_model->get_order_balance($detail->order_code) <= 0)
      {
        //--- mark order as paid
        if(! $this->orders_model->paid($detail->order_code, TRUE) )
        {
          $sc = FALSE;
          $this->error = 'เปลี่ยนสถานะออเดอร์เป็นชำระแล้วไม่สำเร็จ';
        }
      }

      $order = $this->orders_model->get($detail->order_code);

      if($order->state < 3)
      {
        //--- change state to waiting for prepare
        $this->orders_model->change_state($detail->order_code, 3);

        //--- add state event
        $this->order_state_model->add_state($arr);
      }

      //--- complete transecrtion with commit or rollback if any error

      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบรายการชำระเงิน';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function un_confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('id'))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/order_state_model');
      $this->load->model('account/payment_receive_model');
      $id = $this->input->post('id');
      $detail = $this->order_payment_model->get_detail($id);
      $order = $this->orders_model->get($detail->order_code);
      if($order->state < 8)
      {
        $arr = array(
          'order_code' => $detail->order_code,
          'state' => 2,
          'update_user' => get_cookie('uname')
        );

        //--- start transection
        $this->db->trans_begin();

        //--- mark payment as unpaid
        if( ! $this->order_payment_model->un_valid_payment($id) )
        {
          $sc = FALSE;
          $this->error = 'เปลี่ยนสถานะรายการไม่สำเร็จ';
        }


        //--- เพิ่มรายการเช้า payment_receive
        $payment = array(
          'reference' => $order->code,
          'customer_code' => $order->customer_code,
          'pay_date' => now(),
          'amount' => (-1) * $detail->pay_amount,
          'payment_type' => 'TR',
          'valid' => 1
        );

        if(! $this->payment_receive_model->add($payment) )
        {
          $sc = FALSE;
          $this->error = 'เพิ่มรายการเงินเข้าไม่สำเร็จ';
        }

        if($detail->is_deposit == 1 && $order->deposit >= $detail->pay_amount)
        {
          $this->orders_model->update_deposit($detail->order_code, (-1) * $detail->pay_amount);
        }
        //--
        update_order_total_amount($detail->order_code);

        //--- mark order as unpaid
        if( ! $this->orders_model->paid($detail->order_code, FALSE) )
        {
          $sc = FALSE;
          $this->error = 'เปลี่ยนสถานะการชำระเงินไม่สำเร็จ';
        }

        // //--- change state to waiting for payment
        // if(! $this->orders_model->change_state($detail->order_code, 2) )
        // {
        //   $sc = FALSE;
        //   $this->error = 'เปลี่ยนสถานะออเดอร์กลับเป็นรอชำระเงินไม่สำเร็จ';
        // }

        //--- add state event
        $this->order_state_model->add_state($arr);

        //--- complete transecrtion with commit or rollback if any error
        if($sc === TRUE)
        {
          $this->db->trans_commit();
        }
        else
        {
          $this->db->trans_rollback();
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'ไม่อนุญาติให้ยกเลิกการชำระเงินในสถานะนี้';
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบรายการชำระเงิน';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function remove_payment()
  {
    $sc = TRUE;
    if($this->input->post('id'))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/order_state_model');
      $id = $this->input->post('id');
      $detail = $this->order_payment_model->get_detail($id);
      $order = $this->orders_model->get($detail->order_code);
      if($order->state >= 8)
      {
        $sc = FALSE;
        $this->error = 'ไม่อนุญาติให้ลบการชำระเงินในสถานะออเดอร์นี้';
      }
      else
      {
        if(! empty($detail) && $detail->valid == 0)
        {
          //--- start transection
          $this->db->trans_begin();

          //--- mark order as unpaid
          if(! $this->orders_model->paid($detail->order_code, FALSE))
          {
            $sc = FALSE;
            $this->error = 'ย้อนสถานะการชำระเงินไม่สำเร็จ';
          }

          // //--- change deposit
          // if(! $this->orders_model->update_deposit($detail->order_code, ($detail->pay_amount * -1)) )
          // {
          //   $sc = FALSE;
          //   $this->error = 'ปรับปรุงยอดเงินมัดจำไม่สำเร็จ';
          // }

          //---
          update_order_total_amount($detail->order_code);

          //--- change state to pending
          // if( ! $this->orders_model->change_state($detail->order_code, 1) )
          // {
          //   $sc = FALSE;
          //   $this->error = 'ย้อนสถานะออเดอร์ไม่สำเร็จ';
          // }

          //--- add state event
          $arr = array(
            'order_code' => $detail->order_code,
            'state' => 1,
            'update_user' => get_cookie('uname')
          );

          $this->order_state_model->add_state($arr);

          //--- now remove payment row
          $this->order_payment_model->delete($id);

          //--- end transection commit if all success or rollback if any error
          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }

          if($sc === TRUE)
          {
            $file = $this->config->item('image_file_path').'payments/'.$detail->img.'.jpg';
            if(file_exists($file))
            {
              unlink($file);
            }

          }
        }
        else
        {
          $sc = FALSE;
          $this->error = 'ไม่พบรายการชำระเงิน';
        }
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบตัวแปร id กรุณา reload หน้าเว็บแล้วลองใหม่';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_filter()
  {
    $filter = array('code', 'account', 'user', 'from_date', 'to_date', 'customer', 'valid');
    clear_filter($filter);
  }
} //--- end class

?>
