<?php
class Import_order extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/products_model');
    $this->load->model('address/address_model');
    $this->load->model('stock/stock_model');

    $this->load->library('excel');
    $this->load->library('api');
  }


  public function index()
  {
    $sc = TRUE;
    $import = 0;
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
  	$path = $this->config->item('upload_path').'orders/';
    $file	= 'uploadFile';
		$config = array(   // initial config for upload class
			"allowed_types" => "xlsx",
			"upload_path" => $this->config->item('upload_path').'orders/',
			"file_name"	=> "import_order",
			"max_size" => 5120,
			"overwrite" => TRUE
			);

			$this->load->library("upload", $config);

			if(! $this->upload->do_upload($file))
      {
				echo $this->upload->display_errors();
			}
      else
      {
        $info = $this->upload->data();
        /// read file
				$excel = PHPExcel_IOFactory::load($info['full_path']);
				//get only the Cell Collection
        $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

        $i = 1;
        $count = count($collection);

        if( $count <= 501 )
        {
          $ds = array();
          foreach($collection as $cs)
          {
            //---- order code from web site
            $key = $cs['I'];

            $str = substr($key, 0, 2);

            if($str == 'LA')
            {
              $key = substr($key, 2);
            }

            $cs['I'] = $key;

            $key = $key.$cs['M'];


            if(isset($ds[$key]))
            {
              $ds[$key]['N'] += $cs['N'];
              $ds[$key]['O'] += $cs['O'];
            }
            else
            {
              $ds[$key] = $cs;
            }
          }


          //--- รหัสเล่มเอกสาร [อ้างอิงจาก SAP]
          //--- ถ้าเป็นฝากขายแบบโอนคลัง ยืมสินค้า เบิกแปรสภาพ เบิกสินค้า (ไม่เปิดใบกำกับ เปิดใบโอนคลังแทน) นอกนั้น เปิด SO
          $bookcode = getConfig('BOOK_CODE_ORDER');

          $role = 'S';

          //--- รหัสลูกค้าเริ่มต้น หากพอว่าไม่มีการระบุรหัสลูกค้าไว้ จะใช้รหัสนี้แทน
          $default_customer = getConfig('DEFAULT_CUSTOMER');

          $prefix = getConfig('PREFIX_SHIPPING_NUMBER');

          foreach($ds as $rs)
          {
            //--- ถ้าพบ Error ให้ออกจากลูปทันที
            if($sc === FALSE)
            {
              break;
            }

            if($i == 1)
            {
              $headCol = array(
                'A' => 'Consignee Name',
                'B' => 'Address Line 1',
                'C' => 'Province',
                'D' => 'District',
                'E' => 'Sub District',
                'F' => 'postcode',
                'G' => 'email',
                'H' => 'tel',
                'I' => 'orderNumber',
                'J' => 'CreateDateTime',
                'K' => 'Payment Method',
                'L' => 'Channels',
                'M' => 'itemId',
                'N' => 'amount',
                'O' => 'price',
                'P' => 'shipping fee',
                'Q' => 'service fee',
                'R' => 'force update',
                'S' => 'Is DHL'
              );

              foreach($headCol as $col => $field)
              {
                if($rs[$col] !== $field)
                {
                  $sc = FALSE;
                  $message = 'Column '.$col.' Should be '.$field;
                  break;
                }
              }

              if($sc === FALSE)
              {
                break;
              }
            }
            else
            {
              //---- order code from web site
              $ref_code = $rs['I'];

              // $str = substr($ref_code, 0, 2);
              //
              // if($str == 'LA')
              // {
              //   $ref_code = substr($ref_code, 2);
              // }

              $shipping_code = '';

              if($rs['S'] == 'Y' OR $rs['S'] == 'y' OR $rs['S'] == '1')
              {
                $shipping_code = $prefix.$ref_code;
              }

              //---- กำหนดช่องทางการขาย
              $channels = $this->channels_model->get($rs['L']);
              //--- หากไม่ระบุช่องทางขายมา หรือ ช่องทางขายไม่ถูกต้องใช้ default
              if(empty($channels))
              {
                $channels = $this->channels_model->get_default();
              }

              //--- กำหนดช่องทางการชำระเงิน
              $payment = $this->payment_methods_model->get($rs['K']);
              if(empty($payment))
              {
                $payment = $this->payment_methods_model->get_default();
              }

              //------ เช็คว่ามีออเดอร์นี้อยู่ในฐานข้อมูลแล้วหรือยัง
              //------ ถ้ามีแล้วจะได้ order_code กลับมา ถ้ายังจะได้ FALSE;
              $order_code  = $this->orders_model->get_order_code_by_reference($ref_code);

              //-- state ของออเดอร์ จะมีการเปลี่ยนแปลงอีกที
              $state = 3;

              //---- ถ้ายังไม่มีออเดอร์ ให้เพิ่มใหม่ หรือ มีออเดอร์แล้ว แต่ต้องการ update
              //---- โดยการใส่ force update มาเป็น 1
              if($order_code === FALSE OR ($order_code !== FALSE && $rs['R'] == 1))
              {
              	//---	ถ้าเป็นออเดอร์ขายหรือสปอนเซอร์ จะมี id_customer
              	$customer_code = empty($channels->customer_code) ? $default_customer : $channels->customer_code;
                $customer = $this->customers_model->get($customer_code);

              	//---	ถ้าเป็นออเดอร์ขาย จะมี id_sale
              	$sale_code = $customer->sale_code;

              	//---	หากเป็นออนไลน์ ลูกค้าออนไลน์ชื่ออะไร
              	$customer_ref = addslashes(trim($rs['A']));

                //---	ช่องทางการชำระเงิน
                $payment_code = $payment->code;

                //---	ช่องทางการขาย
                $channels_code = $channels->code;

              	//---	วันที่เอกสาร
              	$date_add = PHPExcel_Style_NumberFormat::toFormattedString($rs['J'], 'YYYY-MM-DD');
                $date_add = db_date($date_add, TRUE);

                //--- ค่าจัดส่ง
                $shipping_fee = $rs['P'] == '' ? 0.00 : $rs['P'];

                //--- ค่าบริการอื่นๆ
                $service_fee = $rs['Q'] == '' ? 0.00 : $rs['Q'];

              	//--- รันเลขที่เอกสารตามประเภทเอาสาร
              	$code = $order_code === FALSE ? $this->get_new_code($date_add) : $order_code;

                //---- กรณียังไม่มีออเดอร์
                if($order_code === FALSE)
                {
                  //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
                  $ds = array(
                    'code' => $code,
                    'role' => $role,
                    'bookcode' => $bookcode,
                    'reference' => $ref_code,
                    'customer_code' => $customer_code,
                    'customer_ref' => $customer_ref,
                    'channels_code' => $channels_code,
                    'payment_code' => $payment_code,
                    'sale_code' => $sale_code,
                    'state' => $state,
                    'is_paid' => 1,
                    'is_term' => $payment->has_term,
                    'status' => 1,
                    'date_add' => $date_add,
                    'user' => get_cookie('uname')
                  );

                  //--- เพิ่มเอกสาร
                  if($this->orders_model->add($ds) === TRUE)
                  {
                    $arr = array(
                      'order_code' => $code,
                      'state' => 3,
                      'update_user' => get_cookie('uname')
                    );
                    //--- add state event
                    $this->order_state_model->add_state($arr);

                    $id_address = $this->address_model->get_id($customer_ref, trim($rs['B']));
                    if($id_address === FALSE)
                    {
                      $arr = array(
                        'code' => $customer_ref,
                        'name' => $customer_ref,
                        'address' => trim($rs['B']),
                        'sub_district' => trim($rs['E']),
                        'district' => trim($rs['D']),
                        'province' => trim($rs['C']),
                        'postcode' => trim($rs['F']),
                        'phone' => trim($rs['H']),
                        'alias' => 'Home',
                        'is_default' => 1
                      );

                      $this->address_model->add_shipping_address($arr);
                    }
                    $import++;
                  }
                  else
                  {
                    $sc = FALSE;
                    $message = $ref_code.': เพิ่มออเดอร์ไม่สำเร็จ';
                  }
                }
                else
                {
                  $order = $this->orders_model->get($code);
                  if($order->state <= 3)
                  {
                    //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
                    //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
                    $ds = array(
                      'customer_code' => $customer_code,
                      'customer_ref' => $customer_ref,
                      'channels_code' => $channels_code,
                      'payment_code' => $payment_code,
                      'sale_code' => $sale_code,
                      'state' => $state,
                      'is_term' => $payment->has_term,
                      'date_add' => $date_add,
                      'user' => get_cookie('uname')
                    );

                    $this->orders_model->update($order_code, $ds);
                  }

                  $import++;
                }
              }


              //---- เตรียมข้อมูลสำหรับเพิมรายละเอียดออเดอร์
              $item = $this->products_model->get(trim($rs['M']));

              if(empty($item))
              {
                $sc = FALSE;
                $message = 'ไม่พบข้อมูลสินค้าในระบบ : '.$rs['M'];
                break;
              }

              //---- เช็คข้อมูล ว่ามีรายละเอียดนี้อยู่ในออเดอร์แล้วหรือยัง
              //---- ถ้ามีข้อมูลอยู่แล้ว (TRUE)ให้ข้ามการนำเข้ารายการนี้ไป
              if($this->orders_model->is_exists_detail($code, $item->code) === FALSE)
              {
                //--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
                $arr = array(
                  "order_code"	=> $code,
                  "style_code"		=> $item->style_code,
                  "product_code"	=> $item->code,
                  "product_name"	=> $item->name,
                  "cost"  => $item->cost,
                  "price"	=> ($rs['O']/$rs['N']),
                  "qty"		=> $rs['N'],
                  "discount1"	=> 0,
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => 0,
                  "total_amount"	=> $rs['O'],
                  "id_rule"	=> NULL,
                  "is_count" => $item->count_stock
                );

                if( $this->orders_model->add_detail($arr) === FALSE )
                {
                  $sc = FALSE;
                  $message = 'เพิ่มรายละเอียดรายการไม่สำเร็จ : '.$ref_code;
                  break;
                }
                else
                {
                  $this->update_api_stock($item->code);
                }
              }
              else
              {
                //----  ถ้ามี force update และ สถานะออเดอร์ไม่เกิน 3 (รอจัดสินค้า)
                if($rs['R'] == 1 && $state <= 3)
                {
                  $od  = $this->orders_model->get_order_detail($code, $item->code);

                  $arr = array(
                    "style_code"		=> $item->style_code,
                    "product_code"	=> $item->code,
                    "product_name"	=> $item->name,
                    "cost"  => $item->cost,
                    "price"	=> ($rs['O']/$rs['N']),
                    "qty"		=> $rs['N'],
                    "discount1"	=> 0,
                    "discount2" => 0,
                    "discount3" => 0,
                    "discount_amount" => 0,
                    "total_amount"	=> $rs['O'],
                    "id_rule"	=> NULL,
                    "is_count" => $item->count_stock
                  );

                  if($this->orders_model->update_detail($od->id, $arr) === FALSE)
                  {
                    $sc = FALSE;
                    $message = 'เพิ่มรายละเอียดรายการไม่สำเร็จ : '.$ref_code;
                    break;
                  }
                  else
                  {
                    $this->update_api_stock($item->code);
                  }
                } //--- enf force update
              } //--- end if exists detail

            } //--- end header column

            $i++;
          } //--- end foreach
        }
        else
        {
          $sc = FALSE;
          $message = 'ไฟล์มีจำนวนรายการเกิน 500 บรรทัด';
        }
    } //-- end import success

    echo $sc === TRUE ? 'success' : $message;
  }



  public function update_api_stock($item)
  {
    if(getConfig('SYNC_WEB_STOCK'))
    {
      $sell_stock = $this->stock_model->get_sell_stock($item);
      $reserv_stock = $this->orders_model->get_reserv_stock($item);
      $availableStock = $sell_stock - $reserv_stock;
      $this->api->update_stock($item, $availableStock);
    }

  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER');
    $run_digit = getConfig('RUN_DIGIT_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);
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
}

 ?>
