<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_credit extends PS_Controller
{
  public $menu_code = 'ACODCR';
	public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
	public $title = 'ตรวจสอบลูกหนี้ค้างรับ';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'account/order_credit';
    $this->load->model('account/order_credit_model');
    $this->load->model('masters/customers_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'credit_code', ''),
      'customer' => get_filter('customer', 'credit_customer', ''),
      'from_date' => get_filter('from_date', 'credit_from_date', ''),
      'to_date' => get_filter('to_date', 'credit_to_date', ''),
      'due_from_date' => get_filter('due_from_date', 'due_from_date', ''),
      'due_to_date' => get_filter('due_to_date', 'due_to_date', ''),
      'valid' => get_filter('valid', 'credit_valid', '0')
    );

    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->order_credit_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs = $this->order_credit_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['docs'] = $docs;

		$this->pagination->initialize($init);
    $this->load->view('account/order_credit/credit_list', $filter);
  }



  public function clear_filter()
  {
    $filter = array('credit_code', 'credit_customer', 'credit_from_date', 'credit_to_date', 'credit_valid', 'due_from_date', 'due_to_date');
    clear_filter($filter);
  }


} //---- end class
 ?>
