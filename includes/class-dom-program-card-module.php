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
        $type_label = DOM_Programs::get_type_label((string) $meta['type']);
        $pill_text = $this->resolve_pill_text($type_label, (string) $meta['badge']);

        $variant_slug = $this->resolve_variant_slug($type_label);
        $classes = array(
            'dom-program-card',
            'offering',
            'dom-image-' . ($image_side === 'right' ? 'right' : 'left'),
        );

        if ($image_side === 'right') {
            $classes[] = 'reverse';
        }

        if ($variant_slug !== '') {
            $classes[] = $variant_slug;
        }

        $styles = sprintf(
            '--dom-card-bg:%1$s;--dom-badge-bg:%2$s;--dom-accent:%3$s;--dom-button-border:%4$s;--dom-mobile-image-position:%5$s;',
            esc_attr($meta['card_bg']),
            esc_attr($meta['badge_color']),
            esc_attr($meta['accent_color']),
            esc_attr($meta['button_border_color']),
            esc_attr($meta['mobile_image_position'])
        );

        ob_start();
        ?>
        <article class="<?php echo esc_attr(implode(' ', $classes)); ?>" style="<?php echo esc_attr($styles); ?>">
            <div class="dom-media side left"<?php if (! $image_url) { echo ' style="display:none;"'; } ?>>
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>">
                <?php endif; ?>
            </div>

            <div class="side right">
            <div class="dom-content info">
                <?php if ($pill_text !== '') : ?>
                    <div class="dom-badge dom-location"><?php echo esc_html($pill_text); ?></div>
                <?php endif; ?>

                <div class="heading">
                    <h3 class="dom-title"><?php echo esc_html($title); ?></h3>

                    <?php if (! empty($meta['subtitle'])) : ?>
                        <h4 class="dom-subtitle"><?php echo esc_html($meta['subtitle']); ?></h4>
                    <?php endif; ?>
                </div>

                <ul class="dom-details-grid main-details">
                    <?php echo $this->render_detail_item('cost', $meta['price']); ?>
                    <?php echo $this->render_detail_item('duration', $meta['time']); ?>
                    <?php echo $this->render_detail_item('when', $meta['schedule']); ?>
                    <?php echo $this->render_detail_item('repeat', $meta['frequency']); ?>
                    <?php echo $this->render_detail_item('who', $meta['ages']); ?>
                </ul>

                <?php if (! empty($meta['description'])) : ?>
                    <p class="dom-description"><?php echo esc_html($meta['description']); ?></p>
                <?php endif; ?>

                <?php if (! empty($meta['button_text']) && ! empty($meta['button_url'])) : ?>
                    <a class="dom-button link-ext" href="<?php echo esc_url($meta['button_url']); ?>" data-icon="→" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html($meta['button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
            </div>
        </article>
        <?php
        return (string) ob_get_clean();
    }

    private function render_detail_item(string $item_key, string $value): string
    {
        if (trim($value) === '') {
            return '';
        }

        $classes = 'dom-detail-item ' . $item_key;

        return sprintf(
            '<li class="%1$s" title="%2$s"><span class="dom-icon et-pb-icon" aria-hidden="true"></span><span>%3$s</span></li>',
            esc_attr($classes),
            esc_attr($this->get_detail_title($item_key)),
            esc_html($value)
        );
    }

    private function get_detail_title(string $item_key): string
    {
        switch ($item_key) {
            case 'cost':
                return 'What does this cost?';
            case 'duration':
                return 'How long is this?';
            case 'when':
                return 'When does this occur?';
            case 'repeat':
                return 'Does this repeat?';
            case 'who':
                return 'Who can attend this?';
            default:
                return '';
        }
    }

    private function resolve_variant_slug(string $badge): string
    {
        $allowed_variants = array(
            'bike',
            'climb',
            'fitness',
            'camp',
            'club',
            'team',
            'pass',
            'membership',
            'gift',
        );

        $slug = sanitize_title($badge);

        return in_array($slug, $allowed_variants, true) ? $slug : '';
    }

    private function resolve_pill_text(string $type_label, string $legacy_badge): string
    {
        $type_label = trim($type_label);

        if ($type_label !== '') {
            return $type_label;
        }

        return trim($legacy_badge);
    }
}
