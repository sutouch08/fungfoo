<?php
class Channels_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('channels', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('channels', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('channels');
  }


  public function count_rows($c_code = '', $c_name = '')
  {
    $this->db->select('code');
    if($c_code != '')
    {
      $this->db->like('code', $c_code);
    }

    if($c_name != '')
    {
      $this->db->like('name', $c_name);
    }

    $rs = $this->db->get('channels');

    return $rs->num_rows();
  }




  public function get_channels($code)
  {
    $rs = $this->db->where('code', $code)->get('channels');
    if($rs->num_rows() == 1 )
    {
      return $rs->row();
    }

    return array();
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('channels');
    if($rs->num_rows() == 1 )
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_default()
  {
    $rs = $this->db->where('is_default', 1)->get('channels');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }

  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('channels');
    if($rs->num_rows() > 0)
    {
      return $rs->row()->name;
    }

    return FALSE;
  }


  public function get_data($c_code = '', $c_name = '', $perpage = '', $offset = '')
  {
    if($c_code != '')
    {
      $this->db->like('code', $c_code);
    }

    if($c_name != '')
    {
      $this->db->like('name', $c_name);
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('channels');

    return $rs->result();
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('channels');

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

    $rs = $this->db->where('name', $name)->get('channels');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



}
?>
