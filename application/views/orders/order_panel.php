<?php
// if($order->is_term == 0)
// {
//   $this->load->view('orders/order_online_panel');
// }
// else
// {
//   if($order->role == 'S')
//   {
//     $this->load->view('orders/order_online_panel');
//   }
//
//   $this->load->view('orders/order_state');
// }

if($order->role == 'S')
  {
    $this->load->view('orders/order_online_panel');
  }
?>
