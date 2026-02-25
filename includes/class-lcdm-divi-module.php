<?php

if (! defined('ABSPATH')) {
    exit;
}

class LCDM_Divi_Module
{
    public static function init(): void
    {
        add_action('et_builder_ready', array(__CLASS__, 'register_module'));
    }

    public static function register_module(): void
    {
        if (! class_exists('ET_Builder_Module')) {
            return;
        }

        class LCDM_Program_Card_Module extends ET_Builder_Module
        {
            public $slug = 'lcdm_program_card';
            public $vb_support = 'on';

            public function init(): void
            {
                $this->name = esc_html__('Program Card (Dynamic)', 'little-crushers-divi-module');
                $this->icon_path = '';
            }

            public function get_fields(): array
            {
                return array(
                    'program_id' => array(
                        'label' => esc_html__('Program', 'little-crushers-divi-module'),
                        'type' => 'select',
                        'option_category' => 'basic_option',
                        'options' => LCDM_Programs::get_program_options(),
                        'description' => esc_html__('Select which Program entry to display.', 'little-crushers-divi-module'),
                        'toggle_slug' => 'main_content',
                    ),
                    'image_side_override' => array(
                        'label' => esc_html__('Image Side Override', 'little-crushers-divi-module'),
                        'type' => 'select',
                        'option_category' => 'layout',
                        'options' => array(
                            '' => esc_html__('Use Program Default', 'little-crushers-divi-module'),
                            'left' => esc_html__('Left', 'little-crushers-divi-module'),
                            'right' => esc_html__('Right', 'little-crushers-divi-module'),
                        ),
                        'toggle_slug' => 'layout',
                    ),
                );
            }

            public function render($attrs, $content = null, $render_slug = ''): string
            {
                $program_id = isset($this->props['program_id']) ? absint($this->props['program_id']) : 0;

                if (! $program_id || get_post_type($program_id) !== LCDM_Programs::POST_TYPE) {
                    return '<div class="lcdm-program-card lcdm-empty">' . esc_html__('Select a Program in module settings.', 'little-crushers-divi-module') . '</div>';
                }

                $title = get_the_title($program_id);
                $meta = LCDM_Programs::get_program_meta($program_id);
                $image_url = get_the_post_thumbnail_url($program_id, 'large');
                $image_side = ! empty($this->props['image_side_override']) ? $this->props['image_side_override'] : $meta['image_side'];

                $classes = array('lcdm-program-card', 'lcdm-image-' . ($image_side === 'right' ? 'right' : 'left'));

                $styles = sprintf(
                    '--lcdm-card-bg:%1$s;--lcdm-badge-bg:%2$s;--lcdm-accent:%3$s;--lcdm-button-border:%4$s;',
                    esc_attr($meta['card_bg']),
                    esc_attr($meta['badge_color']),
                    esc_attr($meta['accent_color']),
                    esc_attr($meta['button_border_color'])
                );

                ob_start();
                ?>
                <article class="<?php echo esc_attr(implode(' ', $classes)); ?>" style="<?php echo esc_attr($styles); ?>">
                    <div class="lcdm-media"<?php if (! $image_url) { echo ' style="display:none;"'; } ?>>
                        <?php if ($image_url) : ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>">
                        <?php endif; ?>
                    </div>

                    <div class="lcdm-content">
                        <?php if (! empty($meta['badge'])) : ?>
                            <div class="lcdm-badge"><?php echo esc_html($meta['badge']); ?></div>
                        <?php endif; ?>

                        <h3 class="lcdm-title"><?php echo esc_html($title); ?></h3>

                        <?php if (! empty($meta['subtitle'])) : ?>
                            <p class="lcdm-subtitle"><?php echo esc_html($meta['subtitle']); ?></p>
                        <?php endif; ?>

                        <div class="lcdm-details-grid">
                            <?php echo $this->render_detail_item('$', $meta['price']); ?>
                            <?php echo $this->render_detail_item('↻', $meta['frequency']); ?>
                            <?php echo $this->render_detail_item('◔', $meta['time']); ?>
                            <?php echo $this->render_detail_item('👥', $meta['ages']); ?>
                            <?php echo $this->render_detail_item('▦', $meta['schedule'], true); ?>
                        </div>

                        <?php if (! empty($meta['description'])) : ?>
                            <div class="lcdm-divider"></div>
                            <p class="lcdm-description"><?php echo esc_html($meta['description']); ?></p>
                        <?php endif; ?>

                        <?php if (! empty($meta['button_text']) && ! empty($meta['button_url'])) : ?>
                            <a class="lcdm-button" href="<?php echo esc_url($meta['button_url']); ?>">
                                <?php echo esc_html($meta['button_text']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
                <?php
                return (string) ob_get_clean();
            }

            private function render_detail_item(string $icon, string $value, bool $full_width = false): string
            {
                if (trim($value) === '') {
                    return '';
                }

                $classes = 'lcdm-detail-item' . ($full_width ? ' lcdm-full-width' : '');

                return sprintf(
                    '<div class="%1$s"><span class="lcdm-icon" aria-hidden="true">%2$s</span><span>%3$s</span></div>',
                    esc_attr($classes),
                    esc_html($icon),
                    esc_html($value)
                );
            }
        }

        new LCDM_Program_Card_Module();
    }
}
