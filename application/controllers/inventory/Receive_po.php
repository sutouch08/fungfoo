<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_po extends PS_Controller
{
  public $menu_code = 'ICPURC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title;
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_po';
    $this->load->model('inventory/receive_po_model');
    $this->title = label_value('receive_title');
  }


  public function index()
  {
    $this->load->helper('channels');
    $filter = array(
      'code'    => get_filter('code', 'code', ''),
      'invoice' => get_filter('invoice', 'invoice', ''),
      'po'      => get_filter('po', 'po', ''),
      'vender'  => get_filter('vender', 'vender', ''),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->receive_po_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->receive_po_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->receive_po_model->get_sum_qty($rs->code);
      }
    }

    $filter['document'] = $document;

		$this->pagination->initialize($init);

    $this->load->view('inventory/receive_po/receive_po_list', $filter);
  }



  public function view_detail($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');

    $doc = $this->receive_po_model->get($code);
    if(!empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    }

    $details = $this->receive_po_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }


    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('inventory/receive_po/receive_po_detail', $ds);
  }



  public function print_detail($code)
  {
    $this->load->library('printer');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');

    $doc = $this->receive_po_model->get($code);
    if(!empty($doc))
    {
      $zone = $this->zone_model->get($doc->zone_code);
      $doc->zone_name = $zone->name;
      $doc->warehouse_name = $zone->warehouse_name;
    }

    $details = $this->receive_po_model->get_details($code);

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_received', $ds);
  }


  public function add_details($code)
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $this->load->model('purchase/po_model');
    $details = json_decode($this->input->post('details'));
    $doc = $this->receive_po_model->get($code);
    if(!empty($doc))
    {
      if(!empty($details))
      {
        $this->db->trans_start();
        foreach($details as $rs)
        {
          $row = $this->receive_po_model->get_detail_by_product($code, $rs->product_code);
          if(!empty($row))
          {
            $qty = $row->qty + $rs->qty;
            $arr = array(
              'qty' => $qty,
              'amount' => $qty * $row->price
            );

            $this->receive_po_model->update_detail($row->id, $arr);
          }
          else
          {
            $item = $this->products_model->get($rs->product_code);
            $pod = empty($doc->po_code) ? NULL : $this->po_model->get_detail($doc->po_code, $item->code);
            $price = empty($pod) ? $item->cost : $pod->price;

            $arr = array(
              'receive_code' => $code,
              'po_code' => empty($pod) ? NULL : $pod->po_code,
              'style_code' => $item->style_code,
              'product_code' => $item->code,
              'product_name' => $item->name,
              'price' => $price,
              'qty' => $rs->qty,
              'amount' => $price * $rs->qty,
              'status' => 'N'
            );

            $this->receive_po_model->add_detail($arr);
          }
        }

        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE)
        {
          $sc = FALSE;
          $this->error = label_value('insert_fail');
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
      $this->error = label_value('doc_not_found');
    }


    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function delete_detail($id)
  {
    if($this->receive_po_model->drop_detail($id))
    {
      echo 'success';
    }
    else
    {
      echo label_value('delete_fail');
    }
  }


  public function delete_details($code)
  {
    if($this->receive_po_model->drop_details($code))
    {
      echo 'success';
    }
    else
    {
      echo label_value('delete_fail');
    }
  }



  public function save($code)
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('purchase/po_model');
    $this->load->model('stock/stock_model');

    $auto_close = getConfig('AUTO_CLOSE_PO');
    $doc = $this->receive_po_model->get($code);
    if(empty($doc))
    {
      $sc = FALSE;
      $this->error = label_value('doc_not_found');
    }

    if($sc === TRUE && $doc->status > 0)
    {
      $sc = FALSE;
      $this->error = label_value('invalid_status');
    }

    if($sc === TRUE)
    {
      $details = $this->receive_po_model->get_unsave_details($code);
      if(!empty($details))
      {
        $this->db->trans_start();
        foreach($details as $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          //---- update stock
          if(! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, $rs->qty))
          {
            $sc = FALSE;
            $this->error = "Update stock failed";
          }


          //--- update stock movement
          $arr = array(
            'reference' => $doc->code,
            'warehouse_code' => $doc->warehouse_code,
            'zone_code' => $doc->zone_code,
            'product_code' => $rs->product_code,
            'move_in' => $rs->qty,
            'move_out' => 0,
            'date_add' => $doc->date_add
          );

          if(! $this->movement_model->move_in($doc->code, $rs->product_code, $doc->warehouse_code, $doc->zone_code, $rs->qty, $doc->date_add))
          {
            $sc = FALSE;
            $this->error = 'Insert Movement failed';
          }

          //--- update po
          if(!empty($rs->po_code))
          {
            if(! $this->po_model->update_received($rs->po_code, $rs->product_code, $rs->qty))
            {
              $sc = FALSE;
              $this->error = 'Update PO receive qty failed : '.$rs->po_code;
            }

            //--- change po to partially received
            if(! $this->po_model->change_status($rs->po_code, 2))
            {
              $sc = FALSE;
              $this->error = 'Change PO status failed : '.$rs->po_code;
            }

            //--- close po if complete
            if($auto_close == 1)
            {
              if($this->po_model->is_all_done($rs->po_code))
              {
                if(! $this->po_model->close_po($rs->po_code))
                {
                  $sc = FALSE;
                  $this->error = 'Close PO failed : '.$rs->po_code;
                }
              }
            }
          }

          //--- change status to 'S'
          if(! $this->receive_po_model->update_detail($rs->id, array('status' => 'S')))
          {
            $sc = FALSE;
            $this->error = 'Change item status failed : '.$rs->product_code;
          }

        } //--- end foreach

        //---- change doc status
        //--- 0 = not save, 1 = saved , 2 = Cancle
        if(! $this->receive_po_model->set_status($code, 1))
        {
          $sc = FALSE;
          $this->error = 'Change document status failed : '.$code;
        }

        $this->db->trans_complete();
      }
      else
      {
        $sc = FALSE;
        $this->error = label_value('no_data_found');
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function unsave($code)
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('purchase/po_model');
    $this->load->model('stock/stock_model');

    $doc = $this->receive_po_model->get($code);

    if(empty($doc))
    {
      $sc = FALSE;
      $this->error = label_value('doc_not_found');
    }

    if($sc === TRUE && $doc->status != 1)
    {
      $sc = FALSE;
      $this->error = label_value('invalid_status');
    }

    if($sc === TRUE)
    {
      $details = $this->receive_po_model->get_saved_details($code);
      if(!empty($details))
      {
        $this->db->trans_begin();

        $wh = $this->warehouse_model->get($doc->warehouse_code);
        $gb_auz = getConfig('ALLOW_UNDER_ZERO');
        $auz = $gb_auz == 1 ? TRUE : ($wh->auz == 1 ? TRUE : FALSE);
        foreach($details as $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          //---- update stock

          if($auz OR $this->stock_model->is_enough($doc->zone_code, $rs->product_code, $rs->qty))
          {
            if(! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, $rs->qty*-1))
            {
              $sc = FALSE;
              $this->error = "Update stock failed";
            }

            //--- update stock movement
            if(! $this->movement_model->drop_move_in($doc->code, $rs->product_code, $doc->zone_code))
            {
              $sc = FALSE;
              $this->error = 'Insert Movement failed';
            }

            //--- update po
            if(!empty($rs->po_code))
            {
              if(! $this->po_model->update_received($rs->po_code, $rs->product_code, $rs->qty * -1))
              {
                $sc = FALSE;
                $this->error = 'Update PO receive qty failed : '.$rs->po_code;
              }

              //--- update po status
              if(! $this->po_model->change_status($rs->po_code))
              {
                $sc = FALSE;
                $this->error = 'Change PO status failed : '.$rs->po_code;
              }

              //--- unclose po detail
              $this->po_model->unvalid_detail($rs->po_code, $rs->product_code);

            }

            //--- change status to 'N'
            if(! $this->receive_po_model->update_detail($rs->id, array('status' => 'N')))
            {
              $sc = FALSE;
              $this->error = 'Change item status failed : '.$rs->product_code;
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = label_value('stock_not_enough').' : '.$doc->zone_code .' : '.$rs->product_code;
          }

        } //--- end foreach

        //---- change doc status
        //--- 0 = not save, 1 = saved , 2 = Cancle
        if(! $this->receive_po_model->set_status($code, 0))
        {
          $sc = FALSE;
          $this->error = 'Change document status failed : '.$code;
        }

        if($this->db->trans_status() === TRUE OR $sc === TRUE)
        {
          $this->db->trans_commit();
        }
        else
        {
          $this->db->trans_rollback();
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = label_value('no_data_found');
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function un_close_po($code)
  {
    $sc = TRUE;
    $this->load->model('purchase/po_model');

    $received = $this->po_model->get_sum_received($code); //--- รับแล้วเท่าไร
    $status = $received > 0 ? 2 : 1; //--- ถ้าเคยรับมาแล้วสถานะเป็น part
    return $this->po_model->un_close_po($code, $status);
  }

  public function cancle_received()
  {
    if($this->input->post('code'))
    {
      $this->load->model('inventory/movement_model');
      $this->load->model('purchase/po_model');
      $code = $this->input->post('code');
      $rs = $this->receive_po_model->get($code);
      if(!empty($rs->po_code))
      {
        $po = $this->po_model->get($rs->po_code);
      }

      $details = $this->receive_po_model->get_details($code);
      if(!empty($details))
      {
        $this->db->trans_start();
        foreach($details as $ds)
        {
          //--- ลบรายการรับเข้า
          $this->receive_po_model->drop_detail($ds->id);

          //---- ลบ movement
          $this->movement_model->drop_move_in($rs->code, $ds->product_code, $rs->zone_code);

          //--- update po details
          if($rs->status == 1 && !empty($rs->po_code))
          {
            $this->po_model->update_received($rs->po_code, $ds->product_code, ($ds->qty * (-1)));
            $this->po_model->unvalid_detail($rs->po_code, $ds->product_code);
          }
        }

        $this->receive_po_model->set_status($code, 2); //--- 0 = ยังไม่บันทึก 1 = บันทึกแล้ว 2 = ยกเลิก

        if(!empty($rs->po_code))
        {
          $po_status = $this->po_model->count_received($rs->po_code) > 0 ? 2 : 1;
          $this->po_model->change_status($rs->po_code, $po_status);
        }

        $this->db->trans_complete();
      }

      if($this->db->trans_status() === FALSE)
      {
        echo label_value('cancle_fail');
      }
      else
      {
        echo 'success';
      }
    }
    else
    {
      echo label_value('doc_not_found');
    }

  }



  public function get_po_details()
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $this->load->model('purchase/po_model');
    $po_code = $this->input->get('po_code');
    $po = $this->po_model->get($po_code);
    if(!empty($po))
    {
      if($po->status == 0 OR $po->status == 3 OR $po->status == 4)
      {
        $sc = FALSE;
        $this->error = label_value('po_error');
      }
      else
      {
        $details = $this->po_model->get_details($po_code);
        //$rate = (getConfig('RECEIVE_OVER_PO') * 0.01);
        $ds = array();

        if(!empty($details))
        {
          $no = 1;
          foreach($details as $rs)
          {
            $backlogs = ($rs->qty - $rs->received) < 0 ? 0 : $rs->qty - $rs->received;
            //$limit = $rs->qty + ($rs->qty* $rate);
            $arr = array(
              'no' => $no,
              //'barcode' => $this->products_model->get_barcode($rs->product_code),
              'pdCode' => $rs->product_code,
              'pdName' => $rs->product_name,
              'price' => $rs->price,
              //'qty' => number($rs->qty),
              //'limit' => ($limit - $rs->received) < 0 ? 0 : $limit - $rs->received,
              'backlogs' => number($backlogs)
            );

            array_push($ds, $arr);
            $no++;
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = label_value('no_content');
        }
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = label_value('doc_not_found');
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



  public function edit($code)
  {
    $this->load->model('masters/zone_model');
    $document = $this->receive_po_model->get($code);
    if(!empty($document))
    {
      $zone = $this->zone_model->get($document->zone_code);
      if(!empty($zone))
      {
        $document->zone_name = $zone->name;
      }
      else
      {
        $document->zone_name = '';
      }
    }

    $details = $this->receive_po_model->get_details($code);

    $ds['doc'] = $document;
    $ds['details'] = $details;
    $this->load->view('inventory/receive_po/receive_po_edit', $ds);
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $this->load->model('purchase/po_model');
      $this->load->model('masters/vender_model');
      $this->load->model('masters/zone_model');

      $date_add = db_date($this->input->post('date_add'), TRUE);
      $invoice = $this->input->post('invoice');
      $remark = $this->input->post('remark');

      $vender = $this->vender_model->get($this->input->post('venderCode'));
      $po = $this->po_model->get($this->input->post('poCode'));
      $zone = $this->zone_model->get($this->input->post('zoneCode'));
      $code = $this->input->post('code');

      if(empty($vender))
      {
        $sc = FALSE;
        $this->error = 'รหัสผู้จำหน่ายไม่ถูกต้อง';
      }

      if(empty($invoice))
      {
        $sc = FALSE;
        $this->error = 'กรุณาระบุใบส่งสินค้า';
      }

      if(!empty($po))
      {
        if($po->status == 0)
        {
          $sc = FALSE;
          $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ยังไม่บันทึกใบสั่งซื้อ';
        }
        else if($po->status == 3)
        {
          $sc = FALSE;
          $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ใบสั่งซื้อถูกปิดแล้ว';
        }
        else if($po->status == 4)
        {
          $sc = FALSE;
          $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ใบสั่งซื้อถูกยกเลิกแล้ว';
        }
      }

      if(empty($zone))
      {
        $sc = FALSE;
        $this->error = 'รหัสโซนไม่ถูกต้อง';
      }

      if($sc === TRUE)
      {
        $arr = array(
          'vender_code' => $vender->code,
          'vender_name' => $vender->name,
          'po_code' => empty($po) ? NULL : $po->code,
          'invoice_code' => $invoice,
          'zone_code' => $zone->code,
          'warehouse_code' => $zone->warehouse_code,
          'remark' => $remark,
          'update_user' => get_cookie('uname')
        );

        if(!$this->receive_po_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = label_value('update_fail');
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = label_value('doc_not_found');
    }

    if($sc === TRUE)
    {
      $ds = array('status' => 'success', 'message' => $code);
    }
    else
    {
      $ds = array('status' => 'error', 'message' => $this->error);
    }

    echo json_encode($ds);
  }


  public function add_new()
  {
    $this->load->view('inventory/receive_po/receive_po_add');
  }


  public function add()
  {
    $sc = TRUE;

    if($this->input->post('date_add'))
    {
      $this->load->model('purchase/po_model');
      $this->load->model('masters/vender_model');
      $this->load->model('masters/zone_model');

      $date_add = db_date($this->input->post('date_add'), TRUE);
      $invoice = $this->input->post('invoice');
      $remark = $this->input->post('remark');

      $vender = $this->vender_model->get($this->input->post('venderCode'));
      $po = $this->po_model->get($this->input->post('poCode'));
      $zone = $this->zone_model->get($this->input->post('zoneCode'));
      $code = $this->get_new_code($date_add);

      if(empty($vender))
      {
        $sc = FALSE;
        $this->error = 'รหัสผู้จำหน่ายไม่ถูกต้อง';
      }

      if(empty($invoice))
      {
        $sc = FALSE;
        $this->error = 'กรุณาระบุใบส่งสินค้า';
      }

      if(!empty($po))
      {
        if($po->status == 0)
        {
          $sc = FALSE;
          $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ยังไม่บันทึกใบสั่งซื้อ';
        }
        else if($po->status == 3)
        {
          $sc = FALSE;
          $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ใบสั่งซื้อถูกปิดแล้ว';
        }
        else if($po->status == 4)
        {
          $sc = FALSE;
          $this->error = 'ใบสั่งซื้อไม่ถูกต้อง : ใบสั่งซื้อถูกยกเลิกแล้ว';
        }
      }

      if(empty($zone))
      {
        $sc = FALSE;
        $this->error = 'รหัสโซนไม่ถูกต้อง';
      }

      if($sc === TRUE)
      {
        $arr = array(
          'code' => $code,
          'bookcode' => getConfig('BOOK_CODE_RECEIVE_PO'),
          'vender_code' => $vender->code,
          'vender_name' => $vender->name,
          'po_code' => empty($po) ? NULL : $po->code,
          'invoice_code' => $invoice,
          'zone_code' => $zone->code,
          'warehouse_code' => $zone->warehouse_code,
          'remark' => $remark,
          'date_add' => $date_add,
          'user' => get_cookie('uname')
        );

        if(!$this->receive_po_model->add($arr))
        {
          $sc = FALSE;
          $this->error = label_value('doc_error');
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    if($sc === TRUE)
    {
      $ds = array('status' => 'success', 'message' => $code);
    }
    else
    {
      $ds = array('status' => 'error', 'message' => $this->error);
    }

    echo json_encode($ds);
  }


  public function get_vender_by_po($po_code)
  {
    $this->load->model('purchase/po_model');
    $po = $this->po_model->get($po_code);
    if(!empty($po))
    {
      echo $po->vender_code.' | '.$po->vender_name;
    }
  }



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_PO');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_PO');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_po_model->get_max_code($pre);
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
    $filter = array('code','invoice','po','vender','from_date','to_date');
    clear_filter($filter);
  }

} //--- end class
