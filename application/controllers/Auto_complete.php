<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auto_complete extends CI_Controller
{
  public $ms;
  public function __construct()
  {
    parent::__construct();
    //$language = getConfig('LANGUAGE');
    $display_lang = get_cookie('display_lang');
    $this->language = empty($display_lang) ? 'thai' : $display_lang;
    $this->lang->load($this->language, $this->language);
  }


  public function get_sender()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $rs = $this->db
    ->select('id, name')
    ->like('name', $txt)
    ->limit(20)
    ->get('address_sender');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->id.' | '.$rd->name;
      }
    }

    echo json_encode($sc);
  }


  public function get_customer_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $rs = $this->db
    ->select('code, name')
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->limit(20)
    ->get('customers');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->code.' | '.$rd->name;
      }
    }

    echo json_encode($sc);
  }




public function get_style_code()
{
  $sc = array();

  $this->db->select('code')
  ->like('code', $_REQUEST['term'])
  ->where('is_deleted', 0)
  ->order_by('code', 'ASC')
  ->limit(20);
  $qs = $this->db->get('product_style');

  if($qs->num_rows() > 0)
  {
    foreach($qs->result() as $rs)
    {
      $sc[] = $rs->code;
    }
  }
  else
  {
    $sc[] = "not found";
  }

	echo json_encode($sc);
}


public function get_item_code()
{
  $sc = array();
  $this->db
  ->select('products.code')
  ->from('products')
  ->join('product_color', 'products.color_code = product_color.code', 'left')
  ->join('product_size', 'products.size_code = product_size.code', 'left')
  ->like('products.code', $_REQUEST['term'])
  ->order_by('products.style_code', 'ASC')
  ->order_by('products.color_code', 'ASC')
  ->order_by('product_size.position', 'ASC')
  ->limit(20);

  $qs = $this->db->get();
  if($qs->num_rows() > 0)
  {
    foreach($qs->result() as $rs)
    {
      $sc[] = $rs->code;
    }
  }
  else
  {
    $sc[] = "not found";
  }

  echo json_encode($sc);
}




  public function sub_district()
  {
    $sc = array();
    $adr = $this->db->like('tumbon', $_REQUEST['term'])->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->tumbon.'>>'.$rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }


  public function district()
  {
    $sc = array();
    $adr = $this->db->select("amphur, province, zipcode")
    ->like('amphur', $_REQUEST['term'])
    ->group_by('amphur')
    ->group_by('province')
    ->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }




  public function get_vender_code_and_name()
  {
    $sc = array();
    $this->db->select('code, name')->where('active', 1);

    if(trim($_REQUEST['term']) != '*')
    {
      $this->db->group_start();
      $this->db->like('code', $_REQUEST['term'])->or_like('name', $_REQUEST['term']);
      $this->db->group_end();
    }

    $vender = $this->db->limit(20)->get('vender');

    if($vender->num_rows() > 0)
    {
      foreach($vender->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = label_value('no_content');
    }

    echo json_encode($sc);
  }



  //---- ค้นหาใบเบิกสินค้าแปรสภาพ
  //---- $all : TRUE => ทุกสถานะ
  //---- $all : FALSE => เฉพาะที่ยังไม่ปิด
  public function get_transform_code($all = FALSE)
  {
    $txt = $_REQUEST['term'];
    $sc = array();

    if($all === FALSE)
    {
      $this->db->where('is_closed', 0);
    }

    if($txt != '*')
    {
      $this->db->like('order_code', $txt);
    }

    $this->db->limit(20);
    $code = $this->db->get('order_transform');
    if($code->num_rows() > 0)
    {
      foreach($code->result() as $rs)
      {
        $sc[] = $rs->order_code;
      }
    }
    else
    {
      $sc[] = 'ไม่พบข้อมูล';
    }

    echo json_encode($sc);
  }



  public function get_po_code($vendor = FALSE)
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('code')->where_in('status', array('1', '2'));
    if($vendor !== FALSE)
    {
      $this->db->where('vender_code', $vendor);
    }

    if($txt != '*')
    {
      $this->db->like('code', $txt);
    }

    $po = $this->db->get('po');

    if($po->num_rows() > 0)
    {
      foreach($po->result() as $rs)
      {
        $sc[] = $rs->code;
      }
    }
    else
    {
      $sc[] = label_value('no_content');
    }

    echo json_encode($sc);
  }


  public function get_po_code_and_vender_name($vendor = FALSE)
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db
    ->select('po.code, vender.name')
    ->from('po')
    ->join('vender', 'po.vender_code = vender.code', 'left')
    ->where_in('po.status', array('1', '2'));
    if($vendor !== FALSE)
    {
      $this->db->where('vender_code', $vendor);
    }

    if($txt != '*')
    {
      $this->db->like('po.code', $txt);
    }

    $po = $this->db->get();

    if($po->num_rows() > 0)
    {
      foreach($po->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = label_value('no_content');
    }

    echo json_encode($sc);
  }


  public function get_valid_lend_code($customer_code = '')
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('order_code');
    if($txt != '*')
    {
      $this->db->like('order_code', $txt);
    }

    if(!empty($customer_code))
    {
      $this->db->where('customer_code', $customer_code);
    }

    $this->db->where('valid' , 0)->group_by('order_code')->limit(20);
    $rs = $this->db->get('order_lend_detail');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ds)
      {
        $sc[] = $ds->order_code;
      }
    }

    echo json_encode($sc);
  }


  public function get_zone_code_and_name($warehouse = '')
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('code, name');

    if(!empty($warehouse))
    {
      $this->db->where('warehouse_code', $warehouse);
    }

    $this->db->like('code', $txt);
    $this->db->or_like('name', $txt);
    $rs = $this->db->get('zone');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $zone)
      {
        $sc[] = $zone->code.' | '.$zone->name;
      }
    }
    else
    {
      $sc[] = label_value('no_content');
    }

    echo json_encode($sc);
  }



  public function get_zone_code()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('code');
    if($txt != '*')
    {
      $this->db->like('code', $txt);
    }
    $this->db->limit(20);
    $zone = $this->db->get('zone');

    if(!empty($zone))
    {
      foreach($zone->result() as $rs)
      {
        $sc[] = $rs->code;
      }
    }

    echo json_encode($sc);
  }



  public function get_transform_zone()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db
    ->select('zone.code AS code, zone.name AS name')
    ->from('zone')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
    ->where('warehouse.role', 6); //--- 6 =  คลังแปรสภาพ ดู table warehouse_role

    if($txt != '*')
    {
      $this->db->like('zone.code', $txt);
      $this->db->or_like('zone.name', $txt);
    }

    $this->db->limit(20);

    $zone = $this->db->get();

    if($zone->num_rows() > 0)
    {
      foreach($zone->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = label_value('no_content');
    }

    echo json_encode($sc);
  }




  public function get_lend_zone($customer_code = '')
  {
    $sc = array();
    if(!empty($customer_code))
    {
      $txt = $_REQUEST['term'];
      $this->db
      ->select('zone.code AS code, zone.name AS name')
      ->from('zone')
      ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
      ->join('zone_customer', 'zone_customer.zone_code = zone.code')
      ->where('warehouse.role', 8) //--- 8 =  คลังยืมสินค้า ดู table warehouse_role
      ->where('zone_customer.customer_code', $customer_code);

      if($txt != '*')
      {
        $this->db->like('zone.code', $txt);
        $this->db->or_like('zone.name', $txt);
      }

      $this->db->limit(20);

      $zone = $this->db->get();

      if($zone->num_rows() > 0)
      {
        foreach($zone->result() as $rs)
        {
          $sc[] = $rs->code.' | '.$rs->name;
        }
      }
      else
      {
        $sc[] = label_value('no_content');
      }
    }
    else
    {
      $sc[] = "กรุณาระบุผู้ยืม";
    }

    echo json_encode($sc);
  }





  public function get_sponsor()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->ms->select('BpCode, BpName');

    if($txt != '*')
    {
      $this->ms->like('BpCode', $txt)->or_like('BpName', $txt);
    }

    $this->ms->limit(20);

    $sponsor = $this->ms->get('OOAT');

    if($sponsor->num_rows() > 0)
    {
      foreach($sponsor->result() as $rs)
      {
        $sc[] = $rs->BpCode.' | '.$rs->BpName;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }



  public function get_support()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->ms->select('CardCode, CardName')->where('CardType', 'C');
    if($txt != '*')
    {
      $this->ms->like('CardCode', $txt)->or_like('CardName', $txt);
    }
    $this->ms->limit(20);

    $sponsor = $this->ms->get('OCRD');

    if($sponsor->num_rows() > 0)
    {
      foreach($sponsor->result() as $rs)
      {
        $sc[] = $rs->CardCode.' | '.$rs->CardName;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }




  public function get_employee()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->where('active', 1);
    if($txt != '*')
    {
      $this->db->group_start();
      $this->db->like('code', $txt)->or_like('name', $txt);
      $this->db->group_end();
    }

    $this->db->limit(20);

    $emp = $this->db->get('employee');

    if($emp->num_rows() > 0)
    {
      foreach($emp->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }



  public function get_user()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('uname, name');
    if($txt != '*')
    {
      $this->db->like('uname', $txt)->or_like('name', $txt);
    }
    $this->db->limit(20);

    $sponsor = $this->db->get('user');

    if($sponsor->num_rows() > 0)
    {
      foreach($sponsor->result() as $rs)
      {
        $sc[] = $rs->uname.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }


  public function get_consign_zone($customer_code = '')
  {
    if($customer_code == '')
    {
      echo json_encode(array('เลือกลูกค้าก่อน'));
    }
    else
    {
      $this->db
      ->select('zone.code, zone.name')
      ->from('zone_customer')
      ->join('zone', 'zone.code = zone_customer.zone_code', 'left')
      ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
      ->where('warehouse.role', 2) //--- 2 = คลังฝากขาย
      ->where('zone_customer.customer_code', $customer_code);

      if($_REQUEST['term'] != '*')
      {
        $this->db->like('zone.code', $_REQUEST['term']);
        $this->db->or_like('zone.name', $_REQUEST['term']);
      }

      $this->db->limit(20);
      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        $ds = array();
        foreach($rs->result() as $rd)
        {
          $ds[] = $rd->code.' | '.$rd->name;
        }

        echo json_encode($ds);
      }
      else
      {
        echo json_encode(array('ไม่พบโซน'));
      }
    }
  }



  public function get_product_code()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $rs = $this->db
    ->select('code')
    ->like('code', $txt)
    ->limit(20)
    ->get('products');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $pd)
      {
        $sc[] = $pd->code;
      }
    }
    else
    {
      $sc[] = 'no item found';
    }

    echo json_encode($sc);
  }


  public function get_warehouse_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $sc  = array();
    $this->db->select('code, name');
    if($txt != '*')
    {
      $this->db->like('code', $txt);
      $this->db->or_like('name', $txt);
    }
    $rs = $this->db->limit(20)->get('warehouse');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $wh)
      {
        $sc[] = $wh->code.' | '.$wh->name;
      }
    }
    else
    {
      $sc[] = 'not found';
    }

    echo json_encode($sc);
  }



  public function get_color_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $this->db->select('code, name');
    if($txt != '*')
    {
      $this->db->like('code', $txt);
      $this->db->or_like('name', $txt);
    }
    $rs = $this->db->order_by('code', 'ASC')->limit(20)->get('product_color');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $co)
      {
        $sc[] = $co->code.' | '.$co->name;
      }
    }
    else
    {
      $sc[] = "not_fount";
    }

    echo json_encode($sc);
  }


  public function get_size_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $this->db->select('code, name');
    if($txt != '*')
    {
      $this->db->like('code', $txt, 'after');
      $this->db->or_like('name', $txt, 'after');
    }
    $rs = $this->db->order_by('position', 'ASC')->limit(20)->get('product_size');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $co)
      {
        $sc[] = $co->code.' | '.$co->name;
      }
    }
    else
    {
      $sc[] = "not_fount";
    }

    echo json_encode($sc);
  }

} //-- end class
?>
