<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_receive extends PS_Controller
{
  public $menu_code = 'ACPMRC';
	public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
	public $title = 'ตรวจสอบรายการรับเงิน';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'account/payment_receive';
    $this->load->model('account/payment_receive_model');
    $this->load->model('masters/customers_model');
    $this->load->helper('order_repay');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'receive_code', ''),
      'customer' => get_filter('customer', 'receive_customer', ''),
      'from_date' => get_filter('from_date', 'receive_from_date', ''),
      'to_date' => get_filter('to_date', 'receive_to_date', ''),
      'pay_type' => get_filter('pay_type', 'receive_type', '')
    );

    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->payment_receive_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs = $this->payment_receive_model->get_list($filter, $perpage, $this->uri->segment($segment));
    
    $filter['docs'] = $docs;

		$this->pagination->initialize($init);
    $this->load->view('account/payment_receive/receive_list', $filter);
  }



  public function clear_filter()
  {
    $filter = array('receive_code', 'receive_customer', 'receive_from_date', 'receive_to_date', 'receive_status', 'receive_type');
    clear_filter($filter);
  }


} //---- end class
 ?>
