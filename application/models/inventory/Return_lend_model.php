<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Return_lend_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('return_lend', $ds);
    }

    return FALSE;
  }




  public function update($code, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('return_lend', $ds);
    }

    return FALSE;
  }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('return_lend');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_details($code)
  {
    $qr = "SELECT rld.*, old.qty AS lend_qty, old.receive AS receive
           FROM return_lend_detail AS rld
           LEFT JOIN order_lend_detail AS old ON old.order_code = rld.lend_code AND old.product_code = rld.product_code
           WHERE rld.return_code = '{$code}'";
    $rs = $this->db->query($qr);
    // $this->db
    // ->select('return_lend_detail.*, order_lend_detail.qty AS lend_qty, order_lend_detail.receive AS receive')
    // ->from('return_lend_detail')
    // ->join('return_lend', 'return_lend.code = return_lend_detail.return_code', 'left')
    // ->join('order_lend_detail', 'order_lend_detail.order_code = return_lend.lend_code AND order_lend_detail.product_code = return_lend_detail.product_code', 'left')
    // ->where('return_lend_detail.return_code', $code);
    // $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_lend_details($code)
  {
    $rs = $this->db->where('return_code', $code)->get('return_lend_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //--- delete received details 
  public function drop_details($code)
  {
    return $this->db->where('return_code', $code)->delete('return_lend_detail');
  }




  //--- get return backlogs
  public function get_backlogs($code)
  {
    $this->db
    ->where('order_code', $code)
    ->where('receive <', 'qty', FALSE)
    ->where('valid', 0);
    $rs = $this->db->get('order_lend_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //---- get return qty on return lend row
  public function get_return_qty($return_code, $product_code)
  {
    $rs = $this->db
    ->select('qty')
    ->where('return_code', $return_code)
    ->where('product_code', $product_code)
    ->get('return_lend_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  //---- update received qty on return backlogs  list (table : order_lend_detail)
  public function update_receive($code, $product_code, $qty)
  {
    $rs = $this->get_detail($code, $product_code);
    if(!empty($rs))
    {
      $new_qty = $rs->receive + $qty;

      $arr = array('receive' => $new_qty);

      if($new_qty >= $rs->qty)
      {
        $arr['valid'] = 1;
      }
      else
      {
        $arr['valid'] = 0;
      }

      return $this->db->where('id', $rs->id)->update('order_lend_detail', $arr);
    }

    return FALSE;
  }


  ///---- change document status  0 = not save, 1 = saved , 2 = cancle
  public function change_status($code, $status)
  {
    return $this->db->where('code', $code)->update('return_lend', array('status' => $status, 'update_user' => get_cookie('uname')));
  }



  ///---- change details status  0 = not save, 1 = saved , 2 = cancle
  public function change_details_status($code, $status)
  {
    return $this->db->where('return_code', $code)->update('return_lend_detail', array('status' => $status));
  }




  //--- get return lend detail one row
  public function get_detail($code, $product_code)
  {
    $rs = $this->db->where('order_code', $code)->where('product_code', $product_code)->get('order_lend_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  //--- insert new row
  public function add_detail($ds)
  {
    return $this->db->insert('return_lend_detail', $ds);
  }


  public function count_rows(array $ds = array())
  {
    $this->db->select('status');

    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //---- invoice
    if(!empty($ds['lend_code']))
    {
      $this->db->like('lend_code', $ds['lend_code']);
    }

    //--- customer
    if(!empty($ds['customer_code']))
    {
      $this->db->where_in('customer_code', $this->customer_in($ds['customer_code']));
    }

    if(!empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->get('return_lend');


    return $rs->num_rows();
  }





  public function get_list(array $ds = array(), $perpage = '', $offset = '')
  {
    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //---- invoice
    if(!empty($ds['lend_code']))
    {
      $this->db->like('lend_code', $ds['lend_code']);
    }

    //--- customer
    if(!empty($ds['customer_code']))
    {
      $this->db->where_in('customer_code', $this->customer_in($ds['customer_code']));
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }


    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if(!empty($perpage))
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('return_lend');

    return $rs->result();
  }



  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty')->where('return_code', $code)->get('return_lend_detail');

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }




  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('return_code', $code)->get('return_lend_detail');
    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }




  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('return_lend');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }
} //--- end class

 ?>
