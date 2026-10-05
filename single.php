<?php get_header(); ?>

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
                            <div class="meta-ico text-muted text-xs mb-4 pb-2 border-bottom">
                                <span class="mr-3"><i class="iconfont icon-time"></i> <?php the_time('Y-m-d'); ?></span>
                                <span class="meta-view mr-3"><i class="iconfont icon-chakan-line"></i> <?php echo function_exists('theme_get_post_views') ? (int) theme_get_post_views(get_the_ID()) : 0; ?></span>
                                <span class="mr-3"><a class="smooth text-muted" href="#comments"><i class="iconfont icon-comment"></i> <?php echo get_comments_number(); ?></a></span>
                                <?php $pterms = wp_get_post_terms(get_the_ID(), 'favorites');
                                if ($pterms && !is_wp_error($pterms)) :
                                    foreach (array_slice($pterms, 0, 2) as $pt) : ?>
                                <a href="<?php echo esc_url(get_term_link($pt)); ?>" class="badge vc-l-theme text-ss mr-1"><i class="iconfont icon-folder mr-1"></i><?php echo esc_html($pt->name); ?></a>
                                <?php endforeach; endif; ?>
                            </div>

                            <?php if (has_post_thumbnail()) : ?>
                            <div class="mb-4 text-center">
                                <img src="<?php the_post_thumbnail_url('large'); ?>" class="img-fluid rounded-lg" alt="<?php the_title(); ?>">
                            </div>
                            <?php endif; ?>

                            <div class="single-entry">
                                <?php the_content(); ?>
                            </div>

                            <?php the_tags('<div class="item-tags mt-4">', '', '</div>'); ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <?php
                $prev_link = get_previous_post();
                $next_link = get_next_post();
                if ($prev_link || $next_link) :
                ?>
                <div class="d-flex justify-content-between mt-3 text-sm">
                    <div>
                        <?php if ($prev_link) : ?>
                        <a class="text-muted" href="<?php echo esc_url(get_permalink($prev_link)); ?>"><i class="iconfont icon-arrow-l"></i> <?php echo esc_html($prev_link->post_title); ?></a>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if ($next_link) : ?>
                        <a class="text-muted" href="<?php echo esc_url(get_permalink($next_link)); ?>"><?php echo esc_html($next_link->post_title); ?> <i class="iconfont icon-arrow-r-m"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php
                if (function_exists('theme_get_related_posts')) :
                    $related_posts = theme_get_related_posts(get_the_ID(), 4);
                    if ($related_posts->have_posts()) :
                ?>
                <div class="mt-4">
                    <div class="sidebar-header mb-2">
                        <div class="card-header widget-header">
                            <h3 class="text-md mb-0"><i class="iconfont icon-post mr-2"></i>相关文章</h3>
                        </div>
                    </div>
                    <div class="posts-row row-col-2a row-col-sm-2a row-col-md-4a row-col-lg-4a row-col-xl-4a">
                        <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                        <article class="posts-item post-item d-flex style-post-min post-<?php the_ID(); ?>">
                            <div class="item-header">
                                <div class="item-media">
                                    <a class="item-image" href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                        <img class="fill-cover lazy unfancybox" src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>" loading="lazy">
                                        <?php else : ?>
                                        <img class="fill-cover lazy unfancybox" src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_the_title()); ?>&background=random&size=120" alt="<?php the_title(); ?>" loading="lazy">
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </div>
                            <div class="item-body d-flex flex-column flex-fill">
                                <h3 class="item-title line2"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
                                <div class="mt-auto">
                                    <div class="item-meta d-flex align-items-center text-muted text-xs">
                                        <span class="meta-time"><?php echo get_the_date(); ?></span>
                                        <span class="meta-view ml-auto"><i class="iconfont icon-chakan-line"></i><?php echo function_exists('theme_get_post_views') ? (int) theme_get_post_views(get_the_ID()) : 0; ?></span>
                                    </div>
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
        <?php theme_render_single_sidebar('post'); ?>
    </div>
</div>

<?php get_footer(); ?>
