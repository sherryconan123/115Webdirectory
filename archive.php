<?php get_header(); ?>

<div class="ioui-content switch-container container">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="content-layout">
                <?php theme_breadcrumb(); ?>
                
                <div class="content-card">
                    <div class="d-flex flex-fill align-items-center mb-4">
                        <h4 class="tab-title text-gray text-lg font-bold m-0">
                            <span class="site-tag mr-1" style="font-size: 20px;">📦</span><?php the_archive_title(); ?>
                        </h4>
                        <div class="flex-fill"></div>
                        <div class="meta-ico text-muted text-xs">
                            <span class="meta-view"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg><?php echo $wp_query->found_posts; ?> 条内容</span>
                        </div>
                    </div>
                    
                    <?php if (have_posts()) : ?>
                        <div class="posts-row row-col-1a row-col-sm-1a row-col-md-2a row-col-lg-1a row-col-xl-2a row-col-xxl-2a">
                            <?php while (have_posts()) : the_post(); ?>
                            <article class="posts-item post-item d-flex style-post-min post-<?php the_ID(); ?>">
                                <div class="item-header">
                                    <div class="item-media">
                                        <a class="item-image" href="<?php the_permalink(); ?>">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <img class="fill-cover lazy unfancybox" src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                                            <?php else : ?>
                                                <img class="fill-cover lazy unfancybox" src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_the_title()); ?>&background=random&size=120" alt="<?php the_title(); ?>">
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="item-body d-flex flex-column flex-fill">
                                    <h3 class="item-title line2">
                                        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <div class="mt-auto">
                                        <div class="line1 text-muted text-sm d-none d-md-block"><?php echo wp_trim_words(get_the_content(), 30); ?></div>
                                        <div class="item-meta d-flex align-items-center flex-fill text-muted text-xs">
                                            <div class="meta-left">
                                                <span class="meta-time"><?php echo get_the_date(); ?></span>
                                            </div>
                                            <div class="ml-auto meta-right">
                                                <span class="meta-view"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg><?php echo theme_get_site_views(get_the_ID()); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                            <?php endwhile; ?>
                        </div>
                        <?php theme_pagination(); ?>
                    <?php else : ?>
                        <div class="text-center py-10 text-muted">暂无相关内容</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>