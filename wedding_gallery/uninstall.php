<?php
/**
 * Uninstall Wedding Gallery plugin.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$wg_settings = get_option( 'wg_settings', array() );
$wg_cleanup  = is_array( $wg_settings ) && ! empty( $wg_settings['cleanup_on_uninstall'] );

if ( $wg_cleanup ) {
	$wg_upload_dir = wp_upload_dir();
	if ( isset( $wg_upload_dir['basedir'] ) ) {
		$wg_base_dir    = (string) $wg_upload_dir['basedir'];
		$wg_target_dir  = trailingslashit( $wg_base_dir ) . 'wedding-gallery';
		$wg_real_base   = realpath( $wg_base_dir );
		$wg_real_target = realpath( $wg_target_dir );

		if ( false !== $wg_real_base && false !== $wg_real_target && 0 === strpos( $wg_real_target, $wg_real_base ) && 'wedding-gallery' === basename( $wg_real_target ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			global $wp_filesystem;

			if ( WP_Filesystem() && is_object( $wp_filesystem ) ) {
				$wp_filesystem->delete( $wg_real_target, true );
			}
		}
	}
}

delete_option( 'wg_settings' );
