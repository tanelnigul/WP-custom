<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
?>

<div class="hidden-product-part">

  <form class="cart" method="post" enctype="multipart/form-data" id="professional_method">
    <input id="doorset_professional_measure" name="doorset_professional_measure" value="" type="hidden">
    <input id="doorset_professional_price" name="doorset_professional_price" value="" type="hidden">
    <div class="quantity">
      <input id="quantity" class="input-text qty text" name="quantity" value="1" type="hidden">
    </div>
    <button type="submit" name="add-to-cart" value="<?php the_field('professional_measure_product','shop_default'); ?>" class="real_add_to_cart button alt"></button>
  </form>
</div>

<?php

global $product;
global $woocommerce_wpml;

if ( ! $product->is_purchasable() ) {
  return;
}

// Generate frame colors js object
$frame_designs = get_doorset_frame_colors();
echo '<script>';
  echo 'var frame_colors = '.json_encode($frame_designs).';';
echo '</script>';


$currency = get_woocommerce_currency();
$pricelist = get_doorset_pricelist();
$lang = ICL_LANGUAGE_CODE;

if (ICL_LANGUAGE_CODE == 'swa' | ICL_LANGUAGE_CODE == 'tz-usd') {
  $tz = '_tz';
} else {
  $tz = '';
}

$standard_measures = get_standard_measures();
//  get_doorbuilder_fields();
$slides =array(
  'doorset_color' => 'Color',
  'doorset_measure_method' => 'Measuring method',
  'doorset_measures' => 'Measures',
  'doorset_design' => 'Design',
  'doorset_door_type' => 'Door type',
  'doorset_handedness' => 'Handedness',
  'doorset_furnitur_set' => 'Furnitur sets',
  'doorset_extras' => 'Extras',
  'doorset_install' => 'Installment',
  'doorset_summary' => 'Summary'
);
$cat_descriptions = get_category_descriptions();

while ( have_posts() ) : the_post();

$extras = get_field('doorset_extras', 'shop_default');

?>

<section class="door_builder_page single-product-custom" data-currency="<?php echo $lang; ?>">
  <div class="row single-product-custom-inner">

    <div class="columns product-image-custom medium-6">
        <div class="row">
          <div class="columns medium-centered medium-12">

            <div id="door" style="background: url('https://64door.com/wp-content/themes/64door/i/lk48-2.jpg');">
              <div id="line" class="arrow">
                <div class="text"></div>
              </div>
              <div id="line2" class="arrow">
                <div class="text"></div>
              </div>
              <div id="nolock"></div>
              <div id="lock">
                <img src="" alt="" >
              </div>
              <div id="frame-left"></div>
              <div id="frame-right"></div>
              <div id="frame-top"></div>
              <div id="frame-corner"></div>
              <div id="frame-corner-right"></div>
              <div id="frame-window" ></div>
              <div id="frame-window-bottom"></div>
            </div>
          </div>
        </div>
    </div>

    <div id="door_builder_options" class="columns product-builder-options medium-6">
      <!-- <div class="steps">
        <h3><span id="step_door"><?php _e( '', '64door' ) ?> </span><span id="step_name"></span></h3>
        <div class="step_counter"><?php _e( 'Step:', '64door' ) ?> <span id="current_step">1</span>/
          <span id="steps_amount"></span>
        </div>
      </div> -->

      <div class="errors"></div>

      <section class="product-builder-options-inner">
        <div class="doorbuilder_slider">

        <?php
        $counter = 1;
        foreach($slides as $slide_key => $slide) { ?>

          <div class="product_category_slide step_<?php echo $counter; ?>" data-slide_category="<?php echo $slide_key; ?>" data-slide_title="<?php echo $slide; ?>">
            <div class="row">
              <div class="single_door_addition_container columns">
                <div class="cat_description">
                  <?php if (isset($cat_descriptions[$slide_key])) { echo $cat_descriptions[$slide_key]; } ?>
                </div>
              <?php if ($slide_key == 'doorset_color') { ?>

                <?php
                $default_doorset_color = get_field($slide_key.'_default');
                if( have_rows('doorset_color', 'shop_default') ):
                  while ( have_rows('doorset_color', 'shop_default') ) : the_row(); ?>

                  <div class="add_to_image change-door
                  <?php if (get_sub_field('option') == $default_doorset_color) { echo " selected "; } ?>"
                    data-image="<?php the_sub_field('pattern'); ?>"
                    data-pattern_type="color" data-pattern_door="<?php the_sub_field('pattern'); ?>"
                    data-category_slug="doors"
                    data-image="" data-pattern_type="door"
                    data-price="<?php echo convert_price_noformat(get_sub_field('price'.$tz), $currency); ?>" data-category="<?php echo $slide_key; ?>"
                    data-usd="<?php echo get_sub_field('price'.$tz); ?>" data-category="<?php echo $slide_key; ?>"
                    data-color="<?php the_sub_field('option'); ?>">

                      <div class="row">
                          <div class="columns small-6 part_image_container">
                              <img src="<?php the_sub_field('pattern'); ?>" alt="">
                          </div>
                          <div class="columns small-6 part_info_container">
                              <div class="part_title"><?php the_sub_field('name'); ?></div>
                              <div class="part_price"><?php echo convert_price(get_sub_field('price'.$tz), $currency); ?></div>
                              <div class="door_builder_info_buttons">
                                  <button class="info-button" data-fancybox="" data-src="#door_info_<?php the_sub_field('option'); ?>"><img class="info-icon" src="https://64door.com/wp-content/uploads/2018/03/waouulbdwiwdidequulm.png">
                                  </button>
                                  <button class="custom_button_general">Select</button>
                              </div>

                              <div class="medium-6 door_builder_door_info" style="display: none;" id="door_info_<?php the_sub_field('option'); ?>">
                                <div class="row">
                                    <div class="columns">
                                        <h2><?php the_sub_field('name'); ?></h2>
                                        <p><?php the_sub_field('description'); ?></p>
                                    </div>
                                </div>
                                <div class="door_builder_lightbox_info_buttons_background">
                                    <div class="row">
                                        <div class="medium-12 medium-centered text-center">
                                            <div class="door_builder_lightbox_info_buttons">
                                                <button class="custom_button_general close_button" data-fancybox-close>
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              </div>

                            </div>
                          </div>
                    </div>
                <?php  endwhile;
                endif;
                ?>

               <?php } else if ($slide_key == 'doorset_design') { ?>
                 <?php
                 $default_doorset_design = get_field('doorset_design');
                 if( have_rows('doorset_designs', 'shop_default') ):
                  while ( have_rows('doorset_designs', 'shop_default') ) : the_row();
                 ?>

                   <div class="add_to_image change-frame wide design
                   <?php
                  if (get_sub_field('type') == $default_doorset_design) { echo " selected "; } ?>"
                   data-pattern_type="frame"
                   data-pattern_frame_windowed="<?php the_sub_field('type'); ?>"
                   data-pattern_frame_side=""
                   data-pattern_frame_top=""
                   data-pattern_frame_corner=""
                   data-price="<?php echo convert_price_noformat(get_sub_field('price'.$tz), $currency); ?>"
                   data-usd="<?php echo get_sub_field('price'.$tz); ?>"
                   data-category="<?php echo $slide_key; ?>"
                   data-value_id="<?php the_sub_field('code'); ?>"
                   >

                     <div class="row">
                       <div class="columns small-6 part_image_container wide">
                          <img src="<?php the_sub_field('image'); ?>" alt="<?php the_sub_field('name'); ?>">
                       </div>
                       <div class="columns small-6 part_info_container wide">
                         <div class="part_title wide design">
                           <?php the_sub_field('name'); ?>
                           <button class="popup-box warning" data-fancybox="" data-src="#door_design_info_<?php echo str_replace(' ','_', get_sub_field('name')); ?>">?</button>
                         </div>
                         <div class="part_price wide design"><?php echo convert_price(get_sub_field('price'.$tz), $currency); ?></div>
                         <div class="disabled_text required">Delivery might take up to 2 months</div>
                       </div>
                     </div>


                     <!-- layer info -->
                     <div class="medium-6 door_builder_door_design_info" style="display: none;" id="door_design_info_<?php echo str_replace(' ','_', get_sub_field('name')); ?>">
                       <div class="row">
                           <div class="columns">
                             <h2><?php the_sub_field('name') ?></h2>
                               <?php the_sub_field('disabled_text'); ?>
                           </div>
                       </div>
                       <div class="door_builder_lightbox_info_buttons_background">
                           <div class="row">
                               <div class="medium-12 medium-centered text-center">
                                   <div class="door_builder_lightbox_info_buttons">
                                       <button class="custom_button_general close_button" data-fancybox-close>
                                           Close
                                       </button>
                                   </div>
                               </div>
                           </div>
                       </div>
                     </div>

                   </div>

                 <?php  endwhile;
                 endif; ?>


              <?php } else if($slide_key == 'doorset_door_type') { ?>
                <?php
                $default_material_type = get_field($slide_key.'_default');
                if( have_rows('doorset_door_type', 'shop_default') ):
                   while ( have_rows('doorset_door_type', 'shop_default') ) : the_row(); ?>

                     <div class="add_to_image change-door_type wide
                     <?php if (get_sub_field('option') == $default_material_type) { echo " selected "; } ?>
                     " data-image="" data-dimension_measurement_method="self" data-id="<?php echo get_sub_field('option'); ?>" data-product_id=""
                     data-price="<?php echo convert_price_noformat(get_sub_field('price'.$tz), $currency); ?>" data-usd="<?php echo get_sub_field('price'.$tz); ?>" data-category="doorset_door_type" >
                         <div class="row">
                             <div class="columns small-12 part_info_container">
                                 <div class="part_title wide"><?php echo ucfirst(get_sub_field('option')); ?>
                                   <button class="popup-box warning" data-fancybox="" data-src="#door_type_info_<?php the_sub_field('option'); ?>">?</button>
                                 </div>
                                 <div class="part_price wide"><?php echo convert_price(get_sub_field('price'.$tz), $currency); ?></div>
                             </div>
                         </div>
                     </div>

                     <div class="medium-6 door_builder_door_type_info" style="display: none;" id="door_type_info_<?php the_sub_field('option'); ?>">
                       <div class="row">
                           <div class="columns">
                               <h2><?php echo ucfirst(get_sub_field('option')); ?></h2>
                               <div><?php the_sub_field('disabled_text'); ?></div>
                           </div>
                       </div>
                       <div class="door_builder_lightbox_info_buttons_background">
                           <div class="row">
                               <div class="medium-12 medium-centered text-center">
                                   <div class="door_builder_lightbox_info_buttons">
                                       <button class="custom_button_general close_button" data-fancybox-close>
                                           Close
                                       </button>
                                   </div>
                               </div>
                           </div>
                       </div>
                     </div>

                <?php
                   endwhile;
                endif;
                ?>
              <?php } else if ($slide_key == 'doorset_install') { ?>
                <?php

                if( have_rows('doorset_install', 'shop_default') ):
                  while ( have_rows('doorset_install', 'shop_default') ) : the_row();
                ?>
                <div class="add_to_image change-install wide" data-category="<?php echo $slide_key; ?>" data-id="<?php the_sub_field('option'); ?>" data-price="<?php echo convert_price_noformat(get_sub_field('price'.$tz), $currency); ?>" data-usd="<?php echo get_sub_field('price'.$tz); ?>">
                    <div class="row">
                        <div class="columns small-12 part_info_container wide">
                            <div class="part_title wide"><?php echo get_sub_field('text'); ?></div>
                            <div class="part_price wide"><?php echo convert_price(get_sub_field('price'.$tz), $currency); ?></div>
                        </div>
                    </div>
                </div>

                <?php
                  endwhile;
                endif;
                ?>


              <?php } else if ($slide_key == 'doorset_handedness') { ?>
                <?php
                $handedness = array(
                  'left' => 'Left handed',
                  'right' => 'Right handed'
                );

                foreach($handedness as $k => $v) { ?>

                  <div class="add_to_image change-handedness wide" data-id="<?php echo substr(strtoupper($k), 0, 1); ?>" data-category="<?php echo $slide_key; ?>" data-handedness="<?php echo $k; ?>_handed">
                      <div class="row">
                          <div class="columns small-12 part_info_container">
                              <div class="part_title wide"><?php echo $v; ?></div>
                          </div>
                      </div>
                  </div>

                <?php } ?>
              <?php } elseif ($slide_key == 'doorset_furnitur_set') { ?>
                <?php
                $default_furnitur_set = get_field('doorset_furniture_set_default');
                if( have_rows('sets', 'shop_default') ):
                  while ( have_rows('sets', 'shop_default') ) : the_row(); ?>
                    <div class="single_door_addition_container columns">
                      <div class="add_to_image change-furnitur_set wide
                      <?php
                        $furnitur_code = get_sub_field('code');
                        if ($furnitur_code == $default_furnitur_set ) { echo " selected "; }
                      ?>" data-image="" data-dimension_measurement_method="self" data-id="<?php the_sub_field('code'); ?>"
                        data-product_id="
                        <?php echo get_sub_field('lock').','.get_sub_field('lock_core').','.get_sub_field('hinge').','.get_sub_field('handle').','.get_sub_field('key_hole_cover').','.get_sub_field('striking_plate'); ?>"
                        data-price="<?php echo convert_price_noformat($pricelist['doorset_furnitur_set'][$furnitur_code], $currency); ?>" data-usd="<?php echo $pricelist['doorset_furnitur_set'][$furnitur_code]; ?>" data-category="furniture_set" >

                        <div class="row">
                          <div class="columns small-2 part_image_container furnitur">
                            <img src="<?php the_sub_field('image'); ?>" alt=""/>
                          </div>
                          <div class="columns small-4 furnitur">
                            <div class="part_title wide"><strong><?php echo get_sub_field('name'); ?></strong></div>
                            <div class="part_price wide"><?php echo convert_price($pricelist['doorset_furnitur_set'][$furnitur_code], $currency); ?></div>
                          </div>
                          <div class="columns small-12">
                            <ul class="furnitur left">
                              <li data-part="lock"><?php
                              if (get_sub_field('lock_label') !== "") {
                              echo get_sub_field('lock_label');
                              } else {
                                echo get_the_title(get_sub_field('lock'));
                              }  ?></li>
                              <li data-part="lock_core"><?php
                              if (get_sub_field('lock_core_label') !== "") {
                              echo get_sub_field('lock_core_label');
                              } else {
                                echo get_the_title(get_sub_field('lock_core'));
                              } ?></li>
                              <li data-part="hinge"><?php
                              if (get_sub_field('hinge_label') !== "") {
                              echo get_sub_field('hinge_label');
                              } else {
                                echo get_the_title(get_sub_field('hinge'));
                              } ?></li>
                            </ul>
                            <ul class="furnitur right">
                              <li data-part="handle"><?php
                              if (get_sub_field('handle_label') !== "") {
                              echo get_sub_field('handle_label');
                              } else {
                                echo get_the_title(get_sub_field('handle'));
                              } ?></li>
                              <li data-part="key_hole_cover"><?php
                              if (get_sub_field('key_hole_cover_label') !== "") {
                              echo get_sub_field('key_hole_cover_label');
                              } else {
                                echo get_the_title(get_sub_field('key_hole_cover'));
                              }
                              ?></li>
                              <li data-part="striking_plate"><?php
                              if (get_sub_field('striking_plate_label') !== "") {
                              echo get_sub_field('striking_plate_label');
                              } else {
                                echo get_the_title(get_sub_field('striking_plate'));
                              } ?></li>
                            </ul>
                          </div>
                        </div>

                      </div>
                    </div>

                <?php
                  endwhile;
                endif;
                ?>

              <?php } else if ($slide_key == 'doorset_extras') { ?>

                <?php
                $selected_furniture_set = get_field('doorset_furniture_set_default');
                $custom_furniture_set = get_furnitur_sets($selected_furniture_set);

                $products = get_products();
                $fsets = array();

                foreach($products as $prod) {
                  $fsets[get_the_terms( $prod->ID, 'product_cat' )[0]->slug][] = $prod;
                }
                ?>

                <div class="single_door_addition_container columns">
                  <div class="change-furniture">
                    <div class="row">
                      <div class="columns small-12 part_info_container">
                        <?php

                        foreach ($fsets as $k => $set) {
                          echo '<div class="part_title extras">Select '.get_term_by('slug', $k, 'product_cat')->name.'</div>';
                          echo '<ul class="door-builder-extra furnitur_set" data-category="'.$k.'">';
                          foreach($set as $key => $p) {
                            $slug = get_the_terms( $p->ID, 'product_cat' )[0]->slug;

                            $price = $pricelist['doorset_'.$slug][$p->ID];
                            echo '<li data-value_id='.$p->ID.' data-title="'.$p->post_title.'" data-price='.convert_price_noformat($price, $currency).' data-usd='.$price.' data-category="'.$slug.'"';
                            echo ' class="available ';
                            if ($custom_furniture_set[$slug] == $p->ID) {
                              echo ' selected ';
                            }
                            echo '">';
                            echo '<button class="popup-box info" data-src="#door_extra_product_'.$p->ID.'" data-fancybox="">!</button>';
                            echo '<img class="medium-6 extras" src="'.get_the_post_thumbnail_url( $p->ID, 'thumbnail').'" /><br>';

                            echo "<h5 class='extras title'>$p->post_title</h5><br><p class='extras price'>$$price</p>";
                            echo '</li>';

                            ?>
                            <div class="medium-6 door_builder_product_info" style="display: none;" id="door_extra_product_<?php echo $p->ID; ?>">
                              <div class="row">
                                  <div class="columns">
                                      <h1><?php echo $p->post_title; ?></h1>
                                      <div><?php echo get_post_field('post_content', $p->ID); ?></div>
                                  </div>
                              </div>
                              <div class="door_builder_lightbox_info_buttons_background">
                                  <div class="row">
                                      <div class="medium-12 medium-centered text-center">
                                          <div class="door_builder_lightbox_info_buttons">
                                              <button class="custom_button_general close_button" data-fancybox-close>
                                                  Close
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                            </div>



                          <?php }
                          echo '</ul>';
                        }

                        ?>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- other extras -->

                <div class="single_door_addition_container columns">
                    <div class="change-extra" data-category="extra">
                        <div class="row">
                            <div class="columns small-12 part_info_container">
                              <div class="part_title extras">Integrated threshold</div>
                              <?php
                                $threshold_product_id = get_field('integrated_threshold_product','shop_default');
                                $threshold_price = 22.29; ?>
                                <ul class="door-builder-extra" data-category="extra_int_threshold" data-product_id="<?php echo ''; ?>">
                                  <li class="available"
                                    data-category="integrated_threshold" data-value_id="0" data-price="0"><strong>No</strong>
                                    <br><?php echo convert_price(0, $currency); ?></li>
                                  <li class="available" data-title="Integrated threshold"
                                    data-category="integrated_threshold" data-value_id="1" data-price="<?php echo convert_price_noformat($threshold_price, $currency); ?>" data-usd="<?php echo $threshold_price; ?>">

                                    <img class="medium-6 extras" src="<?php echo get_the_post_thumbnail_url( $threshold_product_id, 'thumbnail'); ?>" /><br>

                                    <button class="popup-box info" data-src="#door_extra_product_<?php echo $threshold_product_id; ?>" data-fancybox="" tabindex="0">!</button>
                                    <?php echo convert_price($threshold_price, $currency); ?></li>

                                </ul>
                                <div class="medium-6 door_builder_product_info" style="display: none;" id="door_extra_product_<?php echo $threshold_product_id; ?>">
                                  <div class="row">
                                      <div class="columns">
                                          <h1><?php echo get_the_title($threshold_product_id); ?></h1>
                                          <div><?php echo get_post_field('post_content', $threshold_product_id); ?></div>
                                      </div>
                                  </div>
                                  <div class="door_builder_lightbox_info_buttons_background">
                                      <div class="row">
                                          <div class="medium-12 medium-centered text-center">
                                              <div class="door_builder_lightbox_info_buttons">
                                                  <button class="custom_button_general close_button" data-fancybox-close>
                                                      Close
                                                  </button>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single_door_addition_container columns">
                    <div class="change-extra">
                        <div class="row">
                            <div class="columns small-12 part_info_container">
                                <div class="part_title extras">Frame width
                                  <button class="popup-box warning" data-src="#door_extra_frame_width" data-fancybox>?</button>
                                </div>
                                <ul class="door-builder-extra" data-category="extra_wider_frame">
                                  <?php
                                  foreach($pricelist['doorset_frame_width'] as $key => $width) { ?>
                                    <li class="available <?php if ($key == 100) echo 'selected'; ?>"
                                    data-category="frame_width"  data-value_id="<?php echo $key; ?>" data-price="<?php echo convert_price_noformat($width, $currency); ?>" data-usd="<?php echo $width; ?>" data-title="Frame width: <?php echo $key; ?>">
                                    <?php echo '<strong>'.$key.'mm</strong>'; ?>
                                    <?php echo '<br>'.convert_price($width, $currency); ?></li>
                                  <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="medium-6 door_builder_door_info" style="display: none;" id="door_extra_frame_width">
                      <div class="row">
                          <div class="columns">
                              <h2>Frame width</h2>
                              <div class="doorbuilder_infobox"><?php the_field('frame_width_disabled_text','shop_default'); ?></div>
                          </div>
                      </div>
                      <div class="door_builder_lightbox_info_buttons_background">
                          <div class="row">
                              <div class="medium-12 medium-centered text-center">
                                  <div class="door_builder_lightbox_info_buttons">
                                      <button class="custom_button_general close_button" data-fancybox-close>
                                          Close
                                      </button>
                                  </div>
                              </div>
                          </div>
                      </div>
                    </div>

                </div>
                <div class="single_door_addition_container columns">
                    <div class="change-extra">
                        <div class="row">
                            <div class="columns small-12 part_info_container">
                                <div class="part_title extras">Frame extension
                                  <button class="popup-box warning" data-src="#door_extra_frame_extension" data-fancybox>?</button>
                                </div>
                                <ul class="door-builder-extra" data-category="extra_frame_extension">
                                  <li class="available"
                                    data-category="frame_extension" data-value_id="0" data-price="0"><strong>No</strong>
                                  <br><?php echo convert_price(0, $currency); ?></li>
                                  <li class="available" data-title="Frame extension"
                                    data-category="frame_extension" data-value_id="1" data-price="<?php echo convert_price_noformat($extras['frame_extension'], $currency); ?>" data-usd="<?php echo $extras['frame_extension']; ?>"><strong>Yes</strong>
                                    <br><?php echo convert_price($extras['frame_extension'], $currency); ?>

                                  </li>

                                </ul>

                            </div>
                        </div>
                    </div>

                    <div class="medium-6 door_builder_door_info" style="display: none;" id="door_extra_frame_extension">
                      <div class="row">
                          <div class="columns">
                            <h2>Frame extension</h2>
                            <div class="doorbuilder_infobox"><?php the_field('fex_disabled_text', 'shop_default'); ?></div>
                          </div>
                      </div>
                      <div class="door_builder_lightbox_info_buttons_background">
                          <div class="row">
                              <div class="medium-12 medium-centered text-center">
                                  <div class="door_builder_lightbox_info_buttons">
                                      <button class="custom_button_general close_button" data-fancybox-close>
                                          Close
                                      </button>
                                  </div>
                              </div>
                          </div>
                      </div>
                    </div>

                </div>

              <?php } else if ($slide_key == 'doorset_measure_method') { ?>
                <div class="single_door_addition_container columns">
                  <div class="add_to_image change-measuring wide" data-image="" data-dimension_measurement_method="professional"
                  data-product_id="" data-price="<?php echo convert_price_noformat(30, $currency); ?>" data-usd="<?php echo 30; ?>"
                  data-category="dimensions" data-category2="Dimensions">
                      <div class="row">
                          <div class="columns small-12 part_info_container">
                              <div class="part_title wide"><?php echo get_the_title(get_field('professional_measure_product','shop_default')); ?></div>
                              <div class="part_price wide"><?php echo convert_price(30, $currency); ?></div>
                          </div>
                      </div>
                  </div>
              </div>
                <div class="single_door_addition_container columns">
                  <div class="add_to_image change-measuring wide" data-image="" data-dimension_measurement_method="self" data-product_id="" data-price="0" data-category="dimensions" data-category2="Dimensions">
                      <div class="row">
                          <div class="columns small-12 part_info_container">
                              <div class="part_title wide">I’ll measure myself</div>
                              <div class="part_price wide"><?php echo convert_price(0, $currency); ?></div>
                          </div>
                      </div>
                  </div>
              </div>
              <?php } else if ($slide_key == 'doorset_measures') { ?>

                <div class="row" data-slide_measurements="professional">
                    <div class="single_door_addition_container columns">

                        <form action="">
                            <div class="row">
                                <div class="columns medium-9">
                                    <label for="professional_address_line_1"><span class="required">*</span> Address</label>
                                    <input id="professional_address_line_1" class="professional_address_line" type="text">
                                    <!-- <input id="professional_address_line_2" class="professional_address_line" type="text">
                                    <input id="professional_address_line_3" class="professional_address_line" type="text"> -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="columns medium-9">
                                    <label for="professional_address_phone"><span class="required">*</span> Phone</label>
                                    <input id="professional_address_phone" class="professional_address_phone" type="tel">
                                </div>
                            </div>
                            <div class="row">
                                <div class="columns medium-9">
                                    <label for="professional_datepicker"><span class="required">*</span> Date</label>
                                    <input id="professional_datepicker" class="hasDatepicker" type="text">
                                </div>
                            </div>
                            <div class="row">
                                <div class="columns medium-9">
                                    <h6><span class="required">*</span> Time</h6>
                                    <div class="columns professional-radio-container small-4">
                                        <input name="professional_time" class="professional_radio" id="professional_time1" type="radio" value="10:00">
                                        <label class="professional_time_label" for="professional_time1">10:00</label>
                                    </div>
                                    <div class="columns professional-radio-container small-4">
                                        <input name="professional_time" class="professional_radio" id="professional_time2" type="radio" value="13:00">
                                        <label class="professional_time_label" for="professional_time2">13:00</label>
                                    </div>
                                    <div class="columns professional-radio-container small-4">
                                        <input name="professional_time" class="professional_radio" id="professional_time3" type="radio" value="16:00">
                                        <label class="professional_time_label" for="professional_time3">16:00</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="columns notes-container medium-9">
                                    <h6>Notes</h6>
                                    <textarea name="professional_notes" id="professional_notes" cols="30" rows="5"></textarea>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="row" data-slide_measurements="self_measure">
                    <div class="single_door_addition_container columns">
                        <div class="row">

                            <div class="columns medium-6 large-4 measure_box">
                                <h3>Width</h3>
                                <select name="door_width" id="door_builder_width">
                                  <?php
                                    foreach ($standard_measures['width'] as $width) {
                                      echo '<option data-price="'.convert_price_noformat($pricelist['doorset_width'][$width], $currency).'" data-usd="'.$pricelist['doorset_width'][$width].'" value="'.$width.'">'.$width.'mm - '.convert_price($pricelist['doorset_width'][$width], $currency).'</option>';
                                    }
                                  ?>
                                </select>
                                <div class="custom-measure"><input id="width_checkbox" data-disabled_size="width" class="width_checkbox size_checkbox checkbox-custom" type="checkbox"><label for="width_checkbox">Custom width</label></div>
    														<br>
    														<input id="custom_door_width" style="display:none;" type="number" min="700">
                                <small>Value between 700mm - 930mm</small>
                            </div>
                            <div class="columns medium-6 large-4 measure_box">
                                <h3>Height</h3>
                                <select name="door_height" id="door_builder_height">
                                  <?php
                                    foreach ($standard_measures['height'] as $height) {
                                      echo '<option data-price="'.convert_price_noformat($pricelist['doorset_height'][$height], $currency).'" data-usd="'.$pricelist['doorset_height'][$height].'" value="'.$height.'">'.$height.'mm - '.convert_price($pricelist['doorset_height'][$height], $currency).'</option>';
                                    }
                                  ?>
                                </select>
                                <div class="custom-measure"><input id="height_checkbox" data-disabled_size="height" class="height_checkbox size_checkbox checkbox-custom" type="checkbox"><label for="height_checkbox">Custom height</label></div>
    														<br><input id="custom_door_height" name="custom_door_height"style="display:none;" type="number" min="2200" max="2400">
                                <small>Value between 2200mm - 2400mm</small>
                            </div>
    												<div class="columns medium-6 end large-4 measure_box">
                                <h3>Thickness</h3>
    														<input id="door_builder_thickness" name="door_builder_thickness" type="number" min="90" max="360">
                                <small>Value between 90mm - 360mm</small>
                            </div>
                        </div>
                        <div class="row">
                          <div class="columns medium-12 custom_price">
                            <strong>Custom cut price: <?php echo convert_price(get_field('custom_cut_price', 'shop_default'),$currency); ?></strong>
                          </div>
                            <div class="columns">
                                <div class="confirm_size_radio_container">
                                    <input id="confirm_size_radio" class="add_to_image2 confirm_size_radio radio-custom" type="radio"><label for="confirm_size_radio">Confirm selected measurements</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              <?php } else if ($slide_key == 'doorset_summary') {
                $i = 0;
                foreach ( $slides as $key => $category ):
                  if ($key != 'doorset_summary') {
                  ?>
                    <div class="single_door_addition_container final_slide_info columns">
                        <div class="row">
                            <div class="columns small-6 part_image_container half">
                                <h5 class="showstep" data-step="<?php echo $i; ?>"><?php echo $category; ?>:</h5>

                            </div>
                            <div class="columns small-6 part_info_container half-2">
                                <div class="<?php echo $key; ?>_chosen_title part_title final"></div>

                            </div>
                        </div>
                    </div>
                  <?php
                  $i++;
                }
                endforeach; ?>
                <div class="single_door_addition_container final_slide_info columns">
                    <div class="row">
                        <div class="columns small-6 part_image_container half">
                            <h5><?php _e( 'Price', '64door' ) ?>:</h5>

                        </div>
                        <div class="columns small-6 part_info_container half-2">
                            <div class="price">
                              <?php echo get_woocommerce_currency_symbol() ?>
                              <span class="end_price_number_novat" data-price=""></span>
                              <p class="vat_included final">Price excluding VAT</p>
                            </div>
                            <div class="price">
                              <?php echo get_woocommerce_currency_symbol() ?>
                              <span class="end_price_number" data-price=""></span>
                              <p class="vat_included final">Price including VAT</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single_door_addition_container final_slide_info columns">
                    <div class="row">
                        <div class="columns small-6 part_image_container half">
                            <h5><?php _e( 'Quantity', '64door' ) ?>:</h5>
                        </div>
                        <div class="columns small-6 part_info_container half-2">
                            <input class="door_builder_quantity_before_cart" type="number" step="1" min="1"
                                   max="" value="1" title="Qty" size="4" pattern="[0-9]*"
                                   inputmode="numeric">
                        </div>
                    </div>
                </div>
                <div class="single_door_addition_container final_slide_info columns">
                    <div class="row">
                        <div class="columns small-6 part_image_container half">
                            <h5><?php _e( 'Total', '64door' ) ?>:</h5>

                        </div>
                        <div class="columns small-6 part_info_container half-2">
                            <div class="price">
                              <?php echo get_woocommerce_currency_symbol() ?>
                              <span class="end_price_quantity_total_number_novat"></span>
                              <p class="vat_included final">Price excluding VAT</p>
                            </div>
                            <div class="price">
                              <?php echo get_woocommerce_currency_symbol() ?>
                              <span class="end_price_quantity_total_number"></span>
                              <p class="vat_included final">Price including VAT</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php } ?>



              </div>


            </div>
          </div>



        <?php $counter++; } ?>
        </div>
      </section>
    </div>

  </div>


  <section class="door_full_price door_builder_buttons">

      <div class="counter_line">
          <div class="counter_line_part"></div>
      </div>
      <div class="builder_navigation_inner row">

          <div class="columns builder_back_button_container medium-4">
              <div id="back_button" class="door_builder_back custom_button_general"></div>
          </div>
          <div class="columns title_price_container medium-4">
    <!-- <?php the_title( '<h1 class="product-title entry-title">', '</h1>' );
    ?> -->
    <div class="steps">
      <div class="step_counter"><?php _e( 'Step:', '64door' ) ?> <span id="current_step">1</span>/<span id="steps_amount"></span>
      </div>
      <h1 class="product-title entry-title"><span id="step_door"><?php _e( '', '64door' ) ?> </span><span id="step_name">Colour</span></h1>
    </div>
              <div class="price"><?php _e( 'Doorset price:', '64door' );
      echo ' ' . get_woocommerce_currency_symbol() ?>
      <span class="price_number"></span>
      <p class="vat_included notfinal">(excl. VAT)</p>
      <span class="pro_measure_number"><?php echo get_post_meta( get_field('professional_measure_product','shop_default'), '_regular_price', true ); ?></span>
              </div>

          </div>
          <div class="columns next_add_to_cart_container medium-4">
              <a class="next_ahref">
                  <div id="next_button"></div>
              </a>

              <form class="cart" method="post" enctype='multipart/form-data'>


                  <input type="hidden" id="doorset_custom_measures" name="doorset_custom_measures" value="" data-price="<?php echo convert_price_noformat(get_field('custom_cut_price', 'shop_default'), $currency); ?>" data-usd="<?php echo get_field('custom_cut_price', 'shop_default'); ?>">
                  <input type="hidden" id="doorset_furnitur_set" name="doorset_furnitur_set" value="">
                  <?php
                  $fields = get_doorbuilder_fields();
                  $furnitur_fields = array('doorset_lock', 'doorset_lock_core', 'doorset_hinge', 'doorset_handle', 'doorset_key_hole_cover', 'doorset_striking_plate');
                  $furnitur_sets = get_furnitur_sets(get_field('doorset_furniture_set_default'));
                  foreach($fields as $key => $field) {
                    if (in_array($key, $furnitur_fields) ) {
                      echo '<input type="hidden" id="'.$key.'" name="'.$key.'" value="'.$furnitur_sets[substr($key,8)].'">';
                    } else {
                      echo '<input type="hidden" id="'.$key.'" name="'.$key.'" value="'.get_field($key).'">';
                    }

                  }
              ?>
              <div class="hidden">
              <?php  woocommerce_quantity_input( array(
                  'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                  'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                  'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $product->get_min_purchase_quantity(),
                  ) );
              ?>
            </div>
              <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button custom_button_general">
                <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
              </button>

            <?php  do_action( 'woocommerce_after_add_to_cart_button' ); ?>
            </form>

          </div>

      </div>
  </section>

</section>



<?php endwhile; ?>
