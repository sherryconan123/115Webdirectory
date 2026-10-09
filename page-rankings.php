<?php
/*
Template Name: 排行榜页
*/
get_header();
$type  = isset($_GET['type']) ? sanitize_key($_GET['type']) : 'sites';
$range = isset($_GET['range']) ? sanitize_key($_GET['range']) : 'today';
$types = theme_ranking_types();
if (!isset($types[$type])) {
    $type = 'sites';
}
if (!in_array($range, array('today', 'week', 'month', 'all'), true)) {
    $range = 'today';
}
$range_labels = array('today' => '日榜', 'week' => '周榜', 'month' => '月榜', 'all' => '总榜');
$range_chars  = array('today' => '日', 'week' => '周', 'month' => '月', 'all' => '总');
$range_desc   = array(
    'today' => '根据今天浏览量降序排列',
    'week'  => '根据近7天浏览量降序排列',
    'month' => '根据近30天浏览量降序排列',
    'all'   => '根据累计浏览量降序排列',
);
$short_names  = array('sites' => '网址', 'post' => '文章', 'book' => '书籍', 'app' => '软件');
// 分类环形 tab 类位（对标目标站：当前项居中，右侧 --r1、左侧 --l1 半透明，其余 --l2 隐藏）
$type_keys = array_keys($types);
$cur       = array_search($type, $type_keys, true);
$ring_class = array();
foreach ($type_keys as $i => $k) {
    if ($i === $cur) {
        $ring_class[$k] = 'active';
    } elseif ($i === ($cur + 1) % 4) {
        $ring_class[$k] = '--r1';
    } elseif ($i === ($cur + 3) % 4) {
        $ring_class[$k] = '--l1';
    } else {
        $ring_class[$k] = '--l2';
    }
}
$data = theme_get_ranking_items($type, $range, 1, 16);
$rankings_url = home_url('/rankings/');
?>
<div class="color-head color-head-rankings" id="ranking-head">
    <div class="color-head-bg"><span class="color01"></span></div>
    <div class="color-head-content page-head-content my-2 my-md-4">
        <div class="range-nav position-relative text-md">
            <?php foreach ($types as $tkey => $tcfg) : ?>
            <a href="<?php echo esc_url(add_query_arg(array('type' => $tkey, 'range' => 'today'), $rankings_url)); ?>"
               class="ajax-posts-load range-tab-btn ranking-ajax-link <?php echo esc_attr($ring_class[$tkey]); ?>"
               data-zones="head,main"><?php echo esc_html($tcfg['label']); ?></a>
            <?php endforeach; ?>
        </div>
        <h1 class="ranking-title h2"><?php echo esc_html($short_names[$type] . '热度' . $range_chars[$range] . '榜'); ?></h1>
        <div class="ranking-desc text-xs"><?php echo esc_html($range_desc[$range]); ?></div>
    </div>
</div>
<main class="container my-2">
    <div class="content-wrap">
        <div class="content-layout">
            <div class="ranking-panel d-flex flex-column flex-md-row" id="ranking-main">
                <div class="ranking-range-nav">
                    <div class="ranking-range-body">
                        <?php foreach ($range_labels as $rkey => $rlabel) : ?>
                        <a href="<?php echo esc_url(add_query_arg(array('type' => $type, 'range' => $rkey), $rankings_url)); ?>"
                           class="ajax-posts-load is-tab-btn range-btn ranking-ajax-link <?php echo ($rkey === $range) ? 'active' : ''; ?>"
                           data-zones="head,main"><?php echo esc_html($rlabel); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card position-relative flex-fill">
                    <div class="ranking-h-ico"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024"><g opacity=".7" fill-rule="evenodd" clip-rule="evenodd" fill="#FFF"><path opacity=".6" d="M51.2 401l1.3-9.1 1.7-7.3 2.4-6 2.6-4.7 2.9-3.3 3.2-2.5 3.5-1.5 7.3-.5 4.7.9 5.5 1.8 6.9 3.2 7.9 4.4 8.9 6.1 17.4 14.8 9.5 9.6 8.9 10 9.1 11.2 9.2 12.6 8.6 13.2 8.2 14.3 8 15.3 6.9 15.8 6.4 16.9 5.9 17.8 4.6 18.2 3.8 17.6 2.7 17.1 1.9 17.1 1.1 16.2.2 15.2-1.5 29.5-3.9 25.9-5.7 21-3.7 9.6-3.8 7.8-3.7 6.1-3.6 4.3-3.2 3.2-4.3 2.2.2-30-.3-28.2-1.4-25.3-2.4-23.1-2.8-20.5-3.8-18.5-4.1-16.5-5.2-15.9-6.8-17.9-8.7-20.4-10.9-22.8-13.2-25.7-16.1-28.6-19.1-31.9-3 1.8 19 31.9 16 28.4 13.2 25.6 10.8 22.8 8.7 20.2 6.7 17.8 5.2 15.7 4 16.3 3.8 18.3 2.8 20.3 2.3 22.9 1.4 25.3.4 28-.2 31.4-2.3.3-4.1-.5-4.5-1.5-5-2.8-5.7-4.6-6-5.7-6.4-7.9-7.1-9.6-7.7-12.3-9.1-16.2-9.6-19.4-10.2-23-9.2-23.1-9.3-26-9.4-28.9-8.6-29.2-6.7-26-5.2-23.5-4.3-23.7-2.8-20.1-1.3-17.3-.3-13.4z"></path><path d="M11.9 594.2l.7-5.7 2.5-7.6 3.9-4.3 3-1.8 7.3-1.8h4.5l5.7.9 6.9 2.4 8.2 4.1 8 4.8 9.3 6.7 11 9.3 22.5 22.1 33.6 37.9 16 19.4 10.2 12.8 14.4 20.7 5.7 10 3.6 7.8 1.9 6.1.6 4.8-.6 3.5-1.4 2.9-2.5 2.3-1.4.8-15.9-7.3-11.6-5.8-11.7-7-11.4-8.3-11.5-9.6-11.3-10.9-12.7-13.7-12.3-14.3L75 660.3l-11.5-15.6-11.2-16.5-2.9 1.9 11.2 16.6 11.6 15.7 12.2 15.2 12.3 14.5 12.9 13.8L121 717l11.7 9.7 11.7 8.4 11.9 7.2 11.8 6 12.6 5.7-2 .5-6.7.9-14.8-.3-18.4-3.2-20.3-6.8-10.1-4.6-10.3-5.7-10.7-6.8-10-7.7-9.8-8.7-9.8-10.3-8.7-10.9-7.9-10.8-6.6-10.7-11-21.4-7.3-20.1-3.7-17.7-.9-8.3z"></path></g><g opacity=".7" fill-rule="evenodd" clip-rule="evenodd" fill="#FFF"><path opacity=".6" d="M912.7 726.8l1.9-11.9 3.1-12.5 3.2-9.8 7.6-17.4 4.5-8.2 9.4-13.9 10.2-11.6 9.8-8.6 8.3-5.1 4.1-1.6 3.2-.6 2.5.4 2.5 1.2 2.2 2.5 1.7 3.8 1.3 5.9 1.3 5.9.5 7.6-.3 10.2-1.6 13.3-2.5 13-8.8 33.2-6.2 19.2-6.2 16.4-7 15.1-6.2 11.6-4.5 7.1-7.8 9.1-5.9 3.7-3.5.2-.2-19.4.5-16.9 1.4-14.5 2.2-12.4 2.4-10.9 3.4-10.2 5.2-12.2 6.7-14.6 9.1-17.1 11.5-19.9-1.5-.9-11.6 20-9.1 17.1-6.8 14.7-5.2 12.3-3.4 10.4-2.5 10.9-2.1 12.5-1.5 14.7-1.5 14.7-.5 17 .2 18.8-3.2-2.1-2.3-2.8-4.5-9.6-3.7-16.1-1.1-8.7v-21.4z"></path><path d="M925.5 795.2l3.2-7.8 8.7-13.2 20.3-24.8 15.5-16L985 724l4.8-2.5 3.9-1.5 3.9-.5 3 .4 2.5 1.2 1.5 1.6 1 2.3.7 7.5-1.6 9.8-3.7 11-3 6.3-7.5 12.3-5 6.2-5.5 5.7-5.5 4.8-5.7 3.9-12 6.1-5.5 1.9-10.7 2.2h-8.3l-1.9-.7 6.3-3 6.5-3.4 6.5-4.1 6.3-5.2 6.3-6.1 9.4-10.6 9.2-11.4 8.5-12.5-1.4-1.1-8.6 12.5-9 11.4-9.4 10.4-6.2 6-6.2 5.2-6.3 4-6.4 3.3-7.9 3.8-.7-.3-1.3-1.3-.7-1.7z"></path></g><path opacity=".6" fill="#FFF" d="M334.7 418.5l.4 6.2 1.1 6.4 1.8 6.1 2.7 5.7 3.4 5.7 54.9 78.7 4.3 10.3 2 10.7-.4 11-2.8 10.9-32.1 83.9-2.1 7.5-1.1 7.3v7.5l1.2 7.5 2.1 7.1 3.2 6.9 4.3 6.4 5 5.5 5.7 4.8 6.4 3.9 6.8 2.8 7.5 2 6.4.9h6.2l6.2-.7 6.2-1.4 86.7-25.6 6.4-1.4 6.2-.7h6.2l6.2.9 6.1 1.6 5.9 2.3 5.5 2.8 5.3 3.7 70.9 56.3 5.3 3.9 5.7 3 5.9 2.3 6.2 1.4 6.2.9h6.4l6.2-.9 6.2-1.4 6.2-2.5 5.7-3 5.3-3.7 4.6-4.3 4.1-4.8 3.6-5.3 2.8-5.7 2.1-6.1 1.4-6.2.5-6.6 2.3-95.3 2.7-10.9 4.8-9.8 6.8-8.7 8.7-7.1 75.9-49 5.2-3.9 4.6-4.5 4.1-5 3.4-5.3 2.7-5.9 2.7-5.9 2-6.1 1.1-6.2.5-6.4-.5-6.4-1.2-6.6-2-6.1-2.7-5.7-3.4-5.3-3.9-5-4.6-4.5-5.2-3.7-5.5-3-6.1-2.5-85.8-29.9-5.9-2.5-5.5-3-5-3.7-4.6-4.3-3.9-4.8-3.4-5.2-2.4-5.7-2.1-6.1-24-86.7-2.1-6.2-2.8-5.7-3.4-5.3-4.3-4.8-4.6-4.5-5.2-3.6-5.7-3.2-5.9-2.3-6.4-1.6-6.6-.9h-6.4l-6.4.7-6.2 1.4-5.9 2.3-5.7 2.8-5.2 3.7-4.8 4.3-4.5 5-54.7 71.2-4.3 5-4.6 4.1-5.2 3.6-5.5 3-5.9 2.3-6.1 1.4-6.1.9h-6.6l-90.7-4.5h-6.6l-6.2.9-6.2 1.6-5.9 2.3-5.5 3-5.2 3.7-4.6 4.3-4.1 5-3.6 5.5-2.8 5.9-2.1 6.1-1.2 6.2-.4 6.7zm287.2-20.6c7.7-1.5 15.2 3.5 16.7 11.2l3.3 17 3.2 13.2 3.4 10.4 3.1 7.7 2.9 5.7 2.3 3.5 2.8 3 4.7 3.8 6.5 4.5 8.6 4.9 11.2 5.3 14.6 5.9c7.3 3 10.8 11.3 7.9 18.6-2.2 5.5-7.6 8.9-13.2 8.9-1.8 0-3.6-.3-5.3-1l-15-6.1-12.8-6-10.6-6.1-8.5-5.8-7.6-6.1-5.8-6.2-4-6.2-4.3-8.3-4-9.7-4.1-12.3-3.7-15.2-3.5-17.9c-1.5-7.7 3.5-15.2 11.2-16.7z"></path><path fill="#FFF" d="M348.3 550.6h4.1l4.3.7 3.9 1.4 3.6 2.1 3.2 3 26.7 29 2.7 2.5 6.4 3.6 7.1 1.2 3.7-.2 39.7-5.5 4.3-.2 4.1.5 3.9 1.2 3.7 2.1 3.2 2.5 2.7 3.4 2.1 3.7 1.2 3.9 1.2 3.9.5 4.3-.2 4.1-1.1 4.1-1.8 3.9-19.9 34.2-1.6 3.4-1.4 7.1.9 7.1 1.4 3.6 17.1 35.3 1.4 4.1.7 4.1-.2 4.3-.7 4.1-1.6 3.7-2.5 3.7-3 3-3.4 2.5-3.7 1.8-4.1 1.1-4.1.2-43.3-8.2-3.9-.5-7.1.9-6.8 3.2-31.9 29.6-6.1 4.3-3.4 1.2-5 .9-5-.2-4.6-1.2-4.5-2.3-3.9-3.2-3-3.9-2.1-4.3-1.1-5-4.1-39-.7-3.7-3-6.6-5-5.2-3.2-2-34.9-18.3-3.6-2.3-3-3-2.5-3.4-1.6-3.7-1.1-4.1-.2-4.3.5-4.3 1.2-3.9 2.1-3.6 2.7-3.4 3.2-2.5 40.2-18.5 3.2-1.8 5.3-5 3.6-6.2 1.1-3.7 7.3-38.6 1.2-4.3 1.8-3.7 2.5-3.2 3.2-2.8 3.6-2.3 4.1-1.4 4.5-1.1z"></path></svg></div>
                    <div class="card-body">
                        <div class="posts-row ajax-posts-row row-col-1a" data-style="sites-default">
                            <?php
                            if (empty($data['posts'])) {
                                echo '<div class="w-100 text-center py-5 text-muted">暂无榜单数据</div>';
                            }
                            foreach ($data['posts'] as $p) {
                                theme_render_ranking_row($p, $type);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar sidebar-tools d-none d-lg-block">
        <div class="theiaStickySidebar">
            <?php theme_render_about_website_widget(); ?>
            <?php theme_render_hotposts_widget(); ?>
            <?php theme_render_tag_cloud_widget($types[$type]['post_type']); ?>
        </div>
    </div>
</main>
<script>
// 排行榜 tab 无刷新切换（pjax：拉取整页后替换头部/主榜容器，URL 同步变化，禁用 JS 时为普通整页跳转）
document.addEventListener('click', function (e) {
    var link = e.target.closest('.ranking-ajax-link');
    if (!link || link.classList.contains('ranking-loading')) return;
    e.preventDefault();
    e.stopPropagation();
    var href = link.href;
    var zones = (link.getAttribute('data-zones') || 'head,main').split(',');
    var zoneMap = {head: 'ranking-head', main: 'ranking-main'};
    link.classList.add('ranking-loading');
    fetch(href, {credentials: 'same-origin'})
        .then(function (res) {
            if (!res.ok) throw new Error('http ' + res.status);
            return res.text();
        })
        .then(function (html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            zones.forEach(function (z) {
                var oldNode = document.getElementById(zoneMap[z]);
                var newNode = doc.getElementById(zoneMap[z]);
                if (oldNode && newNode) {
                    oldNode.replaceWith(document.importNode(newNode, true));
                }
            });
            history.pushState({ranking: true}, '', href);
            window.scrollTo({top: 0});
        })
        .catch(function () {
            window.location.href = href;
        });
}, true);
window.addEventListener('popstate', function () {
    window.location.reload();
});
</script>
<?php get_footer(); ?>
