# Changelog

All notable changes to `wedding-gallery` are documented in this file.

## [1.0.0] - 2026-03-16

### Changed
- Bumped plugin version and release metadata to `1.0.0`.
- Completed WordPress.org compliance cleanup for text domain format, readme metadata, hidden files, and prefixed template/uninstall variables.
- Switched translation runtime/source filenames to `wedding-gallery-*` to match text domain.
- Enforced strict token-based upload page access and added no-cache behavior for protected upload rendering.

## [0.3.0] - 2026-03-15

### Added
- Token-protected guest upload link generation with admin token rotation.
- Local QR code generation inside WordPress admin (no external QR API).
- Mobile-first guest upload UI with large primary action, multiple file selection, and upload progress feedback.
- Admin diagnostics and file health indicators for metadata, decryption, key-version, and runtime-limit issues.
- Metadata integrity verification (HMAC) and reduced plaintext metadata exposure.
- German translations (`de_DE`) for current frontend/admin user-facing strings.

### Changed
- Encrypted media storage uses a dedicated plugin encryption key stored in plugin settings.
- Upload size handling now clamps configured limits to conservative runtime-safe limits.
- Legacy plaintext media download path is disabled; legacy files are flagged for admin action only.
- Uninstall behavior now supports explicit cleanup opt-in for uploaded guest media.

### Security
- Strengthened metadata tamper detection and corruption signaling.
- Preserved admin-gated download flow for encrypted uploads.

## [0.1.0] - 2026-03-14

### Added
- Initial MVP shortcode upload flow, file validation, nonce protection, and admin file listing/download.
