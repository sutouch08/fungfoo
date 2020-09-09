<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Po extends PS_Controller
{
  public $menu_code = 'POPURC';
	public $menu_group_code = 'PO';
  public $menu_sub_group_code = '';
	public $title;
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'purchase/po';
    $this->load->model('purchase/po_model');
    $this->load->model('masters/vender_model');
    $this->load->model('masters/products_model');
    $this->title = label_value($this->menu_code);
  }


  public function index()
  {

    $filter = array(
      'code'    => get_filter('code', 'po_code', ''),
      'vender'  => get_filter('vender', 'po_vender', ''),
      'from_date' => get_filter('fromDate', 'po_from_date', ''),
      'to_date' => get_filter('toDate', 'po_to_date', ''),
      'status' => get_filter('status', 'po_status', 'all') //-- all, 0 = not save, 1= saved (open) , 2 = closed, 3 = cancled
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->po_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->po_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->total_amount = $this->po_model->get_sum_amount($rs->code);
      }
    }

    $filter['po'] = $document;

		$this->pagination->initialize($init);
    $this->load->view('purchase/po/po_list', $filter);
  }



  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('purchase/po/po_add');
    }
    else
    {
      set_error(label_value('no_permission'));
      redirect($this->home);
    }
  }


  public function add()
  {
    if($this->pm->can_add)
    {
      if($this->input->post('vender_code'))
      {
        $date_add = db_date($this->input->post('date_add'));
        $require_date = get_null($this->input->post('require_date')) === NULL ? db_date() : db_date($this->input->post('require_date'));
        $code = $this->get_new_code($date_add);
        $vender_code = $this->input->post('vender_code');
        $remark = $this->input->post('remark');
        $vender = $this->vender_model->get($vender_code);
        if(!empty($vender))
        {
          $arr = array(
            'code' => $code,
            'vender_code' => $vender->code,
            'vender_name' => $vender->name,
            'credit_term' => $vender->credit_term,
            'user' => get_cookie('uname'),
            'require_date' => $require_date,
            'due_date' => date('Y-m-d', strtotime("+{$vender->credit_term} day", strtotime($date_add))),
            'date_add' => $date_add,
            'remark' => $remark
          );

          if($this->po_model->add($arr))
          {
            redirect($this->home.'/edit/'.$code);
          }
          else
          {
            set_error(label_value('doc_error'));
            redirect($this->home.'/add_new');
          }
        }
        else
        {
          set_error(label_value('invalid_vender'));
          redirect($this->home.'/add_new');
        }

      }
      else
      {
        set_error('no_data_found');
        redirect($this->home.'/add_new');
      }
    }
    else
    {
      set_error(label_value('no_permission'));
      redirect($this->home);
    }
  }



  //--- add blunk details to po
  public function add_details($po_code)
  {
    if($this->input->post('data'))
    {
      $data = $this->input->post('data');
      if(!empty($data))
      {
        foreach($data as $rs)
        {
          $code = $rs['code']; //-- รหัสสินค้า
          $qty = $rs['qty'];
          $item = $this->products_model->get($code);
          if($qty > 0)
          {
            $qty = ceil($qty);
            //---
            $ds = $this->po_model->get_detail($po_code, $item->code);
            if(!empty($ds))
            {
              $new_qty = $ds->qty + $qty;
              $arr = array(
                'qty' => $new_qty,
                'total_amount' => $new_qty * $item->cost
              );

              //--- Update
              $this->po_model->update_detail($ds->id, $arr);
            }
            else
            {
              $arr = array(
                'po_code' => $po_code,
                'product_code' => $item->code,
                'product_name' => $item->name,
                'style_code' => $item->style_code,
                'price' => $item->cost,
                'qty' => $qty,
                'total_amount' => $item->cost * $qty
              );
              //---- add
              $this->po_model->add_detail($arr);
            }
          }
        }
      }

      echo 'success';
    }
    else
    {
      echo label_value('no_data_found');
    }
  }



  //--- add details to po
  public function add_detail($po_code)
  {
    if($this->input->post('product_code'))
    {
      $code = $this->input->post('product_code');
      $qty = $this->input->post('qty');

      $item = $this->products_model->get($code);

      if($qty > 0)
      {
        $qty = ceil($qty);
        //---
        $ds = $this->po_model->get_detail($po_code, $item->code);
        if(!empty($ds))
        {
          $new_qty = $ds->qty + $qty;
          $arr = array(
            'qty' => $new_qty,
            'total_amount' => $new_qty * $item->cost
          );

          //--- Update
          $this->po_model->update_detail($ds->id, $arr);
        }
        else
        {
          $arr = array(
            'po_code' => $po_code,
            'product_code' => $item->code,
            'product_name' => $item->name,
            'style_code' => $item->style_code,
            'price' => $item->cost,
            'qty' => $qty,
            'total_amount' => $item->cost * $qty
          );
          //---- add
          $this->po_model->add_detail($arr);
        }
      }
      echo 'success';
    }
    else
    {
      echo label_value('no_data_found');
    }
  }



  public function edit($code)
  {
    $po = $this->po_model->get($code);
    if(!empty($po))
    {
      $po->vender_name = $this->vender_model->get_name($po->vender_code);
    }

    $detail = $this->po_model->get_details($code);
    if(!empty($detail))
    {
      foreach($detail as $rs)
      {
        $rs->product_name = $this->products_model->get_name($rs->product_code);
      }
    }

    $ds['po'] = $po;
    $ds['details'] = $detail;
    $this->load->view('purchase/po/po_edit_detail', $ds);
  }


  public function update()
  {
    if($this->input->post('po_code'))
    {
      $code = $this->input->post('po_code');
      $date_add = db_date($this->input->post('date_add'));
      $require_date = get_null($this->input->post('require_date')) === NULL ? db_date() : db_date($this->input->post('require_date'));
      $vender_code = $this->input->post('vender_code');
      $remark = $this->input->post('remark');
      $vender = $this->vender_model->get($vender_code);
      if(!empty($vender))
      {
        $arr = array(
          'vender_code' => $vender->code,
          'vender_name' => $vender->name,
          'credit_term' => $vender->credit_term,
          'user' => get_cookie('uname'),
          'require_date' => $require_date,
          'due_date' => date('Y-m-d', strtotime("+{$vender->credit_term} day", strtotime($date_add))),
          'date_add' => $date_add,
          'remark' => $remark
        );

        if($this->po_model->update($code, $arr))
        {
          echo 'success';
        }
        else
        {
          echo label_value('update_fail');
        }
      }
      else
      {
        echo label_value('invalid_vender');
      }
    }
    else
    {
      echo label_value('no_data_found');
    }
  }




  public function get_details_table($po_code)
  {
    $details = $this->po_model->get_details($po_code);
    if(!empty($details))
    {
      $ds = array();
      $no = 1;
      $total_qty = 0;
      $total_amount = 0;
      foreach($details as $rs)
      {
        $arr = array(
          'no' => $no,
          'id' => $rs->id,
          'product_code' => $rs->product_code,
          'product_name' => $this->products_model->get_name($rs->product_code),
          'price' => $rs->price,
          'qty' => $rs->qty,
          'amount' => number($rs->total_amount, 2)
        );

        array_push($ds, $arr);
        $no++;
        $total_qty += $rs->qty;
        $total_amount += $rs->total_amount;
      }

      $arr = array(
        'total_qty' => number($total_qty),
        'total_amount' => number($total_amount)
      );

      array_push($ds, $arr);

      echo json_encode($ds);
    }
    else
    {
      echo 'no_content';
    }
  }



  public function remove_detail($id)
  {
    $rs = $this->po_model->delete_detail($id);
    if($rs)
    {
      echo 'success';
    }
    else
    {
      echo label_value('delete_fail');
    }
  }


  public function remove_all_details()
  {
    if($this->input->post('po_code'))
    {
      $code = $this->input->post('po_code');
      if($this->po_model->delete_all_details($code))
      {
        echo 'success';
      }
      else
      {
        echo label_value('delete_fail');
      }
    }
    else
    {
      echo label_value('no_data_found');
    }
  }


public function delete_po($code)
{
  $sc = TRUE;
  if($this->pm->can_delete)
  {
    $po = $this->po_model->get($code);
    if(!empty($po))
    {
      if($po->status > 1)
      {
        $sc = FALSE;
        $this->error = label_value('invalid_status');
      }
      else
      {
        $received = $this->po_model->get_sum_received($code);
        if($received == 0)
        {
          $this->db->trans_start();
          $this->po_model->delete_all_details($code);
          $this->po_model->delete_po($code);
          $this->db->trans_complete();

          if($this->db->trans_status() === FALSE)
          {
            $sc = FALSE;
            $this->error = label_value('delete_fail');
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = label_value('partial_received');
        }
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



  public function save_po()
  {
    if($this->input->post('po_code'))
    {
      $code = $this->input->post('po_code');
      $rs = $this->po_model->change_status($code, 1);
      if($rs)
      {
        echo 'success';
      }
      else
      {
        echo label_value('update_fail');
      }
    }
    else
    {
      echo label_value('no_data_found');
    }
  }


  public function unsave_po()
  {
    if($this->input->post('po_code'))
    {
      $code = $this->input->post('po_code');
      $rs = $this->po_model->change_status($code, 0);
      if($rs)
      {
        echo 'success';
      }
      else
      {
        echo label_value('update_fail');
      }
    }
    else
    {
      echo label_value('no_data_found');
    }
  }



  public function close_po($code)
  {
    $sc = TRUE;
    if($this->pm->can_edit OR $this->pm->can_add)
    {
      $po = $this->po_model->get($code);
      if(!empty($po))
      {
        if($po->status < 3)
        {
          $rs = $this->po_model->close_po($code);
          if(!$rs)
          {
            $sc = FALSE;
            $this->error = label_value('update_fail');
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = lable_value('no_data_found');
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = label_value('no_permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function un_close_po($code)
  {
    $sc = TRUE;
    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $received = $this->po_model->get_sum_received($code); //--- รับแล้วเท่าไร
      $status = $received > 0 ? 2 : 1; //--- ถ้าเคยรับมาแล้วสถานะเป็น part
      if(! $this->po_model->un_close_po($code, $status))
      {
        $sc = FALSE;
        $this->error = label_value('update_fail');
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = label_value('no_permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function view_detail($code)
  {
    $po = $this->po_model->get($code);
    if(!empty($po))
    {
      $po->vender_name = $this->vender_model->get_name($po->vender_code);
    }

    $detail = $this->po_model->get_details($code);
    if(!empty($detail))
    {
      foreach($detail as $rs)
      {
        $rs->product_name = $this->products_model->get_name($rs->product_code);
      }
    }

    $ds['po'] = $po;
    $ds['details'] = $detail;
    $this->load->view('purchase/po/po_view_detail', $ds);
  }



  public function print_po($code)
  {
    $this->load->library('printer');
    $po = $this->po_model->get($code);
    if(!empty($po))
    {
      $po->vender_name = $this->vender_model->get_name($po->vender_code);
    }

    $detail = $this->po_model->get_details($code);
    if(!empty($detail))
    {
      foreach($detail as $rs)
      {
        $rs->product_name = $this->products_model->get_name($rs->product_code);
      }
    }

    $ds['po'] = $po;
    $ds['details'] = $detail;
    $this->load->view('print/print_po', $ds);
  }




  public function get_new_code($date = '')
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_PO');
    $run_digit = getConfig('RUN_DIGIT_PO');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->po_model->get_max_code($pre);
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
    $filter = array('po_code', 'po_vender', 'po_from_date', 'po_to_date', 'po_status');
    clear_filter($filter);
  }
} //-- end class
?>
