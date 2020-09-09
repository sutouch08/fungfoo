<?php
class Consign_check_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  //---- add new document
  public function add($ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('consign_check', $ds);
    }

    return FALSE;
  }



  public function update($code, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update('consign_check', $ds);
    }

    return FALSE;
  }



  //--- get document data row
  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('consign_check');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  //--- get 1 row
  public function get_detail($check_code, $product_code)
  {
    $rs = $this->db
    ->where('check_code', $check_code)
    ->where('product_code', $product_code)
    ->get('consign_check_detail');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  //--- get document details
  public function get_details($code)
  {
    $rs = $this->db->where('check_code', $code)->get('consign_check_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //--- get list for link with consign order
  public function get_active_check_list($zone_code)
  {
    $rs = $this->db
    ->select('code, date_add')
    ->where('zone_code', $zone_code)
    ->where('status', 1)
    ->where('valid', 0)
    ->get('consign_check');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_diff_details($code)
  {
    $rs = $this->db
    ->select('product_code, product_name, (stock_qty - qty) AS diff')
    ->where('check_code', $code)
    ->where('stock_qty !=', 'qty', FALSE)
    ->get('consign_check_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function update_ref_code($code, $consign_code, $valid)
  {
    $this->db
    ->set('consign_code', $consign_code)
    ->set('valid', $valid)
    ->where('code', $code);

    return $this->db->update('consign_check');
  }


  //---- add detail row
  public function add_detail($ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('consign_check_detail', $ds);
    }

    return FALSE;
  }



  public function update_stock_qty($id, $qty)
  {
    return $this->db->set("stock_qty", "stock_qty + {$qty}", FALSE)->where('id', $id)->update('consign_check_detail');
  }



  public function update_check_detail($check_code, $product_code, $qty)
  {
    $this->db
    ->set("qty", "qty+{$qty}", FALSE)
    ->where('check_code', $check_code)
    ->where('product_code', $product_code);

    return $this->db->update('consign_check_detail');
  }



  public function update_box_qty($id_box, $check_code, $product_code, $qty)
  {
    $id = $this->get_box_detail_id($id_box, $check_code, $product_code);

    if($id !== FALSE)
    {
      $this->db
      ->set("qty", "qty + {$qty}", FALSE)
      ->where('id_box', $id_box)
      ->where('product_code', $product_code);
      return $this->db->update('consign_box_detail');
    }
    else
    {
      $arr = array(
        'check_code' => $check_code,
        'id_box' => $id_box,
        'product_code' => $product_code,
        'qty' => $qty
      );

      return $this->add_box_detail($arr);
    }
  }



  public function get_box_detail_id($id_box, $check_code, $product_code)
  {
    $rs = $this->db
    ->where('id_box', $id_box)
    ->where('check_code', $check_code)
    ->where('product_code', $product_code)
    ->get('consign_box_detail');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }



  public function get_box_list($check_code)
  {
    $rs = $this->db
    ->select('consign_box.*')
    ->from('consign_box_detail')
    ->join('consign_box', 'consign_box_detail.id_box = consign_box.id')
    ->where('consign_box_detail.check_code', $check_code)
    ->group_by('consign_box.id')
    ->get();
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function delete_box_qty($id_box, $check_code, $product_code)
  {
    $this->db
    ->where('id_box', $id_box)
    ->where('product_code', $product_code)
    ->where('check_code', $check_code);

    return $this->db->delete('consign_box_detail');
  }



  public function get_consign_box_product_details($check_code, $product_code)
  {
    $rs = $this->db
    ->select('consign_box_detail.*, consign_box.box_no')
    ->from('consign_box_detail')
    ->join('consign_box', 'consign_box_detail.id_box = consign_box.id', 'left')
    ->where('consign_box_detail.check_code', $check_code)
    ->where('consign_box_detail.product_code', $product_code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  ///----- get all product in box
  public function get_consign_box_details($id_box, $check_code)
  {
    $rs = $this->db
    ->select('consign_box_detail.*, consign_box.box_no')
    ->from('consign_box_detail')
    ->join('consign_box', 'consign_box_detail.id_box = consign_box.id', 'left')
    ->where('consign_box_detail.id_box', $id_box)
    ->where('consign_box_detail.check_code', $check_code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function get_consign_box_detail($id_box, $check_code, $product_code)
  {
    $rs = $this->db
    ->where('id_box', $id_box)
    ->where('check_code', $check_code)
    ->where('product_code', $product_code)
    ->get('consign_box_detail');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function add_box($check_code, $barcode)
  {
    $arr = array(
      'code' => $barcode,
      'check_code' => $check_code,
      'box_no' => $this->get_max_box_no($check_code) + 1
    );

    return $this->db->insert('consign_box', $arr);
  }


  public function add_box_detail($ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('consign_box_detail', $ds);
    }

    return FALSE;
  }


  public function get_box($check_code, $barcode)
  {
    $rs = $this->db->where('code', $barcode)->where('check_code', $check_code)->get('consign_box');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_box_qty($id_box, $check_code)
  {
    $rs = $this->db
    ->select_sum('qty', 'qty')
    ->where('id_box', $id_box)
    ->where('check_code', $check_code)
    ->get('consign_box_detail');

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }


  //--- get height box no in document
  public function get_max_box_no($check_code)
  {
    $rs = $this->db->select_max('box_no')->where('check_code', $check_code)->get('consign_box');

    return $rs->row()->box_no === NULL ? 0 : $rs->row()->box_no;
  }




  public function change_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('consign_check');
  }



  public function set_valid($code, $status)
  {
    return $this->db->set('valid', $status)->where('code', $code)->update('consign_check');
  }



  ///---- set all  stock_qty to 0
  public function reset_stock_qty($code)
  {
    return $this->db->set('stock_qty', 0)->where('check_code', $code)->update('consign_check_detail');
  }

  //-- ลบรายการที่เมื่อมีการโหลดยอดตั้งต้นใหม่แล้วไม่มีรายการนี้อยู่แล้วออก
  public function delete_no_item_details($code)
  {
    $this->db
    ->where('check_code', $code)
    ->where('stock_qty', 0, FALSE) //--- set FALSE fo not escape string
    ->where('qty', 0, FALSE); //--- set FALSE fo not escape string

    return $this->db->delete('consign_check_detail');
  }



  //--- ลบรายการในกล่องทั้งหมด
  public function delete_all_box_details($code)
  {
    return $this->db->where('check_code', $code)->delete('consign_box_detail');
  }



  //--- ลบกล่องทั้งหมด
  public function delete_all_box($code)
  {
    return $this->db->where('check_code', $code)->delete('consign_box');
  }



  //--- ลบรายการตั้งต้นและรายการตรวจนับทั้งหมด
  public function delete_all_details($code)
  {
    return $this->db->where('check_code', $code)->delete('consign_check_detail');
  }



  public function count_rows($ds = array())
  {
    $this->db
    ->select('consign_check.code')
    ->from('consign_check')
    ->join('zone', 'consign_check.zone_code = zone.code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('consign_check.code', $s['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db
      ->like('consign_check.customer_code', $ds['customer'])
      ->or_like('consign_check.customer_name', $ds['customer']);
    }

    if(!empty($ds['zone']))
    {
      $this->db
      ->like('zone.code', $ds['zone'])
      ->or_like('zone.name', $ds['zone']);
    }


    if(!empty($ds['consign_code']))
    {
      $this->db->like('consign_code', $ds['consign_code']);
    }


    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if($ds['valid'] != 'all')
    {
      $this->db->where('valid', $ds['valid']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }



  public function get_list($ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('consign_check.*, zone.name AS zone_name')
    ->from('consign_check')
    ->join('zone', 'consign_check.zone_code = zone.code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('consign_check.code', $s['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db
      ->like('consign_check.customer_code', $ds['customer'])
      ->or_like('consign_check.customer_name', $ds['customer']);
    }

    if(!empty($ds['zone']))
    {
      $this->db
      ->like('zone.code', $ds['zone'])
      ->or_like('zone.name', $ds['zone']);
    }


    if(!empty($ds['consign_code']))
    {
      $this->db->like('consign_code', $ds['consign_code']);
    }


    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if($ds['valid'] != 'all')
    {
      $this->db->where('valid', $ds['valid']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']));
    }

    if(!empty($perpage))
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM consign_check WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }



} //--- end class


 ?>
