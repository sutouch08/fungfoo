<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_transform extends PS_Controller
{
  public $menu_code = 'ICTRRC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title = 'รับสินค้าจากการแปรสภาพ';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_transform';
    $this->load->model('inventory/receive_transform_model');
    $this->load->model('inventory/transform_model');
  }


  public function index()
  {
    $this->load->helper('channels');
    $filter = array(
      'code'    => get_filter('code', 'code', ''),
      'invoice' => get_filter('invoice', 'invoice', ''),
      'order_code' => get_filter('order_code', 'order_code', ''),
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
		$rows     = $this->receive_transform_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->receive_transform_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->receive_transform_model->get_sum_qty($rs->code);
      }
    }

    $filter['document'] = $document;

		$this->pagination->initialize($init);
    $this->load->view('inventory/receive_transform/receive_transform_list', $filter);
  }



  public function view_detail($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');

    $doc = $this->receive_transform_model->get($code);
    if(!empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    }

    $details = $this->receive_transform_model->get_details($code);

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('inventory/receive_transform/receive_transform_detail', $ds);
  }



  public function print_detail($code)
  {
    $this->load->library('printer');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/orders_model');

    $doc = $this->receive_transform_model->get($code);
    $order = $this->orders_model->get($doc->order_code);
    if(!empty($doc))
    {
      $zone = $this->zone_model->get($doc->zone_code);
      $doc->zone_name = $zone->name;
      $doc->warehouse_name = $zone->warehouse_name;
      $doc->requester = $this->user_model->get_name($order->user);
      $doc->user_name = $this->user_model->get_name($doc->user);
    }

    $details = $this->receive_transform_model->get_details($code);

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_received_transform', $ds);
  }




  public function save()
  {
    $sc = TRUE;
    $message = 'ทำรายการไม่สำเร็จ';
    $this->load->model('inventory/movement_model');
    if($this->input->post('receive_code'))
    {
      $this->load->model('masters/products_model');
      $this->load->model('masters/zone_model');

      $code = $this->input->post('receive_code');
      $doc = $this->receive_transform_model->get($code);
      $order_code = $this->input->post('order_code');
      $invoice = $this->input->post('invoice');
      $zone_code = $this->input->post('zone_code');
      $warehouse_code = $this->zone_model->get_warehouse_code($zone_code);
      $receive = $this->input->post('receive');
      $backlogs = $this->input->post('backlogs');
      $prices = $this->input->post('prices');

      $arr = array(
        'order_code' => $order_code,
        'invoice_code' => $invoice,
        'zone_code' => $zone_code,
        'warehouse_code' => $warehouse_code,
        'update_user' => get_cookie('uname')
      );

      $this->db->trans_start();

      if($this->receive_transform_model->update($code, $arr) === FALSE)
      {
        $sc = FALSE;
        $message = 'Update Document Fail';
      }

      //--- If update success
      if($sc === TRUE)
      {
        if(!empty($receive))
        {
          //--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
          $this->receive_transform_model->drop_details($code);

          foreach($receive as $item => $qty)
          {
            if($qty != 0 && $sc === TRUE)
            {
              $pd = $this->products_model->get($item);
              $ds = array(
                'receive_code' => $code,
                'style_code' => $pd->style_code,
                'product_code' => $item,
                'product_name' => $pd->name,
                'price' => $prices[$item],
                'qty' => $qty,
                'amount' => $qty * $prices[$item]
              );

              if($this->receive_transform_model->add_detail($ds) === FALSE)
              {
                $sc = FALSE;
                $message = 'Add Receive Row Fail';
                break;
              }

              if($sc === TRUE)
              {
                $ds = array(
                  'reference' => $code,
                  'warehouse_code' => $warehouse_code,
                  'zone_code' => $zone_code,
                  'product_code' => $item,
                  'move_in' => $qty,
                  'date_add' => db_date($doc->date_add, TRUE)
                );

                if($this->movement_model->add($ds) === FALSE)
                {
                  $sc = FALSE;
                  $message = 'บันทึก movement ไม่สำเร็จ';
                }
              }


              //--- update receive_qty in order_transform_detail
              if($sc === TRUE)
              {
                $this->update_transform_receive_qty($order_code, $item, $qty);
              }

            }//--- end if qty > 0
          } //--- end foreach

          if($sc === TRUE)
          {
            $this->receive_transform_model->set_status($code, 1);
            if($this->transform_model->is_complete($order_code) === TRUE)
            {
              $this->transform_model->close_transform($order_code);
            }
          }
        } //--- end if !empty($receive)
      } //--- if $sc === TRUE

      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $sc = FALSE;
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบข้อมูล';
    }

    if($sc === TRUE)
    {
      $this->export_receive($code);
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  //--- update receive_qty in order_transform_detail
  public function update_transform_receive_qty($order_code, $product_code, $qty)
  {
    $list = $this->transform_model->get_transform_product_by_code($order_code, $product_code);
    if(!empty($list))
    {
      foreach($list as $rs)
      {
        if($qty > 0)
        {
          $diff = $rs->sold_qty - $rs->receive_qty;
          if($diff > 0 )
          {
            //--- ถ้า dif มากกว่ายอดที่รับมาให้ใช้ยอดรับ
            //--- หากยอดค้าง มี 2 แถว แถวแรก 5 แถวที่ 2 อีก 5 รวมเป็น 10
            //--- แต่รับเข้ามา 8
            //--- รอบแรก ยอด diff = 5 ซึ่งน้อยกว่า ยอดรับ ให้ใช้ยอด diff (ยอดค้างรับของแถวนั้น)
            //--- รอบสอง ยอด diff = 5 แต่ยอดรับจะเหลือ 3 เพราะถูกตัดออกไปรอบแรก 5 (จากยอดรับ 8)
            //--- รอบสองจึงต้องใช้ยอดรับที่เหลือในการ update
            $valid = $qty >= $diff ? TRUE : FALSE;
            $diff = $diff > $qty ? $qty : $diff;
            $this->transform_model->update_receive_qty($rs->id, $diff);
            $qty -= $diff;
            //--- เมื่อลบยอดค้างรับออกแล้วยังเหลือยอดอีกแสดงว่าแถวนี้รับครบแล้ว ให้ update valid เป็น 1
            if($valid)
            {
              $this->transform_model->valid_detail($rs->id);
            }
          }
        } //--- end if qty > 0
      } //--- endforeach
    }
  }



  public function cancle_received()
  {
    if($this->input->post('receive_code'))
    {
      $this->load->model('inventory/movement_model');
      $code = $this->input->post('receive_code');
      $this->db->trans_start();
      $this->receive_transform_model->cancle_details($code);
      $this->receive_transform_model->set_status($code, 2); //--- 0 = ยังไม่บันทึก 1 = บันทึกแล้ว 2 = ยกเลิก
      $this->movement_model->drop_movement($code);
      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        echo 'ยกเลิกรายการไม่สำเร็จ';
      }
      else
      {
        echo 'success';
      }
    }
    else
    {
      echo 'ไม่พบเลขทีเอกสาร';
    }

  }



  public function get_transform_detail()
  {
    $sc = '';
    $code = $this->input->get('order_code');
    $details = $this->receive_transform_model->get_transform_details($code);
    $ds = array();
    if(!empty($details))
    {
      $no = 1;
      $totalQty = 0;
      $totalBacklog = 0;

      foreach($details as $rs)
      {
        $diff = $rs->sold_qty - $rs->receive_qty;
        $arr = array(
          'no' => $no,
          'barcode' => $rs->barcode,
          'pdCode' => $rs->product_code,
          'pdName' => $rs->name,
          'qty' => number($rs->sold_qty),
          'price' => number($rs->price,2),
          'limit' => $diff,
          'backlog' => number($diff)
        );
        array_push($ds, $arr);
        $no++;
        $totalQty += $rs->sold_qty;
        $totalBacklog += $diff;
      }

      $arr = array(
        'qty' => number($totalQty),
        'backlog' => number($totalBacklog)
      );
      array_push($ds, $arr);

      $sc = json_encode($ds);
    }
    else
    {
      $sc = 'ใบสินค้าไม่ถูกต้องหรือถูกปิดไปแล้ว';
    }

    echo $sc;
  }



  public function edit($code)
  {
    $document = $this->receive_transform_model->get($code);
    $ds['document'] = $document;
    $this->load->view('inventory/receive_transform/receive_transform_edit', $ds);
  }




  public function add_new()
  {
    $this->load->view('inventory/receive_transform/receive_transform_add');
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
        set_error('วันที่ไม่ถูกต้อง');
        redirect($this->home.'/add_new');
      }
      else
      {
        $code = $this->get_new_code($date);
        $arr = array(
          'code' => $code,
          'bookcode' => getConfig('BOOK_CODE_RECEIVE_TRANSFORM'),
          'order_code' => NULL,
          'invoice_code' => NULL,
          'remark' => $this->input->post('remark'),
          'date_add' => $date,
          'user' => get_cookie('uname')
        );

        $rs = $this->receive_transform_model->add($arr);
        if($rs)
        {
          redirect($this->home.'/edit/'.$code);
        }
        else
        {
          set_error('เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
          redirect($this->home.'/add_new');
        }
      }
    }
  }



  public function do_export($code)
  {
    $rs = $this->export_receive($code);
    echo $rs === TRUE ? 'success' : $this->error;
  }


  private function export_receive($code)
  {
    $this->load->model('masters/products_model');
    $doc = $this->receive_transform_model->get($code);
    $sap = $this->receive_transform_model->get_sap_receive_transform($code);

    if(!empty($doc))
    {
      if(empty($sap) OR $tr->DocStatus == 'O')
      {
        if($doc->status == 1)
        {
          $currency = getConfig('CURRENCY');
          $vat_rate = getConfig('PURCHASE_VAT_RATE');
          $vat_code = getConfig('PURCHASE_VAT_CODE');
          $total_amount = $this->receive_transform_model->get_sum_amount($code);
          $ds = array(
            'U_ECOMNO' => $doc->code,
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => $doc->date_add,
            'DocDueDate' => $doc->date_add,
            'DocCur' => $currency,
            'DocRate' => 1,
            'DocTotal' => remove_vat($total_amount),
            'Comments' => $doc->remark,
            'F_E_Commerce' => (empty($sap) ? 'A' : 'U'),
            'F_E_CommerceDate' => now()
          );

          $this->mc->trans_start();

          if(!empty($sap))
          {
            $sc = $this->receive_transform_model->update_sap_receive_transform($code, $ds);
          }
          else
          {
            $sc = $this->receive_transform_model->add_sap_receive_transform($ds);
          }

          if($sc)
          {
            if(!empty($sap))
            {
              $this->receive_transform_model->drop_sap_exists_details($code);
            }

            $details = $this->receive_transform_model->get_details($code);

            if(!empty($details))
            {
              $line = 0;
              foreach($details as $rs)
              {
                $arr = array(
                  'U_ECOMNO' => $rs->receive_code,
                  'LineNum' => $line,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => $rs->product_name,
                  'Quantity' => $rs->qty,
                  'unitMsr' => $this->products_model->get_unit_code($rs->product_code),
                  'PriceBefDi' => remove_vat($rs->price),
                  'LineTotal' => remove_vat($rs->amount),
                  'ShipDate' => $doc->date_add,
                  'Currency' => $currency,
                  'Rate' => 1,
                  'Price' => remove_vat($rs->price),
                  'TotalFrgn' => remove_vat($rs->amount),
                  'WhsCode' => $doc->warehouse_code,
                  'FisrtBin' => $doc->zone_code,
                  'BaseRef' => $doc->order_code,
                  'TaxStatus' => 'Y',
                  'VatPrcnt' => $vat_rate,
                  'VatGroup' => $vat_code,
                  'PriceAfVAT' => $rs->price,
                  'VatSum' => get_vat_amount($rs->amount),
                  'TaxType' => 'Y',
                  'F_E_Commerce' => (empty($sap) ? 'A' : 'U'),
                  'F_E_CommerceDate' => now()
                );

                if( ! $this->receive_transform_model->add_sap_receive_transform_detail($arr))
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
  //--- end export transform



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_TRANSFORM');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_TRANSFORM');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_transform_model->get_max_code($pre);
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
    $filter = array('code','invoice','order_code','from_date','to_date');
    clear_filter($filter);
  }

} //--- end class
