<?php
/**
 * Admin page template.
 *
 * Variables available:
 * - array  $settings
 * - string $protected_upload_url
 * - array  $uploads
 * - string $allowed_text
 * - int    $max_upload_mb
 * - int    $effective_max_upload_mb
 * - array  $upload_limits
 * - array  $key_status
 * - int    $legacy_plaintext_count
 * - array  $upload_health_summary
 * - string $notice
 * - int    $notice_count
 * - int    $notice_skipped
 * - string $current_tab
 * - int    $media_overview_generated_at
 * - bool   $media_overview_from_cache
 * - int    $media_overview_cache_ttl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$guest_upload_vault_settings_tab_url = add_query_arg(
	array(
		'page' => 'guest-upload-vault',
		'tab'  => 'settings',
	),
	admin_url( 'admin.php' )
);
$guest_upload_vault_media_tab_url    = add_query_arg(
	array(
		'page' => 'guest-upload-vault',
		'tab'  => 'media',
	),
	admin_url( 'admin.php' )
);
$guest_upload_vault_help_tab_url    = add_query_arg(
	array(
		'page' => 'guest-upload-vault',
		'tab'  => 'help',
	),
	admin_url( 'admin.php' )
);
$guest_upload_vault_bulk_download_url = admin_url( 'admin-post.php?action=guv_bulk_download' );
$guest_upload_vault_bulk_delete_url   = admin_url( 'admin-post.php?action=guv_bulk_delete' );
$guest_upload_vault_refresh_media_url = admin_url( 'admin-post.php?action=guv_refresh_media_overview' );
?>

<div class="wrap guv-admin-wrap">
	<h1><?php esc_html_e( 'Guest Upload Vault', 'guest-upload-vault' ); ?></h1>
	<div class="guv-brand">
		<img
			src="<?php echo esc_url( GUV_PLUGIN_URL . 'assets/images/logo.png' ); ?>"
			alt="<?php esc_attr_e( 'Guest Upload Vault logo', 'guest-upload-vault' ); ?>"
			class="guv-brand-logo"
		/>
		<p class="description"><?php esc_html_e( 'Securely collect guest photos and videos via protected link or QR code.', 'guest-upload-vault' ); ?></p>
	</div>

	<nav class="nav-tab-wrapper guv-tab-nav" aria-label="<?php esc_attr_e( 'Guest Upload Vault tabs', 'guest-upload-vault' ); ?>">
		<a href="<?php echo esc_url( $guest_upload_vault_settings_tab_url ); ?>" class="nav-tab <?php echo esc_attr( 'settings' === $current_tab ? 'nav-tab-active' : '' ); ?>">
			<?php esc_html_e( 'Settings', 'guest-upload-vault' ); ?>
		</a>
		<a href="<?php echo esc_url( $guest_upload_vault_media_tab_url ); ?>" class="nav-tab <?php echo esc_attr( 'media' === $current_tab ? 'nav-tab-active' : '' ); ?>">
			<?php esc_html_e( 'Media', 'guest-upload-vault' ); ?>
		</a>
		<a href="<?php echo esc_url( $guest_upload_vault_help_tab_url ); ?>" class="nav-tab <?php echo esc_attr( 'help' === $current_tab ? 'nav-tab-active' : '' ); ?>">
			<?php esc_html_e( 'Help', 'guest-upload-vault' ); ?>
		</a>
	</nav>

	<?php if ( 'saved' === $notice ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Settings saved.', 'guest-upload-vault' ); ?></p>
		</div>
	<?php elseif ( 'saved_clamped' === $notice ) : ?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %d: effective max upload in MB */
					esc_html__( 'Settings saved. Max upload size was clamped to %d MB to match server/runtime safety limits.', 'guest-upload-vault' ),
					(int) $effective_max_upload_mb
				);
				?>
			</p>
		</div>
	<?php elseif ( 'delete_success' === $notice ) : ?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %d: deleted files count */
					esc_html__( '%d file was deleted.', 'guest-upload-vault' ),
					(int) $notice_count
				);
				?>
			</p>
		</div>
	<?php elseif ( 'delete_failed' === $notice ) : ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'File could not be deleted.', 'guest-upload-vault' ); ?></p>
		</div>
	<?php elseif ( 'bulk_delete_success' === $notice ) : ?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %d: deleted files count */
					esc_html__( '%d files were deleted.', 'guest-upload-vault' ),
					(int) $notice_count
				);
				?>
			</p>
		</div>
	<?php elseif ( 'bulk_delete_partial' === $notice ) : ?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php
				printf(
					/* translators: 1: deleted files count, 2: skipped files count */
					esc_html__( '%1$d files were deleted. %2$d files were skipped.', 'guest-upload-vault' ),
					(int) $notice_count,
					(int) $notice_skipped
				);
				?>
			</p>
		</div>
	<?php elseif ( 'bulk_delete_failed' === $notice ) : ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'No files could be deleted.', 'guest-upload-vault' ); ?></p>
		</div>
	<?php elseif ( 'bulk_no_selection' === $notice ) : ?>
		<div class="notice notice-warning is-dismissible">
			<p><?php esc_html_e( 'Please select at least one file first.', 'guest-upload-vault' ); ?></p>
		</div>
	<?php elseif ( 'bulk_no_files' === $notice ) : ?>
		<div class="notice notice-warning is-dismissible">
			<p><?php esc_html_e( 'No encrypted files are currently available.', 'guest-upload-vault' ); ?></p>
		</div>
	<?php elseif ( 'zip_unavailable' === $notice ) : ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Bulk ZIP download is not available on this server (ZipArchive missing).', 'guest-upload-vault' ); ?></p>
		</div>
	<?php elseif ( 'zip_failed' === $notice ) : ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Could not create ZIP archive. Please try again.', 'guest-upload-vault' ); ?></p>
		</div>
	<?php elseif ( 'bulk_download_failed' === $notice ) : ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'No files could be added to ZIP download.', 'guest-upload-vault' ); ?></p>
		</div>
	<?php elseif ( 'media_refreshed' === $notice ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Media diagnostics were refreshed.', 'guest-upload-vault' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $upload_limits['is_clamped'] ) ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: 1: configured MB, 2: runtime cap MB */
					esc_html__( 'Current configured max is %1$d MB, but this server can safely handle up to %2$d MB. Uploads are limited to the lower value.', 'guest-upload-vault' ),
					(int) $upload_limits['configured_mb'],
					(int) $upload_limits['runtime_cap_mb']
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $legacy_plaintext_count ) ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: %d: legacy file count */
					esc_html__( 'Detected %d legacy plaintext file(s). They are no longer served by this plugin. Migrate or remove them from uploads/guest-upload-vault.', 'guest-upload-vault' ),
					(int) $legacy_plaintext_count
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( empty( $key_status['healthy'] ) ) : ?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'Encryption key status is unhealthy. Media uploads/downloads may fail until the key configuration is repaired.', 'guest-upload-vault' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $upload_health_summary['missing_metadata'] ) ) : ?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					/* translators: %d: affected files count */
					esc_html__( '%d encrypted file(s) are missing metadata. Those files cannot be downloaded until metadata is restored.', 'guest-upload-vault' ),
					(int) $upload_health_summary['missing_metadata']
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $upload_health_summary['invalid_metadata'] ) ) : ?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					/* translators: %d: affected files count */
					esc_html__( '%d file(s) have damaged or unreadable metadata. Download may fail until repaired from backup.', 'guest-upload-vault' ),
					(int) $upload_health_summary['invalid_metadata']
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $upload_health_summary['metadata_tampered'] ) ) : ?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					/* translators: %d: affected files count */
					esc_html__( '%d file(s) failed metadata integrity checks. They may have been modified or corrupted.', 'guest-upload-vault' ),
					(int) $upload_health_summary['metadata_tampered']
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $upload_health_summary['unsupported_key_version'] ) ) : ?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					/* translators: %d: affected files count */
					esc_html__( '%d file(s) require an encryption key version that is not available on this site.', 'guest-upload-vault' ),
					(int) $upload_health_summary['unsupported_key_version']
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $upload_health_summary['legacy_metadata_plaintext'] ) ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: %d: affected files count */
					esc_html__( '%d file(s) still use legacy plaintext metadata. They remain downloadable, but re-uploading improves privacy.', 'guest-upload-vault' ),
					(int) $upload_health_summary['legacy_metadata_plaintext']
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $upload_health_summary['decrypt_failed'] ) ) : ?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					/* translators: %d: affected files count */
					esc_html__( '%d file(s) failed the decryption health check. The encrypted file or metadata may be corrupted.', 'guest-upload-vault' ),
					(int) $upload_health_summary['decrypt_failed']
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( 'settings' === $current_tab ) : ?>
		<div class="guv-settings-card">
			<h2><?php esc_html_e( 'Settings', 'guest-upload-vault' ); ?></h2>
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="guv_save_settings" />
				<?php wp_nonce_field( 'guv_save_settings', 'guv_save_settings_nonce' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="upload_page_url"><?php esc_html_e( 'Upload Page URL', 'guest-upload-vault' ); ?></label>
							</th>
							<td>
								<input
									type="url"
									id="upload_page_url"
									name="upload_page_url"
									class="regular-text"
									value="<?php echo esc_attr( $settings['upload_page_url'] ); ?>"
									placeholder="https://example.com/upload-page/"
								/>
								<p class="description">
									<?php
									printf(
										/* translators: %s: shortcode */
										esc_html__( 'Add shortcode %s to this page.', 'guest-upload-vault' ),
										'[guest_upload_vault]'
									);
									?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="access_token"><?php esc_html_e( 'Access Token', 'guest-upload-vault' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									id="access_token"
									name="access_token"
									class="regular-text code"
									value="<?php echo esc_attr( $settings['access_token'] ); ?>"
								/>
								<p>
									<button type="submit" name="rotate_token" value="1" class="button">
										<?php esc_html_e( 'Regenerate Guest Link', 'guest-upload-vault' ); ?>
									</button>
								</p>
								<p class="description">
									<?php esc_html_e( 'Anyone with the protected link can upload. Regenerating token invalidates old links and QR prints.', 'guest-upload-vault' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="max_upload_mb"><?php esc_html_e( 'Max Upload Size (MB)', 'guest-upload-vault' ); ?></label>
							</th>
							<td>
								<input
									type="number"
									id="max_upload_mb"
									name="max_upload_mb"
									min="1"
									step="1"
									value="<?php echo esc_attr( $max_upload_mb ); ?>"
								/>
								<p class="description">
									<?php
									printf(
										/* translators: 1: allowed file types, 2: effective MB */
										esc_html__( 'Allowed file types: %1$s. Effective per-file limit: %2$d MB.', 'guest-upload-vault' ),
										esc_html( $allowed_text ),
										(int) $effective_max_upload_mb
									);
									?>
								</p>
								<p class="description">
									<?php
									printf(
										/* translators: 1: upload_max_filesize MB, 2: post_max_size MB, 3: memory_limit MB, 4: memory-safe MB */
										esc_html__( 'Runtime limits (MB): upload_max_filesize=%1$d, post_max_size=%2$d, memory_limit=%3$d, memory-safe ceiling=%4$d.', 'guest-upload-vault' ),
										(int) $upload_limits['upload_max_mb'],
										(int) $upload_limits['post_max_mb'],
										(int) $upload_limits['memory_limit_mb'],
										(int) $upload_limits['memory_safe_mb']
									);
									?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="cleanup_on_uninstall"><?php esc_html_e( 'Cleanup On Uninstall', 'guest-upload-vault' ); ?></label>
							</th>
							<td>
								<label for="cleanup_on_uninstall">
									<input
										type="checkbox"
										id="cleanup_on_uninstall"
										name="cleanup_on_uninstall"
										value="1"
										<?php checked( ! empty( $settings['cleanup_on_uninstall'] ) ); ?>
									/>
									<?php esc_html_e( 'Yes, permanently delete guest media + metadata from uploads/guest-upload-vault when uninstalling this plugin.', 'guest-upload-vault' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'Leave unchecked to keep files on disk after uninstall.', 'guest-upload-vault' ); ?>
								</p>
								<p class="description">
									<?php esc_html_e( 'This setting has no effect on normal plugin deactivation.', 'guest-upload-vault' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Encryption Key', 'guest-upload-vault' ); ?></th>
							<td>
								<p>
									<?php
									printf(
										/* translators: 1: healthy/unhealthy, 2: version, 3: key fingerprint */
										esc_html__( 'Status: %1$s | Version: %2$d | Fingerprint: %3$s', 'guest-upload-vault' ),
										! empty( $key_status['healthy'] ) ? esc_html__( 'Healthy', 'guest-upload-vault' ) : esc_html__( 'Problem', 'guest-upload-vault' ),
										(int) $key_status['key_version'],
										esc_html( (string) $key_status['fingerprint'] )
									);
									?>
								</p>
								<p class="description">
									<?php esc_html_e( 'Backup requirement: keep database/plugin options and uploads/guest-upload-vault together in the same backup/restore set. Restoring only one can make media undecryptable.', 'guest-upload-vault' ); ?>
								</p>
								<p class="description">
									<?php esc_html_e( 'Operational tip: verify restores on a staging site before the event date.', 'guest-upload-vault' ); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Save Settings', 'guest-upload-vault' ) ); ?>
			</form>
		</div>

		<div class="guv-settings-card">
			<h2><?php esc_html_e( 'Protected Upload URL (QR Target)', 'guest-upload-vault' ); ?></h2>
			<?php if ( ! empty( $protected_upload_url ) ) : ?>
				<p><code><?php echo esc_html( $protected_upload_url ); ?></code></p>
				<p>
					<label for="guv_protected_upload_url"><strong><?php esc_html_e( 'Guest Upload Link', 'guest-upload-vault' ); ?></strong></label><br />
					<input
						id="guv_protected_upload_url"
						type="text"
						class="regular-text code"
						readonly
						value="<?php echo esc_attr( $protected_upload_url ); ?>"
						style="max-width: 100%; width: 520px;"
					/>
					<button type="button" class="button" id="guv_copy_link">
						<?php esc_html_e( 'Copy Link', 'guest-upload-vault' ); ?>
					</button>
				</p>
				<p><strong><?php esc_html_e( 'QR Code', 'guest-upload-vault' ); ?></strong></p>
				<div id="guv_qr_code" class="guv-qr-box" aria-label="<?php esc_attr_e( 'Guest upload QR code', 'guest-upload-vault' ); ?>"></div>
				<p>
					<a class="button" id="guv_view_qr" href="#" target="_blank" rel="noopener" style="pointer-events: none; opacity: 0.7;">
						<?php esc_html_e( 'View QR Code', 'guest-upload-vault' ); ?>
					</a>
					<a class="button button-secondary" id="guv_download_qr" href="#" download="guest-upload-vault-qr.png" style="pointer-events: none; opacity: 0.7;">
						<?php esc_html_e( 'Download QR PNG', 'guest-upload-vault' ); ?>
					</a>
				</p>
				<p class="description">
					<?php esc_html_e( 'QR code is generated locally in your browser. The protected upload URL is not sent to third-party QR services.', 'guest-upload-vault' ); ?>
				</p>
				<p class="description">
					<?php esc_html_e( 'Print tip: open the QR image and print at high quality for guest signage.', 'guest-upload-vault' ); ?>
				</p>
				<div class="guv-help-box">
					<p><strong><?php esc_html_e( 'How to use the QR code', 'guest-upload-vault' ); ?></strong></p>
					<ol>
						<li><?php esc_html_e( 'Set the Upload Page URL and click Save Settings to generate the current guest link.', 'guest-upload-vault' ); ?></li>
						<li><?php esc_html_e( 'Use Copy Link for messages, or print the QR code and place it at your event.', 'guest-upload-vault' ); ?></li>
						<li><?php esc_html_e( 'Guests scan the QR code on iPhone or Android and upload from camera, photo library/gallery, or files.', 'guest-upload-vault' ); ?></li>
						<li><?php esc_html_e( 'If you regenerate the guest link/token, all older printed QR codes stop working and must be reprinted.', 'guest-upload-vault' ); ?></li>
					</ol>
				</div>
			<?php else : ?>
				<p><?php esc_html_e( 'Set an Upload Page URL to generate the protected link.', 'guest-upload-vault' ); ?></p>
			<?php endif; ?>
		</div>
	<?php elseif ( 'media' === $current_tab ) : ?>
		<div class="guv-settings-card">
			<h2><?php esc_html_e( 'Collected Media', 'guest-upload-vault' ); ?></h2>
			<?php if ( $media_overview_generated_at > 0 ) : ?>
				<p class="description">
					<?php
					printf(
						/* translators: 1: last updated timestamp, 2: cache source label, 3: cache ttl in seconds */
						esc_html__( 'Overview last updated: %1$s. Source: %2$s. Cache TTL: %3$d seconds.', 'guest-upload-vault' ),
						esc_html( wp_date( 'Y-m-d H:i:s', (int) $media_overview_generated_at ) ),
						$media_overview_from_cache ? esc_html__( 'Cached', 'guest-upload-vault' ) : esc_html__( 'Live', 'guest-upload-vault' ),
						(int) $media_overview_cache_ttl
					);
					?>
				</p>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( $guest_upload_vault_refresh_media_url ); ?>" class="guv-inline-refresh-form">
				<?php wp_nonce_field( 'guv_refresh_media_overview', 'guv_refresh_media_overview_nonce' ); ?>
				<button type="submit" class="button"><?php esc_html_e( 'Refresh Diagnostics', 'guest-upload-vault' ); ?></button>
			</form>
			<?php if ( empty( $uploads ) ) : ?>
				<p><?php esc_html_e( 'No uploads yet.', 'guest-upload-vault' ); ?></p>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( $guest_upload_vault_bulk_download_url ); ?>" id="guv_media_bulk_form">
					<?php wp_nonce_field( 'guv_bulk_download', 'guv_bulk_download_nonce' ); ?>
					<?php wp_nonce_field( 'guv_bulk_delete', 'guv_bulk_delete_nonce' ); ?>

					<div class="guv-media-toolbar">
						<button type="submit" class="button button-primary" name="guv_scope" value="selected" data-guv-requires-selection="1">
							<?php esc_html_e( 'Download Selected (ZIP)', 'guest-upload-vault' ); ?>
						</button>
						<button type="submit" class="button" name="guv_scope" value="all">
							<?php esc_html_e( 'Download All (ZIP)', 'guest-upload-vault' ); ?>
						</button>
						<button type="submit" class="button button-secondary guv-delete-button" name="guv_scope" value="selected" formaction="<?php echo esc_url( $guest_upload_vault_bulk_delete_url ); ?>" data-guv-requires-selection="1" data-guv-delete-scope="selected">
							<?php esc_html_e( 'Delete Selected', 'guest-upload-vault' ); ?>
						</button>
						<button type="submit" class="button guv-delete-button" name="guv_scope" value="all" formaction="<?php echo esc_url( $guest_upload_vault_bulk_delete_url ); ?>" data-guv-delete-scope="all">
							<?php esc_html_e( 'Delete All', 'guest-upload-vault' ); ?>
						</button>
					</div>

					<table class="widefat striped guv-media-table">
						<thead>
							<tr>
								<th class="check-column">
									<label class="screen-reader-text" for="guv_select_all_media"><?php esc_html_e( 'Select all files', 'guest-upload-vault' ); ?></label>
									<input type="checkbox" id="guv_select_all_media" />
								</th>
								<th><?php esc_html_e( 'Preview', 'guest-upload-vault' ); ?></th>
								<th><?php esc_html_e( 'Filename', 'guest-upload-vault' ); ?></th>
								<th><?php esc_html_e( 'Media Type', 'guest-upload-vault' ); ?></th>
								<th><?php esc_html_e( 'Size', 'guest-upload-vault' ); ?></th>
								<th><?php esc_html_e( 'Uploaded', 'guest-upload-vault' ); ?></th>
								<th><?php esc_html_e( 'Health', 'guest-upload-vault' ); ?></th>
								<th><?php esc_html_e( 'Actions', 'guest-upload-vault' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $uploads as $guest_upload_vault_file ) : ?>
								<?php
								$guest_upload_vault_stored_file   = isset( $guest_upload_vault_file['stored_file'] ) ? sanitize_file_name( (string) $guest_upload_vault_file['stored_file'] ) : '';
								$guest_upload_vault_is_encrypted  = GUV_Plugin::ENCRYPTED_FILE_EXT === substr( $guest_upload_vault_stored_file, -strlen( GUV_Plugin::ENCRYPTED_FILE_EXT ) );
								$guest_upload_vault_is_selectable = $guest_upload_vault_is_encrypted;
								$guest_upload_vault_media_kind    = isset( $guest_upload_vault_file['media_kind'] ) ? sanitize_key( (string) $guest_upload_vault_file['media_kind'] ) : 'file';
								$guest_upload_vault_health_status = isset( $guest_upload_vault_file['health_status'] ) ? (string) $guest_upload_vault_file['health_status'] : '';
								$guest_upload_vault_health_label  = __( 'Unknown', 'guest-upload-vault' );
								$guest_upload_vault_health_class  = 'guv-health-warning';
								$guest_upload_vault_can_download  = ! empty( $guest_upload_vault_file['can_download'] );
								$guest_upload_vault_download_url  = '';
								$guest_upload_vault_view_url      = '';

								if ( $guest_upload_vault_can_download && '' !== $guest_upload_vault_stored_file ) {
									$guest_upload_vault_download_url = wp_nonce_url(
										add_query_arg(
											array(
												'action' => 'guv_download_upload',
												'file'   => $guest_upload_vault_stored_file,
											),
											admin_url( 'admin-post.php' )
										),
										'guv_download_file_' . $guest_upload_vault_stored_file
									);
									$guest_upload_vault_view_url = wp_nonce_url(
										add_query_arg(
											array(
												'action' => 'guv_view_upload',
												'file'   => $guest_upload_vault_stored_file,
											),
											admin_url( 'admin-post.php' )
										),
										'guv_view_file_' . $guest_upload_vault_stored_file
									);
								}

								switch ( $guest_upload_vault_health_status ) {
									case 'ok':
										$guest_upload_vault_health_label = __( 'Healthy', 'guest-upload-vault' );
										$guest_upload_vault_health_class = 'guv-health-ok';
										break;
									case 'missing_metadata':
										$guest_upload_vault_health_label = __( 'Missing Metadata', 'guest-upload-vault' );
										$guest_upload_vault_health_class = 'guv-health-error';
										break;
									case 'invalid_metadata':
										$guest_upload_vault_health_label = __( 'Metadata Damaged', 'guest-upload-vault' );
										$guest_upload_vault_health_class = 'guv-health-error';
										break;
									case 'metadata_tampered':
										$guest_upload_vault_health_label = __( 'Integrity Failed', 'guest-upload-vault' );
										$guest_upload_vault_health_class = 'guv-health-error';
										break;
									case 'unsupported_key_version':
										$guest_upload_vault_health_label = __( 'Key Version Mismatch', 'guest-upload-vault' );
										$guest_upload_vault_health_class = 'guv-health-error';
										break;
									case 'decrypt_failed':
										$guest_upload_vault_health_label = __( 'Decrypt Failed', 'guest-upload-vault' );
										$guest_upload_vault_health_class = 'guv-health-error';
										break;
									case 'legacy_metadata_plaintext':
										$guest_upload_vault_health_label = __( 'Legacy Metadata', 'guest-upload-vault' );
										$guest_upload_vault_health_class = 'guv-health-warning';
										break;
									case 'legacy_plaintext':
										$guest_upload_vault_health_label = __( 'Legacy Plaintext', 'guest-upload-vault' );
										$guest_upload_vault_health_class = 'guv-health-warning';
										break;
								}

								$guest_upload_vault_type_label = __( 'File', 'guest-upload-vault' );
								if ( 'photo' === $guest_upload_vault_media_kind ) {
									$guest_upload_vault_type_label = __( 'Photo', 'guest-upload-vault' );
								} elseif ( 'video' === $guest_upload_vault_media_kind ) {
									$guest_upload_vault_type_label = __( 'Video', 'guest-upload-vault' );
								}
								?>
								<tr>
									<th class="check-column">
										<?php if ( $guest_upload_vault_is_selectable ) : ?>
											<label class="screen-reader-text" for="<?php echo esc_attr( 'guv_media_' . md5( $guest_upload_vault_stored_file ) ); ?>">
												<?php esc_html_e( 'Select media file', 'guest-upload-vault' ); ?>
											</label>
											<input
												id="<?php echo esc_attr( 'guv_media_' . md5( $guest_upload_vault_stored_file ) ); ?>"
												type="checkbox"
												name="guv_files[]"
												value="<?php echo esc_attr( $guest_upload_vault_stored_file ); ?>"
												class="guv-media-select"
											/>
										<?php else : ?>
											<span aria-hidden="true">-</span>
										<?php endif; ?>
									</th>
									<td>
										<?php if ( 'photo' === $guest_upload_vault_media_kind && $guest_upload_vault_can_download && '' !== $guest_upload_vault_view_url ) : ?>
											<img src="<?php echo esc_url( $guest_upload_vault_view_url ); ?>" alt="" loading="lazy" class="guv-media-thumb" />
										<?php elseif ( 'video' === $guest_upload_vault_media_kind ) : ?>
											<span class="dashicons dashicons-video-alt3 guv-media-icon" aria-hidden="true"></span>
										<?php else : ?>
											<span class="dashicons dashicons-media-default guv-media-icon" aria-hidden="true"></span>
										<?php endif; ?>
									</td>
									<td><?php echo esc_html( (string) $guest_upload_vault_file['name'] ); ?></td>
									<td>
										<?php echo esc_html( $guest_upload_vault_type_label ); ?>
										<?php if ( ! empty( $guest_upload_vault_file['mime_type'] ) ) : ?>
											<br /><small><?php echo esc_html( (string) $guest_upload_vault_file['mime_type'] ); ?></small>
										<?php endif; ?>
									</td>
									<td><?php echo esc_html( size_format( (int) $guest_upload_vault_file['size'] ) ); ?></td>
									<td><?php echo esc_html( wp_date( 'Y-m-d H:i', (int) $guest_upload_vault_file['modified'] ) ); ?></td>
									<td>
										<span class="guv-health-badge <?php echo esc_attr( $guest_upload_vault_health_class ); ?>"><?php echo esc_html( $guest_upload_vault_health_label ); ?></span><br />
										<small><?php echo esc_html( isset( $guest_upload_vault_file['health_message'] ) ? (string) $guest_upload_vault_file['health_message'] : '' ); ?></small>
									</td>
									<td class="guv-row-actions">
										<?php if ( $guest_upload_vault_can_download && '' !== $guest_upload_vault_view_url ) : ?>
											<a class="button button-small" href="<?php echo esc_url( $guest_upload_vault_view_url ); ?>" target="_blank" rel="noopener">
												<?php esc_html_e( 'View', 'guest-upload-vault' ); ?>
											</a>
										<?php endif; ?>
										<?php if ( $guest_upload_vault_can_download && '' !== $guest_upload_vault_download_url ) : ?>
											<a class="button button-small" href="<?php echo esc_url( $guest_upload_vault_download_url ); ?>">
												<?php esc_html_e( 'Download', 'guest-upload-vault' ); ?>
											</a>
										<?php endif; ?>
										<?php if ( $guest_upload_vault_is_encrypted ) : ?>
											<?php
											$guest_upload_vault_delete_url = wp_nonce_url(
												add_query_arg(
													array(
														'action' => 'guv_delete_upload',
														'file'   => $guest_upload_vault_stored_file,
													),
													admin_url( 'admin-post.php' )
												),
												'guv_delete_file_' . $guest_upload_vault_stored_file
											);
											?>
											<a href="<?php echo esc_url( $guest_upload_vault_delete_url ); ?>" class="button button-small guv-delete-button" data-guv-delete-scope="single">
												<?php esc_html_e( 'Delete', 'guest-upload-vault' ); ?>
											</a>
										<?php else : ?>
											<span><?php esc_html_e( 'Unavailable', 'guest-upload-vault' ); ?></span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</form>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<div class="guv-settings-card">
			<h2><?php esc_html_e( 'How this plugin works', 'guest-upload-vault' ); ?></h2>
			<p><?php esc_html_e( 'Guest Upload Vault provides a protected guest upload page for event photos and videos. Guests can only upload through the tokenized link or QR code. Uploaded files are stored encrypted in uploads/guest-upload-vault and can be managed by admins in the Media tab.', 'guest-upload-vault' ); ?></p>
			<ol class="guv-ops-list">
				<li><?php esc_html_e( 'Create a WordPress page with shortcode [guest_upload_vault].', 'guest-upload-vault' ); ?></li>
				<li><?php esc_html_e( 'Set that page URL in Settings and save.', 'guest-upload-vault' ); ?></li>
				<li><?php esc_html_e( 'Copy the protected guest link or print the QR code.', 'guest-upload-vault' ); ?></li>
				<li><?php esc_html_e( 'Guests upload photos/videos from phone camera, gallery/library, or files.', 'guest-upload-vault' ); ?></li>
				<li><?php esc_html_e( 'Use the Media tab to view health status, preview, download, or delete uploads.', 'guest-upload-vault' ); ?></li>
			</ol>
		</div>

		<div class="guv-settings-card">
			<h2><?php esc_html_e( 'Theme and page design info', 'guest-upload-vault' ); ?></h2>
			<p><?php esc_html_e( 'The visual area above the upload card (for example hero images, logo, page header, and footer) is controlled by your active theme or page builder. The plugin itself renders the protected upload form card via shortcode inside that page.', 'guest-upload-vault' ); ?></p>
		</div>

		<div class="guv-settings-card">
			<h2><?php esc_html_e( 'Pilot handoff notes', 'guest-upload-vault' ); ?></h2>
			<ul class="guv-ops-list">
				<li><?php esc_html_e( 'Backup and restore database + uploads/guest-upload-vault together to keep media decryptable.', 'guest-upload-vault' ); ?></li>
				<li><?php esc_html_e( 'Regenerating the guest token invalidates previous guest links and printed QR codes.', 'guest-upload-vault' ); ?></li>
				<li><?php esc_html_e( 'Cleanup On Uninstall runs only when the plugin is uninstalled, not when it is deactivated.', 'guest-upload-vault' ); ?></li>
				<li><?php esc_html_e( 'On shared hosting, runtime and memory limits can reduce effective max upload size.', 'guest-upload-vault' ); ?></li>
			</ul>
		</div>

		<div class="guv-settings-card">
			<h2><?php esc_html_e( 'Troubleshooting checklist', 'guest-upload-vault' ); ?></h2>
			<ul class="guv-ops-list">
				<li><?php esc_html_e( 'If guests cannot open the upload page, check that the URL includes a valid guv_token.', 'guest-upload-vault' ); ?></li>
				<li><?php esc_html_e( 'If uploads fail, verify allowed file type and max file size limits.', 'guest-upload-vault' ); ?></li>
				<li><?php esc_html_e( 'If media cannot be downloaded, refresh diagnostics and check file health status in the Media tab.', 'guest-upload-vault' ); ?></li>
				<li><?php esc_html_e( 'If decryption errors occur after migration, verify database/options and uploads were restored together.', 'guest-upload-vault' ); ?></li>
			</ul>
		</div>
	<?php endif; ?>
</div>
