<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cancle extends PS_Controller
{
  public $menu_code = 'ICCKCN';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ CANCLE ZONE';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/cancle';
    $this->load->model('inventory/cancle_model');
  }


  public function index()
  {
    $filter = array(
      'order_code' => get_filter('order_code', 'order_code', ''),
      'zone_code' => get_filter('zone_code', 'zone_code', ''),
      'pd_code' => get_filter('pd_code', 'pd_code'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->cancle_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$ds   = $this->cancle_model->get_data($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('inventory/cancle/cancle_view', $filter);
  }


  public function move_back($id)
  {
    $sc = TRUE;
    $this->load->model('stock/stock_model');
    $rs = $this->cancle_model->get($id);
    if(!empty($rs))
    {
      $this->db->trans_begin();
      //---- add stock back to original zone
      if(! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, $rs->qty))
      {
        $sc = FALSE;
        $this->error = 'เพิ่มสต็อกกลับโซนเดิมไม่สำเร็จ';
      }

      //--- delete cancle row
      if(! $this->cancle_model->delete($id))
      {
        $sc = FALSE;
        $this->error = 'ลบรายการไม่สำเร็จ';
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


  //--- Just delete
  public function delete($id)
  {
    $rs = $this->cancle_model->delete($id);

    echo $rs === TRUE ? 'success' : 'failed';
  }


  function clear_filter(){
    $filter = array('order_code', 'pd_code', 'zone_code', 'from_date', 'to_date');
    clear_filter($filter);
  }


} //--- end class
?>
