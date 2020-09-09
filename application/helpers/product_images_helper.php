<?php
function get_image_path($id, $size = 'default')
{
  $CI =& get_instance();
  $CI->load->model('masters/product_image_model');
  $code = $CI->product_image_model->get_style_code($id);
  $path = $CI->config->item('image_path').'products/';
  $no_image_path = base_url().$path.$size.'/no_image_'.$size.'.jpg';
  if($code !== FALSE)
  {
    $image_path = base_url().$path.$size.'/'.$code.'/product_'.$size.'_'.$id.'.jpg';
    $file = $CI->config->item('image_file_path').'products/'.$size.'/'.$code.'/product_'.$size.'_'.$id.'.jpg';
    return file_exists($file) ? $image_path : $no_image_path;
  }

  return $no_image_path;
}




function get_product_image($code, $size = 'default')
{
  $CI =& get_instance();
  $CI->load->model('masters/product_image_model');
  $id_image = $CI->product_image_model->get_id_image($code);
  return get_image_path($id_image, $size);
}




function delete_product_image($id, $code)
{
  $CI =& get_instance();
  $path = $CI->config->item('image_file_path').'products/';
  $use_size = array('mini', 'default', 'medium', 'large');
  foreach($use_size as $size)
  {
    $image_path = $path.$size.'/'.$code.'/product_'.$size.'_'.$id.'.jpg';
    unlink($image_path);
  }
}



function get_cover_image($code, $size = 'default')
{
  $CI =& get_instance();
  $CI->load->model('masters/product_image_model');
  $id  = $CI->product_image_model->get_cover($code);
  return get_image_path($id, $size);
}


function no_image_path($size)
{
  $CI =& get_instance();
  $path = $CI->config->item('image_path');
  $no_image_path = base_url().$path.$size.'/no_image_'.$size.'.jpg';
  return $no_image_path;
}
?>
