<?php
class View_stock extends PS_Controller
{
  public $title = 'เช็คสต็อก';
	public $menu_code = 'SOVIEW';
	public $menu_group_code = 'SO';
	public $pm;
	public function __construct()
	{
		parent::__construct();
    $this->load->model('masters/products_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('stock/stock_model');

    $this->load->helper('order');
    $this->load->helper('warehouse');
    $this->load->helper('product_tab');

    $this->filter = getConfig('STOCK_FILTER');

	}


	public function index()
	{
		$this->load->view('view_stock');
	}



}
 ?>
