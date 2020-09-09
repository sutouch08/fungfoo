<?php
class Stock_balance_zone_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_current_stock_zone(array $ds = array())
  {
    $this->db
    ->select('products.code, products.name, products.cost')
    ->select('zone.code AS zone_code, zone.name AS zone_name, stock.qty')
    ->from('stock')
    ->join('products', 'stock.product_code = products.code', 'left')
    ->join('product_size', 'product_size.code = products.size_code', 'left')
    ->join('zone', 'stock.zone_code = zone.code', 'left');

    if(empty($ds['allZone']) && !empty($ds['zoneCode']))
    {
      $this->db->where('stock.zone_code', $ds['zoneCode']);
    }
    else
    {
      //--- if specify warehouse
      if(empty($ds['allWhouse']))
      {
        $this->db->where_in('zone.warehouse_code', $ds['warehouse']);
      }
    }


    //--- if specify product
    if(empty($ds['allProduct']))
    {
      $this->db->where('products.style_code >=', $ds['pdFrom']);
      $this->db->where('products.style_code <=', $ds['pdTo']);
    }

    $this->db->group_by('stock.product_code');
    $this->db->order_by('products.style_code', 'ASC');
    $this->db->order_by('products.color_code', 'ASC');
    $this->db->order_by('product_size.position', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_stock_zone_prev_date($date, array $ds = array())
  {
    $date = to_date($date);

    $qr  = "SELECT z.code AS zone_code, z.name AS zone_name, ";
    $qr .= "pd.code, pd.name, pd.cost, (SUM(s.move_in) - SUM(s.move_out)) AS qty ";
    $qr .= "FROM stock_movement AS s ";
    $qr .= "JOIN products AS pd ON s.product_code = pd.code ";
    $qr .= "JOIN product_size AS ps ON pd.size_code = ps.code ";
    $qr .= "JOIN zone AS z ON s.zone_code = z.code ";

    $qr .= "WHERE s.date_add <= '{$date}' ";

    if($ds['allProduct'] == 0)
    {
      $qr .= "AND pd.style_code >= '{$ds['pdFrom']}' ";
      $qr .= "AND pd.style_code <= '{$ds['pdTo']}' ";
    }

    //---- ถ้าระบุโซน
    if(empty($ds['allZone']) && !empty($ds['zoneCode']))
    {
      $qr .= "AND s.zone_code = '{$ds['zoneCode']}' ";
    }
    else
    {
      if($ds['allWhouse'] == 0)
      {
        $wh_list = "";
        $i = 1;
        foreach($ds['warehouse'] as $wh)
        {
          $wh_list .= $i === 1 ? "'{$wh}'" : ", '{$wh}'";
          $i++;
        }

        $qr .= "AND z.warehouse_code IN({$wh_list}) ";
      }
    }


    $qr .= "GROUP BY pd.code ";
    $qr .= "ORDER BY pd.style_code ASC, ";
    $qr .= "pd.color_code ASC, ";
    $qr .= "ps.position ASC";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }
}

 ?>
