<?php
$title = (string) get_field('home_roadmap_title');
$description = (string) get_field('home_roadmap_description');
$items = get_field('home_roadmap_items');
$link = get_field('home_roadmap_link');
$fallback_icon_url = get_template_directory_uri() . '/assets/images/svg/document.svg';
$pattern_files = array(
    'roadmap-pattern-foundation.svg',
    'roadmap-pattern-community.svg',
    'roadmap-pattern-platform.svg',
    'roadmap-pattern-culture.svg',
    'roadmap-pattern-planetary.svg',
);
$fallback_items = array(
    array(
        'period' => '2026–2027',
        'title' => __('Фундаментальний етап', 'unihum'),
        'description' => __('Формування концепції проєкту, ключових документів і першої міжнародної спільноти.', 'unihum'),
    ),
    array(
        'period' => '2027–2029',
        'title' => __('Формування спільноти', 'unihum'),
        'description' => __('Створення міжнародного дослідницького співтовариства та програми.', 'unihum'),
    ),
    array(
        'period' => '2028–2031',
        'title' => __('Інтелектуальна платформа', 'unihum'),
        'description' => __('Розробка платформи знань, інструментів аналізу й систем досліджень.', 'unihum'),
    ),
    array(
        'period' => '2030–2035',
        'title' => __('Освіта й культура', 'unihum'),
        'description' => __('Освітні програми, медіапроєкти та глобальні культурні ініціативи.', 'unihum'),
    ),
    array(
        'period' => '2035–2045',
        'title' => __('Планетарна спільнота', 'unihum'),
        'description' => __('Формування культури єдності, співпраці та глобальної відповідальності.', 'unihum'),
    ),
);

if ($title === '') {
    $title = __('Дорожня карта 2026–2045', 'unihum');
}

if ($description === '') {
    $description = __('Шлях від дослідницької ініціативи до планетарної спільноти.', 'unihum');
}

if (!is_array($items) || $items === array()) {
    $items = $fallback_items;
}

$items = array_values(array_filter($items, static function ($item) {
    return is_array($item)
        && ((string) ($item['home_roadmap_item_title'] ?? '') !== ''
            || (string) ($item['title'] ?? '') !== '');
}));

if ($items === array()) {
    return;
}

$link_url = is_array($link) ? (string) ($link['url'] ?? '') : '';
$link_title = is_array($link) ? (string) ($link['title'] ?? '') : '';
$link_target = is_array($link) && !empty($link['target']) ? (string) $link['target'] : '_self';
$timeline_id = wp_unique_id('roadmap-timeline-');
?>
<section class="roadmap" id="roadmap" data-roadmap>
    <div class="container">
        <div class="roadmap__header">
            <div class="roadmap__heading">
                <p class="roadmap__eyebrow"><?php echo esc_html__('Вектор розвитку', 'unihum'); ?></p>
                <h2 class="roadmap__title"><?php echo esc_html($title); ?></h2>
                <p class="roadmap__description"><?php echo esc_html($description); ?></p>
            </div>
            <p class="roadmap__counter" aria-live="polite">
                <span data-roadmap-current>01</span><span aria-hidden="true"> / </span><span><?php echo esc_html(str_pad((string) count($items), 2, '0', STR_PAD_LEFT)); ?></span>
            </p>
        </div>

        <div class="roadmap__timeline" id="<?php echo esc_attr($timeline_id); ?>" style="--roadmap-steps: <?php echo esc_attr((string) count($items)); ?>;">
            <div class="roadmap__navigation" role="tablist" aria-label="<?php echo esc_attr__('Етапи дорожньої карти', 'unihum'); ?>">
                <?php foreach ($items as $index => $item) : ?>
                    <?php $item_period = (string) ($item['home_roadmap_item_period'] ?? $item['period'] ?? ''); ?>
                    <button
                        class="roadmap__step<?php echo $index === 0 ? ' is-active' : ''; ?>"
                        type="button"
                        role="tab"
                        id="<?php echo esc_attr($timeline_id . '-tab-' . $index); ?>"
                        aria-controls="<?php echo esc_attr($timeline_id . '-panel-' . $index); ?>"
                        aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                        tabindex="<?php echo $index === 0 ? '0' : '-1'; ?>"
                        data-roadmap-tab
                    >
                        <span class="roadmap__step-index" aria-hidden="true"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                        <?php if ($item_period !== '') : ?>
                            <span class="roadmap__step-period"><?php echo esc_html($item_period); ?></span>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="roadmap__controls">
                <p class="roadmap__hint">
                    <span class="roadmap__hint-icon" aria-hidden="true"></span>
                    <?php echo esc_html__('Прокручуйте, щоб пройти всі етапи', 'unihum'); ?>
                </p>
            </div>

            <div class="roadmap__panels" data-roadmap-stage>
                <?php foreach ($items as $index => $item) : ?>
                    <?php
                    $item_period = (string) ($item['home_roadmap_item_period'] ?? $item['period'] ?? '');
                    $item_title = (string) ($item['home_roadmap_item_title'] ?? $item['title'] ?? '');
                    $item_description = (string) ($item['home_roadmap_item_description'] ?? $item['description'] ?? '');
                    $icon_id = absint($item['home_roadmap_item_icon'] ?? 0);
                    $pattern_index = $index % count($pattern_files);
                    $pattern_path = get_theme_file_path('assets/images/svg/' . $pattern_files[$pattern_index]);
                    $pattern_svg = is_readable($pattern_path) ? (string) file_get_contents($pattern_path) : '';
                    ?>
                    <article
                        class="roadmap__panel roadmap__panel--pattern-<?php echo esc_attr((string) ($pattern_index + 1)); ?><?php echo $index % 2 === 1 ? ' roadmap__panel--reverse' : ''; ?><?php echo $index === 0 ? ' is-active' : ''; ?>"
                        id="<?php echo esc_attr($timeline_id . '-panel-' . $index); ?>"
                        role="tabpanel"
                        aria-labelledby="<?php echo esc_attr($timeline_id . '-tab-' . $index); ?>"
                        aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>"
                        data-roadmap-index="<?php echo esc_attr((string) $index); ?>"
                        data-roadmap-panel
                    >
                        <div class="roadmap__panel-orbit" aria-hidden="true">
                            <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The SVG is a local theme asset. ?>
                            <?php echo $pattern_svg; ?>
                        </div>
                        <div class="roadmap__panel-content">
                            <div class="roadmap__icon" aria-hidden="true">
                                <?php
                                if ($icon_id) {
                                    echo wp_get_attachment_image($icon_id, 'full', false, array(
                                        'class' => 'roadmap__icon-image',
                                        'alt' => '',
                                    ));
                                } else {
                                    ?>
                                    <img class="roadmap__icon-image" src="<?php echo esc_url($fallback_icon_url); ?>" alt="">
                                    <?php
                                }
                                ?>
                            </div>
                            <?php if ($item_period !== '') : ?>
                                <p class="roadmap__period"><?php echo esc_html($item_period); ?></p>
                            <?php endif; ?>
                            <h3 class="roadmap__item-title"><?php echo esc_html($item_title); ?></h3>
                            <?php if ($item_description !== '') : ?>
                                <p class="roadmap__item-description"><?php echo esc_html($item_description); ?></p>
                            <?php endif; ?>
                        </div>
                        <p class="roadmap__panel-number" aria-hidden="true"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($link_url !== '' && $link_title !== '') : ?>
            <div class="roadmap__action">
                <a class="roadmap__link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>" <?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>>
                    <?php echo esc_html($link_title); ?>
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
