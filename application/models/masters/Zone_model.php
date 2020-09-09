<?php
class Zone_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  //--- add new zone (use with sync only)
  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('zone', $ds);
    }

    return FALSE;
  }


  //--- update zone with sync only
  public function update($id, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('zone', $ds);
    }

    return FALSE;
  }


  //--- add new customer to zone
  public function add_customer(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('zone_customer', $ds);
    }

    return FALSE;
  }



  //--- remove customer from connected zone
  public function delete_customer($id)
  {
    return $this->db->where('id', $id)->delete('zone_customer');
  }


  //---- delete zone  must use only mistake on sap and delete zone in SAP already
  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('zone');
  }

  //--- check zone exists or not
  public function is_exists($code)
  {
    if($this->db->where('code', $code)->count_all_results('zone') > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  //--- check zone exists by id
  public function is_exists_id($id)
  {
    if($this->db->where('id', $id)->count_all_results('zone') > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_code($code, $old_code = NULL)
  {
    $this->db->where('code', $code);
    if(!empty($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->get('zone');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $old_name = NULL)
  {
    $this->db->where('name', $name);
    if(!empty($old_name))
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->get('warehouse');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  //--- check customer exists in zone or not
  public function is_exists_customer($zone_code, $customer_code)
  {
    $rs = $this->db
    ->where('zone_code', $zone_code)
    ->where('customer_code', $customer_code)
    ->count_all_results('zone_customer');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds['customer']))
    {
      return $this->count_rows_customer($ds);
    }

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    return $this->db->count_all_results('zone');
  }




  private function count_rows_customer(array $ds = array())
  {
    $this->db
    ->from('zone_customer')
    ->join('zone', 'zone.code = zone_customer.zone_code')
    ->join('customers', 'zone_customer.customer_code = customers.code')
    ->like('customers.code', $ds['customer'])
    ->or_like('customers.name', $ds['customer']);

    if(!empty($ds['code']))
    {
      $this->db->like('zone.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('zone.name', $ds['name']);
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('zone.warehouse_code', $ds['warehouse']);
    }

    return $this->db->count_all_results();
  }





  public function get_list(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    //--- if search for customer
    if(!empty($ds['customer']))
    {
      return $this->get_list_customer($ds);
    }

    $this->db
    ->select('zone.code AS code, zone.name AS name, warehouse.name AS warehouse_name')
    ->from('zone')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('zone.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('zone.name', $ds['name']);
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('zone.warehouse_code', $ds['warehouse']);
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






  private function get_list_customer(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('zone.code AS code, zone.name AS name, warehouse.name AS warehouse_name')
    ->select('customers.code AS customer_code, customers.name AS customer_name')
    ->from('zone_customer')
    ->join('zone', 'zone.code = zone_customer.zone_code')
    ->join('customers', 'zone_customer.customer_code = customers.code')
    ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
    ->like('customers.code', $ds['customer'])
    ->or_like('customers.name', $ds['customer']);

    if(!empty($ds['code']))
    {
      $this->db->like('zone.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('zone.name', $ds['name']);
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('zone.warehouse_code', $ds['warehouse']);
    }

    $this->db->group_by('zone.code');

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






  public function count_customer($code)
  {
    return $this->db->where('zone_code', $code)->count_all_results('zone_customer');
  }


  public function get_customers($zone_code)
  {

    $rs = $this->db->where('zone_code', $zone_code)->get('zone_customer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function get($code)
  {
    $rs = $this->db
    ->select('zone.code AS code, zone.name AS name, warehouse.code AS warehouse_code, warehouse.name AS warehouse_name')
    ->from('zone')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
    ->where('zone.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }






  public function get_warehouse_code($zone_code)
  {
    $rs = $this->db->select('warehouse_code')->where('code', $zone_code)->get('zone');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->warehouse_code;
    }

    return FALSE;
  }






  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('zone');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function get_zone_detail_in_warehouse($code, $warehouse)
  {
    $rs = $this->db->where('warehouse_code', $warehouse)->where('code', $code)->get('zone');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function search($txt, $warehouse_code)
  {
    if($warehouse_code != '')
    {
      $this->db->where('warehouse_code', $warehouse_code);
    }

    $rs = $this->db->like('code', $txt)->or_like('name', $txt)->get('zone');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function get_all_zone()
  {
    $rs = $this->db->get('zone');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }

} //--- end class

 ?>
