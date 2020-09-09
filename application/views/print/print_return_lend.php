<?php
  $this->load->helper('print');

  $page = '';
  $page .= $this->printer->doc_header();
	$this->printer->add_title("รับคืนสินค้า(จากการยืม)");
	$header	= array(
    'เลขที่' => $doc->code,
    'วันที่'  => thai_date($doc->date_add, FALSE, '/'),
    'ผู้ยืม' => $doc->customer_code.' : '.$doc->customer_name,
    'เลขที่อ้างอิง' => $doc->lend_code,
    'โซนต้นทาง' => $doc->from_zone_name,
    'โซนปลายทาง' => $doc->to_zone_name,
    'คลังต้นทาง' => $doc->from_warehouse_name,
    'คลังปลายทาง' => $doc->to_warehouse_name,
    'ผู้รับคืน' => $this->user_model->get_name($doc->user)
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
  $total_lend = 0;
  $total_receive = 0;
  $total_backlogs = 0;

	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("รหัส", "width:20%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("สินค้า", "width:30%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
            array("ยืม", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("คืน(รวมครั้งนี้)", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
            array("ครั้งนี้", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("คงเหลือ", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
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
	$footer	= array(
						array("ผู้คืน", "", "วันที่ ............................."),
						array("ผู้รับคืน", "","วันที่ ............................."),
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
            $backlogs = $rs->qty - $rs->receive;
            $data = array(
              $n,
							inputRow($rs->product_code),
							inputRow($rs->product_name),
              ac_format($rs->qty),
							ac_format($rs->receive),
              ac_format($rs->return_qty),
              ac_format($backlogs < 0 ? 0 : $backlogs)
						);

            $total_qty += $rs->return_qty;
            $total_lend += $rs->qty;
            $total_receive += $rs->receive;
            $total_backlogs += ($backlogs < 0 ? 0 : $backlogs);

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

        $is_last = FALSE;

				if($this->printer->current_page == $this->printer->total_page)
				{
          $is_last = TRUE;
        }

				$sum_lend = $is_last === TRUE ? number($total_lend) : '';
        $sum_receive = $is_last === TRUE ? number($total_receive) : '';
        $sum_qty = $is_last === TRUE ? number($total_qty) : '';
        $sum_backlogs = $is_last === TRUE ? number($total_backlogs) : '';

				$sub_total = array(
          array(
          "<td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
          border-bottom:0px; border-left:0px; text-align:right;
          width:55.2%;'>
          <strong>รวม</strong>
          </td>
          <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
          border-right:0px; border-bottom:0px; width:10%; text-align:right;'>
          ".$sum_lend."</td>
          <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
          border-right:0px; border-bottom:0px; width:15%; text-align:right;'>
          ".$sum_receive."</td>
          <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
          border-right:0px; border-bottom:0px; width:10%; text-align:right;'>
          ".$sum_qty."</td>
          <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
          border-right:0px; border-bottom:0px; border-bottom-right-radius:10px;
          text-align:right;'>".$sum_backlogs."</td>")

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
