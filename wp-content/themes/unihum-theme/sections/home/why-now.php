<?php
$title = (string) get_field('home_why_now_title');
$description = (string) get_field('home_why_now_description');
$items = get_field('home_why_now_items');
$fallback_icon_url = get_template_directory_uri() . '/assets/images/svg/sun.svg';

if ($title === '') {
    $title = __('Чому саме зараз?', 'unihum');
}

if ($description === '') {
    $description = __('Уперше в історії людство стикається з викликами, які роблять еволюцію свідомості не просто важливою, а необхідною.', 'unihum');
}

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
        <div class="why-now__heading">
            <h2 class="why-now__title"><?php echo esc_html($title); ?></h2>
            <span class="why-now__ornament" aria-hidden="true">✦</span>
            <p class="why-now__description"><?php echo esc_html($description); ?></p>
        </div>

        <div class="why-now__items">
            <?php foreach ($items as $item) : ?>
                <?php
                $item_title = (string) ($item['home_why_now_item_title'] ?? $item['title'] ?? '');
                $item_description = (string) ($item['home_why_now_item_description'] ?? $item['description'] ?? '');
                $icon_id = absint($item['home_why_now_item_icon'] ?? $item['icon'] ?? 0);

                if ($item_title === '' && $item_description === '') {
                    continue;
                }
                ?>
                <article class="why-now__item">
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

                    <?php if ($item_title !== '') : ?>
                        <h3 class="why-now__item-title"><?php echo esc_html($item_title); ?></h3>
                    <?php endif; ?>

                    <span class="why-now__item-divider" aria-hidden="true"></span>

                    <?php if ($item_description !== '') : ?>
                        <p class="why-now__item-description"><?php echo esc_html($item_description); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
