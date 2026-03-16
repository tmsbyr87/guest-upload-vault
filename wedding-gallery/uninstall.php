<?php
/**
 * Uninstall Wedding Gallery plugin.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$wedding_gallery_settings = get_option( 'wg_settings', array() );
$wedding_gallery_cleanup  = is_array( $wedding_gallery_settings ) && ! empty( $wedding_gallery_settings['cleanup_on_uninstall'] );

if ( $wedding_gallery_cleanup ) {
	$wedding_gallery_upload_dir = wp_upload_dir();
	if ( isset( $wedding_gallery_upload_dir['basedir'] ) ) {
		$wedding_gallery_base_dir    = (string) $wedding_gallery_upload_dir['basedir'];
		$wedding_gallery_target_dir  = trailingslashit( $wedding_gallery_base_dir ) . 'wedding-gallery';
		$wedding_gallery_real_base   = realpath( $wedding_gallery_base_dir );
		$wedding_gallery_real_target = realpath( $wedding_gallery_target_dir );

		if ( false !== $wedding_gallery_real_base && false !== $wedding_gallery_real_target && 0 === strpos( $wedding_gallery_real_target, $wedding_gallery_real_base ) && 'wedding-gallery' === basename( $wedding_gallery_real_target ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			global $wp_filesystem;

			if ( WP_Filesystem() && is_object( $wp_filesystem ) ) {
				$wp_filesystem->delete( $wedding_gallery_real_target, true );
			}
		}
	}
}

delete_option( 'wg_settings' );
