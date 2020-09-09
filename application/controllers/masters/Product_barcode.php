<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_barcode extends PS_Controller
{
  public $menu_code = 'DBPROD';

  public function __construct()
  {
    parent::__construct();
    $this->load->model('masters/product_barcode_model');
  }



  public function valid_barcode($barcode, $item)
  {
    $rs = $this->product_barcode_model->is_exists($barcode, $item);

    echo $rs === TRUE ? 'exists' : 'ok';
  }



}//--- end class
  ?>
