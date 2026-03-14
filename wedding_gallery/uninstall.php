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
			try {
				$iterator = new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator( $real_target, FilesystemIterator::SKIP_DOTS ),
					RecursiveIteratorIterator::CHILD_FIRST
				);

				foreach ( $iterator as $item ) {
					$path = $item->getRealPath();
					if ( false === $path ) {
						continue;
					}

					if ( $item->isDir() ) {
						rmdir( $path );
					} else {
						unlink( $path );
					}
				}

				rmdir( $real_target );
			} catch ( Exception $exception ) {
				// Best-effort cleanup during uninstall.
			}
		}
	}
}

delete_option( 'wg_settings' );
