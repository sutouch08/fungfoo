<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Channels extends PS_Controller
{
  public $menu_code = 'DBCHAN';
	public $menu_group_code = 'DB';
	public $title = 'ช่องทางการขาย';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/channels';
    $this->load->model('masters/channels_model');
  }


  public function index()
  {
		$code = get_filter('code', 'code', '');
		$name = get_filter('name', 'name', '');

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->channels_model->count_rows($code, $name);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$rs = $this->channels_model->get_data($code, $name, $perpage, $this->uri->segment($segment));
        
    $ds = array(
      'code' => $code,
      'name' => $name,
			'data' => $rs
    );

		$this->pagination->initialize($init);
    $this->load->view('masters/channels/channels_view', $ds);
  }


  public function add_new()
  {
    $data['code'] = $this->session->flashdata('code');
    $data['name'] = $this->session->flashdata('name');
    $data['customer_code'] = $this->session->flashdata('customer_code');
    $data['customer_name'] = $this->session->flashdata('customer_name');
    $this->load->view('masters/channels/channels_add_view', $data);
  }


  public function add()
  {
    if($this->input->post('code'))
    {
      $sc = TRUE;
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $customer_code = $this->input->post('customer_code');
      $customer_name = $this->input->post('customer_name');
      $ds = array(
        'code' => $code,
        'name' => $name,
        'customer_code' => empty($customer_code) ? NULL : $customer_code,
        'customer_name' => empty($customer_name) ? NULL : $customer_code
      );

      if($this->channels_model->is_exists($code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' already exists");
      }

      if($this->channels_model->is_exists_name($name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' already exists");
      }

      if($sc === TRUE && $this->channels_model->add($ds))
      {
        set_message('Channels created');
      }
      else
      {
        $sc = FALSE;
        set_error('Cannot create channels');
      }

      if($sc === FALSE)
      {
        $this->session->set_flashdata('code', $code);
        $this->session->set_flashdata('name', $name);
        $this->session->set_flashdata('customer_code', $customer_code);
        $this->session->set_flashdata('customer_name', $customer_name);
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
    $data['data'] = $this->channels_model->get_channels($code);
    $this->load->view('masters/channels/channels_edit_view', $data);
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $old_code = $this->input->post('channels_code');
      $old_name = $this->input->post('channels_name');
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $customer_code = $this->input->post('customer_code');
      $customer_name = $this->input->post('customer_name');

      $ds = array(
        'code' => $code,
        'name' => $name,
        'customer_code' => empty($customer_code) ? NULL : $customer_code,
        'customer_name' => empty($customer_name) ? NULL : $customer_name
      );

      if($sc === TRUE && $this->channels_model->is_exists($code, $old_code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' already exists please choose another");
      }

      if($sc === TRUE && $this->channels_model->is_exists_name($name, $old_name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' already exists please choose another");
      }

      if($sc === TRUE)
      {
        if($this->channels_model->update($old_code, $ds) === TRUE)
        {
          set_message('Channels updated');
        }
        else
        {
          $sc = FALSE;
          set_error('Update channels not successfull');
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
      $code = $this->input->post('channels_code');
    }

    redirect($this->home.'/edit/'.$code);
  }



  public function delete($code)
  {
    if($code != '')
    {
      if($this->channels_model->delete($code))
      {
        set_message('Channels deleted');
      }
      else
      {
        set_error('Cannot delete channels');
      }
    }
    else
    {
      set_error('Channels not found');
    }

    redirect($this->home);
  }



  public function clear_filter()
	{
		$this->session->unset_userdata('code');
    $this->session->unset_userdata('name');
		echo 'done';
	}

}//--- end class
 ?>
