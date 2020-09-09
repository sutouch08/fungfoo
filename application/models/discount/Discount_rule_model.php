<?php
class Discount_rule_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    $rs = $this->db->insert('discount_rule', $ds);
    if($rs)
    {
      return $this->db->insert_id();
    }

    return FALSE;
  }



  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('discount_rule', $ds);
    }

    return FALSE;
  }



  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('discount_rule');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return array();
  }

  /*
  |----------------------------------
  | BEGIN ใช้สำหรับแสดงรายละเอียดในหน้าพิมพ์
  |----------------------------------
  */

  public function getCustomerRuleList($id)
  {
    $qr  = "SELECT cs.code, cs.name FROM discount_rule_customer AS cr ";
    $qr .= "JOIN customers AS cs ON cr.customer_code = cs.code ";
    $qr .= "WHERE cr.id_rule = ".$id;

    return $this->db->query($qr);
  }

  public function getCustomerGroupRule($id)
  {
    $qr  = "SELECT cs.code, cs.name FROM discount_rule_customer_group AS cr ";
    $qr .= "JOIN customer_group AS cs ON cr.group_code = cs.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }


  public function getCustomerTypeRule($id)
  {
    $qr  = "SELECT cs.code, cs.code, cs.name FROM discount_rule_customer_type AS cr ";
    $qr .= "JOIN customer_type AS cs ON cr.type_code = cs.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }


  public function getCustomerKindRule($id)
  {
    $qr  = "SELECT cs.code, cs.code, cs.name FROM discount_rule_customer_kind AS cr ";
    $qr .= "JOIN customer_kind AS cs ON cr.kind_code = cs.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }

  public function getCustomerAreaRule($id)
  {
    $qr  = "SELECT cs.code, cs.code, cs.name FROM discount_rule_customer_area AS cr ";
    $qr .= "JOIN customer_area AS cs ON cr.area_code = cs.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }

  public function getCustomerClassRule($id)
  {
    $qr  = "SELECT cs.code, cs.code, cs.name FROM discount_rule_customer_class AS cr ";
    $qr .= "JOIN customer_class AS cs ON cr.class_code = cs.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }

  public function getProductItemRule($id)
  {
    $qr = "SELECT product_code AS code FROM discount_rule_product WHERE id_rule = {$id}";

    return $this->db->query($qr);
  }


  public function getProductStyleRule($id)
  {
    $qr = "SELECT ps.code FROM discount_rule_product_style AS sr ";
    $qr .= "JOIN product_style AS ps ON sr.style_code = ps.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }


  public function getProductGroupRule($id)
  {
    $qr = "SELECT ps.code, ps.name FROM discount_rule_product_group AS sr ";
    $qr .= "JOIN product_group AS ps ON sr.group_code = ps.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }


  public function getProductSubGroupRule($id)
  {
    $qr = "SELECT ps.code, ps.name FROM discount_rule_product_sub_group AS sr ";
    $qr .= "JOIN product_sub_group AS ps ON sr.sub_group_code = ps.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }

  public function getProductTypeRule($id)
  {
    $qr = "SELECT ps.code, ps.name FROM discount_rule_product_type AS sr ";
    $qr .= "JOIN product_type AS ps ON sr.type_code = ps.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }

  public function getProductKindRule($id)
  {
    $qr = "SELECT ps.code, ps.name FROM discount_rule_product_kind AS sr ";
    $qr .= "JOIN product_kind AS ps ON sr.kind_code = ps.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }

  public function getProductCategoryRule($id)
  {
    $qr = "SELECT ps.code, ps.name FROM discount_rule_product_category AS sr ";
    $qr .= "JOIN product_category AS ps ON sr.category_code = ps.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }


  public function getProductBrandRule($id)
  {
    $qr = "SELECT ps.code, ps.name FROM discount_rule_product_brand AS sr ";
    $qr .= "JOIN product_brand AS ps ON sr.brand_code = ps.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }


  public function getProductYearRule($id)
  {
    $qr = "SELECT year FROM discount_rule_product_year WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }


  public function getChannelsRule($id)
  {
    $qr = "SELECT cn.name FROM discount_rule_channels AS cr ";
    $qr .= "JOIN channels AS cn ON cr.channels_code = cn.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }


  public function getPaymentRule($id)
  {
    $qr = "SELECT cn.name FROM discount_rule_payment AS cr ";
    $qr .= "JOIN payment_method AS cn ON cr.payment_code = cn.code ";
    $qr .= "WHERE id_rule = ".$id;

    return $this->db->query($qr);
  }


  /*
  |----------------------------------
  | END ใช้สำหรับแสดงรายละเอียดในหน้าพิมพ์
  |----------------------------------
  */



  /*
  |----------------------------------
  | BEGIN ใช้สำหรับหน้ากำหนดเงื่อนไข
  |----------------------------------
  */
  public function getRuleCustomerId($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->customer_code] = $rd->customer_code;
      }
    }

    return $sc;
  }



  public function getRuleCustomerGroup($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer_group');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->group_code] = $rd->group_code;
      }
    }
    return $sc;
  }


  public function getRuleCustomerType($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer_type');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->type_code] = $rd->type_code;
      }
    }
    return $sc;
  }


  public function getRuleCustomerKind($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer_kind');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->kind_code] = $rd->kind_code;
      }
    }
    return $sc;
  }



  public function getRuleCustomerArea($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer_area');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->area_code] = $rd->area_code;
      }
    }

    return $sc;
  }



  public function getRuleCustomerClass($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer_class');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->class_code] = $rd->class_code;
      }
    }

    return $sc;
  }




  public function getRuleProductItem($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->product_code] = $rd->product_code;
      }
    }

    return $sc;
  }


  public function getRuleProductStyle($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_style');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->style_code] = $rd->style_code;
      }
    }

    return $sc;
  }




  public function getRuleProductGroup($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_group');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->group_code] = $rd->group_code;
      }
    }

    return $sc;
  }



  public function getRuleProductSubGroup($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_sub_group');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->sub_group_code] = $rd->sub_group_code;
      }
    }

    return $sc;
  }




  public function getRuleProductType($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_type');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->type_code] = $rd->type_code;
      }
    }

    return $sc;
  }





  public function getRuleProductKind($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_kind');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->kind_code] = $rd->kind_code;
      }
    }

    return $sc;
  }



  public function getRuleProductCategory($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_category');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->category_code] = $rd->category_code;
      }
    }

    return $sc;
  }




  public function getRuleProductYear($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_year');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->year] = $rd->year;
      }
    }

    return $sc;
  }



  public function getRuleProductBrand($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_brand');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->brand_code] = $rd->brand_code;
      }
    }

    return $sc;
  }




  public function getRuleChannels($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_channels');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->channels_code] = $rd->channels_code;
      }
    }

    return $sc;
  }


  public function getRulePayment($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_payment');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->payment_code] = $rd->payment_code;
      }
    }

    return $sc;
  }




  public function set_all_customer($id, $value)
  {
    /*
    1. set all customer = 1
    2. delete customer rule
    3. delete customer_group rule;
    4. delete customer_type rule;
    5. delete customer_kind rule;
    6. delete customer_area rule;
    7. delete customer_class rule;
    */

    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'success';

    if($value === 1)
    {
      //--- start transection
      $this->db->trans_start();

      //--- 1
      $this->db->query("UPDATE discount_rule SET all_customer = 1 WHERE id = $id");

      //--- 2
      $this->db->query("DELETE FROM discount_rule_customer WHERE id_rule = $id");

      //--- 3
      $this->db->query("DELETE FROM discount_rule_customer_group WHERE id_rule = $id");

      //--- 4
      $this->db->query("DELETE FROM discount_rule_customer_type WHERE id_rule = $id");

      //--- 5
      $this->db->query("DELETE FROM discount_rule_customer_kind WHERE id_rule = $id");

      //--- 6
      $this->db->query("DELETE FROM discount_rule_customer_area WHERE id_rule = $id");

      //--- 7
      $this->db->query("DELETE FROM discount_rule_customer_class WHERE id_rule = $id");

      //--- end transection
      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $result->status = FALSE;
        $result->message = 'กำหนดลูกค้าทั้งหมดไม่สำเร็จ';
      }
    }
    else
    {
      $rs = $this->db->query("UPDATE discount_rule SET all_customer = 0 WHERE id = $id");
      if($rs === FALSE)
      {
        $result->status = FALSE;
        $result->message = 'กำหนดลูกค้าทั้งหมดไม่สำเร็จ';
      }
    }


    return $result;
  }





  public function set_customer_list($id, $cust_list)
  {

    /*
    1. set all customer = 0
    2. delete customers rule;
    2.1 set customer rule;
    3. delete customer_group rule;
    4. delete customer_type rule;
    5. delete customer_kind rule;
    6. delete customer_area rule;
    7. delete customer_class rule;
    */

    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'success';

    //---- start transection
    $this->db->trans_start();

    //--- 1.
    $this->db->query("UPDATE discount_rule SET all_customer = 0 WHERE id = $id");

    //--- 2.
    $this->db->query("DELETE FROM discount_rule_customer WHERE id_rule = $id");

    if(!empty($cust_list))
    {
      foreach($cust_list as $code)
      {
        $this->db->query("INSERT INTO discount_rule_customer (id_rule, customer_code) VALUES ($id, '$code')");
      }
    }

    //--- 3
    $this->db->query("DELETE FROM discount_rule_customer_group WHERE id_rule = $id");

    //--- 4
    $this->db->query("DELETE FROM discount_rule_customer_type WHERE id_rule = $id");

    //--- 5
    $this->db->query("DELETE FROM discount_rule_customer_kind WHERE id_rule = $id");

    //--- 6
    $this->db->query("DELETE FROM discount_rule_customer_area WHERE id_rule = $id");

    //--- 7
    $this->db->query("DELETE FROM discount_rule_customer_class WHERE id_rule = $id");

    //--- end transection
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $result->status = FALSE;
      $result->message = 'กำหนดรายชื่อลูกค้าไม่สำเร็จ';
    }

    return $result;

  }



  public function set_customer_attr($id, $group, $type, $kind, $area, $class)
  {
    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'message';

    //--- start transection
    $this->db->trans_start();

    //--- 1.
    $this->db->query("DELETE FROM discount_rule_customer WHERE id_rule = $id");

    //--- 2
    $this->db->query("DELETE FROM discount_rule_customer_group WHERE id_rule = $id");
    if(!empty($group))
    {
      foreach($group as $code)
      {
        $this->db->query("INSERT INTO discount_rule_customer_group (id_rule, group_code) VALUES ($id, '$code')");
      }
    }

    //--- 3
    $this->db->query("DELETE FROM discount_rule_customer_type WHERE id_rule = $id");
    if(!empty($type))
    {
      foreach($type as $code)
      {
        $this->db->query("INSERT INTO discount_rule_customer_type (id_rule, type_code) VALUES ($id, '$code')");
      }
    }

    //--- 4
    $this->db->query("DELETE FROM discount_rule_customer_kind WHERE id_rule = $id");
    if(!empty($kind))
    {
      foreach($kind as $code)
      {
        $this->db->query("INSERT INTO discount_rule_customer_kind (id_rule, kind_code) VALUES ($id, '$code')");
      }
    }

    //--- 5
    $this->db->query("DELETE FROM discount_rule_customer_area WHERE id_rule = $id");
    if(!empty($area))
    {
      foreach($area as $code)
      {
        $this->db->query("INSERT INTO discount_rule_customer_area (id_rule, area_code) VALUES ($id, '$code')");
      }
    }

    //--- 6
    $this->db->query("DELETE FROM discount_rule_customer_class WHERE id_rule = $id");
    if(!empty($class))
    {
      foreach($class as $code)
      {
        $this->db->query("INSERT INTO discount_rule_customer_class (id_rule, class_code) VALUES ($id, '$code')");
      }
    }


    //--- end transection
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $result->status = FALSE;
      $result->message = 'กำหนดเงื่อนไขคุณลักษณะลูกค้าไม่สำเร็จ';
    }

    return $result;
  }





  public function set_all_product($id, $value)
  {
    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'success';

    if($value == 1)
    {
      //--- start transection
      $this->db->trans_start();

      //--- 1.
      $this->db->query("UPDATE discount_rule SET all_product = 1 WHERE id = $id");

      //--- 2.
      $this->db->query("DELETE FROM discount_rule_product_style WHERE id_rule = $id");

      //--- 3
      $this->db->query("DELETE FROM discount_rule_product_group WHERE id_rule = $id");

      //--- 4
      $this->db->query("DELETE FROM discount_rule_product_sub_group WHERE id_rule = $id");

      //--- 5
      $this->db->query("DELETE FROM discount_rule_product_category WHERE id_rule = $id");

      //--- 6
      $this->db->query("DELETE FROM discount_rule_product_type WHERE id_rule = $id");

      //--- 7
      $this->db->query("DELETE FROM discount_rule_product_kind WHERE id_rule = $id");

      //--- 8
      $this->db->query("DELETE FROM discount_rule_product_brand WHERE id_rule = $id");

      //--- 9
      $this->db->query("DELETE FROM discount_rule_product_year WHERE id_rule = $id");

      //--- end transection
      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $result->status = FALSE;
        $result->message = "บันทึกเงือนไขสินค้าไม่สำเร็จ";
      }

    }
    else
    {
      //--- 1.
      $this->db->query("UPDATE discount_rule SET all_product = 0 WHERE id = $id");
    }

    return $result;
  }




  public function set_product_item($id, $items)
  {
    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'success';

    //---- start transection
    $this->db->trans_start();

    //--- 1.
    $this->db->query("UPDATE discount_rule SET all_product = 0 WHERE id = $id");

    //--- 2 ลบข้อมูลเก่าก่อน
    $this->db->query("DELETE FROM discount_rule_product WHERE id_rule = $id");

    if(!empty($items))
    {
      foreach($items as $code)
      {
        if(!empty($code))
        {
          $this->db->query("INSERT INTO discount_rule_product (id_rule, product_code) VALUES ($id, '{$code}')");
        }
      }
    }

    //--- 3
    $this->db->query("DELETE FROM discount_rule_product_style WHERE id_rule = $id");

    //--- 3
    $this->db->query("DELETE FROM discount_rule_product_group WHERE id_rule = $id");

    //--- 4
    $this->db->query("DELETE FROM discount_rule_product_sub_group WHERE id_rule = $id");

    //--- 5
    $this->db->query("DELETE FROM discount_rule_product_category WHERE id_rule = $id");

    //--- 6
    $this->db->query("DELETE FROM discount_rule_product_type WHERE id_rule = $id");

    //--- 7
    $this->db->query("DELETE FROM discount_rule_product_kind WHERE id_rule = $id");

    //--- 8
    $this->db->query("DELETE FROM discount_rule_product_brand WHERE id_rule = $id");

    //--- 9
    $this->db->query("DELETE FROM discount_rule_product_year WHERE id_rule = $id");

    //--- end transection
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $result->status = FALSE;
      $result->message = 'กำหนดเงื่อนไขรุ่นสินค้าไม่สำเร็จ';
    }

    return $result;
  }




  public function set_product_style($id, $style)
  {
    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'success';

    //---- start transection
    $this->db->trans_start();

    //--- 1.
    $this->db->query("UPDATE discount_rule SET all_product = 0 WHERE id = $id");

    //--- 2 ลบข้อมูลเก่าก่อน
    $this->db->query("DELETE FROM discount_rule_product_style WHERE id_rule = $id");

    if(!empty($style))
    {
      foreach($style as $code)
      {
        $this->db->query("INSERT INTO discount_rule_product_style (id_rule, style_code) VALUES ($id, '$code')");
      }
    }

    //--- 3
    $this->db->query("DELETE FROM discount_rule_product WHERE id_rule = $id");

    //--- 3
    $this->db->query("DELETE FROM discount_rule_product_group WHERE id_rule = $id");

    //--- 4
    $this->db->query("DELETE FROM discount_rule_product_sub_group WHERE id_rule = $id");

    //--- 5
    $this->db->query("DELETE FROM discount_rule_product_category WHERE id_rule = $id");

    //--- 6
    $this->db->query("DELETE FROM discount_rule_product_type WHERE id_rule = $id");

    //--- 7
    $this->db->query("DELETE FROM discount_rule_product_kind WHERE id_rule = $id");

    //--- 8
    $this->db->query("DELETE FROM discount_rule_product_brand WHERE id_rule = $id");

    //--- 9
    $this->db->query("DELETE FROM discount_rule_product_year WHERE id_rule = $id");

    //--- end transection
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $result->status = FALSE;
      $result->message = 'กำหนดเงื่อนไขรุ่นสินค้าไม่สำเร็จ';
    }

    return $result;
  }



  public function set_product_attr($id, $group, $sub_group, $category, $type, $kind, $brand, $year)
  {
    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'success';

    $this->db->trans_start();

    //--- ลบเงื่อนไขสินค้า
    $this->db->where('id_rule', $id)->delete('discount_rule_product');

    //--- ลบเงื่อนไขรุ่นสินค้า
    $this->db->where('id_rule', $id)->delete('discount_rule_product_style');

    //--- กลุ่มสินค้า
    $this->db->where('id_rule', $id)->delete('discount_rule_product_group');
    if(!empty($group))
    {
      foreach($group as $code)
      {
        $this->db->insert('discount_rule_product_group', array('id_rule' => $id, 'group_code' => $code));
      }
    }

    //--- กลุ่มย่อยสินค้า
    $this->db->where('id_rule', $id)->delete('discount_rule_product_sub_group');
    if(!empty($sub_group))
    {
      foreach($sub_group as $code)
      {
        $this->db->insert('discount_rule_product_sub_group', array('id_rule' => $id, 'sub_group_code' => $code));
      }
    }

    //--- หมวดหมู่สินค้า
    $this->db->where('id_rule', $id)->delete('discount_rule_product_category');
    if(!empty($category))
    {
      foreach($category as $code)
      {
        $this->db->insert('discount_rule_product_category', array('id_rule' => $id, 'category_code' => $code));
      }
    }

    //--- ประเภทสินค้า
    $this->db->where('id_rule', $id)->delete('discount_rule_product_type');
    if(!empty($type))
    {
      foreach($type as $code)
      {
        $this->db->insert('discount_rule_product_type', array('id_rule' => $id, 'type_code' => $code));
      }
    }

    //--- ชนิดสินค้า
    $this->db->where('id_rule', $id)->delete('discount_rule_product_kind');
    if(!empty($kind))
    {
      foreach($kind as $code)
      {
        $this->db->insert('discount_rule_product_kind', array('id_rule' => $id, 'kind_code' => $code));
      }
    }

    //--- ยี่ห้อ
    $this->db->where('id_rule', $id)->delete('discount_rule_product_brand');
    if(!empty($brand))
    {
      foreach($brand as $code)
      {
        $this->db->insert('discount_rule_product_brand', array('id_rule' => $id, 'brand_code' => $code));
      }
    }

    //--- ปีสินค้า
    $this->db->where('id_rule', $id)->delete('discount_rule_product_year');
    if(!empty($year))
    {
      foreach($year as $code)
      {
        $this->db->insert('discount_rule_product_year', array('id_rule' => $id, 'year' => $code));
      }
    }

    //--- end transection
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $result->status = FALSE;
      $result->message = 'กำหนดเงื่อนไขคุณลักษณะสินค้าไม่สำเร็จ';
    }

    return $result;

  }



  public function set_all_channels($id)
  {
    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'success';

    $this->db->trans_start();

    //--- ลบเงื่อนไขช่องทางขายทั้งหมดก่อน
    $this->db->where('id_rule', $id)->delete('discount_rule_channels');

    //--- update เงือนไข
    $this->db->set('all_channels', 1)->where('id', $id)->update('discount_rule');

    //--- end transection
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $result->status = FALSE;
      $result->message = 'กำหนดเงื่อนไขช่องทางขายไม่สำเร็จ';
    }

    return $result;
  }


  public function set_channels($id, $channels)
  {
    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'success';

    $this->db->trans_start();

    //--- update เงือนไข
    $this->db->set('all_channels', 0)->where('id', $id)->update('discount_rule');

    //--- ลบเงื่อนไขช่องทางขายทั้งหมดก่อน
    $this->db->where('id_rule', $id)->delete('discount_rule_channels');

    //--- insert ใหม่
    if(!empty($channels))
    {
      foreach($channels as $code)
      {
        $this->db->insert('discount_rule_channels', array('id_rule' => $id, 'channels_code' => $code));
      }
    }

    //--- end transection
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $result->status = FALSE;
      $result->message = 'กำหนดเงื่อนไขช่องทางขายไม่สำเร็จ';
    }

    return $result;
  }



  public function set_all_payment($id)
  {
    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'success';

    $this->db->trans_start();

    //--- ลบเงื่อนไขทั้งหมดก่อน
    $this->db->where('id_rule', $id)->delete('discount_rule_payment');

    //--- update เงือนไข
    $this->db->set('all_payment', 1)->where('id', $id)->update('discount_rule');

    //--- end transection
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $result->status = FALSE;
      $result->message = 'กำหนดเงื่อนไขช่องการชำระเงินไม่สำเร็จ';
    }

    return $result;
  }




  public function set_payment($id, $payment)
  {
    $result = new stdClass();
    $result->status = TRUE;
    $result->message = 'success';

    $this->db->trans_start();

    //--- update เงือนไข
    $this->db->set('all_payment', 0)->where('id', $id)->update('discount_rule');

    //--- ลบเงื่อนไขช่องทางขายทั้งหมดก่อน
    $this->db->where('id_rule', $id)->delete('discount_rule_payment');

    //--- insert ใหม่
    if(!empty($payment))
    {
      foreach($payment as $code)
      {
        $this->db->insert('discount_rule_payment', array('id_rule' => $id, 'payment_code' => $code));
      }
    }

    //--- end transection
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      $result->status = FALSE;
      $result->message = 'กำหนดเงื่อนไขช่องทางการชำระเงินไม่สำเร็จ';
    }

    return $result;
  }

  /*
  |----------------------------------
  | END ใช้สำหรับหน้ากำหนดเงื่อนไข
  |----------------------------------
  */


  public function update_policy($id_rule, $id_policy)
  {
    return $this->db->set('id_policy', $id_policy)->where('id', $id_rule)->update('discount_rule');
  }



  public function count_rows($code, $name, $active, $policy, $discount)
  {
    $qr = "SELECT id FROM discount_rule WHERE isDeleted = 0 ";

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

    if($policy != "")
    {
      $policies = discount_policy_in($policy);
      $qr .= "AND id_policy IN(".$policies.") ";
    }

    if($discount != "")
    {
      $qr .= "AND item_disc = ".$discount." ";
    }

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->num_rows();
    }

    return 0;
  }



  public function get_data($code, $name, $active, $policy, $discount, $perpage = '', $offset = '')
  {
    $qr = "SELECT * FROM discount_rule WHERE isDeleted = 0 ";

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

    if($policy != "")
    {
      $policies = discount_policy_in($policy);
      $qr .= "AND id_policy IN(".$policies.") ";
    }

    if($discount != "")
    {
      $qr .= "AND item_disc = ".$discount." ";
    }

    $qr .= "ORDER BY code DESC";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }





  public function get_policy_rules($id_policy)
  {
    $rs = $this->db->where('id_policy', $id_policy)->get('discount_rule');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }




  public function get_active_rule()
  {
    $rs = $this->db->where('active', 1)->where('id_policy IS NULL')->get('discount_rule');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }



  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM discount_rule WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }



  public function search($txt)
  {
    $rs = $this->db->select('id')
    ->like('code', $txt)
    ->like('name', $txt)
    ->get('discount_rule');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


  public function delete_rule($id)
  {
    //--- start transection
    $this->db->trans_start();

    //--- 1.
    $this->db->where('id_rule', $id)->delete('discount_rule_product_style');

    //--- 2.
    $this->db->where('id_rule', $id)->delete('discount_rule_product_group');

    //--- 3
    $this->db->where('id_rule', $id)->delete('discount_rule_product_sub_group');

    //--- 4
    $this->db->where('id_rule', $id)->delete('discount_rule_product_category');

    //--- 5
    $this->db->where('id_rule', $id)->delete('discount_rule_product_type');

    //--- 6
    $this->db->where('id_rule', $id)->delete('discount_rule_product_kind');

    //--- 7
    $this->db->where('id_rule', $id)->delete('discount_rule_product_brand');

    //--- 8
    $this->db->where('id_rule', $id)->delete('discount_rule_product_year');

    //--- 9
    $this->db->where('id', $id)->delete('discount_rule');

    //--- end transection
    $this->db->trans_complete();

    return $this->db->trans_status();
  }

} //--- end class

 ?>
