<?php
$title = (string) get_field('home_created_title');
$items = get_field('home_created_items');
$fallback_icon_url = get_template_directory_uri() . '/assets/images/svg/document.svg';
$slider_previous_icon_id = absint(get_field('site_icon_slider_previous', 'option'));
$slider_next_icon_id = absint(get_field('site_icon_slider_next', 'option'));
if ($title === '') {
    $title = __('Що ми вже створили', 'unihum');
}

if (!is_array($items)) {
    $items = array();
}

$items = array_values(array_filter($items, static function ($item) {
    return is_array($item)
        && ((string) ($item['home_created_item_title'] ?? '') !== ''
            || (string) ($item['title'] ?? '') !== '');
}));

if ($items === array()) {
    return;
}
?>
<section class="created" id="created">
    <div class="container">
        <div class="created__heading">
            <h2 class="created__title"><?php echo esc_html($title); ?></h2>
            <span class="created__ornament" aria-hidden="true">✦</span>
        </div>

        <div class="created__slider swiper" data-created-slider>
            <div class="created__items swiper-wrapper">
            <?php foreach ($items as $item) : ?>
                <?php
                $item_title = (string) ($item['home_created_item_title'] ?? $item['title'] ?? '');
                $item_description = wp_strip_all_tags(
                    (string) preg_replace(
                        '/<br\s*\/?>/i',
                        ' ',
                        (string) ($item['home_created_item_description'] ?? $item['description'] ?? '')
                    )
                );
                $icon_id = absint($item['home_created_item_icon'] ?? 0);
                $file = $item['home_created_item_file'] ?? array();
                $file_url = is_array($file) ? (string) ($file['url'] ?? '') : (string) $file;
                $file_name = is_array($file) ? (string) ($file['filename'] ?? '') : '';
                ?>
                <div class="created__slide swiper-slide">
                    <article class="created__item">
                        <div class="created__icon" aria-hidden="true">
                            <?php
                            if ($icon_id) {
                                echo wp_get_attachment_image($icon_id, 'full', false, array(
                                    'class' => 'created__icon-image',
                                    'alt' => '',
                                ));
                            } else {
                                ?>
                                <img class="created__icon-image" src="<?php echo esc_url($fallback_icon_url); ?>" alt="">
                                <?php
                            }
                            ?>
                        </div>

                        <h3 class="created__item-title"><?php echo esc_html($item_title); ?></h3>

                        <?php if ($item_description !== '') : ?>
                            <p class="created__item-description"><?php echo esc_html($item_description); ?></p>
                        <?php endif; ?>

                        <?php if ($file_url !== '') : ?>
                            <?php
                            get_template_part('templates/button', null, array(
                                'link' => $file_url,
                                'text' => __('Скачати PDF', 'unihum'),
                                'aria_label' => sprintf(__('Скачати PDF: %s', 'unihum'), $item_title),
                                'download' => $file_name,
                                'class' => 'btn--primary-outline created__download',
                            ));
                            ?>
                        <?php endif; ?>
                    </article>
                </div>
            <?php endforeach; ?>
            </div>
        </div>

        <?php if (count($items) > 1) : ?>
            <div class="created__controls">
                <?php
                get_template_part('templates/button', null, array(
                    'text' => '←',
                    'class' => 'btn--slider-navigation' . ($slider_previous_icon_id ? ' btn--has-mask-icon' : '') . ' created__navigation created__navigation--prev',
                    'aria_label' => __('Попередній матеріал', 'unihum'),
                    'icon_id' => $slider_previous_icon_id,
                ));
                ?>
                <p class="created__counter" aria-live="polite">
                    <span data-created-current>01</span><span aria-hidden="true"> / </span><span><?php echo esc_html(str_pad((string) count($items), 2, '0', STR_PAD_LEFT)); ?></span>
                </p>
                <div class="btn-pagination created__pagination" aria-label="<?php esc_attr_e('Навігація матеріалами', 'unihum'); ?>"></div>
                <?php
                get_template_part('templates/button', null, array(
                    'text' => '→',
                    'class' => 'btn--slider-navigation' . ($slider_next_icon_id ? ' btn--has-mask-icon' : '') . ' created__navigation created__navigation--next',
                    'aria_label' => __('Наступний матеріал', 'unihum'),
                    'icon_id' => $slider_next_icon_id,
                ));
                ?>
            </div>
        <?php endif; ?>
    </div>
</section>
