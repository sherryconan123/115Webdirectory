<?php
/*
Template Name: 新标签页
*/
get_header();
$quick_sites = new WP_Query(array(
    'post_type'      => 'sites',
    'post_status'    => 'publish',
    'posts_per_page' => 24,
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'site_views',
    'order'          => 'DESC',
));
?>
<style>
.bookmark-page{max-width:960px;margin:0 auto;padding:40px 16px 60px;text-align:center}
.bookmark-clock{font-size:56px;font-weight:700;letter-spacing:2px;line-height:1.2;font-variant-numeric:tabular-nums}
.bookmark-date{color:var(--muted-color);margin-top:6px;font-size:15px}
.bookmark-search{max-width:560px;margin:28px auto 0}
.bookmark-search .form-control{height:48px;border-radius:24px 0 0 24px;border:0;padding:0 22px;font-size:15px}
.bookmark-search .btn{border-radius:0 24px 24px 0;height:48px;padding:0 28px}
.bookmark-sites{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:40px}
@media(min-width:640px){.bookmark-sites{grid-template-columns:repeat(6,1fr)}}
@media(min-width:900px){.bookmark-sites{grid-template-columns:repeat(8,1fr)}}
.bookmark-site{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:14px 6px;border-radius:12px;color:inherit;transition:background .2s}
.bookmark-site:hover{background:rgba(116,116,116,.08)}
.bookmark-site img{width:36px;height:36px;border-radius:8px;object-fit:cover}
.bookmark-site .name{margin-top:8px;font-size:12px;max-width:72px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.bookmark-help{margin-top:46px;text-align:left}
.bookmark-help .favorites-body{padding:22px 26px}
.bookmark-help h4{font-size:15px;font-weight:600;margin:0 0 8px}
.bookmark-help h4:not(:first-child){margin-top:18px}
.bookmark-help code{background:rgba(116,116,116,.12);padding:2px 8px;border-radius:4px}
</style>
<div class="ioui-content switch-container container sidebar_no">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="bookmark-page">
                <div class="bookmark-clock" id="bookmark-clock">00:00:00</div>
                <div class="bookmark-date" id="bookmark-date"></div>
                <form class="bookmark-search d-flex" action="https://www.baidu.com/s" method="get" target="_blank">
                    <input type="text" name="wd" class="form-control" placeholder="搜索一下，你就知道" autocomplete="off">
                    <button type="submit" class="btn vc-theme"><i class="iconfont icon-search mr-1"></i>搜索</button>
                </form>
                <div class="bookmark-sites">
                    <?php
                    while ($quick_sites->have_posts()) : $quick_sites->the_post();
                        $fav = function_exists('theme_get_local_favicon') ? theme_get_local_favicon(get_the_ID()) : '';
                        $icon = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') : ($fav ?: 'https://ui-avatars.com/api/?name=' . urlencode(get_the_title()) . '&background=random&color=fff&size=72');
                        $url = theme_get_site_url(get_the_ID()) ?: get_permalink();
                    ?>
                    <a class="bookmark-site" href="<?php echo esc_url($url); ?>" target="_blank" rel="external nofollow noopener" title="<?php the_title_attribute(); ?>">
                        <img src="<?php echo esc_url($icon); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                        <span class="name"><?php echo esc_html(get_the_title()); ?></span>
                    </a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <div class="bookmark-help">
                    <div class="card favorites-body fx-header-bg">
                        <div class="position-relative">
                            <h4 class="text-md">加入收藏夹</h4>
                            按 <code>Ctrl+D</code>（Mac 为 <code>Command+D</code>）可收藏本页，方便快速打开使用。
                            <h4 class="text-md">设为首页</h4>
                            在浏览器 <b>设置页面</b> &gt; <b>启动时</b> 选项下选择 <b>打开特定网页或一组网页</b>，将本页地址 <?php echo esc_url(home_url('/bookmark/')); ?> 添加为启动页。
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function() {
    function pad(n) { return n < 10 ? '0' + n : n; }
    function tick() {
        var d = new Date();
        document.getElementById('bookmark-clock').textContent = pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
        var week = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'][d.getDay()];
        document.getElementById('bookmark-date').textContent = d.getFullYear() + '年' + (d.getMonth() + 1) + '月' + d.getDate() + '日 ' + week;
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
<?php get_footer(); ?>
