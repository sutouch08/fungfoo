<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_color extends PS_Controller
{
  public $menu_code = 'DBPDCL';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข สีสินค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_color';
    $this->load->model('masters/product_color_model');
    $this->load->helper('product_color');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'color_code', ''),
      'name' => get_filter('name', 'color_name', ''),
      'group_code' => get_filter('gen_code', 'gen_code', ''),
      'gen_code' => get_filter('group_code', 'color_group_code', ''),
      'status' => get_filter('status', 'status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->product_color_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$color = $this->product_color_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($color))
    {
      foreach($color as $rs)
      {
        $rs->menber = $this->product_color_model->count_members($rs->code);
      }
    }


    $filter['data'] = $color;

		$this->pagination->initialize($init);
    $this->load->view('masters/product_color/product_color_view', $filter);
  }



  public function set_active()
  {
    $code = $this->input->post('code');
    $active = $this->input->post('active') == 1 ? 0 :1;
    if($code)
    {
      $rs = $this->product_color_model->set_active($code, $active);
      if($rs)
      {
        $sc = "<span class=\"pointer\" onClick=\"toggleActive({$active}, '{$code}')\">";
        $sc .= is_active($active);
        $sc .= "</span>";

        echo $sc;
      }
    }
  }

  public function add_new()
  {
    $data['code'] = $this->session->flashdata('code');
    $data['name'] = $this->session->flashdata('name');
    $data['gen_code'] = $this->session->flashdata('gen_code');
    $data['group_code'] = $this->session->flashdata('group_code');
    $this->title = 'เพิ่ม สีสินค้า';
    $this->load->view('masters/product_color/product_color_add_view', $data);
  }


  public function add()
  {
    if($this->input->post('code'))
    {
      $sc = TRUE;
      $ds = array(
        'code' => trim($this->input->post('code')),
        'name' => trim($this->input->post('name')),
        'group_code' => get_null($this->input->post('group_code')),
        'gen_code' => get_null($this->input->post('gen_code'))
      );

      if($this->product_color_model->is_exists($code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' มีในระบบแล้ว");
      }



      if($sc === TRUE)
      {
        if($this->product_color_model->add($ds) === TRUE)
        {
          set_message('เพิ่มข้อมูลเรียบร้อยแล้ว');
        }
        else
        {
          $sc = FALSE;
          set_error('เพิ่มข้อมูลไม่สำเร็จ');
        }
      }


      if($sc === FALSE)
      {
        $this->session->set_flashdata($ds);
      }
    }
    else
    {
      set_error('ไม่พบข้อมูล');
    }

    redirect($this->home.'/add_new');
  }



  public function edit($code)
  {
    $this->title = 'แก้ไข สีสินค้า';
    $rs = $this->product_color_model->get($code);
    $data = array(
      'code' => $rs->code,
      'name' => $rs->name,
      'group_code' => $rs->group_code,
      'gen_code' => $rs->gen_code
    );

    $this->load->view('masters/product_color/product_color_edit_view', $data);
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $old_code = $this->input->post('product_color_code');
      $old_name = $this->input->post('product_color_name');
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $group_code = $this->input->post('group_code');
      $gen_code = $this->input->post('gen_code');


      $ds = array(
        'code' => $code,
        'name' => $name,
        'group_code' => $group_code,
        'gen_code' => $gen_code
      );

      if($this->product_color_model->is_exists($code, $old_code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' มีอยู่ในระบบแล้ว โปรดใช้รหัสอื่น");
      }

      if($sc === TRUE)
      {
        if($this->product_color_model->update($old_code, $ds) === TRUE)
        {
          set_message('ปรับปรุงข้อมูลเรียบร้อยแล้ว');
        }
        else
        {
          $sc = FALSE;
          set_error('ปรับปรุงข้อมูลไม่สำเร็จ');
        }
      }

    }
    else
    {
      $sc = FALSE;
      set_error('ไม่พบข้อมูล');
    }

    if($sc === FALSE)
    {
      $code = $this->input->post('product_color_code');
    }

    redirect($this->home.'/edit/'.$code);
  }



  public function delete($code)
  {
    $code = urldecode($code);
    if($code != '')
    {
      if($this->product_color_model->delete($code))
      {
        set_message('ลบข้อมูลเรียบร้อยแล้ว');
      }
      else
      {
        set_error('ลบข้อมูลไม่สำเร็จ');
      }
    }
    else
    {
      set_error('ไม่พบข้อมูล');
    }

    redirect($this->home);
  }



  public function is_exists($code)
  {
    if(!empty($code))
    {
      if($this->product_color_model->is_exists($code) === TRUE)
      {
        echo 'exists';
      }
      else
      {
        echo 'not exists';
      }
    }
    else
    {
      echo 'not exists';
    }
  }

  public function clear_filter()
	{
		clear_filter(array('color_code', 'color_name', 'color_group_code', 'color_gen_code'));
		echo 'done';
	}

}//--- end class
 ?>
