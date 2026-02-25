<?php

if (! defined('ABSPATH')) {
    exit;
}

class LCDM_Programs
{
    const POST_TYPE = 'lcdm_program';
    const NONCE_KEY = 'lcdm_program_nonce';

    public static function init(): void
    {
        add_action('init', array(__CLASS__, 'register_post_type'));
        add_action('add_meta_boxes', array(__CLASS__, 'register_meta_boxes'));
        add_action('save_post_' . self::POST_TYPE, array(__CLASS__, 'save_meta'), 10, 2);
    }

    public static function register_post_type(): void
    {
        register_post_type(
            self::POST_TYPE,
            array(
                'labels' => array(
                    'name' => __('Programs', 'little-crushers-divi-module'),
                    'singular_name' => __('Program', 'little-crushers-divi-module'),
                    'add_new_item' => __('Add Program', 'little-crushers-divi-module'),
                    'edit_item' => __('Edit Program', 'little-crushers-divi-module'),
                ),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_rest' => true,
                'menu_icon' => 'dashicons-welcome-learn-more',
                'supports' => array('title', 'thumbnail'),
                'has_archive' => false,
                'rewrite' => array('slug' => 'programs'),
            )
        );
    }

    public static function register_meta_boxes(): void
    {
        add_meta_box(
            'lcdm_program_details',
            __('Program Details', 'little-crushers-divi-module'),
            array(__CLASS__, 'render_details_metabox'),
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    public static function render_details_metabox(\WP_Post $post): void
    {
        wp_nonce_field('lcdm_program_save', self::NONCE_KEY);

        $meta = self::get_program_meta($post->ID);

        ?>
        <style>
            .lcdm-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px; }
            .lcdm-field { margin-bottom: 10px; }
            .lcdm-field label { display:block; font-weight:600; margin-bottom:6px; }
            .lcdm-field input[type="text"],
            .lcdm-field input[type="url"],
            .lcdm-field input[type="color"],
            .lcdm-field select,
            .lcdm-field textarea { width: 100%; }
        </style>

        <div class="lcdm-grid">
            <div class="lcdm-field">
                <label for="lcdm_badge"><?php esc_html_e('Badge Label', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_badge" name="lcdm_badge" type="text" value="<?php echo esc_attr($meta['badge']); ?>" placeholder="CLUB">
            </div>
            <div class="lcdm-field">
                <label for="lcdm_subtitle"><?php esc_html_e('Subtitle / Tagline', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_subtitle" name="lcdm_subtitle" type="text" value="<?php echo esc_attr($meta['subtitle']); ?>" placeholder="adventure-powered learning.">
            </div>

            <div class="lcdm-field">
                <label for="lcdm_price"><?php esc_html_e('Price', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_price" name="lcdm_price" type="text" value="<?php echo esc_attr($meta['price']); ?>" placeholder="$528">
            </div>
            <div class="lcdm-field">
                <label for="lcdm_frequency"><?php esc_html_e('Frequency', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_frequency" name="lcdm_frequency" type="text" value="<?php echo esc_attr($meta['frequency']); ?>" placeholder="Weekly">
            </div>

            <div class="lcdm-field">
                <label for="lcdm_time"><?php esc_html_e('Time', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_time" name="lcdm_time" type="text" value="<?php echo esc_attr($meta['time']); ?>" placeholder="4–6pm">
            </div>
            <div class="lcdm-field">
                <label for="lcdm_ages"><?php esc_html_e('Age Range', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_ages" name="lcdm_ages" type="text" value="<?php echo esc_attr($meta['ages']); ?>" placeholder="Ages 5–8">
            </div>

            <div class="lcdm-field" style="grid-column: span 2;">
                <label for="lcdm_schedule"><?php esc_html_e('Schedule', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_schedule" name="lcdm_schedule" type="text" value="<?php echo esc_attr($meta['schedule']); ?>" placeholder="Tue & Thur, March 3–May 21">
            </div>

            <div class="lcdm-field" style="grid-column: span 2;">
                <label for="lcdm_description"><?php esc_html_e('Description', 'little-crushers-divi-module'); ?></label>
                <textarea id="lcdm_description" name="lcdm_description" rows="6" placeholder="Program description"><?php echo esc_textarea($meta['description']); ?></textarea>
            </div>

            <div class="lcdm-field">
                <label for="lcdm_button_text"><?php esc_html_e('Button Text', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_button_text" name="lcdm_button_text" type="text" value="<?php echo esc_attr($meta['button_text']); ?>" placeholder="Join Little Crushers Club">
            </div>
            <div class="lcdm-field">
                <label for="lcdm_button_url"><?php esc_html_e('Button URL', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_button_url" name="lcdm_button_url" type="url" value="<?php echo esc_url($meta['button_url']); ?>" placeholder="https://">
            </div>

            <div class="lcdm-field">
                <label for="lcdm_image_side"><?php esc_html_e('Image Side', 'little-crushers-divi-module'); ?></label>
                <select id="lcdm_image_side" name="lcdm_image_side">
                    <option value="left" <?php selected($meta['image_side'], 'left'); ?>><?php esc_html_e('Left', 'little-crushers-divi-module'); ?></option>
                    <option value="right" <?php selected($meta['image_side'], 'right'); ?>><?php esc_html_e('Right', 'little-crushers-divi-module'); ?></option>
                </select>
            </div>

            <div class="lcdm-field">
                <label for="lcdm_badge_color"><?php esc_html_e('Badge Color', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_badge_color" name="lcdm_badge_color" type="color" value="<?php echo esc_attr($meta['badge_color']); ?>">
            </div>

            <div class="lcdm-field">
                <label for="lcdm_card_bg"><?php esc_html_e('Card Background', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_card_bg" name="lcdm_card_bg" type="color" value="<?php echo esc_attr($meta['card_bg']); ?>">
            </div>

            <div class="lcdm-field">
                <label for="lcdm_accent_color"><?php esc_html_e('Accent Color', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_accent_color" name="lcdm_accent_color" type="color" value="<?php echo esc_attr($meta['accent_color']); ?>">
            </div>

            <div class="lcdm-field">
                <label for="lcdm_button_border_color"><?php esc_html_e('Button Border Color', 'little-crushers-divi-module'); ?></label>
                <input id="lcdm_button_border_color" name="lcdm_button_border_color" type="color" value="<?php echo esc_attr($meta['button_border_color']); ?>">
            </div>
        </div>
        <?php
        echo '<p><em>' . esc_html__('Set featured image to control the left/right photo.', 'little-crushers-divi-module') . '</em></p>';
    }

    public static function save_meta(int $post_id, \WP_Post $post): void
    {
        if (! isset($_POST[self::NONCE_KEY]) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE_KEY])), 'lcdm_program_save')) {
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
            $key = 'lcdm_' . $field;
            $val = isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';
            update_post_meta($post_id, $key, $val);
        }

        $description = isset($_POST['lcdm_description']) ? sanitize_textarea_field(wp_unslash($_POST['lcdm_description'])) : '';
        update_post_meta($post_id, 'lcdm_description', $description);

        $button_url = isset($_POST['lcdm_button_url']) ? esc_url_raw(wp_unslash($_POST['lcdm_button_url'])) : '';
        update_post_meta($post_id, 'lcdm_button_url', $button_url);

        $color_fields = array('badge_color', 'card_bg', 'accent_color', 'button_border_color');

        foreach ($color_fields as $field) {
            $key = 'lcdm_' . $field;
            $val = isset($_POST[$key]) ? sanitize_hex_color(wp_unslash($_POST[$key])) : '';
            update_post_meta($post_id, $key, $val ?: '');
        }
    }

    public static function get_program_meta(int $post_id): array
    {
        return array(
            'badge' => get_post_meta($post_id, 'lcdm_badge', true),
            'subtitle' => get_post_meta($post_id, 'lcdm_subtitle', true),
            'price' => get_post_meta($post_id, 'lcdm_price', true),
            'frequency' => get_post_meta($post_id, 'lcdm_frequency', true),
            'time' => get_post_meta($post_id, 'lcdm_time', true),
            'ages' => get_post_meta($post_id, 'lcdm_ages', true),
            'schedule' => get_post_meta($post_id, 'lcdm_schedule', true),
            'description' => get_post_meta($post_id, 'lcdm_description', true),
            'button_text' => get_post_meta($post_id, 'lcdm_button_text', true),
            'button_url' => get_post_meta($post_id, 'lcdm_button_url', true),
            'image_side' => get_post_meta($post_id, 'lcdm_image_side', true) ?: 'left',
            'badge_color' => get_post_meta($post_id, 'lcdm_badge_color', true) ?: '#9A62F9',
            'card_bg' => get_post_meta($post_id, 'lcdm_card_bg', true) ?: '#efefef',
            'accent_color' => get_post_meta($post_id, 'lcdm_accent_color', true) ?: '#AF4F27',
            'button_border_color' => get_post_meta($post_id, 'lcdm_button_border_color', true) ?: '#555555',
        );
    }

    public static function get_program_options(): array
    {
        $posts = get_posts(array(
            'post_type' => self::POST_TYPE,
            'numberposts' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        $options = array();

        foreach ($posts as $item) {
            $options[(string) $item->ID] = $item->post_title;
        }

        return $options;
    }
}
