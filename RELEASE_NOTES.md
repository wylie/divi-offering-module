# Release Notes

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
