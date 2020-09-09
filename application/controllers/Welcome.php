<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends PS_Controller
{
	public $title = 'Welcome';
	public $menu_code = '';
	public $menu_group_code = '';
	public $pm;
	public function __construct()
	{
		parent::__construct();
		_check_login();
		$this->pm = new stdClass();
		$this->pm->can_view = 1;
	}


	public function index()
	{
		$this->load->view('welcome_message');
	}
}
