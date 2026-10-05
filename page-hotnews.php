<?php
/*
Template Name: 今日热榜页
*/
get_header();
$sources = function_exists('theme_hotlist_sources') ? theme_hotlist_sources() : array();
// 各来源徽标配色与短名
$source_themes = array(
    array('bg' => '#2932e1', 'short' => '百', 'slug' => '热搜'),
    array('bg' => '#e6162d', 'short' => '微', 'slug' => '热搜'),
    array('bg' => '#161823', 'short' => '抖', 'slug' => '热点'),
);
?>
<style>
.hotapi-page-grid{display:grid;grid-template-columns:repeat(1,1fr);gap:16px}
@media(min-width:768px){.hotapi-page-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1200px){.hotapi-page-grid{grid-template-columns:repeat(3,1fr)}}
.hotapi-page-card{overflow:hidden}
.hotapi-page-card .card-header{display:flex;align-items:center;padding:12px 16px;border-bottom:1px solid var(--border-color,rgba(116,116,116,.12))}
.hotapi-page-card .hotapi-badge-ico{flex:0 0 auto;width:30px;height:30px;border-radius:8px;color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:600;margin-right:10px}
.hotapi-page-card .title-name{font-size:15px;font-weight:600}
.hotapi-page-card .slug-name{margin-left:auto;font-size:12px;color:var(--muted-color);background:rgba(116,116,116,.08);padding:2px 8px;border-radius:10px}
.hotapi-page-card .card-body{padding:8px 12px 12px;max-height:560px;overflow-y:auto}
.hotapi-page-list{list-style:none;margin:0;padding:0}
.hotapi-page-list li{display:flex;align-items:center;gap:8px;padding:7px 4px;border-bottom:1px dashed rgba(116,116,116,.1)}
.hotapi-page-list li:last-child{border-bottom:0}
.hotapi-page-list .hotapi-rank{flex:0 0 auto;width:20px;height:20px;font-size:12px;border-radius:4px;background:rgba(116,116,116,.12);color:var(--muted-color);display:flex;align-items:center;justify-content:center}
.hotapi-page-list .hotapi-rank.vc-l-red{background:#f56c6c;color:#fff}
.hotapi-page-list .hotapi-rank.vc-l-yellow{background:#e6a23c;color:#fff}
.hotapi-page-list .hotapi-rank.vc-l-purple{background:#9b59b6;color:#fff}
.hotapi-page-list a{flex:1;min-width:0;color:inherit;display:flex;align-items:center;gap:8px}
.hotapi-page-list a:hover{color:var(--theme-color)}
.hotapi-page-list .t{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:14px}
.hotapi-page-list .hot-heat{flex:0 0 auto;font-size:12px;color:var(--muted-color)}
.hotapi-page-status{padding:24px 0;text-align:center;color:var(--muted-color);font-size:14px}
.hotapi-page-refresh{position:absolute;top:12px;right:14px;color:var(--muted-color)}
.hotnews-page-head{display:flex;align-items:center;margin-bottom:18px}
.hotnews-page-head h1{font-size:22px;margin:0}
.hotnews-page-head .text-muted{margin-left:12px;font-size:13px}
</style>
<div class="ioui-content switch-container container sidebar_no py-4">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="hotnews-page-head position-relative">
                <h1><i class="iconfont icon-hot mr-2" style="color:#f56c6c"></i>今日热点</h1>
                <span class="text-muted">聚合全网实时热搜榜单，每小时自动更新</span>
                <a href="javascript:;" class="hotapi-page-refresh-all hotapi-page-refresh" title="全部刷新"><i class="iconfont icon-refresh text-md"></i></a>
            </div>
            <?php if (empty($sources)) : ?>
            <div class="card"><div class="card-body hotapi-page-status">热榜数据源未配置，请在后台「主题设置 - 功能模块 - 热门榜单」中检查。</div></div>
            <?php else : ?>
            <div class="hotapi-page-grid">
                <?php foreach ($sources as $i => $source) :
                    $theme = $source_themes[$i] ?? array('bg' => '#8618db', 'short' => mb_substr($source[0], 0, 1), 'slug' => '热榜');
                ?>
                <div class="card hotapi-card hotapi-page-card hotapi-api-<?php echo (int) $i; ?>" data-index="<?php echo (int) $i; ?>">
                    <div class="card-header card-h-w widget-header">
                        <span class="hotapi-badge-ico" style="background:<?php echo esc_attr($theme['bg']); ?>"><?php echo esc_html($theme['short']); ?></span>
                        <span class="title-name"><?php echo esc_html($source[0]); ?></span>
                        <span class="slug-name"><?php echo esc_html($theme['slug']); ?></span>
                    </div>
                    <div class="card-body">
                        <ul class="hotapi-body hotapi-page-list">
                            <li class="hotapi-page-status">加载中...</li>
                        </ul>
                        <div class="hotapi-page-status" style="display:none"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
jQuery(function($) {
    var ajaxUrl = (typeof theme_data !== 'undefined') ? theme_data.ajaxurl : '';

    function renderList($card, items) {
        var html = '';
        $.each(items, function(i, item) {
            var rankCls = i === 0 ? 'vc-l-red' : (i === 1 ? 'vc-l-yellow' : (i === 2 ? 'vc-l-purple' : ''));
            var title = $('<i>').text(item.title).html();
            html += '<li><a href="' + item.url + '" target="_blank" rel="external nofollow noopener">'
                + '<span class="hotapi-rank ' + rankCls + '">' + (i + 1) + '</span>'
                + '<span class="t">' + title + '</span>'
                + (item.hot ? '<span class="hot-heat">' + $('<i>').text(item.hot).html() + '</span>' : '')
                + '</a></li>';
        });
        $card.find('.hotapi-page-list').html(html);
    }

    function loadCard($card, refresh) {
        var $status = $card.find('.hotapi-page-status').last();
        $card.find('.hotapi-page-list').html('<li class="hotapi-page-status">加载中...</li>');
        $.post(ajaxUrl, {action: 'theme_hot_list', source: $card.data('index'), refresh: refresh ? 1 : 0}, function(res) {
            if (res && res.success) {
                renderList($card, res.data.items);
            } else {
                $card.find('.hotapi-page-list').html('');
                $status.text((res && res.data && res.data.msg) ? res.data.msg : '加载失败').show();
            }
        }, 'json').fail(function() {
            $card.find('.hotapi-page-list').html('');
            $status.text('加载失败').show();
        });
    }

    // 首屏自动加载全部卡片
    $('.hotapi-page-card').each(function() {
        loadCard($(this), false);
    });

    // 全部刷新
    $('.hotapi-page-refresh-all').on('click', function(e) {
        e.preventDefault();
        $('.hotapi-page-card').each(function() {
            loadCard($(this), true);
        });
    });
});
</script>
<?php get_footer(); ?>
