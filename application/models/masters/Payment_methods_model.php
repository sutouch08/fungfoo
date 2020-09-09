<?php
class Payment_methods_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('payment_method', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('payment_method', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('payment_method');
  }


  public function count_rows($c_code = '', $c_name = '', $term = '')
  {
    $this->db->select('payment_method.code')
    ->from('payment_method')
    ->join('payment_role', 'payment_method.role = payment_role.id', 'left');

    if($term == 1)
    {
      $this->db->where('has_term', 1);
    }

    if(!empty($c_code))
    {
      $this->db->like('code', $c_code);
    }

    if(!empty($c_name))
    {
      $this->db->like('name', $c_name);
    }

    if(!empty($c_role))
    {
      $this->db->where('role', $c_role);
    }

    return $this->db->count_all_results();

  }




  public function get_payment_methods($code)
  {
    $rs = $this->db->where('code', $code)->get('payment_method');
    return $rs->row();
  }



  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('payment_method');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_default()
  {
    $rs = $this->db->where('is_default', 1)->get('payment_method');
    if($rs->num_rows() == 1)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_role($code)
  {
    $rs = $this->db->select('role')->where('code', $code)->get('payment_method');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->role;
    }

    return FALSE;
  }



  public function get_role_list()
  {
    $rs = $this->db->get('payment_role');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_data($c_code = '', $c_name = '', $c_role = '', $term = '', $perpage = '', $offset = '')
  {
    $this->db->select('payment_method.code, payment_method.name')
    ->select('payment_method.has_term, payment_method.is_default')
    ->select('payment_role.name as role_name, payment_method.date_upd')
    ->from('payment_method')
    ->join('payment_role', 'payment_method.role = payment_role.id', 'left');

    if($term == 1)
    {
      $this->db->where('has_term', 1);
    }

    if(!empty($c_code))
    {
      $this->db->like('code', $c_code);
    }

    if(!empty($c_name))
    {
      $this->db->like('name', $c_name);
    }

    if(!empty($c_role))
    {
      $this->db->where('role', $c_role);
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    return $rs->result();
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('payment_method');

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

    $rs = $this->db->where('name', $name)->get('payment_method');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }




  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('payment_method');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return FALSE;
  }



  public function has_term($code)
  {
    $rs = $this->db->where('code', $code)->where('has_term', 1)->get('payment_method');
    if($rs->num_rows() == 1)
    {
      return TRUE;
    }

    return FALSE;
  }

}
?>
