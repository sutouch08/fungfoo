<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends PS_Controller
{
  public $menu_code = 'ICTRWH';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'TRANFER';
	public $title;
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/transfer';
    $this->load->model('inventory/transfer_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('stock/stock_model');
    $this->title = label_value('transfer_title');
  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'code', ''),
      'from_warehouse'  => get_filter('from_warehouse', 'from_warehouse', ''),
      'user'      => get_filter('user', 'user', ''),
      'to_warehouse'  => get_filter('to_warehouse', 'to_warehouse', ''),
      'from_date' => get_filter('fromDate', 'fromDate', ''),
      'to_date'   => get_filter('toDate', 'toDate', ''),
      'status' => get_filter('status', 'status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->transfer_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs     = $this->transfer_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($docs))
    {
      foreach($docs as $rs)
      {
        $rs->from_warehouse_name = $this->warehouse_model->get_name($rs->from_warehouse);
        $rs->to_warehouse_name = $this->warehouse_model->get_name($rs->to_warehouse);
      }
    }

    $filter['docs'] = $docs;
		$this->pagination->initialize($init);
    $this->load->view('transfer/transfer_list', $filter);
  }



  public function view_detail($code)
  {
    $doc = $this->transfer_model->get($code);
    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'barcode' => FALSE
    );

    $this->load->view('transfer/transfer_view', $ds);
  }


  public function add_new()
  {
    $this->load->view('transfer/transfer_add');
  }


  public function add()
  {
    if($this->input->post('date'))
    {
      $date_add = db_date($this->input->post('date'), TRUE);
      $from_warehouse = $this->input->post('from_warehouse_code');
      $to_warehouse = $this->input->post('to_warehouse_code');
      $remark = $this->input->post('remark');
      $bookcode = getConfig('BOOK_CODE_TRANSFER');
      $code = $this->get_new_code($date_add);

      $ds = array(
        'code' => $code,
        'bookcode' => $bookcode,
        'from_warehouse' => $from_warehouse,
        'to_warehouse' => $to_warehouse,
        'remark' => $remark,
        'user' => get_cookie('uname'),
        'date_add' => $date_add
      );

      $rs = $this->transfer_model->add($ds);
      if($rs === TRUE)
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
      set_error(label_value('doc_not_found'));
      redirect($this->home.'/add_new');
    }
  }



  public function edit($code, $barcode = '')
  {
    $doc = $this->transfer_model->get($code);
    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'barcode' => $barcode == '' ? FALSE : TRUE
    );

    $this->load->view('transfer/transfer_edit', $ds);
  }



  public function update($code)
  {
    $arr = array(
      'date_add' => db_date($this->input->post('date_add'), TRUE),
      'from_warehouse' => $this->input->post('from_warehouse'),
      'to_warehouse' => $this->input->post('to_warehouse'),
      'remark' => $this->input->post('remark'),
      'update_user' => get_cookie('uname')
    );

    $rs = $this->transfer_model->update($code, $arr);

    if($rs)
    {
      echo 'success';
    }
    else
    {
      echo label_value('update_fail');
    }
  }




  public function check_temp_exists($code)
  {
    $temp = $this->transfer_model->is_exists_temp($code);
    if($temp === TRUE)
    {
      echo 'exists';
    }
    else
    {
      echo 'not_exists';
    }
  }



  public function save_transfer($code)
  {
    $sc = TRUE;
    $this->db->trans_begin();
    //--- change state to 1
    $this->transfer_model->set_status($code, 1);
    $this->transfer_model->valid_all_detail($code, 1);

    $details = $this->transfer_model->get_details($code);
    $doc = $this->transfer_model->get($code);
    if(!empty($details))
    {
      $this->load->model('inventory/movement_model');
      //--- global config for allow stock less than zero
      $g_auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;

      foreach($details as $rs)
      {
        //--- 2. move stock out
        $auz = $g_auz === TRUE ? TRUE : $this->warehouse_model->is_auz($doc->from_warehouse);
        $in_zone = $this->stock_model->get_stock_zone($rs->from_zone, $rs->product_code);
        if(($in_zone < $rs->qty) && $auz === FALSE)
        {
          $sc = FALSE;
          $message = $rs->product_code.' : สต็อกคงเหลือน้อยกว่ายอดที่ต้องการย้าย';
        }
        else
        {
          //----- ตัดสต็อกออกจากโซนต้นทาง
          if($this->stock_model->update_stock_zone($rs->from_zone, $rs->product_code, (-1) * $rs->qty) === FALSE)
          {
            $sc = FALSE;
            $message = 'ตัดยอดจากโซนต้นทางไม่สำเร็จ';
          }
        }


        //--- 2.1  update movement
        $move_out = array(
          'reference' => $code,
          'warehouse_code' => $doc->from_warehouse,
          'zone_code' => $rs->from_zone,
          'product_code' => $rs->product_code,
          'move_in' => 0,
          'move_out' => $rs->qty,
          'date_add' => $doc->date_add
        );

        //--- move out
        if($this->movement_model->add($move_out) === FALSE)
        {
          $sc = FALSE;
          $message = 'cannot record movement (out)';
          break;
        }

        //--- 2.2 เพิ่มสต็อกเข้าโซนปลายทาง
        if($this->stock_model->update_stock_zone($rs->to_zone, $rs->product_code, $rs->qty) === FALSE)
        {
          $sc = FALSE;
          $message = 'เพิ่มสต็อกเข้าโซนปลายทางไม่สำเร็จ';
        }

        //--- 2.3 บันทึก movement เข้าปลายทาง
        $move_in = array(
          'reference' => $code,
          'warehouse_code' => $doc->to_warehouse,
          'zone_code' => $rs->to_zone,
          'product_code' => $rs->product_code,
          'move_in' => $rs->qty,
          'move_out' => 0,
          'date_add' => $doc->date_add
        );

        //--- move in
        if($this->movement_model->add($move_in) === FALSE)
        {
          $sc = FALSE;
          $message = 'cannot record movement (in)';
          break;
        }
      }
    }

    if($sc === TRUE)
    {
      $this->db->trans_commit();
    }
    else
    {
      $this->db->trans_rollback();
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function unsave_transfer($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
    $doc = $this->transfer_model->get($code);
    $details = $this->transfer_model->get_details($code);
    if($doc->status == 1)
    {
      $this->db->trans_begin();
      if(!empty($details))
      {
        $g_auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;
        foreach($details as $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          //--- 1. ดึงสต็อกออกจากโซนปลายทาง
          $auz = $g_auz === TRUE ? TRUE : $this->warehouse_model->is_auz($doc->to_warehouse);
          $in_zone = $this->stock_model->get_stock_zone($rs->to_zone, $rs->product_code);
          if($in_zone < $rs->qty && $auz === FALSE)
          {
            $sc = FALSE;
            $this->error = $rs->product_code.' : สินค้าในโซนไม่เพียงพอ';
            break;
          }
          else
          {
            if($this->stock_model->update_stock_zone($rs->to_zone, $rs->product_code, (-1) * $rs->qty) === FALSE)
            {
              $sc = FALSE;
              $this->error = 'ตัดสต็อกออกจากโซน '.$rs->to_zone.' ไม่สำเร็จ';
            }
          }

          //--- 2. drop movement ขาเข้า
          if($this->movement_model->drop_move_in($doc->code, $rs->product_code, $rs->to_zone) === FALSE)
          {
            $sc = FALSE;
            $this->error = $rs->product_code.' : ลบ movement (in) ไม่สำเร็จ';
            break;
          }

          //--- 3. add stock back to zone
          if($this->stock_model->update_stock_zone($rs->from_zone, $rs->product_code, $rs->qty) === FALSE)
          {
            $sc = FALSE;
            $this->error = $rs->product_code.' : เพิ่มสต็อกกลับโซน '.$rs->from_zone.' ไม่สำเร็จ';
            break;
          }

          //--- 4. ลบ movement out
          if($this->movement_model->drop_move_out($doc->code, $rs->product_code, $rs->from_zone) === FALSE)
          {
            $sc = FALSE;
            $this->error = $rs->product_code.' : ลบ movement (out) ไม่สำเร็จ';
            break;
          }

          //--- 5. valid detail
          if($this->transfer_model->valid_detail($rs->id, 0) === FALSE)
          {
            $sc = FALSE;
            $this->error = $rs->product_code.' : เปลี่ยนสถานะรายการไม่สำเร็จ';
            break;
          }

        }
      }

      if($sc === TRUE)
      {
        //--- 6. เปลี่ยนสถานะเอกสาร
        if($this->transfer_model->set_status($code, 0) === FALSE)
        {
          $sc = FALSE;
          $this->error = 'เปลี่ยนสถานะเอกสารไมสำเร็จ';
        }
      }


      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function add_to_transfer()
  {
    $sc = TRUE;
    $code = $this->input->post('transfer_code');
    if($code)
    {
      $this->load->model('masters/products_model');

      $from_zone = $this->input->post('from_zone');
      $to_zone = $this->input->post('to_zone');
      $trans_products = $this->input->post('trans_products');
      if(!empty($trans_products))
      {
        $this->db->trans_start();
        foreach($trans_products as $item => $qty)
        {
          $id = $this->transfer_model->get_id($code, $item, $from_zone, $to_zone);
          if(!empty($id))
          {
            $this->transfer_model->update_qty($id, $qty);
          }
          else
          {
            $arr = array(
              'transfer_code' => $code,
              'product_code' => $item,
              'product_name' => $this->products_model->get_name($item),
              'from_zone' => $from_zone,
              'to_zone' => $to_zone,
              'qty' => $qty
            );

            $this->transfer_model->add_detail($arr);
          }
        }

        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE)
        {
          $sc = FALSE;
          $message = label_value('insert_fail');
        }
      }
    }

    echo $sc === TRUE ? 'success' : $message;

  }




  public function add_to_temp()
  {
    $sc = TRUE;

    if($this->input->post('transfer_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('transfer_code');
      $zone_code = $this->input->post('from_zone');
      $barcode = trim($this->input->post('barcode'));
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        $product_code = $item->code;
        $stock = $this->stock_model->get_stock_zone($zone_code, $product_code);
        //--- จำนวนที่อยู่ใน temp
        $temp_qty = $this->transfer_model->get_temp_qty($code, $product_code, $zone_code);
        //--- จำนวนที่อยู่ใน transfer_detail และยังไม่ valid
        $transfer_qty = $this->transfer_model->get_transfer_qty($code, $product_code, $zone_code);
        //--- จำนวนที่โอนได้คงเหลือ
        $cqty = $stock - ($temp_qty + $transfer_qty);

        if($qty <= $cqty)
        {
          $arr = array(
            'transfer_code' => $code,
            'product_code' => $product_code,
            'zone_code' => $zone_code,
            'qty' => $qty
          );

          if($this->transfer_model->update_temp($arr) === FALSE)
          {
            $sc = FALSE;
            $message = 'ย้ายสินค้าเข้า temp ไม่สำเร็จ';
          }

        }
        else
        {
          $sc = FALSE;
          $message = 'ยอดในโซนไม่เพียงพอ';
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'บาร์โค้ดไม่ถูกต้อง';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }




  public function move_to_zone()
  {
    $sc = TRUE;
    if($this->input->post('transfer_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('transfer_code');
      $barcode = trim($this->input->post('barcode'));
      $to_zone = $this->input->post('zone_code');
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        //--- ย้ายจำนวนใน temp มาเพิ่มเข้า transfer detail
        //--- โดยเอา temp ออกมา(อาจมีหลายรายการ เพราะอาจมาจากหลายโซน
        //--- ดึงรายการจาก temp ตามรายการสินค้า (อาจมีหลายบรรทัด)
        $temp = $this->transfer_model->get_temp_product($code, $item->code);
        if(!empty($temp))
        {
          //--- เริ่มใช้งาน transction
          $this->db->trans_begin();
          foreach($temp as $rs)
          {
            if($qty > 0 && $rs->qty > 0)
            {
              //---- ยอดที่ต้องการย้าย น้อยกว่าหรือเท่ากับ ยอดใน temp มั้ย
              //---- ถ้าใช่ ใช้ยอดที่ต้องการย้ายได้เลย
              //---- แต่ถ้ายอดที่ต้องการย้ายมากว่ายอดใน temp แล้วยกยอดที่เหลือไปย้ายในรอบถัดไป(ถ้ามี)
              $temp_qty = $qty <= $rs->qty ? $qty : $rs->qty;
              $id = $this->transfer_model->get_id($code, $item->code, $rs->zone_code, $to_zone);
              //--- ถ้าพบไอดีให้แก้ไขจำนวน
              if(!empty($id))
              {
                if($this->transfer_model->update_qty($id, $temp_qty) === FALSE)
                {
                  $sc = FALSE;
                  $message = 'แก้ไขยอดในรายการไม่สำเร็จ';
                  break;
                }
              }
              else
              {
                //--- ถ้ายังไม่มีรายการ ให้เพิ่มใหม่
                $ds = array(
                  'transfer_code' => $code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'from_zone' => $rs->zone_code,
                  'to_zone' => $to_zone,
                  'qty' => $temp_qty
                );

                if($this->transfer_model->add_detail($ds) === FALSE)
                {
                  $sc = FALSE;
                  $message = 'เพิ่มรายการไม่สำเร็จ';
                  break;
                }
              }
              //--- ถ้าเพิ่มหรือแก้ไข detail เสร็จแล้ว ทำการ ลดยอดใน temp ตามยอดที่เพิ่มเข้า detail
              if($this->transfer_model->update_temp_qty($rs->id, ($temp_qty * -1)) === FALSE)
              {
                $sc = FALSE;
                $message = 'แก้ไขยอดใน temp ไม่สำเร็จ';
                break;
              }

              //--- ตัดยอดที่ต้องการย้ายออก เพื่อยกยอดไปรอบต่อไป
              $qty -= $temp_qty;
            }
            else
            {
              break;
            } //-- end if qty > 0

            //--- ลบ temp ที่ยอดเป็น 0
            $this->transfer_model->drop_zero_temp();
          } //--- end foreach


          //--- เมื่อทำงานจนจบแล้ว ถ้ายังเหลือยอด แสดงว่ายอดที่ต้องการย้ายเข้า มากกว่ายอดที่ย้ายออกมา
          //--- จะให้ทำกร roll back แล้วแจ้งกลับ
          if($qty > 0)
          {
            $sc = FALSE;
            $message = 'ยอดที่ย้ายเข้ามากกว่ายอดที่ย้ายออกมา';
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
          $message = 'ไม่พบรายการใน temp';
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'บาร์โค้ดไม่ถูกต้อง';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }




  public function is_exists_detail($code)
  {
    $detail = $this->transfer_model->is_exists_detail($code);
    $temp = $this->transfer_model->is_exists_temp($code);

    if($detail === FALSE && $temp === FALSE)
    {
      echo 'not_exists';
    }
    else
    {
      echo 'exists';
    }
  }



  public function get_temp_table($code)
  {
    $ds = array();
    $temp = $this->transfer_model->get_transfer_temp($code);
    if(!empty($temp))
    {
      $no = 1;
      foreach($temp as $rs)
      {
        $arr = array(
          'no' => $no,
          'id' => $rs->id,
          'barcode' => $rs->barcode,
          'products' => $rs->product_code,
          'from_zone' => $rs->zone_code,
          'fromZone' => $this->zone_model->get_name($rs->zone_code),
          'qty' => $rs->qty
        );

        array_push($ds, $arr);
        $no++;
      }
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }




  public function get_transfer_table($code)
  {
    $ds = array();
    $details = $this->transfer_model->get_details($code);

    if(!empty($details))
    {
      $no = 1;
      $total_qty = 0;
      foreach($details as $rs)
      {
        $btn_delete = '';
        if($this->pm->can_add OR $this->pm->can_edit && $rs->valid == 0)
        {
          $btn_delete .= '<button type="button" class="btn btn-minier btn-danger" ';
          $btn_delete .= 'onclick="deleteMoveItem('.$rs->id.', \''.$rs->product_code.'\')">';
          $btn_delete .= '<i class="fa fa-trash"></i></button>';
        }

        $arr = array(
          'id' => $rs->id,
          'no' => $no,
          'barcode' => $rs->barcode,
          'products' => $rs->product_code,
          'from_zone' => $this->zone_model->get_name($rs->from_zone),
          'to_zone' => $this->zone_model->get_name($rs->to_zone),
          'qty' => number($rs->qty),
          'btn_delete' => $btn_delete
        );

        array_push($ds, $arr);
        $no++;
        $total_qty += $rs->qty;
      } //--- end foreach

      $arr = array(
        'total' => number($total_qty)
      );

      array_push($ds, $arr);
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }



  public function get_transfer_zone($warehouse = '')
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $zone = $this->zone_model->search($txt, $warehouse);
    if(!empty($zone))
    {
      foreach($zone as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบโซน';
    }

    echo json_encode($sc);
  }



  public function get_product_in_zone()
  {
    $sc = array();

    if($this->input->get('zone_code'))
    {
      $this->load->model('masters/products_model');

      $zone_code = $this->input->get('zone_code');
      $transfer_code = $this->input->get('transfer_code');
      $stock = $this->stock_model->get_all_stock_in_zone($zone_code);
      if(!empty($stock))
      {
        $no = 1;
        foreach($stock as $rs)
        {
          //--- จำนวนที่อยู่ใน temp
          $temp_qty = $this->transfer_model->get_temp_qty($transfer_code, $rs->product_code, $zone_code);
          //--- จำนวนที่อยู่ใน transfer_detail และยังไม่ valid
          $transfer_qty = $this->transfer_model->get_transfer_qty($transfer_code, $rs->product_code, $zone_code);
          //--- จำนวนที่โอนได้คงเหลือ
          $qty = $rs->qty - ($temp_qty + $transfer_qty);

          if($qty > 0)
          {
            $arr = array(
              'no' => $no,
              'barcode' => $this->products_model->get_barcode($rs->product_code),
              'products' => $rs->product_code,
              'qty' => $qty
            );

            array_push($sc, $arr);
            $no++;
          }
        }
      }
      else
      {
        array_push($sc, array("nodata" => "nodata"));
      }
      echo json_encode($sc);
    }
  }





  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_TRANSFER');
    $run_digit = getConfig('RUN_DIGIT_TRANSFER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->transfer_model->get_max_code($pre);
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




  public function delete_detail($id)
  {
    $rs = $this->transfer_model->drop_detail($id);
    if($rs === TRUE)
    {
      echo 'success';
    }
    else
    {
      echo $this->db->error();
    }
  }




  public function delete_transfer($code)
  {
    $this->load->model('inventory/movement_model');

    $this->db->trans_start();

    //--- clear temp
    $this->transfer_model->drop_all_temp($code);
    //--- delete detail
    $this->transfer_model->drop_all_detail($code);
    //--- drop movement
    $this->movement_model->drop_movement($code);
    //--- change status to 2 (cancled)
    $this->transfer_model->set_status($code, 2);

    $this->db->trans_complete();
    if($this->db->trans_status() === FALSE)
    {
      echo $this->db->error();
    }
    else
    {
      echo 'success';
    }
  }




  public function print_transfer($code)
  {
    $this->load->library('printer');
    $doc = $this->transfer_model->get($code);
    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_transfer', $ds);
  }



  private function do_export($code)
  {
    $doc = $this->transfer_model->get($code);
    $tr = $this->transfer_model->get_sap_transfer_doc($code);
    if(!empty($doc))
    {
      if(empty($tr) OR $tr->DocStatus == 'O')
      {
        if($doc->status == 1)
        {
          $currency = getConfig('CURRENCY');
          $vat_rate = getConfig('SALE_VAT_RATE');
          $vat_code = getConfig('SALE_VAT_CODE');

          $ds = array(
            'U_ECOMNO' => $doc->code,
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => $doc->date_add,
            'DocDueDate' => $doc->date_add,
            'CardCode' => NULL,
            'CardName' => NULL,
            'VatPercent' => 0.000000,
            'VatSum' => 0.000000,
            'VatSumFc' => 0.000000,
            'DiscPrcnt' => 0.000000,
            'DiscSum' => 0.000000,
            'DiscSumFC' => 0.000000,
            'DocCur' => $currency,
            'DocRate' => 1,
            'DocTotal' => 0.000000,
            'DocTotalFC' => 0.000000,
            'Filler' => $doc->from_warehouse,
            'ToWhsCode' => $doc->to_warehouse,
            'Comments' => $doc->remark,
            'F_E_Commerce' => (empty($tr) ? 'A' : 'U'),
            'F_E_CommerceDate' => now(),
            'U_BOOKCODE' => $doc->bookcode
          );

          $this->mc->trans_start();

          if(!empty($tr))
          {
            $sc = $this->transfer_model->update_sap_transfer_doc($code, $ds);
          }
          else
          {
            $sc = $this->transfer_model->add_sap_transfer_doc($ds);
          }

          if($sc)
          {
            if(!empty($tr))
            {
              $this->transfer_model->drop_sap_exists_details($code);
            }

            $details = $this->transfer_model->get_details($code);

            if(!empty($details))
            {
              $line = 0;
              foreach($details as $rs)
              {
                $arr = array(
                  'U_ECOMNO' => $rs->transfer_code,
                  'LineNum' => $line,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => $rs->product_name,
                  'Quantity' => $rs->qty,
                  'unitMsr' => NULL,
                  'PriceBefDi' => 0.000000,
                  'LineTotal' => 0.000000,
                  'ShipDate' => $doc->date_add,
                  'Currency' => $currency,
                  'Rate' => 1,
                  'DiscPrcnt' => 0.000000,
                  'Price' => 0.000000,
                  'TotalFrgn' => 0.000000,
                  'FromWhsCod' => $doc->from_warehouse,
                  'WhsCode' => $doc->to_warehouse,
                  'FisrtBin' => $rs->from_zone,
                  'AllocBinC' => $rs->to_zone,
                  'TaxStatus' => 'Y',
                  'VatPrcnt' => 0.000000,
                  'VatGroup' => NULL,
                  'PriceAfVAT' => 0.000000,
                  'VatSum' => 0.000000,
                  'TaxType' => 'Y',
                  'F_E_Commerce' => (empty($tr) ? 'A' : 'U'),
                  'F_E_CommerceDate' => now()
                );

                if( ! $this->transfer_model->add_sap_transfer_detail($arr))
                {
                  $this->error = 'เพิ่มรายการไม่สำเร็จ';
                }

                $line++;
              }
            }
            else
            {
              $this->error = "ไม่พบรายการสินค้า";
            }
          }
          else
          {
            $this->error = "เพิ่มเอกสารไม่สำเร็จ";
          }

          $this->mc->trans_complete();

          if($this->mc->trans_status() === FALSE)
          {
            return FALSE;
          }

          return TRUE;
        }
        else
        {
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $this->error = "เอกสารถูกปิดไปแล้ว";
      }
    }
    else
    {
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    return FALSE;
  }



  public function export_transfer($code)
  {
    if($this->do_export($code) === TRUE)
    {
      echo 'success';
    }
    else
    {
      echo $this->error;
    }
  }


} //--- end class
?>
