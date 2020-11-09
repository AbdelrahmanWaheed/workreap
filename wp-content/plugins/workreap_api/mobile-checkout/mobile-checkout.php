<?php
/**
 * Template Name: Mobile Checkout Page
 *
 * This file will include all global settings which will be used in all over the plugin,
 * It have gatter and setter methods
 *
 * @link              https://codecanyon.net/user/amentotech/portfolio
 * @since             1.0.0
 * @package           Workreap APP
 *
 */
 if(isset($_GET['order_id'])){ 
	global $wpdb,$woocommerce; 
	$order_id 		= $_GET['order_id'];
	$get_data   	= "SELECT * FROM `".MOBILE_APP_TEMP_CHECKOUT."`  WHERE `id`=".$order_id;
	$temp_date   	= $wpdb->get_results($get_data);
	$temp_date   	= $temp_date[0]->temp_data;
	$order_data 	= maybe_unserialize($temp_date);
	$order_data 	= json_decode($order_data);
	$checkout_url   = wc_get_checkout_url();
	
	//separate arrays
	if ( $order_data ){
		foreach($order_data as $key => $value){
			$$key = $value;
		}	
	}
	 
	 
	$user_id 		= $customer_id; //wp_validate_auth_cookie($order_data['token'], 'logged_in');
	$user 			= get_userdata($user_id);
	
	$order_type		= !empty( $order_type ) ?  $order_type : 'service'; 
	$service_id		= !empty( $service_id ) ?  $service_id : ''; 
	$addons			= !empty( $addons ) ?  explode( ',',$addons ) : array();
	//$project_id		= !empty( $order_data['project_id'] ) ?  $order_data['project_id'] : ''; 
	//$hiring_id		= !empty( $order_data['hiring_id'] ) ?  $order_data['hiring_id'] : ''; 
	 
	
    if ($user) {
        if (!is_user_logged_in()) {
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id);
            $url = $_SERVER['REQUEST_URI'];
			wp_redirect( $url );
        }
    } else{
		esc_html_e('You must be login to view checkout page.','workreap_api'); 
		return false;
	}

	//Selected Payment Method
	if(isset($payment_method) && $payment_method != ""){
		$current_method   = $payment_method;
	}

	$bk_settings	= worrketic_hiring_payment_setting();
		
	if( isset( $bk_settings['type'] ) && $bk_settings['type'] === 'woo' && !empty($order_type) ) {
		if( $order_type === 'service' ){
			$product_id	= workreap_get_hired_product_id();
			if( !empty( $product_id )) {
				if ( class_exists('WooCommerce') ) {
					$woocommerce->session->set('refresh_totals', true);
    				$woocommerce->cart->empty_cart();
					$user_id			= $user_id;
					$price				= get_post_meta($service_id ,'_price',true);
					
					$single_service_price	= $price;
					if( !empty( $addons ) ){
						foreach( $addons as $addon_id ){
							$addons_price		= get_post_meta($addon_id ,'_price',true);
							$addons_price		= !empty( $addons_price ) ? $addons_price : 0 ;
							$price				= $price + $addons_price;
							
						}
					}
					
					$delivery_time		= wp_get_post_terms($service_id, 'delivery');
					$delivery_time 		= !empty( $delivery_time[0] ) ? $delivery_time[0]->term_id : '';
					$admin_shares 		= 0.0;
					$freelancer_shares 	= 0.0;
					
					if( !empty( $price ) ){
						if( isset( $bk_settings['percentage'] ) && $bk_settings['percentage'] > 0 ){
							$admin_shares 		= $price/100*$bk_settings['percentage'];
							$freelancer_shares 	= $price - $admin_shares;
							$admin_shares 		= number_format($admin_shares,2,'.', '');
							$freelancer_shares 	= number_format($freelancer_shares,2,'.', '');
						} else{
							$admin_shares 		= 0.0;
							$freelancer_shares 	= $price;
							$admin_shares 		= number_format($admin_shares,2,'.', '');
							$freelancer_shares 	= number_format($freelancer_shares,2,'.', '');
						}
					}

					$cart_meta['service_id']		= $service_id;
					$cart_meta['delivery_time']		= $delivery_time;
					$cart_meta['price']				= $price;
					$cart_meta['service_price']		= $single_service_price;
					$cart_meta['addons']			= $addons;

					$cart_data = array(
						'product_id' 		=> $product_id,
						'cart_data'     	=> $cart_meta,
						'price'				=> workreap_price_format($price,'return'),
						'payment_type'     	=> 'hiring_service',
						'admin_shares'     	=> $admin_shares,
						'freelancer_shares' => $freelancer_shares,
					);

					$woocommerce->cart->empty_cart();
					$cart_item_data = $cart_data;
					WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);
				} else {
					$esc_html_e('Some error occur, please try again later.','workreap_api'); 
					return false;
				}
			} else{
				$esc_html_e('Some error occur, please try again later.','workreap_api'); 
				return false;
			}
		} else if( $order_type === 'package'){
		
			$product_id		= !empty( $product_id ) ?  $product_id : ''; 
			if( !empty( $product_id )) {
				if ( class_exists('WooCommerce') ) {
				
					global $current_user, $woocommerce;
					$woocommerce->cart->empty_cart(); //empty cart before update cart
					$user_id			= $current_user->ID;
										
					$cart_meta					= array();
					$user_type					= workreap_get_user_type( $user_id );
					$pakeges_features			= workreap_get_pakages_features();
	
					if ( !empty ( $pakeges_features )) {
						foreach( $pakeges_features as $key => $vals ) {
							if( $vals['user_type'] === $user_type || $vals['user_type'] === 'common' ) {
								$item			= get_post_meta($product_id,$key,true);
								$text			=  !empty( $vals['text'] ) ? ' '.esc_html($vals['text']) : '';
								if( $key === 'wt_duration_type' ) {
									$feature 	= workreap_get_duration_types($item,'value');
								} else if( $key === 'wt_badget' ) {
									$feature 	= !empty( $item ) ? $item : 0;
								} else {
									$feature 	= $item;
								}
								
								$cart_meta[$key]	= $feature.$text;
							}
						}
					}
					
					$cart_data = array(
						'product_id' 		=> $product_id,
						'cart_data'     	=> $cart_meta,
						'payment_type'     	=> 'subscription',
					);
	
					$woocommerce->cart->empty_cart();
					$cart_item_data = $cart_data;
					WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);
				} else {
					$json = array();
					$json['type'] 		= 'error';
					$json['message'] 	= esc_html__('Please install WooCommerce plugin to process this order', 'workreap_api');
				}
			} else{
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Some error occur, please try again later', 'workreap_api');
			}
		}
	}
	 
	if( !empty( $current_method ) ){
		$woocommerce->session->set( 'chosen_payment_method', $current_method );
	}
?>
<!doctype html>
<html>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<title><?php esc_html_e('Mobile Checkout Template','workreap_api');?></title>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	<div style="display:none;">
	  <form name="checkout" id="mobile_checkout" method="post" class="woocommerce-checkout" action="<?php echo esc_url( $checkout_url )."?platform=".$platform; ?>" enctype="multipart/form-data">
		  <input type="text" class="mobile-checkout-field" name="billing_first_name" id="billing_first_name" value="<?php echo esc_attr( $billing_info->first_name ); ?>"/>
		  <input type="text" class="mobile-checkout-field" name="billing_last_name" id="billing_last_name" value="<?php echo esc_attr( $billing_info->last_name ); ?>"/>
		  <input type="text" class="mobile-checkout-field" name="billing_country" id="billing_country" value="<?php echo esc_attr( $billing_info->country ); ?>"/>
		  <input type="text" class="mobile-checkout-field" name="billing_company" id="billing_company" value="<?php echo esc_attr( $billing_info->company ); ?>" />
		  <input type="text" class="mobile-checkout-field" name="billing_address_1" id="billing_address_1" placeholder="<?php esc_html_e('House number and street name','workreap_api');?>" value="<?php  echo esc_attr( $billing_info->address_1 ); ?>" />
		  <input type="text" class="mobile-checkout-field" name="billing_address_2" id="billing_address_2" placeholder="<?php esc_html_e('Apartment, suite, unit etc. (optional)','workreap_api');?>" value="<?php  echo esc_attr( $billing_info->address_2 ); ?>" />
		  <input type="text" class="mobile-checkout-field" name="billing_city" id="billing_city" value="<?php  echo esc_attr( $billing_info->city ); ?>" />
		  <input type="text" class="mobile-checkout-field" value="<?php  echo esc_attr( $billing_info->state ); ?>" name="billing_state" id="billing_state" />
		  <input type="text" class="mobile-checkout-field" name="billing_postcode" id="billing_postcode" value="<?php  echo ( $billing_info->postcode ); ?>" />
		  <input type="tel" class="mobile-checkout-field" name="billing_phone" id="billing_phone" value="<?php  echo esc_attr( $billing_info->phone ); ?>" />
		  <input type="email" class="mobile-checkout-field" name="billing_email" id="billing_email" value="<?php  echo esc_attr( $billing_info->email ); ?>" />
		  <input id="ship-to-different-address-checkbox" class="woocommerce-form__input input-checkbox"  type="checkbox" name="ship_to_different_address" value="1" <?php if(isset($sameAddress) && $sameAddress !=""){?> checked="checked" <?php } ?>>
		  <input type="text" class="mobile-checkout-field" name="shipping_first_name" id="shipping_first_name" value="<?php  echo esc_attr( $shipping_info->first_name ); ?>" />  <input type="text" class="mobile-checkout-field" name="shipping_last_name" id="shipping_last_name" value="<?php  echo esc_attr( $shipping_info->last_name ); ?>" />  
		  <input type="text" class="mobile-checkout-field" name="shipping_company" id="shipping_company" value="<?php  echo esc_attr( $shipping_info->company ); ?>" />  
		  <input type="text" class="mobile-checkout-field" name="shipping_country" id="shipping_country" value="<?php  echo esc_attr( $shipping_info->country ); ?>"/>
		  <input type="text" class="mobile-checkout-field" name="shipping_address_1" id="shipping_address_1" placeholder="<?php esc_html_e('House number and street name','workreap_api');?>" value="<?php  echo esc_attr( $shipping_info->address_1 ); ?>" />  
		  <input type="text" class="mobile-checkout-field" name="shipping_address_2" id="shipping_address_2" placeholder="<?php esc_html_e('Apartment, suite, unit etc (optional)','workreap_api');?>" value="<?php  echo esc_attr( $shipping_info->address_2 ); ?>" /> 
		  <input type="text" class="mobile-checkout-field" name="shipping_city" id="shipping_city" value="<?php  echo esc_attr( $shipping_info->city ); ?>" />
		  <input type="text" class="mobile-checkout-field" value="<?php  echo esc_attr( $shipping_info->state ); ?>" name="shipping_state" id="shipping_state" />
		  <input type="text" class="mobile-checkout-field" name="shipping_postcode" id="shipping_postcode" value="<?php  echo esc_attr( $shipping_info->postcode ); ?>" />  <textarea name="order_comments" class="mobile-checkout-field" id="order_comments" placeholder="<?php esc_html_e('Write notes about your order','workreap_api');?>" rows="2" cols="5"><?php $customer_note; ?></textarea>

		  <input type="radio" checked="checked" class="shipping_method" name="shipping_method[]" id="shipping_method_0_<?php echo esc_attr( $shipping_methods ); ?><?php echo esc_attr( $shipid ); ?>" value="<?php echo  esc_attr( $shipping_methods ); ?>:<?php echo esc_attr( $shipid ); ?>" /><?php echo esc_attr( $shipping_methods ); ?>                  
	  </form>
	 </div>               
	<script type="text/javascript"> setTimeout(function(){document.getElementById("mobile_checkout").submit();}, 500);</script>
	</body>
</html>
<?php } ?>