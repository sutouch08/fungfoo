<?php
$this->load->helper('print');

$page = '';
$page .= $this->printer->doc_header();
$this->printer->add_title(label_value('po'));
$header	= array(
  label_value('doc_num') => $po->code,
  label_value('date')  => thai_date($po->date_add, FALSE, '/'),
  label_value('vender') => $po->vender_name,
  label_value('require_date') => thai_date($po->require_date, FALSE, '/'),
  label_value('emp') => $this->user_model->get_name($po->user)
);

if($po->remark != '')
{
  $header[label_value('remark')] = $po->remark;
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
          array(label_value('item_name'), "width:40%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
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
            "text-align:right; border-left: solid 1px #ccc; border-top:0px;"
            );
$this->printer->set_pattern($pattern);

//*******************************  กำหนดช่องเซ็นของ footer *******************************//
$d = date('d', strtotime($po->date_add) );
$m = date('m', strtotime($po->date_add) );
$Y = date('Y', strtotime($po->date_add) );
$footer	= array(
          array(label_value('conducted_by'), "", label_value('date')." ............................."),
          array(label_value('review_by'), "",label_value('date')." ............................."),
          array(label_value('approve_by'), "",label_value('date')."่ .............................")
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
      if($po->status == 4)
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
            number($rs->price, 2),
            number($rs->qty),
            number($rs->total_amount, 2)
          );
          $total_qty += $rs->qty;
          $total_amount += $rs->total_amount;
        }
        else
        {
          $data = array("", "", "", "","", "");
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
        $amount = number($total_amount, 2);
      }else{
        $qty = "";
        $amount = "";
      }

      $sub_total = array(
        array(
        "<td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
        border-bottom:0px; border-left:0px; text-align:right;
        width:75%;'>
        <strong>รวม</strong>
        </td>
        <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
        border-right:0px; border-bottom:0px; width:10%; text-align:right;'>
        ".$qty."
        </td>
        <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
        border-right:0px; border-bottom:0px; border-bottom-right-radius:10px;
        text-align:right;'>".$amount."</td>")

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
