<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Consign_check extends PS_Controller
{
  public $menu_code = 'ICCSRC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = '';
	public $title = 'กระทบยอดสินค้า';
  public $filter;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/consign_check';
    $this->load->model('inventory/consign_check_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
    $this->load->model('stock/stock_model');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'code', ''),
      'customer' => get_filter('customer', 'customer', ''),
      'zone' => get_filter('zone', 'zone', ''),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', ''),
      'status' => get_filter('status', 'status', 'all'),
      'valid' => get_filter('valid', 'valid', 'all'),
      'consign_code' => get_filter('consign_code', 'consign_code', '')
    );

    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->consign_check_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs = $this->consign_check_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if(!empty($docs))
    {
      foreach($docs as $doc)
      {
        $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
      }
    }

    $filter['docs'] = $docs;

		$this->pagination->initialize($init);
    $this->load->view('inventory/consign_check/consign_check_list', $filter);
  }



  public function add_new()
  {
    $this->load->view('inventory/consign_check/consign_check_add');
  }




  //--- add new document
  public function add()
  {
    $sc = TRUE;

    //--- check is form submited
    if($this->input->post())
    {

      $date = db_date($this->input->post('date_add'), TRUE);
      $customer_code = $this->input->post('customer_code');
      $customer_name = $this->input->post('customer');
      $zone = $this->zone_model->get($this->input->post('zone_code'));
      $remark = $this->input->post('remark');

      $code = $this->get_new_code($date);
      $arr = array(
        'code' => $code,
        'customer_code' => $customer_code,
        'customer_name' => $customer_name,
        'zone_code' => $zone->code,
        'warehouse_code' => $zone->warehouse_code,
        'user' => get_cookie('uname'),
        'date_add' => $date,
        'remark' => $remark
      );

      $this->db->trans_begin();

      if($this->consign_check_model->add($arr))
      {
        //---- get stock balance in zone
        $stocks = $this->stock_model->get_all_stock_in_zone($zone->code);
        if(!empty($stocks))
        {
          foreach($stocks as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            $ds = array(
            'check_code' => $code,
            'product_code' => $rs->product_code,
            'product_name' => $this->products_model->get_name($rs->product_code),
            'stock_qty' => $rs->qty
            );

            if( ! $this->consign_check_model->add_detail($ds))
            {
              $sc = FALSE;
              $this->error = "เพิ่มยอดตั้งต้นไม่สำเร็จ";
            }

          } //-- edn foreach

        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เพิ่มเอกสารไม่สำเร็จ";
      }

      if($sc === FALSE)
      {
        $this->db->trans_rollback();
      }
      else
      {
        $this->db->trans_commit();
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูล";
    }

    if($sc === TRUE)
    {
      redirect($this->home.'/edit/'.$code);
    }
    else
    {
      set_error($this->error);
      redirect($this->home.'/add_new');
    }
  }




  //---- edit document details
  public function edit($code)
  {
    $doc = $this->consign_check_model->get($code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);

    $details = $this->consign_check_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $ds['doc'] = $doc;
    $ds['details'] = $details;

    $this->load->view('inventory/consign_check/consign_check_edit', $ds);
  }




  public function view_detail($code)
  {
    $doc = $this->consign_check_model->get($code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);

    $details = $this->consign_check_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $ds['doc'] = $doc;
    $ds['details'] = $details;

    $this->load->view('inventory/consign_check/consign_check_view_detail', $ds);
  }


  //---- do receive item
  public function check_item($code)
  {
    $sc = TRUE;
    $id_box = $this->input->post('id_box');
    $barcode = $this->input->post('barcode');
    $qty = $this->input->post('qty');

    $pd = $this->products_model->get_product_by_barcode($barcode);
    if(empty($pd))
    {
      $sc = FALSE;
      $this->error = "ไม่พบสินค้าในระบบ กรุณาตรวจสอบบาร์โค้ด";
    }
    else
    {
      $this->db->trans_start();

      $ds = $this->consign_check_model->get_detail($code, $pd->code);
      if(empty($ds))
      {
        $sc = FALSE;
        $this->error = "ไม่พบสินค้า '{$pd->code}' ในโซน";
      }
      else
      {
        if($ds->stock_qty < ($ds->qty + $qty))
        {
          $sc = FALSE;
          $this->error = "{$pd->code} : จำนวนสินค้าเกินกว่ายอดที่มีในโซน";
        }
        else
        {
          if( ! $this->consign_check_model->update_check_detail($code, $pd->code, $qty))
          {
            $sc = FALSE;
            $this->error = "{$pd->code} : บันทึกจำนวนตรวจนับไม่สำเร็จ";
          }

          if( ! $this->consign_check_model->update_box_qty($id_box, $code, $pd->code, $qty))
          {
            $sc = FALSE;
            $this->error = "{$pd->code} : บันทึกยอดตรวจนับลงกล่องไม่สำเร็จ";
          }
        }
      }

      $this->db->trans_complete();
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  //---- get checked detail
  public function get_checked_detail($code)
  {

    $product_code = $this->input->get('product_code');
    $sc = array(
      'pdCode' => $product_code
    );

    $details = $this->consign_check_model->get_consign_box_product_details($code, $product_code);
    if(!empty($details))
    {
      $ds = array();
      foreach($details as $rs)
      {
        $arr = array(
          'box' => 'กล่องที่ '.$rs->box_no,
          'qty' => $rs->qty,
          'id_box' => $rs->id_box,
          'pdCode' => $rs->product_code
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      $ds = array('nodata' => 'nodata');
    }

    $sc['rows'] = $ds;
    echo json_encode($sc);
  }




  //--- remove checked detail in this box
  public function remove_checked_item($code)
  {
    $sc = TRUE;
    $product_code = $this->input->post('product_code');
    $id_box = $this->input->post('id_box');
    $detail = $this->consign_check_model->get_consign_box_detail($id_box,$code, $product_code);
    if(!empty($detail))
    {
      $this->db->trans_start();
      $qty = $detail->qty * (-1);

      if( ! $this->consign_check_model->update_check_detail($code, $product_code, $qty))
      {
        $sc = FALSE;
        $this->error = "{$pd->code} : ปรับปรุงตรวจนับไม่สำเร็จ";
      }

      if( ! $this->consign_check_model->delete_box_qty($id_box, $code, $product_code))
      {
        $sc = FALSE;
        $this->error = "{$pd->code} : ปรับปรุงยอดตรวจนับในกล่องไม่สำเร็จ";
      }

      $this->db->trans_complete();
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  public function get_box($code, $barcode)
  {
    $sc = TRUE;
    $box = $this->consign_check_model->get_box($code, $barcode);

    //--- if box not exists
    if(empty($box))
    {
      //--- so add new box
      if($this->consign_check_model->add_box($code, $barcode))
      {
        //--- try to get box again
        $box = $this->consign_check_model->get_box($code, $barcode);
      }
    }

    //--- Now box must not empty
    if(!empty($box))
    {
      $arr = array(
        'id_box' => $box->id,
        'box_no' => $box->box_no,
        'qty' => $this->consign_check_model->get_box_qty($box->id, $code)
      );
    }
    else
    {
      $sc = FALSE;
      $this->error = "เพิ่มกล่องใหม่ไม่สำเร็จ";
    }

    echo $sc === TRUE ? json_encode($arr) : $this->error;
  }



  public function get_box_list($code)
  {
    $ds = array();
    $box_list = $this->consign_check_model->get_box_list($code);

    if(!empty($box_list))
    {
      foreach($box_list as $rs)
      {
        $arr = array(
          'id_box' => $rs->id,
          'barcode' => $rs->code,
          'box_no' => 'กล่องที่ '.$rs->box_no
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      $ds[] = array('nodata' => 'nodata');
    }

    echo json_encode($ds);
  }




  public function close_consign_check($code)
  {
    $sc = TRUE;

    $doc = $this->consign_check_model->get($code);

    if($doc->valid == 0 && $doc->status == 0)
    {
      //--- change status to 1 (saved)
      if(! $this->consign_check_model->change_status($code, 1))
      {
        $sc = FALSE;
        $this->error = "ปิดการตรวจนับไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
      }
    }
    else
    {
      $sc = FALSE;
      if($doc->valid == 1)
      {
        $this->error = "เอกสารถูกดึงไปตัดยอดขายแล้ว";
      }

      if($doc->status != 0)
      {
        $this->error = $doc->status == 2 ? "เอกสารถูกยกเลิกไปแล้ว" : "เอกสารถูกบันทึกไปแล้ว";
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  //--- Unsave document
  public function open_consign_check($code)
  {
    $sc = TRUE;

    $doc = $this->consign_check_model->get($code);

    if($doc->valid == 0 && $doc->status == 1)
    {
      //--- change status to 0 (not save)
      if(! $this->consign_check_model->change_status($code, 0))
      {
        $sc = FALSE;
        $this->error = "เปิดการตรวจนับไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
      }
    }
    else
    {
      $sc = FALSE;
      if($doc->valid == 1)
      {
        $this->error = "เอกสารถูกดึงไปตัดยอดขายแล้ว";
      }

      if($doc->status == 2)
      {
        $this->error = "เอกสารถูกยกเลิกไปแล้ว";
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function update_header($code)
  {
    $sc = TRUE;

    $date_add = db_date($this->input->post('date_add'), TRUE);
    $remark = $this->input->post('remark');

    $arr = array(
      'date_add' => $date_add,
      'remark' => $remark
    );


    if( ! $this->consign_check_model->update($code, $arr))
    {
      $sc = FALSE;
      $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function reload_stock($code)
  {
    $sc = TRUE;
    $doc = $this->consign_check_model->get($code);
    if( ! empty($doc))
    {
      $stocks = $this->stock_model->get_all_stock_in_zone($doc->zone_code);
      if(!empty($stocks))
      {
        //---- ทำยอดตั้งต้นให้เป็น 0 ก่อน
        //--- ตัวไหนมียอดจะถูก update ทีหลัง
        //--- ตัวไหนไม่มียอดจะเป็น 0
        //--- ตัวไหนที่ไม่มีรายการ จะถูกเพิ่ม
        $this->db->trans_start();

        //--- set all stock_qty = 0;
        $this->consign_check_model->reset_stock_qty($code);

        foreach($stocks as $rs)
        {
          if($sc === FALSE)
          {
            break;
          }
          $detail = $this->consign_check_model->get_detail($code, $rs->product_code);
          if(!empty($detail))
          {
            $this->consign_check_model->update_stock_qty($detail->id, $rs->qty);
          }
          else
          {
            $ds = array(
            'check_code' => $code,
            'product_code' => $rs->product_code,
            'product_name' => $this->products_model->get_name($rs->product_code),
            'stock_qty' => $rs->qty
            );

            if( ! $this->consign_check_model->add_detail($ds))
            {
              $sc = FALSE;
              $this->error = "เพิ่มยอดตั้งต้นไม่สำเร็จ";
            }
          }

        } //-- edn foreach

        $this->db->trans_complete();

        //--- delete 0 stock_qty and 0 checked
        $this->consign_check_model->delete_no_item_details($code);
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function clear_all_details($code)
  {
    $sc = $this->delete_all_details($code);

    echo $sc === TRUE ? 'success' : $this->error;
  }


  private function delete_all_details($code)
  {
    $sc = TRUE;
    $doc = $this->consign_check_model->get($code);

    if($doc->valid == 0)
    {
      if($doc->status == 0)
      {
        $this->db->trans_begin();
        //--- 1. ลบรายการในกล่องทั้งหมด
        if( ! $this->consign_check_model->delete_all_box_details($code))
        {
          $sc = FALSE;
          $this->error = "ลบรายการสินค้าในกล่องไม่สำเร็จ";
        }

        //--- 2. ลบกล่องทั้งหมด
        if($sc === TRUE)
        {
          if( ! $this->consign_check_model->delete_all_box($code))
          {
            $sc = FALSE;
            $this->error = "ลบกล่องไม่สำเร็จ";
          }
        }

        //--- 3. ลบรายการรับเข้าและรายการตั้งต้นทั้งหมด
        if($sc === TRUE)
        {
          if( ! $this->consign_check_model->delete_all_details($code))
          {
            $sc = FALSE;
            $this->error = "ลบรายการตั้งต้นและรายการตรวจนับไม่สำเร็จ";
          }
        }

        if($sc === FALSE)
        {
          $this->db->trans_rollback();
        }
        else
        {
          $this->db->trans_commit();
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถลบข้อมูลได้ เนื่องจากเอกสารถูกบันทึกแล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่สามารถลบข้อมูลได้เนื่องจากมีการดึงไปตัดยอดขายแล้ว";
    }

    return $sc;
  }




  //---- ยกเลิกเอกสาร
  public function cancle($code)
  {
    $sc = TRUE;
    if($this->pm->can_delete)
    {
      //--- delete all data
      if( ! $this->delete_all_details($code))
      {
        $sc = FALSE;
      }

      if($sc === TRUE)
      {
        //--- change status = 2 (cancle)
        if( ! $this->consign_check_model->change_status($code, 2))
        {
          $sc = FALSE;
          $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "คุณไม่มีสิทธิ์ในการยกเลิกเอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  public function print($code, $id_box)
  {
    $this->load->library('printer');
    $doc = $this->consign_check_model->get($code);
    if(!empty($doc))
    {
      $zone = $this->zone_model->get($doc->zone_code);
      $doc->zone_name = $zone->name;
      $doc->warehouse_name = $zone->warehouse_name;
    }

    $details = $this->consign_check_model->get_consign_box_details($id_box, $code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->product_name = $this->products_model->get_name($rs->product_code);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_consign_box', $ds);
  }

  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_CONSIGN_CHECK');
    $run_digit = getConfig('RUN_DIGIT_CONSIGN_CHECK');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->consign_check_model->get_max_code($pre);
    if(!empty($code))
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
    $filter = array('code', 'customer', 'zone', 'from_date', 'to_date', 'status', 'valid', 'consign_code' );
    clear_filter($filter);
  }



} //---- end class
 ?>
