<?php get_header(); ?>

<?php
$options = get_option('theme_settings');
$site_url = theme_get_site_url(get_the_ID());
$views = theme_get_site_views(get_the_ID());
$likes = theme_get_site_likes(get_the_ID());
$favorites = get_post_meta(get_the_ID(), '_site_favorites', true) ?: 0;
$site_domain = '';
if ($site_url) {
    $parsed = wp_parse_url($site_url);
    $site_domain = isset($parsed['host']) ? $parsed['host'] : '';
}

// 截图服务
$shot_service = $options['theme_screenshot_url'] ?? 'https://s0.wordpress.com/mshots/v1/{domain}?w=456&h=300';
$shot_url = $site_domain ? str_replace('{domain}', urlencode($site_domain), $shot_service) : '';

// Favicon（本地化：远程获取后保存到 uploads/websiteico/）
$favicon_url = function_exists('theme_get_local_favicon') ? theme_get_local_favicon(get_the_ID()) : '';
if (!$favicon_url && $site_domain) {
    $favicon_service = $options['theme_favicon_url'] ?? 'https://www.google.com/s2/favicons?domain={domain}&sz=32';
    $favicon_url = str_replace('{domain}', urlencode($site_domain), $favicon_service);
}

// QR服务
$qr_service = $options['theme_qr_url'] ?? 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={url}';
$qr_url = $site_url ? str_replace('{url}', urlencode($site_url), $qr_service) : '';

// 跳转链接
$go_url = $site_url ? home_url('/go/?url=' . base64_encode($site_url)) : $site_url;

$terms = wp_get_post_terms(get_the_ID(), 'favorites');
$tags = wp_get_post_terms(get_the_ID(), 'sitetag');
if (is_wp_error($tags)) $tags = wp_get_post_tags(get_the_ID());
?>

<div class="ioui-content switch-container sidebar_right container">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="content-layout">
                <?php theme_breadcrumb(); ?>

                <div class="d-flex flex-column flex-md-row site-content mb-4 mb-md-5">
                    <!-- 网址信息 -->
                    <div class="site-body flex-fill text-sm">
                        <div class="d-flex flex-wrap mb-4">
                            <div class="site-name-box flex-fill mb-3">
                                <h1 class="site-name h3 mb-3"><?php the_title(); ?></h1>
                                <div class="d-flex flex-fill text-muted text-sm">
                                    <span class="mr-3"><i class="iconfont icon-time-o"></i><span title="<?php the_time('Y年n月j日 H:i'); ?>发布"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?>前更新</span></span>
                                    <span class="views mr-3"><i class="iconfont icon-chakan-line"></i> <?php echo esc_html($views); ?></span>
                                    <span class="mr-3"><a class="smooth" href="#comments"><i class="iconfont icon-comment"></i> <?php echo get_comments_number(); ?></a></span>
                                    <?php if ($options['theme_sites_like'] ?? 1) : ?>
                                    <a href="javascript:;" data-type="like" data-post_type="sites" data-post_id="<?php the_ID(); ?>" class="io-posts-like mr-3" data-toggle="tooltip" title="点赞">
                                        <i class="iconfont icon-like-line mr-1"></i><small class="star-count text-xs"><?php echo esc_html($likes); ?></small>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($options['theme_sites_favorite'] ?? 1) : ?>
                            <div class="posts-like">
                                <a href="javascript:;" data-type="favorite" data-post_type="sites" data-post_id="<?php the_ID(); ?>" class="io-posts-like btn vc-l-red text-md py-1" data-toggle="tooltip" title="收藏">
                                    <i class="iconfont icon-collection-line mr-1" data-class="icon-collection icon-collection-line"></i>收藏
                                    <small class="star-count text-xs"><?php echo esc_html($favorites); ?></small>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-2">
                            <p class="mb-2"><?php echo wp_trim_words(get_the_content(), 60); ?></p>
                            <div class="table-div">
                                <div class="table-row">
                                    <div class="table-title">收录时间：</div>
                                    <div class="table-value"><?php the_time('Y-m-d'); ?></div>
                                </div>
                            </div>

                            <?php if ($options['theme_sites_seo'] ?? 1 && $site_domain) : ?>
                            <div class="mt-2 sites-seo-load" data-url="<?php echo esc_attr($site_domain); ?>">
                                <span class="sites-weight loading"></span>
                                <span class="sites-weight loading"></span>
                                <span class="sites-weight loading"></span>
                                <span class="sites-weight loading"></span>
                                <span class="sites-weight loading"></span>
                            </div>
                            <?php endif; ?>

                            <div class="site-go mt-3">
                                <?php if ($site_url) : ?>
                                <a href="<?php echo esc_url($go_url); ?>" title="<?php the_title_attribute(); ?>" target="_blank" class="btn vc-theme btn-shadow px-4 btn-i-r mr-2"><span>打开网站<i class="iconfont icon-arrow-r-m"></i></span></a>
                                <?php if ($options['theme_sites_qr'] ?? 1) : ?>
                                <a href="javascript:" class="btn vc-l-theme btn-outline qr-img btn-i-r mr-2" data-toggle="tooltip" data-placement="bottom" data-html="true" title="<img src='<?php echo esc_url($qr_url); ?>' width='150'>"><span>手机查看<i class="iconfont icon-qr-sweep"></i></span></a>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($options['theme_sites_report'] ?? 1) : ?>
                                <a href="javascript:" class="btn vc-red tooltip-toggle mr-2" data-post_id="<?php the_ID(); ?>" data-toggle="modal" data-placement="top" data-target="#report-sites-modal" title="反馈"><i class="iconfont icon-statement icon-lg"></i></a>
                                <?php endif; ?>
                            </div>

                            <div class="terms-list mt-3">
                                <?php if ($terms && !is_wp_error($terms)) : foreach ($terms as $term) : ?>
                                <a href="<?php echo esc_url(get_term_link($term)); ?>" class="vc-l-yellow btn btn-sm text-height-xs m-1 rounded-pill text-sm" rel="tag" title="查看更多"><i class="iconfont icon-folder mr-1"></i><?php echo esc_html($term->name); ?></a>
                                <?php endforeach; endif; ?>
                                <?php if ($tags && !is_wp_error($tags)) : foreach ($tags as $tag) : ?>
                                <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="vc-l-cyan btn btn-sm text-height-xs m-1 rounded-pill text-sm" rel="tag" title="查看更多"># <?php echo esc_html($tag->name); ?></a>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- 网址信息 end -->

                    <!-- 网站预览 -->
                    <?php if ($shot_url) : ?>
                    <div class="sites-preview ml-0 ml-md-2 mt-3 mt-md-0">
                        <div class="preview-body">
                            <?php if ($favicon_url) : ?>
                            <div class="site-favicon">
                                <img src="<?php echo esc_url($favicon_url); ?>" alt="<?php the_title_attribute(); ?>" width="16" height="16">
                                <span class="text-muted text-xs"><?php echo esc_html($site_domain); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="site-img img-sites">
                                <img class="lazy unfancybox" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0NTYiIGhlaWdodD0iMzAwIj48cmVjdCB3aWR0aD0iNDU2IiBoZWlnaHQ9IjMwMCIgZmlsbD0iI2YwZjBmMCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjE0IiBmaWxsPSIjY2NjIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+5Yqg6L+R5a+85b+r6L+U5ZuePC90ZXh0Pjwvc3ZnPg==" data-src="<?php echo esc_url($shot_url); ?>" height="300" width="456" alt="<?php the_title_attribute(); ?>">
                                <?php if ($site_url) : ?>
                                <a href="<?php echo esc_url($go_url); ?>" title="<?php the_title_attribute(); ?>" target="_blank" class="btn preview-btn rounded-pill vc-theme btn-shadow px-4 btn-sm"><span>打开网站</span></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- 文章内容 -->
                            <div class="panel site-content card">
                                <div class="card-body">
                                    <div class="panel-body single">
                                        <?php the_content(); ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($options['theme_sites_chart'] ?? 1) : ?>
                            <h2 class="text-gray text-lg my-4"><i class="iconfont icon-zouxiang mr-1"></i>数据统计</h2>
                            <div class="card io-chart">
                                <div id="chart-container" style="height:300px" data-type="sites" data-post_id="<?php the_ID(); ?>">
                                    <div class="chart-placeholder p-4">
                                        <div class="legend"><span></span><span></span><span></span></div>
                                        <div class="pillar">
                                            <?php for ($i = 0; $i < 12; $i++) : ?>
                                            <span style="height:<?php echo rand(30, 90); ?>%"></span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <h2 class="text-gray text-lg my-4"><i class="iconfont icon-tubiaopeizhi mr-1"></i>数据评估</h2>
                            <div class="panel site-content sites-default-content card">
                                <div class="card-body">
                                    <p class="viewport">
                                        <?php the_title(); ?>浏览人数已经达到<?php echo esc_html($views); ?>，如你需要查询该站的相关权重信息，可以点击"
                                        <a class="external" href="https://seo.5118.com/?t=ydm" rel="nofollow" target="_blank">5118数据</a>"
                                        "<a class="external" href="https://www.aizhan.com/seo/" rel="nofollow" target="_blank">爱站数据</a>"
                                        "<a class="external" href="https://seo.chinaz.com/" rel="nofollow" target="_blank">Chinaz数据</a>"进入；以目前的网站数据参考，建议大家请以爱站数据为准，更多网站价值评估因素如：<?php the_title(); ?>的访问速度、搜索引擎收录以及索引量、用户体验等；当然要评估一个站的价值，最主要还是需要根据您自身的需求以及需要，一些确切的数据则需要找<?php the_title(); ?>的站长进行洽谈提供。如该站的IP、PV、跳出率等！
                                    </p>
                                    <div class="text-center my-2"><span class="content-title"><span class="d-none">关于<?php the_title(); ?></span>特别声明</span></div>
                                    <p class="text-muted text-sm m-0">
                                        本站<?php bloginfo('name'); ?>提供的<?php the_title(); ?>都来源于网络，不保证外部链接的准确性和完整性，同时，对于该外部链接的指向，不由<?php bloginfo('name'); ?>实际控制，在<?php the_time('Y年n月j日 H:i'); ?>收录时，该网页上的内容，都属于合规合法，后期网页的内容如出现违规，可以直接联系网站管理员进行删除，<?php bloginfo('name'); ?>不承担任何责任。
                                    </p>
                                </div>
                                <div class="card-footer text-muted text-xs">
                                    <div class="d-flex">
                                        <span><?php bloginfo('name'); ?>致力于优质、实用的网络站点资源收集与分享！</span>
                                        <span class="ml-auto d-none d-md-block">本文地址<?php the_permalink(); ?>转载请注明</span>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $related_sites = theme_get_related_sites(get_the_ID(), 8);
                            if ($related_sites->have_posts()) :
                            ?>
                            <h4 class="text-gray text-lg my-4"><i class="site-tag iconfont icon-tag icon-lg mr-1"></i>相关导航</h4>
                            <div class="posts-row row-col-2a row-col-sm-2a row-col-md-4a row-col-lg-4a row-col-xl-4a">
                                <?php while ($related_sites->have_posts()) : $related_sites->the_post();
                                    $r_site_url = theme_get_site_url(get_the_ID());
                                    $r_favicon = function_exists('theme_get_local_favicon') ? theme_get_local_favicon(get_the_ID()) : '';
                                    $r_go = $r_site_url ? home_url('/go/?url=' . base64_encode($r_site_url)) : '';
                                ?>
                                <article class="posts-item sites-item d-flex style-sites-default post-<?php the_ID(); ?>">
                                    <a href="<?php the_permalink(); ?>" data-id="<?php the_ID(); ?>" data-url="<?php echo esc_attr($r_site_url); ?>" class="sites-body" title="<?php the_title(); ?>">
                                        <div class="item-header">
                                            <div class="item-media">
                                                <div class="blur-img-bg lazy-bg" <?php if ($r_favicon) echo 'data-bg="' . esc_url($r_favicon) . '"'; ?>></div>
                                                <div class="item-image">
                                                    <?php if (has_post_thumbnail()) : ?>
                                                    <img class="fill-cover sites-icon lazy unfancybox" src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title(); ?>">
                                                    <?php elseif ($r_favicon) : ?>
                                                    <img class="fill-cover sites-icon lazy unfancybox" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0OCIgaGVpZ2h0PSI0OCI+PHJlY3Qgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiBmaWxsPSIjZjBmMGYwIi8+PC9zdmc+" data-src="<?php echo esc_url($r_favicon); ?>" alt="<?php the_title(); ?>">
                                                    <?php else : ?>
                                                    <img class="fill-cover sites-icon lazy unfancybox" src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_the_title()); ?>&background=random" alt="<?php the_title(); ?>">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                                            <h3 class="item-title line1"><b><?php the_title(); ?></b></h3>
                                            <div class="line1 text-muted text-xs"><?php echo wp_trim_words(get_the_content(), 10); ?></div>
                                        </div>
                                    </a>
                                    <?php if ($r_site_url) : ?>
                                    <div class="sites-tags">
                                        <a href="<?php echo esc_url($r_go); ?>" target="_blank" rel="external nofollow noopener" class="togo ml-auto text-center text-muted is-views" data-id="<?php the_ID(); ?>" data-toggle="tooltip" data-placement="right" title="直达"><i class="iconfont icon-goto"></i></a>
                                    </div>
                                    <?php endif; ?>
                                </article>
                                <?php endwhile; ?>
                                <?php wp_reset_postdata(); ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($options['theme_sites_report'] ?? 1) : ?>
                            <!-- 反馈弹窗 -->
                            <div class="modal fade" id="report-sites-modal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">网站反馈</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted text-sm mb-3">如果发现该网站存在问题（无法访问、内容违规等），请提交反馈：</p>
                                            <div class="form-group">
                                                <label>反馈类型</label>
                                                <select class="form-control" id="report-type">
                                                    <option value="dead">网站无法访问</option>
                                                    <option value="illegal">内容违规</option>
                                                    <option value="wrong">信息错误</option>
                                                    <option value="other">其他问题</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>问题描述</label>
                                                <textarea class="form-control" id="report-content" rows="4" placeholder="请详细描述问题..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                                            <button type="button" class="btn vc-theme" id="submit-report" data-post_id="<?php the_ID(); ?>">提交反馈</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="comment-section mt-4">
                                <?php comments_template(); ?>
                            </div>
            </div>
        </div>
        <?php theme_render_single_sidebar('sites'); ?>
    </div>
</div>

<style>
.site-content{gap:0}
.site-body .site-name{font-weight:700}
.table-div .table-row{display:flex;padding:4px 0}
.table-div .table-title{width:80px;color:var(--muted-color)}
.table-div .table-value{flex:1}
.sites-weight{display:inline-block;width:24px;height:24px;margin-right:8px;border-radius:4px;background:rgba(116,116,116,.15);vertical-align:middle}
.sites-weight.loading{animation:pulse 1.5s ease-in-out infinite}
.sites-weight.loaded{font-size:14px;font-weight:700;text-align:center;line-height:24px}
@keyframes pulse{0%,100%{opacity:.4}50%{opacity:.8}}
.site-go .btn{display:inline-flex;align-items:center;gap:4px}
.sites-preview{flex:0 0 auto;max-width:456px}
.sites-preview .preview-body{border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)}
.site-favicon{display:flex;align-items:center;gap:6px;padding:8px 12px;background:rgba(116,116,116,.06)}
.site-img{position:relative;border-radius:0 0 12px 12px;overflow:hidden}
.site-img img{width:100%;display:block}
.site-img .preview-btn{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);opacity:0;transition:.3s;white-space:nowrap}
.site-img:hover .preview-btn{opacity:1}
.io-chart .chart-placeholder{text-align:center}
.io-chart .pillar{display:flex;align-items:flex-end;justify-content:center;gap:6px;height:200px}
.io-chart .pillar span{display:inline-block;width:5%;background:var(--theme-color);border-radius:3px 3px 0 0;min-height:4px}
.io-chart .legend{display:flex;justify-content:center;gap:16px;margin-bottom:8px}
.io-chart .legend span{display:inline-block;width:12px;height:12px;border-radius:2px;background:rgba(116,116,116,.2)}
.content-title{display:inline-block;padding:4px 16px;font-weight:600;border-bottom:2px solid var(--theme-color)}
.viewport{line-height:1.8}
</style>

<?php if ($options['theme_sites_like'] ?? 1 || $options['theme_sites_favorite'] ?? 1) : ?>
<script>
jQuery(function($) {
    // 点赞/收藏由 main.min.js 的 .io-posts-like（posts_like 接口）统一处理，此处不再重复绑定

    // SEO权重加载
    function loadSeoWeight() {
        var $box = $('.sites-seo-load');
        if (!$box.length) return;
        var domain = $box.data('url');
        $.post(theme_data.ajaxurl, {
            action: 'theme_site_seo',
            domain: domain
        }, function(res) {
            if (res && res.success) {
                var d = res.data;
                var labels = ['百度权重', 'PR值', '出站', '入站', '收录'];
                var values = [d.baidu || 0, d.pr || 0, d.out_links || 0, d.in_links || 0, d.index || 0];
                $box.find('.sites-weight').each(function(i) {
                    if (i < values.length) {
                        $(this).removeClass('loading').addClass('loaded').text(values[i]);
                        $(this).attr('title', labels[i] + ': ' + values[i]);
                    }
                });
            } else {
                $box.find('.sites-weight').removeClass('loading').addClass('loaded').text('-');
            }
        }, 'json').fail(function() {
            $box.find('.sites-weight').removeClass('loading').addClass('loaded').text('-');
        });
    }
    setTimeout(loadSeoWeight, 300);

    // 反馈提交
    $('#submit-report').on('click', function() {
        var $btn = $(this);
        $.post(theme_data.ajaxurl, {
            action: 'theme_site_report',
            post_id: $btn.data('post_id'),
            type: $('#report-type').val(),
            content: $('#report-content').val()
        }, function(res) {
            if (res && res.success) {
                alert('反馈已提交，感谢您的支持！');
                $('#report-sites-modal').modal('hide');
                $('#report-content').val('');
            } else {
                alert('提交失败，请稍后重试');
            }
        }, 'json');
    });
});
</script>
<?php endif; ?>

<?php get_footer(); ?>
