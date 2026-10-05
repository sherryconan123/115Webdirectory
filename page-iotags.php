<?php
/*
Template Name: 快速筛选页
*/
get_header();
$tag_groups = array(
    array('taxonomy' => 'post_tag', 'title' => '文档标签', 'desc' => '文章资讯标签', 'icon' => 'icon-article'),
    array('taxonomy' => 'sitetag',  'title' => '网址标签', 'desc' => '网站收录标签', 'icon' => 'icon-sites'),
    array('taxonomy' => 'apptag',   'title' => 'APP标签',  'desc' => '软件下载标签', 'icon' => 'icon-app'),
    array('taxonomy' => 'booktag',  'title' => '书籍标签', 'desc' => '书籍期刊标签', 'icon' => 'icon-book'),
);
?>
<style>
.iotags-group{margin-bottom:22px}
.iotags-group .card-header{padding:14px 18px;border-bottom:1px solid var(--border-color,rgba(116,116,116,.12));display:flex;align-items:center}
.iotags-group .card-header h3{font-size:16px;margin:0}
.iotags-group .card-header .text-muted{margin-left:10px;font-size:13px}
.iotags-cloud{padding:18px;display:flex;flex-wrap:wrap;gap:10px}
.iotags-cloud a{display:inline-block;padding:5px 14px;border-radius:16px;background:rgba(116,116,116,.07);font-size:13px;color:inherit;line-height:1.6;transition:all .2s}
.iotags-cloud a:hover{background:var(--theme-color);color:#fff}
.iotags-empty{padding:30px 18px;text-align:center;color:var(--muted-color);font-size:14px}
.iotags-page-head{margin-bottom:20px}
.iotags-page-head h1{font-size:22px;margin:0 0 6px}
</style>
<div class="ioui-content switch-container container sidebar_no py-4">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="iotags-page-head">
                <h1><i class="iconfont icon-tags mr-2"></i>快速筛选</h1>
                <div class="text-muted text-sm">通过标签快速发现全站内容</div>
            </div>
            <?php foreach ($tag_groups as $group) :
                if (!taxonomy_exists($group['taxonomy'])) {
                    continue;
                }
                $terms = get_terms(array(
                    'taxonomy'   => $group['taxonomy'],
                    'hide_empty' => false,
                    'number'     => 200,
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                ));
            ?>
            <div class="card iotags-group">
                <div class="card-header widget-header">
                    <h3><i class="iconfont <?php echo esc_attr($group['icon']); ?> mr-2"></i><?php echo esc_html($group['title']); ?></h3>
                    <span class="text-muted"><?php echo esc_html($group['desc']); ?></span>
                    <span class="ml-auto text-muted text-sm">共 <?php echo is_wp_error($terms) ? 0 : count($terms); ?> 个标签</span>
                </div>
                <?php if (is_wp_error($terms) || empty($terms)) : ?>
                <div class="iotags-empty">暂无标签</div>
                <?php else : ?>
                <div class="iotags-cloud">
                    <?php foreach ($terms as $term) : ?>
                    <a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?><span class="ml-1 text-muted"><?php echo (int) $term->count; ?></span></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>
