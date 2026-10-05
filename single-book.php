<?php get_header(); ?>
<?php
$post_id = get_the_ID();
$views = function_exists('theme_get_site_views') ? theme_get_site_views($post_id) : 0;
$book_type = get_post_meta($post_id, 'book_type', true);
$thumb = has_post_thumbnail($post_id) ? get_the_post_thumbnail_url($post_id, 'thumbnail') : '';
?>

<div class="ioui-content switch-container sidebar_right container">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="content-layout">
                <?php theme_breadcrumb(); ?>

                <div class="panel site-content card">
                    <?php while (have_posts()) : the_post(); ?>
                    <div class="card-body">
                        <div class="panel-body single">
                            <h1 class="text-xl font-bold mb-3"><?php the_title(); ?></h1>

                            <!-- 封面 + 标签 + 统计 -->
                            <div class="d-flex align-items-start mb-3 flex-wrap">
                                <?php if ($thumb) : ?>
                                <div class="mr-3 flex-shrink-0">
                                    <img src="<?php echo esc_url($thumb); ?>" class="rounded-lg" width="56" height="80" alt="<?php the_title(); ?>" style="object-fit:cover">
                                </div>
                                <?php endif; ?>
                                <div class="flex-fill">
                                    <div class="item-tags overflow-x-auto no-scrollbar mb-2">
                                        <?php $t_terms = wp_get_post_terms($post_id, 'favorites');
                                        if ($t_terms && !is_wp_error($t_terms)) :
                                            foreach (array_slice($t_terms, 0, 2) as $t) : ?>
                                        <a href="<?php echo esc_url(get_term_link($t)); ?>" class="badge vc-l-theme text-ss mr-1"><i class="iconfont icon-folder mr-1"></i><?php echo esc_html($t->name); ?></a>
                                        <?php endforeach; endif; ?>
                                        <?php if ($book_type) : ?>
                                        <span class="badge vc-l-blue text-ss mr-1"><i class="iconfont icon-book mr-1"></i><?php echo $book_type === 'magazine' ? '杂志' : '书籍'; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="meta-ico text-muted text-xs">
                                        <span class="meta-view mr-3"><i class="iconfont icon-chakan-line"></i> <?php echo (int) $views; ?></span>
                                        <span class="meta-time"><i class="iconfont icon-time"></i> <?php echo get_the_date('Y-m-d'); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="single-entry">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- 相关书籍 -->
                <?php
                if (function_exists('theme_get_related_books')) :
                    $related = theme_get_related_books(get_the_ID(), 8);
                    if ($related->have_posts()) :
                ?>
                <div id="iow_related_books-<?php echo (int) get_the_ID(); ?>" class="module-sidebar-widget io-widget-ranking-list mt-4">
                    <div class="sidebar-header">
                        <div class="card-header widget-header">
                            <h3 class="text-md mb-0"><i class="iconfont icon-book mr-2"></i>相关书籍</h3>
                        </div>
                    </div>
                    <div class="posts-row row-col-2a row-col-sm-2a row-col-md-4a row-col-lg-4a row-col-xl-4a">
                        <?php while ($related->have_posts()) : $related->the_post();
                            $pid = get_the_ID();
                            $pthumb = has_post_thumbnail($pid) ? get_the_post_thumbnail_url($pid, 'thumbnail') : '';
                            $pbook_type = get_post_meta($pid, 'book_type', true);
                        ?>
                        <article class="posts-item book-item d-flex style-book-v1 post-<?php echo (int) $pid; ?>">
                            <div class="item-header">
                                <div class="item-media">
                                    <a class="item-image" href="<?php the_permalink(); ?>">
                                        <?php if ($pthumb) : ?>
                                            <img class="fill-cover lazy unfancybox" src="<?php echo esc_url($pthumb); ?>" alt="<?php the_title(); ?>" loading="lazy">
                                        <?php else : ?>
                                            <img class="fill-cover lazy unfancybox" src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_the_title()); ?>&background=random&size=64" alt="<?php the_title(); ?>" loading="lazy">
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </div>
                            <div class="item-body flex-fill">
                                <h3 class="item-title line1"><a href="<?php the_permalink(); ?>"><b><?php the_title(); ?></b></a></h3>
                                <div class="line1 text-muted text-xs mt-1">
                                    <?php echo esc_html(wp_trim_words(get_the_content(), 10)); ?><?php echo $pbook_type ? ' · ' . esc_html($pbook_type) : ''; ?>
                                </div>
                            </div>
                        </article>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
                <?php endif; endif; ?>

                <div class="mt-4">
                    <?php comments_template(); ?>
                </div>
            </div>
        </div>
        <?php theme_render_single_sidebar('book'); ?>
    </div>
</div>

<?php get_footer(); ?>
