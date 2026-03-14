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
 * - string $notice
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Wedding Gallery', 'wedding-gallery' ); ?></h1>

	<?php if ( 'saved' === $notice ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Settings saved.', 'wedding-gallery' ); ?></p>
		</div>
	<?php elseif ( 'saved_clamped' === $notice ) : ?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %d: effective max upload in MB */
					esc_html__( 'Settings saved. Max upload size was clamped to %d MB to match server/runtime safety limits.', 'wedding-gallery' ),
					(int) $effective_max_upload_mb
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $upload_limits['is_clamped'] ) ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: 1: configured MB, 2: runtime cap MB */
					esc_html__( 'Current configured max is %1$d MB, but this server can safely handle up to %2$d MB. Uploads are limited to the lower value.', 'wedding-gallery' ),
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
					esc_html__( 'Detected %d legacy plaintext file(s). They are no longer served by this plugin. Migrate or remove them from uploads/wedding-gallery.', 'wedding-gallery' ),
					(int) $legacy_plaintext_count
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( empty( $key_status['healthy'] ) ) : ?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'Encryption key status is unhealthy. Media uploads/downloads may fail until the key configuration is repaired.', 'wedding-gallery' ); ?></p>
		</div>
	<?php endif; ?>

	<h2><?php esc_html_e( 'Settings', 'wedding-gallery' ); ?></h2>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<input type="hidden" name="action" value="wg_save_settings" />
		<?php wp_nonce_field( 'wg_save_settings', 'wg_save_settings_nonce' ); ?>

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<label for="upload_page_url"><?php esc_html_e( 'Upload Page URL', 'wedding-gallery' ); ?></label>
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
								esc_html__( 'Add shortcode %s to this page.', 'wedding-gallery' ),
								'[wedding_gallery_upload]'
							);
							?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="access_token"><?php esc_html_e( 'Access Token', 'wedding-gallery' ); ?></label>
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
								<?php esc_html_e( 'Rotate Token', 'wedding-gallery' ); ?>
							</button>
						</p>
					</td>
				</tr>
					<tr>
						<th scope="row">
							<label for="max_upload_mb"><?php esc_html_e( 'Max Upload Size (MB)', 'wedding-gallery' ); ?></label>
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
									esc_html__( 'Allowed file types: %1$s. Effective per-file limit: %2$d MB.', 'wedding-gallery' ),
									esc_html( $allowed_text ),
									(int) $effective_max_upload_mb
								);
								?>
							</p>
							<p class="description">
								<?php
								printf(
									/* translators: 1: upload_max_filesize MB, 2: post_max_size MB, 3: memory_limit MB, 4: memory-safe MB */
									esc_html__( 'Runtime limits (MB): upload_max_filesize=%1$d, post_max_size=%2$d, memory_limit=%3$d, memory-safe ceiling=%4$d.', 'wedding-gallery' ),
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
							<label for="cleanup_on_uninstall"><?php esc_html_e( 'Cleanup On Uninstall', 'wedding-gallery' ); ?></label>
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
								<?php esc_html_e( 'Yes, permanently delete wedding media + metadata from uploads/wedding-gallery when uninstalling this plugin.', 'wedding-gallery' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Leave unchecked to keep files on disk after uninstall.', 'wedding-gallery' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Encryption Key', 'wedding-gallery' ); ?></th>
						<td>
							<p>
								<?php
								printf(
									/* translators: 1: healthy/unhealthy, 2: version, 3: key fingerprint */
									esc_html__( 'Status: %1$s | Version: %2$d | Fingerprint: %3$s', 'wedding-gallery' ),
									! empty( $key_status['healthy'] ) ? esc_html__( 'Healthy', 'wedding-gallery' ) : esc_html__( 'Problem', 'wedding-gallery' ),
									(int) $key_status['key_version'],
									esc_html( (string) $key_status['fingerprint'] )
								);
								?>
							</p>
							<p class="description">
								<?php esc_html_e( 'Backup requirement: keep database/plugin options and uploads/wedding-gallery together in the same backup/restore set. Restoring only one can make media undecryptable.', 'wedding-gallery' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

		<?php submit_button( __( 'Save Settings', 'wedding-gallery' ) ); ?>
	</form>

	<h2><?php esc_html_e( 'Protected Upload URL (QR Target)', 'wedding-gallery' ); ?></h2>
	<?php if ( ! empty( $protected_upload_url ) ) : ?>
		<p>
			<code><?php echo esc_html( $protected_upload_url ); ?></code>
		</p>
	<?php else : ?>
		<p><?php esc_html_e( 'Set an Upload Page URL to generate the protected link.', 'wedding-gallery' ); ?></p>
	<?php endif; ?>

	<h2><?php esc_html_e( 'Uploaded Files', 'wedding-gallery' ); ?></h2>
	<?php if ( empty( $uploads ) ) : ?>
		<p><?php esc_html_e( 'No uploads yet.', 'wedding-gallery' ); ?></p>
	<?php else : ?>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Filename', 'wedding-gallery' ); ?></th>
					<th><?php esc_html_e( 'Type', 'wedding-gallery' ); ?></th>
					<th><?php esc_html_e( 'Size', 'wedding-gallery' ); ?></th>
					<th><?php esc_html_e( 'Uploaded', 'wedding-gallery' ); ?></th>
					<th><?php esc_html_e( 'Action', 'wedding-gallery' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $uploads as $file ) : ?>
					<tr>
						<td><?php echo esc_html( $file['name'] ); ?></td>
						<td><?php echo esc_html( $file['mime_type'] ); ?></td>
						<td><?php echo esc_html( size_format( (int) $file['size'] ) ); ?></td>
						<td><?php echo esc_html( wp_date( 'Y-m-d H:i', (int) $file['modified'] ) ); ?></td>
						<td>
							<?php
								$download_url = wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'wg_download_upload',
											'file'   => $file['stored_file'],
										),
										admin_url( 'admin-post.php' )
									),
									'wg_download_file_' . $file['stored_file']
								);
							?>
							<a class="button button-secondary" href="<?php echo esc_url( $download_url ); ?>">
								<?php esc_html_e( 'Download', 'wedding-gallery' ); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
