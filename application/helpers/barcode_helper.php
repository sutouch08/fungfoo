<?php

function ean_checkdigit($code){
  $code = str_pad($code, 12, "0", STR_PAD_LEFT);
  $sum = 0;
  for($i=(strlen($code)-1);$i>=0;$i--){
    $sum += (($i % 2) * 2 + 1 ) * $code[$i];
  }
  return (10 - ($sum % 10));
}

function generateEAN($ean){
  $digits=array(3211,2221,2122,1411,1132,1231,1114,1312,1213,3112);
  $mirror=array("000000","001011","001101","001110","010011","011001","011100","010101","010110","011010");
  $guards=array("9a1a","1a1a1","a1a");

  $ean=trim($ean);
  if (preg_match("#[^0-9]#i",$ean)){
    die("Invalid EAN-Code");
  }

  if (strlen($ean)<12 || strlen($ean)>13){
    die("Invalid EAN13 Code (must have 12/13 numbers)");
  }

  $ean=substr($ean,0,12);
  $eansum=ean_checkdigit($ean);
  $ean.=$eansum;
  return $ean;
}


 ?>
