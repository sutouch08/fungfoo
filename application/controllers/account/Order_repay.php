<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_repay extends PS_Controller
{
  public $menu_code = 'ACODRP';
	public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
	public $title = 'รับชำระ(ตัดหนี้)';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'account/order_repay';
    $this->load->model('account/order_repay_model');
    $this->load->model('account/order_credit_model');
    $this->load->model('account/payment_receive_model');
    $this->load->model('masters/customers_model');
    $this->load->helper('order_repay');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'repay_code', ''),
      'customer' => get_filter('customer', 'repay_customer', ''),
      'from_date' => get_filter('from_date', 'repay_from_date', ''),
      'to_date' => get_filter('to_date', 'repay_to_date', ''),
      'status' => get_filter('status', 'repay_status', 'all'),
      'pay_type' => get_filter('pay_type', 'repay_type', '')
    );

    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->order_repay_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs = $this->order_repay_model->get_list($filter, $perpage, $this->uri->segment($segment));
    if(!empty($docs))
    {
      foreach($docs as $rs)
      {
        $rs->amount = $this->order_repay_model->get_sum_amount($rs->code);
      }
    }

    $filter['docs'] = $docs;

		$this->pagination->initialize($init);
    $this->load->view('account/order_repay/order_repay_list', $filter);
  }



  public function add_new()
  {
    $ds['new_code'] = $this->get_new_code();
    $this->load->view('account/order_repay/order_repay_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;
    if($this->pm->can_add)
    {
      if($this->input->post('date_add'))
      {
        $date_add = db_date($this->input->post('date_add'), TRUE);
        $code = $this->get_new_code($date);

        $arr = array(
          'code' => $code,
          'customer_code' => $this->input->post('customerCode'),
          'pay_type' => $this->input->post('pay_type'),
          'remark' => $this->input->post('remark'),
          'date_add' => $date_add,
          'user' => get_cookie('uname')
        );

        if(! $this->order_repay_model->add($arr))
        {
          $sc = FALSE;
          set_error("เพิ่มเอกสารไม่สำเร็จ");
        }
      }
      else
      {
        $sc = FALSE;
        set_error('ไม่พบข้อมูล/ข้อมูลไม่ครบถ้วน');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('คุณไม่มีสิทธิ์ในการเพิ่มเอกสาร');
    }

    if($sc === TRUE)
    {
      redirect($this->home.'/edit/'.$code);
    }
    else
    {
      redirect($this->home.'/add_new');
    }

  }



  public function edit($code)
  {
    $doc = $this->order_repay_model->get($code);
    $details = $this->order_repay_model->get_details($code);

    $ds['doc'] = $doc;
    $ds['details'] = $details;

    $this->load->view('account/order_repay/order_repay_edit', $ds);
  }





  public function save($code)
  {
    $sc = TRUE;
    if($this->input->post('pay_amount'))
    {
      $list = $this->input->post('pay_amount');
      $doc = $this->order_repay_model->get($code);
      $total_pay_amount = 0;
      if(!empty($list) && $doc->status == 0)
      {
        $this->db->trans_begin();
        foreach($list as $id => $amount)
        {
          if($sc === FALSE)
          {
            break;
          }

          $ds = $this->order_repay_model->get_detail($id);

          if(!empty($ds) && $amount > 0 && $ds->valid == 0)
          {
            $arr = array(
              'pay_amount' => $amount,
              'valid' => 1
            );

            //--- 1. update ยอดชำระตามที่ป้อนมา และเปลียนสถานะรายการ
            if(! $this->order_repay_model->update_detail($id, $arr))
            {
              $sc = FALSE;
              $this->error = 'บันทึกยอดชำระไม่สำเร็จ';
              break;
            }


            //--- 2. ปรับปรุงยอดขำระในตาราง order_credit
            if($sc === TRUE)
            {
              if(! $this->order_credit_model->pay_order($ds->reference, $amount))
              {
                $sc = FALSE;
                $this->error = 'บันทึกจ่ายเงินในตารางเครดิตไม่สำเร็จ';
                break;
              }
            }

            //--- 3. คืนยอดเครดติคงเหลือในตารางลูกค้า
            if($sc === TRUE)
            {
              if(! $this->customers_model->update_used($doc->customer_code, (-1) * $amount))
              {
                $sc = FALSE;
                $this->error = 'คืนยอดเครดิตคงเหลือให้ลูกค้าไม่สำเร็จ';
                break;
              }
            }

            $total_pay_amount += $amount;

          } //-- end if !empty()
        } //-- end foreach

        //--- 4. บันทึกรับเงิน
        if($sc === TRUE)
        {
          $arr = array(
            'reference' => $code,
            'customer_code' => $doc->customer_code,
            'pay_date' => $doc->date_add,
            'amount' => $total_pay_amount,
            'payment_type' => $doc->pay_type,
            'valid' => 1
          );

          if(! $this->payment_receive_model->add($arr))
          {
            $sc = FALSE;
            $this->error = 'บันทึกรับเงินไม่สำเร็จ';
          }
        }


        if($sc === TRUE)
        {
          if(! $this->order_repay_model->change_status($code, 1))
          {
            $sc = FALSE;
            $this->error = 'เปลี่ยนสถานะเอกสารไม่สำเร็จ';
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
      else
      {
        $sc = FALSE;
        $this->error = 'ไม่พบรายการ';
      }
    }

    if($sc === TRUE)
    {
      set_message('บันทึกรายการเรียบร้อยแล้ว');
      redirect("{$this->home}/view_detail/{$code}");
    }
    else
    {
      set_error($this->error);
      redirect("{$this->home}/edit/{$code}");
    }
  }





  public function add_detail($code)
  {
    $sc = TRUE;
    if($this->input->post('credit'))
    {
      $credit = $this->input->post('credit'); //-- รายการที่เลือกมา
      if(!empty($credit))
      {
        $doc = $this->order_repay_model->get($code);
        $this->db->trans_begin();

        foreach($credit as $id)
        {
          if($sc === FALSE)
          {
            break;
          }

          $order = $this->order_credit_model->get_by_id($id);
          if(!empty($order))
          {
            $arr = array(
              'repay_code' => $code,
              'reference' => $order->order_code,
              'due_date' => $order->due_date,
              'pay_date' => $doc->date_add,
              'amount' => $order->amount,
              'balance' => $order->balance,
              'pay_amount' => $order->balance
            );

            $detail = $this->order_repay_model->get_detail_by_reference($code, $order->order_code);
            if(!empty($detail))
            {
              if(! $this->order_repay_model->update_detail($detail->id, $arr))
              {
                $sc = FALSE;
                $this->error = "Updte failed for : {$order->order_code}";
              }
            }
            else
            {
              if(!$this->order_repay_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = "Insert Failed for : {$order->order_code}";
              }
            }
          } //-- end if empty($order)
        } //--- end foreach

        if(! $this->order_repay_model->change_status($code, 0))
        {
          $sc = FALSE;
          $this->error = 'เปลี่ยนสถานะเอกสารไม่สำเร็จ';
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
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }






  public function view_detail($code)
  {
    $ds = array(
      'doc' => $this->order_repay_model->get($code),
      'details' => $this->order_repay_model->get_details($code)
    );

    $this->load->view('account/order_repay/order_repay_detail', $ds);
  }




  public function update($code)
  {
    if($this->input->post('customer_code'))
    {
      $arr = array(
        'customer_code' => $this->input->post('customer_code'),
        'pay_type' => $this->input->post('pay_type'),
        'date_add' => db_date($this->input->post('date_add'), TRUE),
        'update_user' => get_cookie('uname'),
        'remark' => $this->input->post('remark')
      );

      $sc = $this->order_repay_model->update($code, $arr);

      echo $sc === TRUE ? 'success' : 'ปรับปรุงข้อมูลไม่สำเร็จ';
    }
    else
    {
      echo 'ไม่พบข้อมูล';
    }
  }




  public function get_details_talbe($code)
  {
    $doc = $this->order_repay_model->get($code);
    $details = $this->order_repay_model->get_details($code);
    $ds = array();
    $total_amount = 0;
    $total_balance = 0;
    $total_pay_amount = 0;
    if(!empty($details))
    {
      $no = 1;
      foreach($details as $rs)
      {
        $arr = array(
          'id' => $rs->id,
          'no' => $no,
          'repay_code' => $rs->repay_code,
          'reference' => $rs->reference,
          'due_date' => thai_date($rs->due_date),
          'pay_date' => thai_date($rs->pay_date),
          'amount' => number($rs->amount, 2),
          'balance' => number($rs->balance, 2),
          'pay_amount' => ($doc->status == 1 ? number($rs->pay_amount, 2) : $rs->pay_amount),
          'valid' => $rs->valid
        );

        array_push($ds, $arr);
        $no++;
        $total_amount += $rs->amount;
        $total_balance += $rs->balance;
        $total_pay_amount += $rs->pay_amount;
      }
    }

    $arr = array(
      'total_amount' => number($total_amount, 2),
      'total_balance' => number($total_balance, 2),
      'total_pay_amount' => number($total_pay_amount, 2)
    );

    array_push($ds, $arr);

    echo json_encode($ds);
  }



  public function delete_detail($id)
  {
    $sc = TRUE;
    $detail = $this->order_repay_model->get_detail($id);
    $doc = $this->order_repay_model->get($detail->repay_code);
    if($detail->valid == 1)
    {
      if(! $this->pm->can_delete)
      {
        $sc = FALSE;
        $this->error = 'คุณไมมีสิทธิ์ในการลบ';
      }
      else
      {
        $this->db->trans_begin();
        //--- update paid amount in order_credit
        if(! $this->order_credit_model->unpay_order($detail->reference, $detail->pay_amount))
        {
          $sc = FALSE;
          $this->error = 'ไม่สามารถแก้ไขยอดชำระในตารางเครดิตได้';
        }

        //--- update customer's credit balance;
        if($sc === TRUE)
        {
          if(! $this->customers_model->update_used($doc->customer_code, $detail->pay_amount))
          {
            $sc = FALSE;
            $this->error = 'ปรับปรุงเครติดคงเหลือไม่สำเร็จ';
          }
        }

        //--- ลบรายการ
        if($sc === TRUE)
        {
          if(! $this->order_repay_model->delete_detail($id))
          {
            $sc = FALSE;
            $this->error = 'ลบรายการไม่สำเร็จ';
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
    }
    else
    {
      //-- if valid = 0
      if(! $this->order_repay_model->delete_detail($id))
      {
        $sc = FALSE;
        $this->error = 'ลบรายการไม่สำเร็จ';
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function cancle($code)
  {
    $sc = TRUE;
    if(! $this->pm->can_delete)
    {
      $sc = FALSE;
      $this->error = 'คุณไมมีสิทธิ์ในกา';
    }
    else
    {
      $doc = $this->order_repay_model->get($code);
      $details = $this->order_repay_model->get_details($code);

      $this->db->trans_begin();

      if(!empty($details))
      {
        foreach($details as $detail)
        {
          if($sc === FALSE)
          {
            break;
          }

          if($detail->valid == 1)
          {
            //--- update paid amount in order_credit
            if(! $this->order_credit_model->unpay_order($detail->reference, $detail->pay_amount))
            {
              $sc = FALSE;
              $this->error = 'ไม่สามารถแก้ไขยอดชำระในตารางเครดิตได้';
              break;
            }

            //--- update customer's credit balance;
            if($sc === TRUE)
            {
              if(! $this->customers_model->update_used($doc->customer_code, $detail->pay_amount))
              {
                $sc = FALSE;
                $this->error = 'ปรับปรุงเครติดคงเหลือไม่สำเร็จ';
                break;
              }
            }

            //--- ลบรายการ
            if($sc === TRUE)
            {
              if(! $this->order_repay_model->cancle_detail($detail->id))
              {
                $sc = FALSE;
                $this->error = 'ยกเลิกรายการไม่สำเร็จ';
                break;
              }
            }
          }
          else
          {
            //-- if valid = 0
            if(! $this->order_repay_model->cancle_detail($detail->id))
            {
              $sc = FALSE;
              $this->error = 'ยกเลิกรายการไม่สำเร็จ';
            }
          }
        }
      }

      if($sc === TRUE)
      {
        if(!$this->order_repay_model->change_status($code, 2)) //--- cancle
        {
          $sc = FALSE;
          $this->error = 'ยกเลิกเอกสารไม่สำเร็จ';
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



  public function get_credit_list($customer_code, $repay_code)
  {
    $exclude = $this->get_exclude_order($repay_code);
    $list = $this->order_credit_model->get_unvalid_order($customer_code, $exclude);
    $ds = array();
    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $arr = array(
          'id' => $rs->id,
          'order_code' => $rs->order_code,
          'delivery_date' => thai_date($rs->delivery_date),
          'due_date' => thai_date($rs->due_date),
          'amount' => number($rs->amount, 2),
          'balance' => number($rs->balance, 2)
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      $arr = array('nodata' => 'nodata');
      array_push($ds, $arr);
    }

    echo json_encode($ds);
  }

  public function get_exclude_order($repay_code)
  {
    $list = $this->order_repay_model->get_exclude_order($repay_code);
    if(!empty($list))
    {
      $arr = array();
      foreach($list as $rs)
      {
        $arr[] = $rs->reference;
      }

      return $arr;
    }

    return FALSE;
  }

  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER_REPAY');
    $run_digit = getConfig('RUN_DIGIT_ORDER_REPAY');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_repay_model->get_max_code($pre);
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
    $filter = array('repay_code', 'repay_customer', 'repay_from_date', 'repay_to_date', 'repay_status', 'repay_type');
    clear_filter($filter);
  }


} //---- end class
 ?>
