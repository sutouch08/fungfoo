<?php
  $this->load->helper('print');

  $page = '';
  $page .= $this->printer->doc_header();
	$this->printer->add_title("ใบรับสินค้าสำเร็จรูป");
	$header	= array(
    'เลขที่' => $doc->code,
    'วันที่'  => thai_date($doc->date_add, FALSE, '/'),
    'ใบสั่งซื้อ' => $doc->po_code,
    'ใบรับสินค้า' => $doc->invoice_code,
    'ผู้ขาย' => $doc->vender_name,
    'โซน' => $doc->zone_name,
    'คลัง' => $doc->warehouse_name,
    'พนักงาน' => $this->user_model->get_name($doc->user)
	);
  if($doc->remark != '')
  {
    $header['หมายเหตุ'] = $doc->remark;
  }

	$this->printer->add_header($header);

	$total_row 	= empty($details) ? 0 : count($details);
	$config = array(
    'total_row' => $total_row,
    'font_size' => 10,
    'sub_total_row' => 1
  );

	$this->printer->config($config);

	$row 	= $this->printer->row;
	$total_page = $this->printer->total_page;
	$total_qty 	= 0;
  $total_amount = 0;
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array(label_value('Num'), "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array(label_value('item_code'), "width:20%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array(label_value('item_name'), "text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
            array(label_value('po'), "width:10%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
            array(label_value('price'), "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array(label_value('qty'), "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array(label_value('amount'), "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
						);

	$this->printer->add_subheader($thead);

	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
              "text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:right; border-left: solid 1px #ccc; border-top:0px;"
							);
	$this->printer->set_pattern($pattern);

	//*******************************  กำหนดช่องเซ็นของ footer *******************************//
	$d = date('d', strtotime($doc->date_add) );
	$m = date('m', strtotime($doc->date_add) );
	$Y = date('Y', strtotime($doc->date_add) );
	$footer	= array(
						array("ผู้รับ", "", "วันที่ ............................."),
						array("ผู้ตรวจสอบ", "","วันที่ ............................."),
						array("ผู้อนุมัติ", "","วันที่ .............................")
						);

  $this->printer->set_footer($footer);

	$n = 1;
  $index = 0;
	while($total_page > 0 )
	{
		$page .= $this->printer->page_start();
			$page .= $this->printer->top_page();
			$page .= $this->printer->content_start();
				$page .= $this->printer->table_start();
				if($doc->status == 2)
				{
					$page .= '
				  <div style="width:0px; height:0px; position:relative; left:30%; line-height:0px; top:300px;color:red; text-align:center; z-index:100000; opacity:0.1; transform:rotate(-45deg)">
				      <span style="font-size:150px; border-color:red; border:solid 10px; border-radius:20px; padding:0 20 0 20;">ยกเลิก</span>
				  </div>';
				}

				$i = 0;

				while($i < $row)
        {
					$rs = isset($details[$index]) ? $details[$index] : array();
					if(!empty($rs))
          {
            $data = array(
              $n,
							inputRow($rs->product_code),
							inputRow($rs->product_name),
              $rs->po_code,
              number($rs->price, 2),
							number($rs->qty),
              number($rs->amount, 2)
						);
            $total_qty += $rs->qty;
            $total_amount += $rs->amount;
          }
          else
          {
            $data = array("", "", "", "","", "", "");
          }
					$page .= $this->printer->print_row($data);
					$n++;
          $i++;
          $index++;
				}

				$page .= $this->printer->table_end();

				if($this->printer->current_page == $this->printer->total_page)
				{
					$qty = number($total_qty);
          $amount = number($total_amount,2);
					$remark = $doc->remark;
				}else{
					$qty = "";
          $amount = "";
					$remark = "";
				}

				$sub_total = array(
          array(
          "<td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
          border-bottom:0px; border-left:0px; text-align:right;'>
          <strong>รวม</strong>
          </td>
          <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
          border-right:0px; border-bottom:0px; width:10%; text-align:right;'>
          ".number($total_qty)."</td>
          <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
          border-right:0px; border-bottom:0px; border-bottom-right-radius:10px;
          text-align:right; width:15%;'>".number($total_amount,2)."</td>")

			);

			$page .= $this->printer->print_sub_total($sub_total);
			$page .= $this->printer->content_end();
			$page .= $this->printer->footer;
		  $page .= $this->printer->page_end();
		  $total_page --;
      $this->printer->current_page++;
	}

	$page .= $this->printer->doc_footer();

  echo $page;
?>
