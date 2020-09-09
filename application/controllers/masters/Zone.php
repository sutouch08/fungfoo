<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Zone extends PS_Controller
{
  public $menu_code = 'DBZONE';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'WAREHOUSE';
	public $title = 'เพิ่ม/แก้ไข โซน';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/zone';
    $this->load->model('masters/zone_model');
    $this->load->helper('zone');
    $this->load->helper('warehouse');
    $this->title = label_value('DBZONE');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'zone_code', ''),
      'name' => get_filter('name', 'zone_name', ''),
      'warehouse' => get_filter('warehouse', 'zone_warehouse', ''),
      'customer' => get_filter('customer', 'zone_customer', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->zone_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list = $this->zone_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $rs->customer_count = $this->zone_model->count_customer($rs->code);
      }
    }

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/zone/zone_list', $filter);
  }



  public function add_new()
  {
    $this->load->view('masters/zone/zone_add');
  }



  public function add()
  {
    if($this->input->post('code'))
    {
      $arr = array(
        'code' => trim($this->input->post('code')),
        'name' => trim($this->input->post('name')),
        'warehouse_code' => $this->input->post('warehouse'),
        'date_add' => now(),
        'update_user' => get_cookie('uname')
      );

      if($this->zone_model->add($arr))
      {
        set_message(label_value('insert_success'));
      }
      else
      {
        set_error(label_value('insert_fail'));
      }
    }
    else
    {
      set_error(label_value('no_data_found'));
    }

    redirect($this->home.'/add_new');
  }



  public function is_exists_code($code, $old_code = NULL)
  {
    $exists = $this->zone_model->is_exists_code($code, $old_code);
    if($exists)
    {
      echo label_value('duplicated_code');
    }
    else
    {
      echo 'ok';
    }
  }


  public function is_exists_name($name, $old_name = NULL)
  {
    $exists = $this->zone_model->is_exists_name($name, $old_name);
    if($exists)
    {
      echo label_value('duplicated_name');
    }
    else
    {
      echo 'ok';
    }
  }

  public function edit($code)
  {
    if($this->pm->can_edit)
    {
      $ds['ds'] = $this->zone_model->get($code);
      $ds['customers'] = $this->zone_model->get_customers($code);
      $this->load->view('masters/zone/zone_edit', $ds);
    }
    else
    {
      set_error("คุณไม่มีสิทธิ์แก้ไข");
      redirect($this->home);
    }
  }


  public function update()
  {
    if($this->pm->can_edit)
    {
      if($this->input->post('code'))
      {
        $old_code = $this->input->post('old_code');
        $code = trim($this->input->post('code'));
        $arr = array(
          'code' => $code,
          'name' => trim($this->input->post('name')),
          'warehouse_code' => $this->input->post('warehouse'),
          'update_user' => get_cookie('uname')
        );

        if($this->zone_model->update($old_code, $arr))
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
      else
      {
        set_error(label_value('no_data_found'));
        redirect($this->home);
      }
    }
    else
    {
      set_error(label_value('no_permission'));
      redirect($this->home);
    }
  }

  public function delete($code)
  {
    $sc = TRUE;
    if($this->pm->can_delete)
    {
      if($this->zone_model->count_customer($code) > 0)
      {
        $sc = FALSE;
        $this->error = label_value('child_inside');
      }

      if($sc === TRUE)
      {
        if( ! $this->zone_model->delete($code))
        {
          $sc = FALSE;
          $this->error = label_value('delete_fail');
        }
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = label_value('no_permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  public function add_customer()
  {
    $sc = TRUE;
    if($this->pm->can_edit)
    {
      if($this->input->post('zone_code') && $this->input->post('customer_code'))
      {
        $this->load->model('masters/customers_model');
        $code = $this->input->post('zone_code');
        $customer_code = $this->input->post('customer_code');
        $customer = $this->customers_model->get($customer_code);
        if(!empty($customer))
        {
          if($this->zone_model->is_exists_customer($code, $customer->code))
          {
            $sc = FALSE;
            $this->error = label_value('already_exists');
          }
          else
          {
            $arr = array(
              'zone_code' => $code,
              'customer_code' => $customer->code,
              'customer_name' => $customer->name
            );

            if( ! $this->zone_model->add_customer($arr))
            {
              $sc = FALSE;
              $this->error = label_value('insert_fail');
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = label_value('invalid_code');
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = label_value('no_data_found');
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = label_value('no_permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function delete_customer($id)
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      if( ! $this->zone_model->delete_customer($id))
      {
        $sc = FALSE;
        $this->error = label_value('delete_fail');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = label_value('no_permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function syncData()
  {
    $count = $this->zone_model->count_rows();
    if($count > 0 )
    {
      $last_add = $this->zone_model->get_last_create_date();
      $last_upd = $this->zone_model->get_last_update_date();

      $last_add = empty($last_add) ? now() : $last_add;
      $last_upd = empty($last_upd) ? now() : $last_upd;

      $newData = $this->zone_model->get_new_data($last_add, $last_upd);
    }
    else
    {
      $last_add = date('1970-01-01 00:00:00');
      $newData = $this->zone_model->get_all_zone();
    }

    if(!empty($newData))
    {
      foreach($newData as $rs)
      {
        if($this->zone_model->is_exists_id($rs->id))
        {
          $ds = array(
            'code' => $rs->code,
            'name' => $rs->name,
            'sap_updateDate' => $rs->updateDate,
          );

          $this->zone_model->update($rs->id, $ds);
        }
        else
        {
          $ds = array(
            'id' => $rs->id,
            'code' => $rs->code,
            'name' => $rs->name,
            'warehouse_code' => $rs->warehouse_code,
            'sap_createDate' => $rs->createDate,
            'sap_updateDate' => $rs->updateDate
          );

          $this->zone_model->add($ds);
        }
      }
    }

    echo 'done';
  }



  //--- check zone
  public function get_zone_code()
  {
    $sc = TRUE;
    if($this->input->get('barcode'))
    {
      $code = trim($this->input->get('barcode'));
      if($this->zone_model->is_exists($code) === FALSE)
      {
        $sc = FALSE;
      }
    }

    echo $sc === TRUE ? $code : 'not_exists';
  }



  public function get_warehouse_zone()
  {
    $sc = TRUE;
    $code = trim($this->input->get('barcode'));
    $warehouse_code = trim($this->input->get('warehouse_code'));
    if(!empty($code) && !empty($warehouse_code))
    {
      $zone = $this->zone_model->get_zone_detail_in_warehouse($code, $warehouse_code);
      if($zone === FALSE)
      {
        $sc = FALSE;
      }
    }

    echo $sc === TRUE ? json_encode($zone) : 'not_exists';
  }


  public function clear_filter()
  {
    $filter = array('zone_code', 'zone_name', 'zone_customer', 'zone_warehouse');
    clear_filter($filter);
  }

} //--- end class

 ?>
