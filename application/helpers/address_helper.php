<?php
function get_address_form($adds, $sds, $ds, $dd)
{
  $sc = 'no_address';
    //--- มีที่อยู่เดียว และผู้จัดส่งเดียว
    if($adds == 1 && $sds == 1 )
    {
      $sc = 1;
    }

    //--- มีที่อยู่ แต่ ไม่มีผู้จัดส่ง
    else if( $adds >= 1 && $sds < 1 )
    {

      $sc  = 'no_sender';

    }
    //--- มีที่อยู่มากกว่า 1 หรือ ผู้จัดส่งมากกว่า 1
    else
    {
      //--- มีที่อยู่มากกว่า 1 ที่
      if( $adds >= 1 )
      {
        $add  = '<tr>';
        $add .=   '<td colspan="2">';
        $add .=     '<strong>เลือกที่อยู่สำหรับจัดส่ง</strong>';
        $add .=   '</td>';
        $add .= '<tr>';

        $n    = 1;
        if(!empty($ds))
        {
          foreach($ds as $rs)
          {
            $se = $n == 1 ? 'checked' : '';
            $add .= '<tr>';
            $add .=   '<td class="width-35 middle">';
            $add .=     '<label>';
            $add .=       '<input type="radio" class="ace" name="id_address" value="'.$rs->id.'" '.$se.' />';
            $add .=       '<span class="lbl">&nbsp;&nbsp;'.$rs->alias.'</span>';
            $add .=     '</label>';
            $add .=   '</td>';
            $add .=   '<td style="white-space:normal;">';
            $add .=     $rs->address.'  ต. '.$rs->sub_district.' อ. '.$rs->district.' จ. '.$rs->province;
            $add .=   '</td>';
            $add .= '</tr>';
            $n++;
          }
        }
      }

      $dds = '';
      //--- มีผู้จัดส่งมากกว่า 1
      if( $sds >= 1 )
      {
        $dds  = '<tr>';
        $dds .=   '<td colspan="2">';
        $dds .=     '<strong>เลือกผู้ให้บริการจัดส่ง</strong>';
        $dds .=   '</td>';
        $dds .= '</tr>';


        //--- กำหนดให้มีผู้จัดส่งได้ไม่เกิน 3 รายเท่านั้น
        if(!empty($dd))
        {
          //--- ผู้จัดส่งรายหลัก
          $dds .= '<tr >';
          $dds .=   '<td colspan="2" style="white-space:normal;">';
          $dds .=     '<label>';
          $dds .=       '<input type="radio" class="ace" name="id_sender" value="'.$dd->main_sender.'" checked />';
          $dds .=       '<span class="lbl">&nbsp;&nbsp; '.$dd->main.'</span>'; //---  transport_helper
          $dds .=     '</label>';
          $dds .=   '</td>';
          $dds .= '</tr>';


          //--- รายที่ 2
          if(!empty($dd->second_sender))
          {
            $dds .= '<tr>';
            $dds .=   '<td colspan="2">';
            $dds .=     '<label>';
            $dds .=       '<input type="radio" class="ace" name="id_sender" value="'.$dd->second_sender.'" />';
            $dds .=       '<span class="lbl">&nbsp;&nbsp; '.$dd->second.'</span>'; //---  transport_helper
            $dds .=     '</label>';
            $dds .=   '</td>';
            $dds .= '</tr>';
          }


          //--- รายที่ 3
          if(!empty($dd->third_sender))
          {
            $dds .= '<tr>';
            $dds .=   '<td colspan="2">';
            $dds .=     '<label>';
            $dds .=       '<input type="radio" class="ace" name="id_sender" value="'.$dd->third_sender.'" />';
            $dds .=       '<span class="lbl">&nbsp;&nbsp; '.$dd->third.'</span>'; //---  transport_helper
            $dds .=     '</label>';
            $dds .=   '</td>';
            $dds .= '</tr>';
          }

        } //--- end if $ds
      }

      $dds .= '<tr>';
      $dds .=   '<td colspan="2">';
      $dds .=     '<label>';
      $dds .=       '<span class="lbl">จำนวนกล่อง </span>'; //---  transport_helper
      $dds .=       '<input type="number" class="form-control input-sm input-mini" name="print_qty" value="1" />';
      $dds .=     '</label>';
      $dds .=   '</td>';
      $dds .= '</tr>';

      //--- ประกอบร่าง
      if( $adds >= 1 && $sds >= 1 )
      {
        $sc = '<table class="table table-bordered">';
        $sc .= $add;
        $sc .= $dds;
        $sc .= '</table>';
      }
    }


  return $sc;
}

 ?>
