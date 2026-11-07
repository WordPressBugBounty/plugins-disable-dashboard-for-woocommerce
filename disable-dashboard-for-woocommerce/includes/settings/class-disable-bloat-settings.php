<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
// Add a position in Options menu in admin panel
function wcbloat_menu_page() {
    add_options_page(
        esc_attr__( 'Disable Bloat', 'disable-dashboard-for-woocommerce' ),
        esc_attr__( 'Disable Bloat', 'disable-dashboard-for-woocommerce' ),
        'manage_options',
        'disable-bloat',
        'wcbloat_options_page'
    );
}

add_action( 'admin_menu', 'wcbloat_menu_page' );
// Display admin options page
function wcbloat_options_page() {
    ?>
    <div class="wrap">
        <h1><?php 
    esc_attr_e( 'Disable Bloat for WordPress & WooCommerce', 'disable-dashboard-for-woocommerce' );
    ?></h1>
        <?php 
    $active_tab = ( isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'main' );
    ?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=disable-bloat&tab=main" class="nav-tab <?php 
    echo ( esc_attr( $active_tab ) == 'main' ? 'nav-tab-active' : '' );
    ?>"><?php 
    esc_attr_e( 'WooCommerce', 'disable-dashboard-for-woocommerce' );
    ?></a>
            <a href="?page=disable-bloat&tab=admin" class="nav-tab <?php 
    echo ( esc_attr( $active_tab ) == 'admin' ? 'nav-tab-active' : '' );
    ?>"><?php 
    esc_attr_e( 'Admin panel optimization', 'disable-dashboard-for-woocommerce' );
    ?></a>
            <a href="?page=disable-bloat&tab=performance" class="nav-tab <?php 
    echo ( esc_attr( $active_tab ) == 'performance' ? 'nav-tab-active' : '' );
    ?>"><?php 
    esc_attr_e( 'Site performance', 'disable-dashboard-for-woocommerce' );
    ?></a>
            <a href="?page=disable-bloat&tab=wpcore" class="nav-tab <?php 
    echo ( esc_attr( $active_tab ) == 'wpcore' ? 'nav-tab-active' : '' );
    ?>"><?php 
    esc_attr_e( 'WordPress Core', 'disable-dashboard-for-woocommerce' );
    ?></a>
            <a href="?page=disable-bloat&tab=block" class="nav-tab <?php 
    echo ( esc_attr( $active_tab ) == 'block' ? 'nav-tab-active' : '' );
    ?>"><?php 
    esc_attr_e( 'Block Editor', 'disable-dashboard-for-woocommerce' );
    ?></a>
            <a href="?page=disable-bloat&tab=thirdparty" class="nav-tab <?php 
    echo ( esc_attr( $active_tab ) == 'thirdparty' ? 'nav-tab-active' : '' );
    ?>"><?php 
    esc_attr_e( 'Third-party plugins bloat', 'disable-dashboard-for-woocommerce' );
    ?></a>
            <a href="?page=disable-bloat&tab=data" class="nav-tab <?php 
    echo ( esc_attr( $active_tab ) == 'data' ? 'nav-tab-active' : '' );
    ?>"><?php 
    esc_attr_e( 'Plugin data', 'disable-dashboard-for-woocommerce' );
    ?></a>


        </h2>
        <div class="disable-bloat-container">
            <form action="options.php" method="POST">
                <?php 
    if ( $active_tab == 'main' ) {
        settings_fields( 'wcbloat-main-options' );
        do_settings_sections( 'wcbloat-main' );
    } elseif ( $active_tab == 'admin' ) {
        settings_fields( 'wcbloat-admin-options' );
        do_settings_sections( 'wcbloat-admin' );
    } elseif ( $active_tab == 'performance' ) {
        settings_fields( 'wcbloat-performance-options' );
        do_settings_sections( 'wcbloat-performance' );
    } elseif ( $active_tab == 'wpcore' ) {
        settings_fields( 'wcbloat-wpcore-options' );
        do_settings_sections( 'wcbloat-wpcore' );
    } elseif ( $active_tab == 'block' ) {
        settings_fields( 'wcbloat-block-options' );
        do_settings_sections( 'wcbloat-block' );
    } elseif ( $active_tab == 'thirdparty' ) {
        settings_fields( 'wcbloat-thirdparty-options' );
        do_settings_sections( 'wcbloat-thirdparty' );
    } elseif ( $active_tab == 'data' ) {
        settings_fields( 'wcbloat-data-options' );
        do_settings_sections( 'wcbloat-data' );
    }
    submit_button();
    ?>
            </form>
        </div>
    </div>
  <?php 
}

// Show a bar on WooCommerce settings screen if WooCommerce is not installed
function wcbloat_woo_not_installed_info_bar() {
    // Check if WooCommmerce is active
    if ( !wcbloat_is_woo_active() ) {
        $woo_not_installed_bar = sprintf( '<p class="wcbloat-bar">' . __( 'You need to <strong>activate WooCommerce</strong> to make the options below effective.', 'disable-dashboard-for-woocommerce' ) . '<hr /></p>' );
        return $woo_not_installed_bar;
    }
}

// "Buy PRO" bar
function wcbloat_buy_pro_bar() {
    $is_free = !wcbloat_fs()->is__premium_only();
    /* translators: link to external plugin homepage */
    $buy_pro_bar = ( $is_free ? sprintf( '<p class="wcbloat-bar">' . __( 'Get <a href="%s" target="_blank">Disable Bloat for WordPress & WooCommerce PRO</a> to unlock the<span class="wcbloat-pro-label">PRO</span>options.', 'disable-dashboard-for-woocommerce' ), 'https://disablebloat.com/?utm_source=settings&utm_medium=referral&utm_campaign=Buy+PRO+Bar' ) : '' );
    return $buy_pro_bar;
}

// Register a yes/no checkbox setting and its field in one go.
// $args: page, group, section, title, label, description (may contain HTML), pro (bool).
function wcbloat_add_checkbox_setting(  $option_name, $args  ) {
    add_settings_field(
        $option_name,
        $args['title'],
        'wcbloat_checkbox_setting_callback',
        $args['page'],
        $args['section'],
        array_merge( $args, array(
            'option_name' => $option_name,
        ) )
    );
    register_setting( $args['group'], $option_name, array(
        'sanitize_callback' => 'sanitize_key',
    ) );
}

// Render a checkbox registered with wcbloat_add_checkbox_setting()
function wcbloat_checkbox_setting_callback(  $args  ) {
    $option_name = $args['option_name'];
    $is_pro = !empty( $args['pro'] );
    ?>
    <input type='hidden' name='<?php 
    echo esc_attr( $option_name );
    ?>' value='no'>
    <label><input type='checkbox' name='<?php 
    echo esc_attr( $option_name );
    ?>' <?php 
    checked( get_option( $option_name ), 'yes' );
    echo ( $is_pro ? wcbloat_is_pro_readonly() : '' );
    ?> value='yes'> <?php 
    echo esc_html( $args['label'] );
    echo ( $is_pro ? wcbloat_is_pro_badge() : '' );
    ?></label>
    <p><?php 
    echo wp_kses_post( $args['description'] );
    ?></p>
  <?php 
}

// Options that store a list of keys (multi-checkbox fields)
function wcbloat_array_option_names() {
    return array('wcbloat_admin_disable_features', 'wcbloat_wp_dashboard_widgets_disable', 'wcbloat_wp_sidebar_widgets_disable');
}

// Sanitize a multi-checkbox value: keep an array of sanitized scalar keys only (nested values are dropped)
function wcbloat_sanitize_key_array(  $value  ) {
    if ( !is_array( $value ) ) {
        return array();
    }
    $value = array_filter( $value, 'is_scalar' );
    return array_values( array_filter( array_map( 'sanitize_key', array_map( 'strval', $value ) ) ) );
}

// Keywords that keep a third-party admin notice visible ("Notices from other plugins" option)
function wcbloat_default_notice_keywords() {
    return 'saved, payment, error, failed, expired, security, database';
}

// Sanitize a comma-separated keyword list: lowercase, trimmed, no duplicates, joined with ", "
function wcbloat_sanitize_keyword_list(  $value  ) {
    if ( !is_scalar( $value ) ) {
        return '';
    }
    $words = preg_split( '/[,\\n]+/', strtolower( sanitize_textarea_field( (string) $value ) ) );
    $words = array_unique( array_filter( array_map( 'trim', $words ) ) );
    return implode( ', ', $words );
}

// The keyword list as an array
function wcbloat_notice_keywords() {
    $value = get_option( 'wcbloat_third_party_notices_keep', wcbloat_default_notice_keywords() );
    $value = wcbloat_sanitize_keyword_list( $value );
    return ( $value === '' ? array() : explode( ', ', $value ) );
}

// Options that store free text, with their sanitize callback (used by the settings import)
function wcbloat_text_option_names() {
    return array(
        'wcbloat_third_party_notices_keep' => 'wcbloat_sanitize_keyword_list',
    );
}

// Is the current field PRO only
function wcbloat_is_pro_readonly() {
    $is_pro_readonly = __( ' disabled="disabled"' );
    return $is_pro_readonly;
}

// If the field is PRO-only, display a PRO badge
function wcbloat_is_pro_badge() {
    $is_pro_badge = __( '<span class="wcbloat-pro-label">PRO</span>' );
    return $is_pro_badge;
}

// Include options pages
require_once 'class-disable-bloat-settings-main.php';
require_once 'class-disable-bloat-settings-admin.php';
require_once 'class-disable-bloat-settings-performance.php';
require_once 'class-disable-bloat-settings-wpcore.php';
require_once 'class-disable-bloat-settings-block.php';
require_once 'class-disable-bloat-settings-thirdparty.php';
require_once 'class-disable-bloat-settings-plugin-data.php';
// Remove top-level plugin menu. Fix of a Freemius-related bug, which caused adding unnecessary admin menu (multisite only)
add_action( 'admin_head', 'wcbloat_remove_multisite_plugin_menu' );
function wcbloat_remove_multisite_plugin_menu() {
    remove_menu_page( 'disable-dashboard-for-woocommerce' );
}
