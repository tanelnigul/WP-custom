<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//wc_print_notices();

// array structure: Main_array(Door(Door_components(1, 2, ...n),how_many(i)), Door(Door_components(1, 2, ...n),how_many(i)))


$cart_door_builder_hash = filter_var( $_REQUEST["remove_door"], FILTER_SANITIZE_STRING );
if ( ! empty( $cart_door_builder_hash ) ) {
	foreach ( WC()->cart->get_cart() as $key => $item ) {
		$item_id = $item['product_id'];
		if ( $cart_door_builder_hash == $item['door_builder'] ) {
			WC()->cart->remove_cart_item( $key );
		}
	}
}

$currency = get_woocommerce_currency();

do_action( 'woocommerce_before_cart' );

?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
<!-- Removed wiht new cart layout @joonas
		<thead>
        <tr>
            <th class="product-remove">&nbsp;</th>
            <th class="product-thumbnail">&nbsp;</th>
            <th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-price"><?php _e( 'Price w/o VAT', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
        </tr>
        </thead>
-->
        <tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		$door_set      = [];
		$cart_contents = WC()->cart->get_cart();
		foreach ( $cart_contents as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			$add_door_set = 'false';

			if ( ! empty( $cart_item['door_builder'] ) ) {
				$the_door_set_hash = $cart_item['door_builder'];
				//echo $cart_item['door_builder'];
				//$door_set[ $the_door_set_hash ][] = $cart_item['product_id'];
				$door_set[ $the_door_set_hash ][] = $cart_item;
				?>
				<?php
				if ( get_field( 'door_builder_item_type', $cart_item['product_id'] ) != 'measuring' || true ) {
					?>
                    <div class="hidden-inputs <?php echo $cart_item['door_builder']; ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
						} else {
							$product_quantity = woocommerce_quantity_input( array(
								'input_name'  => "cart[{$cart_item_key}][qty]",
								'input_value' => $cart_item['quantity'],
								'max_value'   => $_product->get_max_purchase_quantity(),
								'min_value'   => '0',
							), $_product, false );
						}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
						?>
                    </div>
				<?php } ?>
				<?php


				if ( $cart_item != end( $cart_contents ) ):
					continue;
				endif;
			}
			?>
			<?php if ( ! empty( $door_set ) ) {
				$price             = 0;
				$quantity          = 1;
				$name              = '';
				$cart_item_ids     = '';
				$door_builder_hash = '';

				foreach ( $door_set as $door_set_single ) {
					?>
                    <tr>
						<?php

						foreach ( $door_set_single as $door_set_part ) {
							$door_builder_hash    = $door_set_part['door_builder'];
							$product_door_builder = apply_filters( 'woocommerce_cart_item_product', $door_set_part ['data'], $door_set_part );
							$this_price           = $product_door_builder->get_price();
							$this_quantity        = $door_set_part['quantity'];
							$price                += $this_price * $this_quantity;
							$name                 .= $product_door_builder->get_name() . ': ' . format_price_to_conform( $this_price, $currency ) . '<strong> x ' . $this_quantity . '</strong><br>';
							$cart_item_ids        .= $product_door_builder->get_id() . ';';

							if ( get_field( 'door_builder_item_type', $door_set_part['product_id'] ) != 'measuring' ) {
								$quantity = $this_quantity;
							}

							?>

							<?php
							if ( $door_set_part == end( $door_set_single ) ) {
								$door_set     = [];
								$add_door_set = 'true';
								continue;
							}
						}
						$total = $price;
						?>
                        <td class="product-remove">
                            <a href="<?php echo get_permalink() ?>?remove_door=<?php echo $door_builder_hash; ?>"
                               class="custom-remove door-builder-remove"
                               data-door_builder_hash="<?php echo $door_builder_hash; ?>"
                               data-builder_ids="<?php echo $cart_item_ids; ?>">×</a>
                        </td>

                        <td class="product-thumbnail">
                        </td>

                        <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
                            <div class="cart_door_set_name">
                                <div class="small-12"><?php _e( 'Door set', '64door' ); ?></div>
                                <div class="small-push-2 small-10"><?php echo $name ?></div>

                            </div>
                        </td>

												<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
													<?php echo format_price_to_conform( $price, $currency ); ?>
                        </td>

												<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
                            <input class="cart_input door_builder_quantity"
                                   data-door_builder_hash="<?php echo $door_builder_hash; ?>" type="number" step="1"
                                   min="0" max=""
                                   value="<?php echo $quantity ?>" title="Qty" size="4" pattern="[0-9]*"
                                   inputmode="numeric">
                        </td>

												<td class="product-subtotal" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
							<?php echo format_price_to_conform( $total, $currency ); ?>
                        </td>

                        <td class="product-subtotal" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
							<?php echo format_price_to_conform( $total, $currency ); ?>
                        </td>

						<?php
						$name  = '';
						$price = 50;
						$total = 0;
						?>
                    </tr>
					<?php
				}
			}

			if ( empty( $cart_item['door_builder'] ) ) {
				$add_door_set = 'false';
			}

			//print_r( $door_set );


			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				if ( $add_door_set == 'true' ) {
					$add_door_set = 'false';
					continue;
				}

				?>

				<tr class="table-subhead table-subhead-primary">
				    <th class="product-remove">&nbsp;</th>
				    <th class="product-thumbnail">&nbsp;</th>
				    <th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
					<th class="product-price"><?php _e( 'Price w/o VAT', 'woocommerce' ); ?> <a class="enableOverride"><i class="fas fa-pen-square"></i></a></th>
					<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
				</tr>

                <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>" data-product_id="<?php echo $_product->get_id(); ?>" data-key="<?php echo $cart_item_key; ?>">

                    <td rowspan="3" class="product-remove">
						<?php
						echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
							'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
							esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
							__( 'Remove this item', 'woocommerce' ),
							esc_attr( $product_id ),
							esc_attr( $_product->get_sku() )
						), $cart_item_key );
						?>
                    </td>

                    <td rowspan="3" class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo $thumbnail;
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
						}
						?>
                    </td>

                    <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;';
						} else {
							echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key );
						}

						// Meta data
						echo WC()->cart->get_item_data( $cart_item );

						// Backorder notification
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
						}
						?>
                    </td>

					<!-- START OF PRICE INPUT LOGIC // OVERRIDE PRICE LOGIC -->

					<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">

						<?php

						// if neither override prices are set
						if ($cart_item['somemeta']->override_price == NULL && $cart_item['override_price'] == NULL) {
							if (is_magic_door($_product->get_id())) {
								$_product->set_price(convert_price_noformat($cart_item['magic_door_price'], $currency));
							} elseif (is_doorset_product($_product->get_id())) {
								$_product->set_price(convert_price_noformat($cart_item['doorset_final_price'], $currency));
							} else {
								$_product->set_price($_product->get_regular_price());
							}

							// if somemeta override is not set but link sharing override is
						} elseif ($cart_item['somemeta']->override_price == NULL && $cart_item['override_price'] !== NULL) {
							$override = $cart_item['override_price'];
							$override = convert_price_noformat($override, $currency);
							$_product->set_price($override);

							// if both override prices are set
						} elseif ($cart_item['somemeta']->override_price !== NULL && $cart_item['override_price'] !== NULL) {
							$override = $cart_item['somemeta']->override_price;
							$override = convert_price_noformat($override, $currency);
							$_product->set_price($override);

							// if somemeta override is set but link sharing override is not
						} elseif ($cart_item['somemeta']->override_price !== NULL && $cart_item['override_price'] == NULL) {
							$override = $cart_item['somemeta']->override_price;
							$override = convert_price_noformat($override, $currency);
							$_product->set_price($override);
						}

						// WC()->cart->set_session();
						var_dump($cart_item);
						// die();
						?>

						<?php if ( current_user_can('administrator') && (ICL_LANGUAGE_CODE == 'ke-usd') || current_user_can('sales_representative') && (ICL_LANGUAGE_CODE == 'ke-usd') ) { ?>

							<div class="override hidden">

								<p class="cart_original">Original price:
									<?php if (is_doorset_product($_product->get_id())) {
										echo convert_price($cart_item['doorset_final_price'], $currency);
									} elseif (is_magic_door($_product->get_id())) {
										echo convert_price($cart_item['magic_door_price'], $currency);
									} else {
										echo convert_price($_product->get_regular_price(), $currency);
									} ?></p>

								<p class="cart_discount">Discount:
									<?php if (is_doorset_product($_product->get_id())) {
										if ( !empty($cart_item['somemeta']->override_price) ) {
											echo convert_price($cart_item['doorset_final_price'] - $cart_item['somemeta']->override_price, $currency);
										} elseif ( !empty($cart_item['doorset_final_price']) ) {
											echo convert_price($cart_item['magic_door_price'] - $cart_item['override_price'], $currency);
										}
									} elseif (is_magic_door($_product->get_id())) {
										if ( !empty($cart_item['somemeta']->override_price) ) {
											echo convert_price($cart_item['magic_door_price'] - $cart_item['somemeta']->override_price, $currency);
										} elseif ( !empty($cart_item['override_price']) ) {
											echo convert_price($cart_item['magic_door_price'] - $cart_item['override_price'], $currency);
										} else {
											echo convert_price(0, $currency);
										}
									} else {
										if ( !empty($cart_item['somemeta']->override_price) ) {
											echo convert_price($_product->get_regular_price() - $cart_item['somemeta']->override_price, $currency);
										} elseif ( !empty($cart_item['override_price']) ) {
											echo convert_price($_product->get_regular_price() - $cart_item['override_price'], $currency);
										}
										else {
										 echo convert_price(0, $currency);
									 }
									} ?></p>

								<input class="cart_input override" name="price-input" type="text" value="<?php echo $_product->get_price(); ?>"/>
							</div>

							<div class="not-hidden">
								<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
							</div>

						<?php } else { ?>
							<p class="cart_original">Original price:
								<?php if (is_doorset_product($_product->get_id())) {
									echo convert_price($cart_item['doorset_final_price'], $currency);
								} elseif (is_magic_door($_product->get_id())) {
									echo convert_price($cart_item['magic_door_price'], $currency);
								} else {
									echo format_price_to_conform($_product->get_regular_price(), $currency);
								} ?></p>

							<p class="cart_discount">Discount:
								<?php if (is_doorset_product($_product->get_id())) {
									if ( !empty($cart_item['somemeta']->override_price) ) {
										echo convert_price($cart_item['doorset_final_price'] - $cart_item['somemeta']->override_price, $currency);
									} elseif ( !empty($cart_item['doorset_final_price']) ) {
										echo convert_price($cart_item['magic_door_price'] - $cart_item['override_price'], $currency);
									}
								} elseif (is_magic_door($_product->get_id())) {
									if ( !empty($cart_item['somemeta']->override_price) ) {
										echo convert_price($cart_item['magic_door_price'] - $cart_item['somemeta']->override_price, $currency);
									} elseif ( !empty($cart_item['override_price']) ) {
										echo convert_price($cart_item['magic_door_price'] - $cart_item['override_price'], $currency);
									} else {
										echo convert_price(0, $currency);
									}
								} else {
									if ( !empty($cart_item['somemeta']->override_price) ) {
										echo format_price_to_conform($_product->get_regular_price() - convert_price_noformat($cart_item['somemeta']->override_price, $currency), $currency);
									} elseif ( !empty($cart_item['override_price']) ) {
										echo format_price_to_conform($_product->get_regular_price() - $cart_item['override_price'], $currency);
									} else {
										echo convert_price(0, $currency);
									}
								} ?></p>

							<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
							}

							?>

					</td>

					<!-- END OF START OF PRICE INPUT LOGIC // OVERRIDE PRICE LOGIC -->

					<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
						} else {
							$product_quantity = woocommerce_quantity_input( array(
								'input_name'  => "cart[{$cart_item_key}][qty]",
								'input_value' => $cart_item['quantity'],
								'max_value'   => $_product->get_max_purchase_quantity(),
								'min_value'   => '0',
							), $_product, false );
						}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
						?>
          </td>

        </tr>

				<tr class="table-subhead">
					<th class="product-price"><?php _e( 'Total w/o VAT', 'woocommerce' ); ?></th>
					<th class="product-price"><?php _e( 'VAT', 'woocommerce' ); ?></th>
            		<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
				</tr>

				<tr>
					<td class="product-subtotal" data-title="<?php esc_attr_e( 'Total w/o VAT', 'woocommerce' ); ?>">
						<?php
						echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
                    </td>

					<td class="product-subtotal" data-title="<?php esc_attr_e( 'VAT', 'woocommerce' ); ?>">
						<?php
						echo wc_price((( wc_get_price_including_tax( $_product ) * $cart_item['quantity']) - ( wc_get_price_excluding_tax( $_product ) * $cart_item['quantity'])), $cart_item, $cart_item_key );
						?>
                    </td>

                    <td class="product-subtotal" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
						<?php
						echo apply_filters( 'woocommerce_cart_item_subtotal', wc_price( wc_get_price_including_tax( $_product ) * $cart_item['quantity']), $cart_item, $cart_item_key );
						?>
                    </td>
				</tr>


				<?php
			}
		}
		?>

		<?php do_action( 'woocommerce_cart_contents' ); ?>

        <tr>
            <td colspan="12" class="actions">

				<?php if ( wc_coupons_enabled() ) { ?>
                    <div class="coupon">
                        <label for="coupon_code"><?php _e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text"
                                                                                                         name="coupon_code"
                                                                                                         class="input-text"
                                                                                                         id="coupon_code"
                                                                                                         value=""
                                                                                                         placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>"/>
                        <input type="submit" class="button" name="apply_coupon"
                               value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"/>
						<?php do_action( 'woocommerce_cart_coupon' ); ?>
                    </div>
				<?php } ?>

                <input type="submit" class="button" name="update_cart"
                       value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"/>

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
            </td>
        </tr>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
        </tbody>
    </table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<div class="cart-collaterals">
	<?php
	/**
	 * woocommerce_cart_collaterals hook.
	 *
	 * @hooked woocommerce_cross_sell_display
	 * @hooked woocommerce_cart_totals - 10
	 */
	do_action( 'woocommerce_cart_collaterals' );
	?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>

<!-- Generate link from cart contents and save them to DB and set hash -->
<?php
$current_user       = wp_get_current_user();
$current_user_roles = $current_user->roles;
?>


<section class="generate-link-from-cart-section">

	<?php
	if ( in_array( 'sales_representative', $current_user_roles ) || in_array( 'administrator', $current_user_roles ) ) {
		?>
        <h3><?php _e( 'Generate link to share with client', '64door' ) ?></h3>

        <div class="row">

            <div class="cart-link-generator-columns tooltip_container columns medium-6">


                <select name="customer_filter" id="customer_filter">
                    <option value="0"><?php _e( 'Select client', '64door' ) ?></option>
					<?php
					$sales_rep_clients = get_sales_rep_clients( get_current_user_id() );
					$client_ids        = [];
					foreach ( $sales_rep_clients as $sales_rep_client ) {
						array_push( $client_ids, $sales_rep_client->client_id );
					}
					foreach ( $client_ids as $client_id ):
						?>
                        <option value="<?php echo $client_id ?>"><?php
							$link_user = get_user_by( "ID", $client_id );
							echo $link_user->first_name . ' ' . $link_user->last_name;
							?> </option>
						<?php
					endforeach;
					?>
                </select>

            </div>
            <div class="cart-link-generator-columns tooltip_container columns medium-6">
                <div class="cart_feedback" id="customer_filter_feedback"></div>
            </div>
        </div>
        <div class="row">
            <div class="columns">
                <input type="checkbox" name="" id="add_customer_information" class="checkbox-custom">
                <label for="add_customer_information">Add customer information?</label>
            </div>
        </div>
        <section class="link_builder_billing_delivery">
            <div class="row">
                <div class="columns medium-6">
                    <h3><?php _e( 'Billing details', '64door' ) ?></h3>
                    <div class="row">
                        <div class="columns medium-6">
                            <label for="billing_first_name">First name</label>
                            <input type="text" id="billing_first_name">
                        </div>
                        <div class="columns medium-6">
                            <label for="billing_last_name">Last name</label>
                            <input type="text" id="billing_last_name">
                        </div>
                    </div>
                    <label for="billing_country">Billing country</label>
					<?php
					$countries = new WC_Countries();
					$countries = $countries->get_countries();
					?>
                    <select class="select2-hidden-accessible country_to_state country_select" id="billing_country">
                        <option value="">Select country</option>

						<?php
						foreach ( $countries as $code => $name ):
							?>
                            <option value="<?php echo $code; ?>"><?php echo $name; ?></option>
							<?php
						endforeach;
						?>
                    </select>
                    <label for="billing_street">Street address</label>
                    <input type="text" id="billing_street">
                    <label for="billing_apartment">Apartment</label>
                    <input type="text" id="billing_apartment">
                    <label for="billing_postcode">Postcode / Zip</label>
                    <input type="text" id="billing_postcode">
                    <label for="billing_city">Town / City</label>
                    <input type="text" id="billing_city">
                    <div class="row">
                        <div class="columns medium-6">
                            <label for="billing_phone">Phone</label>
                            <input type="text" id="billing_phone">
                        </div>
                        <div class="columns medium-6">
                            <label for="billing_email">E-mail address</label>
                            <input type="text" id="billing_email">
                        </div>
                    </div>
                </div>
                <div class="columns medium-6">
                    <h3><?php _e( 'Delivery address', '64door' ) ?></h3>
                    <div class="row">
                        <div class="columns medium-6">
                            <label for="shipping_first_name">First name</label>
                            <input type="text" id="shipping_first_name">
                        </div>
                        <div class="columns medium-6">
                            <label for="shipping_last_name">Last name</label>
                            <input type="text" id="shipping_last_name">
                        </div>
                    </div>
                    <label for="shipping_country">Shipping country</label>
					<?php
					$countries = new WC_Countries();
					$countries = $countries->get_shipping_countries();
					?>
                    <select class="select2-hidden-accessible country_to_state country_select" id="shipping_country">
                        <option value="">Select country</option>
						<?php
						foreach ( $countries as $code => $name ):
							?>
                            <option value="<?php echo $code; ?>"><?php echo $name; ?></option>
							<?php
						endforeach;
						?>
                    </select>
                    <label for="shipping_street">Street address</label>
                    <input type="text" id="shipping_street">
                    <label for="shipping_apartment">Apartment</label>
                    <input type="text" id="shipping_apartment">
                    <label for="shipping_postcode">Postcode / Zip</label>
                    <input type="text" id="shipping_postcode">
                    <label for="shipping_city">Town / City</label>
                    <input type="text" id="shipping_city">
                    <div class="row">
                        <div class="columns medium-6">
                            <label for="shipping_phone">Phone</label>
                            <input type="text" id="shipping_phone">
                        </div>
                        <div class="columns medium-6">
                            <label for="shipping_email">E-mail address</label>
                            <input type="text" id="shipping_email">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="row">
            <div class="cart-link-generator-columns tooltip_container  columns medium-6">
                <input type="number" id="discount_percentage" placeholder="Discount percentage">
								<!-- <label for="discount">per cart</label>
								<input class="discount" type="radio" name="discount_type" id="discount_type" value="per_cart" checked="checked">
								<label for="discount">per line</label>
								<input class="discount" type="radio" name="discount_type" id="discount_type" value="per_product"> -->
                <div class="tooltip_custom">
					<?php
					$percentage_amount = get_field( 'user_maximum_discount', 'user_' . get_current_user_id() );
					if ( $percentage_amount == null ) {
						$percentage_amount = 0;
					}
					?>
                    <span class="tooltiptext"><?php echo __( 'You can give a maximum of ', '64door' ) . $percentage_amount . __( '% discount', '64door' ); ?></span>
                </div>
            </div>
            <div class="cart-link-generator-columns tooltip_container columns medium-6">
                <div class="cart_feedback" id="discount_percentage_feedback"></div>
            </div>
        </div>
				<label for="generated-link-input"><h3>Your generated link:</h3></label>
		    <input id="generated-link-input" type="text" readonly>

		    <button class="button-styled generate-client-link">Generate</button>
		    <button class="button-styled send-client-offer">e-mail offer</button>


		</section>
		<?php
	}
