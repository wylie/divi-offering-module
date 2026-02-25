# Release Notes

## v1.0.22 — 2026-02-25

### Fixed
- Corrected module fallback selection behavior to avoid using non-selection metadata values (for example `program_id_last_edited`) as the active offering.

### Technical
- Updated `DOM_Program_Card_Module` to evaluate candidates by resolvable offering ID and only accept values that map to a valid `dom_program` post.

## v1.0.21 — 2026-02-25

### Fixed
- Prevented selected Offering values from being dropped on frontend renders when Divi stores or validates select values differently per page/module context.

### Technical
- Updated `DOM_Programs::get_program_options()` to provide consistent option availability outside admin.
- Added robust selection key/value fallbacks in `DOM_Program_Card_Module::render()`.

## v1.0.20 — 2026-02-25

### Fixed
- Improved Offering selection resolution so cards render reliably when Divi stores module values in alternate formats (ID, prefixed key, title, slug, URL/path, or delimited strings).

### Technical
- Refactored selection parsing in `DOM_Programs::resolve_program_selection()` to normalize and test multiple candidate values before falling back.

## v1.0.19 — 2026-02-25

### Fixed
- Corrected plugin version metadata so WordPress recognizes this build as a newer release.
- Switched release packaging guidance to preserve the plugin root folder, preventing uploads from installing as a separate plugin entry.

### Technical
- Updated plugin header `Version` and `DOM_VERSION` to `1.0.19` in `divi-offering-module.php`.
- Recommended packaging command now uses archive root folder preservation.

## v1.0.16 — 2026-02-25

### Fixed
- Corrected desktop offering cards where image-left layouts could leave an empty gray area below the image.
- Ensured the image wrapper and image fill the full stretched column height so image height consistently follows the text side.

### Technical
- Updated desktop layout rules in `assets/css/dom-module.css` (`.left` and `.dom-media`) to use flex-based height fill.
- Updated plugin version metadata to `1.0.16` in `divi-offering-module.php`.

## v1.0.15 — 2026-02-25

### Fixed
- Updated desktop offering card image behavior so image height follows the non-image/text side height instead of using a fixed minimum image height.

### Technical
- Updated layout rules in `assets/css/dom-module.css` to remove the desktop fixed image min-height and allow equal-height column stretching.
- Updated plugin version metadata to `1.0.15` in `divi-offering-module.php`.

## v1.0.14 — 2026-02-25

### Fixed
- Included the pill class namespace update in the release (`location` → `dom-location`) to prevent duplicated pill text from external CSS collisions.

### Technical
- Shipped updates in `includes/class-dom-program-card-module.php` and `assets/css/dom-module.css`.
- Updated plugin version metadata to `1.0.14` in `divi-offering-module.php`.

## v1.0.13 — 2026-02-25

### Fixed
- Resolved duplicate text appearing in the top pill by avoiding class-name collisions with generic `location` styles.

### Technical
- Updated pill class from `location` to `dom-location` in module markup and CSS.
- Updated plugin version metadata to `1.0.13` in `divi-offering-module.php`.

## v1.0.12 — 2026-02-25

### Added
- Added a per-Offering **Mobile Image Position** setting in Add/Edit Offering (Center, Top, Bottom, Left, Right).

### Improved
- Applied each Offering's mobile image position only on mobile breakpoints.
- Kept desktop image positioning centered.

### Technical
- Updated plugin version metadata to `1.0.12` in `divi-offering-module.php`.

## v1.0.11 — 2026-02-25

### Improved
- Reduced mobile card image height to avoid overly tall images on smaller viewports.
- Increased offering pill text size from `14px` to `21px`.

### Technical
- Updated plugin version metadata to `1.0.11` in `divi-offering-module.php`.

## v1.0.10 — 2026-02-25

### Improved
- Updated pill rendering to show only the selected **Type** label for cleaner output.
- Kept backward compatibility fallback to legacy badge text when Type is not selected.

### Technical
- Updated plugin version metadata to `1.0.10` in `divi-offering-module.php`.

## v1.0.9 — 2026-02-25

### Added
- Added configurable Offering Types under **Offerings > Types**.
- Added a **Type** select field to **Add/Edit Offering** populated from the configurable Types list.

### Improved
- Made the top pill text dynamic by rendering **Type + Offering Title** (with legacy badge fallback).
- Updated CTA button text alignment to left for closer visual parity with the requested design.

### Technical
- Added Type option storage and sanitization helpers in `DOM_Programs`.
- Preserved backward compatibility by falling back to legacy `Badge Label` meta when Type is not selected.

## v1.0.8 — 2026-02-25

### Improved
- Converted module markup to align more closely with the legacy Offering block structure (`offering`, `side`, `info`, `location`, `main-details`, `link-ext`).
- Updated styling to match the legacy layout/typography, including two-column details and legacy button hover behavior.
- Reintroduced Divi `ETmodules` icon mapping on detail rows for closer parity with the old design system.

### Technical
- Updated plugin version metadata to `1.0.8` in `divi-offering-module.php`.
- Tagged release as `v1.0.8`.

## v1.0.7 — 2026-02-25

### Fixed
- Restored offering detail icons (price, frequency, time, ages, schedule) by switching back to inline SVG icon rendering.
- Removed dependency on external icon font loading for card detail icons.

### Improved
- Adjusted card content spacing to better match prior visual style.
- Tuned subtitle spacing and detail grid spacing for closer parity with older layouts.
- Refined divider and CTA button sizing/spacing for improved visual balance.

### Technical
- Updated plugin version metadata to `1.0.7` in `divi-offering-module.php`.
- Tagged release as `v1.0.7`.
