<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Discount_policy extends PS_Controller
{
  public $menu_code = 'SCPOLI';
	public $menu_group_code = 'SC';
	public $title = 'เพิ่ม/แก้ไข นโยบายส่วนลด';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'discount/discount_policy';
    $this->load->model('discount/discount_policy_model');
    $this->load->model('discount/discount_rule_model');
  }


  public function index()
  {
		$code = get_filter('policy_code', 'policy_code', '');
    $name = get_filter('policy_name', 'policy_name', '');
    $active = get_filter('active', 'active', 2); //-- 0 = not active , 1 = active , 2 = all
    $start_date = get_filter('start_date', 'start_date', '');
    $end_date = get_filter('end_date', 'end_date', '');

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->discount_policy_model->count_rows($code, $name, $active, $start_date, $end_date);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->discount_policy_model->get_data($code, $name, $active, $start_date, $end_date, $perpage, $this->uri->segment($segment));

    $ds = array(
      'code' => $code,
      'name' => $name,
      'active' => $active,
      'start_date' => $start_date,
      'end_date' => $end_date,
			'data' => $result
    );

		$this->pagination->initialize($init);

    $this->load->view('discount/policy/policy_view', $ds);
  }




  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('discount/policy/policy_add_view');
    }
    else
    {
      redirect($this->home);
    }
  }



  public function add_policy()
  {
    if($this->input->post('policy_name'))
    {
      $code = $this->get_new_code();
      $arr = array(
        'code' => $code,
        'name' => $this->input->post('policy_name'),
        'start_date' => db_date($this->input->post('start_date')),
        'end_date' => db_date($this->input->post('end_date')),
        'user' => get_cookie('uname')
      );

      if($this->discount_policy_model->add($arr))
      {
        redirect($this->home.'/edit_policy/'. $code);
      }
      else
      {
        set_error('เพิ่มข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        redirect($this->home.'/add_new');
      }
    }
    else
    {
      set_error('เพิ่มข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
      redirect($this->home.'/add_new');
    }

  }


  public function edit_policy($code)
  {
    $this->load->helper('discount_rule');
    $rs = $this->discount_policy_model->get_policy_by_code($code);
    $data['policy'] = $rs;
    $data['rules']  = $this->discount_rule_model->get_policy_rules($rs->id);
    $this->load->view('discount/policy/policy_edit_view', $data);
  }



  public function update_policy()
  {
    $id = $this->input->post('id');
    $code = $this->input->post('policy_code');
    $ds = array(
      'name' => $this->input->post('policy_name'),
      'start_date' => db_date($this->input->post('start_date')),
      'end_date' => db_date($this->input->post('end_date')),
      'active' => $this->input->post('active'),
      'update_user' => get_cookie('uname')
    );

    $rs = $this->discount_policy_model->update($id, $ds);
    if($rs === TRUE)
    {
      set_message('Updated');
      redirect($this->home.'/edit_policy/'.$code);
    }
    else
    {
      set_error('ปรับปรุงข้อมูลไม่สำเร็จ');
      redirect($this->home.'/edit_policy/'.$code);
    }
  }



  public function get_active_rule()
  {
  	$rules = $this->discount_rule_model->get_active_rule();
  	$ds = array();
    if(!empty($rules))
    {
      foreach($rules as $rs)
      {
        $arr = array(
          'id_rule' => $rs->id,
          'ruleCode' => $rs->code,
          'ruleName' => $rs->name,
          'date_upd' => thai_date($rs->date_upd)
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      $arr = array('nodata' => 'nodata');
      array_push($ds, $arr);
    }

    echo json_encode($ds);
  }




  public function delete_policy($id)
  {
    $rs = $this->discount_policy_model->delete($id);

    echo $rs->status === TRUE ? 'success' : $rs->message;
  }



  public function get_new_code()
  {
    $date = date('Y-m-d');
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_POLICY');
    $run_digit = getConfig('RUN_DIGIT_POLICY');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->discount_policy_model->get_max_code($pre);
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


  public function clear_filter()
  {
    $filter = array('policy_code', 'policy_name', 'active', 'start_date', 'end_date');
    clear_filter($filter);
    echo 'done';
  }

}//-- end class
?>
