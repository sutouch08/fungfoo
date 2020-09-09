<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transport extends PS_Controller{
	public $menu_code = 'DBTRSP'; //--- Add/Edit Users
	public $menu_group_code = 'DB';
	public $menu_sub_group_code = 'TRANSPORT'; 
	public $title = 'เชื่อมโยงขนส่ง';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/transport';
		$this->load->model('masters/transport_model');
		$this->load->model('masters/sender_model');
		$this->load->model('masters/customers_model');
		$this->load->helper('customer_helper');
		$this->load->helper('sender_helper');
  }



  public function index()
  {
		$filter = array(
			'name' => get_filter('name', 'name', ''),
			'sender' => get_filter('sender', 'sender', '')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->transport_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$list = $this->transport_model->get_list($filter, $perpage, $this->uri->segment($segment));

		if(!empty($list))
		{
			foreach($list as $rs )
			{
				$rs->main_sender = $this->sender_model->get_name($rs->main_sender);
				$rs->second_sender = $this->sender_model->get_name($rs->second_sender);
				$rs->third_sender = $this->sender_model->get_name($rs->third_sender);
			}
		}


		$filter['data'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/transport/transport_view', $filter);
  }





	public function add_new()
	{
		$this->load->view('masters/transport/transport_add');
	}


	public function add()
	{
		if($this->input->post('customer_code'))
		{
			$name = $this->input->post('customer_name');
			$code = $this->input->post('customer_code');
			$main = $this->input->post('main_sender_id');
			$second = $this->input->post('second_sender_id');
			$third = $this->input->post('third_sender_id');

			if(! $this->transport_model->is_exists($code))
			{
				$arr = array(
					'customer_code' => $code,
					'main_sender' => $main,
					'second_sender' => $second,
					'third_sender' => $third
				);

				if($this->transport_model->add($arr))
				{
					set_message('เพิ่มรายการเรียบร้อยแล้ว');
				}
				else
				{
					set_error('เพิ่มรายการไม่สำเร็จ');
				}
			}
			else
			{
				set_error("{$name} มีในระบบอยู่แล้ว");
			}
		}
		else
		{
			set_error('ไม่พบข้อมูลในฟอร์ม');
		}

		redirect($this->home.'/add_new');
	}



	public function edit($id)
	{
		$rs = $this->transport_model->get($id);
		if(!empty($rs))
		{
			$rs->customer_name = $this->customers_model->get_name($rs->customer_code);
			$rs->main_sender_name = $this->sender_model->get_name($rs->main_sender);
			$rs->second_sender_name = $this->sender_model->get_name($rs->second_sender);
			$rs->third_sender_name = $this->sender_model->get_name($rs->third_sender);
		}

		$this->load->view('masters/transport/transport_edit', $rs);
	}


	public function update($id)
	{
		if($this->input->post('customer_code'))
		{
			$name = $this->input->post('customer_name');
			$code = $this->input->post('customer_code');
			$main = $this->input->post('main_sender_id');
			$second = $this->input->post('second_sender_id');
			$third = $this->input->post('third_sender_id');

			if(! $this->transport_model->is_exists($code, $id))
			{
				$arr = array(
					'customer_code' => $code,
					'main_sender' => $main,
					'second_sender' => $second,
					'third_sender' => $third
				);

				if($this->transport_model->update($id, $arr))
				{
					set_message('ปรับปรุงรายการเรียบร้อยแล้ว');
				}
				else
				{
					set_error('ปรับปรุงรายการไม่สำเร็จ');
				}
			}
			else
			{
				set_error("{$name} มีในระบบอยู่แล้ว");
			}
		}
		else
		{
			set_error('ไม่พบข้อมูลในฟอร์ม');
		}

		redirect($this->home.'/edit/'.$id);
	}



	public function delete($id)
	{
		if($this->pm->can_delete)
		{
			if($this->transport_model->delete($id))
			{
				set_message('ลบรายการเรียบร้อยแล้ว');
			}
			else
			{
				set_error('ลบรายการไม่สำเร็จ');
			}
		}
		else
		{
			set_error('คุณไม่มีอำนาจในการลบ');
		}

		redirect($this->home);
	}




	public function clear_filter()
	{
		$filter = array('name', 'sender');
		clear_filter($filter);
	}

}//--- end class


 ?>
