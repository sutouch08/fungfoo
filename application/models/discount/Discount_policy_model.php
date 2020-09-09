<?php
class Discount_policy_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    return $this->db->insert('discount_policy', $ds);
  }



  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('discount_policy', $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    $result = new stdClass();
    $result->status = TRUE;

    $this->db->trans_start();

    //---- remove rule from policy before delete
    $this->db->set('id_policy', NULL)->where('id_policy', $id)->update('discount_rule');

    //--- delete policy
    $this->db->where('id', $id)->delete('discount_policy');

    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
       $result->status = FALSE;
       $result->message = $this->db->error();
    }

    return $result;
  }





  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('discount_policy');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_code($id)
  {
    $rs = $this->db->select('code')
    ->where('id', $id)
    ->get('discount_policy');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db->select('name')
    ->where('id', $id)
    ->get('discount_policy');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }





  public function count_rows($code, $name, $active, $start, $end)
  {
    $qr = "SELECT id FROM discount_policy WHERE code != '' ";

    if($code != "")
    {
      $qr .= "AND code LIKE '%".$code."%' ";
    }

    if($name != "")
    {
      $qr .= "AND name LIKE '%".$name."%' ";
    }

    if($active != 2)
    {
      $qr .= "AND active = ".$active." ";
    }

    if($start != "" && $end != "")
    {
      $qr .= "AND (start_date >= '".db_date($start)."' OR end_date <= '".db_date($end)."') ";

    }

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->num_rows();
    }

    return 0;
  }



  public function get_data($code, $name, $active, $start, $end, $perpage = '', $offset = '')
  {
    $qr = "SELECT * FROM discount_policy WHERE code != '' ";

    if($code != "")
    {
      $qr .= "AND code LIKE '%".$code."%' ";
    }

    if($name != "")
    {
      $qr .= "AND name LIKE '%".$name."%' ";
    }

    if($active != 2)
    {
      $qr .= "AND active = ".$active." ";
    }

    if($start != "" && $end != "")
    {
      $qr .= "AND (start_date >= '".db_date($start)."' OR end_date <= '".db_date($end)."') ";

    }

    $qr .= "ORDER BY code DESC";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }





  public function get_policy_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get('discount_policy');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return array();
  }




  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM discount_policy WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }



  public function search($txt)
  {
    $rs = $this->db->select('id')
    ->like('code', $txt)
    ->like('name', $txt)
    ->get('discount_policy');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


  public function is_exists($code)
  {
    if(!empty($code))
    {
      $rs = $this->db->where('code', $code)->get('discount_policy');
      if($rs->num_rows() === 1)
      {
        return TRUE;
      }
    }

    return FALSE;
  }

} //--- end class

 ?>
