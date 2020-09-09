<?php
class Saleman_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_salemans()
  {
    $rs = $this->db->get('saleman');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('saleman');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function is_exists($code)
  {
    $rs = $this->db->where('code', $code)->get('saleman');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_duplicate($new_code, $old_code)
  {
    $rs = $this->db->where('code', $new_code)->where('code !=', $old_code)->get('saleman');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add($ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('saleman', $ds);
    }

    return FALSE;
  }


  public function update($code, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('saleman', $ds);
    }

    return FALSE;
  }



  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('saleman');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function count_rows($ds = array())
  {
    if($ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }


    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    return $this->db->count_all_results('saleman');
  }


  public function get_list($ds = array(), $limit = NULL, $offset = 0)
  {
    if($ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }


    if(!empty($limit))
    {
      $this->db->limit($limit, $offset);
    }

    $rs = $this->db->get('saleman');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function has_order($code)
  {
    return $this->db->where('sale_code', $code)->count_all_results('orders');
  }


  public function has_sold_order($code)
  {
    return $this->db->where('sale_code', $code)->count_all_results('order_sold');
  }



  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('saleman');
  }


  public function get_data(){
    $rs = $this->db->get('saleman');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


} //--- End class

 ?>
