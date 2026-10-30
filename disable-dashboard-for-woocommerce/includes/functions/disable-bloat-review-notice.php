<?php

if ( !defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/* Review notice - ask admins for a WordPress.org review on the Disable Bloat settings page,
/* two weeks after first use. "Maybe later" snoozes it for 30 days, the other choices hide it for good.
/***********************************************************************/
if ( !function_exists( 'wcbloat_review_notice' ) ) {
  function wcbloat_review_notice()
  {
    $screen = get_current_screen();

    if ( !$screen || $screen->id !== 'settings_page_disable-bloat' || !current_user_can( 'manage_options' ) ) {
      return;
    }

    $show_after = get_option( 'wcbloat_review_notice_after' );

    // First visit (or first visit since updating): start the clock
    if ( $show_after === false ) {
      update_option( 'wcbloat_review_notice_after', time() + 14 * DAY_IN_SECONDS, false );
      return;
    }

    // 0 means reviewed or dismissed for good
    if ( empty( $show_after ) || time() < (int) $show_after ) {
      return;
    }

    ?>
    <div class="notice notice-info">
      <p>
        <strong><?php esc_html_e( 'Enjoying Disable Bloat?', 'disable-dashboard-for-woocommerce' ); ?></strong>
        <?php esc_html_e( 'A quick review on WordPress.org helps other site owners find it, and it really makes our day. Thank you!', 'disable-dashboard-for-woocommerce' ); ?>
      </p>
      <p>
        <a class="button button-primary" href="<?php echo esc_url( wcbloat_review_notice_url( 'review' ) ); ?>" target="_blank" rel="noopener noreferrer" onclick="this.closest('.notice').remove()">
          <?php esc_html_e( 'Leave a review', 'disable-dashboard-for-woocommerce' ); ?>
        </a>
        <a class="button" href="<?php echo esc_url( wcbloat_review_notice_url( 'later' ) ); ?>">
          <?php esc_html_e( 'Maybe later', 'disable-dashboard-for-woocommerce' ); ?>
        </a>
        <a class="button button-link" href="<?php echo esc_url( wcbloat_review_notice_url( 'done' ) ); ?>">
          <?php esc_html_e( 'I already did', 'disable-dashboard-for-woocommerce' ); ?>
        </a>
      </p>
    </div>
    <?php
  }
  add_action( 'admin_notices', 'wcbloat_review_notice' );
}

if ( !function_exists( 'wcbloat_review_notice_url' ) ) {
  function wcbloat_review_notice_url( $choice )
  {
    return wp_nonce_url( admin_url( 'admin-post.php?action=wcbloat_review_notice&choice=' . $choice ), 'wcbloat_review_notice' );
  }
}

if ( !function_exists( 'wcbloat_review_notice_choice' ) ) {
  function wcbloat_review_notice_choice()
  {
    check_admin_referer( 'wcbloat_review_notice' );

    if ( !current_user_can( 'manage_options' ) ) {
      wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'disable-dashboard-for-woocommerce' ), 403 );
    }

    $choice = isset( $_GET['choice'] ) ? sanitize_key( $_GET['choice'] ) : '';

    update_option( 'wcbloat_review_notice_after', ( $choice === 'later' ) ? time() + 30 * DAY_IN_SECONDS : 0, false );

    if ( $choice === 'review' ) {
      wp_redirect( 'https://wordpress.org/support/plugin/disable-dashboard-for-woocommerce/reviews/#new-post' );
      exit;
    }

    wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'options-general.php?page=disable-bloat' ) );
    exit;
  }
  add_action( 'admin_post_wcbloat_review_notice', 'wcbloat_review_notice_choice' );
}
