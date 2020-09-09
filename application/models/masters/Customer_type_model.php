<?php
class Customer_type_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('customer_type', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('customer_type', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('customer_type');
  }


  public function count_rows($code = '', $name = '')
  {
    $this->db->select('code');

    if($code != '')
    {
      $this->db->like('code', $code);
    }

    if($name != '')
    {
      $this->db->like('name', $name);
    }

    $rs = $this->db->get('customer_type');

    return $rs->num_rows();
  }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('customer_type');
    return $rs->row();
  }




  public function get_name($code)
  {
    if($code === NULL OR $code === '')
    {
      return $code;
    }
    
    $rs = $this->db->select('name')->where('code', $code)->get('customer_type');
    return $rs->row()->name;
  }




  public function get_data($code = '', $name = '', $perpage = '', $offset = '')
  {
    if($code != '')
    {
      $this->db->like('code', $code);
    }

    if($name != '')
    {
      $this->db->like('name', $name);
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('customer_type');

    return $rs->result();
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('customer_type');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $old_name = '')
  {
    if($old_name != '')
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->where('name', $name)->get('customer_type');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



}
?>
