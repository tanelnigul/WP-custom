<?php
  /*
  Plugin Name: Integrate Directo
  Description: Integrates Directo software with WooCommerce.
  Version: 2.1
  Author: <a href="http://blueglass.ee">BlueGlass Tallinn</a>
  License: GPL2
  */

  add_action( 'plugins_loaded', 'init_directo' );

  function init_directo(){
    // send order to directo after order completed
    add_action( 'woocommerce_thankyou', 'send_order_to_directo', 1000, 1 );
  }

  function send_order_to_directo( $order_id ) {
      $order = wc_get_order( $order_id );

      $order_data = $order->get_data();
      $order_payment_method = $order_data['payment_method'];
      // $order_total = $order->get_total();
      $order_billing_country = $order_data['billing']['country'];

      // define currency of country
      if ($order_billing_country == "KE") {
        $order_currency_new = "KES";
      } elseif ($order_billing_country == "TZ") {
        $order_currency_new = "TZS";
      }

      $order_total = $order->get_total();
      global $woocommerce_wpml;
      $order_total_new = convert_price( $order_total, $order_currency_new );

      // $new_total = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $order_total, $order_currency_new );
      $order->add_order_note("Order billing country currency: ".$order_currency_new."<br>Total price in that currency: ".$order_total_new."", 0, true);

      if ( $order_payment_method == "bacs" OR $order_payment_method == "cheque" OR $order_payment_method == "cod" ) {
        $order->update_status('pending', 'order_note');
        throw new Exception( __( 'Slow payment selected. Status pending.', 'woocommerce' ) );
        exit();
      } else {
        fast_payment( $order_id );
      }
  }

  function sv_wc_process_order_meta_box_action( $order ) {

  		$order_data = $order->get_data();
    	$order_id = $order_data['id'];
      $current_user = wp_get_current_user();
      $current_username = $current_user->user_login;
      $current_userid = $current_user->ID;
      $order->update_status('processing', 'order_note');
      $order->add_order_note("Order sent to Directo by ".$current_username."(".$current_userid.")", 0, true);
  		slow_payment( $order_id );

      // add the flag // uncomment in production
      // update_post_meta( $order->id, '_wc_order_marked_sent_to_directo', 'yes' );

  }
  add_action( 'woocommerce_order_action_wc_custom_order_action', 'sv_wc_process_order_meta_box_action' );


  function slow_payment( $order_id ) {
  	$order = wc_get_order( $order_id );

  	$order_data = $order->get_data();
  	$order_id = $order_data['id'];

    $prefix = '100000';
    $order_id_new = $prefix + $order_id;

    $order_customer_id = $order->get_user_id();
    $order_parent_id = $order_data['parent_id'];
    $order_status = $order_data['status'];
    $order_currency = $order_data['currency'];
    $order_version = $order_data['version'];
    $order_payment_method_title = $order_data['payment_method_title'];
    $order_payment_method = $order_data['payment_method'];

    $order_total = $order->get_total();

    // Using a formated date ( with php date() function as method)
    $order_date_created = $order_data['date_created']->date('d-m-Y H:i:s');

    // Using a timestamp ( with php getTimestamp() function as method)
    $order_timestamp_created = $order_data['date_created']->getTimestamp();
    $order_timestamp_modified = $order_data['date_modified']->getTimestamp();

    $order_discount_total = $order_data['discount_total'];
    $order_discount_tax = $order_data['discount_tax'];
    $order_shipping_total = $order_data['shipping_total'];
    $order_shipping_tax = $order_data['shipping_tax'];
    $order_total = $order_data['cart_tax'];
    $order_total_tax = $order_data['total_tax'];
    $order_customer_id = $order_data['customer_id']; // ... and so on

    ## BILLING INFORMATION:

    $order_billing_first_name = $order_data['billing']['first_name'];
    $order_billing_last_name = $order_data['billing']['last_name'];
    $order_billing_company = $order_data['billing']['company'];
    $order_billing_address_1 = $order_data['billing']['address_1'];
    $order_billing_address_2 = $order_data['billing']['address_2'];
    $order_billing_city = $order_data['billing']['city'];
    $order_billing_state = $order_data['billing']['state'];
    $order_billing_postcode = $order_data['billing']['postcode'];
    $order_billing_country = $order_data['billing']['country'];
    $order_billing_email = $order_data['billing']['email'];
    $order_billing_phone = $order_data['billing']['phone'];
    $order_billing_pin = get_post_meta( $order->get_id(), '_billing_pin', true );
    $order_billing_vat = get_post_meta( $order->get_id(), '_billing_vat', true );

    ## SHIPPING INFORMATION:

    $order_shipping_first_name = $order_data['shipping']['first_name'];
    $order_shipping_last_name = $order_data['shipping']['last_name'];
    $order_shipping_company = $order_data['shipping']['company'];
    $order_shipping_address_1 = $order_data['shipping']['address_1'];
    $order_shipping_address_2 = $order_data['shipping']['address_2'];
    $order_shipping_city = $order_data['shipping']['city'];
    $order_shipping_state = $order_data['shipping']['state'];
    $order_shipping_postcode = $order_data['shipping']['postcode'];
    $order_shipping_country = $order_data['shipping']['country'];
    $order_shipping_pin = get_post_meta( $order->get_id(), '_shipping_pin', true );
    $order_shipping_vat = get_post_meta( $order->get_id(), '_shipping_vat', true );

    foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
      $order_item_name           = $shipping_item_obj->get_name();
      $order_item_type           = $shipping_item_obj->get_type();
      $shipping_method_title     = $shipping_item_obj->get_method_title();
      $shipping_method_id        = $shipping_item_obj->get_method_id(); // The method ID
      $shipping_method_total     = $shipping_item_obj->get_total();
      $shipping_method_total_tax = $shipping_item_obj->get_total_tax();
      $shipping_method_taxes     = $shipping_item_obj->get_taxes();
    }

    if ($shipping_method_title == 'Flat rate') {
      $deliverymethod = 'TRANSPORTATION';
    } else {
      $deliverymethod = 'SELF_PICKUP';
    }

    ## SALESMAN INFORMATION:

    $order_salesman = get_salesman_from_hash($order_customer_id);

    if ($order_salesman == NULL) {
      $order_salesman_first_name = 'XML';
    } else {
    $order_salesman_id = $order_salesman[0]->sales_rep_id;
    $order_salesman_object = get_user_by('ID', $order_salesman_id);
    $order_salesman_name = $order_salesman_object->first_name;
    $order_salesman_email = $order_salesman_object->user_email;

    if (strpos($order_salesman_email, '64door') !== false) {
      $order_salesman_first_name = $order_salesman_name;
    } else {
      $order_salesman_first_name = 'XML';
    }

    }

    if ($order_billing_country !== $order_shipping_country) {
      // header("Location: /?error=countries-dont-match");
      throw new Exception( __( 'Countries have to match.', 'woocommerce' ) );
      exit();
    }

    // Comment out for testing purposes. Uncomment in production.
    // if (!$order->has_status( 'processing' )) { // if order does not come from admin panel
    //   if ( $order_payment_method == "bacs" OR $order_payment_method == "cheque" OR $order_payment_method == "cod" ) {
    //     $order->update_status('pending', 'order_note');
    //     throw new Exception( __( 'Slow payment selected. Status pending.', 'woocommerce' ) );
    //     exit();
    //   }
    // }

    // define currency of country
    if ($order_billing_country == "KE") {
      $order_currency_new = "KES";
    } elseif ($order_billing_country == "TZ") {
      $order_currency_new = "TZS";
    }

    // $order->set_currency($order_currency_new);

    $paid_currency = get_paid_currency( $order_id );

    $paymentmethod = "None";
    // define payment method (i.e CSH etc)
    if ($order_payment_method == "cod") { // CASH
      $paymentmethod = "CSH";
    } elseif ($order_payment_method == "bacs" OR $order_payment_method == "cheque") { // BANK TRANSFER
      $paymentmethod = "BNK";
    } elseif ($order_payment_method == "pesapal") { // PESAPAL.. obviously
      $paymentmethod = "PP";
    }

    if ($order_billing_company != null) {
      $order_company_contact_name = $order_billing_first_name.' '.$order_billing_last_name;
    } else {
      $order_company_contact_name = "";
    }

    // define paymentterm (i.e BNK-USD)
    $order_paymentterm = $paymentmethod.'-'.$paid_currency;

    ## PAID CURRENCY VS DIRECTO DATABASE CURRENCY
    ## FOR CURRENCYRATE


    $coin = 1;
    $usd = 1;
    global $woocommerce_wpml;

    if ($order_billing_country == "TZ") {
      if ($paid_currency == "KES") {
        $coin = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $usd, 'TZS' );
        $currencyrate1 = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $coin, 'TZS' );
        $currencyrate2 = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $coin, 'KES' );
        $currencyrate = $currencyrate1/$currencyrate2;
      } else if ($paid_currency == "TZS") {
        $currencyrate = 1.0;
      } else if ($paid_currency == "USD") {
        $currencyrate = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $usd, 'TZS' );
      }
    } else if ($order_billing_country == "KE") {
      if ($paid_currency == "KES") {
        $currencyrate = 1.0;
      } else if ($paid_currency == "TZS") {
        $coin = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $usd, 'KES' );
        $currencyrate1 = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $coin, 'KES' );
        $currencyrate2 = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $coin, 'TZS' );
        $currencyrate = $currencyrate1/$currencyrate2;
      } else if ($paid_currency == "USD") {
        $currencyrate = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $usd, 'KES' );
      }
    }

    ## GENERATE SKU
    $door_types = get_door_types();
    $standard_measures = get_standard_measures();



    // If shipping address is chosen in Web

    // $order_shipping_first_name = '';
    // $order_shipping_last_name = '';
    // $order_shipping_address_1 = '';
    // $order_shipping_address_2 = '';
    // $order_shipping_postcode = '';
    // $order_shipping_city = '';
    // $order_shipping_country = '';
    if ( get_field('order_paid_time', $order_id) !== NULL ) {
      $order_paid_time = get_field('order_paid_time', $order_id);
    } else {
      $order_paid_time = '';
    }

    // Directo appkey
    $appkey = "3C682C0215E837AF19F2B7C40C9E088A";

    $xmlheader = '<?xml version="1.0" encoding="utf-8"?>
    <transport>
    	<orders>
    		<order appkey="'.$appkey.'" number="'.$order_id_new.'" date="'.$order_date_created.'" VATzone="0" paymentterm="'.$order_paymentterm.'" confirmed="0" priceformula="" object="'.$order_billing_country.'" project="" country="'.$order_billing_country.'" currency="'.$paid_currency.'" currencyrate="'.$currencyrate.'" customer_code="'.$order_billing_pin.'" payer_code="" customer_name="'.$order_billing_first_name.' '.$order_billing_last_name.'" contact="'.$order_company_contact_name.'" comment="" internalcomment="" warehouse="IN_TRANSIT" deliverymethod="'.$deliverymethod.'"
        salesman="'.$order_salesman_first_name.'" language="" customerordernumber="'.$order_id_new.'" total="'.$order_total.'" VAT="0" totalwithVAT="0" sum="'.$order_total.'" balance="0" paymenttime="'.$order_paid_time.'" email="'.$order_billing_email.'" address1="'.$order_billing_address_1.', '.$order_billing_address_2.'" address2="'.$order_billing_postcode.' '.$order_billing_city.'" address3="'.$order_billing_country.'" phone="'.$order_billing_phone.'" deliveryname="'.$order_shipping_first_name.' '.$order_shipping_last_name.'"
        deliveryaddress1="'.$order_shipping_address_1.' '.$order_shipping_address_2.'"
        deliveryaddress2="'.$order_billing_postcode.' '.$order_billing_city.'" deliveryaddress3="'.$order_shipping_country.'" VATregno="'.$order_billing_vat.'" datafield1="" datafield2="" datafield3="" datafield4="" datafield5="" datafield6="" datafield7="" type="" status="NEW" ts="'.$order_timestamp_created.'"><rows>';

    $xmlfooter = '</rows></order></orders></transport>';

    $xmlrows = '';

    $i = 1;
    $j = 1;

    // Iterating through each item in the order
    foreach ( $order->get_items() as $item_id => $item_data ) {

      $_product = $item_data->get_product();

      $item_id = $item_data->get_id();
      $height = wc_get_order_item_meta( $item_id, 'doorset_height' );
      $width = wc_get_order_item_meta( $item_id, 'doorset_width' );
      $color = wc_get_order_item_meta( $item_id, 'doorset_color' );
      $door_type = wc_get_order_item_meta( $item_id, 'doorset_door_type' );
      $door_design = wc_get_order_item_meta( $item_id, 'doorset_design');
      $frame_width = wc_get_order_item_meta( $item_id, 'doorset_frame_width');
      $frame_ext = $item_data['doorset_frame_extension'];
      $install = wc_get_order_item_meta( $item_id, 'doorset_install');

      // print_r($door_types[$door_type]);
      // die();


      $sheet = '';
      if ($width < 731) {
        $sheet = 605;
      } else if ($width >= 731 && $width < 780) {
        $sheet = 655;
      } else if ($width >= 781 && $width < 831) {
        $sheet = 705;
      } else if ($width >= 831 && $width < 881) {
        $sheet = 755;
      } else if ($width >= 881) {
        $sheet = 805;
      }

      $design_code = 'S';
      if (get_field('windowed_frame', $door_design)) {
        $design_code = 'V';
      }

      $panel_sku = array();
      $panel_sku[] = $design_code;
      $panel_sku[] = $door_types[$door_type];
      $panel_sku[] = $color;
      $panel_sku[] = $sheet;
      $panel_sku[] = $frame_width;
      $panel_sku[] = $install;
      if ($frame_ext) {
  			$frame_ex = '-EX';
        $frame_width = '180';
  		} else {
        $frame_ex = '';
      }
      if (!in_array($height, $standard_measures['height']) || !in_array($width, $standard_measures['width'])) {
        $custom = '-C';
      } else {
        $custom = '';
      }

      $doorset_sku = $design_code.'-'.$door_types[$door_type].'-'.$color.'-'.$sheet.'-'.$frame_width.'-'.$install.''.$frame_ex.''.$custom;

      //$product = $item->get_product();
      $product_name = $item_data->get_name();

      $product_id = $item_data['product_id'];
      $variation_id = $item_data['variation_id'];
      $quantity = $item_data['quantity'];
      $tax_class = $item_data['tax_class'];
      $line_subtotal = $item_data['subtotal'];
      $line_subtotal_tax = $item_data['subtotal_tax'];
      $line_total = $item_data['total'];
      $line_total_tax = $item_data['total_tax'];
      $doorset_width = $item_data['doorset_width'];
      $doorset_height = $item_data['doorset_height'];
      $doorset_thickness = $item_data['doorset_thickness'];
      $doorset_design = $item_data['doorset_design'];
      $doorset_color = $item_data['doorset_color'];
      $doorset_door_type = $item_data['doorset_door_type'];
      $doorset_lock = get_the_title($item_data['doorset_lock']);
      $doorset_lock_core = get_the_title($item_data['doorset_lock_core']);
      $doorset_hinge = get_the_title($item_data['doorset_hinge']);
      $doorset_handle = get_the_title($item_data['doorset_handle']);
      $doorset_key_hole_cover = get_the_title($item_data['doorset_key_hole_cover']);
      $doorset_striking_plate = get_the_title($item_data['doorset_striking_plate']);

      if (is_doorset_product($_product->get_id())) {
        $doorset_lock_data = wc_get_product($item_data['doorset_lock']);
        $doorset_lock_sku = $doorset_lock_data->get_sku();
        // $doorset_lock_sku = '0';

        $doorset_lock_core_data = wc_get_product($item_data['doorset_lock_core']);
        $doorset_lock_core_sku = $doorset_lock_core_data->get_sku();
        // $doorset_lock_core_sku = '0';

        $doorset_hinge_data = wc_get_product($item_data['doorset_hinge']);
        $doorset_hinge_sku = $doorset_hinge_data->get_sku();
        // $doorset_hinge_sku = '0';

        $doorset_handle_data = wc_get_product($item_data['doorset_handle']);
        $doorset_handle_sku = $doorset_handle_data->get_sku();
        // $doorset_handle_sku = '0';

        $doorset_key_hole_cover_data = wc_get_product($item_data['doorset_key_hole_cover']);
        $doorset_key_hole_cover_sku = $doorset_key_hole_cover_data->get_sku();
        // $doorset_key_hole_cover_sku = '0';

        $doorset_striking_plate_data = wc_get_product($item_data['doorset_striking_plate']);
        $doorset_striking_plate_sku = $doorset_striking_plate_data->get_sku();
        // $doorset_striking_plate_sku = '0';
      } else {
        $product_sku = $_product->get_sku();
      }

      $doorset_integrated_threshold = $item_data['doorset_integrated_threshold'];
      $doorset_frame_width = $item_data['doorset_frame_width'];
      $doorset_frame_extension = $item_data['doorset_frame_extension'];
      $doorset_install = $item_data['doorset_install'];
      $doorset_handedness = $item_data['doorset_handedness'];
      $doorset_panel_sku = $item_data['doorset_panel_sku'];
      $doorset_extras = $item_data['doorset_extras'];

      // Find discount per line if override price is in place
      $override_price = $item_data['somemeta']->override_price;
      $share_override_price = $item_data['override_price'];

      $discount = 0;

      if (is_doorset_product($_product->get_id())) {
        $discountsum = $item_data['doorset_final_price'] - $line_subtotal;
        $discount = $discountsum / $item_data['doorset_final_price'] * 100;
      } elseif (is_magic_door($_product->get_id())) {
        $discountsum = $item_data['magic_door_price'] - $line_subtotal;
        $discount = $discountsum / $item_data['magic_door_price'] * 100;
      } else {
        $discountsum = $_product->get_regular_price() - $line_subtotal;
        $discount = $discountsum / $_product->get_regular_price() * 100;
      }

      // $discount = $order->get_discount_total();

      // TEMPORARY
      // $doorset_lock = 'Doorset Lock';
      // $doorset_lock_core = 'Doorset Lock Core';
      // $doorset_hinge = 'Doorset Hinge';
      // $doorset_handle = 'Doorset Handle';
      // $doorset_key_hole_cover = 'Doorset Key Hole Cover';
      // $doorset_striking_plate = 'Doorset Striking Plate';

      ## CONVERT LINE SUBTOTAL TO PRICE IN WHICH ORDER WAS PAID
      // $line_subtotal = $line_subtotal/$currencyrate;
      global $woocommerce_wpml;
      $line_subtotal = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $line_subtotal, $paid_currency );

      // $order_discount_total = get_discount_amount( $line_subtotal );

      $order->set_total($line_subtotal);

      if (is_doorset_product($_product->get_id())) {
        // DOORSET
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_sku.'" unit="pcs" quantity="'.$quantity.'" description="'.$product_name.'" price="'.$item_data['doorset_final_price'].'" total="'.$item_data['doorset_final_price'].'" discount="'.$discount.'" object="" comment="WDTH:'.$doorset_width.',HGTH:'.$doorset_height.',THCK:'.$doorset_thickness.',HAND:'.$doorset_handedness.'" rn="'.$j.'" rr="'.$i.'"></row>';
        $j++;
        // EXTRAS
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_hinge_sku.'" unit="pcs" quantity="'.$quantity.'" description="'.$doorset_hinge.'" price="0" total="0" object="" comment="" rn="'.$j.'" rr="'.$i.'"></row>';
        $j++;
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_lock_core_sku.'" unit="pcs" quantity="'.$quantity.'" description="'.$doorset_lock_core.'" price="0" total="0" object="" comment="" rn="'.$j.'" rr="'.$i.'"></row>';
        $j++;
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_lock_sku.'" unit="pcs" quantity="'.$quantity.'" description="'.$doorset_lock.'" price="0" total="0" object="" comment="" rn="'.$j.'" rr="'.$i.'"></row>';
        $j++;
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_handle_sku.'" unit="pcs" quantity="'.$quantity.'" description="'.$doorset_handle.'" price="0" total="0" object="" comment="" rn="'.$j.'" rr="'.$i.'"></row>';
        $j++;
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_key_hole_cover_sku.'" unit="pcs" quantity="'.$quantity.'" description="'.$doorset_key_hole_cover.'" price="0" total="0" object="" comment="" rn="'.$j.'" rr="'.$i.'"></row>';
        $j++;
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_striking_plate_sku.'" unit="pcs" quantity="'.$quantity.'" description="'.$doorset_striking_plate.'" price="0" total="0" object="" comment="" rn="'.$j.'" rr="'.$i.'"></row>';
        $j++;
        if ($doorset_integrated_threshold) {
          $xmlrows .= '<row number="'.$order_id_new.'" item="SEAL-ASTD805-A-1-1012" unit="pcs" quantity="'.$quantity.'" description="Drop Down Seal – Trend" price="0" total="0" object="" comment="" rn="'.$j.'" rr="'.$i.'"></row>';
          $i++;
          $j++;
        }

        $i = $i + 7;
      } elseif (is_magic_door($_product->get_id())) {
        // MAGIC DOOR ITEM
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$product_sku.'" unit="pcs" quantity="'.$quantity.'" description="'.$product_name.'" price="'.$item_data['magic_door_price'].'" total="'.$item_data['magic_door_price'].'" discount="'.$discount.'" object="" comment="" rn="'.$j.'" rr="'.$i.'"></row>';
        $i++;
        $j++;

      } else {
        // NORMAL ITEM
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$product_sku.'" unit="pcs" quantity="'.$quantity.'" description="'.$product_name.'" price="'.$_product->get_regular_price().'" total="'.$_product->get_regular_price().'" discount="'.$discount.'" object="" comment="" rn="'.$j.'" rr="'.$i.'"></row>';
        $i++;
        $j++;
      }
    }

    $xmldata = $xmlheader .' '. $xmlrows .' '. $xmlfooter;

    // print_r('<pre>');
    // print_r( $order_billing_country );
    // print_r( $order_id );
    // print_r( $doorset_lock_sku );
    // print_r( '<br>'. $doorset_lock_core_sku );
    // print_r( '<br>'. $doorset_hinge_sku );
    // print_r( '<br>'. $doorset_lock_sku );
    // print_r( '<br>'. $doorset_key_hole_cover_sku );
    // print_r( '<br>'. $doorset_integrated_threshold );
    // print_r( $currencyrate );
    // print '<textarea>';
    // print_r( $line_subtotal_tax );
    // print '</textarea>';
    // die();

    // check which directo to send order data to
    if ($order_billing_country == "KE") {
      $url = 'https://directo.gate.ee/xmlcore/64door_factory_ke/xmlcore.asp';
    } elseif ($order_billing_country == "TZ") {
      $url = 'https://directo.gate.ee/xmlcore/64door_factory_tz/xmlcore.asp';
    }


    write_log($xmldata);

    directo_in( $xmldata, $url );

  }

  function fast_payment( $order_id ) {
    $order = wc_get_order( $order_id );


  	$order_data = $order->get_data();
  	$order_id = $order_data['id'];


    $prefix = '100000';
    $order_id_new = $prefix + $order_id;

    $order_customer_id = $order->get_user_id();
    $order_parent_id = $order_data['parent_id'];
    $order_status = $order_data['status'];
    $order_currency = $order_data['currency'];
    $order_version = $order_data['version'];
    $order_payment_method_title = $order_data['payment_method_title'];
    $order_payment_method = $order_data['payment_method'];

    // Using a formated date ( with php date() function as method)
    $order_date_created = $order_data['date_created']->date('d-m-Y H:i:s');

    // Using a timestamp ( with php getTimestamp() function as method)
    $order_timestamp_created = $order_data['date_created']->getTimestamp();
    $order_timestamp_modified = $order_data['date_modified']->getTimestamp();

    // $order_discount_total = $order_data['discount_total'];
    $order_discount_tax = $order_data['discount_tax'];
    $order_shipping_total = $order_data['shipping_total'];
    $order_shipping_tax = $order_data['shipping_tax'];
    $order_total = $order_data['cart_tax'];
    $order_total_tax = $order_data['total_tax'];
    $order_customer_id = $order_data['customer_id']; // ... and so on

    ## BILLING INFORMATION:

    $order_billing_first_name = $order_data['billing']['first_name'];
    $order_billing_last_name = $order_data['billing']['last_name'];
    $order_billing_company = $order_data['billing']['company'];
    $order_billing_address_1 = $order_data['billing']['address_1'];
    $order_billing_address_2 = $order_data['billing']['address_2'];
    $order_billing_city = $order_data['billing']['city'];
    $order_billing_state = $order_data['billing']['state'];
    $order_billing_postcode = $order_data['billing']['postcode'];
    $order_billing_country = $order_data['billing']['country'];
    $order_billing_email = $order_data['billing']['email'];
    $order_billing_phone = $order_data['billing']['phone'];
    $order_billing_pin = get_post_meta( $order->get_id(), '_billing_pin', true );
    $order_billing_vat = get_post_meta( $order->get_id(), '_billing_vat', true );

    ## SHIPPING INFORMATION:

    $order_shipping_first_name = $order_data['shipping']['first_name'];
    $order_shipping_last_name = $order_data['shipping']['last_name'];
    $order_shipping_company = $order_data['shipping']['company'];
    $order_shipping_address_1 = $order_data['shipping']['address_1'];
    $order_shipping_address_2 = $order_data['shipping']['address_2'];
    $order_shipping_city = $order_data['shipping']['city'];
    $order_shipping_state = $order_data['shipping']['state'];
    $order_shipping_postcode = $order_data['shipping']['postcode'];
    $order_shipping_country = $order_data['shipping']['country'];
    $order_shipping_pin = get_post_meta( $order->get_id(), '_shipping_pin', true );
    $order_shipping_vat = get_post_meta( $order->get_id(), '_shipping_vat', true );

    foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
      $order_item_name           = $shipping_item_obj->get_name();
      $order_item_type           = $shipping_item_obj->get_type();
      $shipping_method_title     = $shipping_item_obj->get_method_title();
      $shipping_method_id        = $shipping_item_obj->get_method_id(); // The method ID
      $shipping_method_total     = $shipping_item_obj->get_total();
      $shipping_method_total_tax = $shipping_item_obj->get_total_tax();
      $shipping_method_taxes     = $shipping_item_obj->get_taxes();
    }

    if ($shipping_method_title == 'Flat rate') {
      $deliverymethod = 'TRANSPORTATION';
    } else {
      $deliverymethod = 'SELF_PICKUP';
    }

    ## SALESMAN INFORMATION:

    $order_salesman = get_salesman($order_customer_id);

    if ($order_billing_country !== $order_shipping_country) {
      // header("Location: /?error=countries-dont-match");
      throw new Exception( __( 'Countries have to match.', 'woocommerce' ) );
      exit();
    }

    // Comment out for testing purposes. Uncomment in production.
    // if (!$order->has_status( 'processing' )) { // if order does not come from admin panel
    //   if ( $order_payment_method == "bacs" OR $order_payment_method == "cheque" OR $order_payment_method == "cod" ) {
    //     $order->update_status('pending', 'order_note');
    //     throw new Exception( __( 'Slow payment selected. Status pending.', 'woocommerce' ) );
    //     exit();
    //   }
    // }

    // define currency of country
    if ($order_billing_country == "KE") {
      $order_currency_new = "KES";
    } elseif ($order_billing_country == "TZ") {
      $order_currency_new = "TZS";
    }

    // $order->set_currency($order_currency_new);

    $paymentmethod = "None";
    // define payment method (i.e CSH etc)
    if ($order_payment_method == "cod") { // CASH
      $paymentmethod = "CSH";
    } elseif ($order_payment_method == "bacs" OR $order_payment_method == "cheque") { // BANK TRANSFER
      $paymentmethod = "BNK";
    } elseif ($order_payment_method == "pesapal") { // PESAPAL.. obviously
      $paymentmethod = "PP";
    }

    if ($order_billing_company != null) {
      $order_company_contact_name = $order_billing_first_name.' '.$order_billing_last_name;
    } else {
      $order_company_contact_name = "";
    }

    // define paymentterm (i.e BNK-USD)
    $order_paymentterm = $paymentmethod.'-'.$order_currency_new;


    ## PAID CURRENCY VS DIRECTO DATABASE CURRENCY
    ## FOR CURRENCYRATE

      $currencyrate = '1.000000000';
      $currency = $order_currency;
      // $paid_currency = 'Payment Fast';

    ## GENERATE SKU
    $door_types = get_door_types();
    $standard_measures = get_standard_measures();

    // Directo appkey
    $appkey = "3C682C0215E837AF19F2B7C40C9E088A";

    $xmlheader = '<?xml version="1.0" encoding="utf-8"?>
    <transport>
    	<orders>
    		<order appkey="'.$appkey.'" number="'.$order_id_new.'" date="'.$order_date_created.'" VATzone="0" paymentterm="'.$order_paymentterm.'" confirmed="0" priceformula="" object="'.$order_billing_country.'" project="" country="'.$order_billing_country.'" currency="'.$paid_currency.'" currencyrate="'.$currencyrate.'" customer_code="'.$order_billing_pin.'" payer_code="" customer_name="'.$order_billing_first_name.' '.$order_billing_last_name.'" contact="'.$order_company_contact_name.'" comment="" internalcomment="" warehouse="IN_TRANSIT" deliverymethod="'.$deliverymethod.'"
        salesman="'.$order_salesman_first_name.'" language="" customerordernumber="'.$order_id_new.'" total="'.$order_total.'" VAT="0" totalwithVAT="0" sum="'.$order_total.'" balance="0" paymenttime="'.$order_paid_time.'" email="'.$order_billing_email.'" address1="'.$order_billing_address_1.', '.$order_billing_address_2.'" address2="'.$order_billing_postcode.' '.$order_billing_city.'" address3="'.$order_billing_country.'" phone="'.$order_billing_phone.'" deliveryname="'.$order_shipping_first_name.' '.$order_shipping_last_name.'"
        deliveryaddress1="'.$order_shipping_address_1.' '.$order_shipping_address_2.'"
        deliveryaddress2="'.$order_billing_postcode.' '.$order_billing_city.'" deliveryaddress3="'.$order_shipping_country.'" VATregno="'.$order_billing_vat.'" datafield1="" datafield2="" datafield3="" datafield4="" datafield5="" datafield6="" datafield7="" type="" status="NEW" ts="'.$order_timestamp_created.'"><rows>';

    $xmlfooter = '</rows></order></orders></transport>';

    $xmlrows = '';

    $i = 1;

    // Iterating through each item in the order
    foreach ( $order->get_items() as $item_id => $item_data ) {

      $_product = $item_data->get_product();

      $item_id = $item_data->get_id();
      $height = wc_get_order_item_meta( $item_id, 'doorset_height' );
      $width = wc_get_order_item_meta( $item_id, 'doorset_width' );
      $color = wc_get_order_item_meta( $item_id, 'doorset_color' );
      $door_type = wc_get_order_item_meta( $item_id, 'doorset_door_type' );
      $door_design = wc_get_order_item_meta( $item_id, 'doorset_design');
      $frame_width = wc_get_order_item_meta( $item_id, 'doorset_frame_width');
      $frame_ext = $item_data['doorset_frame_extension'];
      $install = wc_get_order_item_meta( $item_id, 'doorset_install');


      $sheet = '';
      if ($width < 731) {
        $sheet = 605;
      } else if ($width >= 731 && $width < 780) {
        $sheet = 655;
      } else if ($width >= 781 && $width < 831) {
        $sheet = 705;
      } else if ($width >= 831 && $width < 881) {
        $sheet = 755;
      } else if ($width >= 881) {
        $sheet = 805;
      }

      $design_code = 'S';
      if (get_field('windowed_frame', $door_design)) {
        $design_code = 'V';
      }

      $panel_sku = array();
      $panel_sku[] = $design_code;
      $panel_sku[] = $door_types[$door_type];
      $panel_sku[] = $color;
      $panel_sku[] = $sheet;
      $panel_sku[] = $frame_width;
      $panel_sku[] = $install;
      if (!in_array($height, $standard_measures['height']) || !in_array($width, $standard_measures['width'])) {
        $frame = 'C';
      } else {
        $frame = 'EX';
      }

      $doorset_sku = $design_code.'-'.$door_types[$door_type].'-'.$color.'-'.$sheet.'-'.$frame_width.'-'.$install.'-'.$frame;

      if (!wc_get_order_item_meta( $item_id, 'doorset_panel_sku' )) {
        wc_add_order_item_meta( $item_id, 'doorset_panel_sku', implode('-', $panel_sku) );
      }

      //$product = $item->get_product();
      $product_name = $item_data->get_name();

      $product_id = $item_data['product_id'];
      $variation_id = $item_data['variation_id'];
      $quantity = $item_data['quantity'];
      $tax_class = $item_data['tax_class'];
      $line_subtotal = $item_data['subtotal'];
      $line_subtotal_tax = $item_data['subtotal_tax'];
      $line_total = $item_data['total'];
      $line_total_tax = $item_data['total_tax'];
      $doorset_width = $item_data['doorset_width'];
      $doorset_height = $item_data['doorset_height'];
      $doorset_thickness = $item_data['doorset_thickness'];
      $doorset_design = $item_data['doorset_design'];
      $doorset_color = $item_data['doorset_color'];
      $doorset_door_type = $item_data['doorset_door_type'];
      $doorset_lock = get_the_title($item_data['doorset_lock']);
      $doorset_lock_core = get_the_title($item_data['doorset_lock_core']);
      $doorset_hinge = get_the_title($item_data['doorset_hinge']);
      $doorset_integrated_threshold = ['doorset_integrated_threshold'][0];
      $doorset_frame_width = $item_data['doorset_frame_width'];
      $doorset_frame_extension = $item_data['doorset_frame_extension'];
      $doorset_install = $item_data['doorset_install'];
      $doorset_handedness = $item_data['doorset_handedness'];
      $doorset_panel_sku = $item_data['doorset_panel_sku'];
      $doorset_extras = $item_data['doorset_extras'];

      // EXTRAS
      if (is_doorset_product($_product->get_id())) {
        // DOORSET
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_sku.'" unit="pcs" quantity="'.$quantity.'" description="'.$product_name.'" price="'.$line_subtotal.'" total="'.$line_subtotal.'" object="" comment="WDTH:'.$doorset_width.',HGTH:'.$doorset_height.',THCK:'.$doorset_thickness.',HAND:'.$doorset_handedness.'" rn="1" rr="'.$i.'"></row>';

        if ($doorset_integrated_threshold) {
          $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_integrated_threshold.'" unit="pcs" quantity="'.$quantity.'" description="'.$doorset_integrated_threshold.'" price="0" total="0" object="" comment="" rn="1" rr="'.$i.'"></row>';
        }
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_hinge.'" unit="pcs" quantity="'.$quantity.'" description="'.$doorset_hinge.'" price="0" total="0" object="" comment="" rn="1" rr="'.$i.'"></row>';
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_lock_core.'" unit="pcs" quantity="'.$quantity.'" description="'.$doorset_lock_core.'" price="0" total="0" object="" comment="" rn="'.$i.'" rr="1"></row>';
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$doorset_lock.'" unit="pcs" quantity="'.$quantity.'" description="'.$doorset_lock.'" price="0" total="0" object="" comment="" rn="1" rr="'.$i.'"></row>';
      } else {
        // NORMAL ITEM
        $xmlrows .= '<row number="'.$order_id_new.'" item="'.$product_name.'" unit="pcs" quantity="'.$quantity.'" description="'.$product_name.'" price="'.$line_subtotal.'" total="'.$line_subtotal.'" object="" comment="WDTH:'.$doorset_width.',HGTH:'.$doorset_height.',THCK:'.$doorset_thickness.',HAND:'.$doorset_handedness.'" rn="1" rr="'.$i.'"></row>';
      }

      $i++;
    }


    $xmldata = $xmlheader .' '. $xmlrows .' '. $xmlfooter;

    // print_r('<pre>');
    // print_r( $xmldata );
    // print_r('</pre>');
    // die();

    // check which directo to send order data to
    if ($order_billing_country == "KE") {
      $url = 'https://directo.gate.ee/xmlcore/64door_factory_ke/xmlcore.asp';
    } elseif ($order_billing_country == "TZ") {
      $url = 'https://directo.gate.ee/xmlcore/64door_factory_tz/xmlcore.asp';
    }

    directo_in( $xmldata, $url );
  }

  function directo_in( $xmldata, $url ) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS, 'xmldata='.urlencode($xmldata).'&put=1&what=order');

    $result = curl_exec($ch);

    curl_close($ch);

  }

  // ERROR LOGGING

  if (!function_exists('write_log')) {
    function write_log ( $log )  {
      if ( true === WP_DEBUG ) {
        if ( is_array( $log ) || is_object( $log ) ) {
          error_log( print_r( $log, true ) );
            } else {
              error_log( $log );
              }
          }
      }
  }


  function id_register_settings() {
     add_option( 'id_options', 'This is my option value.');
     register_setting( 'id_options_group', 'id_options', 'id_callback' );
  }
  add_action( 'admin_init', 'id_register_settings' );

  function id_register_options_page() {
  add_options_page('Integrate Directo', 'Integrate Directo', 'manage_options', 'id', 'id_options_page');
  }
  add_action('admin_menu', 'id_register_options_page');

  function id_options_page() { ?>

  <div>
  <?php screen_icon(); ?>
  <h2>Integrate Directo settings</h2>
  <p>There will be options for the plugin here soon.</p>
  <p>Plugin developed by BlueGlass Tallinn.</p>
  <!-- <form method="post" action="options.php">
  <?php settings_fields( 'id_options_group' ); ?>
  <h3>This is my option</h3>
  <p>Some text here.</p>
  <table>
  <tr valign="top">
  <th scope="row"><label for="id_option_name">Label</label></th>
  <td><input type="text" id="id_option_name" name="id_option_name" value="<?php echo get_option('id_option_name'); ?>" /></td>
  </tr>
  </table>
  <?php  submit_button(); ?>
  </form> -->
  </div>
  <?php } ?>
