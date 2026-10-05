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
$range_desc   = array(
    'today' => '根据今天浏览量降序排列',
    'week'  => '根据近7天浏览量降序排列',
    'month' => '根据近30天浏览量降序排列',
    'all'   => '根据累计浏览量降序排列',
);
$data = theme_get_ranking_items($type, $range, 1, 20);
?>
<style>
.ranking-page .ajax-posts-row .posts-item{padding:12px 4px;border-bottom:1px dashed var(--border-color,rgba(116,116,116,.12))}
.ranking-page .ajax-posts-row .posts-item:last-child{border-bottom:0}
.ranking-page .rank-num{flex:0 0 26px;width:26px;height:26px;font-size:13px;font-weight:600;border-radius:6px;background:rgba(116,116,116,.12);color:var(--muted-color)}
.ranking-page .rank-num.vc-l-red{background:#f56c6c;color:#fff}
.ranking-page .rank-num.vc-l-yellow{background:#e6a23c;color:#fff}
.ranking-page .rank-num.vc-l-purple{background:#9b59b6;color:#fff}
.ranking-page .sites-body{min-width:0}
.ranking-page .range-tab-btn.vc-theme,.ranking-page .range-btn.vc-theme{color:#fff;border-color:transparent}
</style>
<div class="ioui-content switch-container container sidebar_no py-4">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="ranking-page">
                <div class="ranking-type-nav mb-3">
                    <?php
                    $type_icons = array('sites' => 'icon-sites', 'post' => 'icon-article', 'book' => 'icon-book', 'app' => 'icon-app');
                    foreach ($types as $tkey => $tcfg) :
                        $active = ($tkey === $type);
                    ?>
                    <a href="<?php echo esc_url(add_query_arg(array('type' => $tkey, 'range' => $range === 'today' ? 'today' : $range), home_url('/rankings/'))); ?>"
                       class="ajax-posts-load range-tab-btn btn <?php echo $active ? 'vc-theme' : 'vc-l-theme btn-outline'; ?> mr-2 mb-2"
                       data-type="<?php echo esc_attr($tkey); ?>"<?php echo $active ? ' data-active="1"' : ''; ?>>
                        <i class="iconfont <?php echo esc_attr($type_icons[$tkey] ?? 'icon-sites'); ?> mr-1"></i><?php echo esc_html($tcfg['label']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <div class="ranking-panel">
                    <div class="card">
                        <div class="card-header d-flex align-items-center flex-wrap">
                            <div class="ranking-range-nav mr-3">
                                <div class="ranking-range-body btn-group btn-group-sm">
                                    <?php foreach ($range_labels as $rkey => $rlabel) : ?>
                                    <a href="javascript:;" class="ajax-posts-load is-tab-btn range-btn btn btn-outline <?php echo ($rkey === $range) ? 'active vc-theme' : ''; ?>"
                                       data-range="<?php echo esc_attr($rkey); ?>"><?php echo esc_html($rlabel); ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="ranking-subtitle text-muted text-sm mt-2 mt-md-0">
                                <b class="ranking-type-name"><?php echo esc_html($types[$type]['label']); ?></b>
                                ·
                                <span class="ranking-range-name"><?php echo esc_html(str_replace(array('日', '周', '月', '总'), '', $range_labels[$range]) . '热度' . $range_labels[$range]); ?></span>
                                <span class="ranking-desc ml-1">/ <?php echo esc_html($range_desc[$range]); ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="posts-row ajax-posts-row row-col-1a" data-type="<?php echo esc_attr($type); ?>" data-range="<?php echo esc_attr($range); ?>" data-page="1">
                                <?php
                                $rank = 1;
                                foreach ($data['posts'] as $p) {
                                    theme_render_ranking_row($p, $rank++, $type, isset($data['views_map'][$p->ID]) ? $data['views_map'][$p->ID] : 0);
                                }
                                if (empty($data['posts'])) {
                                    echo '<div class="w-100 text-center py-5 text-muted">暂无榜单数据</div>';
                                }
                                ?>
                            </div>
                            <div class="text-center mt-3">
                                <a href="javascript:;" class="btn vc-l-theme btn-outline btn-sm px-4 ranking-load-more"<?php echo ($data['page'] * $data['per_page'] >= $data['found']) ? ' style="display:none"' : ''; ?>>
                                    <i class="iconfont icon-add mr-1"></i>加载更多
                                </a>
                                <div class="ranking-loading text-muted text-sm mt-2" style="display:none">加载中...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) {
    var ajaxUrl = (typeof theme_data !== 'undefined') ? theme_data.ajaxurl : '';
    var nonce = (typeof theme_data !== 'undefined') ? theme_data.nonce : '';

    function loadRanking(type, range, page, append) {
        var $row = $('.ajax-posts-row');
        var $more = $('.ranking-load-more');
        $('.ranking-loading').show();
        if (!append) {
            $row.html('<div class="w-100 text-center py-5 text-muted">加载中...</div>');
        }
        $.post(ajaxUrl, {action: 'theme_ranking_list', nonce: nonce, type: type, range: range, page: page}, function(res) {
            $('.ranking-loading').hide();
            if (res && res.success) {
                if (append) {
                    $row.append(res.data.html);
                } else {
                    $row.html(res.data.html || '<div class="w-100 text-center py-5 text-muted">暂无榜单数据</div>');
                }
                $row.data('page', res.data.page).data('type', type).data('range', range);
                if (res.data.has_more) {
                    $more.show();
                } else {
                    $more.hide();
                }
            } else {
                $row.html('<div class="w-100 text-center py-5 text-muted">加载失败，请稍后重试</div>');
                $more.hide();
            }
        }, 'json').fail(function() {
            $('.ranking-loading').hide();
            if (!append) {
                $row.html('<div class="w-100 text-center py-5 text-muted">加载失败，请稍后重试</div>');
            }
        });
    }

    // 类型切换：整页跳转（保留URL可分享，与目标站一致）
    $('.ranking-type-nav .range-tab-btn').on('click', function(e) {
        // 已激活项不跳转
        if ($(this).data('active')) {
            e.preventDefault();
        }
    });

    // 时间范围切换：AJAX 无刷新
    $('.ranking-range-nav').on('click', '.range-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        if ($btn.hasClass('active')) return;
        $btn.addClass('active vc-theme').siblings().removeClass('active vc-theme');
        var type = $('.ajax-posts-row').data('type');
        var range = $btn.data('range');
        var rangeNames = {today: '热度日榜', week: '热度周榜', month: '热度月榜', all: '热度总榜'};
        var rangeDescs = {
            today: '根据今天浏览量降序排列',
            week: '根据近7天浏览量降序排列',
            month: '根据近30天浏览量降序排列',
            all: '根据累计浏览量降序排列'
        };
        $('.ranking-range-name').text(rangeNames[range]);
        $('.ranking-desc').text('/ ' + rangeDescs[range]);
        // 同步类型tab链接中的range参数
        $('.ranking-type-nav .range-tab-btn').each(function() {
            var url = new URL($(this).attr('href'), window.location.origin);
            url.searchParams.set('range', range);
            $(this).attr('href', url.pathname + url.search);
        });
        loadRanking(type, range, 1, false);
    });

    // 加载更多
    $('.ranking-load-more').on('click', function(e) {
        e.preventDefault();
        var $row = $('.ajax-posts-row');
        loadRanking($row.data('type'), $row.data('range'), parseInt($row.data('page'), 10) + 1, true);
    });
});
</script>
<?php get_footer(); ?>
