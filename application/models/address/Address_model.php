<?php
class Address_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_shipping_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('address_ship_to');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_default_address($code)
  {
    $rs = $this->db
    ->where('code', $code)
    ->order_by('is_default', 'DESC')
    ->limit(1)
    ->get('address_ship_to');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_shipping_address($code)
  {
    $rs = $this->db->where('code', $code)->get('address_ship_to');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }



  public function add_shipping_address(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('address_ship_to', $ds);
    }

    return FALSE;
  }



  public function update_shipping_address($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update('address_ship_to', $ds);
  }



  public function delete_shipping_address($id)
  {
    return $this->db->where('id', $id)->delete('address_ship_to');
  }



  public function set_default_shipping_address($id)
  {
    return $this->db->set('is_default', 1)->where('id', $id)->update('address_ship_to');
  }


  public function unset_default_shipping_address($code)
  {
    $this->db->set('is_default', 0)
    ->where('code', $code)
    ->where('is_default', 1);

    return $this->db->update('address_ship_to');
  }



  public function get_id($code, $address = NULL)
  {
    $this->db->select('id')->where('code', $code);
    if($address != NULL)
    {
      $this->db->where('address', $address);
    }
    $this->db->order_by('is_default', 'DESC')->limit(1);
    $rs = $this->db->get('address_ship_to');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }


  public function count_address($code)
  {
    return $this->db->where('code', $code)->count_all_results('address_ship_to');
  }




} //--- end class

 ?>
