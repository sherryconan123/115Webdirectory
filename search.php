<?php get_header();

$search_type = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : 'sites';
if (!in_array($search_type, array('sites', 'post', 'app', 'book'), true)) {
    $search_type = 'sites';
}
$keyword = get_search_query();
$tabs = array(
    'sites' => '网址',
    'post'  => '文章',
    'app'   => '软件',
    'book'  => '书籍',
);
$placeholder = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiPjwvc3ZnPg==';
?>

<div class="ioui-content switch-container sidebar_right container">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="content-layout">

                <!-- 搜索类型切换 -->
                <div class="mb-4">
                    <?php foreach ($tabs as $type_key => $type_label) : ?>
                    <a class="btn btn-tab-h mr-2 text-gray<?php echo $search_type === $type_key ? ' active' : ''; ?>" href="<?php echo esc_url(home_url('/?s=' . urlencode($keyword) . '&post_type=' . $type_key)); ?>" title="有关&ldquo;<?php echo esc_attr($keyword); ?>&rdquo;的<?php echo esc_attr($type_label); ?>"><?php echo esc_html($type_label); ?></a>
                    <?php endforeach; ?>
                </div>

                <h4 class="text-gray text-lg mb-4"><i class="iconfont icon-search mr-1"></i>&ldquo;<?php echo esc_html($keyword); ?>&rdquo;的搜索结果</h4>

                <?php if (have_posts()) : ?>
                <div class="posts-row row-col-2a row-col-md-3a row-col-lg-4a">
                    <?php while (have_posts()) : the_post();
                        $pid = get_the_ID();
                        $thumb_url = has_post_thumbnail($pid) ? get_the_post_thumbnail_url($pid, 'full') : 'https://ui-avatars.com/api/?name=' . urlencode(mb_substr(get_the_title(), 0, 1)) . '&background=random&size=256';
                        $pterms = wp_get_post_terms($pid, 'favorites');
                        if (is_wp_error($pterms)) $pterms = array();
                        $ptags = ($search_type === 'app' && taxonomy_exists('apptag')) ? wp_get_post_terms($pid, 'apptag') : array();
                        if (is_wp_error($ptags)) $ptags = array();
                        $views = function_exists('theme_get_site_views') ? theme_get_site_views($pid) : 0;
                        $likes = function_exists('theme_get_site_likes') ? theme_get_site_likes($pid) : 0;
                        $down = ($search_type === 'app' && function_exists('theme_get_download_count')) ? (int) theme_get_download_count($pid) : 0;
                        $pver = ($search_type === 'app' && function_exists('theme_get_app_version')) ? theme_get_app_version($pid) : '';
                        $picons = ($search_type === 'app' && function_exists('theme_get_app_platform_icons')) ? theme_get_app_platform_icons($pid) : array();
                    ?>
                    <?php if ($search_type === 'app') : ?>
                    <article class="posts-item app-item d-flex style-app-max post-<?php echo (int) $pid; ?>">
                        <div class="item-header">
                            <div class="item-media" style="background-image:linear-gradient(130deg,#f9f9f9,#e8e8e8)">
                                <a class="item-image" href="<?php the_permalink(); ?>" style="transform:scale(83%)">
                                    <img class="fill-cover lazy unfancybox" src="<?php echo esc_url($placeholder); ?>" data-src="<?php echo esc_url($thumb_url); ?>" height="auto" width="auto" alt="<?php the_title_attribute(); ?>">
                                </a>
                            </div>
                        </div>
                        <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                            <h3 class="item-title line1">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?><span class="app-v text-xs"> - <?php echo $pver ? esc_html($pver) : '最新'; ?></span></a>
                            </h3>
                            <div class="app-content mt-auto">
                                <div class="text-muted text-xs line1"><?php echo esc_html(wp_trim_words(get_the_content(), 12)); ?></div>
                                <div class="app-meta d-flex align-items-center">
                                    <div class="item-tags overflow-x-auto no-scrollbar">
                                        <?php foreach (array_slice($pterms, 0, 1) as $pt) : ?>
                                        <a href="<?php echo esc_url(get_term_link($pt)); ?>" class="badge vc-l-theme text-ss mr-1" rel="tag" title="查看更多文章"><i class="iconfont icon-folder mr-1"></i><?php echo esc_html($pt->name); ?></a>
                                        <?php endforeach; ?>
                                        <?php foreach (array_slice($ptags, 0, 3) as $ptag) : ?>
                                        <a href="<?php echo esc_url(get_term_link($ptag)); ?>" class="badge text-ss mr-1" rel="tag" title="查看更多文章"># <?php echo esc_html($ptag->name); ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="meta-ico text-muted text-xs">
                                        <span class="meta-comm d-none d-md-inline-block"><i class="iconfont icon-comment"></i><?php echo (int) get_comments_number($pid); ?></span>
                                        <span class="meta-view"><i class="iconfont icon-chakan-line"></i><?php echo esc_html(theme_format_num($views)); ?></span>
                                        <span class="meta-like d-none d-md-inline-block"><i class="iconfont icon-like-line"></i><?php echo (int) $likes; ?></span>
                                        <span class="meta-down d-none d-md-inline-block"><i class="iconfont icon-down"></i> <?php echo (int) $down; ?></span>
                                    </div>
                                </div>
                                <?php if ($picons) : ?>
                                <div class="app-platform text-muted text-sm mb-n1">
                                    <?php foreach ($picons as $pi) : ?>
                                    <i class="iconfont <?php echo esc_attr($pi['icon']); ?>" data-toggle="tooltip" title="<?php echo esc_attr($pi['title']); ?>"></i>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                    <?php elseif ($search_type === 'post') : ?>
                    <article class="posts-item post-item d-flex style-post-min post-<?php echo (int) $pid; ?>">
                        <div class="item-header">
                            <div class="item-media">
                                <a class="item-image" href="<?php the_permalink(); ?>">
                                    <img class="fill-cover lazy unfancybox" src="<?php echo esc_url($placeholder); ?>" data-src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>">
                                </a>
                            </div>
                        </div>
                        <div class="item-body d-flex flex-column flex-fill">
                            <h3 class="item-title line2"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
                            <div class="mt-auto">
                                <div class="line1 text-muted text-sm d-none d-md-block"><?php echo esc_html(wp_trim_words(get_the_content(), 24)); ?></div>
                                <div class="item-meta d-flex align-items-center flex-fill text-muted text-xs">
                                    <span class="meta-time"><?php echo esc_html(get_the_date()); ?></span>
                                    <span class="ml-auto meta-view"><i class="iconfont icon-chakan-line"></i><?php echo esc_html(theme_format_num($views)); ?></span>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php elseif ($search_type === 'book') : ?>
                    <article class="posts-item book-item d-flex post-<?php echo (int) $pid; ?> col-2a col-md-3a">
                        <div class="item-header">
                            <div class="item-media" style="background-image:linear-gradient(130deg,#f5f5f5,#e0e0e0)">
                                <a class="item-image" href="<?php the_permalink(); ?>">
                                    <img class="fill-cover lazy unfancybox" src="<?php echo esc_url($placeholder); ?>" data-src="<?php echo esc_url($thumb_url); ?>" height="auto" width="auto" alt="<?php the_title_attribute(); ?>">
                                </a>
                            </div>
                        </div>
                        <div class="item-body overflow-hidden d-flex flex-column flex-fill text-center">
                            <h3 class="item-title line1"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="text-muted text-xs line1 mt-auto"><?php echo esc_html(wp_trim_words(get_the_content(), 12)); ?></div>
                            <div class="meta-ico text-muted text-xs mt-1">
                                <span class="meta-view"><i class="iconfont icon-chakan-line"></i><?php echo esc_html(theme_format_num($views)); ?></span>
                            </div>
                        </div>
                    </article>
                    <?php else : ?>
                    <article class="posts-item sites-item d-flex style-sites-max post-<?php echo (int) $pid; ?>">
                        <a href="<?php the_permalink(); ?>" class="sites-body" title="<?php the_title_attribute(); ?>">
                            <div class="item-header">
                                <div class="item-media">
                                    <div class="blur-img-bg lazy-bg"></div>
                                    <div class="item-image">
                                        <img class="fill-cover sites-icon lazy unfancybox" src="<?php echo esc_url($placeholder); ?>" data-src="<?php echo esc_url($thumb_url); ?>" height="auto" width="auto" alt="<?php the_title_attribute(); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                                <h3 class="item-title line1"><b><?php the_title(); ?></b></h3>
                                <div class="line1 text-muted text-xs"><?php echo esc_html(wp_trim_words(get_the_content(), 15)); ?></div>
                            </div>
                        </a>
                        <div class="meta-ico text-muted text-xs">
                            <span class="meta-comm d-none d-md-inline-block"><i class="iconfont icon-comment"></i><?php echo (int) get_comments_number($pid); ?></span>
                            <span class="meta-view"><i class="iconfont icon-chakan-line"></i><?php echo esc_html(theme_format_num($views)); ?></span>
                            <span class="meta-like d-none d-md-inline-block"><i class="iconfont icon-like-line"></i><?php echo (int) $likes; ?></span>
                        </div>
                        <div class="sites-tags">
                            <div class="item-tags overflow-x-auto no-scrollbar">
                                <?php foreach (array_slice($pterms, 0, 2) as $pt) : ?>
                                <a href="<?php echo esc_url(get_term_link($pt)); ?>" class="badge vc-l-theme text-ss mr-1" rel="tag" title="查看更多文章"><i class="iconfont icon-folder mr-1"></i><?php echo esc_html($pt->name); ?></a>
                                <?php endforeach; ?>
                            </div>
                            <?php $site_link = function_exists('theme_get_site_url') ? theme_get_site_url($pid) : ''; if ($site_link) : ?>
                            <a href="<?php echo esc_url($site_link); ?>" target="_blank" rel="external nofollow noopener" class="togo ml-auto text-center text-muted is-views" data-toggle="tooltip" data-placement="right" title="直达">
                                <i class="iconfont icon-goto"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endif; ?>
                    <?php endwhile; ?>
                </div>
                <div class="posts-nav mb-4">
                    <?php if (function_exists('theme_pagination')) theme_pagination(); ?>
                </div>
                <?php else : ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="nothing-box nothing-type-none">
                            <div class="nothing">
                                <svg class="nothing-svg" viewBox="0 0 220 170" xmlns="http://www.w3.org/2000/svg" width="200">
                                    <ellipse cx="110" cy="148" rx="82" ry="10" fill="#eef1f6"/>
                                    <path d="M44 78h104l22 18v34a10 10 0 0 1-10 10H54a10 10 0 0 1-10-10V88z" fill="#ffd9a8"/>
                                    <path d="M148 78l22 18h-16a6 6 0 0 1-6-6V78z" fill="#f5b96e"/>
                                    <path d="M58 104h76M58 120h52" stroke="#e09b4a" stroke-width="5" stroke-linecap="round"/>
                                    <circle cx="166" cy="64" r="27" fill="#fff" stroke="#d9deea" stroke-width="3"/>
                                    <circle cx="157" cy="59" r="3.4" fill="#9aa6bf"/><circle cx="175" cy="59" r="3.4" fill="#9aa6bf"/>
                                    <path d="M157 69q9 8 18 0" stroke="#9aa6bf" stroke-width="3" fill="none" stroke-linecap="round"/>
                                    <text x="96" y="122" font-size="36" font-weight="bold" fill="#e8893a">?</text>
                                </svg>
                                <div class="nothing-msg text-sm text-muted">没有找到与&ldquo;<?php echo esc_html($keyword); ?>&rdquo;相关的内容</div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- 右侧栏：关于本站 + 热门文章 + 标签云 -->
        <div class="sidebar sidebar-tools d-none d-lg-block">
            <div class="theiaStickySidebar">
                <div class="card io-sidebar-widget io-widget-about-website mb-3">
                    <div class="about-website-body">
                        <div class="about-cover bg-image media-bg p-2 fx-bg">
                            <div class="d-flex align-items-center">
                                <div class="avatar-md">
                                    <img class="avatar" src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_bloginfo('name')); ?>&background=8618db&color=fff&size=64" height="auto" width="auto" alt="<?php bloginfo('name'); ?>">
                                </div>
                                <div class="flex-fill overflow-hidden ml-2">
                                    <div class="text-md"><?php bloginfo('name'); ?></div>
                                    <div class="text-xs line1 mt-1"><?php bloginfo('description'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="about-meta mt-2">
                            <div class="posts-row">
                                <div class="col-1a tips-box vc-l-theme btn-outline bg-no-a">
                                    <div class="text-xl"><?php echo function_exists('theme_get_total_sites') ? theme_get_total_sites() : 0; ?></div>
                                    <div class="text-ss">收录网址</div>
                                </div>
                                <div class="col-3a tips-box vc-l-blue btn-outline bg-no-a">
                                    <div class="text-xl"><?php echo function_exists('theme_get_total_posts') ? theme_get_total_posts() : 0; ?></div>
                                    <div class="text-ss">收录文章</div>
                                </div>
                                <div class="col-3a tips-box vc-l-green btn-outline bg-no-a">
                                    <div class="text-xl"><?php echo function_exists('theme_get_total_apps') ? theme_get_total_apps() : 0; ?></div>
                                    <div class="text-ss">收录软件</div>
                                </div>
                                <div class="col-3a tips-box vc-l-red btn-outline bg-no-a">
                                    <div class="text-xl"><?php echo function_exists('theme_get_total_books') ? theme_get_total_books() : 0; ?></div>
                                    <div class="text-ss">收录书籍</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (function_exists('theme_render_hotposts_widget')) theme_render_hotposts_widget(); ?>
                <?php if (function_exists('theme_render_tag_cloud_widget')) theme_render_tag_cloud_widget($search_type); ?>
            </div>
        </div>

    </div>
</div>

<?php get_footer(); ?>
