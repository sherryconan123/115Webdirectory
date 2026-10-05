<?php
/*
Template Name: 次级导航页
*/
get_header();
$collections = function_exists('theme_get_top_collections') ? theme_get_top_collections(true) : array();
$hot_posts = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 15,
    'orderby'        => 'date',
    'order'          => 'DESC',
));
?>
<style>
.mininav-layout{display:grid;grid-template-columns:1fr;gap:20px}
@media(min-width:992px){.mininav-layout{grid-template-columns:220px 1fr}}
.mininav-cats{list-style:none;margin:0;padding:8px 0}
.mininav-cats li a{display:flex;align-items:center;padding:9px 16px;color:inherit;font-size:14px}
.mininav-cats li a:hover{background:rgba(116,116,116,.07);color:var(--theme-color)}
.mininav-cats .iconfont{margin-right:8px}
.mininav-posts{list-style:none;margin:0;padding:0}
.mininav-posts li{display:flex;align-items:center;gap:12px;padding:13px 18px;border-bottom:1px solid var(--border-color,rgba(116,116,116,.1))}
.mininav-posts li:last-child{border-bottom:0}
.mininav-posts a{flex:1;min-width:0;color:inherit;font-size:15px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.mininav-posts a:hover{color:var(--theme-color)}
.mininav-posts .post-date{flex:0 0 auto;color:var(--muted-color);font-size:13px}
.mininav-posts .post-views{flex:0 0 56px;text-align:right;color:var(--muted-color);font-size:13px}
</style>
<div class="ioui-content switch-container container sidebar_no py-4">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="mininav-layout">
                <div class="card">
                    <ul class="mininav-cats">
                        <?php foreach ($collections as $collection) : ?>
                        <li><a href="<?php echo esc_url(get_term_link($collection)); ?>"><i class="<?php echo esc_attr(theme_get_category_icon($collection->slug)); ?>"></i><?php echo esc_html($collection->name); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card">
                    <div class="card-header widget-header" style="padding:14px 18px;border-bottom:1px solid var(--border-color,rgba(116,116,116,.1))">
                        <h3 class="text-md m-0"><i class="iconfont icon-hot mr-2"></i>热门文章</h3>
                    </div>
                    <ul class="mininav-posts">
                        <?php while ($hot_posts->have_posts()) : $hot_posts->the_post(); ?>
                        <li>
                            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                            <span class="post-date"><?php echo esc_html(human_time_diff(get_the_time('U'), current_time('timestamp'))); ?>前</span>
                            <span class="post-views"><?php echo esc_html(theme_format_num((int) get_post_meta(get_the_ID(), 'post_views', true))); ?></span>
                        </li>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
