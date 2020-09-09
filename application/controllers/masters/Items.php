<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Items extends PS_Controller
{
  public $menu_code = 'DBITEM';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข รายการสินค้า';
  public $error = '';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/items';
    $this->title = label_value($this->menu_code);
    //--- load model
    $this->load->model('masters/products_model');
    $this->load->model('masters/product_group_model');
    $this->load->model('masters/product_kind_model');
    $this->load->model('masters/product_type_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/product_brand_model');
    $this->load->model('masters/product_category_model');
    $this->load->model('masters/product_color_model');
    $this->load->model('masters/product_size_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('masters/product_image_model');

    //---- load helper
    $this->load->helper('product_tab');
    $this->load->helper('product_brand');
    $this->load->helper('product_tab');
    $this->load->helper('product_kind');
    $this->load->helper('product_type');
    $this->load->helper('product_group');
    $this->load->helper('product_category');
    $this->load->helper('product_sub_group');
    $this->load->helper('product_images');
    $this->load->helper('unit');

  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'code', ''),
      'name'      => get_filter('name', 'name', ''),
      'group'     => get_filter('group', 'group', ''),
      'sub_group' => get_filter('sub_group', 'sub_group', ''),
      'category'  => get_filter('category', 'category', ''),
      'kind'      => get_filter('kind', 'kind', ''),
      'type'      => get_filter('type', 'type', ''),
      'brand'     => get_filter('brand', 'brand', ''),
      'year'      => get_filter('year', 'year', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->products_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$products = $this->products_model->get_list($filter, $perpage, $this->uri->segment($segment));
    $ds       = array();
    if(!empty($products))
    {
      foreach($products as $rs)
      {
        $product = new stdClass();
        $product->code    = $rs->code;
        $product->name    = $rs->name;
        $product->price   = $rs->price;
        $product->group   = $this->product_group_model->get_name($rs->group_code);
        $product->kind    = $this->product_kind_model->get_name($rs->kind_code);
        $product->type    = $this->product_type_model->get_name($rs->type_code);
        $product->category  = $this->product_category_model->get_name($rs->category_code);
        $product->brand   = $this->product_brand_model->get_name($rs->brand_code);
        $product->year    = $rs->year;
        $product->sell    = $rs->can_sell;
        $product->active  = $rs->active;
        $product->api     = $rs->is_api;
        $product->date_upd = $rs->date_upd;

        $ds[] = $product;
      }
    }

    $filter['data'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('masters/product_items/items_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/product_items/items_add_view');
  }


  public function add()
  {
    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      if($this->products_model->is_exists($code))
      {
        set_error($code.' '.label_value('already_exists'));
      }
      else
      {
        $count = $this->input->post('count_stock');
        $sell = $this->input->post('can_sell');
        $api = $this->input->post('is_api');
        $active = $this->input->post('active');
        $user = get_cookie('uname');

        $arr = array(
          'code' => trim($this->input->post('code')),
          'name' => trim($this->input->post('name')),
          'barcode' => get_null(trim($this->input->post('barcode'))),
          'style_code' => trim($this->input->post('style')),
          'color_code' => get_null($this->input->post('color')),
          'size_code' => get_null($this->input->post('size')),
          'group_code' => get_null($this->input->post('group_code')),
          'sub_group_code' => get_null($this->input->post('sub_group_code')),
          'category_code' => get_null($this->input->post('category_code')),
          'kind_code' => get_null($this->input->post('kind_code')),
          'type_code' => get_null($this->input->post('type_code')),
          'brand_code' => get_null($this->input->post('brand_code')),
          'year' => $this->input->post('year'),
          'cost' => round($this->input->post('cost'), 2),
          'price' => round($this->input->post('price'), 2),
          'unit_code' => $this->input->post('unit_code'),
          'count_stock' => is_null($count) ? 0 : 1,
          'can_sell' => is_null($sell) ? 0 : 1,
          'active' => is_null($active) ? 0 : 1,
          'is_api' => is_null($api) ? 0 : 1,
          'update_user' => $user
        );

        if($this->products_model->add($arr))
        {
          set_message(label_value('insert_success'));
        }
        else
        {
          set_error(label_value('insert_fail'));
        }
      }
    }
    else
    {
      set_error(label_value('no_data_found'));
    }

    redirect($this->home.'/add_new');
  }


  public function add_duplicate()
  {
    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      if($this->products_model->is_exists($code))
      {
        set_error($code.' '.label_value('already_exists'));
      }
      else
      {
        $count = $this->input->post('count_stock');
        $sell = $this->input->post('can_sell');
        $api = $this->input->post('is_api');
        $active = $this->input->post('active');
        $user = get_cookie('uname');

        $arr = array(
          'code' => trim($this->input->post('code')),
          'name' => trim($this->input->post('name')),
          'barcode' => get_null(trim($this->input->post('barcode'))),
          'style_code' => trim($this->input->post('style')),
          'color_code' => get_null($this->input->post('color')),
          'size_code' => get_null($this->input->post('size')),
          'group_code' => get_null($this->input->post('group_code')),
          'sub_group_code' => get_null($this->input->post('sub_group_code')),
          'category_code' => get_null($this->input->post('category_code')),
          'kind_code' => get_null($this->input->post('kind_code')),
          'type_code' => get_null($this->input->post('type_code')),
          'brand_code' => get_null($this->input->post('brand_code')),
          'year' => $this->input->post('year'),
          'cost' => round($this->input->post('cost'), 2),
          'price' => round($this->input->post('price'), 2),
          'unit_code' => $this->input->post('unit_code'),
          'count_stock' => is_null($count) ? 0 : 1,
          'can_sell' => is_null($sell) ? 0 : 1,
          'active' => is_null($active) ? 0 : 1,
          'is_api' => is_null($api) ? 0 : 1,
          'update_user' => $user
        );

        if($this->products_model->add($arr))
        {
          set_message(label_value('insert_success'));
        }
        else
        {
          set_error(label_value('insert_fail'));
        }
      }
    }
    else
    {
      set_error(label_value('no_data_found'));
    }

    redirect($this->home);
  }




  public function edit($code)
  {
    $item = $this->products_model->get($code);
    if(!empty($item))
    {
      $this->load->view('masters/product_items/items_edit_view', $item);
    }
    else
    {
      set_error(label_value('no_content'));
      redirect($this->home);
    }
  }



  public function duplicate($code)
  {
    $item = $this->products_model->get($code);
    if(!empty($item))
    {
      $this->load->view('masters/product_items/items_duplicate_view', $item);
    }
    else
    {
      set_error(label_value('no_content'));
      redirect($this->home);
    }
  }


  public function update($old_code)
  {
    if($this->input->post('code'))
    {
      $code = $this->input->post('code');

      if($this->products_model->is_exists($code, $old_code))
      {
        set_error($code.' '.label_value('already_exists'));
        redirect($this->home.'/edit/'.$old_code);
      }
      else
      {
        $count = $this->input->post('count_stock');
        $sell = $this->input->post('can_sell');
        $api = $this->input->post('is_api');
        $active = $this->input->post('active');
        $user = get_cookie('uname');

        $arr = array(
          'code' => trim($this->input->post('code')),
          'name' => trim($this->input->post('name')),
          'barcode' => get_null(trim($this->input->post('barcode'))),
          'style_code' => trim($this->input->post('style')),
          'color_code' => get_null($this->input->post('color')),
          'size_code' => get_null($this->input->post('size')),
          'group_code' => get_null($this->input->post('group_code')),
          'sub_group_code' => get_null($this->input->post('sub_group_code')),
          'category_code' => get_null($this->input->post('category_code')),
          'kind_code' => get_null($this->input->post('kind_code')),
          'type_code' => get_null($this->input->post('type_code')),
          'brand_code' => get_null($this->input->post('brand_code')),
          'year' => $this->input->post('year'),
          'cost' => round($this->input->post('cost'), 2),
          'price' => round($this->input->post('price'), 2),
          'unit_code' => $this->input->post('unit_code'),
          'count_stock' => is_null($count) ? 0 : 1,
          'can_sell' => is_null($sell) ? 0 : 1,
          'active' => is_null($active) ? 0 : 1,
          'is_api' => is_null($api) ? 0 : 1,
          'update_user' => $user
        );

        if($this->products_model->update($old_code, $arr))
        {
          set_message(label_value('update_success'));
          redirect($this->home.'/edit/'.$code);
        }
        else
        {
          set_error(label_value('update_fail'));
          redirect($this->home.'/edit/'.$old_code);
        }
      }
    }
    else
    {
      set_error(label_value('no_data_found'));
      redirect($this->home.'/edit/'.$old_code);
    }
  }



  public function is_exists_code($code, $old_code = '')
  {
    if($this->products_model->is_exists($code, $old_code))
    {
      echo label_value('duplicated_code');
    }
    else
    {
      echo 'ok';
    }
  }



  public function toggle_can_sell($code)
  {
    $status = $this->products_model->get_status('can_sell', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('can_sell', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }


  public function toggle_active($code)
  {
    $status = $this->products_model->get_status('active', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('active', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }



  public function toggle_api($code)
  {
    $status = $this->products_model->get_status('is_api', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('is_api', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }


  public function delete_item($item)
  {
    $sc = TRUE;

    if($item != '')
    {
      if(! $this->products_model->has_transection($item))
      {
        if(! $this->products_model->delete_item($item))
        {
          $sc = FALSE;
          $message = "ลบรายการไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $message = "ไม่สามารถลบ {$item} ได้ เนื่องจากสินค้ามี Transcetion เกิดขึ้นแล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบข้อมูล';
    }

    echo $sc === TRUE ? 'success' : $message;
  }





  public function clear_filter()
	{
    $filter = array('code','name','group','sub_group','category','kind','type','brand','year');
    clear_filter($filter);
	}
}

?>
