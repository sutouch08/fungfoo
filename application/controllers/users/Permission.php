<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission extends PS_Controller{
	public $menu_code = 'SCPERM'; //--- Add/Edit Profile
	public $menu_group_code = 'SC'; //--- System security
	public $title = 'Permission';
  public $permission = FALSE;

  public function __construct()
  {
    parent::__construct();
    //--- If any right to add, edit, or delete mean granted
    if($this->pm->can_add OR $this->pm->can_edit OR $this->pm->can_delete)
    {
      $this->permission = TRUE;
    }

    $this->home = base_url().'users/permission';
    $this->load->model('users/profile_model');
    $this->load->model('users/permission_model');
  }



  public function index()
  {
		$profileName = get_filter('profileName', 'profileName', '');

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->profile_model->count_rows($profileName);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->profile_model->get_profiles($profileName, $perpage, $this->uri->segment($segment));
    $data = array();

    if(!empty($result))
    {
      foreach($result as $rs)
      {
        $row = new stdClass();
        $row->id = $rs->id;
        $row->name = $rs->name;
        $row->member = $this->profile_model->count_members($rs->id);
        $data[] = $row;
      }
    }

    $ds = array(
      'profileName' => $profileName,
			'data' => $data
    );

		$this->pagination->initialize($init);

    $this->load->view('users/permission_view', $ds);
  }



  public function edit_permission($id)
  {
    $this->load->model('menu');
    $profile = $this->profile_model->get_profile($id);
    $this->title = 'กำหนดสิทธิ์ - '.$profile->name;
    $data['data'] = $profile;
    $data['menus'] = array();
    $groups = $this->menu->get_valid_menu_groups();
    if(!empty($groups))
    {
      foreach($groups as $group)
      {
        $ds = array(
          'group_code' => $group->code,
          'group_name' => $group->name,
          'menu' => ''
        );

        $menus = $this->menu->get_valid_menus_by_group($group->code, TRUE);

        if(!empty($menus))
        {
          $item = array();
          foreach($menus as $menu)
          {
            $arr = array(
              'menu_code' => $menu->code,
              'menu_name' => $menu->name,
              'permission' => $this->permission_model->get_permission($menu->code, $id)
            );
            array_push($item, $arr);
          }

          $ds['menu'] = $item;
        }

        array_push($data['menus'], $ds);
      }
    }

    $this->load->view('users/permission_edit_view', $data);
  }






	public function save_profile_permission()
	{
		if($this->input->post('id_profile'))
		{
			$id_profile = $this->input->post('id_profile');
			$menu = $this->input->post('menu');
			$view = $this->input->post('view');
			$add = $this->input->post('add');
			$edit = $this->input->post('edit');
			$delete = $this->input->post('delete');
			$approve = $this->input->post('approve');

			$this->permission_model->drop_profile_permission($id_profile);

			if(!empty($menu))
			{
				foreach($menu as $code)
				{
					$pm = array(
						'menu' => $code,
						'uid' => NULL,
						'id_profile' => $id_profile,
						'can_view' => isset($view[$code]) ? 1 : 0,
						'can_add' => isset($add[$code]) ? 1 : 0,
						'can_edit' => isset($edit[$code]) ? 1 : 0,
						'can_delete' => isset($delete[$code]) ? 1 : 0,
						'can_approve' => isset($approve[$code]) ? 1 : 0
					);

					$this->permission_model->add($pm);
				}
			}

			set_message('Done!');
			redirect($this->home.'/edit_permission/'.$id_profile);
		}
	}






  public function clear_filter()
  {
    clear_filter('profileName');
    echo 'done';
  }

} //-- end class
  ?>
