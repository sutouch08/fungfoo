<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_tab extends PS_Controller
{
  public $menu_code = 'DBPTAB';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข แถบแสดงสินค้า';
  public $error = '';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_tab';
    //--- load model
    $this->load->model('masters/product_tab_model');
    $this->load->helper('product_tab');
  }


  public function index()
  {
    $filter = array(
      'tab_name' => get_filter('tab_name', 'tab_name', ''),
      'parent' => get_filter('parent', 'parent', '')
    );

    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->product_tab_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$tabs = $this->product_tab_model->get_list($filter, $perpage, $this->uri->segment($segment));
    if(!empty($tabs))
    {
      foreach($tabs as $rs)
      {
        if($rs->id_parent == 0)
        {
          $rs->parent = "TOP LEVEL";
        }

        $rs->members = $this->product_tab_model->countMember($rs->id);
      }
    }

    $filter['tabs'] = $tabs;
		$this->pagination->initialize($init);
    $this->load->view('masters/product_tab/product_tab_view', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/product_tab/product_tab_add');
  }


  public function add()
  {
    if($this->input->post('tab_name'))
    {
      $name = trim($this->input->post('tab_name'));
      $parent = $this->input->post('tabs');

      if(! $this->product_tab_model->isExists('name', $name))
      {
        $arr = array(
          'name' => $name,
          'id_parent' => $parent
        );

        if($this->product_tab_model->add($arr))
        {
          set_message("เพิ่มแถบสินค้าเรียบร้อยแล้ว");
        }
        else
        {
          set_error("เพิ่มแถบสินค้าไม่สำเร็จ");
        }
      }
      else
      {
        set_error("ชื่อแถบซ้ำ กรุณาใช้ชื่อแถบอื่น");
      }
    }
    else
    {
      set_error("กรุณาระบุชื่อแถบสินค้า");
    }

    redirect($this->home.'/add_new');
  }


  public function edit($id)
  {
    $ds = $this->product_tab_model->get($id);
    $this->load->view('masters/product_tab/product_tab_edit', $ds);
  }


  public function update($id)
  {
    if($this->input->post('tab_name'))
    {
      $name = trim($this->input->post('tab_name'));
      $parent = $this->input->post('tabs');

      if(! $this->product_tab_model->isExists('name', $name, $id))
      {
        $arr = array(
          'name' => $name,
          'id_parent' => $parent
        );

        if(! $this->product_tab_model->update($id, $arr))
        {
          set_error('แก้ไขข้อมูลไม่สำเร็จ');
        }
        else
        {
          set_message('แก้ไขข้อมูลเรียบร้อยแล้ว');
        }
      }
      else
      {
        set_error('ชื่อซ้ำ กรุณาใช้ชื่ออื่น');
      }
    }
    else
    {
      set_error('กรุณาระบุชื่อแถบสินค้า');
    }

    redirect($this->home.'/edit/'.$id);
  }


  public function delete($id)
  {
    $sc = TRUE;

    if(! $this->product_tab_model->is_has_child($id))
    {
      if( ! $this->product_tab_model->delete($id))
      {
        $sc = FALSE;
        $this->error = "ลบแถบสินค้าไม่สำเร็จ";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่สามารถลบแถบได้เนื่องจากมีแถบลูกค้างอยู่";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_filter()
  {
    $filter = array('tab_name', 'parent');
    clear_filter($filter);
  }


}//--- end class
?>
