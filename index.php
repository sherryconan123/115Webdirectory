<?php get_header(); ?>
<aside class="ioui-aside switch-container container">
    <div class="aside-body" id="layout_aside">
        <div class="aside-card blur-bg shadow h-100">
            <ul class="aside-ul overflow-y-auto no-scrollbar">
                <?php
                $collections = theme_get_top_collections(true);
                foreach ($collections as $collection) :
                    $icon_class = theme_get_category_icon($collection->slug);
                ?>
                <li class="aside-item">
                    <a href="#term-<?php echo $collection->term_id; ?>" class="aside-btn hide-target smooth">
                        <i class="<?php echo $icon_class; ?> icon-fw"></i>
                        <span class="ml-2"><?php echo $collection->name; ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="aside-bottom mt-2 pt-2">
                <a href="javascript:;" class="aside-btn btn-outdent d-none d-md-block">
                    <i class="iconfont icon-shrink icon-fw" switch-class="iconfont icon-expand icon-fw"></i>
                    <span class="ml-2" switch-text="展开">收起</span>
                </a>
                <a href="javascript:;" class="aside-btn d-block d-md-none" data-toggle-div="" data-target="#layout_aside" data-class="is-mobile">
                    <i class="iconfont icon-shrink icon-fw"></i>
                    <span class="ml-2">收起</span>
                </a>
            </div>
        </div>
    </div>
</aside>
<div class="header-banner mb-4 module-id-0 header-calculate header-big css-color bg-gradual" style="background-image: linear-gradient(45deg, #8618db 0%, #d711ff 50%, #460fdd 100%);">
    <div class="switch-container search-container content container">
        <div id="search" class="big-search mx-auto" style="--big-search-height:320px;--big-mobile-height:220px">
            <div class="search-box-big">
                <div class="search-list-menu no-scrollbar overflow-x-auto slider-ul into">
                    <div class="search-menu slider-li active" data-default="type-big-zhannei" data-target="#group-onsite" data-id="group-onsite">站内</div>
                    <div class="search-menu slider-li" data-default="type-baidu" data-target="#group-a" data-id="group-a">常用</div>
                    <div class="search-menu slider-li" data-default="type-baidu1" data-target="#group-b" data-id="group-b">搜索</div>
                    <div class="search-menu slider-li" data-default="type-br" data-target="#group-c" data-id="group-c">工具</div>
                    <div class="search-menu slider-li" data-default="type-zhihu" data-target="#group-d" data-id="group-d">社区</div>
                    <div class="search-menu slider-li" data-default="type-taobao1" data-target="#group-e" data-id="group-e">生活</div>
                    <div class="search-menu slider-li" data-default="type-zhaopin" data-target="#group-f" data-id="group-f">求职</div>
                </div>
                <form action="<?php echo home_url(); ?>/?post_type=sites&s=" method="get" target="_blank" data-page="home" class="search-form">
                    <input type="text" id="search-text" class="form-control search-key" data-smart-tips="false" placeholder="输入关键字搜索" style="outline:0" autocomplete="off" data-status="true">
                    <div class="search-tools">
                        <span type="submit" class="btn vc-theme search-submit-btn"><i class="iconfont icon-search"></i></span>
                    </div>
                </form>
                <div class="search-list-group">
                    <ul id="group-onsite" class="search-group group-onsite no-scrollbar overflow-x-auto active">
                        <li class="search-term active type-big-zhannei" data-id="type-big-zhannei" data-value="<?php echo home_url(); ?>/?post_type=sites&s=" data-placeholder="你想了解些什么"></li>
                    </ul>
                    <ul id="group-a" class="search-group group-a no-scrollbar overflow-x-auto">
                        <li class="search-term type-baidu" data-id="type-baidu" data-value="https://www.baidu.com/s?wd=%s%" data-placeholder="百度一下">百度</li>
                        <li class="search-term type-google" data-id="type-google" data-value="https://www.google.com/search?q=%s%" data-placeholder="谷歌两下">Google</li>
                        <li class="search-term type-zhannei" data-id="type-zhannei" data-value="<?php echo home_url(); ?>/?post_type=sites&s=%s%" data-placeholder="站内搜索">站内</li>
                        <li class="search-term type-taobao" data-id="type-taobao" data-value="https://s.taobao.com/search?q=%s%" data-placeholder="淘宝">淘宝</li>
                        <li class="search-term type-bing" data-id="type-bing" data-value="https://cn.bing.com/search?q=%s%" data-placeholder="微软Bing搜索">Bing</li>
                    </ul>
                    <ul id="group-b" class="search-group group-b no-scrollbar overflow-x-auto">
                        <li class="search-term type-baidu1" data-id="type-baidu1" data-value="https://www.baidu.com/s?wd=%s%" data-placeholder="百度一下">百度</li>
                        <li class="search-term type-google1" data-id="type-google1" data-value="https://www.google.com/search?q=%s%" data-placeholder="谷歌两下">Google</li>
                        <li class="search-term type-360" data-id="type-360" data-value="https://www.so.com/s?q=%s%" data-placeholder="360好搜">360</li>
                        <li class="search-term type-sogo" data-id="type-sogo" data-value="https://www.sogou.com/web?query=%s%" data-placeholder="搜狗搜索">搜狗</li>
                        <li class="search-term type-bing1" data-id="type-bing1" data-value="https://cn.bing.com/search?q=%s%" data-placeholder="微软Bing搜索">Bing</li>
                    </ul>
                    <ul id="group-c" class="search-group group-c no-scrollbar overflow-x-auto">
                        <li class="search-term type-br" data-id="type-br" data-value="https://rank.chinaz.com/all/%s%" data-placeholder="请输入网址(不带https://)">权重查询</li>
                        <li class="search-term type-links" data-id="type-links" data-value="https://link.chinaz.com/%s%" data-placeholder="请输入网址(不带https://)">友链检测</li>
                        <li class="search-term type-ping" data-id="type-ping" data-value="https://ping.chinaz.com/%s%" data-placeholder="请输入网址(不带https://)">PING检测</li>
                        <li class="search-term type-404" data-id="type-404" data-value="https://tool.chinaz.com/Links/?DAddress=%s%" data-placeholder="请输入网址(不带https://)">死链检测</li>
                    </ul>
                    <ul id="group-d" class="search-group group-d no-scrollbar overflow-x-auto">
                        <li class="search-term type-zhihu" data-id="type-zhihu" data-value="https://www.zhihu.com/search?type=content&q=%s%" data-placeholder="知乎">知乎</li>
                        <li class="search-term type-wechat" data-id="type-wechat" data-value="https://weixin.sogou.com/weixin?type=2&query=%s%" data-placeholder="微信">微信</li>
                        <li class="search-term type-weibo" data-id="type-weibo" data-value="https://s.weibo.com/weibo/%s%" data-placeholder="微博">微博</li>
                        <li class="search-term type-douban" data-id="type-douban" data-value="https://www.douban.com/search?q=%s%" data-placeholder="豆瓣">豆瓣</li>
                    </ul>
                    <ul id="group-e" class="search-group group-e no-scrollbar overflow-x-auto">
                        <li class="search-term type-taobao1" data-id="type-taobao1" data-value="https://s.taobao.com/search?q=%s%" data-placeholder="淘宝">淘宝</li>
                        <li class="search-term type-jd" data-id="type-jd" data-value="https://search.jd.com/Search?keyword=%s%" data-placeholder="京东">京东</li>
                        <li class="search-term type-xiachufang" data-id="type-xiachufang" data-value="https://www.xiachufang.com/search/?keyword=%s%" data-placeholder="下厨房">下厨房</li>
                    </ul>
                    <ul id="group-f" class="search-group group-f no-scrollbar overflow-x-auto">
                        <li class="search-term type-zhaopin" data-id="type-zhaopin" data-value="https://sou.zhaopin.com/?jl=765&kw=%s%" data-placeholder="智联招聘">智联招聘</li>
                        <li class="search-term type-lagou" data-id="type-lagou" data-value="https://www.lagou.com/jobs/list_%s%?labelWords=&fromSearch=true&suginput=" data-placeholder="拉勾招聘">拉勾</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php theme_render_hot_ad_bar('my-3 my-md-5'); ?>
<section id="module_id_1" class="custom-background module-id-1" style="margin-top: 0px; margin-bottom: 0px; padding: 0px 0px 0px 0px">
    <div class="ioui-content switch-container home-container sidebar_no container">
        <div class="ioui-main">
            <div class="content-wrap">
                <div class="content-layout">
                    <?php theme_render_world_clock(); ?>
                    <div id="iow_big_posts_max-3" class="module-sidebar-widget io-widget-big-posts-list">
                        <div class="sidebar-header mb-3">
                            <div class="module-header widget-header">
                                <h3 class="text-md mb-0"><i class="mr-2 iconfont icon-hot"></i>热门网址</h3>
                            </div>
                            <div class="list-select-line"></div>
                            <div class="list-selects no-scrollbar">
                                <a class="list-select list-ajax-by" href="javascript:;" data-target="#iow_big_posts_max-3" data-type="date">发布</a>
                                <a class="list-select list-ajax-by" href="javascript:;" data-target="#iow_big_posts_max-3" data-type="modified">更新</a>
                                <a class="list-select list-ajax-by active" href="javascript:;" data-target="#iow_big_posts_max-3" data-type="views">浏览</a>
                                <a class="list-select list-ajax-by" href="javascript:;" data-target="#iow_big_posts_max-3" data-type="like">点赞</a>
                                <a class="list-select list-ajax-by" href="javascript:;" data-target="#iow_big_posts_max-3" data-type="favorite">收藏</a>
                                <a class="list-select list-ajax-by" href="javascript:;" data-target="#iow_big_posts_max-3" data-type="comment">评论</a>
                            </div>
                        </div>
                        <div class="posts-row row-col-2a row-col-sm-2a row-col-md-3a row-col-lg-4a row-col-xl-5a row-col-xxl-6a ajax-panel">
                            <?php
                            $hot_big_sites = theme_get_top_sites(12);
                            while ($hot_big_sites->have_posts()) : $hot_big_sites->the_post();
                                $site_url = theme_get_site_url(get_the_ID());
                                $views = theme_get_site_views(get_the_ID());
                                $likes = theme_get_site_likes(get_the_ID());
                                $fav_url = function_exists('theme_get_local_favicon') ? theme_get_local_favicon(get_the_ID()) : '';
                            ?>
                            <article class="posts-item sites-item d-flex style-sites-max">
                                <a href="<?php the_permalink(); ?>" data-id="<?php the_ID(); ?>" data-url="<?php echo esc_url($site_url); ?>" class="sites-body" title="<?php the_title(); ?>">
                                    <div class="item-header">
                                        <div class="item-media">
                                            <div class="blur-img-bg lazy-bg"<?php if ($fav_url) echo ' data-bg="' . esc_url($fav_url) . '"'; ?>></div>
                                            <div class="item-image">
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <img class="fill-cover sites-icon lazy unfancybox" src="<?php the_post_thumbnail_url('thumbnail'); ?>" height="auto" width="auto" alt="<?php the_title(); ?>">
                                                <?php elseif ($fav_url) : ?>
                                                    <img class="fill-cover sites-icon lazy unfancybox" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0OCIgaGVpZ2h0PSI0OCI+PHJlY3Qgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiBmaWxsPSIjZjBmMGYwIi8+PC9zdmc+" data-src="<?php echo esc_url($fav_url); ?>" height="auto" width="auto" alt="<?php the_title(); ?>">
                                                <?php else : ?>
                                                    <img class="fill-cover sites-icon lazy unfancybox" src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_the_title()); ?>&background=random&color=fff" height="auto" width="auto" alt="<?php the_title(); ?>">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                                        <h3 class="item-title line1"><b><?php the_title(); ?></b></h3>
                                        <div class="line1 text-muted text-xs"><?php echo wp_trim_words(get_the_content(), 15); ?></div>
                                    </div>
                                </a>
                                <div class="meta-ico text-muted text-xs">
                                    <span class="meta-view"><i class="iconfont icon-chakan-line"></i><?php echo $views; ?></span>
                                    <span class="meta-like d-none d-md-inline-block"><i class="iconfont icon-like-line"></i><?php echo $likes; ?></span>
                                </div>
                                <div class="sites-tags">
                                    <div class="item-tags overflow-x-auto no-scrollbar">
                                        <?php
                                        $terms = get_the_terms(get_the_ID(), 'favorites');
                                        if ($terms && !is_wp_error($terms)) :
                                            foreach ($terms as $term) :
                                        ?>
                                        <a href="<?php echo get_term_link($term); ?>" class="badge vc-l-theme text-ss mr-1" rel="tag" title="查看更多文章"><i class="iconfont icon-folder mr-1"></i><?php echo $term->name; ?></a>
                                        <?php endforeach; endif; ?>
                                    </div>
                                    <?php if (!empty($site_url)) : ?>
                                    <a href="<?php echo esc_url($site_url); ?>" target="_blank" rel="external nofollow noopener" class="togo ml-auto text-center text-muted is-views" data-id="<?php the_ID(); ?>" data-toggle="tooltip" data-placement="right" title="直达">
                                        <i class="iconfont icon-goto"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </article>
                            <?php endwhile; ?>
                            <?php wp_reset_postdata(); ?>
                        </div>
                        <div class="text-center mt-3 mb-2">
                            <a href="javascript:;" class="btn vc-l-theme btn-outline btn-sm px-4 list-ajax-more" data-target="#iow_big_posts_max-3" data-type="views" data-page="2"><i class="iconfont icon-add mr-1"></i>加载更多</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="module_id_2" class="custom-background module-id-2" style="margin-top: 0px; margin-bottom: 0px; padding: 0px 0px 0px 0px">
    <div class="ioui-content switch-container home-container sidebar_right container">
        <div class="ioui-main">
            <div class="content-wrap">
                <div class="content-layout show-card">
                    <?php
                    $collections = theme_get_top_collections(true);
                    foreach ($collections as $collection) :
                        $icon_class = theme_get_category_icon($collection->slug);
                        $section_post_types = theme_collection_post_types($collection->slug);
                        $collection_sites = new WP_Query(array(
                            'post_type' => $section_post_types,
                            'posts_per_page' => 12,
                            'tax_query' => array(array(
                                'taxonomy' => 'favorites',
                                'field' => 'term_id',
                                'terms' => $collection->term_id,
                            )),
                        ));
                        if (!$collection_sites->have_posts()) {
                            wp_reset_postdata();
                            continue;
                        }
                        // 子分类标签（与目标站 io-slider-tab 一致），有子分类且子分类有内容时显示
                        $child_terms = get_terms(array(
                            'taxonomy' => 'favorites',
                            'parent' => $collection->term_id,
                            'hide_empty' => true,
                            'orderby' => 'term_id',
                        ));
                        if (is_wp_error($child_terms)) {
                            $child_terms = array();
                        }
                    ?>
                    <div class="content-card">
                        <div id="term-<?php echo $collection->term_id; ?>" class="d-flex flex-fill align-items-center mb-2">
                            <h4 class="tab-title text-gray text-md m-0">
                                <i class="site-tag <?php echo $icon_class; ?> icon-lg mr-1"></i><?php echo $collection->name; ?>
                            </h4>
                            <div class="flex-fill tab-to-more"></div>
                            <a class="btn-more text-xs ml-2" href="<?php echo get_term_link($collection); ?>">more+</a>
                        </div>
                        <?php if (!empty($child_terms)) : ?>
                        <div class="io-slider-tab no-scrollbar overflow-x-auto mb-2">
                            <div class="slider-li tab-item active" data-tab="all">全部</div>
                            <?php foreach ($child_terms as $child_term) : ?>
                            <div class="slider-li tab-item" data-tab="term-<?php echo (int) $child_term->term_id; ?>"><?php echo esc_html($child_term->name); ?></div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <div class="io-tab-panel" data-panel="all">
                            <div class="row row-col-1a row-col-sm-1a row-col-md-1a row-col-lg-1a row-col-xl-3a row-col-xxl-4a">
                                <?php
                                $item_idx = 0;
                                while ($collection_sites->have_posts()) : $collection_sites->the_post();
                                    theme_render_collection_item(get_post_type(get_the_ID()), $item_idx++);
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                        <?php foreach ($child_terms as $child_term) :
                            $child_sites = new WP_Query(array(
                                'post_type' => $section_post_types,
                                'posts_per_page' => 12,
                                'tax_query' => array(array(
                                    'taxonomy' => 'favorites',
                                    'field' => 'term_id',
                                    'terms' => $child_term->term_id,
                                )),
                            ));
                            if (!$child_sites->have_posts()) {
                                wp_reset_postdata();
                                continue;
                            }
                        ?>
                        <div class="io-tab-panel d-none" data-panel="term-<?php echo (int) $child_term->term_id; ?>">
                            <div class="row row-col-1a row-col-sm-1a row-col-md-1a row-col-lg-1a row-col-xl-3a row-col-xxl-4a">
                                <?php
                                $item_idx = 0;
                                while ($child_sites->have_posts()) : $child_sites->the_post();
                                    theme_render_collection_item(get_post_type(get_the_ID()), $item_idx++);
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="sidebar sidebar-tools d-none d-lg-block">
                <div class="theiaStickySidebar">
                    <?php theme_render_about_website_widget(); ?>
                    <?php theme_render_currency_widget(); ?>
                    <?php theme_render_hotlist_widget(); ?>
                    <?php theme_render_hotposts_widget(); ?>
                    <div class="card io-sidebar-widget io-widget-ranking-list ajax-parent">
                        <div class="sidebar-header">
                            <div class="card-header widget-header">
                                <h3 class="text-md mb-0">排行榜</h3>
                            </div>
                        </div>
                        <div class="range-nav text-md">
                            <a href="javascript:;" class="is-tab-btn active" data-range="today">日榜</a>
                            <a href="javascript:;" class="is-tab-btn" data-range="week">周榜</a>
                            <a href="javascript:;" class="is-tab-btn" data-range="month">月榜</a>
                        </div>
                        <div class="card-body">
                            <div class="posts-row row-sm ajax-panel row-col-1a">
                                <?php
                                $top_sites = theme_get_top_sites(6);
                                $rank = 1;
                                while ($top_sites->have_posts()) : $top_sites->the_post();
                                    $site_url = theme_get_site_url(get_the_ID());
                                ?>
                                <div class="posts-item sites-item d-flex style-sites-default muted-bg br-md no-go-ico">
                                    <span class="rank-num <?php echo ($rank <= 3) ? 'font-bold' : ''; ?>"><?php echo $rank; ?></span>
                                    <a href="<?php the_permalink(); ?>" target="_blank" data-id="<?php the_ID(); ?>" data-url="<?php echo esc_url($site_url); ?>" class="sites-body" title="<?php the_title(); ?>">
                                        <div class="item-header">
                                            <div class="item-media">
                                                <div class="blur-img-bg lazy-bg"></div>
                                                <div class="item-image">
                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <img class="fill-cover sites-icon lazy unfancybox" src="<?php the_post_thumbnail_url('thumbnail'); ?>" height="auto" width="auto" alt="<?php the_title(); ?>">
                                                    <?php else : ?>
                                                        <img class="fill-cover sites-icon lazy unfancybox" src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_the_title()); ?>&background=random&color=fff" height="auto" width="auto" alt="<?php the_title(); ?>">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                                            <h3 class="item-title line1"><b><?php the_title(); ?></b></h3>
                                            <div class="line1 text-muted text-xs"><?php echo wp_trim_words(get_the_content(), 8); ?></div>
                                        </div>
                                    </a>
                                </div>
                                <?php $rank++; endwhile; ?>
                                <?php wp_reset_postdata(); ?>
                            </div>
                        </div>
                        <a href="<?php echo home_url('/rankings'); ?>" class="btn vc-l-yellow d-block mx-3 mb-3 text-sm" target="_blank">查看完整榜单</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
/* 世界时钟 */
.io-world-clock{background:var(--wc-bg);border-radius:12px;padding:8px 0}
.io-world-clock .io-hscroll-list{flex-wrap:nowrap}
.io-world-clock .clock-item{flex:0 0 auto;padding:2px 18px;white-space:nowrap;border-right:1px solid rgba(116,116,116,.15);line-height:1.6}
.io-world-clock .clock-item:last-child{border-right:0}
.io-world-clock .clock-meta{font-size:12px;color:#66686b}
.io-world-clock .flags{margin-right:6px;white-space:nowrap}
.io-world-clock .flags .flag-img{width:16px;height:12px;margin-right:3px;vertical-align:-1px;border-radius:2px;object-fit:cover}
.io-world-clock .flags .flag-emoji{margin-right:3px}
.io-world-clock .names{margin-right:8px}
.io-world-clock .time{font-size:12px;color:#93959a;font-weight:400;font-variant-numeric:tabular-nums}
/* 汇率 */
.io-currency-list .io-currency-item{display:flex;align-items:center;gap:8px;padding:5px 8px;font-size:13px;border-radius:8px}
.io-currency-list .io-currency-item:nth-child(odd){background:rgba(116,116,116,.06)}
.io-currency-list .cr-name{color:var(--muted-color)}
.io-currency-list .cr-value{font-weight:600;font-variant-numeric:tabular-nums}
/* 热门榜单 */
.hotapi-list li a{display:flex;align-items:center;gap:8px;padding:4px 8px;border-radius:8px}
.hotapi-list li a:hover{background:rgba(116,116,116,.08)}
.hotapi-rank{flex:0 0 auto;width:18px;height:18px;font-size:12px;border-radius:4px;background:rgba(116,116,116,.15);color:var(--muted-color)}
.hotapi-rank.is-top{background:var(--theme-color);color:#fff}
.hotapi-list .t{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:left}
/* 热门文章 */
.theme-hotposts-list{list-style:none;margin:0;padding:0}
.theme-hotposts-list li{display:flex;align-items:center;gap:8px;padding:5px 2px;border-bottom:1px dashed rgba(116,116,116,.15)}
.theme-hotposts-list li:last-child{border-bottom:0}
.theme-hotposts-list .hp-title{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.theme-hotposts-list .hp-date{color:var(--muted-color);font-size:12px;flex:0 0 auto}
.theme-hotposts-refresh{position:absolute;top:12px;right:14px;color:var(--muted-color)}
.io-widget-single-posts-list .sidebar-header{position:relative}
/* 首页分类子标签 */
.io-slider-tab{display:flex;gap:8px;padding-bottom:4px}
.io-slider-tab .tab-item{flex:0 0 auto;padding:4px 14px;border-radius:14px;font-size:13px;color:var(--muted-color);background:rgba(116,116,116,.07);cursor:pointer;transition:all .2s;user-select:none}
.io-slider-tab .tab-item:hover{color:var(--theme-color)}
.io-slider-tab .tab-item.active{background:var(--theme-color);color:#fff}
</style>
<script>
jQuery(function($) {
    var ajaxUrl = (typeof theme_data !== 'undefined') ? theme_data.ajaxurl : '';

    // 世界时钟
    function themeClockTick() {
        $('.io-world-clock .clock-item').each(function() {
            var tz = $(this).data('timezone');
            var el = $(this).find('.time');
            try {
                var now = new Date();
                var parts = {};
                new Intl.DateTimeFormat('en-US', {timeZone: tz, month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false}).formatToParts(now).forEach(function(p) { parts[p.type] = p.value; });
                var hh = parts.hour === '24' ? '00' : parts.hour;
                el.text(parts.month + '-' + parts.day + ' ' + hh + ':' + parts.minute + ':' + parts.second);
            } catch (e) {
                el.text('--');
            }
        });
    }
    if ($('.io-world-clock').length) {
        themeClockTick();
        setInterval(themeClockTick, 1000);
    }

    // 热门榜单
    function renderHotList($panel, items) {
        var html = '';
        $.each(items, function(i, item) {
            var title = $('<i>').text(item.title).html();
            html += '<li><a href="' + item.url + '" target="_blank" rel="external nofollow">'
                + '<span class="hotapi-rank' + (i < 3 ? ' is-top' : '') + '">' + (i + 1) + '</span>'
                + '<span class="t">' + title + '</span>'
                + (item.hot ? '<span class="hot text-ss text-muted">' + $('<i>').text(item.hot).html() + '</span>' : '')
                + '</a></li>';
        });
        $panel.find('.hotapi-list').html(html);
        $panel.find('.hotapi-status').text('');
    }
    function loadHotPanel($panel, refresh) {
        $panel.find('.hotapi-list').html('<li class="text-muted text-sm">加载中...</li>');
        $.post(ajaxUrl, {action: 'theme_hot_list', source: $panel.data('index'), refresh: refresh ? 1 : 0}, function(res) {
            if (res && res.success) {
                renderHotList($panel, res.data.items);
            } else {
                $panel.find('.hotapi-list').html('');
                $panel.find('.hotapi-status').text((res && res.data && res.data.msg) ? res.data.msg : '加载失败');
            }
        }, 'json').fail(function() {
            $panel.find('.hotapi-list').html('');
            $panel.find('.hotapi-status').text('加载失败');
        });
    }
    if ($('.hotapi-card').length) {
        $('.hotapi-card').each(function() {
            var $card = $(this);
            if ($card.hasClass('active')) loadHotPanel($card, false);
            $card.find('.hotapi-refresh').on('click', function(e) {
                e.preventDefault();
                loadHotPanel($card, true);
            });
        });
        $(document).on('click', '.hotapi-tab-btn', function() {
            var $btn = $(this);
            $btn.addClass('active').siblings().removeClass('active');
            var $card = $($btn.data('target'));
            $card.addClass('active').siblings('.hotapi-card').removeClass('active');
            if (!$card.data('loaded')) {
                loadHotPanel($card, false);
                $card.data('loaded', true);
            }
        });
    }

    // 热门文章
    function loadHotPosts(refresh) {
        $.post(ajaxUrl, {action: 'theme_hot_posts', refresh: refresh ? 1 : 0}, function(res) {
            if (res && res.success) {
                var html = '';
                $.each(res.data.items, function(i, item) {
                    html += '<li><span class="hotapi-rank' + (i < 3 ? ' is-top' : '') + '">' + (i + 1) + '</span>'
                        + '<a class="hp-title" href="' + item.url + '" target="_blank">' + $('<i>').text(item.title).html() + '</a>'
                        + '<span class="hp-date">' + item.date + '</span></li>';
                });
                $('.theme-hotposts-list').html(html);
            } else {
                $('.theme-hotposts-list').html('<li class="text-muted">加载失败</li>');
            }
        }, 'json').fail(function() {
            $('.theme-hotposts-list').html('<li class="text-muted">加载失败</li>');
        });
    }
    if ($('.theme-hotposts-list').length) loadHotPosts(false);
    $(document).on('click', '.theme-hotposts-refresh', function(e) {
        e.preventDefault();
        loadHotPosts(true);
    });

    // 热门网址排序
    $(document).on('click', '.list-ajax-by', function(e) {
        e.preventDefault();
        var $btn = $(this);
        $btn.addClass('active').siblings().removeClass('active');
        var type = $btn.data('type');
        var $panel = $($btn.data('target')).find('.ajax-panel');
        $panel.html('<div class="w-100 text-center py-4 text-muted">加载中...</div>');
        $.post(ajaxUrl, {action: 'theme_big_posts', type: type, page: 1}, function(res) {
            if (res && res.success) {
                $panel.html(res.data.items);
                var $more = $($btn.data('target')).find('.list-ajax-more');
                if (res.data.has_more) {
                    $more.data('type', type).data('page', 2).show();
                } else {
                    $more.hide();
                }
            }
        }, 'json').fail(function() {
            $panel.html('<div class="w-100 text-center py-4 text-muted">加载失败</div>');
        });
    });

    // 首页分类子标签切换
    $(document).on('click', '.io-slider-tab .tab-item', function() {
        var $tab = $(this);
        if ($tab.hasClass('active')) return;
        var $card = $tab.closest('.content-card');
        $tab.addClass('active').siblings().removeClass('active');
        $card.find('.io-tab-panel').addClass('d-none')
            .filter('[data-panel="' + $tab.data('tab') + '"]').removeClass('d-none');
    });

    // 加载更多
    $(document).on('click', '.list-ajax-more', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var type = $btn.data('type') || 'views';
        var page = ($btn.data('page') || 1) + 1;
        var $panel = $($btn.data('target')).find('.ajax-panel');
        var orig = $btn.html();
        $btn.html('<i class="iconfont icon-loading"></i> 加载中...').prop('disabled', true);
        $.post(ajaxUrl, {action: 'theme_big_posts', type: type, page: page}, function(res) {
            $btn.html(orig).prop('disabled', false);
            if (res && res.success) {
                $panel.append(res.data.items);
                $btn.data('page', page);
                if (!res.data.has_more) $btn.hide();
            }
        }, 'json').fail(function() {
            $btn.html(orig).prop('disabled', false);
        });
    });
});
</script>
<?php get_footer(); ?>
