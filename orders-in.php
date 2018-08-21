<?php
/*
Plugin Name: Integrate Directo - Orders In
Description: Integrates Directo software with WooCommerce.
Version: 1.0
Author: <a href="http://blueglass.ee">BlueGlass Tallinn</a>
License: GPL2
*/

// get_order_from_directo( 5010 );

function get_order_from_directo( $order_id ) {

  // $user_id = get_current_user_id();

  $url = 'https://directo.gate.ee/xmlcore/64door_factory_ke/xmlcore.asp?get=1&what=order&key=3C682C0215E837AF19F2B7C40C9E088A';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, $url );
  curl_setopt($ch, CURLOPT_POST, 0);

  $result = curl_exec($ch);

  // echo $result;

  header('Content-Type: text/html; charset=utf-8');

  $data = new SimpleXMLElement($result);

  foreach( $data->orders->order as $order ) {
    if ( $order['number'] == 100000 + $order_id ) {
      echo '<div class="directo_info">';

      $inproduction = 0;
      $indelivery = 0;
      $delivered = 0;
      $finished = 0;
      foreach( $order->rows->row as $row ) {
        if ( $row['inproduction'] > 0 ) {
          $inproduction = $inproduction + $row['inproduction'];
        }
        if ( $row['indelivery'] > 0 ) {
          $indelivery = $indelivery + $row['indelivery'];
        }
        if ( $row['delivered'] > 0 ) {
          $delivered = $delivered + $row['delivered'];
        }
        if ( $row['produced'] > 0 ) {
          $finished = $finished + $row['produced'];
        }
        if ( !$row['acceptedtime'] ) {
          $acceptedtime = 'Order not reviewed yet, check back later';
        } else {
          $acceptedtime = $row['acceptedtime'];
          $acceptedtime = str_replace("T", " ", $acceptedtime);
        }
        if ( !$row['finishedtime'] ) {
          $finishedtime = 'Estimated finish time not specified yet, check back later';
        } else {
          $finishedtime = $row['finishedtime'];
          $finishedtime = str_replace("T", " ", $finishedtime);
        }
        if ( !$row['targetdeliverytime'] ) {
          $targetdelivery = 'Target delivery time not specified yet, check back later';
        } else {
        $targetdelivery = $row['targetdeliverytime'];
        $targetdelivery = str_replace("T", " ", $targetdelivery);
        }
      }
      echo '<table>
        <tr>
          <td>Accepted time:</td>
          <td>'.$acceptedtime.'</td>
        </tr>
        <tr>
          <td>Items in production:</td>
          <td>'.$inproduction.'</td>
        </tr>
        <tr>
          <td>Finished items:</td>
          <td>'.$finished.'</td>
        </tr>
        <tr>
          <td>Estimated finish time:</td>
          <td>'.$finishedtime.'</td>
        </tr>
        <tr>
          <td>Target delivery time:</td>
          <td>'.$targetdelivery.'</td>
        </tr>
        <tr>
          <td>Items in delivery:</td>
          <td>'.$indelivery.'</td>
        </tr>
        <tr>
          <td>Delivered:</td>
          <td>'.$delivered.'</td>
        </tr>
      </table></div>';
    }
  }

}
