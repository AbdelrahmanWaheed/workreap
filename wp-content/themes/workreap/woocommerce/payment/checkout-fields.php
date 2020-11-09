<?php

/**
 * Remove all possible fields
 **/
if (!function_exists('wc_remove_unused_checkout_fields')) {

    function wc_remove_unused_checkout_fields( $fields ) {

        // Billing fields
        unset( $fields['billing']['billing_company'] );
        unset( $fields['billing']['billing_country'] );
        // unset( $fields['billing']['billing_email'] );
        unset( $fields['billing']['billing_phone'] );
        unset( $fields['billing']['billing_state'] );
        // unset( $fields['billing']['billing_first_name'] );
        // unset( $fields['billing']['billing_last_name'] );
        unset( $fields['billing']['billing_address_1'] );
        unset( $fields['billing']['billing_address_2'] );
        unset( $fields['billing']['billing_city'] );
        unset( $fields['billing']['billing_postcode'] );

        // Shipping fields
        unset( $fields['shipping']['shipping_company'] );
        unset( $fields['shipping']['shipping_country'] );
        unset( $fields['shipping']['shipping_phone'] );
        unset( $fields['shipping']['shipping_state'] );
        unset( $fields['shipping']['shipping_first_name'] );
        unset( $fields['shipping']['shipping_last_name'] );
        unset( $fields['shipping']['shipping_address_1'] );
        unset( $fields['shipping']['shipping_address_2'] );
        unset( $fields['shipping']['shipping_city'] );
        unset( $fields['shipping']['shipping_postcode'] );

        // Order fields
        unset( $fields['order']['order_comments'] );

        return $fields;
    }
    add_filter( 'woocommerce_checkout_fields', 'wc_remove_unused_checkout_fields' );
}

/**
 * Remove order notes
 */
if (!function_exists('wc_remove_order_notes')) {

    function wc_remove_order_notes( $enable_order_notes ) {
        return false;
    }
    add_filter( 'woocommerce_enable_order_notes_field', 'wc_remove_order_notes', 98 );
}
