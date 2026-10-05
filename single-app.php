<?php get_header(); ?>
<?php
while (have_posts()) : the_post();
    $post_id = get_the_ID();
    $file_size = function_exists('theme_get_file_size') ? theme_get_file_size($post_id) : '';
    $views = function_exists('theme_get_site_views') ? theme_get_site_views($post_id) : 0;
    $download_count = function_exists('theme_get_download_count') ? theme_get_download_count($post_id) : 0;
    $fav_count = (int) (get_post_meta($post_id, 'app_favorites', true) ?: 0);
    $is_faved = function_exists('theme_is_liked') ? theme_is_liked($post_id, 'favorite') : false;
    $language = function_exists('theme_get_app_language') ? theme_get_app_language($post_id) : '中文';
    $platform_icons = function_exists('theme_get_app_platform_icons') ? theme_get_app_platform_icons($post_id) : array();
    $app_version = function_exists('theme_get_app_version') ? theme_get_app_version($post_id) : '';
    $official_url = trim((string) get_post_meta($post_id, 'app_official_url', true));

    $terms = wp_get_post_terms($post_id, 'favorites');
    if (is_wp_error($terms)) $terms = array();
    $app_tags = taxonomy_exists('apptag') ? wp_get_post_terms($post_id, 'apptag') : array();
    if (is_wp_error($app_tags)) $app_tags = array();

    $thumb = has_post_thumbnail($post_id)
        ? get_the_post_thumbnail_url($post_id, 'full')
        : 'https://ui-avatars.com/api/?name=' . urlencode(mb_substr(get_the_title(), 0, 1)) . '&background=f56c6c&color=fff&size=256';
    $placeholder = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiPjwvc3ZnPg==';

    // 截图：优先 app_screenshot meta（逗号分隔多张），其次正文首图
    $shots = array_filter(array_map('trim', explode(',', (string) get_post_meta($post_id, 'app_screenshot', true))));
    if (empty($shots)) {
        $page_content = get_the_content();
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $page_content, $m)) {
            $shots = array($m[1]);
        }
    }

    // 手机查看二维码：优先后台配置的二维码服务，否则使用站内 /qr/ 或公共 API
    $qr_service = (get_option('theme_settings'))['theme_qr_url'] ?? (home_url('/qr/?text={url}&size=150&margin=10'));
    if (strpos($qr_service, '{url}') === false) {
        $qr_service = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={url}';
    }
    $qr_url = str_replace('{url}', rawurlencode(get_permalink($post_id)), $qr_service);
?>
<main class="container my-2" role="main">
    <div class="background-fx"></div>
    <?php if (function_exists('theme_render_hot_ad_bar')) theme_render_hot_ad_bar('mb-3'); ?>

    <div class="row app-content py-5 mb-xl-5 mb-0 mx-xxxl-n5">
        <!-- app信息 -->
        <div class="col">
            <div class="d-md-flex mt-n3 mb-5 my-xl-0">
                <div class="app-ico text-center mr-2 d-none d-md-block">
                    <img class="app-rounded mr-0 mr-md-3 lazy unfancybox" src="<?php echo esc_url($placeholder); ?>" data-src="<?php echo esc_url($thumb); ?>" height="auto" width="128" alt="<?php the_title_attribute(); ?>">
                </div>
                <div class="app-info flex-fill">
                    <nav class="text-xs mb-3 mb-md-1 text-muted" aria-label="breadcrumb">
                        <i class="iconfont icon-home"></i>
                        <a class="crumbs" href="<?php echo esc_url(home_url('/')); ?>">首页</a>
                        <?php foreach ($terms as $t) : ?>
                        <i class="text-color vc-theme px-1">•</i><a href="<?php echo esc_url(get_term_link($t)); ?>"><?php echo esc_html($t->name); ?></a>
                        <?php endforeach; ?>
                        <i class="text-color vc-theme px-1">•</i><span aria-current="page"><?php the_title(); ?></span>
                    </nav>
                    <div class="app-info-ico d-flex flex-md-row flex-column text-center text-md-left mb-4">
                        <div class="app-ico mb-3 d-block d-md-none">
                            <img class="app-rounded lazy unfancybox" src="<?php echo esc_url($placeholder); ?>" data-src="<?php echo esc_url($thumb); ?>" height="auto" width="68" alt="<?php the_title_attribute(); ?>">
                        </div>
                        <div class="flex-fill">
                            <h1 class="h3 mb-1"><?php the_title(); ?><?php if ($app_version) : ?><span class="text-md"><?php echo esc_html($app_version); ?></span><?php endif; ?></h1>
                            <div class="app-nature text-sm">
                                <span class="badge vc-black mr-1"><i class="iconfont icon-version mr-2"></i>官方版</span>
                                <span class="badge vc-black mr-1"><i class="iconfont icon-ad-line mr-2"></i>无广告</span>
                                <span class="badge vc-black mr-1"><i class="iconfont icon-chakan mr-2"></i><?php echo esc_html(theme_format_num($views)); ?></span>
                            </div>
                        </div>
                        <div class="posts-like mt-3 mt-md-0">
                            <a href="javascript:;" data-type="favorite" data-post_type="app" data-post_id="<?php echo (int) $post_id; ?>" class="io-posts-like btn vc-l-red text-md py-1<?php echo $is_faved ? ' liked' : ''; ?>" data-toggle="tooltip" title="收藏">
                                <i class="iconfont icon-collection-line mr-1" data-class="icon-collection icon-collection-line"></i>收藏
                                <small class="star-count text-xs"><?php echo (int) $fav_count; ?></small>
                            </a>
                        </div>
                    </div>
                    <p><?php echo esc_html(wp_trim_words(get_the_content(), 50)); ?></p>
                    <div class="table-div mb-4">
                        <div class="table-row">
                            <div class="table-title">更新日期：</div>
                            <div class="table-value"><?php echo esc_html(get_the_date('Y年n月j日')); ?></div>
                        </div>
                        <div class="table-row">
                            <div class="table-title">分类标签：</div>
                            <div class="table-value">
                                <?php if ($terms || $app_tags) : ?>
                                    <?php foreach ($terms as $t) : ?>
                                    <span class="mr-2"><a href="<?php echo esc_url(get_term_link($t)); ?>" rel="tag"><?php echo esc_html($t->name); ?></a><i class="iconfont icon-wailian text-ss"></i></span>
                                    <?php endforeach; ?>
                                    <?php foreach ($app_tags as $tag) : ?>
                                    <span class="mr-2"><a href="<?php echo esc_url(get_term_link($tag)); ?>" rel="tag"><?php echo esc_html($tag->name); ?></a><i class="iconfont icon-wailian text-ss"></i></span>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                <span class="text-muted">未分类</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="table-row">
                            <div class="table-title">语言：</div>
                            <div class="table-value"><?php echo esc_html($language); ?></div>
                        </div>
                        <div class="table-row">
                            <div class="table-title">平台：</div>
                            <div class="table-value">
                                <?php if ($platform_icons) : foreach ($platform_icons as $pi) : ?>
                                <span class="m-1" data-toggle="tooltip" title="<?php echo esc_attr($pi['title']); ?>"><i class="iconfont <?php echo esc_attr($pi['icon']); ?> mr-1"></i></span>
                                <?php endforeach; else : ?>
                                <span class="m-1" data-toggle="tooltip" title="多平台"><i class="iconfont icon-microsoft mr-1"></i></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 app-button">
                        <button type="button" class="btn btn-lg px-4 vc-theme btn-i-l mr-3 mb-2 io-ajax-modal" data-modal_size="modal-lg" data-modal_type="overflow-hidden" data-action="get_app_down_btn" data-post_id="<?php echo (int) $post_id; ?>" data-id="0">
                            <i class="iconfont icon-down mr-2"></i>立即下载
                        </button>
                    </div>
                    <p class="mb-0 text-muted text-sm">
                        <?php if ($file_size) : ?>
                        <span class="mr-2"><i class="iconfont icon-zip"></i> <span><?php echo esc_html($file_size); ?></span></span>
                        <?php endif; ?>
                        <span class="mr-2"><i class="iconfont icon-qushitubiao"></i> <span class="down-count-text count-a"><?php echo (int) $download_count; ?></span> 人已下载</span>
                        <span class="mr-2 cursor-pointer" data-toggle="tooltip" data-placement="bottom" data-html="true" title="<img src='<?php echo esc_url($qr_url); ?>' width='150'>"><i class="iconfont icon-phone"></i> 手机查看</span>
                    </p>
                </div>
            </div>
        </div>
        <!-- app信息 END -->
        <?php if (!empty($shots)) : ?>
        <!-- 截图幻灯片 -->
        <div class="col-12 col-xl-5">
            <div class="mx-auto screenshot-carousel rounded-lg">
                <div id="carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner" role="listbox">
                        <?php foreach ($shots as $si => $shot) : ?>
                        <div class="carousel-item<?php echo $si === 0 ? ' active' : ''; ?>">
                            <div class="img_wrapper">
                                <a href="<?php echo esc_url($shot); ?>" class="text-center" data-fancybox="screen" data-caption="<?php the_title_attribute(); ?>的使用截图[<?php echo $si + 1; ?>]">
                                    <img class="img-fluid lazy unfancybox" src="<?php echo esc_url($placeholder); ?>" data-src="<?php echo esc_url($shot); ?>" height="auto" width="auto" alt="<?php the_title_attribute(); ?>的使用截图[<?php echo $si + 1; ?>]">
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($shots) > 1) : ?>
                    <ol class="carousel-indicators">
                        <?php foreach ($shots as $si => $shot) : ?>
                        <li data-target="#carousel" data-slide-to="<?php echo (int) $si; ?>"<?php echo $si === 0 ? ' class="active"' : ''; ?>></li>
                        <?php endforeach; ?>
                    </ol>
                    <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
                        <i class="iconfont icon-arrow-l icon-lg" aria-hidden="true"></i><span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
                        <i class="iconfont icon-arrow-r icon-lg" aria-hidden="true"></i><span class="sr-only">Next</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- 截图幻灯片 END -->
        <?php endif; ?>
    </div>

    <div class="content">
        <div class="content-wrap">
            <div class="content-layout">
                <!-- 软件介绍正文 -->
                <div class="panel site-content card">
                    <div class="card-body">
                        <div class="panel-body single">
                            <?php the_content(); ?>
                        </div>
                        <?php if ($official_url) : ?>
                        <div class="text-center">
                            <a href="<?php echo esc_url(home_url('/go/?url=' . urlencode(base64_encode($official_url)))); ?>" target="_blank" rel="external nofollow noopener" class="btn btn-lg vc-blue btn-outline py-2 px-5 my-3">去官方网站了解更多</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 相关软件 -->
                <?php
                if (function_exists('theme_get_related_apps')) :
                    $related = theme_get_related_apps($post_id, 6);
                    if ($related->have_posts()) :
                ?>
                <h4 class="text-gray text-lg my-4"><i class="site-tag iconfont icon-tag icon-lg mr-1"></i>相关软件</h4>
                <div class="posts-row">
                    <?php while ($related->have_posts()) : $related->the_post();
                        $rpid = get_the_ID();
                        $rthumb = has_post_thumbnail($rpid) ? get_the_post_thumbnail_url($rpid, 'full') : 'https://ui-avatars.com/api/?name=' . urlencode(mb_substr(get_the_title(), 0, 1)) . '&background=random&size=256';
                        $rterms = wp_get_post_terms($rpid, 'favorites');
                        if (is_wp_error($rterms)) $rterms = array();
                        $rtags = taxonomy_exists('apptag') ? wp_get_post_terms($rpid, 'apptag') : array();
                        if (is_wp_error($rtags)) $rtags = array();
                        $ricons = function_exists('theme_get_app_platform_icons') ? theme_get_app_platform_icons($rpid) : array();
                        $rver = function_exists('theme_get_app_version') ? theme_get_app_version($rpid) : '';
                        $rfav = (int) (get_post_meta($rpid, 'app_favorites', true) ?: 0);
                    ?>
                    <article class="posts-item app-item d-flex style-app-max post-<?php echo (int) $rpid; ?> col-2a col-md-3a">
                        <div class="item-header">
                            <div class="item-media" style="background-image:linear-gradient(130deg,#f9f9f9,#e8e8e8)">
                                <a class="item-image" href="<?php the_permalink(); ?>" style="transform:scale(83%)">
                                    <img class="fill-cover lazy unfancybox" src="<?php echo esc_url($placeholder); ?>" data-src="<?php echo esc_url($rthumb); ?>" height="auto" width="auto" alt="<?php the_title_attribute(); ?>">
                                </a>
                            </div>
                        </div>
                        <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                            <h3 class="item-title line1">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?><span class="app-v text-xs"> - <?php echo $rver ? esc_html($rver) : '最新'; ?></span></a>
                            </h3>
                            <div class="app-content mt-auto">
                                <div class="text-muted text-xs line1"><?php echo esc_html(wp_trim_words(get_the_content(), 12)); ?></div>
                                <div class="app-meta d-flex align-items-center">
                                    <div class="item-tags overflow-x-auto no-scrollbar">
                                        <?php if ($rterms) : foreach (array_slice($rterms, 0, 1) as $rt) : ?>
                                        <a href="<?php echo esc_url(get_term_link($rt)); ?>" class="badge vc-l-theme text-ss mr-1" rel="tag" title="查看更多文章"><i class="iconfont icon-folder mr-1"></i><?php echo esc_html($rt->name); ?></a>
                                        <?php endforeach; endif; ?>
                                        <?php foreach (array_slice($rtags, 0, 3) as $rtag) : ?>
                                        <a href="<?php echo esc_url(get_term_link($rtag)); ?>" class="badge text-ss mr-1" rel="tag" title="查看更多文章"># <?php echo esc_html($rtag->name); ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="meta-ico text-muted text-xs">
                                        <span class="meta-comm d-none d-md-inline-block" data-toggle="tooltip" title="去评论" js-href="#comments"><i class="iconfont icon-comment"></i><?php echo (int) get_comments_number($rpid); ?></span>
                                        <span class="meta-view"><i class="iconfont icon-chakan-line"></i><?php echo esc_html(theme_format_num(theme_get_site_views($rpid))); ?></span>
                                        <span class="meta-like d-none d-md-inline-block"><i class="iconfont icon-like-line"></i><?php echo $rfav; ?></span>
                                        <span class="meta-down d-none d-md-inline-block"><i class="iconfont icon-down"></i> <?php echo (int) theme_get_download_count($rpid); ?></span>
                                    </div>
                                </div>
                                <?php if ($ricons) : ?>
                                <div class="app-platform text-muted text-sm mb-n1">
                                    <?php foreach ($ricons as $pi) : ?>
                                    <i class="iconfont <?php echo esc_attr($pi['icon']); ?>" data-toggle="tooltip" title="<?php echo esc_attr($pi['title']); ?>"></i>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <?php endif; endif; ?>

                <?php comments_template(); ?>
            </div><!-- content-layout end -->
        </div><!-- content-wrap end -->
        <?php theme_render_single_sidebar('app'); ?>
    </div>
</main>
<?php endwhile; ?>

<?php get_footer(); ?>
