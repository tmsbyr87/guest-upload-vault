# Wedding Gallery Product / Technical Spec

## 1. Product Summary

Wedding Gallery is a WordPress plugin that lets wedding guests upload photos and videos through a protected page opened from a QR code.

The plugin should feel native to WordPress:
- guests use a simple mobile-friendly upload page
- admins configure the experience from WordPress admin
- uploads are stored in a dedicated plugin-owned uploads directory
- security and validation follow WordPress-native patterns

## 2. Problem Statement

Wedding guests take many photos and videos during an event, but collecting them afterward is fragmented and unreliable. The goal is to provide a low-friction upload experience that works instantly from a QR code without requiring guest accounts.

## 3. Goals

- Make guest uploads easy on mobile devices.
- Protect the upload page from casual abuse.
- Store uploaded media in a dedicated plugin-owned directory under the WordPress uploads base path.
- Give site admins a simple setup and moderation workflow.
- Keep the MVP narrow enough for a first production release.

## 4. Non-Goals for MVP

- Public gallery browsing
- Guest login/accounts
- Multi-event event management
- Advanced moderation dashboard
- Video transcoding or compression pipeline
- Cloud storage integration
- Social sharing, tagging, AI processing, or face recognition

## 5. Primary Users

### Guests

Guests scan a QR code, open a protected upload page, and submit photos or videos from a phone.

### Admins

Admins configure the plugin, generate or manage the protected upload link, and review resulting uploads in WordPress.

## 6. Core User Flow

1. Admin installs and activates the plugin.
2. Admin configures plugin settings.
3. Admin creates or selects a WordPress page that contains the upload UI.
4. Plugin generates a protected QR-code destination URL for that page.
5. Guest scans the QR code and opens the protected page.
6. Guest selects files and submits them.
7. Plugin validates the request and stores valid files in the dedicated upload directory.
8. Admin reviews uploads in the plugin admin area.

## 7. Recommended MVP Scope

### Included

- One protected guest upload page
- Shortcode-based frontend rendering
- REST endpoint for upload submission
- Image uploads
- Limited video uploads
- WordPress admin settings page
- Dedicated storage under `/wp-content/uploads/wedding-gallery/`
- Basic moderation state stored in plugin-managed upload records
- QR-target URL display for admins

### Recommended MVP Constraints

- Support one wedding/event per site for the first release
- Support images: JPG, JPEG, PNG, WEBP
- Support video: MP4 only
- Allow multiple image uploads per request
- Allow one video per request
- Apply strict upload size limits
- Store uploads locally under `/wp-content/uploads/wedding-gallery/`

## 8. Functional Requirements

### Frontend Upload Experience

The guest-facing page must:
- render on a normal WordPress page
- work well on mobile devices
- explain what can be uploaded
- support selecting multiple images
- support selecting a single video
- show validation and success/error messages clearly
- optionally collect a guest display name
- include a consent/permission confirmation checkbox

### Protection Model

The upload page must not be openly usable without the protected access mechanism.

The MVP should use:
- a long random event access token embedded in the QR-code URL
- server-side token validation before accepting uploads
- WordPress nonce verification for request integrity

Nonce protection must not be treated as the primary access-control layer.

### Upload Processing

The plugin must:
- validate allowed file types on the server
- validate file extension and actual file type
- enforce upload size limits
- limit file counts per request
- reject unsupported formats
- write accepted files into `/wp-content/uploads/wedding-gallery/`
- store plugin-specific metadata in plugin-managed upload records

### Admin Experience

The plugin admin area must allow an administrator to:
- set the wedding/event name
- select or identify the upload page
- generate, view, and rotate the access token
- configure allowed file types
- configure upload size guidance
- enable or disable moderation mode
- view the QR destination URL

## 9. WordPress-Native Architecture

### Recommended Frontend Pattern

Use a shortcode for the guest upload interface.

Reasoning:
- it is the most practical MVP integration with the WordPress editor
- it lets admins place the experience on a normal page
- it avoids unnecessary custom page routing complexity
- it keeps the content and layout manageable through WordPress

A block can be added later, but is not required for MVP.

### Recommended Submission Pattern

Use a custom WordPress REST API endpoint for upload submissions.

Reasoning:
- cleaner separation between rendered page and upload handling
- structured JSON responses for frontend validation states
- easier future extensibility than an admin-ajax-only design
- aligns with modern WordPress plugin patterns

### Recommended Admin Pattern

Use:
- a plugin settings page in WordPress admin
- Settings API for persistent options
- capability checks for administrative access
- admin assets loaded only on the plugin screens

### Recommended Media Pattern

Do not use the WordPress Media Library for guest uploads.

Instead, store files in a dedicated directory:
- `/wp-content/uploads/wedding-gallery/`

Use WordPress-native upload path helpers and filesystem-safe handling where helpful, but do not create attachment posts for guest uploads.

This approach ensures:
- guest uploads remain isolated from the main Media Library
- file organization is predictable for this plugin
- admin review and moderation can be designed around wedding-specific workflows

## 10. Data Model

### Site-Level Options

Store plugin configuration in WordPress options, including:
- event name
- upload page identifier
- active guest access token
- allowed mime types/file classes
- max file size guidance
- moderation enabled flag

### Upload Records

Store plugin-managed metadata for each uploaded file, such as:
- source = wedding_gallery
- event name or event identifier
- stored filename and relative path
- original filename
- mime type
- file size
- upload token reference or token fingerprint
- guest display name if collected
- moderation state
- upload timestamp

Because uploads are not stored as Media Library attachments, the plugin should maintain its own upload records.

The preferred approach is a small plugin-managed database table for upload records.

## 11. Security Requirements

### Access Control

Because guests are unauthenticated users, the design must not rely on logged-in WordPress permissions alone.

Required controls:
- long random protected URL token
- token validation on every upload request
- nonce validation for request integrity
- capability checks on admin screens

### Input and File Validation

Required controls:
- sanitize all text inputs
- escape all rendered output
- allowlist supported mime types
- validate file extension and actual file type server-side
- reject disallowed or malformed files

### Abuse Controls

The MVP should include lightweight abuse mitigation:
- rate limiting per IP or token window where practical
- request-size and file-count caps
- optional token rotation by admin
- optional ability to disable uploads temporarily

### Operational Limits

Video uploads are the highest-risk part of the MVP because hosting limits vary widely.

The plugin should clearly communicate:
- supported file types
- upload size limits
- that very large videos may fail depending on hosting constraints

## 12. Shortcode vs Custom Route Decision

### Shortcode for page rendering

Recommended for MVP.

Benefits:
- easiest editor workflow
- WordPress-native content placement
- low implementation complexity

Tradeoff:
- presentation still lives inside a standard WordPress page rather than a fully custom app shell

### Custom route for page rendering

Not recommended for MVP.

Benefits:
- more control over request lifecycle and page rendering

Tradeoff:
- more complexity
- less native content-editor integration
- unnecessary for the initial product scope

### Final Recommendation

Use both patterns selectively:
- shortcode for rendering the protected guest upload UI
- REST route for form submission and upload processing

## 13. Risks

### Anonymous upload abuse

If the QR-code URL is shared beyond invited guests, the endpoint could be abused.

Mitigation:
- strong random tokens
- token rotation
- moderation mode
- lightweight rate limiting

### Video upload reliability

Large video uploads may fail on shared hosting or weak mobile connections.

Mitigation:
- MP4-only support
- conservative size limits
- one video per request
- clear UX messaging

### Storage growth

Guest media can consume disk space quickly.

Mitigation:
- admin guidance
- size limits
- file count limits
- future offload strategy outside MVP

### Security of file uploads

File uploads are a sensitive attack surface.

Mitigation:
- WordPress-native upload handling
- strict server-side validation
- limited mime allowlist
- no trust in client-provided type data

### Poor mobile UX

Guests will mainly upload from phones in inconsistent network conditions.

Mitigation:
- minimal form fields
- mobile-first design
- short clear error messages
- simple success state

## 14. Best Implementation Approach

The best MVP implementation approach is:
- WordPress plugin with modular internal structure
- shortcode for the guest-facing page
- custom REST endpoint for uploads
- settings page built with Settings API
- dedicated storage under `/wp-content/uploads/wedding-gallery/`
- plugin-managed upload records for file state and moderation
- token-protected QR-code URL plus nonce verification

This approach is preferred because it:
- stays aligned with WordPress conventions
- minimizes custom infrastructure
- reduces maintenance complexity
- supports future expansion into blocks, moderation tools, and multi-event support

## 15. Suggested Internal Plugin Modules

The implementation should be separated conceptually into modules such as:
- bootstrap/plugin registration
- shortcode rendering
- REST upload controller
- admin settings controller
- file storage service
- upload records repository
- security and validation helpers
- QR/access token management

This keeps the plugin maintainable without requiring a heavy framework.

## 16. Phase 2 Opportunities

After MVP validation, likely next steps are:
- Gutenberg block instead of or alongside shortcode
- multiple weddings/events
- dedicated admin upload review screen
- storage offload to cloud media services
- resumable or chunked uploads for large videos
- public or private gallery views

## 17. Source Basis

This spec is based on WordPress-native guidance and APIs around:
- Shortcode API
- REST API custom endpoints and permission callbacks
- REST authentication and nonce use
- Settings API
- admin menu and scoped admin asset loading
- WordPress upload path and file handling
- server-side file type validation
- WordPress security practices for sanitizing and escaping

Primary references:
- https://developer.wordpress.org/apis/shortcode/
- https://developer.wordpress.org/plugins/shortcodes/
- https://developer.wordpress.org/rest-api/
- https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
- https://developer.wordpress.org/rest-api/extending-the-rest-api/routes-and-endpoints/
- https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/
- https://developer.wordpress.org/plugins/settings/settings-api/
- https://developer.wordpress.org/reference/functions/add_menu_page/
- https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
- https://developer.wordpress.org/apis/security/nonces/
- https://developer.wordpress.org/apis/security/sanitizing/
- https://developer.wordpress.org/reference/functions/wp_handle_upload/
- https://developer.wordpress.org/reference/functions/wp_upload_dir/
- https://developer.wordpress.org/reference/functions/wp_mkdir_p/
- https://developer.wordpress.org/reference/functions/wp_check_filetype_and_ext/
- https://developer.wordpress.org/reference/functions/check_upload_mimes/
