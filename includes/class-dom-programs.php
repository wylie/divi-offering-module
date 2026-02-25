<?php

if (! defined('ABSPATH')) {
    exit;
}

class DOM_Programs
{
    const POST_TYPE = 'dom_program';
    const NONCE_KEY = 'dom_program_nonce';
    const TYPE_OPTION_KEY = 'dom_offering_types';

    public static function init(): void
    {
        add_action('init', array(__CLASS__, 'register_post_type'));
        add_action('add_meta_boxes', array(__CLASS__, 'register_meta_boxes'));
        add_action('save_post_' . self::POST_TYPE, array(__CLASS__, 'save_meta'), 10, 2);
        add_action('admin_menu', array(__CLASS__, 'register_admin_menu'));
    }

    public static function register_admin_menu(): void
    {
        add_submenu_page(
            'edit.php?post_type=' . self::POST_TYPE,
            __('Offering Types', 'divi-offering-module'),
            __('Types', 'divi-offering-module'),
            'manage_options',
            'dom-offering-types',
            array(__CLASS__, 'render_types_page')
        );
    }

    public static function render_types_page(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $saved = false;

        if (
            isset($_POST['dom_types_nonce'])
            && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dom_types_nonce'])), 'dom_save_types')
        ) {
            $raw_types = isset($_POST['dom_type_labels']) ? sanitize_textarea_field(wp_unslash($_POST['dom_type_labels'])) : '';
            $labels = self::parse_type_labels($raw_types);
            update_option(self::TYPE_OPTION_KEY, $labels, false);
            $saved = true;
        }

        $type_labels = self::get_type_labels();
        $type_lines = implode("\n", $type_labels);
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Offering Types', 'divi-offering-module'); ?></h1>
            <?php if ($saved) : ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Types updated.', 'divi-offering-module'); ?></p></div>
            <?php endif; ?>
            <p><?php esc_html_e('These values populate the Type select in Add Offering. Enter one type per line.', 'divi-offering-module'); ?></p>
            <form method="post">
                <?php wp_nonce_field('dom_save_types', 'dom_types_nonce'); ?>
                <textarea name="dom_type_labels" rows="10" class="large-text code"><?php echo esc_textarea($type_lines); ?></textarea>
                <p>
                    <button type="submit" class="button button-primary"><?php esc_html_e('Save Types', 'divi-offering-module'); ?></button>
                </p>
            </form>
        </div>
        <?php
    }

    public static function register_post_type(): void
    {
        register_post_type(
            self::POST_TYPE,
            array(
                'labels' => array(
                    'name' => __('Offerings', 'divi-offering-module'),
                    'singular_name' => __('Offering', 'divi-offering-module'),
                    'add_new_item' => __('Add Offering', 'divi-offering-module'),
                    'edit_item' => __('Edit Offering', 'divi-offering-module'),
                ),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_rest' => true,
                'menu_icon' => 'dashicons-welcome-learn-more',
                'supports' => array('title', 'thumbnail'),
                'has_archive' => false,
                'rewrite' => array('slug' => 'offerings'),
            )
        );
    }

    public static function register_meta_boxes(): void
    {
        add_meta_box(
            'dom_program_details',
            __('Offering Details', 'divi-offering-module'),
            array(__CLASS__, 'render_details_metabox'),
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    public static function render_details_metabox(\WP_Post $post): void
    {
        wp_nonce_field('dom_program_save', self::NONCE_KEY);

        $meta = self::get_program_meta($post->ID);

        ?>
        <style>
            .dom-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px; }
            .dom-field { margin-bottom: 10px; }
            .dom-field label { display:block; font-weight:600; margin-bottom:6px; }
            .dom-field input[type="text"],
            .dom-field input[type="url"],
            .dom-field input[type="color"],
            .dom-field select,
            .dom-field textarea { width: 100%; }
        </style>

        <div class="dom-grid">
            <div class="dom-field">
                <label for="dom_type"><?php esc_html_e('Type', 'divi-offering-module'); ?></label>
                <select id="dom_type" name="dom_type">
                    <option value=""><?php esc_html_e('Select a Type', 'divi-offering-module'); ?></option>
                    <?php foreach (self::get_type_options() as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($meta['type'], $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="dom-field">
                <label for="dom_subtitle"><?php esc_html_e('Subtitle / Tagline', 'divi-offering-module'); ?></label>
                <input id="dom_subtitle" name="dom_subtitle" type="text" value="<?php echo esc_attr($meta['subtitle']); ?>" placeholder="Subtitle">
            </div>

            <div class="dom-field">
                <label for="dom_price"><?php esc_html_e('Price', 'divi-offering-module'); ?></label>
                <input id="dom_price" name="dom_price" type="text" value="<?php echo esc_attr($meta['price']); ?>" placeholder="How much does this cost?">
            </div>
            <div class="dom-field">
                <label for="dom_frequency"><?php esc_html_e('Frequency', 'divi-offering-module'); ?></label>
                <input id="dom_frequency" name="dom_frequency" type="text" value="<?php echo esc_attr($meta['frequency']); ?>" placeholder="Does this repeat, and at one cadence?">
            </div>

            <div class="dom-field">
                <label for="dom_time"><?php esc_html_e('Time', 'divi-offering-module'); ?></label>
                <input id="dom_time" name="dom_time" type="text" value="<?php echo esc_attr($meta['time']); ?>" placeholder="How long or what time does this occur?">
            </div>
            <div class="dom-field">
                <label for="dom_ages"><?php esc_html_e('Age Range', 'divi-offering-module'); ?></label>
                <input id="dom_ages" name="dom_ages" type="text" value="<?php echo esc_attr($meta['ages']); ?>" placeholder="Who can attend this?">
            </div>

            <div class="dom-field" style="grid-column: span 2;">
                <label for="dom_schedule"><?php esc_html_e('Schedule', 'divi-offering-module'); ?></label>
                <input id="dom_schedule" name="dom_schedule" type="text" value="<?php echo esc_attr($meta['schedule']); ?>" placeholder="When does this occur?">
            </div>

            <div class="dom-field" style="grid-column: span 2;">
                <label for="dom_description"><?php esc_html_e('Description', 'divi-offering-module'); ?></label>
                <textarea id="dom_description" name="dom_description" rows="6" placeholder="Offering description"><?php echo esc_textarea($meta['description']); ?></textarea>
            </div>

            <div class="dom-field">
                <label for="dom_button_text"><?php esc_html_e('Button Text', 'divi-offering-module'); ?></label>
                <input id="dom_button_text" name="dom_button_text" type="text" value="<?php echo esc_attr($meta['button_text']); ?>" placeholder="Learn More">
            </div>
            <div class="dom-field">
                <label for="dom_button_url"><?php esc_html_e('Button URL', 'divi-offering-module'); ?></label>
                <input id="dom_button_url" name="dom_button_url" type="url" value="<?php echo esc_url($meta['button_url']); ?>" placeholder="https://">
            </div>

            <div class="dom-field">
                <label for="dom_image_side"><?php esc_html_e('Image Side', 'divi-offering-module'); ?></label>
                <select id="dom_image_side" name="dom_image_side">
                    <option value="left" <?php selected($meta['image_side'], 'left'); ?>><?php esc_html_e('Left', 'divi-offering-module'); ?></option>
                    <option value="right" <?php selected($meta['image_side'], 'right'); ?>><?php esc_html_e('Right', 'divi-offering-module'); ?></option>
                </select>
            </div>

            <div class="dom-field">
                <label for="dom_badge_color"><?php esc_html_e('Badge Color', 'divi-offering-module'); ?></label>
                <input id="dom_badge_color" name="dom_badge_color" type="color" value="<?php echo esc_attr($meta['badge_color']); ?>">
            </div>

            <div class="dom-field">
                <label for="dom_card_bg"><?php esc_html_e('Card Background', 'divi-offering-module'); ?></label>
                <input id="dom_card_bg" name="dom_card_bg" type="color" value="<?php echo esc_attr($meta['card_bg']); ?>">
            </div>

            <div class="dom-field">
                <label for="dom_accent_color"><?php esc_html_e('Accent Color', 'divi-offering-module'); ?></label>
                <input id="dom_accent_color" name="dom_accent_color" type="color" value="<?php echo esc_attr($meta['accent_color']); ?>">
            </div>

            <div class="dom-field">
                <label for="dom_button_border_color"><?php esc_html_e('Button Border Color', 'divi-offering-module'); ?></label>
                <input id="dom_button_border_color" name="dom_button_border_color" type="color" value="<?php echo esc_attr($meta['button_border_color']); ?>">
            </div>
        </div>
        <?php
        echo '<p><em>' . esc_html__('Set featured image to control the left/right photo.', 'divi-offering-module') . '</em></p>';
    }

    public static function save_meta(int $post_id, \WP_Post $post): void
    {
        if (! isset($_POST[self::NONCE_KEY]) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE_KEY])), 'dom_program_save')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        if ($post->post_type !== self::POST_TYPE) {
            return;
        }

        $text_fields = array(
            'badge',
            'type',
            'subtitle',
            'price',
            'frequency',
            'time',
            'ages',
            'schedule',
            'button_text',
            'image_side',
        );

        foreach ($text_fields as $field) {
            $key = 'dom_' . $field;
            $val = isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';

            if ($field === 'type') {
                $allowed = array_keys(self::get_type_options());
                $val = in_array($val, $allowed, true) ? $val : '';
            }

            update_post_meta($post_id, $key, $val);
        }

        $description = isset($_POST['dom_description']) ? sanitize_textarea_field(wp_unslash($_POST['dom_description'])) : '';
        update_post_meta($post_id, 'dom_description', $description);

        $button_url = isset($_POST['dom_button_url']) ? esc_url_raw(wp_unslash($_POST['dom_button_url'])) : '';
        update_post_meta($post_id, 'dom_button_url', $button_url);

        $color_fields = array('badge_color', 'card_bg', 'accent_color', 'button_border_color');

        foreach ($color_fields as $field) {
            $key = 'dom_' . $field;
            $val = isset($_POST[$key]) ? sanitize_hex_color(wp_unslash($_POST[$key])) : '';
            update_post_meta($post_id, $key, $val ?: '');
        }
    }

    public static function get_program_meta(int $post_id): array
    {
        return array(
            'badge' => get_post_meta($post_id, 'dom_badge', true),
            'type' => get_post_meta($post_id, 'dom_type', true),
            'subtitle' => get_post_meta($post_id, 'dom_subtitle', true),
            'price' => get_post_meta($post_id, 'dom_price', true),
            'frequency' => get_post_meta($post_id, 'dom_frequency', true),
            'time' => get_post_meta($post_id, 'dom_time', true),
            'ages' => get_post_meta($post_id, 'dom_ages', true),
            'schedule' => get_post_meta($post_id, 'dom_schedule', true),
            'description' => get_post_meta($post_id, 'dom_description', true),
            'button_text' => get_post_meta($post_id, 'dom_button_text', true),
            'button_url' => get_post_meta($post_id, 'dom_button_url', true),
            'image_side' => get_post_meta($post_id, 'dom_image_side', true) ?: 'left',
            'badge_color' => get_post_meta($post_id, 'dom_badge_color', true) ?: '#9A62F9',
            'card_bg' => get_post_meta($post_id, 'dom_card_bg', true) ?: '#efefef',
            'accent_color' => get_post_meta($post_id, 'dom_accent_color', true) ?: '#AF4F27',
            'button_border_color' => get_post_meta($post_id, 'dom_button_border_color', true) ?: '#555555',
        );
    }

    public static function get_type_options(): array
    {
        $labels = self::get_type_labels();
        $options = array();

        foreach ($labels as $label) {
            $value = sanitize_title($label);

            if ($value === '' || isset($options[$value])) {
                continue;
            }

            $options[$value] = $label;
        }

        return $options;
    }

    public static function get_type_label(string $type_value): string
    {
        $options = self::get_type_options();

        return isset($options[$type_value]) ? (string) $options[$type_value] : '';
    }

    private static function get_type_labels(): array
    {
        $saved = get_option(self::TYPE_OPTION_KEY, array('Activity'));

        if (! is_array($saved)) {
            return array('Activity');
        }

        $labels = array();

        foreach ($saved as $label) {
            $label = trim((string) $label);

            if ($label === '') {
                continue;
            }

            $labels[] = $label;
        }

        return ! empty($labels) ? array_values(array_unique($labels)) : array('Activity');
    }

    private static function parse_type_labels(string $raw_types): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $raw_types) ?: array();
        $labels = array();

        foreach ($lines as $line) {
            $label = trim((string) $line);

            if ($label === '') {
                continue;
            }

            $labels[] = sanitize_text_field($label);
        }

        $labels = array_values(array_unique($labels));

        return ! empty($labels) ? $labels : array('Activity');
    }

    public static function get_program_options(): array
    {
        $posts = get_posts(array(
            'post_type' => self::POST_TYPE,
            'numberposts' => -1,
            'post_status' => is_admin() ? array('publish', 'draft', 'pending', 'private', 'future') : 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        $options = array();

        foreach ($posts as $item) {
            $options['program_' . (string) $item->ID] = $item->post_title;
        }

        return $options;
    }

    public static function resolve_program_selection(string $selected_program): int
    {
        $selected_program = trim($selected_program);

        if ($selected_program === '') {
            return 0;
        }

        if (is_numeric($selected_program)) {
            $program_id = absint($selected_program);

            return get_post_type($program_id) === self::POST_TYPE ? $program_id : 0;
        }

        if (strpos($selected_program, 'program_') === 0) {
            $program_id = absint(substr($selected_program, 8));

            return get_post_type($program_id) === self::POST_TYPE ? $program_id : 0;
        }

        if (preg_match('/program[_-](\d+)/i', $selected_program, $matches) === 1) {
            $program_id = absint($matches[1]);

            return get_post_type($program_id) === self::POST_TYPE ? $program_id : 0;
        }

        if (preg_match('/\b(\d+)\b/', $selected_program, $matches) === 1) {
            $program_id = absint($matches[1]);

            if (get_post_type($program_id) === self::POST_TYPE) {
                return $program_id;
            }
        }

        $found_by_title = get_page_by_title($selected_program, OBJECT, self::POST_TYPE);

        if ($found_by_title instanceof \WP_Post) {
            return (int) $found_by_title->ID;
        }

        $slug = sanitize_title($selected_program);
        $found_by_slug = get_page_by_path($slug, OBJECT, self::POST_TYPE);

        if ($found_by_slug instanceof \WP_Post) {
            return (int) $found_by_slug->ID;
        }

        $all_programs = get_posts(array(
            'post_type' => self::POST_TYPE,
            'numberposts' => -1,
            'post_status' => array('publish', 'draft', 'pending', 'private', 'future'),
        ));

        $selected_program_normalized = strtolower(trim(wp_strip_all_tags($selected_program)));

        foreach ($all_programs as $program_post) {
            $candidate_title = strtolower(trim((string) $program_post->post_title));
            $candidate_slug = strtolower(trim((string) $program_post->post_name));

            if ($selected_program_normalized === $candidate_title || $selected_program_normalized === $candidate_slug) {
                return (int) $program_post->ID;
            }
        }

        return 0;
    }
}
