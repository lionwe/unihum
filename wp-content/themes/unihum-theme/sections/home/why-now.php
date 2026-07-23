<?php
$title = trim((string) get_field('home_why_now_title'));
$description = trim((string) get_field('home_why_now_description'));
$items = get_field('home_why_now_items');
$fallback_icon_urls = array(
    get_template_directory_uri() . '/assets/images/svg/brain.svg',
    get_template_directory_uri() . '/assets/images/svg/people.svg',
    get_template_directory_uri() . '/assets/images/svg/planet.svg',
    get_template_directory_uri() . '/assets/images/svg/lotus.svg',
);

if (!is_array($items)) {
    $items = array();
}

$items = array_values(array_filter($items, static function ($item) {
    return is_array($item)
        && ((string) ($item['home_why_now_item_title'] ?? '') !== ''
            || (string) ($item['home_why_now_item_description'] ?? '') !== ''
            || isset($item['title'], $item['description']));
}));

if ($items === array()) {
    return;
}
?>
<section class="why-now" id="why-now">
    <div class="container">
        <div class="why-now__layout">
            <div class="why-now__heading">
                <?php if ($title !== '') : ?>
                    <h2 class="why-now__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <span class="why-now__eyebrow" aria-hidden="true">✦</span>
                <?php if ($description !== '') : ?>
                    <p class="why-now__description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>

            <div class="why-now__items">
                <?php foreach ($items as $index => $item) : ?>
                    <?php
                    $item_title = (string) ($item['home_why_now_item_title'] ?? $item['title'] ?? '');
                    $item_description = (string) ($item['home_why_now_item_description'] ?? $item['description'] ?? '');
                    $icon_id = absint($item['home_why_now_item_icon'] ?? $item['icon'] ?? 0);
                    $fallback_icon_url = $fallback_icon_urls[$index % count($fallback_icon_urls)];

                    if ($item_title === '' && $item_description === '') {
                        continue;
                    }
                    ?>
                    <article class="why-now__item">
                        <span class="why-now__item-index" aria-hidden="true"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                        <div class="why-now__item-marker">
                            <div class="why-now__icon" aria-hidden="true">
                                <?php
                                if ($icon_id) {
                                    echo wp_get_attachment_image($icon_id, 'full', false, array(
                                        'class' => 'why-now__icon-image',
                                        'alt' => '',
                                    ));
                                } else {
                                    ?>
                                    <img class="why-now__icon-image" src="<?php echo esc_url($fallback_icon_url); ?>" alt="">
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="why-now__item-content">
                            <?php if ($item_title !== '') : ?>
                                <h3 class="why-now__item-title"><?php echo esc_html($item_title); ?></h3>
                            <?php endif; ?>

                            <?php if ($item_description !== '') : ?>
                                <p class="why-now__item-description"><?php echo esc_html($item_description); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
