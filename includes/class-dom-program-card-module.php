<?php

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('ET_Builder_Module')) {
    return;
}

class DOM_Program_Card_Module extends ET_Builder_Module
{
    public $slug = 'dom_program_card';
    public $vb_support = 'on';

    public function init(): void
    {
        $this->name = esc_html__('Offering Card (Dynamic)', 'divi-offering-module');
        $this->icon_path = '';
    }

    public function get_fields(): array
    {
        return array(
            'program_id' => array(
                'label' => esc_html__('Offering', 'divi-offering-module'),
                'type' => 'select',
                'option_category' => 'basic_option',
                'options' => DOM_Programs::get_program_options(),
                'description' => esc_html__('Select which Offering entry to display.', 'divi-offering-module'),
                'toggle_slug' => 'main_content',
            ),
            'image_side_override' => array(
                'label' => esc_html__('Image Side Override', 'divi-offering-module'),
                'type' => 'select',
                'option_category' => 'layout',
                'options' => array(
                    '' => esc_html__('Use Offering Default', 'divi-offering-module'),
                    'left' => esc_html__('Left', 'divi-offering-module'),
                    'right' => esc_html__('Right', 'divi-offering-module'),
                ),
                'toggle_slug' => 'layout',
            ),
        );
    }

    public function render($attrs, $content = null, $render_slug = ''): string
    {
        $selected_program = isset($this->props['program_id']) ? (string) $this->props['program_id'] : '';
        $program_id = DOM_Programs::resolve_program_selection($selected_program);

        if (! $program_id || get_post_type($program_id) !== DOM_Programs::POST_TYPE) {
            return '<div class="dom-program-card dom-empty">' . esc_html__('Select an Offering in module settings.', 'divi-offering-module') . '</div>';
        }

        $title = get_the_title($program_id);
        $meta = DOM_Programs::get_program_meta($program_id);
        $image_url = get_the_post_thumbnail_url($program_id, 'large');
        $image_side = ! empty($this->props['image_side_override']) ? $this->props['image_side_override'] : $meta['image_side'];

        $classes = array('dom-program-card', 'dom-image-' . ($image_side === 'right' ? 'right' : 'left'));

        $styles = sprintf(
            '--dom-card-bg:%1$s;--dom-badge-bg:%2$s;--dom-accent:%3$s;--dom-button-border:%4$s;',
            esc_attr($meta['card_bg']),
            esc_attr($meta['badge_color']),
            esc_attr($meta['accent_color']),
            esc_attr($meta['button_border_color'])
        );

        ob_start();
        ?>
        <article class="<?php echo esc_attr(implode(' ', $classes)); ?>" style="<?php echo esc_attr($styles); ?>">
            <div class="dom-media"<?php if (! $image_url) { echo ' style="display:none;"'; } ?>>
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>">
                <?php endif; ?>
            </div>

            <div class="dom-content">
                <?php if (! empty($meta['badge'])) : ?>
                    <div class="dom-badge"><?php echo esc_html($meta['badge']); ?></div>
                <?php endif; ?>

                <h3 class="dom-title"><?php echo esc_html($title); ?></h3>

                <?php if (! empty($meta['subtitle'])) : ?>
                    <p class="dom-subtitle"><?php echo esc_html($meta['subtitle']); ?></p>
                <?php endif; ?>

                <div class="dom-details-grid">
                    <?php echo $this->render_detail_item('price', $meta['price']); ?>
                    <?php echo $this->render_detail_item('frequency', $meta['frequency']); ?>
                    <?php echo $this->render_detail_item('time', $meta['time']); ?>
                    <?php echo $this->render_detail_item('ages', $meta['ages']); ?>
                    <?php echo $this->render_detail_item('schedule', $meta['schedule'], true); ?>
                </div>

                <?php if (! empty($meta['description'])) : ?>
                    <div class="dom-divider"></div>
                    <p class="dom-description"><?php echo esc_html($meta['description']); ?></p>
                <?php endif; ?>

                <?php if (! empty($meta['button_text']) && ! empty($meta['button_url'])) : ?>
                    <a class="dom-button" href="<?php echo esc_url($meta['button_url']); ?>">
                        <?php echo esc_html($meta['button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </article>
        <?php
        return (string) ob_get_clean();
    }

    private function render_detail_item(string $icon_key, string $value, bool $full_width = false): string
    {
        if (trim($value) === '') {
            return '';
        }

        $classes = 'dom-detail-item' . ($full_width ? ' dom-full-width' : '');
        $icon_svg = $this->render_icon_svg($icon_key);

        return sprintf(
            '<div class="%1$s"><span class="dom-icon" aria-hidden="true">%2$s</span><span>%3$s</span></div>',
            esc_attr($classes),
            $icon_svg,
            esc_html($value)
        );
    }

    private function render_icon_svg(string $icon_key): string
    {
        switch ($icon_key) {
            case 'price':
                return '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M12 1a10 10 0 1 0 10 10A10 10 0 0 0 12 1Zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8Z"></path><path d="M13.4 7.7c1.4.2 2.3 1.1 2.3 2.3h-2a.6.6 0 0 0-.2-.5 1.8 1.8 0 0 0-1-.4v2.7c2.1.4 3.3 1.3 3.3 3.1 0 1.7-1.2 2.9-3.3 3.1V20h-1.1v-1.9c-2-.2-3.3-1.4-3.4-3.3h2c.1.8.6 1.4 1.4 1.6v-2.8C9.8 13.2 8.7 12.4 8.7 10.8c0-1.7 1.2-2.9 3-3.1V6h1.1Zm-1.9 3.4v-2a1.1 1.1 0 0 0-1.1 1c0 .5.3.8 1.1 1Zm1.1 2.5v2.2a1.3 1.3 0 0 0 1.3-1.1c0-.6-.3-1-1.3-1.1Z"></path></svg>';
            case 'frequency':
                return '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M12 4V1L8 5l4 4V6a6 6 0 1 1-6 6H4a8 8 0 1 0 8-8Z"></path></svg>';
            case 'time':
                return '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8Z"></path><path d="M12.8 7h-1.6v6l5 3 .8-1.3-4.2-2.5Z"></path></svg>';
            case 'ages':
                return '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><circle cx="9" cy="8" r="3"></circle><path d="M3.5 19a5.5 5.5 0 0 1 11 0v1h-11Z"></path><circle cx="17" cy="9" r="2.5"></circle><path d="M14.5 20a4.5 4.5 0 0 1 9 0v1h-9Z"></path></svg>';
            case 'schedule':
                return '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M7 2h2v2h6V2h2v2h3v18H4V4h3Zm11 8H6v10h12Zm0-4H6v2h12Z"></path></svg>';
            default:
                return '<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><circle cx="12" cy="12" r="2"></circle></svg>';
        }
    }
}
