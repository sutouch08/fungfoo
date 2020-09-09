<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transform_stock extends PS_Controller
{
  public $menu_code = 'ICTRFS';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'REQUEST';
	public $title = 'เบิกแปรสภาพ(สต็อก)';
  public $filter;
  public $role = 'Q';
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/transform_stock';
    $this->load->model('inventory/transform_model');
    $this->load->model('orders/orders_model');
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
    $this->load->helper('transform');
  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'code', ''),
      'customer'  => get_filter('customer', 'customer', ''),
      'user'      => get_filter('user', 'user', ''),
      'user_ref'  => get_filter('user_ref', 'user_ref', ''),
      'from_date' => get_filter('fromDate', 'fromDate', ''),
      'to_date'   => get_filter('toDate', 'toDate', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

    $role     = 'Q'; //--- Q = แปรสภาพเพื่อสต็อก
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
    $this->load->view('transform/transform_list', $filter);
  }



  public function add_new()
  {
    $this->load->view('transform/transform_add');
  }



  public function add()
  {
    if($this->input->post('customerCode'))
    {
      $this->load->model('masters/zone_model');

      $book_code = getConfig('BOOK_CODE_TRANSFORM_STOCK');
      $date_add = db_date($this->input->post('date'));
      $code = $this->get_new_code($date_add);
      $role = 'Q'; //--- T = เบิกแปรสภาพ
      $zone_code = $this->input->post('zoneCode');
      $warehouse_code = $this->zone_model->get_warehouse_code($zone_code);

      $ds = array(
        'code' => $code,
        'role' => $role,
        'bookcode' => $book_code,
        'customer_code' => $this->input->post('customerCode'),
        'user' => get_cookie('uname'),
        'remark' => $this->input->post('remark'),
        'user_ref' => $this->input->post('empName'),
        'zone_code' => $zone_code,
        'warehouse_code' => $warehouse_code
      );

      if($this->orders_model->add($ds) === TRUE)
      {
        $this->transform_model->add($code);
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
    $this->load->helper('print');
    $this->load->helper('transform');
    $this->load->model('masters/zone_model');

    $ds = array();
    $rs = $this->orders_model->get($code);
    if(!empty($rs))
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
      $rs->user          = $this->user_model->get_name($rs->user);
      $rs->state_name    = get_state_name($rs->state);
      $rs->zone_name     = $this->zone_model->get_name($rs->zone_code);
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
    if(!empty($details))
    {
      foreach($details as $rd)
      {
        $rd->hasTransformProduct = $this->transform_model->hasTransformProduct($rd->id);
        $rd->transform_product = $this->transform_model->get_transform_product($rd->id);
        $rd->sum_transform_product_qty = $this->transform_model->get_sum_transform_product_qty($rd->id);
      }
    }

    $ds['state'] = $ost;
    $ds['order'] = $rs;
    $ds['details'] = $details;
    $this->load->view('transform/transform_edit', $ds);
  }



  public function update_order()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $this->load->model('masters/zone_model');

      $code = $this->input->post('order_code');
      $zone_code = $this->input->post('zoneCode');
      $warehouse_code = $this->zone_model->get_warehouse_code($zone_code);

      $ds = array(
        'customer_code' => $this->input->post('customer_code'),
        'date_add' => db_date($this->input->post('date_add')),
        'user_ref' => $this->input->post('user_ref'),
        'remark' => $this->input->post('remark'),
        'status' => 0,
        'zone_code' => $zone_code,
        'warehouse_code' => $warehouse_code
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
    $this->load->helper('print');
    $this->load->helper('transform');
    $this->load->helper('product_tab');
    $this->load->model('masters/zone_model');

    $ds = array();
    $rs = $this->orders_model->get($code);
    if(!empty($rs))
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
      $rs->user          = $this->user_model->get_name($rs->user);
      $rs->state_name    = get_state_name($rs->state);
      $rs->zone_name    = $this->zone_model->get_name($rs->zone_code);
    }

    $state = $this->order_state_model->get_order_state($code);
    $details = $this->orders_model->get_order_details($code);
    if(!empty($details))
    {
      foreach($details as $rd)
      {
        $rd->hasTransformProduct = $this->transform_model->hasTransformProduct($rd->id);
        $rd->transform_product = $this->transform_model->get_transform_product($rd->id);
        $rd->sum_transform_product_qty = $this->transform_model->get_sum_transform_product_qty($rd->id);
      }
    }

    $ds['order'] = $rs;
    $ds['details'] = $details;
    $this->load->view('transform/transform_edit_detail', $ds);

  }



  public function save($code)
  {
    $sc = TRUE;
    $order = $this->orders_model->get($code);

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


  public function add_transform_product()
  {
    if($this->input->post('id_order_detail'))
    {
      $this->load->model('inventory/invoice_model');

      $order_code = $this->input->post('order_code');
      $id_order_detail  = $this->input->post('id_order_detail');
      $original_product = $this->input->post('original_product');
      $transform_product = $this->input->post('transform_product');
      $qty = intval($this->input->post('qty'));

      $order = $this->orders_model->get($order_code);
      $sold = $this->invoice_model->get_total_sold_qty($order_code);
      $sold_qty = $sold > $qty ? $qty : $sold;
      $valid = $order->state == 8 ? 1 : 0;

      $arr = array(
        'order_code' => $order_code,
        'id_order_detail' => $id_order_detail,
        'original_code' => $original_product,
        'product_code' => $transform_product,
        'order_qty' => $qty,
        'sold_qty' => $sold_qty,
        'valid' => $valid
        );

      $rs = $this->transform_model->update($arr);

      if( $rs === TRUE)
      {
        $rd = $this->transform_model->get_transform_product($id_order_detail);
        $ra  = getTransformProducts($rd);
        $ra .= '<input type="hidden" id="transform-qty-'.$id_order_detail.'" value="'.$this->transform_model->get_sum_transform_product_qty($id_order_detail).'" />';

        $sc = json_encode(array('data' => $ra));
      }
      else
      {
        $sc = 'ทำรายการไม่สำเร็จ';
      }
    }
    else
    {
      $sc = 'ไม่พบข้อมูลสินค้า';
    }
    echo $sc;
  }




  public function remove_transform_product()
  {
    if($this->input->post('id_order_detail'))
    {
      $id_order_detail = $this->input->post('id_order_detail');
      $product_code = $this->input->post('product_code');

      $rs = $this->transform_model->remove_transform_product($id_order_detail, $product_code);
      if($rs === TRUE)
      {
        $rd = $this->transform_model->get_transform_product($id_order_detail);
        $ra  = getTransformProducts($rd);
        $ra .= '<input type="hidden" id="transform-qty-'.$id_order_detail.'" value="'.$this->transform_model->get_sum_transform_product_qty($id_order_detail).'" />';

        $sc = json_encode(array('data' => $ra));
      }
      else
      {
        $sc = 'ทำรายการไม่สำเร็จ';
      }
    }
    else
    {
      $sc = 'ไม่พบข้อมูลสินค้า';
    }

    echo $sc;
  }



  public function remove_transform_detail()
  {
    $id_order_detail = $this->input->post('id_order_detail');
    if($this->transform_model->remove_transform_detail($id_order_detail) === TRUE)
    {
      echo 'success';
    }
    else
    {
      echo 'ลบการเชื่อมโยงสินค้าไม่สำเร็จ';
    }
  }



  public function is_exists_connected()
  {
    $id_order_detail = $this->input->get('id_order_detail');
    if($this->transform_model->hasTransformProduct($id_order_detail) === TRUE)
    {
      echo 'exists';
    }
    else
    {
      echo 'not_exists';
    }
  }





  public function get_detail_table($code)
  {
    $this->load->helper('print');
    $this->load->helper('transform');
    $this->load->helper('product_tab');

    $sc = "no data found";
  	$order = $this->orders_model->get($code);

    $details = $this->orders_model->get_order_details($code);
    if(!empty($details))
    {
      $no = 1;
      $total_qty = 0;
      $ds = array();

      foreach($details as $rs)
      {
        $hasTransformProduct = $this->transform_model->hasTransformProduct($rs->id);
        $transform_product = $this->transform_model->get_transform_product($rs->id);
        $checked = $hasTransformProduct === FALSE ? 'checked' : '';
        $arr = array(
          'id' => $rs->id,
          'no' => $no,
          'imageLink' => get_product_image($rs->product_code, 'mini'),
          'productCode' => $rs->product_code,
          'productName' => $rs->product_name,
          'qty' => number($rs->qty),
          'transProduct' => $transform_product === FALSE ? '' : getTransformProducts($transform_product, $order->state, $order->is_expired),
          'trans_qty' => $this->transform_model->get_sum_transform_product_qty($rs->id),
          'checkbox' => $checked,
          'button' => $hasTransformProduct === FALSE ? '' : 'show'
        );

        array_push($ds, $arr);
        $total_qty += $rs->qty;
        $no++;
      }

      $arr = array('total_qty' => number($total_qty));
      array_push($ds, $arr);

      $sc = json_encode($ds);
    }

    echo $sc;
  }

  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_TRANSFORM_STOCK');
    $run_digit = getConfig('RUN_DIGIT_TRANSFORM_STOCK');
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
      'user_ref',
      'fromDate',
      'toDate'
    );

    clear_filter($filter);
  }
}
?>
