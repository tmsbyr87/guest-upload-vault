<?php
/**
 * Frontend upload template.
 *
 * Variables available:
 * - bool   $is_authorized
 * - string $status
 * - string $message
 * - int    $max_upload_mb
 * - string $action_url
 * - string $redirect_url
 * - string $allowed_text
 * - string $authorized_token
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$guest_upload_vault_status_class = 'success' === $status ? 'guv-alert-success' : 'guv-alert-error';
$guest_upload_vault_status_title = 'success' === $status ? __( 'Thank you, upload complete.', 'guest-upload-vault' ) : __( 'Upload could not be completed.', 'guest-upload-vault' );
?>
<style>
.guv-upload-wrap {
	max-width: 620px;
	margin: 0 auto;
	padding-top: calc(16px + env(safe-area-inset-top));
	padding-right: max(16px, env(safe-area-inset-right));
	padding-bottom: calc(20px + env(safe-area-inset-bottom));
	padding-left: max(16px, env(safe-area-inset-left));
	font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
	color: #2d2433;
	font-size: 16px;
	line-height: 1.5;
}

.guv-upload-card {
	background: linear-gradient(180deg, #fffdf9 0%, #fff7f8 100%);
	border: 1px solid #f1dde2;
	border-radius: 20px;
	padding: 24px 18px;
	box-shadow: 0 10px 28px rgba(71, 27, 43, 0.08);
}

.guv-upload-title {
	margin: 0 0 8px;
	font-size: clamp(1.55rem, 6vw, 2rem);
	line-height: 1.14;
	letter-spacing: -0.01em;
	color: #4f2a3a;
}

.guv-upload-subtitle {
	margin: 0 0 22px;
	color: #6b5963;
	font-size: clamp(1rem, 4.3vw, 1.12rem);
	line-height: 1.5;
}

.guv-alert {
	border-radius: 14px;
	padding: 14px 16px;
	margin-bottom: 16px;
	font-size: 1rem;
	line-height: 1.5;
}

.guv-alert strong {
	display: block;
	margin-bottom: 6px;
	font-size: 1.02rem;
}

.guv-alert-success {
	background: #effaf1;
	border: 1px solid #b7e6c0;
	color: #1f5b2d;
}

.guv-alert-error {
	background: #fff3f2;
	border: 1px solid #f4c7c2;
	color: #822a27;
}

.guv-file-input {
	position: absolute;
	width: 1px;
	height: 1px;
	opacity: 0;
	pointer-events: none;
}

.guv-picker-btn {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 100%;
	min-height: 58px;
	padding: 14px 16px;
	border: 0;
	border-radius: 14px;
	text-align: center;
	font-size: clamp(1.08rem, 4.7vw, 1.2rem);
	font-weight: 700;
	line-height: 1.2;
	letter-spacing: 0.01em;
	background: #a33b63;
	color: #ffffff;
	cursor: pointer;
	box-sizing: border-box;
	-webkit-tap-highlight-color: transparent;
	touch-action: manipulation;
	transition: background-color 0.2s ease, transform 0.08s ease, box-shadow 0.2s ease;
}

.guv-picker-btn:hover,
.guv-picker-btn:focus-visible {
	background: #8e3258;
	box-shadow: 0 0 0 3px rgba(163, 59, 99, 0.22);
}

.guv-picker-btn:active {
	transform: translateY(1px);
}

.guv-picker-btn.is-disabled {
	opacity: 0.7;
	cursor: not-allowed;
	pointer-events: none;
}

.guv-file-summary {
	margin: 14px 2px 16px;
	font-size: 1rem;
	line-height: 1.45;
	color: #5f4f58;
	word-break: break-word;
}

.guv-hint-list {
	margin: 0 0 16px;
	padding: 14px 16px;
	background: rgba(255, 255, 255, 0.75);
	border: 1px solid #ecd8de;
	border-radius: 14px;
	font-size: 0.99rem;
	line-height: 1.55;
	color: #5b4b54;
}

.guv-hint-list p {
	margin: 0 0 8px;
}

.guv-hint-list .guv-hint-primary {
	font-weight: 700;
	color: #4f2a3a;
}

.guv-hint-list p:last-child {
	margin-bottom: 0;
}

.guv-submit-btn {
	width: 100%;
	min-height: 58px;
	padding: 14px 16px;
	border: 0;
	border-radius: 14px;
	background: #2c6f56;
	color: #ffffff;
	font-size: clamp(1.1rem, 4.9vw, 1.22rem);
	line-height: 1.2;
	letter-spacing: 0.01em;
	font-weight: 700;
	cursor: pointer;
	-webkit-tap-highlight-color: transparent;
	touch-action: manipulation;
	transition: background-color 0.2s ease, transform 0.08s ease, box-shadow 0.2s ease;
}

.guv-submit-btn:hover,
.guv-submit-btn:focus-visible {
	background: #245c47;
	box-shadow: 0 0 0 3px rgba(44, 111, 86, 0.22);
}

.guv-submit-btn:active {
	transform: translateY(1px);
}

.guv-submit-btn[disabled] {
	opacity: 0.7;
	cursor: progress;
}

.guv-progress-wrap {
	margin-top: 16px;
	padding: 14px 16px;
	background: #ffffff;
	border: 1px solid #ecd8de;
	border-radius: 14px;
}

.guv-progress-bar-track {
	position: relative;
	height: 12px;
	border-radius: 999px;
	background: #f3e6ea;
	overflow: hidden;
}

.guv-progress-bar-fill {
	position: absolute;
	top: 0;
	left: 0;
	height: 100%;
	width: 0;
	border-radius: 999px;
	background: linear-gradient(90deg, #c04d77 0%, #8c3658 100%);
	transition: width 0.2s ease;
}

.guv-progress-text {
	margin: 10px 0 0;
	font-size: 0.98rem;
	line-height: 1.45;
	color: #5f4f58;
}

@media (max-width: 380px) {
	.guv-upload-wrap {
		padding-top: calc(12px + env(safe-area-inset-top));
		padding-right: max(12px, env(safe-area-inset-right));
		padding-bottom: calc(16px + env(safe-area-inset-bottom));
		padding-left: max(12px, env(safe-area-inset-left));
	}

	.guv-upload-card {
		padding: 20px 14px;
	}
}

@media (min-width: 680px) {
	.guv-upload-wrap {
		padding-top: 24px;
		padding-right: 24px;
		padding-bottom: 24px;
		padding-left: 24px;
	}

	.guv-upload-card {
		padding: 28px 24px;
	}

	.guv-picker-btn,
	.guv-submit-btn {
		min-height: 56px;
		font-size: 1.08rem;
	}
}
</style>
<div class="guv-upload-wrap">
	<?php if ( ! $is_authorized ) : ?>
		<div class="guv-upload-card">
			<div class="guv-alert guv-alert-error">
				<strong><?php esc_html_e( 'Protected guest upload', 'guest-upload-vault' ); ?></strong>
				<?php esc_html_e( 'This page is only available through the protected event link or QR code.', 'guest-upload-vault' ); ?>
			</div>
		</div>
	<?php else : ?>
		<div class="guv-upload-card">
			<h2 class="guv-upload-title"><?php esc_html_e( 'Share your event moments', 'guest-upload-vault' ); ?></h2>
			<p class="guv-upload-subtitle"><?php esc_html_e( 'Select photos or videos from your phone and upload them in one step.', 'guest-upload-vault' ); ?></p>

			<?php if ( ! empty( $status ) && ! empty( $message ) ) : ?>
				<div class="guv-alert <?php echo esc_attr( $guest_upload_vault_status_class ); ?>" role="status" aria-live="polite">
					<strong><?php echo esc_html( $guest_upload_vault_status_title ); ?></strong>
					<?php echo esc_html( $message ); ?>
				</div>
			<?php endif; ?>

			<div id="guv-client-alert" class="guv-alert guv-alert-error" style="display:none;" role="alert" aria-live="assertive"></div>

			<form id="guv-upload-form" action="<?php echo esc_url( $action_url ); ?>" method="post" enctype="multipart/form-data" aria-busy="false">
				<input type="hidden" name="action" value="guv_upload" />
				<input type="hidden" id="guv_redirect_to" name="redirect_to" value="<?php echo esc_url( $redirect_url ); ?>" />
				<input type="hidden" name="<?php echo esc_attr( GUV_Plugin::TOKEN_QUERY_ARG ); ?>" value="<?php echo esc_attr( $authorized_token ); ?>" />
				<?php wp_nonce_field( 'guv_upload_action_' . $authorized_token, 'guv_upload_nonce' ); ?>

				<input
					id="guv_files"
					class="guv-file-input"
					name="guv_files[]"
					type="file"
					multiple
					required
					accept=".jpg,.jpeg,.png,.webp,.mp4,.mov,image/jpeg,image/png,image/webp,video/mp4,video/quicktime"
				/>
				<label for="guv_files" id="guv_picker_btn" class="guv-picker-btn">
					<?php esc_html_e( 'Choose Photos or Videos', 'guest-upload-vault' ); ?>
				</label>
				<p id="guv_file_summary" class="guv-file-summary">
					<?php esc_html_e( 'No files selected yet.', 'guest-upload-vault' ); ?>
				</p>

				<div class="guv-hint-list">
					<p class="guv-hint-primary">
						<?php
						printf(
							/* translators: 1: allowed file types, 2: max file size in MB */
							esc_html__( 'Allowed: %1$s | Max per file: %2$d MB', 'guest-upload-vault' ),
							esc_html( $allowed_text ),
							(int) $max_upload_mb
						);
						?>
					</p>
					<p><?php esc_html_e( 'On iPhone/Android you can choose camera, photo library/gallery, or files.', 'guest-upload-vault' ); ?></p>
					<p><?php esc_html_e( 'Tip: Long phone videos are often large and may exceed the upload size limit.', 'guest-upload-vault' ); ?></p>
					<p><?php esc_html_e( 'You can select multiple files at once.', 'guest-upload-vault' ); ?></p>
				</div>

				<button id="guv_submit_btn" class="guv-submit-btn" type="submit">
					<?php esc_html_e( 'Upload Now', 'guest-upload-vault' ); ?>
				</button>

				<div id="guv_progress_wrap" class="guv-progress-wrap" hidden>
					<div class="guv-progress-bar-track">
						<div id="guv_progress_fill" class="guv-progress-bar-fill"></div>
					</div>
					<p id="guv_progress_text" class="guv-progress-text"><?php esc_html_e( 'Preparing upload...', 'guest-upload-vault' ); ?></p>
				</div>
			</form>
		</div>
	<?php endif; ?>
</div>
