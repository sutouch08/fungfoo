<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_product extends PS_Controller
{
  public $menu_code = 'ICPDRC';
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
    $this->title = label_value('receive_product_title');
  }


  public function index()
  {
    $this->load->helper('channels');
    $filter = array(
      'code'    => get_filter('code', 'code', ''),
      'invoice' => get_filter('invoice', 'invoice', ''),
      'po'      => get_filter('po', 'po', ''),
      'vendor'  => get_filter('vendor', 'vendor', ''),
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

    $this->load->view('print/print_received', $ds);
  }




  public function save()
  {
    $sc = TRUE;
    $message = label_value('operation_fail');
    if($this->input->post('receive_code'))
    {
      $this->load->model('masters/products_model');
      $this->load->model('masters/zone_model');
      $this->load->model('inventory/movement_model');
      $this->load->model('purchase/po_model');

      $auto_close = getConfig('AUTO_CLOSE_PO');
      $code = $this->input->post('receive_code');
      $vendor_code = $this->input->post('vendor_code');
      $vendor_name = $this->input->post('vendorName');
      $po_code = $this->input->post('poCode');
      $invoice = $this->input->post('invoice');
      $zone_code = $this->input->post('zone_code');
      $warehouse_code = $this->zone_model->get_warehouse_code($zone_code);
      $receive = $this->input->post('receive');
      $backlogs = $this->input->post('backlogs');
      $prices = $this->input->post('prices');
      $approver = $this->input->post('approver') == '' ? NULL : $this->input->post('approver');

      $doc = $this->receive_po_model->get($code);

      $arr = array(
        'vendor_code' => $vendor_code,
        'vendor_name' => $vendor_name,
        'po_code' => $po_code,
        'invoice_code' => $invoice,
        'zone_code' => $zone_code,
        'warehouse_code' => $warehouse_code,
        'update_user' => get_cookie('uname'),
        'approver' => $approver
      );

      $this->db->trans_start();

      if($this->receive_po_model->update($code, $arr) === FALSE)
      {
        $sc = FALSE;
        $message = label_value('update_fail');
      }
      else
      {
        if(!empty($receive))
        {
          //--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
          $this->receive_po_model->drop_details($code);

          foreach($receive as $item => $qty)
          {
            if($qty != 0)
            {
              $pd = $this->products_model->get($item);
              $bf = $backlogs[$item]; ///--- ยอดค้ารับ ก่อนรับ
              $af = ($bf - $qty) > 0 ? ($bf - $qty) : 0;  //--- ยอดค้างรับหลังรับแล้ว
              $ds = array(
                'receive_code' => $code,
                'style_code' => $pd->style_code,
                'product_code' => $item,
                'product_name' => $pd->name,
                'price' => $prices[$item],
                'qty' => $qty,
                'amount' => $qty * $prices[$item],
                'before_backlogs' => $bf,
                'after_backlogs' => $af
              );

              if($this->receive_po_model->add_detail($ds) === FALSE)
              {
                $sc = FALSE;
                $message = label_value('insert_fail');
                break;
              }
              else
              {
                //--- insert Movement in
                $arr = array(
                  'reference' => $code,
                  'warehouse_code' => $warehouse_code,
                  'zone_code' => $zone_code,
                  'product_code' => $item,
                  'move_in' => $qty,
                  'move_out' => 0,
                  'date_add' => $doc->date_add
                );

                $this->movement_model->add($arr);
                $this->po_model->update_received($po_code, $pd->code, $qty);
              }
            }
          } //--- end foreach

          $this->receive_po_model->set_status($code, 1);
          $this->po_model->change_status($po_code, 2); //--- change po to partially received
          if($auto_close == 1)
          {
            if($this->po_model->is_all_done($po_code))
            {
              $this->po_model->close_po($po_code);
            }
          }
        }
      }

      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $sc = FALSE;
      }
    }
    else
    {
      $sc = FALSE;
      $message = label_value('no_data_found');
    }


    echo $sc === TRUE ? 'success' : $message;
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
    if($this->input->post('receive_code'))
    {
      $this->load->model('inventory/movement_model');
      $this->load->model('purchase/po_model');
      $code = $this->input->post('receive_code');
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



  public function get_po_detail()
  {
    $sc = '';
    $this->load->model('masters/products_model');
    $this->load->model('purchase/po_model');
    $po_code = $this->input->get('po_code');
    $po = $this->po_model->get($po_code);
    $details = $this->receive_po_model->get_po_details($po_code);
    $rate = (getConfig('RECEIVE_OVER_PO') * 0.01);
    $ds = array();
    if(!empty($po))
    {
      $ds['vender_code'] = $po->code;
      $ds['vender_name'] = $po->vender_name;
    }

    $dl = array();

    if(!empty($details))
    {
      $no = 1;
      $totalQty = 0;
      $totalBacklog = 0;

      foreach($details as $rs)
      {
        $backlogs = ($rs->qty - $rs->received) < 0 ? 0 : $rs->qty - $rs->received;
        $limit = $rs->qty + ($rs->qty* $rate);
        $arr = array(
          'no' => $no,
          'barcode' => $this->products_model->get_barcode($rs->product_code),
          'pdCode' => $rs->product_code,
          'pdName' => $rs->product_name,
          'price' => $rs->price,
          'qty' => number($rs->qty),
          'limit' => ($limit - $rs->received) < 0 ? 0 : $limit - $rs->received,
          'backlog' => number($backlogs)
        );
        array_push($dl, $arr);
        $no++;
        $totalQty += $rs->qty;
        $totalBacklog += $backlogs;
      }

      $arr = array(
        'qty' => number($totalQty),
        'backlog' => number($totalBacklog)
      );
      array_push($dl, $arr);

      $ds['list'] = $dl;

      $sc = json_encode($ds);
    }
    else
    {
      $sc = label_value('po_error');
    }

    echo $sc;
  }



  public function edit($code)
  {
    $document = $this->receive_po_model->get($code);
    $ds['doc'] = $document;
    $this->load->view('inventory/receive_po/receive_po_edit', $ds);
  }




  public function add_new()
  {
    $this->load->view('inventory/receive_po/receive_po_add');
  }


  public function add()
  {
    $sc = array();

    if($this->input->post('date_add'))
    {
      $date_add = $this->input->post('date_add');
      $Y = date('Y', strtotime($date_add));
      $date = db_date($date_add, TRUE);
      if($Y > '2500')
      {
        set_error(label_value('date_error'));
        redirect($this->home.'/add_new');
      }
      else
      {
        $code = $this->get_new_code($date);
        $arr = array(
          'code' => $code,
          'bookcode' => getConfig('BOOK_CODE_RECEIVE_PO'),
          'vendor_code' => NULL,
          'vendor_name' => NULL,
          'po_code' => NULL,
          'invoice_code' => NULL,
          'remark' => $this->input->post('remark'),
          'date_add' => $date,
          'user' => get_cookie('uname')
        );

        $rs = $this->receive_po_model->add($arr);
        if($rs)
        {
          redirect($this->home.'/edit/'.$code);
        }
        else
        {
          set_error(label_value('doc_error'));
          redirect($this->home.'/add_new');
        }
      }
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
    $filter = array('code','invoice','po','vendor','from_date','to_date');
    clear_filter($filter);
  }

} //--- end class
