<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_methods extends PS_Controller
{
  public $menu_code = 'DBPAYM';
	public $menu_group_code = 'DB';
	public $title = 'Payment channels';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/payment_methods';
    $this->load->model('masters/payment_methods_model');
  }


  public function index()
  {
		$code = get_filter('code', 'code', '');
		$name = get_filter('name', 'name', '');
    $term = get_filter('term', 'term', 0);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->payment_methods_model->count_rows($code, $name, $term);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$rs = $this->payment_methods_model->get_data($code, $name, $term, $perpage, $this->uri->segment($segment));
    $ds = array(
      'code' => $code,
      'name' => $name,
      'term' => $term,
			'data' => $rs
    );

		$this->pagination->initialize($init);
    $this->load->view('masters/payment_methods/payment_methods_view', $ds);
  }


  public function add_new()
  {
    $this->load->helper('payment_method');
    $data['code'] = $this->session->flashdata('code');
    $data['name'] = $this->session->flashdata('name');
    $data['role'] = $this->session->flashdata('role');
    $this->load->view('masters/payment_methods/payment_methods_add_view', $data);
  }


  public function add()
  {
    if($this->input->post('code'))
    {
      $sc = TRUE;
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $term = $this->input->post('term');
      $ds = array(
        'code' => $code,
        'name' => $name,
        'has_term' => $term === NULL ? 0 : $term
      );

      if($this->payment_methods_model->is_exists($code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' already exists");
      }

      if($this->payment_methods_model->is_exists_name($name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' already exists");
      }

      if($sc === TRUE && $this->payment_methods_model->add($ds))
      {
        set_message('payment_methods created');
      }
      else
      {
        $sc = FALSE;
        set_error('Cannot create payment_methods');
      }

      if($sc === FALSE)
      {
        $this->session->set_flashdata('code', $code);
        $this->session->set_flashdata('name', $name);
        $this->session->set_flashdata('term', $term);
      }
    }
    else
    {
      set_error('Data not found');
    }

    redirect($this->home.'/add_new');
  }



  public function edit($code)
  {
    $this->load->helper('payment_method');
    $rs = $this->payment_methods_model->get_payment_methods($code);
    $data = array(
      'code' => $rs->code,
      'name' => $rs->name,
      'term' => $rs->has_term,
      'role' => $rs->role
    );

    $this->load->view('masters/payment_methods/payment_methods_edit_view', $data);
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $old_code = $this->input->post('payment_methods_code');
      $old_name = $this->input->post('payment_methods_name');
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $term = $this->input->post('term');
      $role = $this->input->post('role');

      $ds = array(
        'code' => $code,
        'name' => $name,
        'has_term' => $term === NULL ? 0 : $term,
        'role' => $role
      );

      if($sc === TRUE && $this->payment_methods_model->is_exists($code, $old_code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' already exists please choose another");
      }

      if($sc === TRUE && $this->payment_methods_model->is_exists_name($name, $old_name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' already exists please choose another");
      }

      if($sc === TRUE)
      {
        if($this->payment_methods_model->update($old_code, $ds) === TRUE)
        {
          set_message('Payment channels updated');
        }
        else
        {
          $sc = FALSE;
          set_error('Update payment channels not successfull');
        }
      }

    }
    else
    {
      $sc = FALSE;
      set_error('Data not found');
    }

    if($sc === FALSE)
    {
      $code = $this->input->post('payment_methods_code');
    }

    redirect($this->home.'/edit/'.$code);
  }



  public function delete($code)
  {
    if($code != '')
    {
      if($this->payment_methods_model->delete($code))
      {
        set_message('Payment channels deleted');
      }
      else
      {
        set_error('Cannot delete payment channels');
      }
    }
    else
    {
      set_error('payment channels not found');
    }

    redirect($this->home);
  }



  //--- เช็คว่าการชำระเงินเป็นแบบเครดิตหรือไม่
  public function is_credit_payment($code)
  {
    //---- ตรวจสอบว่าเป็นเครดิตหรือไม่
    $rs = $this->payment_methods_model->has_term($code);
    echo $rs === TRUE ? 1 : 0;
  }


  public function clear_filter()
	{
		$this->session->unset_userdata('code');
    $this->session->unset_userdata('name');
    $this->session->unset_userdata('term');
		echo 'done';
	}

}//--- end class
 ?>
