<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movement extends PS_Controller
{
  public $menu_code = 'ICCKMV';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ Movement';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/movement';
    $this->load->model('inventory/movement_model');
  }


  public function index()
  {
    $filter = array(
      'reference' => get_filter('reference', 'reference', ''),
      'zone_code' => get_filter('zone_code', 'zone_code', ''),
      'warehouse_code' => get_filter('warehouse_code', 'warehouse_code', ''),
      'product_code' => get_filter('product_code', 'product_code', ''),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', '')
    );

    $arr = array();
    foreach($filter AS $key => $value)
    {
      if(!empty($value))
      {
        $arr[$key] = $value;
      }
    }

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->movement_model->count_rows($arr);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$ds   = $this->movement_model->get_data($arr, $perpage, $this->uri->segment($segment));

    $filter['data'] = $ds;
    $filter['total_row'] = $this->movement_model->get_sum_movement($arr);
		$this->pagination->initialize($init);
    $this->load->view('inventory/movement/movement_view', $filter);
  }


  function clear_filter(){
    $filter = array('reference', 'product_code', 'zone_code', 'warehouse_code','from_date', 'to_date');
    clear_filter($filter);
  }


} //--- end class
?>
