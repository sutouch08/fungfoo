<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sponsor extends PS_Controller
{
  public $menu_code = 'SOODSP';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'สปอนเซอร์';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/sponsor';
    $this->load->model('orders/orders_model');
    $this->load->model('orders/sponsor_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/products_model');

    $this->load->helper('order');
    $this->load->helper('customer');
    $this->load->helper('users');
    $this->load->helper('state');
    $this->load->helper('product_images');
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
      'to_date'       => get_filter('toDate', 'toDate', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

    $role     = 'P'; //--- P = sponsor;
		$segment  = 4; //-- url segment
		$rows     = $this->orders_model->count_rows($filter, $role);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->orders_model->get_data($filter, $perpage, $this->uri->segment($segment), $role);
    $ds       = array();
    if(!empty($orders))
    {
      foreach($orders as $rs)
      {
        $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
        $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
        $rs->state_name    = get_state_name($rs->state);
        $ds[] = $rs;
      }
    }

    $filter['orders'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('sponsor/sponsor_list', $filter);
  }


  public function get_sponsor_budget($customer_code)
  {
    echo $this->get_budget($customer_code);
  }


  private function get_budget($customer_code)
  {
    $current = $this->sponsor_model->get_budget($customer_code);
    $used = $this->sponsor_model->get_budget_used($customer_code);

    return ($current - $used);
  }



  public function add_new()
  {
    $this->load->view('sponsor/sponsor_add');
  }



  public function add()
  {
    if($this->input->post('customerCode'))
    {
      $book_code = getConfig('BOOK_CODE_SPONSOR');
      $date_add = db_date($this->input->post('date'));
      $code = $this->get_new_code($date_add);
      $role = 'P'; //--- P = Sponsor

      $ds = array(
        'code' => $code,
        'role' => $role,
        'bookcode' => $book_code,
        'customer_code' => $this->input->post('customerCode'),
        'user' => get_cookie('uname'),
        'remark' => $this->input->post('remark'),
        'user_ref' => $this->input->post('empName')
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
    else
    {
      set_error('ไม่พบข้อมูลลูกค้า กรุณาตรวจสอบ');
      redirect($this->home.'/add_new');
    }
  }



  public function edit_order($code)
  {
    $ds = array();
    $rs = $this->orders_model->get($code);
    if(!empty($rs))
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
      $rs->user          = $this->user_model->get_name($rs->user);
      $rs->state_name    = get_state_name($rs->state);
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
    $ds['state'] = $ost;
    $ds['order'] = $rs;
    $ds['details'] = $details;
    $ds['allowEditDisc'] = FALSE; //getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
    $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
    $ds['edit_order'] = TRUE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
    $this->load->view('sponsor/sponsor_edit', $ds);
  }



  public function update_order()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $code = $this->input->post('order_code');
      $ds = array(
        'customer_code' => $this->input->post('customer_code'),
        'date_add' => db_date($this->input->post('date_add')),
        'user_ref' => $this->input->post('user_ref'),
        'remark' => $this->input->post('remark'),
        'status' => 0
      );

      $rs = $this->orders_model->update($code, $ds);

      if($rs === FALSE)
      {
        $sc = FALSE;
        $message = 'ปรับปรุงข้อมูลไม่สำเร็จ';
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
      $details = $this->orders_model->get_order_details($code);
      $ds['order'] = $rs;
      $ds['details'] = $details;
      $ds['allowEditDisc'] = FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = FALSE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('sponsor/sponsor_edit_detail', $ds);
    }
  }



  public function save($code)
  {
    $sc = TRUE;
    $order = $this->orders_model->get($code);

    //---- check credit balance
    $amount = $this->orders_model->get_order_total_amount($code);
    //--- creadit used
    $credit_used = $this->sponsor_model->get_budget_used($order->customer_code);

    //--- credit balance from sap
    $credit_balance = $this->sponsor_model->get_budget($order->customer_code);

    if($credit_used > $credit_balance)
    {
      $diff = $credit_used - $credit_balance;
      $sc = FALSE;
      $message = 'เครดิตคงเหลือไม่พอ (ขาด : '.number($diff, 2).')';
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



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_SPONSOR');
    $run_digit = getConfig('RUN_DIGIT_SPONSOR');
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
      'toDate'
    );

    clear_filter($filter);
  }
}
?>
