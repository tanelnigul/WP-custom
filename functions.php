<?php
//
// BlueGlass Interactive
//

$theme_version  = '1.0';
$functions_path = TEMPLATEPATH . '/functions/';
$template_url   = get_bloginfo( 'template_url' );


// Security best practices
include_once( $functions_path . '/security.php' );

// Theme ajax functions
include_once( $functions_path . '/ajax.php' );

// Theme sidebars
include_once( $functions_path . '/sidebars.php' );

// Helper functions
include_once( $functions_path . '/helpers.php' );

//Post types
//include_once($functions_path . 'post_types.php');

//Category meta
//include_once($functions_path . 'category_meta.php');

//Shortcodes
require_once $functions_path . 'theme_shortcodes/shortcodes.php';
//include_once($functions_path . 'theme_shortcodes/alert.php');
include_once( $functions_path . 'theme_shortcodes/tabs.php' );
include_once( $functions_path . 'theme_shortcodes/toggle.php' );
//include_once($functions_path . 'theme_shortcodes/html.php');

if ( is_admin() ) {
	// Taxonomy custom fields
	//include_once($functions_path . 'category-meta.php');

	//tinyMCE includes
	include_once( $functions_path . 'theme_shortcodes/tinymce_shortcodes.php' );
	include_once( $functions_path . '/add_thumbs_to_admin.php' );
}


//
// Security Best Practices ( Comment function to disable )
//
blueglass_security_disable_xmlrpc();
blueglass_security_disable_rest_api();
blueglass_security_remove_rss_links();
blueglass_security_dissalow_file_edit();
blueglass_security_remove_wp_version_string();
//


add_filter( 'woocommerce_get_breadcrumb', '__return_false' );

// Get language code from WPML if one of plugin is enabled
if ( function_exists( 'icl_get_languages' ) ) {
	$lang = ICL_LANGUAGE_CODE;
} else {
	$default_lang = explode( '-', get_bloginfo( 'language' ) );
	$lang         = $default_lang[0];
}


// Setups
add_action( 'after_setup_theme', 're_setup_template' );
function re_setup_template() {
	add_theme_support( 'post-thumbnails' );

	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'image', 'quote', 'video' ) );

	//add_image_size( 'tiny', 78, 81, true );

	register_nav_menus( array( 'top-menu' => __( 'Top menu', 'blueglass' ) ) );
	add_theme_support( 'woocommerce' );
}

add_action( 'after_setup_theme', 're_setup_template_2' );
function re_setup_template_2() {
	add_theme_support( 'post-thumbnails' );

	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'image', 'quote', 'video' ) );

	//add_image_size( 'tiny', 78, 81, true );

	register_nav_menus( array( 'dark-menu' => __( 'Dark menu', 'blueglass' ) ) );
}


add_action( 'admin_init', 'blueglass_admin_JS_init_method' );
function blueglass_admin_JS_init_method() {
	global $template_url;

	wp_enqueue_script( 'adminjs', $template_url . '/functions/admin_js.js', 'jquery', false );
	wp_enqueue_style( 'adminjs', $template_url . '/functions/admin_css.css', 'jquery', false );
}


if ( ! function_exists( 'mighty_enqueue_head_scripts' ) ) {
	add_action( 'get_header', 'blueglass_enqueue_head_scripts' );
	function blueglass_enqueue_head_scripts() {
		global $template_url, $theme_version;

		wp_enqueue_style( 'fancybox', $template_url . "/css/app.css", false, $theme_version );
		//wp_enqueue_style( 'fancybox', $template_url ."/css/jquery.fancybox.css", FALSE, $theme_version );
		wp_enqueue_style( 'slick', $template_url ."/js/slick-slider/slick/slick.css", FALSE, $theme_version );
	}
}

/*
	Remember to switch "production" parameter to "true" in gulpfile.js before going live.
	This will minify all js files in "src/js" folder
*/
add_action( 'get_footer', 'blueglass_JS_init_method' );
function blueglass_JS_init_method() {
	global $template_url;

	// Load jQuery
	if ( ! is_admin() ) {

		$localized_scripts = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
		//$localized_scripts['custom'] = 'variable';

		wp_enqueue_script( 'jquery' );

		// Foundation Core
		wp_enqueue_script( 'theme-foundation', $template_url . '/bower_components/foundation-sites/dist/js/foundation.min.js', 'jquery' );
		//wp_enqueue_script('theme-mousewheel', $template_url.'/js/jquery.mousewheel-3.0.6.pack.js', 'jquery');
		//wp_enqueue_script('theme-fancybox', $template_url.'/js/jquery.fancybox.js', 'jquery');
		//wp_enqueue_script('theme-fancybox-media', $template_url.'/js/jquery.fancybox-media.js', 'jquery');

		wp_enqueue_style( 'slickjs', $template_url . '/js/slick-slider/slick/slick.min.js', 'jquery', false );
		wp_enqueue_script( 'theme-javascript', $template_url . '/js/app.js', 'theme-scripts' );
		wp_localize_script( 'theme-javascript', 'scripts_localized', $localized_scripts );

	}
}

// add acf options page
if ( function_exists( 'acf_add_options_page' ) ) {

	$args = array(
		'page_title'  => 'Theme Settings',
		'menu_title'  => 'Theme Settings',
		'menu_slug'   => '',
		'capability'  => 'manage_options',
		'position'    => false,
		'parent_slug' => 'themes.php',
		'icon_url'    => false,
		'redirect'    => true,
		'post_id'     => 'options',
		'autoload'    => false,

	);

	acf_add_options_page( $args );
}

//Ajax login


function ajax_login_init() {

	wp_register_script( 'ajax-login-script', get_template_directory_uri() . '/js/ajax-login-script.js', array( 'jquery' ) );
	wp_enqueue_script( 'ajax-login-script' );

	wp_localize_script( 'ajax-login-script', 'ajax_login_object', array(
		'ajaxurl'            => admin_url( 'admin-ajax.php' ),
		'redirecturl'        => home_url( '/my-account' ),
		'txt_authenticating' => __( 'Authenticating', 'giveaway' ),
		'txt_failed'         => __( 'Login failed', 'giveaway' ),
		'txt_welcome'        => __( 'Welcome back!', 'giveaway' )
	) );

	// Enable the user with no privileges to run ajax_login() in AJAX
	add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
	add_action( 'wp_ajax_ajaxlogin', 'ajax_login' );
}

// Execute the action only if the user isn't logged in
if ( ! is_user_logged_in() ) {
	add_action( 'init', 'ajax_login_init' );
}

function ajax_login() {
	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-login-nonce', 'security' );
	// Nonce is checked, get the POST data and sign user on
	$info                  = array();
	$info['user_login']    = sanitize_text_field( $_POST['username'] );
	$info['user_password'] = sanitize_text_field( $_POST['password'] );
	$info['remember']      = false;

	$user_signon = wp_signon( $info, false );
	if ( is_wp_error( $user_signon ) ) {
		echo json_encode( array( 'loggedin' => false, 'message' => __( 'Wrong username or password.' ) ) );
	} else {
		echo json_encode( array( 'loggedin' => true, 'message' => __( 'Login successful, redirecting...' ) ) );
	}

	wp_die();
}


//Add Wordpress user role

add_role(
	'sales_representative',
	__( 'Sales Representative' ),
	array(
		'read'         => false,  // true allows this capability
		'edit_posts'   => false,
		'delete_posts' => false, // Use false to explicitly deny
	)
);


//Woocommerce add cart to user with hashed link

function parse_cart_old( $cart_items_array, $discount_to_cart, $quantity ) {
	$quantity_count = 0;

	foreach ( $cart_items_array as $cart_item ) {
		$quantity[ $quantity_count ];
		WC()->cart->add_to_cart( $cart_item, $quantity[ $quantity_count ] );
		$quantity_count ++;

		if ( end( $cart_items_array ) == $cart_item ) {
			if ( ! empty( $discount_to_cart ) ) {
				WC()->cart->add_discount( $discount_to_cart );
			}
			?>
      <script>
        window.location = "<?php echo home_url( '/cart' ) ?>"
      </script>
			<?php
		}
	}
}

function parse_cart( $cart_items_array, $discount_to_cart ) {

	//clear cart
	WC()->cart->empty_cart();

	// add items with meta and variations
	// print_r($cart_items_array);
	// die();

	foreach($cart_items_array as $key => $item) {
		WC()->cart->add_to_cart( $item['id'], $item['quantity'], $item['variation_id'], $item['variation'], $item['cart_item_data'] );
	}

	if ( ! empty( $discount_to_cart ) ) {

		// Create a random hash variable for coupon
		$coupon_code = substr(md5(uniqid(mt_rand(), true)) , 0, 8);
		$amount = $discount_to_cart; // Coupon discount in %
		$discount_type = 'percent_product'; // 'percent' - %; 'fixed_cart' - fixed amount; percent_product - per product

		$coupon = array(
			'post_title'   => $coupon_code,
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'	   => 'shop_coupon'
		);

		$new_coupon_id = wp_insert_post( $coupon );

		// Add meta
		update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
		update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
		update_post_meta( $new_coupon_id, 'individual_use', 'yes' );
		update_post_meta( $new_coupon_id, 'product_ids', '' );
		update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
		update_post_meta( $new_coupon_id, 'usage_limit', '1' );
		update_post_meta( $new_coupon_id, 'expiry_date', '' );
		update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
		update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

		// print($discount_to_cart);
		// die();
		// $discount_to_cart = sanitize_text_field( 'testingdiscounts' );
		WC()->cart->add_discount( $coupon_code );
		WC()->cart->calculate_totals();
		WC()->cart->set_session();
	}
	// print_r($cart_items_array);
	// die();
	?>
	<script>
		 window.location = "<?php echo home_url( '/cart' ); ?>"
 </script>
 <?php

}

add_action( 'wp_ajax_nopriv_fill_customer_information', 'fill_customer_information' );
add_action( 'wp_ajax_fill_customer_information', 'fill_customer_information' );
function fill_customer_information() {
	global $wpdb;
	$sales_cart = $_COOKIE["client-offer-hash"];
		$client_info                 = $wpdb->get_results( "SELECT cart_client_data FROM 64d_woocommerce_sales_sent_carts WHERE sent_cart_hash = '$sales_cart'", OBJECT );
	if ( ! empty( $client_info ) ) {
		$client_info_decoded         = json_decode( $client_info[0]->cart_client_data, true );
		$data['billing_first_name']  = $client_info_decoded['billing_first_name'];
		$data['billing_last_name']   = $client_info_decoded['billing_last_name'];
		$data['billing_country']     = $client_info_decoded['billing_country'];
		$data['billing_street']      = $client_info_decoded['billing_street'];
		$data['billing_apartment']   = $client_info_decoded['billing_apartment'];
		$data['billing_postcode']    = $client_info_decoded['billing_postcode'];
		$data['billing_city']        = $client_info_decoded['billing_city'];
		$data['billing_phone']       = $client_info_decoded['billing_phone'];
		$data['billing_email']       = $client_info_decoded['billing_email'];
		$data['shipping_first_name'] = $client_info_decoded['shipping_first_name'];
		$data['shipping_last_name']  = $client_info_decoded['shipping_last_name'];
		$data['shipping_country']    = $client_info_decoded['shipping_country'];
		$data['shipping_street']     = $client_info_decoded['shipping_street'];
		$data['shipping_apartment']  = $client_info_decoded['shipping_apartment'];
		$data['shipping_postcode']   = $client_info_decoded['shipping_postcode'];
		$data['shipping_city']       = $client_info_decoded['shipping_city'];
		$data['shipping_phone']      = $client_info_decoded['shipping_phone'];
		$data['shipping_email']      = $client_info_decoded['shipping_email'];
		$data['success']             = true;
	}else{
		$data['success']             = false;
    }
	echo json_encode( $data );
	die();

}

function sales_rep_clients_links( $sales_rep_id ) {
	global $wpdb;
	$sales_rep_hashed_links_clients = $wpdb->get_results( "SELECT * FROM 64d_woocommerce_sales_sent_carts WHERE sales_rep_id = '$sales_rep_id' ORDER BY ID DESC", OBJECT );

	if ( ! empty( $sales_rep_hashed_links_clients ) ) {
		return $sales_rep_hashed_links_clients;
	} else {
		return false;
	}
}

add_action( 'wp_ajax_nopriv_create_link_for_client', 'create_link_for_client' );
add_action( 'wp_ajax_create_link_for_client', 'create_link_for_client' );

function create_link_for_client() {
	global $wpdb;
	$current_user            = wp_get_current_user();
	$current_user_roles      = $current_user->roles;
	$current_user_id         = $current_user->ID;

	$personal_discount        = get_field( 'user_maximum_discount', 'user_' . $current_user_id );

	$customer_generated_link = false;
	if ( in_array( 'customer', $current_user_roles ) ) {
		$customer_generated_link = true;
	} else if ( in_array( 'administrator', $current_user_roles ) || in_array( 'sales_representative', $current_user_roles ) ) {
		$customer_generated_link = false;
	}

	$customer_info_json                        = [];
	$customer_info_json['billing_first_name']  = filter_var( $_REQUEST['data']['billing_first_name'], FILTER_SANITIZE_STRING );
	$customer_info_json['billing_last_name']   = filter_var( $_REQUEST['data']['billing_last_name'], FILTER_SANITIZE_STRING );
	$customer_info_json['billing_country']     = filter_var( $_REQUEST['data']['billing_country'], FILTER_SANITIZE_STRING );
	$customer_info_json['billing_street']      = filter_var( $_REQUEST['data']['billing_street'], FILTER_SANITIZE_STRING );
	$customer_info_json['billing_apartment']   = filter_var( $_REQUEST['data']['billing_apartment'], FILTER_SANITIZE_STRING );
	$customer_info_json['billing_postcode']    = filter_var( $_REQUEST['data']['billing_postcode'], FILTER_SANITIZE_STRING );
	$customer_info_json['billing_city']        = filter_var( $_REQUEST['data']['billing_city'], FILTER_SANITIZE_STRING );
	$customer_info_json['billing_phone']       = filter_var( $_REQUEST['data']['billing_phone'], FILTER_SANITIZE_STRING );
	$customer_info_json['billing_email']       = filter_var( $_REQUEST['data']['billing_email'], FILTER_SANITIZE_STRING );
	$customer_info_json['shipping_first_name'] = filter_var( $_REQUEST['data']['shipping_first_name'], FILTER_SANITIZE_STRING );
	$customer_info_json['shipping_last_name']  = filter_var( $_REQUEST['data']['shipping_last_name'], FILTER_SANITIZE_STRING );
	$customer_info_json['shipping_country']    = filter_var( $_REQUEST['data']['shipping_country'], FILTER_SANITIZE_STRING );
	$customer_info_json['shipping_street']     = filter_var( $_REQUEST['data']['shipping_street'], FILTER_SANITIZE_STRING );
	$customer_info_json['shipping_apartment']  = filter_var( $_REQUEST['data']['shipping_apartment'], FILTER_SANITIZE_STRING );
	$customer_info_json['shipping_postcode']   = filter_var( $_REQUEST['data']['shipping_postcode'], FILTER_SANITIZE_STRING );
	$customer_info_json['shipping_city']       = filter_var( $_REQUEST['data']['shipping_city'], FILTER_SANITIZE_STRING );
	$customer_info_json['shipping_phone']      = filter_var( $_REQUEST['data']['shipping_phone'], FILTER_SANITIZE_STRING );
	$customer_info_json['shipping_email']      = filter_var( $_REQUEST['data']['shipping_email'], FILTER_SANITIZE_STRING );

	$customer_info_json = json_encode( $customer_info_json );

	$customer_id    = filter_var( $_REQUEST['data']["customer_id"], FILTER_SANITIZE_STRING );
	$sales_discount = filter_var( $_REQUEST['data']["discount_percentage"], FILTER_SANITIZE_NUMBER_INT );
	$discount_type = filter_var( $_REQUEST['data']["discount_type"], FILTER_SANITIZE_NUMBER_INT );

	// $user_discount = get_field( 'user_maximum_discount', 'user_' . $current_user_id );

		if ( $personal_discount != null) {
			$maximum_discount = $personal_discount;
		}	else if ( current_user_can('administrator') ) {
			$maximum_discount = get_field( 'admins_max_discount', 'option' );
		} elseif ( current_user_can('sales_representative') ) {
			$maximum_discount = get_field( 'salespersons_max_discount', 'option' );
 		}


	if ( $sales_discount !=null ) {
		if ( $maximum_discount != null ) {
			if ( $sales_discount > $maximum_discount ) {
				$data['success'] = false;
				$data['message'] = __( "You don't have permission to give more than ", '64door' ) . $maximum_discount . __( "% discount.", '64door' );
				echo json_encode( $data );
				die();
			} elseif ( $maximum_discount = null ) {
					$data['success'] = false;
					$data['message'] = __( "You don't have permission to give discounts.", '64door' );
					echo json_encode( $data );
					die();
			}
		}
	}

	$cart_items = [];
	$quantity   = [];
	$products   = [];

	$doorbuilder_fields = get_doorbuilder_fields();

	$total = 0;

	foreach ( WC()->cart->get_cart() as $cart_item ) {

		$total = $total + $cart_item['price'];

		$meta = array();
		$variation_id = null;
		$variation = array();
		if ( $cart_item['variation_id'] != 0 ) {
			$variation_id = $cart_item['variation_id'];
			$variation = $cart_item['variation'];
		}
		// in case of doorbuilder
		if (is_doorset_product($cart_item['product_id'])) {
			foreach($doorbuilder_fields as $k => $v) {
				if (isset($cart_item[$k])) {
					$meta[$k] = $cart_item[$k];
				}
			}
		}
		// add professional measure meta if available
		if (isset($cart_item['doorset_professional_measure'])) {
			$meta['doorset_professional_measure'] = $cart_item['doorset_professional_measure'];
		}
		if (isset($cart_item['somemeta']->override_price)) {
			$meta['override_price'] = $cart_item['somemeta']->override_price;
		}
		if (isset($cart_item['magic_door_desc'])) {
			$meta['magic_door_desc'] = $cart_item['magic_door_desc'];
		}
		if (isset($cart_item['magic_door_title'])) {
			$meta['magic_door_title'] = $cart_item['magic_door_title'];
		}
		if (isset($cart_item['magic_door_price'])) {
			$meta['magic_door_price'] = $cart_item['magic_door_price'];
		}

		$products[] = array(
			'id' => $cart_item['product_id'],
			'quantity' => $cart_item['quantity'],
			'variation_id' => $variation_id,
			'variation' => $variation,
			'cart_item_data' => $meta
		);


	}
	// print '<pre>';
	// print_r($products);
	// print '</pre>';
	$cart_json['cart_items'] = $products;

	// $cart_items_separated = '';
	// $cart_item_count      = count( $cart_items );
	// $cart_item_counter    = 1;
	// foreach ( $cart_items as $cart_item ) {
	// 	if ( $cart_item_counter == $cart_item_count ) {
	// 		$cart_items_separated    .= $cart_item;
	// 		$cart_json['cart_items'] = $cart_items_separated;
	// 	} else {
	// 		$cart_items_separated .= $cart_item . ';';
	// 		$cart_item_counter ++;
	// 	}
	// }
	// $cart_quantity_separated = '';
	// $cart_quantity_count     = count( $quantity );
	// $cart_quantity_counter   = 1;
	// foreach ( $quantity as $quan ) {
	// 	if ( $cart_quantity_counter == $cart_quantity_count ) {
	// 		$cart_quantity_separated    .= $quan;
	// 		$cart_json['cart_quantity'] = $cart_quantity_separated;
	// 	} else {
	// 		$cart_quantity_separated .= $quan . ';';
	// 		$cart_quantity_counter ++;
	// 	}
	// }

	$salesman_id = get_current_user_id();

	$cart_json['user_id'] = 0;
	if ( ! empty( $customer_id ) ) {
		$cart_json['user_id'] = $customer_id;
	}
	$cart_json['sales_discount'] = '';
	if ( ! empty( $sales_discount ) ) {
		$cart_json['sales_discount'] = $sales_discount;
	}
	$cart_json['salesman_id'] = '';
	if ( ! empty( $salesman_id ) ) {
		$cart_json['salesman_id'] = $salesman_id;
	}
	if ( ! empty( $total ) ) {
		$cart_json['total'] = $total;
	}
	$cart_json['discount_type'] = $discount_type;

	$cart_json = json_encode( $cart_json );
	$cart_hash = md5( uniqid( rand(), true ) );
	$cart_hash = hash_check( $cart_hash );

	if ( $customer_generated_link ) {
		$wpdb->insert( '64d_woocommerce_client_generated_carts', array(
			'sent_cart_hash'     => $cart_hash,
			'sent_cart_contents' => $cart_json,
			'total_price'        => $total,
		) );
	} else {
		$wpdb->insert( '64d_woocommerce_sales_sent_carts', array(
			'sent_cart_hash'     => $cart_hash,
			'sent_cart_contents' => $cart_json,
			'cart_client_data'   => $customer_info_json,
			'sales_rep_id'       => get_current_user_id(),
			'total_price'        => $total,
			'used_or_not'        => 0,
		) );
	}

	// print $cart_json;
	// die();

	$data['customer_info'] = $customer_info_json;
	$data['success']       = true;
	$data['hash']          = $cart_hash;
	$data['clientlink']    = home_url( "/client-sales/?client_offer=" ) . $cart_hash;
	echo json_encode( $data );

	include('woocommerce/emails/customer-offer-email.php');

	$body = get_offer_email_template();

	// wp_mail(
	// 	'tanel@blueglass.ee',
	// 	'Offer nr X from 64door.com',
	// 	$body
	// );

	die();
}

function hash_check( $cart_hash ) {
	global $wpdb;
	$results   = $wpdb->get_results( "SELECT * FROM 64d_woocommerce_sales_sent_carts WHERE sent_cart_hash = '$cart_hash'", OBJECT );
	//$results_2 = $wpdb->get_results( "SELECT * FROM 64d_woocommerce_client_generated_links WHERE sent_cart_hash = '$cart_hash'", OBJECT );
	if ( empty( $results ) && empty( $results_2 ) ) {
		return $cart_hash;
	} else {
		$cart_hash = md5( uniqid( rand(), true ) );

		return hash_check( $cart_hash );
	}
}

function get_sales_rep_clients( $sales_rep_id ) {
	global $wpdb;
	$results = $wpdb->get_results( "SELECT client_id FROM 64d_woocommerce_sales_rep_clients WHERE sales_rep_id = '$sales_rep_id'", OBJECT );
	if ( ! empty( $results ) ) {
		return $results;
	} else {
		return false;
	}
}

function get_salesman( $order_customer_id ) {
	global $wpdb;
	$results = $wpdb->get_results( "SELECT sales_rep_id FROM 64d_woocommerce_sales_rep_clients WHERE client_id = '$order_customer_id'" );
	if ( ! empty( $results ) ) {
		return $results;
	} else {
		return false;
	}
}

function get_salesman_from_hash( $order_customer_id ) {
	if (isset($_COOKIE["woocommerce_cart_hash"])) {
		global $wpdb;
		$cart_hash                  = $_COOKIE["woocommerce_cart_hash"];
		// $results                    = $wpdb->get_results( "SELECT * FROM 64d_woocommerce_sales_sent_carts WHERE sent_cart_hash = '$cart_hash'", OBJECT );
		// $sent_cart_contents_decoded = json_decode( $results[0]->sent_cart_contents, true );
		// $cart_total                 = WC()->cart->cart_contents_total;

		$results = $wpdb->get_results( "SELECT sales_rep_id FROM 64d_woocommerce_sales_sent_carts WHERE sent_cart_hash = '$cart_hash'" );
		if ( ! empty( $results ) ) {
			// print $results;
			// die();
			return $results;
		} else {
			return false;
		}
	}
}

add_action( 'wp_ajax_nopriv_sales_rep_connect_new_user', 'sales_rep_connect_new_user' );
add_action( 'wp_ajax_sales_rep_connect_new_user', 'sales_rep_connect_new_user' );

function sales_rep_connect_new_user() {
	global $wpdb;
	$customer_email = filter_var( $_REQUEST['data']["customer_email"], FILTER_SANITIZE_STRING );

	$user_information = get_user_by( 'email', $customer_email );

	if ( empty( $user_information ) ) {
		$data['message'] = __( 'There is no user with that email', '64door' );
		$data['success'] = false;
		echo json_encode( $data );
		die();
	}


	$maybe_user_has_rep = $wpdb->get_results( "SELECT * FROM 64d_woocommerce_sales_rep_clients WHERE client_id = '$user_information->ID'", OBJECT );


	if ( $maybe_user_has_rep ) {
		$data['message'] = __( 'Customer already has a Sales Representative', '64door' );
		$data['success'] = false;
		echo json_encode( $data );
		die();
	} else {
		$wpdb->insert( '64d_woocommerce_sales_rep_clients', array(
			'sales_rep_id' => get_current_user_id(),
			'client_id'    => $user_information->ID,
		) );

		$data['message'] = __( 'Customer added to your list', '64door' );
		$data['success'] = true;
	}
	echo json_encode( $data );
	die();
}

// cart ajax functions


//Woocommerce Functions

//Custom Woocommerce ORDER statuses

function register_new_order_statuses_order_status() {
	register_post_status( 'wc-awaiting-shipment', array(
		'label'                     => 'Awaiting shipment',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Awaiting shipment <span class="count">(%s)</span>', 'Awaiting shipment <span class="count">(%s)</span>' )
	) );
	register_post_status( 'wc-awaiting-payment', array(
		'label'                     => 'Awaiting payment',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Awaiting payment <span class="count">(%s)</span>', 'Awaiting payment <span class="count">(%s)</span>' )
	) );
	register_post_status( 'wc-in-production', array(
		'label'                     => 'In production',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'In production <span class="count">(%s)</span>', 'In production <span class="count">(%s)</span>' )
	) );
	register_post_status( 'wc-order-shipped', array(
		'label'                     => 'Order shipped',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Order shipped <span class="count">(%s)</span>', 'Order shipped <span class="count">(%s)</span>' )
	) );
}

add_action( 'init', 'register_new_order_statuses_order_status' );

// Add to list of WC Order statuses
function add_new_order_statuses_to_order_statuses( $order_statuses ) {

	$new_order_statuses = array();

	// add new order status after processing
	foreach ( $order_statuses as $key => $status ) {

		$new_order_statuses[ $key ] = $status;

		if ( 'wc-processing' === $key ) {
			$new_order_statuses['wc-awaiting-shipment'] = 'Awaiting shipment';
			$new_order_statuses['wc-awaiting-payment']  = 'Awaiting payment';
			$new_order_statuses['wc-in-production']     = 'In production';
			$new_order_statuses['wc-order-shipped']     = 'Order shipped';
		}
	}

	return $new_order_statuses;
}

add_filter( 'wc_order_statuses', 'add_new_order_statuses_to_order_statuses' );

//Increase woocommerce cart expiry

if ( ! class_exists( 'WoocommerceLicenseAPI' ) ) {
	add_filter( 'wc_session_expiring', 'filter_ExtendSessionExpiring' );

	add_filter( 'wc_session_expiration', 'filter_ExtendSessionExpired' );

	function filter_ExtendSessionExpiring( $seconds ) {
		return ( 60 * 60 * 24 * 30 ) - ( 60 * 60 );
	}

	function filter_ExtendSessionExpired( $seconds ) {
		return 60 * 60 * 24 * 30;
	}
}


//woocommerce custom discount function


function sale_custom_price( $discount ) {

	if (isset($_COOKIE["client-offer-hash"])) {
		global $wpdb;
		$cart_hash                  = $_COOKIE["client-offer-hash"];
		$results                    = $wpdb->get_results( "SELECT * FROM 64d_woocommerce_sales_sent_carts WHERE sent_cart_hash = '$cart_hash'", OBJECT );
		$sent_cart_contents_decoded = json_decode( $results[0]->sent_cart_contents, true );
		$cart_total                 = WC()->cart->cart_contents_total;


		//Full cart discount
		$discount_percentage = $sent_cart_contents_decoded['sales_discount'];
		//$discount_fee        = (float) $cart_total * ( $discount_percentage / 100 );


		if ( ! empty( $discount_fee ) ) {
			$discount_fee *= - 1;

			WC()->cart->add_fee( 'Discount ' . $sent_cart_contents_decoded['sales_discount'] . '%', $discount_fee, true, '' );
		}
	}

	if (isset($_COOKIE["woocommerce_cart_hash"])) {
		global $wpdb;
		$cart_hash                  = $_COOKIE["client-offer-hash"];
		$results                    = $wpdb->get_results( "SELECT * FROM 64d_woocommerce_sales_sent_carts WHERE sent_cart_hash = '$cart_hash'", OBJECT );
		$sent_cart_contents_decoded = json_decode( $results[0]->sent_cart_contents, true );
		$salesman_id = $sent_cart_contents_decoded['salesman_id'];
		// print_r($results);
		// setcookie('salesman_id', $salesman_id);
		// die();

	}

}

add_action( 'woocommerce_cart_calculate_fees', 'sale_custom_price' );


/**
 * Add order again button in my orders actions.
 *
 * @param  array $actions
 * @param  WC_Order $order
 *
 * @return array
 */
function cs_add_order_again_to_my_orders_actions( $actions, $order ) {
	if ( $order->has_status( 'completed' ) || $order->has_status( 'order-shipped' ) ) {
		$actions['order-again'] = array(
			//get_the_'  => wp_nonce_url( add_query_arg( 'order_again', $order->id ), 'woocommerce-order_again' ),
			'name' => __( 'Order Again', 'woocommerce' )
		);
	}

	return $actions;
}

add_filter( 'woocommerce_my_account_my_orders_actions', 'cs_add_order_again_to_my_orders_actions', 50, 2 );


add_filter( 'wcml_load_multi_currency_in_ajax', 'load_multi_currency_in_ajax', 10, 1 );

function load_multi_currency_in_ajax( $load ) {

	if ( is_checkout() ) {
		$load = false; // If this is the checkout page, do not load multi-currency filters in AJAX actions
	} else if ( is_admin() ) {
		$load = false;
	} else {
		$load = true;
	}

	return $load;

}

function add_action_to_multi_currency_ajax( $ajax_actions ) {
	$ajax_actions[] = 'search_for_product_results';

	return $ajax_actions;
}

add_filter( 'wcml_multi_currency_ajax_actions', 'add_action_to_multi_currency_ajax', 10, 1 );


//product search ajax

add_action( 'wp_ajax_nopriv_search_for_product_results', 'search_for_product_results' );
add_action( 'wp_ajax_search_for_product_results', 'search_for_product_results' );


function search_for_product_results() {
	$category     = json_decode( filter_var( $_REQUEST['data']["categories"], FILTER_SANITIZE_STRING ) );
	$colors       = json_decode( filter_var( $_REQUEST['data']["colors"], FILTER_SANITIZE_STRING ) );
	$paged        = filter_var( $_REQUEST['data']["pagination"], FILTER_SANITIZE_STRING );
	$search_term  = filter_var( $_REQUEST['data']["search_term"], FILTER_SANITIZE_STRING );
	$in_stock     = filter_var( $_REQUEST['data']["in_stock"], FILTER_SANITIZE_STRING );
	$price_filter = filter_var( $_REQUEST['data']["price_filter"], FILTER_SANITIZE_STRING );
	$currency     = filter_var( $_REQUEST['data']["currency"], FILTER_SANITIZE_STRING );


	if ( $price_filter == 'ASC' ) {
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => 6,
			'orderby'        => 'meta_value_num',
			'meta_key'       => '_price',
			'order'          => 'asc',
			'paged'          => $paged,
		);
	} else if ( $price_filter == 'DESC' ) {
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => 6,
			'orderby'        => 'meta_value_num',
			'meta_key'       => '_price',
			'order'          => 'desc',
			'paged'          => $paged,
		);
	} else if ( $price_filter == 'ALL' ) {
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => 6,
			'order'          => 'desc',
			'paged'          => $paged,
		);
	}

	$args['suppress_filters'] = 0;

	$tax_query = array( 'RELATION' => 'AND' );
	array_push( $tax_query,
		array(
			'taxonomy' => 'product_cat',
			'terms'    => array( 65 ),
			'operator' => 'NOT IN',
		) );

	if ( ! empty( $category ) ) {
		array_push( $tax_query,
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $category
			) );
	}
	if ( ! empty( $colors ) ) {
		array_push( $tax_query,
			array(
				'taxonomy' => 'pa_color',
				'field'    => 'term_id',
				'terms'    => $colors
			)
		);
	}
	if ( ! empty( $search_term ) ) {
		$args['s'] = $search_term;
	}
	if ( $in_stock == 'in_stock' ) {
		$args['meta_query'] = array(
			array(
				'key'   => '_stock_status',
				'value' => 'instock'
			),
			array(
				'key'   => '_backorders',
				'value' => 'no'
			),
			array(
				'key'   => 'door_builder_item',
				'value' => array('1'),
                'compare' => 'NOT IN'
			),
		);
	}






	$args['tax_query'] = $tax_query;

	$products = new WP_Query( $args );


	if ( $paged == $products->max_num_pages ) {
		$data['hide_button'] = true;
	}
	$data['max_num_pages'] = $products->max_num_pages;


	global $woocommerce_wpml;

	$return_to_search = "";


	if ( $products->have_posts() ) {
		$return_to_search = '<div class="row examples-row">';
		while ( $products->have_posts() ) {
			$products->the_post();
			global $product;


			$return_to_search .= '<div class="columns medium-6 large-4 examples-single" data-suffix="' . $woocommerce_wpml->multi_currency->session->client_currency . '" data-id="' . $product->id . '" >';
			$return_to_search .= '<div class="image_container">';
			$return_to_search .= '<img src="' . get_the_post_thumbnail_url() . '">';
			$return_to_search .= '</div>';
			$return_to_search .= '<div class="prod-info">';
			$return_to_search .= '<h4>' . get_the_title() . '</h4>';
			$return_to_search .= '<p>' . get_the_content() . '</p>';
			$return_to_search .= '<!--<p class="prices old-price">from <span>24 000 ZIK</span> to <span>3 000 000 ZIK</span></p>-->';
			//$return_to_search .= '<p class="prices new-price">from <span>' .aw_price_filter( $product->get_price(), "KES" ). '</p>';
			$return_to_search .= '<p class="prices new-price"><span>' . custom_product_price_display( $product->id, $currency, true ) . '</p>';
			$return_to_search .= '</div>';
			$return_to_search .= '<a class="btn-offer" href="' . get_permalink() . '">' . __( 'Choose', '64Door' ) . ' </a>';
			$return_to_search .= '</div>';
		}
		$return_to_search .= '</div>';
	} else {
		$return_to_search .= '<h3>' . __( 'There are no products that match your selection', '64door' ) . '</h3>';
	}


	$data['success']       = true;
	$data['returned_html'] = $return_to_search;


	echo json_encode( $data );
	die();

}


function custom_product_price_display( $product_id, $currency, $ajax = false ) {
	global $woocommerce_wpml;
	$product          = wc_get_product( $product_id );
	$currency_options = $woocommerce_wpml->settings['currency_options'][ $currency ];
	$should_round     = $currency_options['rounding'];

	$currency_symbol = get_woocommerce_currency_symbol( $currency );

	$decimals           = $currency_options['num_decimals'];
	$decimal_separator  = $currency_options['decimal_sep'];
	$thousand_separator = $currency_options['thousand_sep'];
	$currency_position  = $currency_options['position'];

	$left  = '';
	$right = '';
	switch ( $currency_position ) {
		case 'left':
			$left = $currency_symbol;
			break;
		case 'right':
			$right = $currency_symbol;
			break;
		case 'left_space':
			$left = $currency_symbol . ' ';
			break;
		case 'right_space':
			$right = ' ' . $currency_symbol;
			break;
	}
	if ( $product->is_type( 'grouped' ) ) {
		$door_parts            = $product->get_children();
		$parts_category_arrays = [];
		$parts_category_values = [];
		foreach ( $door_parts as $part ) {
			$part_category                             = get_the_terms( $part, 'product_cat' )[0]->name;
			$parts_category_arrays[ $part_category ][] = $part;
			if ( ! in_array( $part_category, $parts_category_values ) ) {
				array_push( $parts_category_values, $part_category );
			}
		}
		$minimal_price = 0;
		$maximum_price = 0;
		foreach ( $parts_category_arrays as $category ) {
			$child_prices = array();
			foreach ( $category as $part ) {
				$child_prices[] = get_post_meta( $part, '_price', true );
			}
			$minimal_price += min( $child_prices );
			$maximum_price += max( $child_prices );
		}

		$min_price = $minimal_price;
		$max_price = $maximum_price;


		$ajax == true ? $price_min = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $min_price, $currency ) : $price_min = $min_price;
		empty( $price_min ) ? $price_min = 0 : $price_min;
		$should_round != 'disabled' ? $price_min = $woocommerce_wpml->multi_currency->prices->apply_rounding_rules( $price_min, $currency ) : $price_min;

		$ajax == true ? $price_max = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $max_price, $currency ) : $price_max = $max_price;
		empty( $price_max ) ? $price_max = 0 : $price_max;
		$should_round != 'disabled' ? $price_max = $woocommerce_wpml->multi_currency->prices->apply_rounding_rules( $price_max, $currency ) : $price_max;

		$price_min = apply_filters( 'formatted_woocommerce_price', number_format( $price_min, $decimals, $decimal_separator, $thousand_separator ), $price_min, $decimals, $decimal_separator, $thousand_separator );
		$price_max = apply_filters( 'formatted_woocommerce_price', number_format( $price_max, $decimals, $decimal_separator, $thousand_separator ), $price_max, $decimals, $decimal_separator, $thousand_separator );


		$price = '<span class="dark_price_letters">' . __( 'from', '64door' ) . '<span> ' . $left . $price_min . $right . ' <span class="dark_price_letters"> ' . __( 'to ', '64door' ) . '<span> ' . $left . $price_max . $right;


	} elseif ( $product->is_type( 'simple' ) ) {

		$ajax == true ? $price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $product->get_price(), $currency ) : $price = $product->get_price();
		empty( $price ) ? $price = 0 : $price;
		$should_round != 'disabled' ? $price = $woocommerce_wpml->multi_currency->prices->apply_rounding_rules( $price, $currency ) : $price;
		$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );
		$price = $left . $price . $right;

	} elseif ( $product->is_type( 'variable' ) ) {

		$ajax == true ? $price_min = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $product->get_variation_price( 'min', true ), $currency ) : $price_min = $product->get_variation_price( 'min', true );
		empty( $price_min ) ? $price_min = 0 : $price_min;
		$should_round != 'disabled' ? $price_min = $woocommerce_wpml->multi_currency->prices->apply_rounding_rules( $price_min, $currency ) : $price_min;

		$ajax == true ? $price_max = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $product->get_variation_price( 'max', true ), $currency ) : $price_max = $product->get_variation_price( 'max', true );
		empty( $price_max ) ? $price_max = 0 : $price_max;
		$should_round != 'disabled' ? $price_max = $woocommerce_wpml->multi_currency->prices->apply_rounding_rules( $price_max, $currency ) : $price_max;

		$price_min = apply_filters( 'formatted_woocommerce_price', number_format( $price_min, $decimals, $decimal_separator, $thousand_separator ), $price_min, $decimals, $decimal_separator, $thousand_separator );
		$price_max = apply_filters( 'formatted_woocommerce_price', number_format( $price_max, $decimals, $decimal_separator, $thousand_separator ), $price_max, $decimals, $decimal_separator, $thousand_separator );

		$price = '<span class="dark_price_letters">' . __( 'from', '64door' ) . '<span> ' . $left . $price_min . $right . ' <span class="dark_price_letters"> ' . __( 'to ', '64door' ) . '<span> ' . $left . $price_max . $right;

	}

	return $price;
}

function convert_price($price, $currency) {
	global $woocommerce_wpml;

	$currency_options = $woocommerce_wpml->settings['currency_options'][ $currency ];

	$price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $price, $currency );

	if ($currency == 'TZS') {
		$price = round($price, -3);
	} elseif ($currency == 'KES') {
		$price = round($price, -2);
	} else {
		$price = round($price, 2);
	}

	return format_price_to_conform($price, $currency);
}

function convert_price_noformat($price, $currency) {
	global $woocommerce_wpml;

	$currency_options = $woocommerce_wpml->settings['currency_options'][ $currency ];

	$price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $price, $currency );

	if ($currency == 'TZS') {
	$price = round($price, -3);
	} elseif ($currency == 'KES') {
		$price = round($price, -2);
	} else {
		$price = $price;
	}
	return $price;
}

function format_price_to_conform( $price, $currency ) {

	global $woocommerce_wpml;
	//$product          = wc_get_product( $product_id );
	$currency_options = $woocommerce_wpml->settings['currency_options'][ $currency ];
	//$should_round     = $currency_options['rounding'];

	$currency_symbol = get_woocommerce_currency_symbol( $currency );

	$decimals           = $currency_options['num_decimals'];
	$decimal_separator  = $currency_options['decimal_sep'];
	$thousand_separator = $currency_options['thousand_sep'];
	$currency_position  = $currency_options['position'];

	$left  = '';
	$right = '';
	switch ( $currency_position ) {
		case 'left':
			$left = $currency_symbol;
			break;
		case 'right':
			$right = $currency_symbol;
			break;
		case 'left_space':
			$left = $currency_symbol . ' ';
			break;
		case 'right_space':
			$right = ' ' . $currency_symbol;
			break;
	}

	empty( $price ) ? $price = 0 : $price;
	$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );
	$price = $left . $price . $right;

	return $price;


}


#add_action( 'save_post', 'save_post_callback' );
// function save_post_callback( $post_id ) {
// 	global $post;
// 	if ( $post->post_type == 'product' ) {
//
//
// 		return;
// 	}
// 	//if you get here then it's your post type so do your thing....
// }

function door_builder_unique_key_and_and_item_unique( $cart_item_data, $product_id ) {


	if ( get_field( 'door_builder_item', $product_id )) {
		if ( isset( $_COOKIE["built_door"] ) ) {
			$cart_item_data['door_builder'] = $_COOKIE["built_door"];
		}
	} else {
		setcookie( "built_door", "", time() - 60, "/" );
	}
	$unique_cart_item_key         = md5( microtime() . rand() );
	$cart_item_data['unique_key'] = $unique_cart_item_key;

	return $cart_item_data;
}

//add_filter( 'woocommerce_add_cart_item_data', 'door_builder_unique_key_and_and_item_unique', 10, 2 );


function addShippingToOrder( $order, $shipping_amount, $shipping_name ) {
	//$shipping_tax = array();
	//$shipping_rate = new WC_Shipping_Rate( '', $shipping_name,
	//$shipping_amount, $shipping_tax,
	//'custom_shipping_method' );
	//$order->add_shipping($shipping_rate);

}

add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta_after_order', 10, 3 );
function add_order_item_meta_after_order( $item_id, $cart_item_data ) {
	if ( ! empty( $cart_item_data['door_builder'] ) ) {
		wc_add_order_item_meta( $item_id, 'door_builder', $cart_item_data['door_builder'], true );
	}
}

// SHIPPING ZONES AND PRICES
add_filter( 'woocommerce_states', 'custom_woocommerce_states' );
function custom_woocommerce_states( $states ) {

	$states['KE'] = array(
    'KE1' => 'Kenya 1',
    'KE2' => 'Kenya 2'
  );

  return $states;
}

add_filter( 'woocommerce_package_rates', 'custom_delivery_flat_rate_cost_calculation', 10, 2 );
function custom_delivery_flat_rate_cost_calculation( $rates, $package )
{

	$items = WC()->cart->get_cart_contents_count();

	$shipping_zone = WC_Shipping_Zones::get_zone_matching_package( $package );
  $zone=$shipping_zone->get_zone_name();

  foreach($rates as $rate_key => $rate_values){
      $method_id = $rate_values->method_id;
      $rate_id = $rate_values->id;

      if ( 'flat_rate' === $method_id ) {

				if( have_rows('shipping_prices', 'shop_default') ):
				   while ( have_rows('shipping_prices', 'shop_default') ) : the_row();

							if ($zone == get_sub_field('zone_name')) {

								if( have_rows('prices') ):
								   while ( have_rows('prices') ) : the_row();
									 	if ($items >= get_sub_field('min_limit') && $items <= get_sub_field('max_limit')) {
											$rates[$rate_id]->cost = get_sub_field('price');

										}
								 	endwhile;
							 	endif;

							}
				   endwhile;
				endif;

      }
  }
  return $rates;
}

function get_doorbuilder_fields() {
	return array(
		'doorset_color' => 'Color',
		'doorset_design' => 'Door design',
		'doorset_width' => 'Width',
		'doorset_height' => 'Height',
		'doorset_thickness' => 'Doorset thickness',
		'doorset_door_type' => 'Door type',
		'doorset_lock' => 'Lock',
		'doorset_lock_core' => 'Lock core',
		'doorset_hinge' => 'Hinge',
		'doorset_handle' => 'Handle',
		'doorset_key_hole_cover' => 'Key hole cover',
		'doorset_striking_plate' => 'Striking plate',
		'doorset_integrated_threshold' => 'Integrated threshold',
		'doorset_frame_width' => 'Wider frame',
		'doorset_frame_extension' => 'Frame extension',
		'doorset_install' => 'Doorset installment',
		'doorset_handedness' => 'Handedness',
		'doorset_final_price' => 'Price',
		'doorset_original_price' => 'Original price'
	);
}

function get_pricelist_fields() {
	return array(
		'doorset_color',
		'doorset_width',
		'doorset_height',
		'doorset_door_type',
		'doorset_frame_width',
		'doorset_install'
	);
}

add_action( 'woocommerce_add_cart_item_data', 'save_additional_data', 10, 2 );
function save_additional_data( $cart_item_data, $product_id ) {

	$fields = get_doorbuilder_fields();
	foreach($fields as $key => $field) {
		if( isset( $_REQUEST[$key] )) {
	    $cart_item_data[$key] = $_REQUEST[$key];
	    $cart_item_data['unique_key'] = md5( microtime().rand() );
			if ($key == 'doorset_final_price') {
				$cart_item_data[$key] = convert_price_noformat($_REQUEST[$key], 'USD');
		    $cart_item_data['unique_key'] = md5( microtime().rand() );
			}
	  }
	}

	// print_r( $_REQUEST['doorset_final_price'] );
  //
	// die();

	if( isset( $_REQUEST['doorset_professional_measure'] )) {
		$cart_item_data['doorset_professional_measure'] = $_REQUEST['doorset_professional_measure'];
		$cart_item_data['unique_key'] = md5( microtime().rand() );
	}

	// if( isset( $_REQUEST['doorset_final_price'] )) {
	// 	$cart_item_data['doorset_final_price'] = $_REQUEST['doorset_final_price'];
	// 	$cart_item_data['unique_key'] = md5( microtime().rand() );
	// }

  return $cart_item_data;
}

add_filter( 'woocommerce_get_item_data', 'render_meta_on_cart_and_checkout', 10, 2 );
function render_meta_on_cart_and_checkout( $cart_data, $cart_item = null ) {
  $custom_items = array();

  if( !empty( $cart_data ) ) {
      $custom_items = $cart_data;
  }

	if (is_doorset_product($cart_item['product_id'])) {
		$fields = get_doorbuilder_fields();
		$install_options = get_doorset_install_methods();
		foreach($fields as $key => $field) {
			if ($key == 'doorset_furnitur_set') {
				continue;
			}
			if ($key == 'doorset_lock' || $key == 'doorset_lock_core' || $key == 'doorset_hinge' || $key == 'doorset_handle' || $key == 'doorset_key_hole_cover' || $key == 'doorset_striking_plate') {
				$custom_items[] = array( "name" => $field, "value" => get_the_title($cart_item[$key]) );
			} else if (($key == 'doorset_frame_extension' || $key == 'doorset_integrated_threshold') && $cart_item[$key] == 1) {
				$custom_items[] = array("name" => $field, "value" => 'Yes');
			} else if ($key == 'doorset_install') {
				$custom_items[] = array("name" => $field, "value" => $install_options[$cart_item[$key]]);
			} else if (!empty ($cart_item[$key])) {
		  	$custom_items[] = array( "name" => $field, "value" => $cart_item[$key] );
		  }
		}
	}


	if (!empty ($cart_item['doorset_professional_measure'])) {
  	$custom_items[] = array( "name" => 'Professional measure', "value" => $cart_item['doorset_professional_measure'] );
  }

  return $custom_items;
}

add_action( 'woocommerce_add_order_item_meta', 'additional_info_order_meta_handler', 1, 3 );
function additional_info_order_meta_handler( $item_id, $values, $cart_item_key ) {

	$fields = get_doorbuilder_fields();
	foreach($fields as $key => $field) {
		if( isset( $values[$key] ) ) {
	      wc_add_order_item_meta( $item_id, $key, $values[$key] );
	  }
	}

}

// cancel order
function cancel_expired_order($order_id) {
	$order = wc_get_order($order_id);
	$order->update_status('cancelled', 'Check payment has expired!');

	// $orders = array();
	// $args = array(
	// 	'post_type' => 'shop_order',
	// );
  //
	// $the_query = new WP_Query($args);
	// if ( $the_query->have_posts() ) {
	// 	while ( $the_query->have_posts() ) {
	// 		$the_query->the_post();
	// 		$orders[] = get_the_ID();
	// 	}
	// 	wp_reset_postdata();
	// }

	//return $orders;
}


function add_custom_price( $cart_object ) {

	$pricelist = get_doorset_pricelist();

  foreach ( $cart_object->cart_contents as $key => $value ) {

		// print_r($value);
		// die();

		if ($value['product_id'] == '5433' | $value['product_id'] == '5434' | $value['product_id'] == '5435' | $value['product_id'] == '5436') {
			update_field('custom_description', 'Hey this is a test', $value['product_id']);
		}

		/* OVERRIDE PRICE IN CART */

		// if override price is already set on product then show it in cart instead of real price
		// if ( $value['override_price'] > 0 && $value['override_price'] !== NULL ) {
		//  $value['data']->set_price( $value['override_price'] );
	 	// }
		//
		// // if the override price is being changed in cart
		// if ( isset( $_POST['price-input'] ) ) {
		//
		// 	// $custom_price = $_POST['price-input-'.$value['product_id']];
		//
		// 	// $woocommerce->cart->remove_cart_item('f43812e96373decb9693c1bb366df356');
		//
		// 	// add_post_meta( $value['product_id'], 'override_price', $custom_price );
		// 	wc_add_order_item_meta( $value['product_id'], 'override_price', $custom_price);
		//
		// 	// $value['override_price'] = $custom_price;
		// 	// $value['data']->set_price( $custom_price );
		// 	continue;
		//
		// 	// or if it isn't being changed in the cart but it already exists then show it in cart and skip function
		// } elseif ( !isset( $_POST['price-input-'.$value['product_id']] ) && $value['override_price'] > 0 || !isset( $_POST['price-input-'.$value['product_id']] ) && $value['override_price'] !== NULL ) {
		// 	continue;
		// }
		/* END OF OVERRIDE PRICE IN CART */

		if (is_doorset_product($value['product_id'])) {
			$calculated_price = 0;

			foreach($pricelist as $k => $field) {
		  	if (!empty($value[$k]) && $k != 'doorset_width' && $k != 'doorset_height') {
		  		$calculated_price += $pricelist[$k][$value[$k]];
					// print $k . ' -  ' . $pricelist[$k][$value[$k]] . '<br>';
		  	}
			}

			// width and height
			$custom = false;

			if (!empty($value['doorset_width'])) {
				$calculated_price += get_width_price($value['doorset_width']);
				// print 'Doorset Width -  ' . get_width_price($value['doorset_width']) . '<br>';

				if (!in_array($value['doorset_width'], get_standard_measures()['width'] )) {
					$custom = true;
				}

			}
			if (!empty($value['doorset_height'])) {
				$calculated_price += get_height_price($value['doorset_height']);
				// print 'Doorset Height -  ' . get_height_price($value['doorset_height']) . '<br>';


				if (!in_array($value['doorset_height'], get_standard_measures()['height'] )) {
					$custom = true;
				}

			}

			if ($value['doorset_lock'] == 'Cylinder Key Lock – Patent Grande') {
				$calculated_price += $pricelist['doorset_furnitur_set']['FS2'];
				// print 'FS2 ' . $pricelist['doorset_furnitur_set']['FS2'] . '<br>';
			} else if ($value['doorset_lock'] == 'Cart') {
				$calculated_price += $pricelist['doorset_furnitur_set']['FS1'];
				// print 'FS1 ' . $pricelist['doorset_furnitur_set']['FS1'] . '<br>';
			}

			// in case of custom measures
			if ($custom) {
				$calculated_price += get_field('custom_cut_price', 'shop_default');
			}

			// extras
			if ($value['doorset_integrated_threshold']) {

				$calculated_price += get_post_meta( get_field('integrated_threshold_product','shop_default'), '_regular_price', true );
				// print 'Integrated threshold ' . get_post_meta( get_field('integrated_threshold_product','shop_default'), '_regular_price', true ) . '<br>';
			}
			if (!empty($value['doorset_frame_extension'])) {
			 	$calculated_price += $pricelist['doorset_extras']['frame_extension'];
				// print 'Frame extension ' . $pricelist['doorset_extras']['frame_extension'] . '<br>';
			}

			// print 'Total: '.$calculated_price.'<br>';
			// die();

			// if ( is_admin() && ! defined( 'DOING_AJAX' ) )
      //   return;

			// CHANGE DOORSET NAME BY COLOUR IN ORDER
			$chosen_color = $value['doorset_color'];

			$colors = get_field('doorset_color', 'options');

			if (isset($colors)) {
				foreach ($colors as $color) {
					$option = $color['option'];
					$name = $color['name'];
				}
			}

			$currency = get_woocommerce_currency();
			global $woocommerce_wpml;


			$final_price = $calculated_price;

			$value['doorset_final_price'] = $final_price;
			$value['doorset_original_price'] = $final_price;
			// wc_add_order_item_meta($value['data']->get_id(), 'doorset_final_price', $final_price, true);
			// print $value['doorset_final_price'];
			// update_post_meta('doorset_final_price', $final_price, $value['product_id']);
			// print "Yo" . get_post_meta($value['product_id'], 'doorset_final_price', true);

			$final_price = convert_price_noformat($final_price, $currency);
			// print $final_price;
			// print $value['doorset_final_price'];

			if ($currency == 'TZS') {
			$final_price = round($final_price, -3);
			} elseif ($currency == 'KES') {
				$final_price = round($final_price, -2);
			} else {
				$final_price = round($final_price, 2);
			}

			if ( $value['somemeta']->override_price !== NULL && !empty($value['somemeta']->override_price)  ) {
				$value['data']->set_price(convert_price_noformat($value['somemeta']->override_price, $currency));
			} elseif ($value['somemeta']->override_price == NULL && $value['override_price'] !== NULL) {
				$value['data']->set_price(convert_price_noformat($value['override_price'], $currency));
			} else {
				$value['data']->set_price($final_price);
			}

			if (empty($value['somemeta']->override_price) && empty($value['override_price'])) {
				$value['data']->set_price($final_price);
			}

			$wc_product = $value['data'];

			$color = get_color_name($value['doorset_color']);
			$height = $value['doorset_height'];
			$width = $value['doorset_width'];
			$material = $value['doorset_door_type'];
			$type = $value['doorset_design'];

			if ($type == 'vented_glass') {
				$type_name = 'Vented glass';
			} elseif ($type == 'vented_solid') {
				$type_name = 'Vented solid';
			} elseif ($type == 'standard') {
				$type_name = 'Standard';
			}

			if (isset($color)) {
				$new_name = $height.'x'.$width.' '.$type_name.' '.$material.' '.$color.' doorset';
			} else {
				$new_name = 'Doorset';
			}

			if ( method_exists( $wc_product, 'set_name' ) ) {
      	$wc_product->set_name( $new_name );
      } else {
      	$wc_product->post->post_title = $new_name;
			}

		// IF MAGIC DOOR
		} else {

			if ( $value['product_id'] !== 5433 && $value['product_id'] !== 5434 && $value['product_id'] !== 5435 && $value['product_id'] !== 5436 ) {
				if (ICL_LANGUAGE_CODE == 'swa' | ICL_LANGUAGE_CODE == 'tz-usd') {
					$price = get_field('price_tanzania', $value['product_id']);

					if ( $value['somemeta']->override_price !== NULL && !empty($value['somemeta']->override_price)) {
						$value['data']->set_price($value['somemeta']->override_price);
					} elseif ($value['somemeta']->override_price == NULL && $value['override_price'] !== NULL) {
						$value['data']->set_price($value['override_price']);
					} else {
						$value['data']->set_price($price);
					}

				}
			}

		}

  }

}
add_action( 'woocommerce_calculate_totals', 'add_custom_price', 99 );

function get_custom_price ( $product ) {
	$currency = get_woocommerce_currency();
	if (ICL_LANGUAGE_CODE == 'swa' | ICL_LANGUAGE_CODE == 'tz-usd') {
		$price = get_field('price_tanzania', $value['product_id']);
	} else {
		$price = $product->get_regular_price();
	}
	return convert_price(include_taxes($price), $currency);
}

function get_color_name($color) {
	if ($color == 'LD46') {
		$color_name = 'Cherry';
	} elseif ($color == 'LM52') {
		$color_name = 'Dark Lady';
	} elseif ($color == 'LG69') {
		$color_name = 'Marbella';
	} elseif ($color == 'LK48') {
		$color_name = 'Noce Leuca';
	} elseif ($color == 'LK84') {
		$color_name = 'Oregon Pine';
	} elseif ($color == 'LD30') {
		$color_name = 'Walnut';
	} elseif ($color == '03H3') {
		$color_name = 'Wenge';
	}
	return $color_name;
}


function convert_final_price_to_usd( $cart_item_data, $product_id ) {
		global $woocommerce_wpml;
		$chosen_currency = get_woocommerce_currency();

		if ($chosen_currency == 'USD') {
			$currency_options = $woocommerce_wpml->settings['currency_options'][$chosen_currency];

			$cart_item_data['doorset_final_price'] = $cart_item_data['doorset_final_price'];
			return $cart_item_data;
		}

		if ($chosen_currency !== 'USD') {

		$currency = 'USD';

		$currency_options = $woocommerce_wpml->settings['currency_options'][$chosen_currency];

    $final = $cart_item_data['doorset_final_price'];

		$newfinal = $final / $currency_options['rate'];

		$cart_item_data['doorset_final_price'] = $newfinal;
		return $cart_item_data;
		}
}

add_filter( 'woocommerce_add_cart_item_data', 'convert_final_price_to_usd', 10, 3 );

// function check_if_threshold() {
// 	$items = $woocommerce->cart->get_cart();
//
//         foreach($items as $item => $values) {
//             $_product =  wc_get_product( $values['data']->get_id());
//             $product_name = $_product->get_title();
// 						if ($product_name.indexOf('integrated') < -1) {
// 							echo "Yay!";
// 						}
// 						$regular_price = get_post_meta($values['product_id'] , '_regular_price', true);
//         }
//
// }
//
// add_action( 'woocommerce_before_calculate_totals', 'check_if_threshold', 99);

acf_add_options_page(array(
    'page_title' => 'Shop defaults',
    'menu_title' => 'Shop defaults',
    'post_id' => 'shop_default'
));

function get_products() {
	$product_ids = array();
	if( have_rows('sets', 'shop_default') ):
	  while ( have_rows('sets', 'shop_default') ) : the_row();

	    array_push( $product_ids, get_sub_field('lock') );
	    array_push( $product_ids, get_sub_field('lock_core') );
	    array_push( $product_ids, get_sub_field('hinge') );

	  endwhile;
	endif;

	$args = array(
	  'post_type' => 'product',
		'post__in' => $product_ids,
	  'posts_per_page' => -1,

);

	return get_posts( $args );
}


function get_doorset_designs() {

	if (ICL_LANGUAGE_CODE == 'swa' | ICL_LANGUAGE_CODE == 'tz-usd') {
		$tz = '_tz';
	} else {
		$tz = '';
	}

	$designs = array();
	if( have_rows('doorset_designs', 'shop_default') ):
	 while ( have_rows('doorset_designs', 'shop_default') ) : the_row();
		 $designs[get_sub_field('type')] = get_sub_field('price'.$tz);
	 endwhile;
	endif;

	return $designs;
}

function get_doorset_install_methods() {
	$options = array();
	 if( have_rows('doorset_install', 'shop_default') ):
		while ( have_rows('doorset_install', 'shop_default') ) : the_row();
			$options[get_sub_field('option')] = get_sub_field('text');
		endwhile;
	 endif;

	return $options;

}


function get_door_types() {
	$types = array();
	 if( have_rows('doorset_door_type', 'shop_default') ):
		while ( have_rows('doorset_door_type', 'shop_default') ) : the_row();
			$types[get_sub_field('option')] = get_sub_field('code');
		endwhile;
	 endif;

	return $types;

}

// ACF FIELDS
// load door types in default product edit view
function acf_load_doorset_default_choices( $field ) {

	if ($field['name'] == 'doorset_furniture_set_default') {
	  // reset choices
	  $field['choices'] = array();

	  if( have_rows('sets', 'shop_default') ) {
	    while( have_rows('sets', 'shop_default') ) {

        the_row();

        $value = get_sub_field('code');
        $label = get_sub_field('name');

        $field['choices'][ $value ] = $label;
	     }
	  }
	} else {
		$select_field = str_replace('_default', '', $field['name']);
	  $field['choices'] = array();

	  if( have_rows($select_field, 'shop_default') ) {
	      while( have_rows($select_field, 'shop_default') ) {

	          the_row();

	          $value = get_sub_field('option');
	          $label = get_sub_field('option');

	          $field['choices'][ $value ] = $label;
	      }
	  }
	}

  return $field;

}

add_filter('acf/load_field/name=doorset_color_default', 'acf_load_doorset_default_choices');
add_filter('acf/load_field/name=doorset_door_type_default', 'acf_load_doorset_default_choices');
add_filter('acf/load_field/name=doorset_furniture_set_default', 'acf_load_doorset_default_choices');


add_filter('acf/load_field/name=pattern_frame_color', 'my_acf_load_colors');
function my_acf_load_colors( $field ) {

  $field['choices'] = array();

  if( have_rows('doorset_color', 'shop_default') ) {

      while( have_rows('doorset_color', 'shop_default') ) {

        the_row();

        $value = get_sub_field('option');
        $label = get_sub_field('option');

        $field['choices'][ $value ] = $label;
      }
  }

  return $field;

}

add_filter('acf/load_field/name=doorset_design', 'acf_load_door_designs');
function acf_load_door_designs( $field ) {

  $field['choices'] = array();
	foreach(get_doorset_designs() as $key => $value) {
	  $field['choices'][ $key ] = $key;
	}
  return $field;

}

function get_doorset_frame_colors() {
	$result = array();
	if( have_rows('frame_designs', 'shop_default') ):
	   while ( have_rows('frame_designs', 'shop_default') ) : the_row();
	    $result[get_sub_field('pattern_frame_color')] = array(
	      'pattern_frame_corner' => get_sub_field('pattern_frame_corner'),
	      'pattern_frame_top' => get_sub_field('pattern_frame_top'),
	      'pattern_frame_side' => get_sub_field('pattern_frame_side')
	    );
	 endwhile;
	endif;

	return $result;
}


function get_standard_measures() {
  return array(
    'height' => array(2100,2400),
    'width' => array(700,750,800,850,900)
  );
}

function get_furnitur_sets($set = null) {
	$available_furnitures = array();
	if( have_rows('sets', 'shop_default') ):
		while ( have_rows('sets', 'shop_default') ) : the_row();
			$available_furnitures[get_sub_field('code')]['lock'] = get_sub_field('lock');
			$available_furnitures[get_sub_field('code')]['lock_core'] = get_sub_field('lock_core');
			$available_furnitures[get_sub_field('code')]['hinge'] = get_sub_field('hinge');
			$available_furnitures[get_sub_field('code')]['handle'] = get_sub_field('handle');
			$available_furnitures[get_sub_field('code')]['key_hole_cover'] = get_sub_field('key_hole_cover');
			$available_furnitures[get_sub_field('code')]['striking_plate'] = get_sub_field('striking_plate');
			$available_furnitures[get_sub_field('code')]['image'] = get_sub_field('image');
		endwhile;
	endif;
	if ($set) {
		return $available_furnitures[$set];
	}
	return $available_furnitures;
}

function get_category_descriptions() {
	$descriptions = array();
	if( have_rows('description', 'option') ):
		while ( have_rows('description', 'option') ) : the_row();
			$descriptions[get_sub_field('category')] = get_sub_field('text');

		endwhile;
	endif;

	return $descriptions;
}

function get_width_price($width) {

	if ( ICL_LANGUAGE_CODE == 'swa' | ICL_LANGUAGE_CODE == 'tz-usd' ) {
		$tz = '_tz';
	} else {
		$tz = '';
	}

	$custom = false;
	if (!in_array($width, get_standard_measures()['width'] )) {
		$custom = true;
	}

	$width_prices = array();
	if( have_rows('doorset_width', 'shop_default') ):
	  while ( have_rows('doorset_width', 'shop_default') ) : the_row();
			$price = get_sub_field('price'.$tz);
			if ($custom) {
				$price *= 1.2;
			}
			$width_prices[get_sub_field('option')] = $price;
	  endwhile;
	endif;

	if ($width < 731) {
		$sheet = 700;
	} else if ($width >= 731 && $width < 780) {
		$sheet = 750;
	} else if ($width >= 781 && $width < 831) {
		$sheet = 800;
	} else if ($width >= 831 && $width < 881) {
		$sheet = 850;
	} else if ($width >= 881) {
		$sheet = 900;
	}
	return $width_prices[$sheet];
}

function get_height_price($height) {

	if ( ICL_LANGUAGE_CODE == 'swa' | ICL_LANGUAGE_CODE == 'tz-usd' ) {
		$tz = '_tz';
	} else {
		$tz = '';
	}

	$custom = false;
	if (!in_array($height, get_standard_measures()['height'] )) {
		$custom = true;
	}

	$height_prices = array();
	if( have_rows('doorset_height', 'shop_default') ):
	  while ( have_rows('doorset_height', 'shop_default') ) : the_row();
			$price = get_sub_field('price'.$tz);
			if ($custom) {
				$price *= 1.2;
			}
			$height_prices[get_sub_field('option')] = $price;
	  endwhile;
	endif;

	$sheet = 2100;
	if ($height > 2100) {
		$sheet = 2400;
	}

	return $height_prices[$sheet];
}

function get_doorset_pricelist() {

	$pricelist = array();

	// kui kasutaja on tzs-usd või tzs
	if (ICL_LANGUAGE_CODE == 'swa' | ICL_LANGUAGE_CODE == 'tz-usd') {
		$tz = '_tz';
	} else {
		$tz = '';
	}

	// print($tz);
	// die();

	// doorset main
	foreach(get_pricelist_fields() as $field) {

		if( have_rows($field, 'shop_default') ):
			while ( have_rows($field, 'shop_default') ) : the_row();
				$pricelist[$field][get_sub_field('option')] = get_sub_field('price'.$tz);
			endwhile;
		endif;
	}

	// doorset design
	$designs = get_doorset_designs();

	foreach($designs as $key => $design) {

		$pricelist['doorset_design'][$key] = $design;

	}

	// doorset extras
	$extras = get_field('doorset_extras', 'shop_default');
	if (isset($extras)) {
		foreach ($extras as $key => $value) {
			$pricelist['doorset_extras'][$key] = $value;
		}
	}

	// furnitur sets and subproducts
	$parts = array('lock', 'lock_core', 'hinge', 'handle', 'key_hole_cover', 'striking_plate');
	if( have_rows('sets', 'shop_default') ):
		while ( have_rows('sets', 'shop_default') ) : the_row();
			$set_price = 0;
			foreach($parts as $part) {
				$part_price = get_sub_field($part.'_price'.$tz);
				$pricelist['doorset_'.$part][get_sub_field($part)] = $part_price;
				$set_price += $part_price;

			}
			$pricelist['doorset_furnitur_set'][get_sub_field('code')] = $set_price;

		endwhile;
	endif;

	return $pricelist;
}


function get_product_price_range($prices, $currency) {

	$result = array();

	if (isset($prices['doorset_color'])) {
		foreach($prices['doorset_color'] as $color => $price) {
			$minimal_price = $maximum_price = $price;
			foreach ( $prices as $key => $category ) {
				if ($key == 'doorset_color' || $key == 'doorset_furnitur_set' || $key == 'doorset_design') {
					continue;
				}

				$child_prices = array();

				foreach ( $category as $part ) {
					$child_prices[] = $part;
				}

				$minimal_price += min( $child_prices );
				$maximum_price += max( $child_prices );
			}

			$minimal_price = $minimal_price - 105.56; // TEMPORARY FIX!!!

			// $minimal_price = convert_price($minimal_price, $currency);
			// $maximum_price = convert_price($maximum_price, $currency);

			global $woocommerce_wpml;

			$minimal_price = $woocommerce_wpml->multi_currency->prices->convert_price_amount($minimal_price, $currency);
			$maximum_price = $woocommerce_wpml->multi_currency->prices->convert_price_amount($maximum_price, $currency);

			if ($currency == 'TZS') {
			$minimal_price = round($minimal_price, -3);
			$maximum_price = round($maximum_price, -3);
			} elseif ($currency == 'KES') {
				$minimal_price = round($minimal_price, -2);
				$maximum_price = round($maximum_price, -2);
			} else {
				$minimal_price = $minimal_price;
				$maximum_price = $maximum_price;
			}

			$minimal_price = format_price_to_conform($minimal_price, $currency);
			$maximum_price = format_price_to_conform($maximum_price, $currency);

			$result[$color] =  '<span class="dark_price_letters">' . __( 'from', '64door' ) . '</span> ' . $minimal_price. ' <span class="dark_price_letters"> ' . __( 'to ', '64door' ) . '</span> '.$maximum_price;
		}
	}


	return $result;
}

add_action( 'woocommerce_thankyou', 'gen_next_order_nr' );
function gen_next_order_nr($order_id) {
	// generate order number
	get_next_order_nr($order_id);
}
//   $order = wc_get_order( $order_id );
//   if (!$order) {
//     return;
//   }
//   $door_types = get_door_types();
//   $standard_measures = get_standard_measures();
//
//   foreach ($order->get_items() as $item_key => $item_values) {
//
// 		$_product = $item_values->get_product();
//
// 		if (!is_doorset_product($_product->get_id())) {
// 			continue;
// 		}
//
// 		$item_id = $item_values->get_id();
//     $height = wc_get_order_item_meta( $item_id, 'doorset_height' );
//     $width = wc_get_order_item_meta( $item_id, 'doorset_width' );
//     $color = wc_get_order_item_meta( $item_id, 'doorset_color' );
//     $door_type = wc_get_order_item_meta( $item_id, 'doorset_door_type' );
// 		$door_design = wc_get_order_item_meta( $item_id, 'doorset_design');
// 		$frame_width = wc_get_order_item_meta( $item_id, 'doorset_frame_width');
// 		$frame_ext = wc_get_order_item_meta( $item_id, 'doorset_frame_extension');
// 		$install = wc_get_order_item_meta( $item_id, 'doorset_install');
//
//
// 		$sheet = '';
// 		if ($width < 731) {
// 			$sheet = 605;
// 		} else if ($width >= 731 && $width < 780) {
// 			$sheet = 655;
// 		} else if ($width >= 781 && $width < 831) {
// 			$sheet = 705;
// 		} else if ($width >= 831 && $width < 881) {
// 			$sheet = 755;
// 		} else if ($width >= 881) {
// 			$sheet = 805;
// 		}
//
// 		$design_code = 'S';
// 		if (get_field('windowed_frame', $door_design)) {
// 			$design_code = 'V';
// 		}
//
// 		$panel_sku = array();
// 		$panel_sku[] = $design_code;
// 		$panel_sku[] = $door_types[$door_type];
// 		$panel_sku[] = $color;
// 		$panel_sku[] = $sheet;
// 		$panel_sku[] = $frame_width;
// 		$panel_sku[] = $install;
// 		if ($frame_ext) {
// 			$frame_ex = '-EX';
// 			$frame_width = '180';
// 		} else {
// 			$frame_ex = '';
// 		}
// 		if (!in_array($height, $standard_measures['height']) || !in_array($width, $standard_measures['width'])) {
// 			$custom = '-C';
// 		} else {
// 			$custom = '';
// 		}
//
// 		$doorset_sku = $design_code.'-'.$door_types[$door_type].'-'.$color.'-'.$sheet.'-'.$frame_width.'-'.$install.''.$frame_ex.''.$custom;
//
// 		#if (!wc_get_order_item_meta( $item_id, 'doorset_panel_sku' )) {
// 			wc_add_order_item_meta( $item_id, 'doorset_panel_sku', $doorset_sku);
// 		#}
//
//
//
//   }
// 	// generate order number
// 	//get_next_order_nr($order_id);
//
// 	// send ok order to directo
// 	if (get_post_status($order_id) == 'wc-processing') {
// 		send_order_data_to_directo($order_id);
// 	}
//
// }

function send_order_data_to_directo($order_id) {
	$order = wc_get_order( $order_id );
  if (!$order) {
    return;
  }

  update_post_meta($order_id, 'data_sent_to_directo', 'yes');

}

//add_filter( 'woocommerce_order_number', 'change_woocommerce_order_number' );
function change_woocommerce_order_number( $order_id ) {
    $prefix = '70';
    $new_order_id = $prefix . get_post_meta($order_id, 'order_number', true);
    return $new_order_id;
}

function get_next_order_nr($order_id) {

	global $wpdb;
	$sql = "SELECT (meta_value + 1) FROM 64d_postmeta where meta_key = 'order_number' order by meta_value desc limit 1";
	$next_ordernr = $wpdb->get_var( $sql);

	add_post_meta($order_id, 'order_number', $next_ordernr);
	return $next_ordernr;


}

function is_doorset_product($id) {
	return  (get_field('door_builder_item_type', $id)) ? true : false;
}

function is_magic_door($id) {
	$terms = get_the_terms( $id, 'product_cat' );
	if ($terms[0]->term_id == 516 | $terms[0]->term_id == 517 | $terms[0]->term_id == 518 | $terms[0]->term_id == 519) {
		return true;
	} else {
		return false;
	}
}

// ADD PIN AND VAT FIELD IN CHECKOUT

// Hook in
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields( $fields ) {
     $fields['shipping']['shipping_pin'] = array(
    'label'     => __('PIN', 'woocommerce'),
    'placeholder'   => _x('PIN code', 'placeholder', 'woocommerce'),
    'required'  => true,
    'class'     => array('form-row-first'),
    'clear'     => true
     );

		 $fields['shipping']['shipping_vat'] = array(
    'label'     => __('VAT', 'woocommerce'),
    'placeholder'   => _x('VAT code', 'placeholder', 'woocommerce'),
    'required'  => false,
    'class'     => array('form-row-last'),
    'clear'     => true
     );

     return $fields;
}

/**
 * Display field value on the order edit page
 */

add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

function my_custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('PIN from Checkout form').':</strong> ' . get_post_meta( $order->get_id(), '_shipping_pin', true ) . '</p>';
		echo '<p><strong>'.__('VAT from Checkout form').':</strong> ' . get_post_meta( $order->get_id(), '_shipping_vat', true ) . '</p>';
}

// ----- BILLING -----

// Hook in
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_billing_fields' );

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_billing_fields( $fields ) {
     $fields['billing']['billing_pin'] = array(
    'label'     => __('PIN', 'woocommerce'),
    'placeholder'   => _x('PIN code', 'placeholder', 'woocommerce'),
    'required'  => true,
    'class'     => array('form-row-first'),
    'clear'     => true
     );

		 $fields['billing']['billing_vat'] = array(
    'label'     => __('VAT', 'woocommerce'),
    'placeholder'   => _x('VAT code', 'placeholder', 'woocommerce'),
    'required'  => false,
    'class'     => array('form-row-last'),
    'clear'     => true
     );

     return $fields;
}

/**
 * Display field value on the order edit page
 */

add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_billing_field_display_admin_order_meta', 10, 1 );

function my_custom_checkout_billing_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('PIN from Checkout form').':</strong> ' . get_post_meta( $order->get_id(), '_billing_pin', true ) . '</p>';
		echo '<p><strong>'.__('VAT from Checkout form').':</strong> ' . get_post_meta( $order->get_id(), '_billing_vat', true ) . '</p>';
}

// MAKE STATE/COUNTY OPTIONAL

// Hook in
add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );

// Our hooked in function - $address_fields is passed via the filter!
function custom_override_default_address_fields( $address_fields ) {
     $address_fields['state']['required'] = false;

     return $address_fields;
}

// KUI COUNTRY ON MISSING

add_filter( 'woocommerce_countries',  'add_country_missing' );
function add_country_missing( $countries ) {
  $new_countries = array(
	                    'Missing'  => __( 'Country missing? Contact us.', 'woocommerce' ),
	                    );

	return array_merge( $countries, $new_countries );
}

$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if (strpos($url, 'error=countries-dont-match') !==false) {
    echo "<div class='woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout'><div class='woocommerce-error'>Shipping and billing countries don't match!</div></div>";
}

/**
 * CHECK IF COUNTRIES ARE SAME
 */
add_action('woocommerce_checkout_process', 'countries_match');

function countries_match() {
    // Check if set, if its not set add an error.
    if ( $_POST['billing_country'] !== $_POST['shipping_country'] )
        wc_add_notice( __( 'Shipping and billing countries have to match.' ), 'error' );
}

// CUSTOM BUTTON FOR ORDERS

/**
 * Add a custom action to order actions select box on edit order page
 * Only added for paid orders that haven't fired this action yet
 *
 * @param array $actions order actions array to display
 * @return array - updated actions
 */
function sv_wc_add_order_meta_box_action( $actions ) {
    global $theorder;

		$order_id = $theorder->get_id();

		$paid_currency = get_paid_currency( $order_id );

		// bail if the order has been paid for or this action has been run
    if ( ! $theorder->has_status( array( 'pending' ) ) || get_post_meta( $theorder->id, '_wc_order_marked_sent_to_directo', true ) || $paid_currency == NULL) {
        return $actions;
    }

    // add "mark printed" custom action
    $actions['wc_custom_order_action'] = __( 'Send order to Directo', 'my-textdomain' );
    return $actions;
}
add_action( 'woocommerce_order_actions', 'sv_wc_add_order_meta_box_action' );

// function change_currency( $order ) {
//
// 	$new_currency = 'EUR';
// 	$current_currency = $order->get_currency();
// 	echo $current_currency;
//
// 	// $order->set_currency($new_currency);
//
// }
//
// add_action('woocommerce_process_shop_order_meta', 'change_currency');
//

function get_paid_currency( $order_id ) {
	$paid_currency = get_field('order_currency', $order_id);
	return $paid_currency;
}

function action_woocommerce_process_shop_order_meta( $order_id ) {
	global $post;
    if ($post->post_type != 'shop_order') { // if post type is order
        return;
    }
	$order = wc_get_order( $order_id );
	$order_data = $order->get_data();

	global $woocommerce_wpml;

	// $currency_options = $woocommerce_wpml->settings['currency_options'][ $currency ];

	// $currency = $order->get_currency();
	// $currency_symbol = get_woocommerce_currency_symbol( $currency );

	// $total = $order->get_total();
	// $newtotal = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $total, $currency );

	// $order->set_total($newtotal);

	$paid_currency = get_field('order_currency', $order_id);
	$order->add_order_note("Order paid in currency: ".$paid_currency."", 0, true);
	return $paid_currency;
};

// add the action
add_action( 'save_post', 'action_woocommerce_process_shop_order_meta', 10, 2 );

// function change_currency_in_admin( $order_id ) {
// 	$order = wc_get_order( $order_id );
//
// 	$order_data = $order->get_data();
// 	// $order_billing_country = $order_data['billing']['country'];
//
// 	// define currency of country
// 	// if ($order_billing_country == "KE") {
// 	// 	$order_currency_new = "KES";
// 	// } elseif ($order_billing_country == "TZ") {
// 	// 	$order_currency_new = "TZS";
// 	// }
//
// 	$order_currency_new = 'KES';
// 	$order->add_order_note("Order billing country currency: ".$order_currency_new."", 0, true);
// 	// $order->set_currency( $order_currency_new );
// 	// $order->set_total(0);
// 	// print_r('<pre>');
// 	// print_r($order);
// 	// print_r('<pre>');
// 	// die();
// }
//
// add_action( 'woocommerce_before_pay_action', 'change_currency_in_admin', 10, 2);

// woocommerce_pay_order_before_submit
// woocommerce_before_checkout_process
// woocommerce_before_pay_action
// before_woocommerce_pay
// wc_before_products_ending_sales

add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

function new_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options -> Reading
  // Return the number of products you wanna show per page.
  $cols = 18;
  return $cols;
}

add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );

function custom_pre_get_posts_query( $q ) {

    $q->set( 'tax_query', array(array(
       'taxonomy' => 'product_cat',
       'field' => 'slug',
       'terms' => array( 'dimensions' ),
       'operator' => 'NOT IN'
    )));

}

add_filter('woocommerce_currency_symbol', 'change_existing_currency_symbol', 10, 2);

function change_existing_currency_symbol( $currency_symbol, $currency ) {
     switch( $currency ) {
          case 'KES': $currency_symbol = 'KSh'; break;
      case 'TZS': $currency_symbol = 'TSh'; break;
     }
     return $currency_symbol;
}


// // POPULATE DOORSET COLOR CODE FIELDS
// function acf_populate_color_code( $field ) {
//   // Reset choices
//   $field['doorset_color_code'] = array();
//
//   // Get field from options page
//   $countries_and_areas = get_field('countries_and_areas', 'options');
//
//   // Get only countries in array
//   foreach ($countries_and_areas as $key => $value) {
//     $countries[] = $value['country'];
//   }
//
//   // Sort countries alphabetically
//   natsort( $countries );
//
//   // Populate choices
//   foreach( $countries as $choice ) {
//     $field['choices'][ $choice ] = $choice;
//   }
//
//   // Return choices
//   return $field;
//
// }
// // Populate select field using filter
// add_filter('acf/load_field/key=field_52b1b7007bfa4', 'acf_populate_color_code');

function include_taxes( $price ) {
	// woocommerce_price($product->get_price_including_tax());
	$currency = get_woocommerce_currency();

	$ke_vat = 1.16; // 16% VAT
	$tz_vat = 1.18; // 18% VAT

	if ( $currency == 'KES ' ) {
		$price = $price * $ke_vat;
	} elseif ( $currency == 'TZS' ) {
		$price = $price * $tz_vat;
	} else {
		$price *= $ke_vat;
	}
	return $price;
}

function filter_woocommerce_calc_tax( $taxes, $price, $rates, $price_includes_tax, $suppress_rounding )
{
    $current_lang = apply_filters( 'wpml_current_language', NULL );
    if ($current_lang == 'ke-usd') {
        foreach ($taxes as $key => $tax){
            $vat = 16 /100;
            $taxes[$key]= $price * $vat;
        }
    } elseif ($current_lang == 'ke') {
			foreach ($taxes as $key => $tax){
					$vat = 16 /100;
					$taxes[$key]= $price * $vat;
			}
		} elseif ($current_lang == 'tz-usd') {
			foreach ($taxes as $key => $tax){
					$vat = 18 /100;
					$taxes[$key]= $price * $vat;
			}
		} elseif ($current_lang == 'TZS') {
			foreach ($taxes as $key => $tax){
					$vat = 18 /100;
					$taxes[$key]= $price * $vat;
			}
		}
    return $taxes;
};
add_filter( 'woocommerce_calc_tax', 'filter_woocommerce_calc_tax', 10, 5 );

add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
		if ( '' !== $query->get( 'facetwp' ) ) {
				$is_main_query = (bool) $query->get( 'facetwp' );
		}
		return $is_main_query;
}, 10, 2 );

add_filter( 'facetwp_index_row', function( $params, $class ) {
	if ( 'example' == $params['facet_name'] ) {
			$excluded_terms = array( 'Magic', 'Uncategorized' );
			if ( in_array( $params['facet_display_value'], $excluded_terms ) ) {
					return false;
			}
	}
	return $params;
}, 10, 2 );

function fwp_home_custom_query( $query ) {
	global $post;
    if ( $query->is_main_query() && is_shop() && !is_admin() | $query->is_main_query() && is_shop() && !current_user_can('sales_representative') && !current_user_can('administrator')) {
        $query->set( 'post_type', array( 'product' ) );
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'desc' );
				$query->set( 'posts_per_page', 18 );
				$tax_query = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => array('magic', 'uncategorized'),
                'operator' => 'NOT IN',
            ),
        );
        $query->set( 'tax_query', $tax_query );
    }
}
add_filter( 'pre_get_posts', 'fwp_home_custom_query' );



// MAGIC DOOR FUNCTIONALITY

// validate and sanitize when adding to cart
function magic_door_add_to_cart($passed, $product_id, $qty){

    if( isset( $_POST['magic_door_desc'] ) && sanitize_text_field( $_POST['magic_door_desc'] ) == '' ){
        $product = wc_get_product( $product_id );
        wc_add_notice( sprintf( '%s cannot be added to the cart until you enter custom description.', $product->get_title() ), 'error' );
        return false;
    }

    return $passed;

}
add_filter( 'woocommerce_add_to_cart_validation', 'magic_door_add_to_cart', 10, 3 );

// add the custom data to the cart item
function magic_door_add_cart_item_data( $cart_item, $product_id ){

    if( isset( $_POST['magic_door_desc'] ) ) {
        $cart_item['magic_door_desc'] = sanitize_text_field( $_POST['magic_door_desc'] );
    }

		if( isset( $_POST['magic_door_title'] ) ) {
        $cart_item['magic_door_title'] = sanitize_text_field( $_POST['magic_door_title'] );
    }

		if( isset( $_POST['magic_door_price'] ) ) {
				$cart_item['magic_door_price'] = $_POST['magic_door_price'];
		}

    return $cart_item;

}
add_filter( 'woocommerce_add_cart_item_data', 'magic_door_add_cart_item_data', 10, 2 );


// add the custom data to the cart item
function magic_door_get_cart_item_from_session( $cart_item, $values ) {

		global $woocommerce;
		$wc_product = $cart_item['data'];

		$currency = get_woocommerce_currency();
		global $woocommerce_wpml;

    if ( isset( $values['magic_door_desc'] ) ){
        $cart_item['magic_door_desc'] = $values['magic_door_desc'];
    }
		if ( isset( $values['magic_door_title'] ) ){
			$cart_item['magic_door_title'] = $values['magic_door_title'];
			if ( method_exists( $wc_product, 'set_name' ) ) {
      	$wc_product->set_name( $values['magic_door_title'] );
      } else {
      	$wc_product->post->post_title = $values['magic_door_title'];
			}
    }
		if ( isset( $values['magic_door_price'] ) ){
			$cart_item['magic_door_price'] = $values['magic_door_price'];
			$cart_item['data']->set_price(convert_price_noformat($values['magic_door_price'], $currency));
		}

    return $cart_item;

}
add_filter( 'woocommerce_get_cart_item_from_session', 'magic_door_get_cart_item_from_session', 20, 2 );

// save the custom data on checkout
function magic_door_add_order_item_meta( $item_id, $values ) {

		if ( ! empty( $values['magic_door_title'] ) ) {
				woocommerce_add_order_item_meta( $item_id, 'magic_door_title', $values['magic_door_title'] );
		}

    if ( ! empty( $values['magic_door_desc'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'magic_door_desc', $values['magic_door_desc'] );
    }

		if ( ! empty( $values['magic_door_price'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'magic_door_price', $values['magic_door_price'] );
    }
}
add_action( 'woocommerce_add_order_item_meta', 'magic_door_add_order_item_meta', 10, 2 );


// display info
function magic_door_get_item_data( $other_data, $cart_item ) {

    if ( isset( $cart_item['magic_door_desc'] ) ){

        $other_data[] = array(
            'name' => 'Custom Description',
            'value' => sanitize_text_field( $cart_item['magic_door_desc'] )
        );

    }

    return $other_data;

}
add_filter( 'woocommerce_get_item_data', 'magic_door_get_item_data', 10, 2 );

// show item data on order page
function magic_door_order_item_product( $cart_item, $order_item ){

    if( isset( $order_item['magic_door_desc'] ) ){
        $cart_item_meta['magic_door_desc'] = $order_item['magic_door_desc'];
    }

    return $cart_item;

}
add_filter( 'woocommerce_order_item_product', 'magic_door_order_item_product', 10, 2 );


function magic_door_email_order_meta_fields( $fields ) {
    $fields['magic_door_desc'] = 'Magic Door Description';
    return $fields;
}
add_filter('woocommerce_email_order_meta_fields', 'magic_door_email_order_meta_fields');


function magic_door_order_again_cart_item_data( $cart_item, $order_item, $order ){

    if( isset( $order_item['magic_door_desc'] ) ){
        $cart_item_meta['magic_door_desc'] = $order_item['magic_door_desc'];
    }

    return $cart_item;

}
add_filter( 'woocommerce_order_again_cart_item_data', 'magic_door_order_again_cart_item_data', 10, 3 );

// add_action('woocommerce_add_to_cart', 'custome_add_to_cart');


// function custome_add_to_cart() {
//     global $woocommerce;
//
//     $product_id = $_POST['magic_door_desc'];
//
//     $found = false;
//
//     //check if product already in cart
//     if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
//         foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
//             $_product = $values['data'];
//             if ( $_product->id == $product_id )
//                 $found = true;
//         }
//         // if product not found, add it
//         if ( ! $found )
//             WC()->cart->add_to_cart( $product_id );
//     } else {
//         // if no products in cart, add it
//         WC()->cart->add_to_cart( $product_id );
//     }
// }

// //Change cart item price
// function add_custom_price_callback( $cart_object ) {
//
// 	foreach ( $cart_object->cart_contents as $value ) {
//
// 		$custom_price = intval($_POST['price-input']);
// 		$value['data']->price = $custom_price;
//
// 	}
// 	die();
//
// }

// global $woocommerce;
//     $items = $woocommerce->cart->get_cart();
//
//         foreach($items as $item => $values) {
//             $_product =  wc_get_product( $values['data']->get_id());
//             echo "<b>".$_product->get_title().'</b>  <br> Quantity: '.$values['quantity'].'<br>';
//             $price = get_post_meta($values['product_id'] , '_price', true);
//             echo "  Price: ".$price."<br>";
//         }

function Update_Cart_Metadata(){

	if (current_user_can('sales_representative') | current_user_can('administrator')) {
		global $wpdb;
		$current_user            = wp_get_current_user();
		$current_user_roles      = $current_user->roles;
		$current_user_id         = $current_user->ID;

		$personal_discount        = get_field( 'user_maximum_discount', 'user_' . $current_user_id );

		if ( $personal_discount != null) {
			$maximum_discount = $personal_discount;
		}	else if ( current_user_can('administrator') ) {
			$maximum_discount = get_field( 'admins_max_discount', 'option' );
		} elseif ( current_user_can('sales_representative') ) {
			$maximum_discount = get_field( 'salespersons_max_discount', 'option' );
		}

		$discount_allowed = true;
		if ( $sales_discount !=null ) {
			if ( $maximum_discount != null ) {
				if ( $sales_discount > $maximum_discount ) {
					$discount_allowed = false;
				} elseif ( $maximum_discount = null ) {
						$discount_allowed = false;
				}
			}
		}


		global $woocommerce;
		$cart = $woocommerce->cart->cart_contents;
		$updt = Array();
		foreach ($_POST['k'] AS $item){
		    $product = new stdClass();
		    $updtCL = new stdClass();
		    $product->{'id'} = $item['p'];

				$product->{'override_price'} = $item['m']; // This is metadata

		    $updtCL->{'krtkey'} = $item['k']; // This is product key in cart
		    $updtCL->{'meta'} = $product;
		    $updt[] = $updtCL;
		}

		// cycle the cart replace the meta of the correspondant key
		foreach ($cart as $key => $item) {
		    foreach($updt as $updtitem){
		        if($key == $updtitem->krtkey) { // if this kart item corresponds with the received, the meta data is updated
		            // Update the content of the kart
		            $woocommerce->cart->cart_contents[$key]['somemeta'] = $updtitem->meta;
		        }
		    }

		}

		// This is the magic: With this function, the modified object gets saved.
		$woocommerce->cart->set_session();

		$woocommerce->cart->calculate_totals();

		wp_die('{"e":"ok", "Updt": "'.count($arrupdt).'"}');
	}
}
add_action('wp_ajax_nopriv_Update_Cart_Metadata', 'Update_Cart_Metadata');
add_action('wp_ajax_Update_Cart_Metadata', 'Update_Cart_Metadata');

add_filter( 'woocommerce_get_cart_item_from_session', function ( $cartItemData, $cartItemSessionData, $cartItemKey ) {
    if ( isset( $cartItemSessionData['override_price'] ) ) {
        $cartItemData['override_price'] = $cartItemSessionData['override_price'];
    }

    return $cartItemData;
}, 10, 3 );

function add_override_price() {
	if (is_checkout()) {
		global $woocommerce;
		$currency = get_woocommerce_currency();
		$cart_contents = $woocommerce->cart->get_cart();

		foreach ( $cart_contents as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$current_price = $_product->get_price();
			// print $current_price;
			if ( $cart_item['somemeta']->override_price !== NULL && !empty($cart_item['somemeta']->override_price)) {
				$price = convert_price_noformat($cart_item['somemeta']->override_price, $currency);
				$_product->set_price($price);
				// WC()->cart->calculate_totals();
			} elseif ( $cart_item['somemeta']->override_price == NULL && $cart_item['override_price'] !== NULL) {
				$price = convert_price_noformat($cart_item['override_price'], $currency);
				$_product->set_price($price);
				// WC()->cart->calculate_totals();
			} else {
				if (is_doorset_product($_product->get_id())) {
					$_product->set_price($cart_item['doorset_final_price']);
				} else {
					$_product->set_price($current_price);
				}
			}
		}
	}
}
add_action('woocommerce_before_calculate_totals', 'add_override_price', 10, 1 );

add_action( 'woocommerce_thankyou', 'my_custom_tracking' );
function my_custom_tracking( $order_id ) { ?>
	<script>
		jQuery(document).ready(function() {

			jQuery('.see_details').each(function() {
				jQuery(this).click(function() {
					jQuery(this).text(function(i, text){
						return text === "Less details ⮝" ? "More details ⮟" : "Less details ⮝";
					})
					jQuery(this).next('.variation').toggleClass('show');
				});
			});
		});
	</script>
<?php }
