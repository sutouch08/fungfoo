<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends PS_Controller{
	public $menu_code = 'DBEMPL'; //--- Add/Edit Employee
	public $menu_group_code = 'DB'; //--- System security
	public $title = 'เพิ่ม/แก้ไข พนักงาน';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'hr/employee';
    $this->load->model('masters/employee_model');
  }


} //--- end class 

?>
