<?php

if ( !defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

// Checking if WooCommerce plugin is active, including multisite activations
function wcbloat_is_woo_active()
{
    static $cached = null;

  if ( $cached !== null ) {
      return $cached;
  }

  if ( !function_exists( 'is_plugin_active_for_network' ) ) {
      require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
  }

    // The basename of the main plugin file (not of this included file), so that
    // network activation checks work on multisite installations.
    $plugin_basename = defined( 'WCBLOAT_PLUGIN_BASENAME' ) ? WCBLOAT_PLUGIN_BASENAME : 'disable-dashboard-for-woocommerce/disable-dashboard-for-woocommerce.php';

  if ( is_multisite() && is_plugin_active_for_network( $plugin_basename ) ) {
      // this plugin is network activated - Woo must be network activated
      $is_active = is_plugin_active_for_network( 'woocommerce/woocommerce.php' );
  } else {
      // this plugin runs on a single site || is locally activated - Woo can be network or locally activated
      $is_active = is_plugin_active( 'woocommerce/woocommerce.php' );
  }

    // Fallback for WooCommerce installed in a non-standard folder
  if ( !$is_active && class_exists( 'WooCommerce' ) ) {
      $is_active = true;
  }

    // Only cache a negative result once all plugins are loaded (the fallback above may still become true)
  if ( $is_active || did_action( 'plugins_loaded' ) ) {
      $cached = $is_active;
  }

    return $is_active;
}
