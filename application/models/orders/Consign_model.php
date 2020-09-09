<?php
class Consign_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function count_rows(array $ds = array(), $role = 'C')
  {
    $this->db->select('status');
    $this->db->where('role', $role);

    //---- เลขที่เอกสาร
    if($ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    //--- รหัส/ชื่อ ลูกค้า
    if($ds['customer'] != '')
    {
      $customers = customer_in($ds['customer']);
      $this->db->where_in('customer_code', $customers);
    }

    //---- user name / display name
    if($ds['user'] != '')
    {
      $users = user_in($ds['user']);
      $this->db->where_in('user', $users);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->get('orders');


    return $rs->num_rows();
  }





  public function get_data(array $ds = array(), $perpage = '', $offset = '', $role = 'S')
  {
      $this->db->where('role', $role);

      //---- เลขที่เอกสาร
      if($ds['code'] != '')
      {
        $this->db->like('code', $ds['code']);
      }

      //--- รหัส/ชื่อ ลูกค้า
      if($ds['customer'] != '')
      {
        $customers = customer_in($ds['customer']);
        $this->db->where_in('customer_code', $customers);
      }

      //---- user name / display name
      if($ds['user'] != '')
      {
        $users = user_in($ds['user']);
        $this->db->where_in('user', $users);
      }

      //---- เลขที่อ้างอิงออเดอร์ภายนอก
      if($ds['reference'] != '')
      {
        $this->db->like('reference', $ds['reference']);
      }

      //---เลขที่จัดส่ง
      if($ds['ship_code'] != '')
      {
        $this->db->like('shipping_code', $ds['ship_code']);
      }

      //--- ช่องทางการขาย
      if($ds['channels'] != '')
      {
        $this->db->where('channels_code', $ds['channels']);
      }

      //--- ช่องทางการชำระเงิน
      if($ds['payment'] != '')
      {
        $this->db->where('payment_code', $ds['payment']);
      }

      if($ds['from_date'] != '' && $ds['to_date'] != '')
      {
        $this->db->where('date_add >=', from_date($ds['from_date']));
        $this->db->where('date_add <=', to_date($ds['to_date']));
      }

      $this->db->order_by('code', 'DESC');

      if($perpage != '')
      {
        $offset = $offset === NULL ? 0 : $offset;
        $this->db->limit($perpage, $offset);
      }

      $rs = $this->db->get('orders');

      return $rs->result();
  }





  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM orders WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }
}


 ?>
