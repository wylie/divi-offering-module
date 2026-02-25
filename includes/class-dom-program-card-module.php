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
        $icon_html = $this->render_icon_html($icon_key);

        return sprintf(
            '<div class="%1$s"><span class="dom-icon" aria-hidden="true">%2$s</span><span>%3$s</span></div>',
            esc_attr($classes),
            $icon_html,
            esc_html($value)
        );
    }

    private function render_icon_html(string $icon_key): string
    {
        $icon_class = '';

        switch ($icon_key) {
            case 'price':
                $icon_class = 'fa-solid fa-dollar-sign fas';
                break;
            case 'frequency':
                $icon_class = 'fa-solid fa-rotate-right fas fa-sync-alt';
                break;
            case 'time':
                $icon_class = 'fa-regular fa-clock far';
                break;
            case 'ages':
                $icon_class = 'fa-solid fa-users fas fa-user-friends';
                break;
            case 'schedule':
                $icon_class = 'fa-regular fa-calendar-days far fa-calendar-alt';
                break;
            default:
                $icon_class = 'fa-solid fa-circle fas';
                break;
        }

        return sprintf('<i class="%s"></i>', esc_attr($icon_class));
    }
}
