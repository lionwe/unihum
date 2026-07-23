<?php
$site_name = get_bloginfo('name');
$site_tagline = trim((string) get_bloginfo('description'));
$custom_logo = get_custom_logo();
$languages = array();
$footer_copy = '';
$privacy_url = '';
$privacy_title = '';
$made_by = '';
$made_by_link = '';

if (function_exists('get_field')) {
    $footer_copy = (string) get_field('site_footer_copy', 'option');
    $privacy_url = trim((string) get_field('site_footer_privacy', 'option'));
    $made_by = trim((string) get_field('site_footer_made_by', 'option'));
    $made_by_link = trim((string) get_field('site_footer_made_by_link', 'option'));
}

if ($privacy_url !== '') {
    $privacy_page_id = url_to_postid($privacy_url);

    if ($privacy_page_id) {
        $translated_privacy_page_id = 0;

        if (function_exists('pll_get_post') && function_exists('pll_current_language')) {
            $current_language = pll_current_language('slug');

            if ($current_language) {
                $translated_privacy_page_id = (int) pll_get_post(
                    $privacy_page_id,
                    $current_language
                );
            }
        }

        if ($translated_privacy_page_id) {
            $privacy_page_id = $translated_privacy_page_id;
            $translated_privacy_url = get_permalink($privacy_page_id);

            if ($translated_privacy_url) {
                $privacy_url = $translated_privacy_url;
            }
        }

        $privacy_title = (string) get_the_title($privacy_page_id);
    }

    if ($privacy_title === '') {
        $privacy_title = __('Політика конфіденційності', 'unihum');
    }
}

if (function_exists('pll_the_languages')) {
    $languages = pll_the_languages(array(
        'raw' => 1,
        'hide_if_empty' => 0,
    ));
}
?>
<footer class="footer">
    <div class="container">
        <div class="footer__top">
            <div class="footer__brand">
                <?php if ($custom_logo) : ?>
                    <?php echo $custom_logo; ?>
                <?php else : ?>
                    <a class="footer__brand-link" href="<?php echo esc_url(home_url('/')); ?>">
                        <?php echo esc_html($site_name); ?>
                    </a>
                <?php endif; ?>

                <?php if ($site_tagline !== '') : ?>
                    <span class="footer__tagline"><?php echo esc_html($site_tagline); ?></span>
                <?php endif; ?>
            </div>

            <?php if (has_nav_menu('menu-footer')) : ?>
                <nav
                    class="footer__navigation"
                    aria-label="<?php echo esc_attr__('Навігація у футері', 'unihum'); ?>"
                >
                    <?php
                    get_template_part(
                        'templates/navigation',
                        null,
                        array('location' => 'menu-footer')
                    );
                    ?>
                </nav>
            <?php endif; ?>

            <?php if (is_array($languages) && count($languages) > 1) : ?>
                <nav
                    class="footer__language-navigation"
                    aria-label="<?php echo esc_attr__('Мови сайту', 'unihum'); ?>"
                >
                    <ul class="footer__languages">
                        <?php foreach ($languages as $language) : ?>
                            <?php
                            $slug = isset($language['slug'])
                                ? sanitize_key($language['slug'])
                                : '';
                            $label = $slug === 'uk' ? 'UA' : strtoupper($slug);
                            $language_url = isset($language['url'])
                                ? (string) $language['url']
                                : '';
                            $is_current = !empty($language['current_lang']);

                            if ($slug === '' || (!$is_current && $language_url === '')) {
                                continue;
                            }
                            ?>
                            <li class="footer__language">
                                <?php if ($is_current) : ?>
                                    <span aria-current="page"><?php echo esc_html($label); ?></span>
                                <?php else : ?>
                                    <a href="<?php echo esc_url($language_url); ?>">
                                        <?php echo esc_html($label); ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <?php if ($footer_copy !== '' || $privacy_url !== '' || $made_by !== '') : ?>
            <div class="footer__bottom">
                <?php if ($footer_copy !== '') : ?>
                    <div class="footer__copyright">
                        <?php echo wp_kses_post($footer_copy); ?>
                    </div>
                <?php endif; ?>

                <?php if ($privacy_url !== '') : ?>
                    <a class="footer__privacy" href="<?php echo esc_url($privacy_url); ?>">
                        <?php echo esc_html($privacy_title); ?>
                    </a>
                <?php endif; ?>

                <?php if ($made_by !== '') : ?>
                    <div class="footer__made-by">
                        <span><?php echo esc_html__('Розробка:', 'unihum'); ?></span>
                        <?php if ($made_by_link !== '') : ?>
                            <a href="<?php echo esc_url($made_by_link); ?>">
                                <?php echo esc_html($made_by); ?>
                            </a>
                        <?php else : ?>
                            <span class="footer__made-by-name"><?php echo esc_html($made_by); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
