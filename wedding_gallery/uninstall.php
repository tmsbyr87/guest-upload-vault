<?php
/**
 * Uninstall Wedding Gallery plugin.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'wg_settings', array() );
$cleanup  = is_array( $settings ) && ! empty( $settings['cleanup_on_uninstall'] );

if ( $cleanup ) {
	$upload_dir = wp_upload_dir();
	if ( isset( $upload_dir['basedir'] ) ) {
		$base_dir   = (string) $upload_dir['basedir'];
		$target_dir = trailingslashit( $base_dir ) . 'wedding-gallery';
		$real_base  = realpath( $base_dir );
		$real_target = realpath( $target_dir );

		if ( false !== $real_base && false !== $real_target && 0 === strpos( $real_target, $real_base ) && 'wedding-gallery' === basename( $real_target ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			global $wp_filesystem;

			if ( WP_Filesystem() && is_object( $wp_filesystem ) ) {
				$wp_filesystem->delete( $real_target, true );
			}
		}
	}
}

delete_option( 'wg_settings' );
