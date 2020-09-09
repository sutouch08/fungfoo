<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Discount_rule extends PS_Controller
{
  public $menu_code = 'SCRULE';
	public $menu_group_code = 'SC';
	public $title = 'เพิ่ม/แก้ไข เงือนไขส่วนลด';
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'discount/discount_rule';
    $this->load->model('discount/discount_policy_model');
    $this->load->model('discount/discount_rule_model');
  }


  public function index()
  {
    $this->load->helper('discount_policy');
    $this->load->helper('discount_rule');

		$code = get_filter('rule_code', 'rule_code', '');
    $name = get_filter('rule_name', 'rule_name', '');
    $active = get_filter('active', 'active', 2); //-- 0 = not active , 1 = active , 2 = all
    $policy = get_filter('policy', 'policy', ''); //-- รหัส หรือ ชื่อนโยบาย
    $discount = get_filter('rule_disc', 'rule_disc', '');
		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->discount_rule_model->count_rows($code, $name, $active, $policy, $discount);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->discount_rule_model->get_data($code, $name, $active, $policy, $discount, $perpage, $this->uri->segment($segment));

    $ds = array(
      'code' => $code,
      'name' => $name,
      'active' => $active,
      'policy' => $policy,
      'discount' => $discount,
			'rules' => $result
    );

		$this->pagination->initialize($init);

    $this->load->view('discount/rule/rule_view', $ds);
  }



  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('discount/rule/rule_add_view');
    }
    else
    {
      redirect($this->home);
    }
  }



  public function add()
  {
    if($this->pm->can_add)
    {
      if($this->input->post('name'))
      {
        $code = $this->get_new_code();
        $name = $this->input->post('name');

        $arr = array(
          'code' => $code,
          'name' => $name,
          'user' => get_cookie('uname')
        );

        $id = $this->discount_rule_model->add($arr);
        if($id !== FALSE)
        {
          redirect($this->home.'/edit_rule/'.$id);
        }
        else
        {
          set_error('สร้างเงื่อนไขส่วนลดไม่สำเร็จ');
          redirect($this->home.'/add_new');
        }
      }
      else
      {
        set_error('ไม่พบชื่อเงื่อนไข กรุณาตรวจสอบแล้วลองใหม่อีกครั้ง');
        redirect($this->home.'/add_new');
      }
    }
    else
    {
      set_error('คุณไม่มีสิทธิ์ในการสร้างเงื่อนไขส่วนลด');
      redirect($this->home);
    }
  }



  public function edit_rule($id_rule)
  {
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/products_model');
    $data['rule'] = $this->discount_rule_model->get($id_rule);
    $this->load->view('discount/rule/rule_edit_view', $data);
  }



  public function update_rule($id)
  {
    $arr = array(
      'name' => $this->input->post('name'),
      'active' => $this->input->post('active')
    );

    $rs = $this->discount_rule_model->update($id, $arr);

    echo $rs === TRUE ? 'success' : 'แก้ไขรายการไม่สำเร็จ';
  }




  //---- set discount on discount tab
  public function set_discount()
  {
    $sc = TRUE;

    $id_rule  = $this->input->post('id_rule');
    $setPrice = trim($this->input->post('set_price'));
    $price    = $this->input->post('price');
    $disc     = trim($this->input->post('disc'));
    $unit     = $this->input->post('disc_unit');
    $disc2    = trim($this->input->post('disc2'));
    $unit2    = $this->input->post('disc_unit2');
    $disc3    = trim($this->input->post('disc3'));
    $unit3    = $this->input->post('disc_unit3');
    $minQty   = $this->input->post('min_qty');
    $minAmount = $this->input->post('min_amount');
    $canGroup = trim($this->input->post('can_group'));

    $minQty = $minQty > 0 ? $minQty : 0; //-- ต้องไม่ติดลบ
    $minAmount = $minAmount > 0 ? $minAmount : 0; //--- ต้องไม่ติดลบ
    $canGroup = $canGroup == 'Y' ? 1 : 0;
    $discUnit = $unit == 'P' ? 'percent' : ($unit == 'A' ? 'amount' : 'percent');
    $discUnit2 = $unit2 == 'P' ? 'percent' : ($unit2 == 'A' ? 'amount' : 'percent');
    $discUnit3 = $unit3 == 'P' ? 'percent' : ($unit3 == 'A' ? 'amount' : 'percent');

    if($setPrice == 'Y')
    {
      $disc = 0;
      $price = $price > 0 ? $price : 0;
    }


    if($setPrice == 'N')
    {
      $price = 0;
      $disc = $disc >= 0 ? $disc : 0;
    }

    //--- ถ้าไม่ได้กำหนดราคาขาย และส่วนลดเป็น % ส่วนลดต้องไม่เกิน 100%
    if($setPrice == 'N' && $unit == 'P' && $disc > 100)
    {
      $sc = FALSE;
      $message = 'ส่วนลดต้องไม่เกิน 100%';
    }

    if($setPrice == 'N' && $disc == 0 && $disc2 > 0)
    {
      $sc = FALSE;
      $message = 'ต้องกำหนดส่วนลด step 1 ก่อนระบุส่วนลด step 2';
    }

    if($disc2 == 0 && $disc3 > 0)
    {
      $sc = FALSE;
      $message = 'ต้องกำหนดส่วนลด step 2 ก่อนระบุส่วนลด step 3';
    }


    //---- ถ้าไม่มีอะไรผิดพลาด
    if($sc === TRUE)
    {
      $arr = array(
        'qty' => $minQty,
        'amount' => $minAmount,
        'canGroup' => $canGroup,
        'item_price' => $price,
        'item_disc' => $disc,
        'item_disc_unit' => $discUnit,
        'item_disc_2' => $disc2,
        'item_disc_2_unit' => $discUnit2,
        'item_disc_3' => $disc3,
        'item_disc_3_unit' => $discUnit3,
        'update_user' => get_cookie('uname')
      );

      if($this->discount_rule_model->update($id_rule, $arr) !== TRUE)
      {
        $sc = FALSE;
        $message = 'บันทีกเงื่อนไขส่วนลดไม่สำเร็จ';
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }





  //---- set rule in customer tab
  public function set_customer_rule()
  {
    if($this->input->post('id_rule'))
    {
      $id_rule = $this->input->post('id_rule');

      //--- all customer ?
      $all = $this->input->post('all_customer') == 'Y' ? TRUE : FALSE;

      //--- customer name ?
      $custId = $this->input->post('customer_id') == 'Y' ? TRUE : FALSE;

      //--- customer group ?
      $group = $this->input->post('customer_group') == 'Y' ? TRUE : FALSE;

      //--- customer type ?
      $type = $this->input->post('customer_type') == 'Y' ? TRUE : FALSE;

      //--- customer kind ?
      $kind = $this->input->post('customer_kind') == 'Y' ? TRUE : FALSE;

      //--- customer area ?
      $area = $this->input->post('customer_area') == 'Y' ? TRUE : FALSE;

      //--- customer class ?
      $class = $this->input->post('customer_class') == 'Y' ? TRUE : FALSE;

      if($all === TRUE)
      {
        $rs = $this->discount_rule_model->set_all_customer($id_rule, 1);
        echo $rs->status === TRUE ? 'success' : $rs->message;
        exit();
      }

      if($all === FALSE)
      {
        //--- เปลี่ยนเงื่อนไข set all_customer = 0
        $this->discount_rule_model->set_all_customer($id_rule, 0);

        //--- กรณีระบุชื่อลูกค้า
        if($custId === TRUE)
        {
          $cusList = $this->input->post('custId');
          $rs = $this->discount_rule_model->set_customer_list($id_rule, $cusList);
          echo $rs->status === TRUE ? 'success' : $rs->message;
          exit();
        }

        //--- กรณีไม่ระบุชื่อลูกค้า
        if($custId === FALSE)
        {
          $group = $this->input->post('customerGroup');
          $type  = $this->input->post('customerType');
          $kind  = $this->input->post('customerKind');
          $area  = $this->input->post('customerArea');
          $class = $this->input->post('customerClass');

          $rs = $this->discount_rule_model->set_customer_attr($id_rule, $group, $type, $kind, $area, $class);
          echo $rs->status === TRUE ? 'success' : $rs->message;
        } //--- end if custId == false
      } //--- end if $all === false
    }
  }



  public function set_product_rule()
  {
    $id_rule = $this->input->post('id_rule');

    //--- all product ?
    $all = $this->input->post('all_product') == 'Y' ? TRUE : FALSE;

    //--- item code
    $item = $this->input->post('product_item') == 'Y' ? TRUE : FALSE;

    //--- product model ?
    $style = $this->input->post('product_style') == 'Y' ? TRUE : FALSE;

    //--- product group ?
    $group = $this->input->post('product_group') == 'Y' ? TRUE : FALSE;

    //--- product sub group ?
    $sub = $this->input->post('product_sub_group') == 'Y' ? TRUE : FALSE;

    //--- product category ?
    $category = $this->input->post('product_category') == 'Y' ? TRUE : FALSE;

    //--- product type ?
    $type = $this->input->post('product_type') == 'Y' ? TRUE : FALSE;

    //--- product kind ?
    $kind = $this->input->post('product_kind') == 'Y' ? TRUE : FALSE;

    //--- product brand ?
    $brand = $this->input->post('product_brand') == 'Y' ? TRUE : FALSE;

    //--- product year ?
    $year = $this->input->post('product_year') == 'Y' ? TRUE : FALSE;

    if($all === TRUE)
    {
      $rs = $this->discount_rule_model->set_all_product($id_rule, 1);
      echo $rs->status === TRUE ? 'success' : $rs->message;
      exit();
    }

    if($all === FALSE)
    {
      //--- เปลี่ยนเงื่อนไข set all_product = 0
      $this->discount_rule_model->set_all_product($id_rule, 0);

      //--- กรณีระบุรหัสสินค้า
      if($item === TRUE)
      {
        $itemList = $this->input->post('itemId');
        $rs = $this->discount_rule_model->set_product_item($id_rule, $itemList);
        echo $rs->status === TRUE ? 'success' : $rs->message;
        exit;
      }


      //--- กรณีระบุรุ่นสินค้า
      if($style === TRUE)
      {
        $styleList = $this->input->post('styleId');
        $rs = $this->discount_rule_model->set_product_style($id_rule, $styleList);
        echo $rs->status === TRUE ? 'success' : $rs->message;
        exit;
      }

      //--- กรณีไม่ระบุชื่อสินค้า
      if($style === FALSE)
      {
        $group = $this->input->post('productGroup');
        $sub_group = $this->input->post('productSubGroup');
        $category  = $this->input->post('productCategory');
        $type  = $this->input->post('productType');
        $kind  = $this->input->post('productKind');
        $brand = $this->input->post('productBrand');
        $year  = $this->input->post('productYear');

        $rs = $this->discount_rule_model->set_product_attr($id_rule, $group, $sub_group, $category, $type, $kind, $brand, $year);
        echo $rs->status === TRUE ? 'success' : $rs->message;
        exit();
      } //--- end if styleId == false
    } //--- end if $all === false
  }



  public function set_channels_rule()
  {
    $id_rule = $this->input->post('id_rule');

    //--- all channels ?
    $all = $this->input->post('all_channels') == 'Y' ? TRUE : FALSE;

    if($all === TRUE)
    {
      $rs = $this->discount_rule_model->set_all_channels($id_rule);
      echo $rs->status === TRUE ? 'success' : $rs->message;
      exit();
    }

    if($all === FALSE)
    {
      $channels = $this->input->post('channels');
      $rs = $this->discount_rule_model->set_channels($id_rule, $channels);
      echo $rs->status === TRUE ? 'success' : $rs->message;
      exit();
    } //--- end if $all === false

  }




  public function set_payment_rule()
  {
    $id_rule = $this->input->post('id_rule');

    //--- all channels ?
    $all = $this->input->post('all_payment') == 'Y' ? TRUE : FALSE;

    if($all === TRUE)
    {
      $rs = $this->discount_rule_model->set_all_payment($id_rule);
      echo $rs->status === TRUE ? 'success' : $rs->message;
      exit();
    }

    if($all === FALSE)
    {
      $payment = $this->input->post('payment');
      $rs = $this->discount_rule_model->set_payment($id_rule, $payment);
      echo $rs->status === TRUE ? 'success' : $rs->message;
      exit();
    } //--- end if $all === false

  }




  public function add_policy_rule()
  {
    $sc = TRUE;

    $id_policy = $this->input->post('id_policy');
  	$rule = $this->input->post('rule');

  	if(!empty($rule))
  	{
  		foreach($rule as $id_rule)
  		{
  			if($this->discount_rule_model->update_policy($id_rule, $id_policy) === FALSE)
  			{
  				$sc = FALSE;
  				$message = 'เพิ่มกฏไม่สำเร็จ';
  			}
  		}	//--- end foreach
  	}	//--- end if empty

  	echo $sc === TRUE ? 'success' : $message;
  }



  public function unlink_rule()
  {
    $sc = TRUE;
    $id_rule = $this->input->post('id_rule');
    if($this->discount_rule_model->update_policy($id_rule, NULL) === FALSE)
    {
      $sc = FALSE;
      $message = 'ลบกฏไม่สำเร็จ';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function delete_rule()
  {
    $sc = TRUE;
    //--- check before delete
    $id = $this->input->post('id_rule');
    $rule = $this->discount_rule_model->get($id);
    if(!empty($rule))
    {
      if(!empty($rule->id_policy))
      {
        $policy_code = $this->discount_policy_model->get_code($rule->id_policy);
        $sc = FALSE;
        $this->error = "มีการเชื่อมโยงเงื่อนไขไว้กับนโยบายเลขที่ : {$policy_code} กรุณาลบการเชื่อมโยงก่อนลบเงื่อนไขนี้";
      }
      else
      {
        if(! $this->discount_rule_model->delete_rule($id))
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Not found";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function view_rule_detail($id)
  {
    $this->load->library('printer');
    $rule = $this->discount_rule_model->get($id);
    $policy = $this->discount_policy_model->get($rule->id_policy);
    $ds['id_rule'] = $id;
    $ds['rule'] = $rule;
    $ds['policy'] = $policy;
    $this->load->view('discount/policy/view_rule_detail', $ds);
  }

  public function get_new_code()
  {
    $date = date('Y-m-d');
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RULE');
    $run_digit = getConfig('RUN_DIGIT_RULE');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->discount_rule_model->get_max_code($pre);
    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }



  //--- Po
  public function get_product_grid()
  {
    $rs = TRUE;
    $style_code = $this->input->get('style_code');
    if(!empty($style_code))
    {
      $this->load->model('masters/products_model');
      if($this->products_model->is_exists_style($style_code))
      {
        $sc = $this->getProductGrid($style_code);
      	$tableWidth	= $this->products_model->countAttribute($style_code) == 1 ? 600 : $this->getTableWidth($style_code);
      	$sc .= ' | '.$tableWidth;
      	$sc .= ' | ' . $style_code;
      	$sc .= ' | ' . $style_code;
      }
      else
      {
        $rs = FALSE;
        $this->error = "รหัสไม่ถูกต้อง";
      }
    }
    else
    {
      $rs = FALSE;
      $this->error = "ไม่พบรหัสสินค้า";
    }


  	echo $sc;
  }



  public function getProductGrid($style_code)
	{
    $this->load->model('masters/product_style_model');
		$sc = '';
    $style = $this->product_style_model->get($style_code);
		$attrs = $this->getAttribute($style->code);

		if( count($attrs) == 1  )
		{
			$sc .= $this->productGridOneAttribute($style, $attrs[0]);
		}
		else if( count( $attrs ) == 2 )
		{
			$sc .= $this->productGridTwoAttribute($style);
		}
		return $sc;
	}



  public function productGridOneAttribute($style, $attr)
	{
    $this->load->model('masters/products_model');
		$sc 		= '';
		$data 	= $attr == 'color' ? $this->getAllColors($style->code) : $this->getAllSizes($style->code);
		$items	= $this->products_model->get_style_items($style->code);
		$sc 	 .= "<table class='table table-bordered'>";
		$i 		  = 0;

    foreach($items as $item )
    {
      $id_attr	= $item->size_code === NULL OR $item->size_code === '' ? $item->color_code : $item->size_code;
      $sc 	.= $i%2 == 0 ? '<tr>' : '';

      $code = $attr == 'color' ? $item->color_code : $item->size_code;

			$sc 	.= '<td class="middle text-center width-25" style="border-right:0px;">';
			$sc 	.= '<strong>' .	$code. '</strong>';
			$sc 	.= '</td>';
			$sc 	.= '<td class="middle width-25" class="one-attribute">';
      $sc   .= '<label>';
      $sc   .= '<input type="checkbox" class="ace check-item" value="'.$item->code.'"/>';
      $sc   .= '<span class="lbl"></span>';
      $sc   .= '</label>';
			$sc 	.= '</td>';

			$i++;

			$sc 	.= $i%2 == 0 ? '</tr>' : '';

    }

		$sc	.= "</table>";

		return $sc;
	}


  public function productGridTwoAttribute($style)
	{
    $this->load->model('masters/products_model');
		$colors	= $this->getAllColors($style->code);
		$sizes 	= $this->getAllSizes($style->code);
		$sc 		= '';
		$sc 		.= '<table class="table table-bordered">';
		$sc 		.= $this->gridHeader($colors);

		foreach( $sizes as $size_code => $size )
		{
			$sc 	.= '<tr style="font-size:12px;">';
			$sc 	.= '<td class="text-center middle" style="width:70px;"><strong>'.$size_code.'</strong></td>';

			foreach( $colors as $color_code => $color )
			{
        $item = $this->products_model->get_item_by_color_and_size($style->code, $color_code, $size_code);

				if( !empty($item) )
				{
					$sc 	.= '<td class="order-grid">';
          $sc   .= '<label>';
          $sc   .= '<input type="checkbox" class="ace check-item check-'.$size_code.'" value="'.$item->code.'"/>';
          $sc   .= '<span class="lbl"></span>';
          $sc   .= '</label>';
    			$sc 	.= '</td>';
				}
				else
				{
					$sc .= '<td class="order-grid">N/A</td>';
				}
			} //--- End foreach $colors

      $sc 	.= '<td class="order-grid">';
      $sc   .= '<label>';
      $sc   .= '<input type="checkbox" class="ace" onChange="toggleSelect($(this),\''.$size_code.'\')"/>';
      $sc   .= '<span class="lbl"></span>';
      $sc   .= '</label>';
      $sc 	.= '</td>';


			$sc .= '</tr>';
		} //--- end foreach $sizes
	$sc .= '</table>';
	return $sc;
	}




  public function getTableWidth($style_code)
  {
    $sc = 800; //--- ชั้นต่ำ
    $tdWidth = 50;  //----- แต่ละช่อง
    $padding = 60; //----- สำหรับช่องแสดงไซส์
    $color = $this->products_model->count_color($style_code);
    if($color > 0)
    {
      $sc = $color * $tdWidth + $padding;
    }

    return $sc;
  }


  public function getAttribute($style_code)
  {
    $this->load->model('masters/products_model');
    $sc = array();
    $color = $this->products_model->count_color($style_code);
    $size  = $this->products_model->count_size($style_code);
    if( $color > 0 )
    {
      $sc[] = "color";
    }

    if( $size > 0 )
    {
      $sc[] = "size";
    }
    return $sc;
  }





  public function gridHeader(array $colors)
  {
    $sc = '<tr class="font-size-12"><td>&nbsp;</td>';
    foreach( $colors as $code => $name )
    {
      $sc .= '<td class="text-center middle"><strong>'.$code .'</strong></td>';
    }

    $sc .= '<td class="text-center middle">ทั้งหมด</td>';
    $sc .= '</tr>';
    return $sc;
  }





  public function getAllColors($style_code)
	{
    $this->load->model('masters/products_model');
		$sc = array();
    $colors = $this->products_model->get_all_colors($style_code);
    if($colors !== FALSE)
    {
      foreach($colors as $color)
      {
        $sc[$color->code] = $color->name;
      }
    }

    return $sc;
	}




  public function getAllSizes($style_code)
	{
    $this->load->model('masters/products_model');
		$sc = array();
		$sizes = $this->products_model->get_all_sizes($style_code);
		if( $sizes !== FALSE )
		{
      foreach($sizes as $size)
      {
        $sc[$size->code] = $size->name;
      }
		}
		return $sc;
	}


  public function clear_filter()
  {
    $filter = array('rule_code', 'rule_name', 'active', 'policy', 'rule_disc');
    clear_filter($filter);
  }
} //--- end class
?>
