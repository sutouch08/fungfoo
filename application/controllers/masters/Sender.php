<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sender extends PS_Controller{
	public $menu_code = 'DBSEND'; //--- Add/Edit Users
	public $menu_group_code = 'DB'; //--- System security
	public $menu_sub_group_code = 'TRANSPORT';
	public $title = 'เพิ่ม/แก้ไข ขนส่ง';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/sender';
		$this->load->model('masters/sender_model');
  }



  public function index()
  {
		$filter = array(
			'name' => get_filter('name', 'name', ''),
			'addr' => get_filter('addr', 'addr', ''),
			'phone' => get_filter('phone', 'phone', ''),
			'type' => get_filter('type', 'type', 'all'),
			'in_list' => get_filter('in_list', 'in_list', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->sender_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->sender_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('masters/sender/sender_view', $filter);
  }





	public function add_new()
	{
		$this->load->view('masters/sender/sender_add');
	}


	public function add()
	{
		if($this->input->post('name'))
		{
			$name = addslashes(trim($this->input->post('name')));
			$addr1 = addslashes(trim($this->input->post('address1')));
			$addr2 = addslashes(trim($this->input->post('address2')));
			$phone = trim($this->input->post('phone'));
			$open = $this->input->post('open');
			$close = $this->input->post('close');
			$type = $this->input->post('type');
			$inlist = $this->input->post('in_list') ? 1 : 0;

			$arr = array(
				'name' => $name,
				'address1' => $addr1,
				'address2' => $addr2,
				'phone' => $phone,
				'open' => $open,
				'close' => $close,
				'type' => $type,
				'show_in_list' => $inlist
			);

			if($this->sender_model->add($arr))
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
			set_error('ไม่พบข้อมูลในฟอร์ม');
		}

		redirect($this->home.'/add_new');
	}



	public function edit($id)
	{
		$rs = $this->sender_model->get($id);
		$this->load->view('masters/sender/sender_edit', $rs);
	}


	public function update($id)
	{
		if($this->input->post('name'))
		{
			$name = addslashes(trim($this->input->post('name')));

			if(! $this->sender_model->is_exists($name, $id))
			{
				$addr1 = addslashes(trim($this->input->post('address1')));
				$addr2 = addslashes(trim($this->input->post('address2')));
				$phone = trim($this->input->post('phone'));
				$open = $this->input->post('open');
				$close = $this->input->post('close');
				$type = $this->input->post('type');
				$inlist = $this->input->post('in_list') ? 1 : 0;

				$arr = array(
					'name' => $name,
					'address1' => $addr1,
					'address2' => $addr2,
					'phone' => $phone,
					'open' => $open,
					'close' => $close,
					'type' => $type,
					'show_in_list' => $inlist
				);

				if($this->sender_model->update($id, $arr))
				{
					set_message('ปรับปรุงข้อมูลเรียบร้อยแล้ว');
				}
				else
				{
					set_error('ปรับปรุงข้อมูลไม่สำเร็จ');
				}
			}
			else
			{
				set_error("ชื่อ {$name} มีอยู่แล้ว กรุณาใช้ชื่ออื่น");
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
			if($this->sender_model->delete($id))
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
		$filter = array('name', 'addr', 'phone', 'type');
		clear_filter($filter);
	}

}//--- end class


 ?>
