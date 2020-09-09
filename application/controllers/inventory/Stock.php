<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends PS_Controller
{
  public $menu_code = 'ICCKST';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบสต็อก';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/stock';
    $this->load->model('stock/stock_model');
  }


  public function index()
  {
    $filter = array(
      'pd_code'          => get_filter('pd_code', 'pd_code', ''),
      'zone_code'      => get_filter('zone_code', 'zone_code', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->stock_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$ds   = $this->stock_model->get_data($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('inventory/stock/stock_view', $filter);
  }


  function clear_filter(){
    $filter = array('pd_code', 'zone_code');
    clear_filter($filter);
  }


} //--- end class
?>
