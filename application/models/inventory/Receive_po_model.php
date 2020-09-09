<?php
class Receive_po_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_product', $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_product_detail', $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('receive_product', $ds);
    }

    return FALSE;
  }


  public function update_detail($id, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('receive_product_detail', $ds);
    }

    return FALSE;
  }





  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('receive_product');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_detail_by_product($code, $product_code)
  {
    $rs = $this->db
    ->where('receive_code', $code)
    ->where('product_code', $product_code)
    ->get('receive_product_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('receive_product_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_details($code)
  {
    $this->db
    ->select('rd.*')
    ->select('pd.barcode')
    ->from('receive_product_detail AS rd')
    ->join('products AS pd', 'rd.product_code = pd.code', 'left')
    ->join('product_size AS ps', 'pd.size_code = ps.code', 'left')
    ->where('rd.receive_code', $code)
    ->order_by('rd.style_code', 'ASC')
    ->order_by('pd.color_code', 'ASC')
    ->order_by('ps.position', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_unsave_details($code)
  {
    $rs = $this->db->where('receive_code', $code)->where('status', 'N')->get('receive_product_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_saved_details($code)
  {
    $rs = $this->db->where('receive_code', $code)->where('status', 'S')->get('receive_product_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function drop_details($code)
  {
    return $this->db->where('receive_code', $code)->delete('receive_product_detail');
  }



  public function drop_detail($id)
  {
    return $this->db->where('id', $id)->delete('receive_product_detail');
  }



  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('receive_code', $code)->update('receive_product_detail');
  }



  public function get_po_details($po_code)
  {
    $rs = $this->db
    ->select('product_code, product_name, price, qty, received')
    ->where('po_code', $po_code)
    ->get('po_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function is_exists_detail($code, $product_code)
  {
    $rs = $this->db->select('id')->where('receive_code', $code)->where('product_code', $product_code)->get('receive_product_detail');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('receive_code', $code)
    ->get('receive_product_detail');

    return intval($rs->row()->qty);
  }



  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('receive_code', $code)->get('receive_product_detail');
    return $rs->row()->amount === NULL ? 0.00 : $rs->row()->amount;
  }




  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('receive_product');
  }



  public function count_rows(array $ds = array())
  {
    $this->db->select('status');

    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if(!empty($ds['po']))
    {
      $this->db->like('po_code', $ds['po']);
    }

    //---- invoice
    if(!empty($ds['invoice']))
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->get('receive_product');


    return $rs->num_rows();
  }





  public function get_data(array $ds = array(), $perpage = '', $offset = '', $role = 'S')
  {
    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if(!empty($ds['po']))
    {
      $this->db->like('po_code', $ds['po']);
    }

    //---- invoice
    if(!empty($ds['invoice']))
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }


    //--- vender
    if(!empty($ds['vender']))
    {
      $this->db->like('vender_code', $ds['vender']);
      $this->db->or_like('vender_name', $ds['vender']);
    }


    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('receive_product');
    return $rs->result();
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('receive_product');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }



}

 ?>
