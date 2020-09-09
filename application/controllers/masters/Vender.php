<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Vender extends PS_Controller
{
  public $menu_code = 'DBVEND';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = '';
	public $title = 'เพิ่ม/แก้ไข ผู้จำหน่าย';

  public function __construct()
  {
    parent::__construct();
    $this->title = label_value('DBVEND');
    $this->home = base_url().'masters/vender';
    $this->load->model('masters/vender_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'vender_code', ''),
      'name' => get_filter('name', 'vender_name', ''),
      'active' => get_filter('active', 'vender_active', 2)
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment = 4; //-- url segment
		$rows = $this->vender_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$emps = $this->vender_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $emps;

		$this->pagination->initialize($init);
    $this->load->view('masters/vender/vender_view', $filter);
  }


  public function add_new()
  {
    $active = $this->session->flashdata('active');
    $data['code'] = $this->session->flashdata('code');
    $data['name'] = $this->session->flashdata('name');
    $data['credit_term'] = $this->session->flashdata('credit_term');
    $data['active'] = is_null($active) ? 1 : $this->session->flashdata('active');
    $this->load->view('masters/vender/vender_add', $data);
  }


  public function add()
  {
    if($this->input->post('code'))
    {
      $sc = TRUE;
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $term = $this->input->post('credit_term');
      $active = $this->input->post('active');

      $ds = array(
        'code' => $code,
        'name' => $name,
        'credit_term' => $term,
        'active' => $active
      );

      if($this->vender_model->is_exists($code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' ".label_value('already_exists'));
      }

      if($this->vender_model->is_exists_name($name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' ".label_value('already_exists'));
      }

      if($sc === TRUE)
      {
        if($this->vender_model->add($ds))
        {
          set_message(label_value('insert_success'));
        }
        else
        {
          $sc = FALSE;
          set_error('insert_fail');
        }
      }


      if($sc === FALSE)
      {
        $this->session->set_flashdata('code', $code);
        $this->session->set_flashdata('name', $name);
        $this->session->set_flashdata('credit_term', $term);
        $this->session->set_flashdata('active', $active);
      }
    }
    else
    {
      set_error(label_value('no_data_found'));
    }

    redirect($this->home.'/add_new');
  }



  public function edit($code)
  {
    $rs = $this->vender_model->get($code);
    $data = array(
      'code' => $rs->code,
      'name' => $rs->name,
      'credit_term' => $rs->credit_term,
      'active' => $rs->active
    );

    $this->load->view('masters/vender/vender_edit', $data);
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $old_code = $this->input->post('old_code');
      $old_name = $this->input->post('old_name');
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $term = $this->input->post('credit_term');
      $active = $this->input->post('active');

      $ds = array(
        'code' => $code,
        'name' => $name,
        'credit_term' => $term,
        'active' => $active
      );

      if($sc === TRUE && $this->vender_model->is_exists($code, $old_code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' ".label_value('already_exists'));
      }

      if($sc === TRUE && $this->vender_model->is_exists_name($name, $old_name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' ".label_value('already_exists'));
      }

      if($sc === TRUE)
      {
        if($this->vender_model->update($old_code, $ds) === TRUE)
        {
          set_message(label_value('update_success'));
        }
        else
        {
          $sc = FALSE;
          set_error(label_value('update_fail'));
        }
      }

    }
    else
    {
      $sc = FALSE;
      set_error(label_value('no_data_found'));
    }

    if($sc === FALSE)
    {
      $code = $old_code;
    }

    redirect($this->home.'/edit/'.$code);
  }



  public function delete($code)
  {
    if($code != '')
    {
      if($this->vender_model->delete($code))
      {
        set_message(label_value('delete_success'));
      }
      else
      {
        set_error(label_value('delete_fail'));
      }
    }
    else
    {
      set_error(label_value('no_data_found'));
    }

    redirect($this->home);
  }






  public function clear_filter()
	{
		$filter = array('emp_code', 'emp_name', 'emp_active');
    clear_filter($filter);
		echo 'done';
	}
}

?>
