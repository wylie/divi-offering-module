# Divi Offering Module

A WordPress plugin that provides:

- **Programs** custom post type for non-technical content updates
- **Program Card (Dynamic)** Divi module that renders a card from selected Program data

## Editable fields (WP Admin > Programs)

- Featured Image
- Type (select)
- Program title (post title)
- Subtitle/tagline
- Price
- Time
- Schedule
- Frequency
- Age range
- Description
- Button text + URL
- Image side (left/right)
- Badge color
- Card background color
- Accent color
- Button border color

## Manage Type options

- Go to **Offerings > Types**
- Add one Type label per line (for example: Activity, Membership, Camp)
- Save to update the **Type** dropdown shown in **Add Offering**

## Install

1. Zip this folder: `divi-offering-module`
2. In WordPress, go to **Plugins > Add New > Upload Plugin**
3. Activate plugin
4. Ensure Divi is active
5. Add/edit a Program under **Programs**
6. In Divi Builder, add **Program Card (Dynamic)** and choose your Program

## Local development

1. Edit plugin files directly in this folder.
2. Recreate the ZIP after changes.
3. Upload ZIP to your WordPress environment.

## Deploy to remote WordPress

1. In terminal, from the parent directory, run:
   - `cd divi-offering-module && git archive --format=zip --prefix=divi-offering-module/ --output ../divi-offering-module.zip HEAD`
2. In remote WP Admin, go to **Plugins > Add New > Upload Plugin**.
3. Upload ZIP and activate (or replace existing version).

## Notes

- If no Program is selected, module shows a placeholder message.
- If no featured image exists, the media panel is hidden.
