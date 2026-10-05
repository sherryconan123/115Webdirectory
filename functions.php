<?php
define('THEME_VERSION', '5.86');

function theme_enqueue_scripts() {
    wp_enqueue_style('bootstrap-css', get_template_directory_uri() . '/css/bootstrap.min.css', array(), THEME_VERSION);
    wp_enqueue_style('swiper-css', get_template_directory_uri() . '/css/swiper-bundle.min.css', array(), THEME_VERSION);
    wp_enqueue_style('lightbox-css', get_template_directory_uri() . '/css/jquery.fancybox.min.css', array(), THEME_VERSION);
    wp_enqueue_style('iconfont-css', get_template_directory_uri() . '/css/iconfont.css', array(), THEME_VERSION);
    wp_enqueue_style('theme-style', get_template_directory_uri() . '/css/main.min.css', array('bootstrap-css'), THEME_VERSION);
    
    wp_enqueue_script('jquery', get_template_directory_uri() . '/js/jquery.min.js', array(), THEME_VERSION, true);
    wp_enqueue_script('lazyload-js', get_template_directory_uri() . '/js/lazyload.min.js', array('jquery'), THEME_VERSION, true);
    wp_enqueue_script('theme-script', get_template_directory_uri() . '/js/main.min.js', array('jquery'), THEME_VERSION, true);
    wp_enqueue_script('theia-sticky-js', get_template_directory_uri() . '/js/theia-sticky-sidebar.js', array('jquery'), THEME_VERSION, true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    wp_localize_script('theme-script', 'theme_data', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'home_url' => home_url(),
        'theme_version' => THEME_VERSION,
        'nonce' => wp_create_nonce('theme_ajax_nonce'),
    ));

    // main.min.js（原主题压缩脚本）依赖全局 IO 对象，需同步输出
    wp_localize_script('theme-script', 'IO', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'homeurl' => home_url(),
        'uid' => get_current_user_id(),
        'version' => THEME_VERSION,
        'themeType' => 'auto-system',
        'apikey' => '',
        'isDarkMode' => false,
        'isAsideInitEvent' => false,
        'isFooterVisible' => false,
        'isHeaderVisible' => false,
        'isDominantColor' => false,
        'isShowAsideSub' => false,
        'homeWidth' => 1200,
        'asideWidth' => 300,
        'hotWords' => array(),
        'footprintData' => array(),
        'postData' => array('postId' => 0, 'postType' => ''),
        'localize' => array(
            'networkError' => '网络错误，请稍后重试',
            'successAlert' => '操作成功',
            'nightMode' => '夜间模式',
            'lightMode' => '日间模式',
            'extractionCode' => '提取码',
            'userAgreement' => '请先阅读并同意用户协议',
            'reSend' => '秒后重新发送',
            'clearFootprint' => '清除浏览足迹',
            'wait' => '请稍候...',
            'loading' => '加载中...',
            'cancelBtn' => '取消',
            'okBtn' => '确定',
            'infoAlert' => '提示',
            'parameterError' => '参数错误',
            'errorAlert' => '操作失败',
            'warningAlert' => '警告',
        ),
    ));
    // main.min.js 执行前的兼容层：$ 别名、jQuery 对象引用、ioRequire 垫片，
    // 以及轻量 Tooltip（含 HTML 二维码）与 Carousel 行为（主题只加载 bootstrap CSS，不加载 bootstrap JS）
    $shim_js = <<<'JS'
(function () {
    if (!window.jQuery) return;
    window.$ = window.$ || window.jQuery;
    /* main.min.js 的 AJAX 回调可能链式调用 tooltip/popover，保留空插件防止报错 */
    jQuery.fn.tooltip = jQuery.fn.tooltip || function () { return this; };
    jQuery.fn.popover = jQuery.fn.popover || function () { return this; };
    window.ioRequire = window.ioRequire || function (name, cb) {
        if (typeof cb === 'function') { try { cb(); } catch (e) { if (window.console) console.warn('ioRequire shim:', e); } }
    };
    if (window.IO) {
        IO.document = jQuery(document);
        IO.body = jQuery(document.body);
        IO.html = jQuery('html');
        IO.window = jQuery(window);
    }

    /* ============ 轻量 Tooltip（事件委托，支持动态插入内容/data-html/placement/点击触发） ============ */
    var tipEl = null, tipTimer = null, tipTrigger = null;
    function ensureTip() {
        if (!tipEl) {
            tipEl = document.createElement('div');
            tipEl.className = 'io-tooltip';
            tipEl.innerHTML = '<div class="io-tooltip-inner"></div><div class="io-tooltip-arrow"></div>';
            document.body.appendChild(tipEl);
        }
        return tipEl;
    }
    function tipTarget(e) {
        var t = e.target;
        return t && t.closest ? t.closest('[data-toggle="tooltip"]') : null;
    }
    function showTip(el) {
        var title = el.getAttribute('data-original-title');
        if (title === null) {
            title = el.getAttribute('title') || '';
            el.setAttribute('data-original-title', title);
            el.removeAttribute('title');
        }
        if (!title) return;
        tipTrigger = el;
        var box = ensureTip();
        var inner = box.querySelector('.io-tooltip-inner');
        if (el.getAttribute('data-html') === 'true') { inner.innerHTML = title; } else { inner.textContent = title; }
        var placement = el.getAttribute('data-placement') || 'top';
        box.className = 'io-tooltip show placement-' + placement;
        var r = el.getBoundingClientRect();
        var b = box.getBoundingClientRect();
        var x, y;
        var gap = 8;
        if (placement === 'bottom') { x = r.left + r.width / 2 - b.width / 2; y = r.bottom + gap; }
        else if (placement === 'left') { x = r.left - b.width - gap; y = r.top + r.height / 2 - b.height / 2; }
        else if (placement === 'right') { x = r.right + gap; y = r.top + r.height / 2 - b.height / 2; }
        else { x = r.left + r.width / 2 - b.width / 2; y = r.top - b.height - gap; }
        x = Math.max(8, Math.min(x, window.pageXOffset + document.documentElement.clientWidth - b.width - 8));
        box.style.left = (x + window.pageXOffset) + 'px';
        box.style.top = (y + window.pageYOffset) + 'px';
        clearTimeout(tipTimer);
    }
    function hideTip() {
        clearTimeout(tipTimer);
        tipTimer = setTimeout(function () {
            if (tipEl) tipEl.classList.remove('show');
            tipTrigger = null;
        }, 120);
    }
    document.addEventListener('mouseover', function (e) { var el = tipTarget(e); if (el) { clearTimeout(tipTimer); showTip(el); } });
    document.addEventListener('mouseout', function (e) { var el = tipTarget(e); if (el) hideTip(); });
    document.addEventListener('focusin', function (e) { var el = tipTarget(e); if (el) showTip(el); });
    document.addEventListener('focusout', function (e) { var el = tipTarget(e); if (el) hideTip(); });
    /* 触摸设备：点击切换；点击页面其它处关闭（不阻止链接/按钮自身行为） */
    document.addEventListener('click', function (e) {
        var el = tipTarget(e);
        if (el && !el.closest('a[href],button')) {
            if (tipTrigger === el && tipEl && tipEl.classList.contains('show')) { hideTip(); }
            else { clearTimeout(tipTimer); showTip(el); }
        } else if (tipEl && tipEl.classList.contains('show')) {
            hideTip();
        }
    });

    /* ============ 轻量 Carousel（data-ride="carousel"：自动轮播/指示器/前后按钮/触摸滑动） ============ */
    function initCarousel(root) {
        if (root.__ioCarousel) return;
        root.__ioCarousel = true;
        var items = root.querySelectorAll('.carousel-item');
        if (!items.length) return;
        var indicators = root.querySelectorAll('.carousel-indicators li');
        var idx = 0, timer = null, paused = false;
        function go(n) {
            idx = (n + items.length) % items.length;
            items.forEach(function (it, i) { it.classList.toggle('active', i === idx); });
            indicators.forEach(function (li, i) { li.classList.toggle('active', i === idx); });
        }
        function next() { go(idx + 1); }
        function prev() { go(idx - 1); }
        function play() { stop(); timer = setInterval(next, 5000); }
        function stop() { if (timer) { clearInterval(timer); timer = null; } }
        root.addEventListener('click', function (e) {
            var nextBtn = e.target.closest('.carousel-control-next');
            var prevBtn = e.target.closest('.carousel-control-prev');
            var li = e.target.closest('.carousel-indicators li');
            if (nextBtn) { e.preventDefault(); next(); }
            else if (prevBtn) { e.preventDefault(); prev(); }
            else if (li) { e.preventDefault(); go(parseInt(li.getAttribute('data-slide-to'), 10) || 0); }
        });
        root.addEventListener('mouseenter', function () { paused = true; stop(); });
        root.addEventListener('mouseleave', function () { paused = false; play(); });
        var sx = 0, sy = 0, swiping = false;
        root.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; sy = e.touches[0].clientY; swiping = true; stop(); }, { passive: true });
        root.addEventListener('touchend', function (e) {
            if (!swiping) return;
            swiping = false;
            var dx = e.changedTouches[0].clientX - sx;
            var dy = e.changedTouches[0].clientY - sy;
            if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) { dx < 0 ? next() : prev(); }
            play();
        }, { passive: true });
        play();
    }
    function initAllCarousels() {
        document.querySelectorAll('[data-ride="carousel"]').forEach(initCarousel);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllCarousels);
    } else {
        initAllCarousels();
    }
    /* AJAX 注入的轮播也自动初始化 */
    jQuery(document).on('ajaxComplete', function () {
        document.querySelectorAll('[data-ride="carousel"]').forEach(initCarousel);
    });

    /* ============ 轻量 Modal（main.min.js 内 ioModal 依赖 $.fn.modal，主题未加载 bootstrap JS） ============ */
    function modalShow(modal, options) {
        var $m = jQuery(modal);
        var opts = options || {};
        if (opts.backdrop === undefined) opts.backdrop = true;
        $m.data('bs.modal.opts', opts);
        if (opts.backdrop) {
            var bd = document.createElement('div');
            bd.className = 'modal-backdrop fade';
            document.body.appendChild(bd);
            $m.data('bs.modal.backdrop', bd);
            void bd.offsetWidth;
            bd.classList.add('show');
        }
        document.body.classList.add('modal-open');
        $m.css('display', 'block');
        void modal.offsetWidth;
        $m.addClass('show');
        $m.attr('aria-hidden', 'false');
        setTimeout(function () { var d = $m.find('.modal-dialog .btn:first').get(0); if (d) d.focus({ preventScroll: true }); }, 200);
    }
    function modalHide(modal) {
        var $m = jQuery(modal);
        $m.removeClass('show').attr('aria-hidden', 'true');
        setTimeout(function () { $m.css('display', ''); }, 150);
        var bd = $m.data('bs.modal.backdrop');
        if (bd) { bd.classList.remove('show'); setTimeout(function () { bd.parentNode && bd.parentNode.removeChild(bd); }, 150); $m.removeData('bs.modal.backdrop'); }
        if (!jQuery('.modal.show').length) document.body.classList.remove('modal-open');
    }
    jQuery.fn.modal = function (option) {
        return this.each(function () {
            var stored = jQuery(this).data('bs.modal.opts') || {};
            if (typeof option === 'object') {
                stored = jQuery.extend({ backdrop: true, keyboard: true }, option);
                jQuery(this).data('bs.modal.opts', stored);
                modalShow(this, stored);
            } else if (option === 'hide') {
                modalHide(this);
            } else {
                modalShow(this, stored.keyboard !== undefined ? stored : { backdrop: true, keyboard: true });
            }
        });
    };
    /* 关闭按钮 / 遮罩 / ESC */
    jQuery(document).on('click', '[data-dismiss="modal"], .io-close', function (e) {
        e.preventDefault();
        var $m = jQuery(this).closest('.modal');
        if ($m.length) modalHide($m.get(0));
    });
    jQuery(document).on('click', '.modal', function (e) {
        if (e.target !== this) return;
        var opts = jQuery(this).data('bs.modal.opts') || {};
        if (opts.backdrop !== 'static' && opts.backdrop !== false) modalHide(this);
    });
    jQuery(document).on('keydown', function (e) {
        if (e.key !== 'Escape') return;
        jQuery('.modal.show').each(function () {
            var opts = jQuery(this).data('bs.modal.opts') || {};
            if (opts.keyboard !== false) modalHide(this);
        });
        if (lightboxState.root) closeLightbox();
    });

    /* ============ 轻量灯箱（data-fancybox 图片组：截图/正文大图） ============ */
    var lightboxState = { root: null, list: [], idx: 0 };
    function isImageHref(href) {
        return /\.(jpe?g|png|gif|webp|bmp|svg)(\?|#|$)/i.test(href || '');
    }
    function renderLightbox() {
        if (!lightboxState.root) return;
        var item = lightboxState.list[lightboxState.idx] || {};
        lightboxState.root.querySelector('.io-lightbox-img').src = item.href || '';
        lightboxState.root.querySelector('.io-lightbox-caption').textContent = item.caption || '';
        var multi = lightboxState.list.length > 1;
        lightboxState.root.querySelector('.io-lightbox-prev').style.display = multi ? '' : 'none';
        lightboxState.root.querySelector('.io-lightbox-next').style.display = multi ? '' : 'none';
    }
    function openLightbox(list, idx) {
        lightboxState.list = list;
        lightboxState.idx = idx;
        if (!lightboxState.root) {
            var root = document.createElement('div');
            root.className = 'io-lightbox';
            root.innerHTML =
                '<div class="io-lightbox-stage">' +
                '<img class="io-lightbox-img" alt="">' +
                '<button type="button" class="io-lightbox-nav io-lightbox-prev" aria-label="上一张"><i class="iconfont icon-arrow-l"></i></button>' +
                '<button type="button" class="io-lightbox-nav io-lightbox-next" aria-label="下一张"><i class="iconfont icon-arrow-r"></i></button>' +
                '</div>' +
                '<div class="io-lightbox-caption"></div>' +
                '<button type="button" class="io-lightbox-close" aria-label="关闭"><i class="iconfont icon-close"></i></button>';
            document.body.appendChild(root);
            lightboxState.root = root;
            root.addEventListener('click', function (e) {
                if (e.target.closest('.io-lightbox-prev')) { e.preventDefault(); lightboxState.idx = (lightboxState.idx - 1 + lightboxState.list.length) % lightboxState.list.length; renderLightbox(); return; }
                if (e.target.closest('.io-lightbox-next')) { e.preventDefault(); lightboxState.idx = (lightboxState.idx + 1) % lightboxState.list.length; renderLightbox(); return; }
                if (e.target.closest('.io-lightbox-img')) return;
                closeLightbox();
            });
        }
        renderLightbox();
        void lightboxState.root.offsetWidth;
        lightboxState.root.classList.add('show');
    }
    function closeLightbox() {
        if (lightboxState.root) lightboxState.root.classList.remove('show');
    }
    jQuery(document).on('click', 'a[data-fancybox]', function (e) {
        var href = this.getAttribute('href') || '';
        if (!isImageHref(href)) return; // 非图片（如 iframe 链接）交回默认行为
        e.preventDefault();
        var group = this.getAttribute('data-fancybox') || '';
        var links = Array.prototype.slice.call(document.querySelectorAll('a[data-fancybox="' + group + '"]')).filter(function (a) {
            return isImageHref(a.getAttribute('href'));
        });
        if (!links.length) links = [this];
        var list = links.map(function (a) { return { href: a.getAttribute('href'), caption: a.getAttribute('data-caption') || '' }; });
        openLightbox(list, Math.max(0, links.indexOf(this)));
    });
})();
JS;
    wp_add_inline_script('theme-script', $shim_js, 'before');
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');

function theme_setup() {
    register_nav_menus(array(
        'header' => __('Header Menu', '115theme'),
    ));
    add_theme_support('custom-logo');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'theme_setup');

function create_custom_post_types() {
    $args_sites = array(
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'sites'),
        'supports'            => array('title', 'editor', 'thumbnail', 'custom-fields', 'tags'),
        'labels'              => array(
            'name'               => __('网站收录', '115theme'),
            'singular_name'      => __('网站收录', '115theme'),
            'add_new'            => __('添加网站', '115theme'),
            'add_new_item'       => __('添加网站', '115theme'),
            'edit_item'          => __('编辑网站', '115theme'),
            'new_item'           => __('新网站', '115theme'),
            'view_item'          => __('查看网站', '115theme'),
            'search_items'       => __('搜索网站', '115theme'),
            'not_found'          => __('未找到网站', '115theme'),
            'not_found_in_trash' => __('回收站中未找到网站', '115theme'),
        ),
        'menu_icon'           => 'dashicons-admin-links',
    );
    register_post_type('sites', $args_sites);

    $args_app = array(
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'app'),
        'supports'            => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'labels'              => array(
            'name'               => __('软件下载', '115theme'),
            'singular_name'      => __('软件下载', '115theme'),
        ),
        'menu_icon'           => 'dashicons-screenoptions',
    );
    register_post_type('app', $args_app);

    $args_book = array(
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'book'),
        'supports'            => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'labels'              => array(
            'name'               => __('书籍期刊', '115theme'),
            'singular_name'      => __('书籍期刊', '115theme'),
        ),
        'menu_icon'           => 'dashicons-book',
    );
    register_post_type('book', $args_book);
}
add_action('init', 'create_custom_post_types');

function create_taxonomies() {
    $args = array(
        'labels' => array(
            'name'              => __('分类', '115theme'),
            'singular_name'     => __('分类', '115theme'),
            'search_items'      => __('搜索分类', '115theme'),
            'all_items'         => __('所有分类', '115theme'),
            'parent_item'       => __('父分类', '115theme'),
            'parent_item_colon' => __('父分类:', '115theme'),
            'edit_item'         => __('编辑分类', '115theme'),
            'update_item'       => __('更新分类', '115theme'),
            'add_new_item'      => __('添加新分类', '115theme'),
            'new_item_name'     => __('新分类名称', '115theme'),
            'menu_name'         => __('分类', '115theme'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'favorites'),
    );
    register_taxonomy('favorites', array('sites', 'post', 'app', 'book'), $args);

    // 真标签（非层级），与目标站 /apptag/ /sitetag/ 一致
    $tag_args = array(
        'labels' => array(
            'name'          => __('标签', '115theme'),
            'singular_name' => __('标签', '115theme'),
            'search_items'  => __('搜索标签', '115theme'),
            'all_items'     => __('所有标签', '115theme'),
            'edit_item'     => __('编辑标签', '115theme'),
            'update_item'   => __('更新标签', '115theme'),
            'add_new_item'  => __('添加新标签', '115theme'),
            'new_item_name' => __('新标签名称', '115theme'),
            'menu_name'     => __('标签', '115theme'),
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'apptag'),
    );
    register_taxonomy('apptag', array('app'), $tag_args);

    $site_tag_args = $tag_args;
    $site_tag_args['rewrite'] = array('slug' => 'sitetag');
    register_taxonomy('sitetag', array('sites', 'post'), $site_tag_args);

    $book_tag_args = $tag_args;
    $book_tag_args['rewrite'] = array('slug' => 'booktag');
    register_taxonomy('booktag', array('book'), $book_tag_args);
}
add_action('init', 'create_taxonomies');

function theme_activate() {
    flush_rewrite_rules();
    theme_create_default_data();
    update_option('theme_default_data_seeded', THEME_VERSION);
}
add_action('after_switch_theme', 'theme_activate');

/**
 * 数据自愈：通过后台直接上传zip覆盖更新主题时，after_switch_theme 不会触发，
 * 会导致 favorites 分类为空、左侧分类导航和首页分类区块不显示。
 * 在 init 阶段检查版本标记，缺失时自动补齐默认分类与演示数据（内部均幂等）。
 */
function theme_ensure_default_data() {
    if (get_option('theme_default_data_seeded') === THEME_VERSION) {
        return;
    }
    theme_create_default_collections();
    theme_create_demo_sites();
    theme_create_demo_posts();
    theme_create_demo_apps();
    theme_create_default_friendlinks();
    theme_create_default_pages();
    theme_create_default_menu();
    // 详情页评论区依赖 comment_status=open；历史演示数据可能是 closed，批量打开前台内容类型的评论
    global $wpdb;
    $wpdb->query("UPDATE {$wpdb->posts} SET comment_status='open' WHERE post_type IN ('post','app','sites','book') AND post_status='publish' AND comment_status <> 'open'");
    // 新版本可能注册了新分类法（apptag/sitetag/booktag），刷新一次固定链接
    flush_rewrite_rules();
    update_option('theme_default_data_seeded', THEME_VERSION);
}
add_action('init', 'theme_ensure_default_data', 20);

function theme_deactivate() {
    flush_rewrite_rules();
}
add_action('switch_theme', 'theme_deactivate');

function theme_create_default_data() {
    theme_create_default_collections();
    theme_create_demo_sites();
    theme_create_demo_posts();
    theme_create_demo_apps();
    theme_create_default_friendlinks();
    theme_create_default_pages();
    theme_create_default_menu();
}

function theme_create_default_collections() {
    $default_collections = array(
        array('name' => '常用推荐', 'slug' => 'changyong'),
        array('name' => '网盘云储', 'slug' => 'wangpan'),
        array('name' => '社区资讯', 'slug' => 'zixun'),
        array('name' => '书籍期刊', 'slug' => 'qikan'),
        array('name' => '软件游戏', 'slug' => 'ruanjian'),
        array('name' => '设计工具', 'slug' => 'sheji'),
        array('name' => '开发工具', 'slug' => 'kaifa'),
        array('name' => '常用工具', 'slug' => 'gongju'),
        array('name' => '素材资源', 'slug' => 'sucai'),
        array('name' => '友情链接', 'slug' => 'friendlink'),
    );
    
    foreach ($default_collections as $collection) {
        if (!term_exists($collection['slug'], 'favorites')) {
            wp_insert_term(
                $collection['name'],
                'favorites',
                array('slug' => $collection['slug'])
            );
        }
    }
}

function theme_create_default_friendlinks() {
    $existing = get_option('theme_friendlinks', array());
    if (!empty($existing)) {
        return;
    }
    $default_friendlinks = array(
        array('name' => '115啦', 'url' => 'https://115.la', 'description' => '115啦导航'),
    );
    update_option('theme_friendlinks', $default_friendlinks);
}

/* 兼容 WP 6.2+：按标题查询指定文章类型（替代已弃用的 get_page_by_title） */
function theme_get_post_by_title($title, $post_type = 'sites') {
    $query = new WP_Query(array(
        'post_type'              => $post_type,
        'title'                  => $title,
        'post_status'            => 'all',
        'posts_per_page'         => 1,
        'no_found_rows'          => true,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
        'orderby'                => 'post_date ID',
        'order'                  => 'ASC',
    ));
    return !empty($query->posts) ? $query->posts[0] : null;
}

function theme_create_demo_sites() {
    $demo_sites = array(
        // 常用推荐
        array('title' => '哔哩哔哩', 'content' => '年轻人的视频社区', 'url' => 'https://www.bilibili.com', 'category' => 'changyong'),
        array('title' => '115啦', 'content' => '115啦导航', 'url' => 'https://115.la', 'category' => 'changyong'),
        array('title' => '百度', 'content' => '全球最大的中文搜索引擎', 'url' => 'https://www.baidu.com', 'category' => 'changyong'),
        array('title' => '谷歌', 'content' => '全球最大的搜索引擎', 'url' => 'https://www.google.com', 'category' => 'changyong'),
        array('title' => '豆瓣', 'content' => '中文互联网文化社区', 'url' => 'https://www.douban.com', 'category' => 'changyong'),
        array('title' => '知乎', 'content' => '中文互联网问答社区', 'url' => 'https://www.zhihu.com', 'category' => 'changyong'),
        array('title' => '微博', 'content' => '中国最大的社交媒体平台', 'url' => 'https://weibo.com', 'category' => 'changyong'),
        array('title' => '小红书', 'content' => '年轻人的生活方式平台', 'url' => 'https://www.xiaohongshu.com', 'category' => 'changyong'),
        array('title' => '淘宝', 'content' => '中国最大的电商平台', 'url' => 'https://www.taobao.com', 'category' => 'changyong'),
        array('title' => '京东', 'content' => '综合网购商城', 'url' => 'https://www.jd.com', 'category' => 'changyong'),
        array('title' => '抖音', 'content' => '短视频平台', 'url' => 'https://www.douyin.com', 'category' => 'changyong'),

        // 网盘云储
        array('title' => '百度网盘', 'content' => '百度推出的云存储服务', 'url' => 'https://pan.baidu.com', 'category' => 'wangpan'),
        array('title' => '阿里云盘', 'content' => '阿里推出的云存储服务', 'url' => 'https://www.alipan.com', 'category' => 'wangpan'),
        array('title' => 'OneDrive', 'content' => '微软云存储服务', 'url' => 'https://onedrive.live.com', 'category' => 'wangpan'),
        array('title' => 'Dropbox', 'content' => '全球知名云存储服务', 'url' => 'https://www.dropbox.com', 'category' => 'wangpan'),
        array('title' => '坚果云', 'content' => '专注办公的云存储服务', 'url' => 'https://www.jianguoyun.com', 'category' => 'wangpan'),
        array('title' => '夸克网盘', 'content' => '夸克浏览器云盘', 'url' => 'https://pan.quark.cn', 'category' => 'wangpan'),
        array('title' => '115网盘', 'content' => '115云存储服务', 'url' => 'https://www.115.com', 'category' => 'wangpan'),
        array('title' => '腾讯微云', 'content' => '腾讯云存储服务', 'url' => 'https://v.qq.com', 'category' => 'wangpan'),
        array('title' => '蓝奏云', 'content' => '蓝奏云存储', 'url' => 'https://www.lanzou.com', 'category' => 'wangpan'),
        array('title' => 'Google Drive', 'content' => '谷歌云盘', 'url' => 'https://drive.google.com', 'category' => 'wangpan'),

        // 社区资讯
        array('title' => '澎湃新闻', 'content' => '中国原创新闻平台', 'url' => 'https://www.thepaper.cn', 'category' => 'zixun'),
        array('title' => '36氪', 'content' => '互联网科技媒体', 'url' => 'https://36kr.com', 'category' => 'zixun'),
        array('title' => '虎嗅', 'content' => '商业科技资讯平台', 'url' => 'https://www.huxiu.com', 'category' => 'zixun'),
        array('title' => 'IT之家', 'content' => '科技资讯门户', 'url' => 'https://www.ithome.com', 'category' => 'zixun'),
        array('title' => '爱范儿', 'content' => '科技媒体', 'url' => 'https://www.ifanr.com', 'category' => 'zixun'),
        array('title' => '少数派', 'content' => '数字生活方式媒体', 'url' => 'https://sspai.com', 'category' => 'zixun'),
        array('title' => '虎扑', 'content' => '体育资讯社区', 'url' => 'https://www.hupu.com', 'category' => 'zixun'),
        array('title' => 'V2EX', 'content' => '创意工作者社区', 'url' => 'https://www.v2ex.com', 'category' => 'zixun'),
        array('title' => '掘金', 'content' => '开发者社区', 'url' => 'https://juejin.cn', 'category' => 'zixun'),
        array('title' => '知乎日报', 'content' => '知乎每日精选', 'url' => 'https://daily.zhihu.com', 'category' => 'zixun'),

        // 书籍期刊
        array('title' => '微信读书', 'content' => '腾讯推出的阅读App', 'url' => 'https://weread.qq.com', 'category' => 'qikan'),
        array('title' => '网易云阅读', 'content' => '网易推出的阅读平台', 'url' => 'https://book.163.com', 'category' => 'qikan'),
        array('title' => '掌阅', 'content' => '中文数字阅读平台', 'url' => 'https://www.zhangyue.com', 'category' => 'qikan'),
        array('title' => '起点中文网', 'content' => '原创文学门户', 'url' => 'https://www.qidian.com', 'category' => 'qikan'),
        array('title' => '晋江文学城', 'content' => '原创言情小说平台', 'url' => 'https://www.jjwxc.net', 'category' => 'qikan'),
        array('title' => '豆瓣读书', 'content' => '图书评论社区', 'url' => 'https://book.douban.com', 'category' => 'qikan'),
        array('title' => 'Kindle', 'content' => '亚马逊电子书', 'url' => 'https://www.amazon.cn/kindle', 'category' => 'qikan'),
        array('title' => '得到', 'content' => '知识付费平台', 'url' => 'https://www.dedao.cn', 'category' => 'qikan'),
        array('title' => '喜马拉雅', 'content' => '音频分享平台', 'url' => 'https://www.ximalaya.com', 'category' => 'qikan'),

        // 软件游戏
        array('title' => 'Steam', 'content' => '全球最大游戏平台', 'url' => 'https://store.steampowered.com', 'category' => 'ruanjian'),
        array('title' => 'Epic Games', 'content' => '游戏平台', 'url' => 'https://www.epicgames.com', 'category' => 'ruanjian'),
        array('title' => 'WeGame', 'content' => '腾讯游戏平台', 'url' => 'https://www.wegame.com.cn', 'category' => 'ruanjian'),
        array('title' => 'Photoshop', 'content' => 'Adobe专业图像处理软件', 'url' => 'https://www.adobe.com/products/photoshop.html', 'category' => 'ruanjian'),

        // 设计工具
        array('title' => 'Figma', 'content' => '在线设计协作工具', 'url' => 'https://www.figma.com', 'category' => 'sheji'),
        array('title' => 'Sketch', 'content' => 'Mac平台专业设计工具', 'url' => 'https://www.sketch.com', 'category' => 'sheji'),
        array('title' => 'Canva', 'content' => '在线设计平台', 'url' => 'https://www.canva.cn', 'category' => 'sheji'),
        array('title' => '创客贴', 'content' => '在线设计工具', 'url' => 'https://www.chuangkit.com', 'category' => 'sheji'),
        array('title' => '站酷', 'content' => '设计师社区', 'url' => 'https://www.zcool.com.cn', 'category' => 'sheji'),
        array('title' => '花瓣网', 'content' => '设计师灵感采集工具', 'url' => 'https://huaban.com', 'category' => 'sheji'),
        array('title' => 'Dribbble', 'content' => '设计师作品展示平台', 'url' => 'https://dribbble.com', 'category' => 'sheji'),
        array('title' => 'Behance', 'content' => 'Adobe创意作品平台', 'url' => 'https://www.behance.net', 'category' => 'sheji'),
        array('title' => '在线配色', 'content' => '专业配色工具', 'url' => 'https://color.adobe.com', 'category' => 'sheji'),

        // 开发工具
        array('title' => 'GitHub', 'content' => '全球最大的代码托管平台', 'url' => 'https://github.com', 'category' => 'kaifa'),
        array('title' => 'VS Code', 'content' => '微软开发的代码编辑器', 'url' => 'https://code.visualstudio.com', 'category' => 'kaifa'),
        array('title' => 'WebStorm', 'content' => 'JetBrains前端IDE', 'url' => 'https://www.jetbrains.com/webstorm', 'category' => 'kaifa'),
        array('title' => 'IntelliJ IDEA', 'content' => 'Java开发IDE', 'url' => 'https://www.jetbrains.com/idea', 'category' => 'kaifa'),
        array('title' => 'PyCharm', 'content' => 'Python开发IDE', 'url' => 'https://www.jetbrains.com/pycharm', 'category' => 'kaifa'),
        array('title' => 'Notepad++', 'content' => '免费代码编辑器', 'url' => 'https://notepad-plus-plus.org', 'category' => 'kaifa'),
        array('title' => 'Sublime Text', 'content' => '代码编辑器', 'url' => 'https://www.sublimetext.com', 'category' => 'kaifa'),
        array('title' => '腾讯CDC', 'content' => '腾讯用户研究与体验设计', 'url' => 'https://cdc.tencent.com', 'category' => 'kaifa'),
        array('title' => '阿里UX', 'content' => '阿里巴巴设计团队', 'url' => 'https://ux.alibabagroup.com', 'category' => 'kaifa'),

        // 常用工具
        array('title' => 'TinyPNG', 'content' => '在线图片压缩', 'url' => 'https://tinypng.com', 'category' => 'gongju'),
        array('title' => '在线PS', 'content' => '在线Photoshop', 'url' => 'https://www.photopea.com', 'category' => 'gongju'),
        array('title' => '二维码生成', 'content' => '在线二维码生成器', 'url' => 'https://cli.im', 'category' => 'gongju'),
        array('title' => 'JSON格式化', 'content' => 'JSON在线格式化工具', 'url' => 'https://www.json.cn', 'category' => 'gongju'),
        array('title' => '站长工具', 'content' => '站长SEO工具', 'url' => 'https://tool.chinaz.com', 'category' => 'gongju'),
        array('title' => 'ProcessOn', 'content' => '在线流程图制作', 'url' => 'https://www.processon.com', 'category' => 'gongju'),
        array('title' => '墨刀', 'content' => '原型设计工具', 'url' => 'https://modao.cc', 'category' => 'gongju'),
        array('title' => '图标搜索', 'content' => '免费图标搜索平台', 'url' => 'https://www.flaticon.com', 'category' => 'gongju'),

        // 素材资源
        array('title' => 'Pexels', 'content' => '免费图片素材库', 'url' => 'https://www.pexels.com', 'category' => 'sucai'),
        array('title' => 'Unsplash', 'content' => '高质量免费图片', 'url' => 'https://unsplash.com', 'category' => 'sucai'),
        array('title' => 'Pixabay', 'content' => '免费图片视频素材', 'url' => 'https://pixabay.com', 'category' => 'sucai'),
        array('title' => '千库网', 'content' => '设计素材库', 'url' => 'https://www.58pic.com', 'category' => 'sucai'),
        array('title' => '包图网', 'content' => '设计素材平台', 'url' => 'https://ibaotu.com', 'category' => 'sucai'),
        array('title' => '摄图网', 'content' => '正版图片素材', 'url' => 'https://699pic.com', 'category' => 'sucai'),
        array('title' => '阿里图标库', 'content' => '免费图标库', 'url' => 'https://www.iconfont.cn', 'category' => 'sucai'),

        // 友情链接
        array('title' => '115啦', 'content' => '115啦导航', 'url' => 'https://115.la', 'category' => 'friendlink'),
        array('title' => '百度', 'content' => '百度搜索', 'url' => 'https://www.baidu.com', 'category' => 'friendlink'),
        array('title' => 'Google', 'content' => '谷歌搜索', 'url' => 'https://www.google.com', 'category' => 'friendlink'),
    );
    
    foreach ($demo_sites as $site) {
        $existing = theme_get_post_by_title($site['title'], 'sites');
        if (!$existing) {
            $term = get_term_by('slug', $site['category'], 'favorites');
            $post_id = wp_insert_post(array(
                'post_title'    => $site['title'],
                'post_content'  => $site['content'],
                'post_status'   => 'publish',
                'post_type'     => 'sites',
            ));
            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, 'site_url', $site['url']);
                update_post_meta($post_id, 'site_views', rand(100, 10000));
                update_post_meta($post_id, 'site_likes', rand(10, 500));
                update_post_meta($post_id, '_theme_demo', '1');
                if ($term) {
                    wp_set_post_terms($post_id, array($term->term_id), 'favorites');
                }
            }
        }
    }
}

/**
 * 演示资讯文章（10篇，post 类型）
 */
function theme_create_demo_posts() {
    $demo_posts = array(
        array('title' => '人工智能大模型持续进化，多模态应用加速落地', 'content' => '近年来，人工智能大模型技术快速迭代，从单一文本生成走向图文、音视频多模态融合。业内专家表示，随着算力成本下降与开源生态繁荣，AI 应用正加速渗透到办公、教育、医疗、设计等各个行业，显著提升生产效率。未来，AI 智能体（Agent）有望成为继搜索引擎之后的又一通用入口。'),
        array('title' => '国产芯片再获突破，先进封装工艺成为竞争焦点', 'content' => '国内半导体产业链近期传来好消息，多家厂商在先进封装领域取得阶段性成果。分析人士指出，在制程工艺受限的背景下，通过 Chiplet（小芯片）与 2.5D/3D 封装技术提升整体性能，成为国产芯片突围的重要路径，相关产业链公司持续加大研发投入。'),
        array('title' => '新能源汽车销量再创新高，智能化配置成购车首选', 'content' => '最新数据显示，国内新能源汽车月度渗透率持续攀升，智能驾驶、智能座舱成为消费者最关注的配置。多家车企发布城市 NOA（导航辅助驾驶）功能，用车体验不断升级。与此同时，充电基础设施建设加快，高速公路服务区充电桩覆盖率大幅提升。'),
        array('title' => '低空经济写入政府工作报告，无人机应用场景不断拓展', 'content' => '低空经济作为战略性新兴产业受到广泛关注。除物流配送、航拍巡线外，无人机在应急救援、城市治理、文旅观光等领域的应用不断拓展。专家预计，随着空域管理改革深化与 eVTOL（电动垂直起降飞行器）成熟，低空经济将形成万亿级市场规模。'),
        array('title' => '开源社区活力迸发，中国开发者贡献度稳居全球前列', 'content' => '全球最大代码托管平台年度报告显示，中国开发者数量与开源项目贡献度持续增长，在人工智能、云原生、前端框架等领域涌现出一批高影响力的开源项目。业内认为，开源已成为技术创新的重要协作模式，也是企业构建技术生态的关键抓手。'),
        array('title' => '6G 研发稳步推进，太赫兹通信试验取得进展', 'content' => '工信部相关负责人表示，我国 6G 技术研发试验稳步推进，在太赫兹通信、智能超表面、通感一体化等关键技术方向取得阶段性成果。按照规划，6G 有望在 2030 年前后实现商用，将支撑全息通信、数字孪生、沉浸式 XR 等全新应用场景。'),
        array('title' => '数据要素市场加速培育，数据资产入表企业增多', 'content' => '随着数据要素相关政策落地，数据资产确认、登记、评估、交易体系逐步完善，越来越多企业完成数据资源入表。专家表示，数据作为新型生产要素，其价值释放需要流通交易机制与安全合规体系双轮驱动，隐私计算等技术将发挥重要作用。'),
        array('title' => '跨境电商保持快速增长，独立站与品牌化趋势明显', 'content' => '跨境电商行业延续快速增长态势，越来越多卖家从平台铺货转向独立站运营与品牌建设。AI 选品、智能客服、多语言本地化运营工具大幅降低了出海门槛。物流方面，海外仓数量持续增加，履约时效显著改善。'),
        array('title' => '国产操作系统生态日益完善，政企迁移步伐加快', 'content' => '国产操作系统在兼容性、稳定性和易用性方面持续提升，软硬件适配数量突破百万款。在党政、金融、能源等关键行业，规模化替代应用不断落地。社区生态方面，主流国产系统均基于开源内核构建，应用商店软件数量快速增长。'),
        array('title' => '数字人直播走向常态化，AIGC 重塑内容生产流程', 'content' => '电商直播间里，24 小时不下线的数字人主播越来越常见。结合 AIGC 技术，数字人可以自动生成话术、实时回答观众提问，直播成本大幅降低。除电商外，数字人在新闻播报、企业培训、文旅导览等场景也开始规模化应用。'),
    );

    $zixun_term = get_term_by('slug', 'zixun', 'favorites');
    foreach ($demo_posts as $p) {
        $existing = theme_get_post_by_title($p['title'], 'post');
        if ($existing) {
            $post_id = $existing->ID;
        } else {
            $post_id = wp_insert_post(array(
                'post_title'   => $p['title'],
                'post_content' => $p['content'],
                'post_status'  => 'publish',
                'post_type'    => 'post',
            ));
            $post_id = ($post_id && !is_wp_error($post_id)) ? $post_id : 0;
        }
        if ($post_id) {
            update_post_meta($post_id, '_theme_demo', '1');
            if ($zixun_term) {
                wp_set_post_terms($post_id, array($zixun_term->term_id), 'favorites');
            }
        }
    }
}

/**
 * 演示下载软件（10个，app 类型）
 */
function theme_create_demo_apps() {
    $demo_apps = array(
        array('title' => '微信', 'content' => '国民级即时通讯应用，支持聊天、语音视频通话、朋友圈、小程序与移动支付。', 'url' => 'https://pc.weixin.qq.com', 'size' => '268MB', 'platform' => 'Windows / Mac / 手机端', 'version' => '3.9.10', 'tags' => array('聊天', '社交', '免费')),
        array('title' => 'QQ', 'content' => '腾讯旗下经典即时通讯软件，支持文件互传、群聊、屏幕分享与游戏中心。', 'url' => 'https://im.qq.com', 'size' => '215MB', 'platform' => 'Windows / Mac / 手机端', 'version' => '9.9.15', 'tags' => array('聊天', '社交', '免费')),
        array('title' => '抖音', 'content' => '短视频与直播平台，海量精彩内容，支持创作、分享与电商购物。', 'url' => 'https://www.douyin.com/downloadpage', 'size' => '186MB', 'platform' => 'Windows / 手机端', 'version' => '27.5.0', 'tags' => array('短视频', '视频', '直播')),
        array('title' => '支付宝', 'content' => '数字生活开放平台，移动支付、理财、生活缴费一站式服务。', 'url' => 'https://www.alipay.com', 'size' => '152MB', 'platform' => '手机端', 'version' => '10.6.0', 'tags' => array('支付', '理财', '生活')),
        array('title' => '淘宝', 'content' => '综合性网上购物平台，亿万商品随心购，支持直播带货与百亿补贴。', 'url' => 'https://www.taobao.com/markets/n/taobaoapp', 'size' => '198MB', 'platform' => '手机端', 'version' => '12.3.2', 'tags' => array('购物', '电商', '直播')),
        array('title' => '京东', 'content' => '正品自营电商平台，数码家电品质之选，物流极速送达。', 'url' => 'https://app.jd.com', 'size' => '173MB', 'platform' => '手机端', 'version' => '14.1.2', 'tags' => array('购物', '电商', '数码')),
        array('title' => '美团', 'content' => '本地生活服务平台，外卖、酒店、买菜、打车一站式搞定。', 'url' => 'https://www.meituan.com/mobile/download/meituan', 'size' => '165MB', 'platform' => '手机端', 'version' => '12.20.4', 'tags' => array('外卖', '生活', '团购')),
        array('title' => '高德地图', 'content' => '专业的手机地图导航，实时路况、打车、公交地铁出行全覆盖。', 'url' => 'https://www.amap.com', 'size' => '241MB', 'platform' => '手机端', 'version' => '13.5.1', 'tags' => array('地图', '导航', '出行')),
        array('title' => '网易云音乐', 'content' => '专注发现与分享的音乐社区，海量曲库、个性推荐与乐评文化。', 'url' => 'https://music.163.com/#/download', 'size' => '196MB', 'platform' => 'Windows / Mac / 手机端', 'version' => '3.0.18', 'tags' => array('音乐', '播放器', '免费')),
        array('title' => '哔哩哔哩', 'content' => '年轻人聚集的视频社区，番剧、游戏、知识、生活内容丰富多彩。', 'url' => 'https://app.bilibili.com', 'size' => '205MB', 'platform' => '手机端', 'version' => '8.1.0', 'tags' => array('视频', '弹幕', '动漫')),
    );

    $ruanjian_term = get_term_by('slug', 'ruanjian', 'favorites');
    foreach ($demo_apps as $idx => $app) {
        $existing = theme_get_post_by_title($app['title'], 'app');
        if ($existing) {
            $post_id = $existing->ID;
        } else {
            $post_id = wp_insert_post(array(
                'post_title'   => $app['title'],
                'post_content' => $app['content'],
                'post_status'  => 'publish',
                'post_type'    => 'app',
            ));
            $post_id = ($post_id && !is_wp_error($post_id)) ? $post_id : 0;
        }
        if ($post_id) {
            update_post_meta($post_id, 'download_url', $app['url']);
            update_post_meta($post_id, 'file_size', $app['size']);
            update_post_meta($post_id, 'app_platform', $app['platform']);
            update_post_meta($post_id, 'app_version', $app['version']);
            update_post_meta($post_id, 'app_language', '中文');
            if (!get_post_meta($post_id, 'app_favorites', true)) {
                update_post_meta($post_id, 'app_favorites', rand(10, 500));
            }
            if (!get_post_meta($post_id, 'site_views', true)) {
                update_post_meta($post_id, 'site_views', rand(800, 9000));
            }
            if (!get_post_meta($post_id, 'download_count', true)) {
                update_post_meta($post_id, 'download_count', rand(50, 800));
            }
            update_post_meta($post_id, '_theme_demo', '1');
            if ($ruanjian_term) {
                wp_set_post_terms($post_id, array($ruanjian_term->term_id), 'favorites');
            }
            if (!empty($app['tags'])) {
                wp_set_post_terms($post_id, $app['tags'], 'apptag', false);
            }
        }
    }
}

/**
 * 清空所有演示数据：仅删除带 _theme_demo 标记的演示内容（网址/文章/软件），
 * 用户自行添加的数据不受影响；演示分类清空后自动移除。
 */
function theme_clear_demo_data() {
    // 删除带演示标记的内容：网址 / 文章 / 软件
    $demo_types = array('sites', 'post', 'app');
    foreach ($demo_types as $pt) {
        $items = get_posts(array(
            'post_type'      => $pt,
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'meta_key'       => '_theme_demo',
            'meta_value'     => '1',
        ));
        foreach ($items as $pid) {
            wp_delete_post($pid, true);
        }
    }
    // 演示分类：内容清空后删除空的默认分类（用户自建分类不动）
    $default_slugs = array('changyong', 'wangpan', 'zixun', 'qikan', 'ruanjian', 'sheji', 'kaifa', 'gongju', 'sucai', 'friendlink');
    foreach ($default_slugs as $slug) {
        $term = get_term_by('slug', $slug, 'favorites');
        if ($term && $term->count < 1) {
            wp_delete_term($term->term_id, 'favorites');
        }
    }
}

function theme_get_site_url($post_id) {
    return get_post_meta($post_id, 'site_url', true);
}

function theme_get_site_views($post_id) {
    return get_post_meta($post_id, 'site_views', true) ?: 0;
}

function theme_get_site_likes($post_id) {
    return get_post_meta($post_id, 'site_likes', true) ?: 0;
}

/**
 * 网址图标本地化
 * 远程获取 favicon → 保存到 uploads/websiteico/ → 文件名用网站名字
 * 通过 post meta _local_favicon 缓存本地 URL
 *
 * 前台渲染不阻塞：无缓存时立即回退远程 URL，同时登记 shutdown 任务，
 * 在页面响应发出后（fastcgi_finish_request）静默下载，下次访问即命中本地文件；
 * 后台单条/批量刷新走 AJAX 同步下载（theme_download_favicon）并返回详细结果。
 */
function theme_get_local_favicon($post_id) {
    if (!$post_id) return '';

    $domain = theme_get_site_domain($post_id);
    if (!$domain) return '';

    $options = get_option('theme_settings');
    $localize_enabled = !is_array($options) || !isset($options['theme_favicon_localize']) || $options['theme_favicon_localize'];

    // 0. 未启用本地化 → 直接用配置的远程服务
    if (!$localize_enabled) {
        return theme_favicon_service_url($domain);
    }

    // 1. 命中 meta 缓存 → 验证文件仍存在 → 直接返回
    $cached = get_post_meta($post_id, '_local_favicon', true);
    if ($cached) {
        $upload_dir = wp_upload_dir();
        $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $cached);
        if (file_exists($file_path) && filesize($file_path) > 0) {
            return $cached;
        }
        // 文件已删除或损坏 → 清缓存重新下载
        delete_post_meta($post_id, '_local_favicon');
    }

    // 2. 远程回退 URL
    $remote_url = theme_favicon_service_url($domain);

    // 3. 失败冷却期内（上次下载失败 1 小时内）→ 直接回退远程，不重复尝试拖慢页面
    if (get_transient('theme_fav_fail_' . $post_id)) {
        return $remote_url;
    }

    // 4. 无缓存：登记 shutdown 异步下载任务，本次先回退远程 URL（不阻塞渲染）
    if (!isset($GLOBALS['_theme_fav_pending'])) {
        $GLOBALS['_theme_fav_pending'] = array();
        add_action('shutdown', 'theme_favicon_shutdown_download', 99);
    }
    $GLOBALS['_theme_fav_pending'][$post_id] = $domain;

    return $remote_url;
}

/**
 * shutdown：页面响应发送后静默下载图标
 */
function theme_favicon_shutdown_download() {
    $pending = isset($GLOBALS['_theme_fav_pending']) ? $GLOBALS['_theme_fav_pending'] : array();
    if (!$pending) return;

    @ignore_user_abort(true);
    // 支持 fastcgi_finish_request（Nginx+PHP-FPM / Apache+proxy_fcgi）时先把响应发给浏览器；
    // 老 mod_php 没有该函数，则用短时间预算，避免首次访问长时间白屏（剩余图标下次访问继续）
    $can_background = function_exists('fastcgi_finish_request');
    if ($can_background) {
        @fastcgi_finish_request();
    }
    @set_time_limit($can_background ? 120 : 25);
    $budget = $can_background ? 90 : 12;
    $started = microtime(true);

    foreach ($pending as $pid => $domain) {
        if (microtime(true) - $started > $budget) break;
        theme_download_favicon($pid, $domain);
    }
}

/**
 * 取网站链接的域名
 */
function theme_get_site_domain($post_id) {
    static $cache = array();
    if (isset($cache[$post_id])) return $cache[$post_id];
    $site_url = theme_get_site_url($post_id);
    $domain = '';
    if ($site_url) {
        $parsed = wp_parse_url($site_url);
        $domain = isset($parsed['host']) ? $parsed['host'] : '';
    }
    $cache[$post_id] = $domain;
    return $domain;
}

/**
 * 后台配置的 favicon 服务 URL（前台回退显示用）
 */
function theme_favicon_service_url($domain) {
    $options = get_option('theme_settings');
    $service = is_array($options) && !empty($options['theme_favicon_url'])
        ? $options['theme_favicon_url']
        : 'https://www.google.com/s2/favicons?domain={domain}&sz=32';
    return str_replace('{domain}', urlencode($domain), $service);
}

/**
 * 下载候选源（按优先级）：
 * 1) 后台配置的 favicon 服务（默认 Google）
 * 2) 网站自身 /favicon.ico（https，失败再 http）——国内服务器也能连通
 */
function theme_favicon_candidate_urls($domain) {
    $urls = array();
    $service = theme_favicon_service_url($domain);
    if ($service) $urls[] = array('url' => $service, 'timeout' => 6);
    $urls[] = array('url' => 'https://' . $domain . '/favicon.ico', 'timeout' => 5);
    $urls[] = array('url' => 'http://' . $domain . '/favicon.ico', 'timeout' => 5);
    return $urls;
}

/**
 * 按二进制魔数识别图片类型，返回扩展名；非图片返回空字符串
 */
function theme_favicon_detect_ext($body, $content_type = '') {
    if (strlen($body) < 6) return '';
    if ($body[0] === "\x89" && substr($body, 1, 3) === 'PNG') return 'png';
    if (substr($body, 0, 3) === "\xFF\xD8\xFF") return 'jpg';
    if (substr($body, 0, 3) === 'GIF') return 'gif';
    if (substr($body, 0, 4) === 'RIFF' && substr($body, 8, 4) === 'WEBP') return 'webp';
    if (substr($body, 0, 4) === "\x00\x00\x01\x00") return 'ico';
    // SVG（开头可能有 BOM/空白/<?xml）
    $head = ltrim(substr($body, 0, 512));
    if (stripos($head, '<svg') === 0 || stripos($head, '<?xml') === 0) return 'svg';
    // content-type 兜底
    if ($content_type) {
        if (strpos($content_type, 'png') !== false) return 'png';
        if (strpos($content_type, 'jpeg') !== false || strpos($content_type, 'jpg') !== false) return 'jpg';
        if (strpos($content_type, 'gif') !== false) return 'gif';
        if (strpos($content_type, 'webp') !== false) return 'webp';
        if (strpos($content_type, 'x-icon') !== false || strpos($content_type, 'vnd.microsoft.icon') !== false) return 'ico';
        if (strpos($content_type, 'svg') !== false) return 'svg';
    }
    return '';
}

/**
 * 从网站名字生成安全文件名
 */
function theme_favicon_safe_name($post_id) {
    $filename = get_the_title($post_id);
    // 去掉 Windows / Linux 非法文件名字符
    $filename = str_replace(array('\\', '/', ':', '*', '?', '"', '<', '>', '|', "\0"), '', $filename);
    // 连续空白→下划线
    $filename = trim(preg_replace('/\s+/', '_', $filename));
    // 去掉已有扩展名后缀
    $filename = preg_replace('/\.(png|ico|jpg|jpeg|gif|webp|svg)$/i', '', $filename);
    // 长度截断（避免文件系统路径过长）
    if (function_exists('mb_strlen')) {
        if (mb_strlen($filename) > 60) $filename = mb_substr($filename, 0, 60);
    } elseif (strlen($filename) > 60) {
        $filename = substr($filename, 0, 60);
    }
    if ($filename === '') $filename = 'site_' . $post_id;
    return $filename;
}

/**
 * 真正执行下载（多源容错 + 图片校验）并落盘
 * 返回 array('ok'=>bool, 'url'=>本地URL或'', 'error'=>失败原因)
 */
function theme_download_favicon($post_id, $domain = '') {
    if (!$post_id) return array('ok' => false, 'url' => '', 'error' => '缺少网站 ID');
    if (!$domain) $domain = theme_get_site_domain($post_id);
    if (!$domain) return array('ok' => false, 'url' => '', 'error' => '网站链接为空或无法识别域名');

    $upload_dir = wp_upload_dir();
    $target_dir = $upload_dir['basedir'] . '/websiteico';
    $target_url_base = $upload_dir['baseurl'] . '/websiteico';
    if (!file_exists($target_dir)) {
        wp_mkdir_p($target_dir);
        @file_put_contents($target_dir . '/index.php', '<?php // Silence is golden');
        @file_put_contents($target_dir . '/.htaccess', "Options -Indexes\n");
    }

    $filename = theme_favicon_safe_name($post_id);

    // 同名文件已存在（同名网站复用图标）
    foreach (array('png', 'ico', 'jpg', 'jpeg', 'gif', 'webp', 'svg') as $ext) {
        $candidate = $target_dir . '/' . $filename . '.' . $ext;
        if (file_exists($candidate) && filesize($candidate) > 0) {
            $local_url = $target_url_base . '/' . $filename . '.' . $ext;
            update_post_meta($post_id, '_local_favicon', $local_url);
            delete_transient('theme_fav_fail_' . $post_id);
            return array('ok' => true, 'url' => $local_url, 'error' => '');
        }
    }

    // transient 锁，防止并发重复下载
    $lock_key = 'theme_fav_lock_' . $post_id;
    if (get_transient($lock_key)) {
        return array('ok' => false, 'url' => '', 'error' => '另一请求正在下载，请几秒后重试');
    }
    set_transient($lock_key, 1, 30);

    $errors = array();
    $saved = false;
    $local_url = '';

    foreach (theme_favicon_candidate_urls($domain) as $src) {
        $response = wp_remote_get($src['url'], array(
            'timeout'     => $src['timeout'],
            'redirection' => 3,
            'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
            'headers'     => array('Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8'),
            'sslverify'   => false,
        ));

        if (is_wp_error($response)) {
            $errors[] = $src['url'] . ' => ' . $response->get_error_message();
            continue;
        }
        $code = (int) wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        if ($code !== 200) {
            $errors[] = $src['url'] . ' => HTTP ' . $code;
            continue;
        }
        if (strlen($body) < 50) {
            $errors[] = $src['url'] . ' => 内容过小（' . strlen($body) . ' 字节）';
            continue;
        }
        $content_type = wp_remote_retrieve_header($response, 'content-type');
        $ext = theme_favicon_detect_ext($body, $content_type);
        if (!$ext) {
            $errors[] = $src['url'] . ' => 返回内容不是图片（' . trim(substr($content_type, 0, 40)) . '）';
            continue;
        }

        $file_path = $target_dir . '/' . $filename . '.' . $ext;
        $written = @file_put_contents($file_path, $body);
        if ($written === false || $written < 50) {
            if (file_exists($file_path)) @unlink($file_path);
            $errors[] = $src['url'] . ' => 写入文件失败';
            continue;
        }

        $local_url = $target_url_base . '/' . $filename . '.' . $ext;
        $saved = true;
        break;
    }

    delete_transient($lock_key);

    if ($saved) {
        update_post_meta($post_id, '_local_favicon', $local_url);
        delete_transient('theme_fav_fail_' . $post_id);
        return array('ok' => true, 'url' => $local_url, 'error' => '');
    }

    // 全部失败：冷却 1 小时，避免前台每次请求都重试
    set_transient('theme_fav_fail_' . $post_id, 1, HOUR_IN_SECONDS);
    return array('ok' => false, 'url' => '', 'error' => implode('；', $errors));
}

/**
 * 网址图标刷新（删除旧缓存文件 + 清 meta/失败标记/锁）
 */
function theme_refresh_favicon($post_id) {
    $cached = get_post_meta($post_id, '_local_favicon', true);
    if ($cached) {
        $upload_dir = wp_upload_dir();
        $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $cached);
        if (file_exists($file_path)) @unlink($file_path);
        delete_post_meta($post_id, '_local_favicon');
    }
    delete_transient('theme_fav_fail_' . $post_id);
    delete_transient('theme_fav_lock_' . $post_id);
}

/* AJAX：后台单条/批量刷新网址图标 */
add_action('wp_ajax_theme_refresh_favicons', 'theme_ajax_refresh_favicons');
function theme_ajax_refresh_favicons() {
    if (!current_user_can('edit_posts')) wp_send_json_error(array('msg' => '无权限'));
    check_ajax_referer('theme_ajax_nonce', 'nonce');
    @set_time_limit(120);

    $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
    if ($post_id) {
        theme_refresh_favicon($post_id);
        $result = theme_download_favicon($post_id);
        if ($result['ok']) {
            wp_send_json_success(array('url' => $result['url'], 'local' => true, 'msg' => '图标已本地化'));
        }
        wp_send_json_success(array('url' => '', 'local' => false, 'msg' => '下载失败：' . $result['error']));
    }

    // 批量：每批 10 个，前端分批递归调用
    $limit  = isset($_POST['limit']) ? min(20, max(1, (int) $_POST['limit'])) : 10;
    $offset = isset($_POST['offset']) ? max(0, (int) $_POST['offset']) : 0;
    $q = new WP_Query(array(
        'post_type'      => 'sites',
        'posts_per_page' => $limit,
        'offset'         => $offset,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ));

    $done = 0;
    $failed = 0;
    $last_error = '';
    foreach ($q->posts as $pid) {
        // 已有有效本地文件则跳过，只计数
        $cached = get_post_meta($pid, '_local_favicon', true);
        if ($cached) {
            $upload_dir = wp_upload_dir();
            $fp = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $cached);
            if (file_exists($fp) && filesize($fp) > 0) {
                $done++;
                continue;
            }
        }
        $r = theme_download_favicon($pid);
        if ($r['ok']) {
            $done++;
        } else {
            $failed++;
            $last_error = $r['error'];
        }
    }

    $processed = $offset + count($q->posts);
    $total = (int) wp_count_posts('sites')->publish;
    $msg = sprintf('已处理 %d / %d，本批失败 %d', $processed, $total, $failed);
    if ($failed && $last_error) {
        $msg .= '（最后错误：' . (function_exists('mb_substr') ? mb_substr($last_error, 0, 150) : substr($last_error, 0, 150)) . '）';
    }
    wp_send_json_success(array(
        'done'   => $done,
        'failed' => $failed,
        'offset' => $processed,
        'total'  => $total,
        'msg'    => $msg,
    ));
}

/**
 * 文章浏览量（post 文章独立计数，兼容 site_views）
 */
function theme_get_post_views($post_id) {
    $views = get_post_meta($post_id, 'post_views', true);
    if ($views === '' || $views === false) {
        $views = get_post_meta($post_id, 'site_views', true);
    }
    return $views ?: 0;
}

/**
 * APP 下载地址 / 文件大小 / 下载次数
 */
function theme_get_download_url($post_id) {
    return get_post_meta($post_id, 'download_url', true);
}

function theme_get_file_size($post_id) {
    return get_post_meta($post_id, 'file_size', true);
}

function theme_get_download_count($post_id) {
    return get_post_meta($post_id, 'download_count', true) ?: 0;
}

function theme_get_app_language($post_id) {
    return get_post_meta($post_id, 'app_language', true) ?: '中文';
}

function theme_get_app_platform($post_id) {
    return get_post_meta($post_id, 'app_platform', true) ?: '多平台';
}

function theme_get_app_version($post_id) {
    return get_post_meta($post_id, 'app_version', true) ?: '';
}

/**
 * 平台文字 -> 图标数组 array(array('icon'=>'icon-microsoft','title'=>'Windows'), ...)
 */
function theme_get_app_platform_icons($post_id) {
    $text = mb_strtolower(get_post_meta($post_id, 'app_platform', true) ?: '多平台');
    if ($text === '') $text = '多平台';
    $map = array(
        array('keys' => array('windows', 'win', '微软', 'pc端', ' pc', 'xp', 'win7', 'win10', 'win11'), 'icon' => 'icon-microsoft', 'title' => 'PC'),
        array('keys' => array('mac', 'os x', 'osx', '苹果电脑'), 'icon' => 'icon-mac', 'title' => 'Mac OS'),
        array('keys' => array('linux', '乌班图', 'ubuntu'), 'icon' => 'icon-linux', 'title' => 'Linux'),
        array('keys' => array('android', '安卓'), 'icon' => 'icon-android', 'title' => '安卓'),
        array('keys' => array('ios', 'iphone', 'ipad'), 'icon' => 'icon-app-store-fill', 'title' => 'IOS'),
    );
    $icons = array();
    foreach ($map as $m) {
        foreach ($m['keys'] as $k) {
            if (mb_strpos($text, $k) !== false) {
                $icons[$m['icon']] = array('icon' => $m['icon'], 'title' => $m['title']);
                break;
            }
        }
    }
    $is_mobile_only = ($icons === array()) && (mb_strpos($text, '手机') !== false || mb_strpos($text, '移动') !== false || mb_strpos($text, '安卓') !== false || mb_strpos($text, 'ios') !== false);
    if ($icons === array() && $is_mobile_only) {
        $icons['icon-android'] = array('icon' => 'icon-android', 'title' => 'Android');
        $icons['icon-phone'] = array('icon' => 'icon-phone', 'title' => 'iOS');
    }
    if ($icons === array()) {
        $icons = array(
            'icon-microsoft' => array('icon' => 'icon-microsoft', 'title' => 'Windows'),
            'icon-mac'       => array('icon' => 'icon-mac', 'title' => 'macOS'),
            'icon-android'   => array('icon' => 'icon-android', 'title' => 'Android'),
        );
    } elseif (mb_strpos($text, '手机端') !== false || mb_strpos($text, '移动端') !== false) {
        $icons['icon-android'] = array('icon' => 'icon-android', 'title' => 'Android');
    }
    return array_values($icons);
}

/**
 * 数字美化：>=10000 显示 x.x 万，>=1000 显示 x.xK
 */
function theme_format_num($n) {
    $n = (int) $n;
    if ($n >= 10000) {
        return rtrim(rtrim(number_format($n / 10000, 1), '0'), '.') . '万';
    }
    if ($n >= 1000) {
        return rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'K';
    }
    return (string) $n;
}

/**
 * 相关内容查询：同 favorites 分类，排除当前文章
 */
function theme_get_related_posts($post_id, $count = 4) {
    return theme_get_related_items($post_id, 'post', $count);
}

function theme_get_related_apps($post_id, $count = 8) {
    return theme_get_related_items($post_id, 'app', $count);
}

function theme_get_related_books($post_id, $count = 8) {
    return theme_get_related_items($post_id, 'book', $count);
}

function theme_get_related_sites($post_id, $count = 8) {
    return theme_get_related_items($post_id, 'sites', $count);
}

function theme_get_related_items($post_id, $post_type, $count) {
    $args = array(
        'post_type'      => $post_type,
        'posts_per_page' => $count,
        'post__not_in'   => array($post_id),
        'post_status'    => 'publish',
        'no_found_rows'  => true,
    );
    // 网址保持原有按浏览量排序，其他类型按发布时间
    if ($post_type === 'sites') {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = 'site_views';
        $args['order']    = 'DESC';
    }
    $terms = wp_get_post_terms($post_id, 'favorites', array('fields' => 'ids'));
    if (!is_wp_error($terms) && !empty($terms)) {
        $args['tax_query'] = array(array(
            'taxonomy' => 'favorites',
            'field'    => 'term_id',
            'terms'    => $terms,
        ));
    }
    return new WP_Query($args);
}

function theme_get_top_collections($hide_empty = false) {
    $default_order = array('changyong', 'wangpan', 'zixun', 'qikan', 'ruanjian', 'sheji', 'kaifa', 'gongju', 'sucai', 'friendlink');
    $terms = array();
    foreach ($default_order as $slug) {
        $term = get_term_by('slug', $slug, 'favorites');
        if ($term) {
            if ($hide_empty && $term->count < 1) {
                continue;
            }
            $terms[] = $term;
        }
    }
    if (empty($terms)) {
        $terms = get_terms(array(
            'taxonomy' => 'favorites',
            'hide_empty' => $hide_empty,
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => 12,
        ));
        if (is_wp_error($terms)) {
            $terms = array();
        }
    }
    return $terms;
}

function theme_get_collection_url($term_slug) {
    return home_url('/favorites/' . $term_slug);
}

/**
 * 分类对应内容类型：社区资讯=资讯文章+网址、软件游戏=下载软件+网址、书籍期刊=书籍+网址，其余分类=网址
 */
function theme_collection_post_types($slug) {
    $map = array(
        'zixun'    => array('post', 'sites'),
        'ruanjian' => array('app', 'sites'),
        'qikan'    => array('book', 'sites'),
    );
    return isset($map[$slug]) ? $map[$slug] : array('sites');
}

/**
 * 输出条目所属分类标签（最多2个）
 */
function theme_render_item_terms($post_id) {
    $terms = get_the_terms($post_id, 'favorites');
    if ($terms && !is_wp_error($terms)) {
        foreach (array_slice($terms, 0, 2) as $term) {
            echo '<a href="' . esc_url(get_term_link($term)) . '" class="badge vc-l-theme text-ss mr-1" rel="tag" title="查看更多"><i class="iconfont icon-folder mr-1"></i>' . esc_html($term->name) . '</a>';
        }
    }
}

/**
 * 首页/分类页统一条目渲染：按内容类型输出 网址 / 资讯文章(图文) / 下载软件 / 书籍 卡片
 * 需在主循环（the_post 已设置）中调用
 */
function theme_render_collection_item($post_type = 'sites', $idx = 0) {
    $post_id   = get_the_ID();
    $permalink = get_permalink($post_id);
    $title     = get_the_title($post_id);
    $thumb     = has_post_thumbnail($post_id)
        ? get_the_post_thumbnail_url($post_id, 'thumbnail')
        : 'https://ui-avatars.com/api/?name=' . urlencode($title) . '&background=random&color=fff';
    $terms_html = function() use ($post_id) {
        ob_start();
        theme_render_item_terms($post_id);
        return ob_get_clean();
    };

    if ($post_type === 'post') {
        // 资讯图文卡片
        ?>
        <article class="posts-item post-item d-flex style-post-min post-<?php echo (int) $post_id; ?>">
            <div class="item-header">
                <div class="item-media">
                    <a class="item-image" href="<?php echo esc_url($permalink); ?>">
                        <img class="fill-cover lazy unfancybox" src="<?php echo esc_url($thumb); ?>" height="auto" width="auto" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
            </div>
            <div class="item-body d-flex flex-column flex-fill">
                <h3 class="item-title line2">
                    <a href="<?php echo esc_url($permalink); ?>" title="<?php echo esc_attr($title); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <div class="mt-auto">
                    <div class="line1 text-muted text-sm d-none d-md-block"><?php echo esc_html(wp_trim_words(get_the_content(), 24)); ?></div>
                    <div class="item-tags overflow-x-auto no-scrollbar"><?php echo $terms_html(); ?></div>
                </div>
            </div>
        </article>
        <?php
    } elseif ($post_type === 'app') {
        // 软件下载卡片
        $download_url = get_post_meta($post_id, 'download_url', true);
        $file_size    = get_post_meta($post_id, 'file_size', true);
        $gradients    = array(
            'linear-gradient(130deg,#ffedf1,#e86db6)',
            'linear-gradient(130deg,#e3f2fd,#42a5f5)',
            'linear-gradient(130deg,#e8f5e9,#66bb6a)',
            'linear-gradient(130deg,#fff3e0,#ffa726)',
            'linear-gradient(130deg,#f3e5f5,#ab47bc)',
        );
        $gradient = $gradients[$idx % count($gradients)];
        ?>
        <article class="posts-item app-item d-flex style-app-max post-<?php echo (int) $post_id; ?>">
            <div class="item-header">
                <div class="item-media" style="background-image:<?php echo esc_attr($gradient); ?>;">
                    <a class="item-image" href="<?php echo esc_url($permalink); ?>" style="transform:scale(81%)">
                        <img class="fill-cover lazy unfancybox" src="<?php echo esc_url($thumb); ?>" height="auto" width="auto" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
            </div>
            <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                <h3 class="item-title line1"><b><a href="<?php echo esc_url($permalink); ?>" title="<?php echo esc_attr($title); ?>"><?php echo esc_html($title); ?></a></b></h3>
                <div class="app-content mt-auto">
                    <div class="text-muted text-xs line1"><?php echo esc_html(wp_trim_words(get_the_content(), 15)); ?></div>
                    <div class="app-meta d-flex align-items-center">
                        <div class="item-tags overflow-x-auto no-scrollbar"><?php echo $terms_html(); ?></div>
                        <?php if (!empty($download_url)) : ?>
                        <a href="<?php echo esc_url($download_url); ?>" target="_blank" rel="external nofollow noopener" class="togo ml-auto text-center text-muted is-views" data-id="<?php echo (int) $post_id; ?>" data-toggle="tooltip" data-placement="right" title="下载">
                            <i class="iconfont icon-download"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($file_size)) : ?>
                    <div class="text-muted text-ss mt-1"><?php echo esc_html($file_size); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php
    } elseif ($post_type === 'book') {
        // 书籍卡片
        $book_type = get_post_meta($post_id, 'book_type', true);
        $book_desc = wp_trim_words(get_the_content(), 18) . ($book_type ? ' · ' . $book_type : '');
        ?>
        <article class="posts-item book-item d-flex style-book-v1 post-<?php echo (int) $post_id; ?>">
            <div class="item-header">
                <div class="item-media">
                    <a class="item-image" href="<?php echo esc_url($permalink); ?>">
                        <img class="fill-cover lazy unfancybox" src="<?php echo esc_url($thumb); ?>" height="auto" width="auto" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
            </div>
            <div class="item-body flex-fill">
                <h3 class="item-title line1"><a href="<?php echo esc_url($permalink); ?>" title="<?php echo esc_attr($title); ?>"><?php echo esc_html($title); ?></a></h3>
                <div class="line1 text-muted text-xs mt-1"><?php echo esc_html($book_desc); ?></div>
            </div>
        </article>
        <?php
    } else {
        // 网址卡片
        $site_url  = theme_get_site_url($post_id);
        $views     = theme_get_site_views($post_id);
        $likes     = theme_get_site_likes($post_id);
        $comments  = get_comments_number($post_id);
        ?>
        <article class="posts-item sites-item d-flex style-sites-max post-<?php echo (int) $post_id; ?>">
            <a href="<?php echo esc_url($permalink); ?>" data-id="<?php echo (int) $post_id; ?>" data-url="<?php echo esc_url($site_url); ?>" class="sites-body" title="<?php echo esc_attr($title); ?>">
                <div class="item-header">
                    <div class="item-media">
                        <div class="blur-img-bg lazy-bg"></div>
                        <div class="item-image">
                            <img class="fill-cover sites-icon lazy unfancybox" src="<?php echo esc_url($thumb); ?>" height="auto" width="auto" alt="<?php echo esc_attr($title); ?>">
                        </div>
                    </div>
                </div>
                <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                    <h3 class="item-title line1"><b><?php echo esc_html($title); ?></b></h3>
                    <div class="line1 text-muted text-xs"><?php echo esc_html(wp_trim_words(get_the_content(), 15)); ?></div>
                </div>
            </a>
            <div class="meta-ico text-muted text-xs">
                <span class="meta-comm d-none d-md-inline-block" data-toggle="tooltip" title="去评论"><i class="iconfont icon-comment"></i><?php echo (int) $comments; ?></span>
                <span class="meta-view"><i class="iconfont icon-chakan-line"></i><?php echo (int) $views; ?></span>
                <span class="meta-like d-none d-md-inline-block"><i class="iconfont icon-like-line"></i><?php echo (int) $likes; ?></span>
            </div>
            <div class="sites-tags">
                <div class="item-tags overflow-x-auto no-scrollbar"><?php echo $terms_html(); ?></div>
                <?php if (!empty($site_url)) : ?>
                <a href="<?php echo esc_url($site_url); ?>" target="_blank" rel="external nofollow noopener" class="togo ml-auto text-center text-muted is-views" data-id="<?php echo (int) $post_id; ?>" data-toggle="tooltip" data-placement="right" title="直达">
                    <i class="iconfont icon-goto"></i>
                </a>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}

function theme_get_category_icon($slug) {
    $icon_map = array(
        'changyong' => 'io io-fuwu',
        'changyongtuijian' => 'io io-fuwu',
        'tuijian' => 'io io-fuwu',
        'fuwu' => 'io io-fuwu',
        'wangpan' => 'iconfont icon-en',
        'wangpanyunchu' => 'iconfont icon-en',
        'yunchu' => 'iconfont icon-en',
        'yuncunchu' => 'iconfont icon-en',
        'zixun' => 'io io-zixun',
        'shequzixun' => 'io io-zixun',
        'shequ' => 'io io-zixun',
        'xinwenzixun' => 'io io-zixun',
        'news' => 'io io-zixun',
        'qikan' => 'io io-book',
        'shujifukan' => 'io io-book',
        'shuju' => 'io io-book',
        'book' => 'io io-book',
        'ruanjian' => 'io io-app',
        'ruanjianyouxi' => 'io io-app',
        'youxi' => 'io io-yanshi',
        'yanshi' => 'io io-yanshi',
        'yonghuyanshi' => 'io io-yanshi',
        'gongju' => 'io io-gongju',
        'changyonggongju' => 'io io-gongju',
        'tools' => 'io io-gongju',
        'sucai' => 'io io-sucai1-copy',
        'sucai Ziyuan' => 'io io-sucai1-copy',
        'sucai Ziyuan' => 'io io-sucai1-copy',
        'shejiziyuan' => 'io io-sucai1-copy',
        'design' => 'io io-sucai1-copy',
        'tuandui' => 'io io-tuandui',
        'uedtuandui' => 'io io-tuandui',
        'ued' => 'io io-tuandui',
        'friendlink' => 'io io-links',
        'youqinglianjie' => 'io io-links',
        'shejiaomeiti' => 'io io-fuwu',
        'dianshangpingtai' => 'io io-fuwu',
        'xuexijiaocheng' => 'io io-book',
        'bangongxiaolv' => 'io io-gongju',
        'yulexiuxian' => 'io io-app',
        'yunfuwu' => 'iconfont icon-en',
        'kaifagongju' => 'io io-gongju',
    );
    return isset($icon_map[$slug]) ? $icon_map[$slug] : 'io io-fuwu';
}

function theme_get_recent_sites($count = 12) {
    $args = array(
        'post_type' => 'sites',
        'posts_per_page' => $count,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    return new WP_Query($args);
}

function theme_get_top_sites($count = 6) {
    $args = array(
        'post_type' => 'sites',
        'posts_per_page' => $count,
        'orderby' => 'meta_value_num',
        'meta_key' => 'site_views',
        'order' => 'DESC',
    );
    return new WP_Query($args);
}

function theme_get_hot_posts($count = 6) {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $count,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    return new WP_Query($args);
}

function theme_get_total_sites() {
    $count = wp_count_posts('sites');
    return $count->publish;
}

function theme_get_total_posts() {
    $count = wp_count_posts('post');
    return $count->publish;
}

function theme_get_total_apps() {
    $count = wp_count_posts('app');
    return $count->publish;
}

function theme_get_total_books() {
    $count = wp_count_posts('book');
    return $count->publish;
}

function theme_increment_views() {
    if (is_admin() || !is_singular()) {
        return;
    }
    $post_id = get_queried_object_id();
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, array('sites', 'post', 'app', 'book'), true)) {
        return;
    }
    if (get_post_status($post_id) !== 'publish') {
        return;
    }
    // 同一访客 30 分钟内重复打开不重复计数
    $cookie_key = 'theme_vid_' . $post_id;
    if (isset($_COOKIE[$cookie_key])) {
        return;
    }
    if (!headers_sent()) {
        setcookie($cookie_key, '1', time() + 1800, COOKIEPATH, COOKIE_DOMAIN);
    }
    // 总浏览量：网址沿用 site_views，文章/软件/书籍用 post_views
    $total_key = ($post_type === 'sites') ? 'site_views' : 'post_views';
    $views = (int) get_post_meta($post_id, $total_key, true);
    update_post_meta($post_id, $total_key, $views + 1);
    // 每日浏览量日志（保留 60 天），供排行榜日/周/月榜使用
    $log = get_post_meta($post_id, '_theme_daily_views', true);
    if (!is_array($log)) {
        $log = array();
    }
    $today = current_time('Y-m-d');
    $log[$today] = isset($log[$today]) ? (int) $log[$today] + 1 : 1;
    $cutoff = date('Y-m-d', strtotime($today . ' -60 days'));
    foreach ($log as $d => $c) {
        if ($d < $cutoff) {
            unset($log[$d]);
        }
    }
    update_post_meta($post_id, '_theme_daily_views', $log);
}
add_action('template_redirect', 'theme_increment_views');

function theme_increment_likes() {
    if (isset($_POST['post_id'])) {
        $post_id = intval($_POST['post_id']);
        $likes = get_post_meta($post_id, 'site_likes', true);
        update_post_meta($post_id, 'site_likes', ($likes ? $likes : 0) + 1);
        echo get_post_meta($post_id, 'site_likes', true);
    }
    wp_die();
}
add_action('wp_ajax_increment_likes', 'theme_increment_likes');
add_action('wp_ajax_nopriv_increment_likes', 'theme_increment_likes');

function theme_add_favorite() {
    if (isset($_POST['post_id'])) {
        $post_id = intval($_POST['post_id']);
        $favorites = get_post_meta($post_id, 'site_favorites', true);
        update_post_meta($post_id, 'site_favorites', ($favorites ? $favorites : 0) + 1);
        echo get_post_meta($post_id, 'site_favorites', true);
    }
    wp_die();
}
add_action('wp_ajax_add_favorite', 'theme_add_favorite');
add_action('wp_ajax_nopriv_add_favorite', 'theme_add_favorite');

function theme_settings_init() {
    register_setting('theme_settings', 'theme_settings', array(
        'sanitize_callback' => 'theme_settings_sanitize',
    ));
    
    add_settings_section(
        'theme_settings_general',
        __('基本设置', '115theme'),
        'theme_settings_general_callback',
        'theme_settings'
    );
    
    add_settings_section(
        'theme_settings_logo',
        __('LOGO设置', '115theme'),
        'theme_logo_section_callback',
        'theme_settings'
    );

    add_settings_field(
        'theme_logo',
        __('LOGO图片（日间）', '115theme'),
        'theme_logo_callback',
        'theme_settings',
        'theme_settings_logo'
    );

    add_settings_field(
        'theme_logo_night',
        __('夜间模式LOGO', '115theme'),
        'theme_logo_night_callback',
        'theme_settings',
        'theme_settings_logo'
    );

    add_settings_field(
        'theme_logo_switch',
        __('双LOGO切换', '115theme'),
        'theme_logo_switch_callback',
        'theme_settings',
        'theme_settings_logo'
    );

    add_settings_field(
        'theme_about_cover',
        __('关于本站封面图', '115theme'),
        'theme_about_cover_callback',
        'theme_settings',
        'theme_settings_logo'
    );

    add_settings_section(
        'theme_settings_modules',
        __('功能模块', '115theme'),
        'theme_modules_section_callback',
        'theme_settings'
    );

    add_settings_field(
        'theme_world_clock',
        __('世界时钟', '115theme'),
        'theme_world_clock_callback',
        'theme_settings',
        'theme_settings_modules'
    );

    add_settings_field(
        'theme_currency',
        __('实时汇率', '115theme'),
        'theme_currency_callback',
        'theme_settings',
        'theme_settings_modules'
    );

    add_settings_field(
        'theme_hotlist',
        __('热门榜单', '115theme'),
        'theme_hotlist_callback',
        'theme_settings',
        'theme_settings_modules'
    );

    add_settings_field(
        'theme_hotposts',
        __('热门文章', '115theme'),
        'theme_hotposts_callback',
        'theme_settings',
        'theme_settings_modules'
    );

    add_settings_field(
        'theme_social',
        __('社交链接', '115theme'),
        'theme_social_callback',
        'theme_settings',
        'theme_settings_modules'
    );

    add_settings_field(
        'theme_lang_switch',
        __('多国语言切换', '115theme'),
        'theme_lang_switch_callback',
        'theme_settings',
        'theme_settings_modules'
    );

    add_settings_section(
        'theme_settings_sites',
        __('导航详情页', '115theme'),
        'theme_sites_section_callback',
        'theme_settings'
    );

    add_settings_field('theme_sites_like', __('点赞功能', '115theme'), 'theme_sites_checkbox_callback', 'theme_settings', 'theme_settings_sites', array('key' => 'theme_sites_like', 'label' => '启用点赞按钮（AJAX交互）'));
    add_settings_field('theme_sites_favorite', __('收藏功能', '115theme'), 'theme_sites_checkbox_callback', 'theme_settings', 'theme_settings_sites', array('key' => 'theme_sites_favorite', 'label' => '启用收藏按钮（AJAX交互）'));
    add_settings_field('theme_sites_seo', __('SEO权重', '115theme'), 'theme_sites_checkbox_callback', 'theme_settings', 'theme_settings_sites', array('key' => 'theme_sites_seo', 'label' => '显示SEO权重（异步加载5118/爱站/Chinaz数据）'));
    add_settings_field('theme_sites_chart', __('数据图表', '115theme'), 'theme_sites_checkbox_callback', 'theme_settings', 'theme_settings_sites', array('key' => 'theme_sites_chart', 'label' => '显示访问趋势图表'));
    add_settings_field('theme_sites_qr', __('二维码', '115theme'), 'theme_sites_checkbox_callback', 'theme_settings', 'theme_settings_sites', array('key' => 'theme_sites_qr', 'label' => '显示"手机查看"二维码按钮'));
    add_settings_field('theme_sites_report', __('反馈功能', '115theme'), 'theme_sites_checkbox_callback', 'theme_settings', 'theme_settings_sites', array('key' => 'theme_sites_report', 'label' => '显示"反馈"按钮（弹窗提交）'));
    add_settings_field('theme_screenshot_url', __('截图服务', '115theme'), 'theme_sites_text_callback', 'theme_settings', 'theme_settings_sites', array('key' => 'theme_screenshot_url', 'desc' => '截图API地址，{domain}会被替换为域名'));
    add_settings_field('theme_favicon_url', __('Favicon服务', '115theme'), 'theme_sites_text_callback', 'theme_settings', 'theme_settings_sites', array('key' => 'theme_favicon_url', 'desc' => 'Favicon API地址，{domain}会被替换为域名。启用本地化后，图标会自动下载保存到 uploads/websiteico/ 目录'));
    add_settings_field('theme_favicon_localize', __('图标本地化', '115theme'), 'theme_favicon_localize_callback', 'theme_settings', 'theme_settings_sites');
    add_settings_field('theme_qr_url', __('二维码服务', '115theme'), 'theme_sites_text_callback', 'theme_settings', 'theme_settings_sites', array('key' => 'theme_qr_url', 'desc' => '二维码API地址，{url}会被替换为目标网址'));

    add_settings_section(
        'theme_settings_ad',
        __('入驻广告', '115theme'),
        'theme_ad_section_callback',
        'theme_settings'
    );
    add_settings_field('theme_ad_enable', __('入驻开关', '115theme'), 'theme_sites_checkbox_callback', 'theme_settings', 'theme_settings_ad', array('key' => 'theme_ad_enable', 'label' => '开启"立即入驻"付费广告功能（首页热门区展示付费广告位）'));
    add_settings_field('theme_ad_price_week', __('周付套餐（7天）', '115theme'), 'theme_ad_price_callback', 'theme_settings', 'theme_settings_ad', array('key' => 'theme_ad_price_week', 'default' => '68'));
    add_settings_field('theme_ad_price_month', __('月付套餐（30天，推荐）', '115theme'), 'theme_ad_price_callback', 'theme_settings', 'theme_settings_ad', array('key' => 'theme_ad_price_month', 'default' => '198'));
    add_settings_field('theme_ad_price_quarter', __('季付套餐（90天）', '115theme'), 'theme_ad_price_callback', 'theme_settings', 'theme_settings_ad', array('key' => 'theme_ad_price_quarter', 'default' => '498'));
    add_settings_field('theme_ad_price_halfyear', __('半年付套餐（180天，特惠）', '115theme'), 'theme_ad_price_callback', 'theme_settings', 'theme_settings_ad', array('key' => 'theme_ad_price_halfyear', 'default' => '888'));
    add_settings_field('theme_ad_price_custom', __('自定义时长单价', '115theme'), 'theme_ad_price_callback', 'theme_settings', 'theme_settings_ad', array('key' => 'theme_ad_price_custom', 'default' => '0.8', 'suffix' => '元/小时'));
    add_settings_field('theme_ad_hours_min', __('自定义最短时长', '115theme'), 'theme_ad_price_callback', 'theme_settings', 'theme_settings_ad', array('key' => 'theme_ad_hours_min', 'default' => '1', 'suffix' => '小时'));
    add_settings_field('theme_ad_hours_max', __('自定义最长时长', '115theme'), 'theme_ad_price_callback', 'theme_settings', 'theme_settings_ad', array('key' => 'theme_ad_hours_max', 'default' => '240', 'suffix' => '小时'));
    add_settings_field('theme_ad_wechat_qr', __('微信收款码', '115theme'), 'theme_ad_qr_callback', 'theme_settings', 'theme_settings_ad', array('key' => 'theme_ad_wechat_qr', 'label' => '微信收款二维码'));
    add_settings_field('theme_ad_alipay_qr', __('支付宝收款码', '115theme'), 'theme_ad_qr_callback', 'theme_settings', 'theme_settings_ad', array('key' => 'theme_ad_alipay_qr', 'label' => '支付宝收款二维码'));
    add_settings_field('theme_ad_notice', __('收款说明', '115theme'), 'theme_ad_notice_callback', 'theme_settings', 'theme_settings_ad');

    add_settings_section(
        'theme_settings_tools',
        __('悬浮工具与AI助手', '115theme'),
        'theme_tools_section_callback',
        'theme_settings'
    );
    add_settings_field('theme_qq_service', __('QQ客服链接', '115theme'), 'theme_sites_text_callback', 'theme_settings', 'theme_settings_tools', array('key' => 'theme_qq_service', 'desc' => '填写后右侧工具条显示QQ客服按钮，如 https://wpa.qq.com/msgrd?v=3&uin=123456&site=qq&menu=yes，留空不显示'));
    add_settings_field('theme_weather', __('天气组件', '115theme'), 'theme_weather_field_callback', 'theme_settings', 'theme_settings_tools');
    add_settings_field('theme_ai', __('AI助手', '115theme'), 'theme_ai_field_callback', 'theme_settings', 'theme_settings_tools');
}
add_action('admin_init', 'theme_settings_init');

function theme_settings_sanitize($input) {
    if (is_array($input)) {
        $checkboxes = array('theme_logo_switch', 'theme_world_clock_on', 'theme_currency_on', 'theme_hotlist_on', 'theme_hotposts_on', 'theme_lang_switch', 'theme_sites_like', 'theme_sites_favorite', 'theme_sites_seo', 'theme_sites_chart', 'theme_sites_qr', 'theme_sites_report', 'theme_ad_enable', 'theme_weather_on', 'theme_ai_on');
        foreach ($checkboxes as $cb) {
            $input[$cb] = isset($input[$cb]) ? 1 : 0;
        }
        if (isset($input['theme_lang_switch_list'])) {
            $input['theme_lang_switch_list'] = sanitize_textarea_field($input['theme_lang_switch_list']);
        }
        $text_fields = array('theme_qq_service', 'theme_weather_token', 'theme_ai_api_url', 'theme_ai_model');
        foreach ($text_fields as $tf) {
            if (isset($input[$tf])) {
                $input[$tf] = sanitize_text_field($input[$tf]);
            }
        }
        if (isset($input['theme_ai_api_key'])) {
            $input['theme_ai_api_key'] = trim($input['theme_ai_api_key']);
        }
    }
    return $input;
}

function theme_tools_section_callback() {
    echo '<p>' . __('配置右侧悬浮工具条上的QQ客服、天气组件与AI助手', '115theme') . '</p>';
}

function theme_weather_field_callback() {
    $options = get_option('theme_settings');
    $on = !empty($options['theme_weather_on']);
    $token = $options['theme_weather_token'] ?? 'faeb43c3-de83-4ce0-ac30-c997d962d388';
    ?>
    <label><input type="checkbox" name="theme_settings[theme_weather_on]" value="1" <?php checked($on); ?>> 启用右侧悬浮天气组件（心知天气免费小部件）</label>
    <p class="description">开启后工具条显示天气按钮，悬浮可查看天气详情。</p>
    <input type="text" class="regular-text" name="theme_settings[theme_weather_token]" value="<?php echo esc_attr($token); ?>" placeholder="心知天气 Widget Token">
    <p class="description">Widget Token，默认为官方演示 Token，建议前往 seniverse.com 免费申请替换。</p>
    <?php
}

function theme_ai_field_callback() {
    $options = get_option('theme_settings');
    $on = !empty($options['theme_ai_on']);
    $api_url = $options['theme_ai_api_url'] ?? '';
    $api_key = $options['theme_ai_api_key'] ?? '';
    $model = $options['theme_ai_model'] ?? 'gpt-4o-mini';
    ?>
    <label><input type="checkbox" name="theme_settings[theme_ai_on]" value="1" <?php checked($on); ?>> 启用右下角AI助手悬浮球与对话面板</label>
    <p class="description">兼容 OpenAI Chat Completions 接口（/v1/chat/completions），可对接 OpenAI、DeepSeek、通义千问、智谱等兼容服务。</p>
    <input type="text" class="regular-text" name="theme_settings[theme_ai_api_url]" value="<?php echo esc_attr($api_url); ?>" placeholder="https://api.openai.com/v1/chat/completions">
    <p class="description">接口完整地址</p>
    <input type="text" class="regular-text" name="theme_settings[theme_ai_api_key]" value="<?php echo esc_attr($api_key); ?>" placeholder="API Key（仅保存在本站服务端）">
    <p class="description">API 密钥</p>
    <input type="text" class="regular-text" name="theme_settings[theme_ai_model]" value="<?php echo esc_attr($model); ?>" placeholder="gpt-4o-mini">
    <p class="description">模型名称，如 gpt-4o-mini、deepseek-chat、qwen-plus</p>
    <?php
}

function theme_settings_general_callback() {
    echo '<p>' . __('设置网站的基本信息', '115theme') . '</p>';
}

function theme_logo_section_callback() {
    echo '<p>' . __('上传日间、夜间两张LOGO图片，开启"双LOGO切换"后，前台会随日间/夜间模式自动切换对应的LOGO', '115theme') . '</p>';
}

function theme_logo_field_html($key, $value, $desc = '') {
    $preview_display = empty($value) ? 'none' : 'block';
    ?>
    <div class="theme-logo-field">
        <img class="theme-logo-preview" src="<?php echo esc_url($value); ?>" alt="LOGO预览" style="max-height:50px;display:<?php echo esc_attr($preview_display); ?>;margin-bottom:8px;">
        <br>
        <input type="text" class="regular-text theme-logo-url" name="theme_settings[<?php echo esc_attr($key); ?>]" id="<?php echo esc_attr($key); ?>_url" value="<?php echo esc_attr($value); ?>" placeholder="输入LOGO图片URL">
        <button type="button" class="button theme-logo-upload" data-target="<?php echo esc_attr($key); ?>_url"><?php _e('上传LOGO', '115theme'); ?></button>
        <button type="button" class="button theme-logo-clear" data-target="<?php echo esc_attr($key); ?>_url"><?php _e('清除', '115theme'); ?></button>
        <?php if ($desc) : ?><p class="description"><?php echo $desc; ?></p><?php endif; ?>
    </div>
    <?php
}

function theme_logo_callback() {
    $options = get_option('theme_settings');
    $default_logo = '';
    $logo_value = $options['theme_logo'] ?? $default_logo;
    theme_logo_field_html('theme_logo', $logo_value, '日间模式显示，建议使用彩色图标+深色文字的透明PNG，尺寸100x50px左右');
    ?>
    <script>
    jQuery(document).ready(function($) {
        var logoFrame;
        $(document).on('click', '.theme-logo-upload', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            if (!logoFrame) {
                logoFrame = wp.media({
                    title: '<?php esc_attr_e('选择LOGO图片', '115theme'); ?>',
                    button: { text: '<?php esc_attr_e('使用这张图片', '115theme'); ?>' },
                    multiple: false,
                    library: { type: 'image' }
                });
            }
            logoFrame.off('select').on('select', function() {
                var attachment = logoFrame.state().get('selection').first().toJSON();
                $('#' + target).val(attachment.url);
                $('#' + target).closest('.theme-logo-field').find('.theme-logo-preview').attr('src', attachment.url).show();
            });
            logoFrame.open();
        });
        $(document).on('click', '.theme-logo-clear', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target).val('');
            $('#' + target).closest('.theme-logo-field').find('.theme-logo-preview').hide();
        });
        $(document).on('change', '.theme-logo-url', function() {
            var url = $(this).val();
            var $preview = $(this).closest('.theme-logo-field').find('.theme-logo-preview');
            if (url) {
                $preview.attr('src', url).show();
            } else {
                $preview.hide();
            }
        });
    });
    </script>
    <?php
}

function theme_logo_night_callback() {
    $options = get_option('theme_settings');
    $night_value = $options['theme_logo_night'] ?? '';
    theme_logo_field_html('theme_logo_night', $night_value, '夜间模式显示，建议使用彩色图标+白色文字的透明PNG；留空则两种模式共用日间LOGO');
}

function theme_logo_switch_callback() {
    $options = get_option('theme_settings');
    $checked = $options['theme_logo_switch'] ?? 1;
    echo '<label><input type="checkbox" name="theme_settings[theme_logo_switch]" value="1" ' . checked($checked, 1, false) . '> 启用日间/夜间双LOGO自动切换</label>';
    echo '<p class="description">开启：日间显示"LOGO图片（日间）"，夜间显示"夜间模式LOGO"（需已上传夜间LOGO）；关闭：两种模式均显示日间LOGO</p>';
}

function theme_about_cover_callback() {
    $options = get_option('theme_settings');
    $default_cover = 'https://cdn2.iocdn.cc/gh/owen0o0/ioStaticResources@master/banner/wHoOcfQGhqvlUkd.jpg';
    $cover_value = isset($options['theme_about_cover']) ? $options['theme_about_cover'] : $default_cover;
    theme_logo_field_html('theme_about_cover', $cover_value, '首页右侧"关于本站"卡片顶部封面背景图，建议尺寸 600x200 以上横图；留空（清空输入框）则使用主题色渐变背景；不填写默认使用内置封面图');
}

function theme_settings_page() {
    if (isset($_POST['import_demo_data']) && current_user_can('manage_options')) {
        theme_create_default_collections();
        theme_create_demo_sites();
        theme_create_demo_posts();
        theme_create_demo_apps();
        echo '<div class="updated"><p>演示数据已导入！（网址、文章、软件各一批，仅添加缺失项，不影响已有内容）</p></div>';
    }
    if (isset($_POST['clear_demo_data']) && current_user_can('manage_options')) {
        theme_clear_demo_data();
        echo '<div class="updated"><p>所有演示数据已清空！</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php _e('主题设置', '115theme'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('theme_settings');
            do_settings_sections('theme_settings');
            submit_button();
            ?>
        </form>
        <h2 style="margin-top: 30px;">演示数据</h2>
        <form method="post" style="display:inline-block; margin-right:10px;">
            <p>一键导入主流网站分类与演示站点（仅添加缺失项，不影响已有内容）：</p>
            <?php submit_button('一键导入演示数据', 'primary', 'import_demo_data', false); ?>
        </form>
        <form method="post" style="display:inline-block;" onsubmit="return confirm('确定要清空所有站点和分类吗？此操作不可恢复！');">
            <p>清空所有站点和分类（不可恢复）：</p>
            <?php submit_button('清空所有演示数据', 'secondary', 'clear_demo_data', false, array('style' => 'background:#d63638;color:#fff;border-color:#d63638;')); ?>
        </form>
    </div>
    <?php
}

function theme_add_settings_menu() {
    add_menu_page(
        __('主题设置', '115theme'),
        __('主题设置', '115theme'),
        'manage_options',
        'theme_settings',
        'theme_settings_page',
        'dashicons-admin-settings',
        6
    );
    add_submenu_page(
        'theme_settings',
        __('主题更新', '115theme'),
        __('主题更新', '115theme'),
        'manage_options',
        'theme_update_check',
        'theme_update_check_page'
    );
    add_submenu_page(
        'theme_settings',
        __('友链管理', '115theme'),
        __('友链管理', '115theme'),
        'manage_options',
        'theme_friendlinks',
        'theme_friendlinks_page'
    );
    $pending_orders = 0;
    $ad_orders = get_option('theme_ad_orders', array());
    foreach ($ad_orders as $o) {
        if (($o['status'] ?? '') === 'pending') $pending_orders++;
    }
    $orders_title = __('入驻订单', '115theme') . ($pending_orders > 0 ? ' <span class="awaiting-mod">' . $pending_orders . '</span>' : '');
    add_submenu_page(
        'theme_settings',
        __('入驻订单', '115theme'),
        $orders_title,
        'manage_options',
        'theme_ad_orders',
        'theme_ad_orders_page'
    );
}
add_action('admin_menu', 'theme_add_settings_menu');

function theme_admin_enqueue_media($hook) {
    if ('toplevel_page_theme_settings' === $hook) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'theme_admin_enqueue_media');

function theme_friendlinks_page() {
    if (isset($_POST['add_friendlink']) && current_user_can('manage_options')) {
        $friendlinks = get_option('theme_friendlinks', array());
        $friendlinks[] = array(
            'name' => sanitize_text_field($_POST['fl_name']),
            'url' => sanitize_url($_POST['fl_url']),
            'description' => sanitize_text_field($_POST['fl_description']),
        );
        update_option('theme_friendlinks', $friendlinks);
        echo '<div class="updated"><p>友链添加成功！</p></div>';
    }
    if (isset($_POST['init_friendlinks']) && current_user_can('manage_options')) {
        theme_create_default_friendlinks();
        echo '<div class="updated"><p>默认友链初始化成功！</p></div>';
    }
    if (isset($_POST['edit_friendlink']) && current_user_can('manage_options')) {
        $friendlinks = get_option('theme_friendlinks', array());
        $index = intval($_POST['fl_index']);
        if (isset($friendlinks[$index])) {
            $friendlinks[$index] = array(
                'name' => sanitize_text_field($_POST['fl_name']),
                'url' => sanitize_url($_POST['fl_url']),
                'description' => sanitize_text_field($_POST['fl_description']),
            );
            update_option('theme_friendlinks', $friendlinks);
            echo '<div class="updated"><p>友链修改成功！</p></div>';
        }
    }
    if (isset($_GET['delete']) && current_user_can('manage_options')) {
        $friendlinks = get_option('theme_friendlinks', array());
        $index = intval($_GET['delete']);
        if (isset($friendlinks[$index])) {
            array_splice($friendlinks, $index, 1);
            update_option('theme_friendlinks', $friendlinks);
            echo '<div class="updated"><p>友链删除成功！</p></div>';
        }
    }
    $friendlinks = get_option('theme_friendlinks', array());
    $edit_index = isset($_GET['edit']) ? intval($_GET['edit']) : -1;
    $edit_link = $edit_index >= 0 && isset($friendlinks[$edit_index]) ? $friendlinks[$edit_index] : null;
    ?>
    <div class="wrap">
        <h1><?php _e('友链管理', '115theme'); ?></h1>
        
        <h2><?php echo $edit_link ? '编辑友链' : '添加友链'; ?></h2>
        <form method="post">
            <?php if ($edit_link) : ?>
            <input type="hidden" name="fl_index" value="<?php echo $edit_index; ?>">
            <?php endif; ?>
            <table class="form-table">
                <tr>
                    <th><label for="fl_name">网站名称</label></th>
                    <td><input type="text" id="fl_name" name="fl_name" class="regular-text" required value="<?php echo $edit_link ? esc_attr($edit_link['name']) : ''; ?>"></td>
                </tr>
                <tr>
                    <th><label for="fl_url">网站链接</label></th>
                    <td><input type="url" id="fl_url" name="fl_url" class="regular-text" required placeholder="https://" value="<?php echo $edit_link ? esc_attr($edit_link['url']) : ''; ?>"></td>
                </tr>
                <tr>
                    <th><label for="fl_description">网站描述</label></th>
                    <td><input type="text" id="fl_description" name="fl_description" class="regular-text" value="<?php echo $edit_link ? esc_attr($edit_link['description']) : ''; ?>"></td>
                </tr>
            </table>
            <input type="submit" name="<?php echo $edit_link ? 'edit_friendlink' : 'add_friendlink'; ?>" class="button button-primary" value="<?php echo $edit_link ? '保存修改' : '添加友链'; ?>">
            <?php if ($edit_link) : ?>
            <a href="?page=theme_friendlinks" class="button">取消编辑</a>
            <?php endif; ?>
        </form>
        
        <h2 style="margin-top: 30px;">友链列表</h2>
        <?php if (empty($friendlinks)) : ?>
            <p>暂无友链，添加一个吧！</p>
            <form method="post">
                <input type="submit" name="init_friendlinks" class="button" value="初始化默认友链">
            </form>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>网站名称</th>
                        <th>网站链接</th>
                        <th>描述</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($friendlinks as $index => $link) : ?>
                        <tr>
                            <td><?php echo esc_html($link['name']); ?></td>
                            <td><a href="<?php echo esc_url($link['url']); ?>" target="_blank"><?php echo esc_url($link['url']); ?></a></td>
                            <td><?php echo esc_html($link['description']); ?></td>
                            <td>
                                <a href="?page=theme_friendlinks&edit=<?php echo $index; ?>" class="edit-link">编辑</a> | 
                                <a href="?page=theme_friendlinks&delete=<?php echo $index; ?>" class="delete-link">删除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

function theme_add_meta_boxes() {
    add_meta_box('theme_site_meta', __('网站信息', '115theme'), 'theme_site_meta_box_callback', 'sites', 'normal', 'high');
    add_meta_box('theme_app_meta', __('应用信息', '115theme'), 'theme_app_meta_box_callback', 'app', 'normal', 'high');
    add_meta_box('theme_book_meta', __('书籍信息', '115theme'), 'theme_book_meta_box_callback', 'book', 'normal', 'high');
}
add_action('add_meta_boxes', 'theme_add_meta_boxes');

function theme_site_meta_box_callback($post) {
    wp_nonce_field('theme_site_meta_nonce', 'theme_site_meta_nonce');
    $site_url = get_post_meta($post->ID, 'site_url', true);
    ?>
    <p>
        <label for="site_url"><?php _e('网站链接', '115theme'); ?></label>
        <input type="url" id="site_url" name="site_url" value="<?php echo esc_attr($site_url); ?>" class="regular-text">
    </p>
    <p>
        <button type="button" class="button button-secondary" id="theme-refresh-favicon-btn" data-post-id="<?php echo (int) $post->ID; ?>">
            <span class="dashicons dashicons-image-rotate" style="vertical-align:middle"></span> 刷新网站图标（本地化）
        </button>
        <span id="theme-fav-status" class="text-muted"></span>
    </p>
    <script>
    jQuery(function($){
        $('#theme-refresh-favicon-btn').on('click', function(){
            var $btn = $(this), pid = $btn.data('post-id'), $s = $('#theme-fav-status');
            $btn.prop('disabled', true); $s.text('正在下载…');
            $.post(ajaxurl, {
                action: 'theme_refresh_favicons',
                nonce: '<?php echo wp_create_nonce('theme_ajax_nonce'); ?>',
                post_id: pid
            }, function(res){
                $btn.prop('disabled', false);
                if (res && res.success) {
                    if (res.data.local) {
                        $s.html('✓ ' + res.data.msg + ' <a href="' + res.data.url + '" target="_blank">' + res.data.url + '</a>');
                    } else {
                        $s.css('color','#b32d2e').text('✗ ' + (res.data.msg || '刷新失败'));
                    }
                }
                else { $s.css('color','#b32d2e').text('✗ ' + (res.data && res.data.msg ? res.data.msg : '刷新失败')); }
            }, 'json').fail(function(xhr){
                $btn.prop('disabled', false);
                $s.css('color','#b32d2e').text('✗ 请求失败（HTTP ' + (xhr && xhr.status ? xhr.status : '0') + '），请刷新后台页面后重试');
            });
        });
    });
    </script>
    <?php
}

function theme_app_meta_box_callback($post) {
    wp_nonce_field('theme_app_meta_nonce', 'theme_app_meta_nonce');
    $download_url = get_post_meta($post->ID, 'download_url', true);
    $file_size = get_post_meta($post->ID, 'file_size', true);
    $official_url = get_post_meta($post->ID, 'app_official_url', true);
    $down_pc = get_post_meta($post->ID, 'app_down_pc', true);
    $down_mac = get_post_meta($post->ID, 'app_down_mac', true);
    $down_android = get_post_meta($post->ID, 'app_down_android', true);
    $down_ios = get_post_meta($post->ID, 'app_down_ios', true);
    $screenshot = get_post_meta($post->ID, 'app_screenshot', true);
    ?>
    <p>
        <label for="download_url"><?php _e('下载链接（默认/备用，弹窗无平台链接时使用）', '115theme'); ?></label>
        <input type="url" id="download_url" name="download_url" value="<?php echo esc_attr($download_url); ?>" class="regular-text">
    </p>
    <p>
        <label for="app_official_url"><?php _e('官方网站地址（详情页显示“去官方网站了解更多”按钮）', '115theme'); ?></label>
        <input type="url" id="app_official_url" name="app_official_url" value="<?php echo esc_attr($official_url); ?>" class="regular-text">
    </p>
    <p>
        <label><?php _e('分平台下载地址（可选，可附提取码，格式：https://xxx 或 https://xxx abcd）', '115theme'); ?></label><br>
        <input type="text" name="app_down_pc" value="<?php echo esc_attr($down_pc); ?>" placeholder="PC/Windows 下载地址" class="regular-text"><br>
        <input type="text" name="app_down_mac" value="<?php echo esc_attr($down_mac); ?>" placeholder="Mac 下载地址" class="regular-text" style="margin-top:6px"><br>
        <input type="text" name="app_down_android" value="<?php echo esc_attr($down_android); ?>" placeholder="安卓下载地址" class="regular-text" style="margin-top:6px"><br>
        <input type="text" name="app_down_ios" value="<?php echo esc_attr($down_ios); ?>" placeholder="iOS 下载地址" class="regular-text" style="margin-top:6px">
    </p>
    <p>
        <label for="file_size"><?php _e('文件大小', '115theme'); ?></label>
        <input type="text" id="file_size" name="file_size" value="<?php echo esc_attr($file_size); ?>" class="regular-text">
    </p>
    <p>
        <label for="app_screenshot"><?php _e('软件截图（多个图片地址用英文逗号分隔，留空则取正文首图）', '115theme'); ?></label>
        <textarea id="app_screenshot" name="app_screenshot" class="large-text code" rows="3"><?php echo esc_textarea($screenshot); ?></textarea>
    </p>
    <?php
}

function theme_book_meta_box_callback($post) {
    wp_nonce_field('theme_book_meta_nonce', 'theme_book_meta_nonce');
    $book_type = get_post_meta($post->ID, 'book_type', true);
    ?>
    <p>
        <label for="book_type"><?php _e('书籍类型', '115theme'); ?></label>
        <input type="text" id="book_type" name="book_type" value="<?php echo esc_attr($book_type); ?>" class="regular-text">
    </p>
    <?php
}

function theme_save_custom_fields($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    $post_type = get_post_type($post_id);
    
    if ($post_type === 'sites') {
        if (!isset($_POST['theme_site_meta_nonce']) || !wp_verify_nonce($_POST['theme_site_meta_nonce'], 'theme_site_meta_nonce')) return;
        if (isset($_POST['site_url'])) {
            $new_url = esc_url($_POST['site_url']);
            $old_url = (string) get_post_meta($post_id, 'site_url', true);
            update_post_meta($post_id, 'site_url', $new_url);
            // 网站链接变更 → 清掉旧域名图标，前台下次访问按新域名自动重新下载
            if ($new_url !== $old_url && function_exists('theme_refresh_favicon')) {
                theme_refresh_favicon($post_id);
            }
        }
    } elseif ($post_type === 'app') {
        if (!isset($_POST['theme_app_meta_nonce']) || !wp_verify_nonce($_POST['theme_app_meta_nonce'], 'theme_app_meta_nonce')) return;
        if (isset($_POST['download_url'])) update_post_meta($post_id, 'download_url', esc_url_raw($_POST['download_url']));
        if (isset($_POST['app_official_url'])) update_post_meta($post_id, 'app_official_url', esc_url_raw($_POST['app_official_url']));
        foreach (array('app_down_pc', 'app_down_mac', 'app_down_android', 'app_down_ios') as $down_key) {
            if (isset($_POST[$down_key])) update_post_meta($post_id, $down_key, sanitize_text_field(wp_unslash($_POST[$down_key])));
        }
        if (isset($_POST['file_size'])) update_post_meta($post_id, 'file_size', sanitize_text_field($_POST['file_size']));
        if (isset($_POST['app_screenshot'])) {
            $shots = array_filter(array_map('trim', explode(',', wp_unslash($_POST['app_screenshot']))));
            $shots = array_map('esc_url_raw', $shots);
            update_post_meta($post_id, 'app_screenshot', implode(',', $shots));
        }
    } elseif ($post_type === 'book') {
        if (!isset($_POST['theme_book_meta_nonce']) || !wp_verify_nonce($_POST['theme_book_meta_nonce'], 'theme_book_meta_nonce')) return;
        if (isset($_POST['book_type'])) update_post_meta($post_id, 'book_type', sanitize_text_field($_POST['book_type']));
    }
}
add_action('save_post', 'theme_save_custom_fields');

/**
 * 动态 body class（与 OneNav 一致）
 * 首页：aside-show（左侧分类栏占位）+ have-banner（banner 滚动交互）
 * 详情页：sidebar_right（右侧 310px 栏），无左侧分类栏、无 banner
 */
function theme_body_classes($classes) {
    if (is_admin()) return $classes;
    $classes[] = 'container-body';
    $classes[] = 'wp-theme-115theme';
    if (is_front_page() || is_home()) {
        $classes[] = 'aside-show';
        $classes[] = 'have-banner';
    } elseif (is_singular() || is_search()) {
        $classes[] = 'sidebar_right';
        if (is_singular()) {
            $singular_type = get_post_type();
            if ($singular_type && !in_array($singular_type, $classes, true)) {
                $classes[] = $singular_type;
            }
        }
    } else {
        $classes[] = 'aside-show';
    }
    return $classes;
}
add_filter('body_class', 'theme_body_classes');

function theme_modify_main_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if ($query->is_home()) {
            $query->set('post_type', 'sites');
        }
        // 分类归档页：社区资讯含文章、软件游戏含软件、书籍期刊含书籍
        if ($query->is_tax('favorites')) {
            $term = $query->get_queried_object();
            if ($term && !empty($term->slug)) {
                $query->set('post_type', theme_collection_post_types($term->slug));
            }
        }
        
        $orderby_raw = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
        if (!empty($orderby_raw)) {
            $meta_map = array(
                'views' => 'site_views',
                'like' => 'site_likes',
                'start' => 'site_likes',
                'comment' => 'comment_count',
                'modified' => 'modified',
                'date' => 'date',
            );
            if (isset($meta_map[$orderby_raw])) {
                if ($orderby_raw === 'date' || $orderby_raw === 'modified') {
                    $query->set('orderby', $orderby_raw);
                } elseif ($orderby_raw === 'comment') {
                    $query->set('orderby', 'comment_count');
                } else {
                    $meta_key = $meta_map[$orderby_raw];
                    $query->set('orderby', 'meta_value_num');
                    $query->set('meta_key', $meta_key);
                    $query->set('meta_query', array(
                        'relation' => 'OR',
                        array('key' => $meta_key, 'compare' => 'EXISTS'),
                        array('key' => $meta_key, 'compare' => 'NOT EXISTS'),
                    ));
                }
            }
        }
        
        $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';
        $allowed_types = array('sites', 'post', 'app', 'book');
        if (!empty($post_type) && in_array($post_type, $allowed_types)) {
            $query->set('post_type', $post_type);
        }
    }
}
add_action('pre_get_posts', 'theme_modify_main_query');

function theme_ajax_load_big_posts() {
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
    $args = array(
        'post_type' => 'sites',
        'posts_per_page' => 12,
        'paged' => $page,
        'orderby' => $orderby,
        'order' => 'DESC',
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();
        $site_url = theme_get_site_url(get_the_ID());
        ?>
        <article class="posts-item sites-item d-flex style-sites-default post-<?php the_ID(); ?> ajax-url">
            <a href="<?php the_permalink(); ?>" data-id="<?php the_ID(); ?>" data-url="<?php echo esc_url($site_url); ?>" class="sites-body" title="<?php the_title(); ?>">
                <div class="item-header">
                    <div class="item-media">
                        <div class="blur-img-bg lazy-bg"></div>
                        <div class="item-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <img class="fill-cover sites-icon lazy unfancybox" src="<?php the_post_thumbnail_url('thumbnail'); ?>" height="auto" width="auto" alt="<?php the_title(); ?>">
                            <?php else : ?>
                                <img class="fill-cover sites-icon lazy unfancybox" src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_the_title()); ?>&background=random" height="auto" width="auto" alt="<?php the_title(); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                    <h3 class="item-title line1"><b><?php the_title(); ?></b></h3>
                    <div class="line1 text-muted text-xs"><?php echo wp_trim_words(get_the_content(), 10); ?></div>
                </div>
            </a>
            <div class="sites-tags">
                <?php if (!empty($site_url)) : ?>
                    <a href="<?php echo esc_url($site_url); ?>" target="_blank" rel="external nofollow noopener" class="togo ml-auto text-center text-muted is-views" data-id="<?php the_ID(); ?>" data-toggle="tooltip" data-placement="right" title="直达">
                        <i class="iconfont icon-goto"></i>
                    </a>
                <?php endif; ?>
            </div>
        </article>
        <?php
        endwhile;
    }
    wp_die();
}
add_action('wp_ajax_load_big_posts', 'theme_ajax_load_big_posts');
add_action('wp_ajax_nopriv_load_big_posts', 'theme_ajax_load_big_posts');

class Theme_Nav_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array)$item->classes;
        $classes[] = 'menu-item';
        
        if ($depth > 0) {
            $classes[] = 'sub-menu-item';
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= '<li' . $id . $class_names . '>';
        
        $atts = array();
        $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
        $atts['href'] = !empty($item->url) ? $item->url : '';
        
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);
        
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        
        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';

        $item_output .= apply_filters('the_title', $item->title, $item->ID);
        
        if (isset($args->walker) && is_object($args->walker) && !empty($args->walker->has_children) && $depth === 0) {
            $item_output .= '<i class="iconfont icon-arrow-b"></i>';
        }
        
        $item_output .= '</a>';
        $item_output .= $args->after;
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    function start_lvl(&$output, $depth = 0, $args = array()) {
        if ($depth === 0) {
            $output .= '<ul class="sub-menu">';
        } else {
            $output .= '<ul class="aside-sub d-none">';
        }
    }
    
    function end_lvl(&$output, $depth = 0, $args = array()) {
        $output .= '</ul>';
    }
}

function theme_shortcode_sites($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
        'count' => 10,
    ), $atts);
    
    $args = array(
        'post_type' => 'sites',
        'posts_per_page' => intval($atts['count']),
    );
    
    if (!empty($atts['category'])) {
        $term = get_term_by('slug', $atts['category'], 'favorites');
        if ($term) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'favorites',
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ),
            );
        }
    }
    
    $query = new WP_Query($args);
    ob_start();
    
    if ($query->have_posts()) {
        echo '<div class="row row-col-md-3a">';
        while ($query->have_posts()) : $query->the_post();
        $site_url = theme_get_site_url(get_the_ID());
        ?>
        <article class="posts-item sites-item d-flex style-sites-default">
            <a href="<?php the_permalink(); ?>" data-id="<?php the_ID(); ?>" data-url="<?php echo esc_url($site_url); ?>" class="sites-body" title="<?php the_title(); ?>">
                <div class="item-header">
                    <div class="item-media">
                        <div class="blur-img-bg lazy-bg"></div>
                        <div class="item-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <img class="fill-cover sites-icon lazy unfancybox" src="<?php the_post_thumbnail_url('thumbnail'); ?>" height="auto" width="auto" alt="<?php the_title(); ?>">
                            <?php else : ?>
                                <img class="fill-cover sites-icon lazy unfancybox" src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_the_title()); ?>&background=random" height="auto" width="auto" alt="<?php the_title(); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                    <h3 class="item-title line1"><b><?php the_title(); ?></b></h3>
                    <div class="line1 text-muted text-xs"><?php echo wp_trim_words(get_the_content(), 10); ?></div>
                </div>
            </a>
            <div class="sites-tags">
                <?php if (!empty($site_url)) : ?>
                    <a href="<?php echo esc_url($site_url); ?>" target="_blank" rel="external nofollow noopener" class="togo ml-auto text-center text-muted is-views" data-id="<?php the_ID(); ?>" data-toggle="tooltip" data-placement="right" title="直达">
                        <i class="iconfont icon-goto"></i>
                    </a>
                <?php endif; ?>
            </div>
        </article>
        <?php
        endwhile;
        echo '</div>';
    }
    
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('sites', 'theme_shortcode_sites');

function theme_get_option($option_name) {
    $options = get_option('theme_settings');
    return isset($options[$option_name]) ? $options[$option_name] : '';
}

function theme_breadcrumb() {
    $home_link = home_url();
    $home_text = '首页';
    $separator = '<i class="iconfont icon-arrow-b"></i>';
    
    echo '<nav class="breadcrumb mb-4">';
    echo '<a href="' . esc_url($home_link) . '" class="breadcrumb-item">' . $home_text . '</a>';
    echo $separator;
    
    if (is_category() || is_tax()) {
        $term = get_queried_object();
        echo '<span class="breadcrumb-item active">' . esc_html($term->name) . '</span>';
    } elseif (is_single()) {
        $post = get_post();
        $terms = wp_get_post_terms($post->ID, 'favorites');
        if ($terms && !is_wp_error($terms)) {
            $term = $terms[0];
            echo '<a href="' . esc_url(get_term_link($term)) . '" class="breadcrumb-item">' . esc_html($term->name) . '</a>';
            echo $separator;
        }
        echo '<span class="breadcrumb-item active">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_page()) {
        echo '<span class="breadcrumb-item active">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_search()) {
        echo '<span class="breadcrumb-item active">搜索结果</span>';
    } elseif (is_archive()) {
        echo '<span class="breadcrumb-item active">' . esc_html(get_the_archive_title()) . '</span>';
    }
    
    echo '</nav>';
}

function theme_pagination() {
    global $wp_query;
    
    $total_pages = $wp_query->max_num_pages;
    
    if ($total_pages <= 1) {
        return;
    }
    
    $current_page = max(1, get_query_var('paged'));
    
    echo '<nav class="pagination-wrapper mt-4">';
    echo '<ul class="pagination justify-content-center">';
    
    if ($current_page > 1) {
        echo '<li class="page-item">';
        echo '<a class="page-link" href="' . esc_url(get_pagenum_link($current_page - 1)) . '" aria-label="上一页">';
        echo '<i class="iconfont icon-arrow-b"></i>';
        echo '</a>';
        echo '</li>';
    }
    
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } elseif ($i <= 3 || ($i > $current_page - 2 && $i < $current_page + 2) || $i > $total_pages - 2) {
            echo '<li class="page-item"><a class="page-link" href="' . esc_url(get_pagenum_link($i)) . '">' . $i . '</a></li>';
        } elseif (($i == 4 && $current_page > 5) || ($i == $total_pages - 3 && $current_page < $total_pages - 4)) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    if ($current_page < $total_pages) {
        echo '<li class="page-item">';
        echo '<a class="page-link" href="' . esc_url(get_pagenum_link($current_page + 1)) . '" aria-label="下一页">';
        echo '<i class="iconfont icon-arrow-b"></i>';
        echo '</a>';
        echo '</li>';
    }
    
    echo '</ul>';
    echo '</nav>';
}

/* ===================== 功能模块设置 ===================== */

function theme_modules_section_callback() {
    echo '<p>' . __('控制首页各功能模块的开启与内容', '115theme') . '</p>';
}

/**
 * translate.js 支持的全部语言（语言代码 => 原生名称）
 */
function theme_lang_default_list() {
    return array(
        'chinese_simplified' => '简体中文',
        'chinese_traditional' => '繁體中文',
        'english' => 'English',
        'japanese' => '日本語',
        'korean' => '한국어',
        'french' => 'Français',
        'spanish' => 'Español',
        'deutsch' => 'Deutsch',
        'portuguese' => 'Português',
        'italian' => 'Italiano',
        'russian' => 'Русский',
        'arabic' => 'العربية',
        'thai' => 'ไทย',
        'vietnamese' => 'Tiếng Việt',
        'indonesian' => 'Bahasa Indonesia',
        'malay' => 'Bahasa Melayu',
        'turkish' => 'Türkçe',
        'polish' => 'Polski',
        'dutch' => 'Nederlands',
        'swedish' => 'Svenska',
        'greek' => 'Ελληνικά',
        'czech' => 'Čeština',
        'hungarian' => 'Magyar',
        'romanian' => 'Română',
        'ukrainian' => 'Українська',
        'hebrew' => 'עברית',
        'hindi' => 'हिन्दी',
        'persian' => 'فارسی',
        'urdu' => 'اردو',
        'filipino' => 'Filipino',
        'latin' => 'Latina',
        'estonian' => 'Eesti',
        'latvian' => 'Latviešu',
        'lithuanian' => 'Lietuvių',
        'slovak' => 'Slovenčina',
        'slovenian' => 'Slovenščina',
        'bulgarian' => 'Български',
        'croatian' => 'Hrvatski',
        'serbian' => 'Српски',
        'danish' => 'Dansk',
        'finnish' => 'Suomi',
        'norwegian' => 'Norsk',
        'icelandic' => 'Íslenska',
        'irish' => 'Gaeilge',
        'maltese' => 'Malti',
        'catalan' => 'Català',
        'galician' => 'Galego',
        'basque' => 'Euskara',
        'welsh' => 'Cymraeg',
        'afrikaans' => 'Afrikaans',
        'zulu' => 'isiZulu',
        'swahili' => 'Kiswahili',
        'hausa' => 'Hausa',
        'yoruba' => 'Yorùbá',
        'igbo' => 'Igbo',
        'amharic' => 'አማርኛ',
        'bengali' => 'বাংলা',
        'tamil' => 'தமிழ்',
        'telugu' => 'తెలుగు',
        'kannada' => 'ಕನ್ನಡ',
        'malayalam' => 'മലയാളം',
        'marathi' => 'मराठी',
        'gujarati' => 'ગુજરાતી',
        'punjabi' => 'ਪੰਜਾਬੀ',
        'nepali' => 'नेपाली',
        'sinhala' => 'සිංහල',
        'burmese' => 'မြန်မာ',
        'khmer' => 'ខ្មែរ',
        'lao' => 'ລາວ',
        'mongolian' => 'Монгол',
        'tibetan' => 'བོད་སྐད།',
        'uyghur' => 'ئۇيغۇرچە',
        'kazakh' => 'Қазақша',
        'uzbek' => "Oʻzbekcha",
        'turkmen' => 'Türkmençe',
        'kyrgyz' => 'Кыргызча',
        'tajik' => 'Тоҷикӣ',
        'georgian' => 'ქართული',
        'armenian' => 'Հայերեն',
        'azerbaijani' => 'Azərbaycan',
        'albanian' => 'Shqip',
        'macedonian' => 'Македонски',
        'bosnian' => 'Bosanski',
        'montenegrin' => 'Crnogorski',
    );
}

/**
 * 多国语言切换支持的语言列表（translate.js 语言代码）
 * 默认包含全部 73 种语言，可在后台自定义删减/排序
 */
function theme_lang_list() {
    $options = get_option('theme_settings');
    $defaults = theme_lang_default_list();
    $raw = $options['theme_lang_switch_list'] ?? '';
    if (!trim($raw)) {
        return $defaults;
    }
    $list = array();
    foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $parts = array_map('trim', explode('|', $line));
        if (!empty($parts[0])) {
            $code = $parts[0];
            $name = isset($parts[1]) && $parts[1] !== '' ? $parts[1] : (isset($defaults[$code]) ? $defaults[$code] : $code);
            $list[$code] = $name;
        }
    }
    return $list;
}

/**
 * 后台 textarea 默认预填的语言列表（仅包含已有国旗图标的 16 种语言）
 * 若用户在后台清空保存，则 theme_lang_list() 会回退加载全部 73 种语言
 */
function theme_lang_default_list_text() {
    $all = theme_lang_default_list();
    // 取国旗 map 中已收录的语言代码（顺序与国旗 map 一致）
    $flag_codes = array(
        'chinese_simplified', 'chinese_traditional', 'english', 'korean', 'japanese',
        'french', 'deutsch', 'spanish', 'russian', 'arabic', 'thai', 'vietnamese',
        'portuguese', 'italian', 'indonesian', 'turkish',
    );
    $out = array();
    foreach ($flag_codes as $code) {
        $out[] = $code . '|' . (isset($all[$code]) ? $all[$code] : $code);
    }
    return implode("\n", $out);
}

/**
 * translate.js 语言代码 → 国旗 SVG 文件名
 */
function theme_lang_flag_code($lang) {
    $map = array(
        'chinese_simplified' => 'cn', 'chinese_traditional' => 'tw', 'english' => 'us',
        'korean' => 'kr', 'japanese' => 'jp', 'french' => 'fr', 'deutsch' => 'de',
        'spanish' => 'es', 'russian' => 'ru', 'arabic' => 'sa', 'thai' => 'th',
        'vietnamese' => 'vi', 'portuguese' => 'pt', 'italian' => 'it',
        'indonesian' => 'id', 'turkish' => 'tr',
    );
    return isset($map[$lang]) ? $map[$lang] : '';
}

/**
 * 输出国旗图标 HTML（未收录的语言回退为地球图标）
 */
function theme_lang_flag_html($lang) {
    $code = theme_lang_flag_code($lang);
    if ($code) {
        return '<img class="lang-flag" src="' . esc_url(get_template_directory_uri() . '/assets/images/flags/' . $code . '.svg') . '" alt="' . esc_attr($lang) . '" loading="lazy" decoding="async" width="16" height="12">';
    }
    return '<svg class="lang-flag" width="16" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>';
}

function theme_lang_switch_callback() {
    $options = get_option('theme_settings');
    $on = $options['theme_lang_switch'] ?? 1;
    $default_text = theme_lang_default_list_text();
    $list = $options['theme_lang_switch_list'] ?? $default_text;
    echo '<label><input type="checkbox" name="theme_settings[theme_lang_switch]" value="1" ' . checked($on, 1, false) . '> 启用右上角多国语言切换</label>';
    echo '<textarea name="theme_settings[theme_lang_switch_list]" class="large-text code" rows="8" placeholder="每行一个：语言代码|名称">' . esc_textarea($list) . '</textarea>';
    echo '<p class="description">每行格式：语言代码|显示名称，留空则使用全部 73 种默认语言；第一个语言为默认（源）语言。浏览器会根据访客系统语言自动切换到对应语种（访客可手动选择，选择后记忆）。</p>';
}

/**
 * 前台输出 translate.js 翻译引擎与切换器脚本
 */
function theme_lang_switcher_footer() {
    $options = get_option('theme_settings');
    $on = $options['theme_lang_switch'] ?? 1;
    if (!$on) {
        return;
    }
    $site_name = get_bloginfo('name');
    $enabled_codes = array_values(array_keys(theme_lang_list()));
    // 浏览器 ISO 639-1 语言代码 → translate.js 代码（中文需按地区区分简繁）
    $browser_map = array(
        'zh' => 'chinese_simplified', 'zh-cn' => 'chinese_simplified', 'zh-sg' => 'chinese_simplified',
        'zh-tw' => 'chinese_traditional', 'zh-hk' => 'chinese_traditional', 'zh-mo' => 'chinese_traditional',
        'en' => 'english', 'ja' => 'japanese', 'ko' => 'korean', 'fr' => 'french',
        'es' => 'spanish', 'de' => 'deutsch', 'pt' => 'portuguese', 'it' => 'italian',
        'ru' => 'russian', 'ar' => 'arabic', 'th' => 'thai', 'vi' => 'vietnamese',
        'id' => 'indonesian', 'ms' => 'malay', 'tr' => 'turkish', 'pl' => 'polish',
        'nl' => 'dutch', 'sv' => 'swedish', 'el' => 'greek', 'cs' => 'czech',
        'hu' => 'hungarian', 'ro' => 'romanian', 'uk' => 'ukrainian', 'he' => 'hebrew', 'iw' => 'hebrew',
        'hi' => 'hindi', 'fa' => 'persian', 'ur' => 'urdu', 'fil' => 'filipino', 'tl' => 'filipino',
        'la' => 'latin', 'et' => 'estonian', 'lv' => 'latvian', 'lt' => 'lithuanian',
        'sk' => 'slovak', 'sl' => 'slovenian', 'bg' => 'bulgarian', 'hr' => 'croatian',
        'sr' => 'serbian', 'da' => 'danish', 'fi' => 'finnish', 'nb' => 'norwegian', 'no' => 'norwegian',
        'is' => 'icelandic', 'ga' => 'irish', 'mt' => 'maltese', 'ca' => 'catalan',
        'gl' => 'galician', 'eu' => 'basque', 'cy' => 'welsh', 'af' => 'afrikaans',
        'zu' => 'zulu', 'sw' => 'swahili', 'ha' => 'hausa', 'yo' => 'yoruba',
        'ig' => 'igbo', 'am' => 'amharic', 'bn' => 'bengali', 'ta' => 'tamil',
        'te' => 'telugu', 'kn' => 'kannada', 'ml' => 'malayalam', 'mr' => 'marathi',
        'gu' => 'gujarati', 'pa' => 'punjabi', 'ne' => 'nepali', 'si' => 'sinhala',
        'my' => 'burmese', 'km' => 'khmer', 'lo' => 'lao', 'mn' => 'mongolian',
        'bo' => 'tibetan', 'ug' => 'uyghur', 'kk' => 'kazakh', 'uz' => 'uzbek',
        'tk' => 'turkmen', 'ky' => 'kyrgyz', 'tg' => 'tajik', 'ka' => 'georgian',
        'hy' => 'armenian', 'az' => 'azerbaijani', 'sq' => 'albanian', 'mk' => 'macedonian',
        'bs' => 'bosnian',
    );
    ?>
<style>
/* 语言旗标与下拉菜单 */
.lang-flag{width:16px;height:12px;border-radius:2px;object-fit:cover;flex-shrink:0;}
.lang-switch-list li a{display:flex;align-items:center;padding:7px 16px;font-size:13px;line-height:1.4;white-space:nowrap;color:inherit;}
.lang-switch-list li a .lang-flag{margin-right:8px;}
.lang-switch-list li a:hover{background:rgba(134,24,219,.08);color:var(--theme-color,#8618db);}
.lang-switch-list li a.is-current{color:var(--theme-color,#8618db);font-weight:600;}
.lang-switch-list li a span{margin-left:4px;}
.io-black-mode .lang-switch-list li a:hover{background:rgba(255,255,255,.08);}
.lang-switch-list.is-loading{opacity:.5;pointer-events:none;}
.lang-switch-list{max-height:70vh;overflow-y:auto;overflow-x:hidden;}
</style>
<script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/translate.min.js'); ?>" id="io-translate-js"></script>
<script id="io-translate-js-after">
document.addEventListener("DOMContentLoaded",function(){
    if(typeof(translate)!=="object"){return;}
    var ioEnabledLangs=<?php echo wp_json_encode($enabled_codes); ?>;
    var ioBrowserMap=<?php echo wp_json_encode($browser_map); ?>;
    translate.selectLanguageTag.show=false;
    translate.language.setLocal("chinese_simplified");
    var ioSyncLangSwitcher=function(lang){
        var el=document.querySelector('.lang-switch-item[data-lang="'+lang+'"]');
        if(!el){return;}
        document.querySelectorAll(".lang-switch-item").forEach(function(a){a.classList.toggle("is-current",a===el);});
        var cur=document.querySelector(".lang-switch-current");
        if(!cur){return;}
        var flag=el.querySelector(".lang-flag");
        cur.setAttribute("data-lang",lang);
        cur.setAttribute("title",el.querySelector("span")?el.querySelector("span").textContent:lang);
        if(flag){cur.innerHTML=flag.outerHTML;}
    };
    /* 浏览器语言自动识别：仅在访客未手动选择过语种时触发 */
    var ioAutoApplyBrowserLang=function(){
        var stored=translate.storage&&typeof translate.storage.get==="function"?translate.storage.get("to"):null;
        if(stored&&stored.length>0){return;}
        var nav=navigator.language||navigator.userLanguage||"";
        if(!nav){return;}
        nav=nav.toLowerCase();
        var code=ioBrowserMap[nav];
        if(!code){
            var base=nav.split("-")[0];
            code=ioBrowserMap[base];
        }
        if(!code){return;}
        if(ioEnabledLangs.indexOf(code)===-1){return;}
        if(code===translate.language.getLocal()){return;}
        translate.changeLanguage(code);
        ioSyncLangSwitcher(code);
    };
    if(typeof(translate.language.getCurrent)==="function"){ioSyncLangSwitcher(translate.language.getCurrent());}
    translate.service.use("client.edge");
    translate.ignore.text=["<?php echo esc_js($site_name); ?>"];
    translate.request.listener.start();
    translate.execute();
    ioAutoApplyBrowserLang();
    document.addEventListener("click",function(e){
        var el=e.target.closest(".lang-switch-item");
        if(!el){return;}
        var list=el.closest(".lang-switch-list");
        if(list&&list.classList.contains("is-loading")){return;}
        var lang=el.getAttribute("data-lang");
        if(!lang){return;}
        if(list){list.classList.add("is-loading");}
        translate.changeLanguage(lang);
        ioSyncLangSwitcher(lang);
        if(list){setTimeout(function(){list.classList.remove("is-loading");},2000);}
    });
});
</script>
    <?php
}
add_action('wp_footer', 'theme_lang_switcher_footer');

function theme_tz_flag($tz) {
    $map = array(
        'Asia/Shanghai' => '🇨🇳', 'Asia/Hong_Kong' => '🇭🇰', 'Asia/Taipei' => '🇹🇼', 'Asia/Tokyo' => '🇯🇵',
        'Asia/Seoul' => '🇰🇷', 'Asia/Singapore' => '🇸🇬', 'Asia/Bangkok' => '🇹🇭', 'Asia/Kuala_Lumpur' => '🇲🇾',
        'Asia/Jakarta' => '🇮🇩', 'Asia/Manila' => '🇵🇭', 'Asia/Kolkata' => '🇮🇳', 'Asia/Dubai' => '🇦🇪',
        'Europe/London' => '🇬🇧', 'Europe/Paris' => '🇫🇷', 'Europe/Berlin' => '🇩🇪', 'Europe/Moscow' => '🇷🇺',
        'America/New_York' => '🇺🇸', 'America/Chicago' => '🇺🇸', 'America/Denver' => '🇺🇸', 'America/Los_Angeles' => '🇺🇸',
        'America/Toronto' => '🇨🇦', 'America/Vancouver' => '🇨🇦', 'America/Sao_Paulo' => '🇧🇷',
        'Australia/Sydney' => '🇦🇺', 'Pacific/Auckland' => '🇳🇿', 'Africa/Cairo' => '🇪🇬',
    );
    return isset($map[$tz]) ? $map[$tz] : '🌐';
}

function theme_flag_emoji($code) {
    $map = array(
        'CNY' => '🇨🇳', 'USD' => '🇺🇸', 'EUR' => '🇪🇺', 'JPY' => '🇯🇵', 'HKD' => '🇭🇰', 'GBP' => '🇬🇧',
        'AUD' => '🇦🇺', 'KRW' => '🇰🇷', 'CAD' => '🇨🇦', 'CHF' => '🇨🇭', 'SGD' => '🇸🇬', 'INR' => '🇮🇳',
        'RUB' => '🇷🇺', 'THB' => '🇹🇭', 'MYR' => '🇲🇾', 'PHP' => '🇵🇭', 'VND' => '🇻🇳', 'BRL' => '🇧🇷',
        'MXN' => '🇲🇽', 'NZD' => '🇳🇿', 'TWD' => '🇹🇼', 'MOP' => '🇲🇴',
    );
    return isset($map[$code]) ? $map[$code] : '🌐';
}

function theme_world_clock_callback() {
    $options = get_option('theme_settings');
    $on = $options['theme_world_clock_on'] ?? 1;
    $cities = $options['theme_world_clock_cities'] ?? "Asia/Shanghai|北京|cn\nAsia/Manila|马尼拉|ph\nAsia/Kuala_Lumpur|吉隆坡|my\nAsia/Singapore|新加坡|sg\nAmerica/New_York|美东|us\nAmerica/Los_Angeles|美西|us\nEurope/London|伦敦|gb\nEurope/Berlin|中欧|eu\nAsia/Tokyo|东京|jp";
    echo '<label><input type="checkbox" name="theme_settings[theme_world_clock_on]" value="1" ' . checked($on, 1, false) . '> 启用世界时钟模块（首页"热门网址"上方）</label>';
    echo '<textarea name="theme_settings[theme_world_clock_cities]" class="large-text code" rows="10" placeholder="每行一个：时区|城市名|国旗代码">' . esc_textarea($cities) . '</textarea>';
    echo '<p class="description">每行格式：时区|城市名|国旗代码，如 <code>Asia/Shanghai|北京|cn</code>。相同时区的城市会自动合并为一组（如北京/马尼拉/吉隆坡/新加坡）。时区标识见 <a href="https://www.php.net/manual/en/timezones.php" target="_blank">php.net/timezones</a>，国旗代码见 <a href="https://flagcdn.com/" target="_blank">flagcdn.com</a>（如 cn、us、jp、gb、eu）；国旗代码留空则显示 emoji。</p>';
}

function theme_currency_callback() {
    $options = get_option('theme_settings');
    $on = $options['theme_currency_on'] ?? 1;
    $base = $options['theme_currency_base'] ?? 'CNY';
    $list = $options['theme_currency_list'] ?? 'USD,EUR,JPY,HKD,GBP,AUD';
    echo '<label><input type="checkbox" name="theme_settings[theme_currency_on]" value="1" ' . checked($on, 1, false) . '> 启用实时汇率模块（首页侧栏）</label>';
    echo '<p>基准货币：<input type="text" name="theme_settings[theme_currency_base]" value="' . esc_attr($base) . '" class="small-text"> ';
    echo '目标货币：<input type="text" name="theme_settings[theme_currency_list]" value="' . esc_attr($list) . '" class="regular-text"></p>';
    echo '<p class="description">目标货币用英文逗号分隔。数据来源 open.er-api.com，缓存6小时</p>';
}

function theme_hotlist_callback() {
    $options = get_option('theme_settings');
    $on = $options['theme_hotlist_on'] ?? 1;
    $default = "百度热点|https://www.115.la/hot-api.php?type=baidu\n微博热点|https://www.115.la/hot-api.php?type=weibo\n抖音热点|https://www.115.la/hot-api.php?type=douyin";
    // 后台显示规范化后的数据源（IT之家→抖音热点，只保留三个官方来源）
    $sources = theme_hotlist_sources();
    $lines = array();
    foreach ($sources as $s) {
        $lines[] = $s[0] . '|' . $s[1];
    }
    $raw = implode("\n", $lines);
    echo '<label><input type="checkbox" name="theme_settings[theme_hotlist_on]" value="1" ' . checked($on, 1, false) . '> 启用热门榜单模块（首页侧栏）</label>';
    echo '<textarea name="theme_settings[theme_hotlist_sources]" class="large-text code" rows="6" placeholder="每行一个：名称|接口地址（可用 || 分隔多个备用接口，自动切换）">' . esc_textarea($raw) . '</textarea>';
    echo '<p class="description">每行格式：名称|接口地址。一个来源可配置多个接口，用 <code>||</code> 分隔，主接口失败时自动尝试下一个。数据缓存1小时，全部失败则5分钟内不再重复请求。默认使用官方 115.la 提供的热点 API（百度/微博/抖音）。</p>';
}

/**
 * 获取并规范化热榜数据源
 * 兼容旧配置：若来源使用旧的第三方接口（vvhan/imsyy/oioweb），自动替换为官方 115.la 热点 API
 */
function theme_hotlist_sources() {
    $options = get_option('theme_settings');
    $default = "百度热点|https://www.115.la/hot-api.php?type=baidu\n微博热点|https://www.115.la/hot-api.php?type=weibo\n抖音热点|https://www.115.la/hot-api.php?type=douyin";
    $sources_raw = $options['theme_hotlist_sources'] ?? $default;
    $sources = theme_parse_config_lines($sources_raw);
    // 官方三个来源的名称→URL 映射
    $official = array(
        '百度热点' => 'https://www.115.la/hot-api.php?type=baidu',
        '微博热点' => 'https://www.115.la/hot-api.php?type=weibo',
        '抖音热点' => 'https://www.115.la/hot-api.php?type=douyin',
    );
    $result = array();
    $seen = array();
    foreach ($sources as $s) {
        $name = $s[0];
        $url  = $s[1];
        // IT之家 旧来源自动替换为 抖音热点
        if ($name === 'IT之家') {
            $name = '抖音热点';
        }
        // 只保留三个官方来源，URL 统一指向官方 API
        if (isset($official[$name])) {
            if (!isset($seen[$name])) {
                $result[] = array($name, $official[$name]);
                $seen[$name] = true;
            }
        }
    }
    // 兜底：如果解析后为空，返回默认三个
    if (empty($result)) {
        $result = theme_parse_config_lines($default);
    }
    return $result;
}

function theme_hotposts_callback() {
    $options = get_option('theme_settings');
    $on = $options['theme_hotposts_on'] ?? 1;
    $count = $options['theme_hotposts_count'] ?? 8;
    echo '<label><input type="checkbox" name="theme_settings[theme_hotposts_on]" value="1" ' . checked($on, 1, false) . '> 启用热门文章模块（首页侧栏）</label>';
    echo '<p>显示数量：<input type="number" name="theme_settings[theme_hotposts_count]" value="' . esc_attr($count) . '" class="small-text" min="1" max="20"></p>';
}

function theme_social_callback() {
    $options = get_option('theme_settings');
    $fields = array(
        'theme_social_wechat' => '微信二维码图片URL（关于本站悬停显示）',
        'theme_social_qq' => 'QQ链接',
        'theme_social_weibo' => '微博链接',
        'theme_social_github' => 'GitHub链接',
    );
    foreach ($fields as $key => $label) {
        $val = $options[$key] ?? '';
        echo '<p><label>' . esc_html($label) . '<br><input type="text" name="theme_settings[' . esc_attr($key) . ']" value="' . esc_attr($val) . '" class="regular-text"></label></p>';
    }
}

/* ===================== 功能模块前台渲染 ===================== */

function theme_parse_config_lines($raw, $limit = 2) {
    $result = array();
    $lines = preg_split('/\r\n|\r|\n/', $raw);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $parts = array_map('trim', explode('|', $line, $limit));
        if (count($parts) >= 2 && $parts[0] !== '' && $parts[1] !== '') {
            $result[] = $parts;
        }
    }
    return $result;
}

function theme_render_world_clock() {
    $options = get_option('theme_settings');
    if (!($options['theme_world_clock_on'] ?? 1)) return;
    $default_cities = "Asia/Shanghai|北京|cn\nAsia/Manila|马尼拉|ph\nAsia/Kuala_Lumpur|吉隆坡|my\nAsia/Singapore|新加坡|sg\nAmerica/New_York|美东|us\nAmerica/Los_Angeles|美西|us\nEurope/London|伦敦|gb\nEurope/Berlin|中欧|eu\nAsia/Tokyo|东京|jp";
    $cities_raw = isset($options['theme_world_clock_cities']) ? trim($options['theme_world_clock_cities']) : '';
    if ($cities_raw === '') {
        $cities_raw = $default_cities;
    }
    $cities = theme_parse_config_lines($cities_raw, 3);
    if (empty($cities)) return;

    // 按时区当前偏移分组：同一偏移的城市合并为一个时钟项（如北京/马尼拉/吉隆坡/新加坡同 UTC+8）
    $groups = array();
    $now_ts = time();
    foreach ($cities as $city) {
        $tz_str = $city[0];
        $name   = $city[1];
        $flag   = $city[2] ?? '';
        $offset = null;
        try {
            $tz = new DateTimeZone($tz_str);
            $offset = $tz->getOffset(new DateTime('now', $tz));
        } catch (Exception $e) {
            $offset = null;
        }
        $key = ($offset === null) ? 'tz_' . $tz_str : 'off_' . $offset;
        if (!isset($groups[$key])) {
            $groups[$key] = array('timezone' => $tz_str, 'cities' => array());
        }
        $groups[$key]['cities'][] = array('name' => $name, 'flag' => strtolower($flag), 'tz' => $tz_str);
    }
    ?>
    <div id="iow_world_clock" class="module-sidebar-widget io-widget-world-clock mb-3">
        <div class="io-world-clock is-horizontal" data-show-seconds="1" style="--wc-bg:rgba(116, 116, 116, 0.08)">
            <div class="io-hscroll world-clock-scroll no-move-btn">
                <div class="io-hscroll-track no-scrollbar">
                    <div class="io-hscroll-list d-flex">
                        <?php foreach ($groups as $group) : ?>
                        <div class="clock-item" data-timezone="<?php echo esc_attr($group['timezone']); ?>">
                            <span class="clock-meta">
                                <span class="flags" aria-hidden="true">
                                    <?php foreach ($group['cities'] as $c) :
                                        if ($c['flag'] !== '') : ?>
                                    <img class="flag-img" src="https://flagcdn.com/<?php echo esc_attr($c['flag']); ?>.svg" title="<?php echo esc_attr($c['name']); ?>" alt="<?php echo esc_attr($c['name']); ?>" loading="lazy" decoding="async" width="16" height="12" onerror="this.style.display='none'">
                                    <?php else : ?>
                                    <span class="flag-emoji"><?php echo theme_tz_flag($c['tz']); ?></span>
                                    <?php endif; endforeach; ?>
                                </span>
                                <span class="names"><?php echo esc_html(implode(' ', array_column($group['cities'], 'name'))); ?></span>
                            </span>
                            <span class="time">--:--:--</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function theme_get_currency_rates($base) {
    $cache_key = 'theme_currency_' . md5($base);
    $data = get_transient($cache_key);
    if ($data === false) {
        $response = wp_remote_get('https://open.er-api.com/v6/latest/' . urlencode($base), array('timeout' => 8));
        if (is_wp_error($response)) return false;
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($body['rates'])) return false;
        $data = array(
            'rates' => $body['rates'],
            'date' => isset($body['time_last_update_utc']) ? gmdate('Y-m-d', strtotime($body['time_last_update_utc'])) : '',
        );
        set_transient($cache_key, $data, 6 * HOUR_IN_SECONDS);
    }
    return $data;
}

function theme_render_currency_widget() {
    $options = get_option('theme_settings');
    if (!($options['theme_currency_on'] ?? 1)) return;
    $base = strtoupper($options['theme_currency_base'] ?? 'CNY');
    $list_raw = strtoupper($options['theme_currency_list'] ?? 'USD,EUR,JPY,HKD,GBP,AUD');
    $currencies = array_filter(array_map('trim', explode(',', $list_raw)));
    if (empty($currencies)) return;
    $data = theme_get_currency_rates($base);
    ?>
    <div class="card io-sidebar-widget io-widget-currency-rate">
        <div class="sidebar-header">
            <div class="card-header widget-header">
                <h3 class="text-md mb-0"><i class="mr-2 iconfont icon-jinbi"></i>汇率</h3>
            </div>
        </div>
        <div class="card-body py-2">
            <?php if ($data) : ?>
            <div class="io-currency-list">
                <?php foreach ($currencies as $code) : ?>
                <?php $rate = isset($data['rates'][$code]) ? $data['rates'][$code] : null; ?>
                <div class="io-currency-item">
                    <span class="cr-flag"><?php echo theme_flag_emoji($code); ?></span>
                    <span class="cr-name"><?php echo esc_html($base); ?> → <?php echo esc_html($code); ?></span>
                    <span class="cr-value flex-fill text-right"><?php echo $rate !== null ? esc_html(round($rate, 4)) : '--'; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-ss text-muted mt-1">更新：<?php echo esc_html($data['date']); ?>（1 <?php echo esc_html($base); ?> 兑换）</div>
            <?php else : ?>
            <div class="text-muted text-sm py-2 text-center">汇率数据暂时无法获取</div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function theme_render_hotlist_widget() {
    $options = get_option('theme_settings');
    if (!($options['theme_hotlist_on'] ?? 1)) return;
    $sources = theme_hotlist_sources();
    if (empty($sources)) return;
    ?>
    <div class="card io-sidebar-widget io-widget-hot-api">
        <div class="card hotapi-tab-card">
            <div class="card-header widget-header">
                <div class="overflow-x-auto no-scrollbar">
                    <?php foreach ($sources as $i => $source) : ?>
                    <div class="hotapi-tab-btn d-inline-block <?php echo $i === 0 ? 'active' : ''; ?>" data-target=".hotapi-src-<?php echo $i; ?>">
                        <span class="title-name text-sm"><?php echo esc_html($source[0]); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php foreach ($sources as $i => $source) : ?>
            <div class="hotapi-card hotapi-src-<?php echo $i; ?> <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>">
                <div class="card-body d-flex flex-column pb-2 pt-0">
                    <div class="overflow-y-auto hotapi-body" style="max-height:320px">
                        <ul class="hotapi-list"></ul>
                    </div>
                    <div class="d-flex mt-auto text-xs text-muted pt-2">
                        <div class="hotapi-status"></div>
                        <div class="flex-fill"></div>
                        <a href="javascript:" class="hotapi-refresh" title="刷新"><i class="iconfont icon-refresh text-md"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function theme_render_hotposts_widget() {
    $options = get_option('theme_settings');
    if (!($options['theme_hotposts_on'] ?? 1)) return;
    $count = intval($options['theme_hotposts_count'] ?? 8);
    $count = min(max($count, 1), 20);
    ?>
    <div class="card io-sidebar-widget io-widget-single-posts-list">
        <div class="sidebar-header">
            <div class="card-header widget-header">
                <h3 class="text-md mb-0"><i class="mr-2 iconfont icon-tools"></i>热门文章</h3>
            </div>
            <a href="javascript:;" class="theme-hotposts-refresh ajax-auto-post click auto" title="刷新"><i class="iconfont icon-refresh"></i></a>
        </div>
        <div class="card-body py-2">
            <ul class="theme-hotposts-list text-sm"></ul>
        </div>
    </div>
    <?php
}

/**
 * 详情页侧栏排行条目
 */
function theme_single_rank_rows($post_type, $orderby) {
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => 6,
        'post__not_in' => array(get_the_ID()),
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'order' => 'DESC',
    );
    if ($orderby === 'views' || $orderby === 'fav') {
        $args['meta_key'] = $orderby === 'views' ? 'site_views' : 'app_favorites';
        $args['orderby'] = 'meta_value_num';
    } else {
        $args['orderby'] = 'comment_count';
    }
    $q = new WP_Query($args);
    if (!$q->have_posts()) {
        wp_reset_postdata();
        return;
    }
    while ($q->have_posts()) : $q->the_post();
        $pid = get_the_ID();
        $rank_placeholder = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiPjwvc3ZnPg==';
        $thumb = has_post_thumbnail($pid) ? get_the_post_thumbnail_url($pid, 'thumbnail') : 'https://ui-avatars.com/api/?name=' . urlencode(get_the_title()) . '&background=random&color=fff&size=64';
        $views = theme_get_site_views($pid);
        $down = theme_get_download_count($pid);
    ?>
    <div class="posts-item app-item d-flex style-app-card post-<?php echo (int) $pid; ?> no-padding">
        <div class="item-header">
            <div class="item-media" style="background-image:linear-gradient(130deg,#f9f9f9,#e8e8e8)">
                <a class="item-image" href="<?php the_permalink(); ?>" style="transform:scale(83%)">
                    <img class="fill-cover lazy unfancybox" src="<?php echo esc_url($rank_placeholder); ?>" data-src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>">
                </a>
            </div>
        </div>
        <div class="item-body overflow-hidden d-flex flex-column flex-fill">
            <h3 class="item-title line1"><a href="<?php the_permalink(); ?>"><?php the_title(); ?><?php if ($post_type === 'app') : $rv = theme_get_app_version($pid); ?><span class="app-v text-xs"> - <?php echo $rv ? esc_html($rv) : '最新'; ?></span><?php endif; ?></a></h3>
            <div class="app-content mt-auto">
                <div class="line1 text-muted text-xs"><?php echo esc_html(wp_trim_words(get_the_content(), 10)); ?></div>
                <div class="meta-ico text-muted text-xs">
                    <span class="meta-view"><i class="iconfont icon-chakan-line"></i><?php echo esc_html(theme_format_num($views)); ?></span>
                    <?php if ($post_type === 'app') : ?><span class="meta-down d-none d-md-inline-block"><i class="iconfont icon-down"></i> <?php echo esc_html(theme_format_num($down)); ?></span><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    endwhile;
    wp_reset_postdata();
}

/**
 * 侧栏标签云
 */
function theme_render_tag_cloud_widget($post_type) {
    // 目标站聚合多种分类法：favorites + sitetag + apptag（+ apps 如果存在）
    $taxonomies = array('favorites');
    if ($post_type === 'app' || $post_type === 'sites') {
        $taxonomies[] = 'sitetag';
    }
    if ($post_type === 'app') {
        $taxonomies[] = 'apptag';
    }
    if ($post_type === 'book') {
        $taxonomies[] = 'booktag';
    }

    $all_terms = array();
    foreach ($taxonomies as $tax) {
        if (!taxonomy_exists($tax)) continue;
        $terms = get_terms(array(
            'taxonomy'   => $tax,
            'number'     => 30,
            'orderby'    => 'count',
            'order'      => 'DESC',
            'hide_empty' => true,
        ));
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                $key = $tax . '_' . $term->term_id;
                $all_terms[$key] = $term;
            }
        }
    }

    // 打乱顺序（目标站 orderby=rand）
    $keys = array_keys($all_terms);
    shuffle($keys);
    $all_terms = array_values(array_intersect_key($all_terms, array_flip($keys)));

    // 取前 20 个
    $all_terms = array_slice($all_terms, 0, 20);
    if (empty($all_terms)) return;

    $colors = array('vc-l-red', 'vc-l-blue', 'vc-l-yellow', 'vc-l-green', 'vc-l-purple', 'vc-l-cyan', 'vc-l-theme', 'vc-l-violet');
    ?>
    <div class="card io-sidebar-widget io-widget-tag-cloud mb-3">
        <div class="sidebar-header">
            <div class="card-header widget-header">
                <h3 class="text-md mb-0"><i class="mr-2 iconfont icon-tools"></i>标签云</h3>
            </div>
        </div>
        <div class="card-body d-flex flex-wrap" style="gap:6px">
            <?php foreach ($all_terms as $i => $term) : ?>
            <a href="<?php echo esc_url(get_term_link($term)); ?>" class="btn btn-sm flex-fill <?php echo esc_attr($colors[$i % count($colors)]); ?>"><?php echo esc_html($term->name); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * 详情页右侧栏（与 OneNav 双栏布局一致）
 * sites：投稿作者 + 网址排行 + 标签云
 * app：软件排行（日/周/月 tab）+ 标签云
 * post：文章排行 + 热门文章
 */
function theme_render_single_sidebar($post_type = 'sites') {
    $type_names = array(
        'sites' => '网址',
        'app'   => '软件',
        'post'  => '文章',
        'book'  => '书籍',
    );
    $type_name = $type_names[$post_type] ?? '内容';
    $current_id = get_the_ID();
    $author = get_userdata(get_the_author_meta('ID'));
    ?>
    <div class="sidebar sidebar-tools d-none d-lg-block">
        <div class="theiaStickySidebar">
            <?php if ($post_type === 'sites' && $author) : ?>
            <!-- 投稿作者 -->
            <div class="card io-sidebar-widget io-widget-about-author mb-3">
                <div class="widget-author-cover br-top-inherit text-center">
                    <div class="author-bg bg-image br-top-inherit fx-bg"></div>
                    <div class="widget-author-avatar mt-n5">
                        <?php echo get_avatar($author->ID, 80, '', $author->display_name, array('class' => 'avatar avatar-80 photo')); ?>
                    </div>
                </div>
                <div class="widget-author-meta p-3">
                    <div class="text-center mb-2">
                        <div><?php echo esc_html($author->display_name); ?></div>
                        <div class="badge vc-purple btn-outline text-ss mt-2">投稿者</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 同类型排行榜 -->
            <?php
            // app/post：日榜默认；sites：周榜默认（与目标站一致）
            $tabs = $post_type === 'sites'
                ? array(array('week', '周榜', 'comment', true), array('today', '日榜', 'views', false), array('month', '月榜', 'fav', false))
                : array(array('today', '日榜', 'views', true), array('week', '周榜', 'comment', false), array('month', '月榜', 'fav', false));
            $rank_link = get_post_type_archive_link($post_type) ?: home_url('/');
            ?>
            <div class="fx-header-bg card io-sidebar-widget io-widget-ranking-list ajax-parent mb-3">
                <div class="sidebar-header">
                    <div class="card-header widget-header">
                        <h3 class="text-md mb-0"><?php echo esc_html($type_name); ?></h3>
                    </div>
                </div>
                <div class="range-nav text-md theme-rank-tabs">
                    <?php foreach ($tabs as $ti => $tab) : ?>
                    <a href="javascript:;" class="is-tab-btn<?php echo $tab[3] ? ' active' : ''; ?>" data-tab="<?php echo esc_attr($tab[0]); ?>"><?php echo esc_html($tab[1]); ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="card-body">
                    <?php foreach ($tabs as $ti => $tab) : ?>
                    <div class="posts-row row-sm ajax-panel row-col-1a theme-rank-panel<?php echo $tab[3] ? '' : ' d-none'; ?>" data-panel="<?php echo esc_attr($tab[0]); ?>">
                        <?php theme_single_rank_rows($post_type, $tab[2]); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?php echo esc_url($rank_link); ?>" class="btn vc-l-yellow d-block mx-3 mb-3 text-sm">查看完整榜单</a>
            </div>

            <?php if ($post_type === 'post') : ?>
            <?php theme_render_hotposts_widget(); ?>
            <?php else : ?>
            <?php theme_render_tag_cloud_widget($post_type); ?>
            <?php endif; ?>
        </div>
    </div>
    <script>
    jQuery(function($){
        $('.theme-rank-tabs .is-tab-btn').on('click', function(){
            var $t = $(this);
            $t.addClass('active').siblings().removeClass('active');
            $t.closest('.io-widget-ranking-list').find('.theme-rank-panel').addClass('d-none')
                .filter('[data-panel="' + $t.data('tab') + '"]').removeClass('d-none');
        });
    });
    </script>
    <?php
}

/* ===================== 功能模块 AJAX ===================== */

add_action('wp_ajax_theme_hot_list', 'theme_ajax_hot_list');
add_action('wp_ajax_nopriv_theme_hot_list', 'theme_ajax_hot_list');
function theme_ajax_hot_list() {
    $index = isset($_POST['source']) ? intval($_POST['source']) : 0;
    $refresh = !empty($_POST['refresh']);
    $sources = theme_hotlist_sources();
    if (!isset($sources[$index])) {
        wp_send_json_error(array('msg' => '数据源不存在'));
    }
    // 支持一个来源配置多个接口（用 || 分隔），逐个尝试，第一个成功即使用
    $urls = array_values(array_filter(array_map('trim', preg_split('/\|\|/', $sources[$index][1]))));
    if (empty($urls)) {
        wp_send_json_error(array('msg' => '数据源未配置接口'));
    }
    $cache_key = 'theme_hotlist_' . md5($sources[$index][1]);
    $fail_key = 'theme_hotlist_fail_' . md5($sources[$index][1]);
    if ($refresh) {
        delete_transient($cache_key);
        delete_transient($fail_key);
    }
    $items = get_transient($cache_key);
    if ($items !== false) {
        wp_send_json_success(array('items' => $items));
    }
    // 若短时间内全部失败过，直接返回失败避免反复请求
    if (get_transient($fail_key)) {
        wp_send_json_error(array('msg' => '接口暂时不可用，请稍后刷新'));
    }
    $items = array();
    $site_url = home_url('/');
    foreach ($urls as $url) {
        // refresh 时通知上游 API 也清缓存
        if ($refresh) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'refresh=1';
        }
        // 官方热点 API 需传递调用方站点地址，用于友链校验
        if (strpos($url, 'hot-api.php') !== false) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'site=' . urlencode($site_url);
        }
        $response = wp_remote_get($url, array('timeout' => 8, 'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'));
        if (is_wp_error($response)) {
            continue;
        }
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($body)) {
            continue;
        }
        $list = array();
        if (isset($body['data']) && is_array($body['data'])) {
            $list = $body['data'];
        } elseif (isset($body['list']) && is_array($body['list'])) {
            $list = $body['list'];
        } elseif (isset($body['items']) && is_array($body['items'])) {
            $list = $body['items'];
        } elseif (isset($body['result']) && is_array($body['result'])) {
            $list = $body['result'];
        }
        if (empty($list) && isset($body['data']['list']) && is_array($body['data']['list'])) {
            $list = $body['data']['list'];
        }
        foreach ($list as $item) {
            if (!is_array($item)) continue;
            $title = isset($item['title']) ? wp_strip_all_tags($item['title']) : '';
            $link = '';
            foreach (array('mUrl', 'url', 'link', 'mobileUrl') as $k) {
                if (!empty($item[$k])) { $link = $item[$k]; break; }
            }
            if ($title === '' || $link === '') continue;
            $items[] = array(
                'title' => $title,
                'url'   => esc_url_raw($link),
                'hot'   => isset($item['hot']) ? $item['hot'] : (isset($item['hotValue']) ? $item['hotValue'] : ''),
            );
            if (count($items) >= 10) break;
        }
        if (!empty($items)) {
            break;
        }
    }
    if (empty($items)) {
        // 全部接口失败，缓存失败状态 5 分钟，避免反复请求
        set_transient($fail_key, 1, 5 * MINUTE_IN_SECONDS);
        wp_send_json_error(array('msg' => '暂无数据，请稍后刷新'));
    }
    set_transient($cache_key, $items, HOUR_IN_SECONDS);
    wp_send_json_success(array('items' => $items));
}

add_action('wp_ajax_theme_hot_posts', 'theme_ajax_hot_posts');
add_action('wp_ajax_nopriv_theme_hot_posts', 'theme_ajax_hot_posts');
function theme_ajax_hot_posts() {
    $options = get_option('theme_settings');
    $count = intval($options['theme_hotposts_count'] ?? 8);
    $count = min(max($count, 1), 20);
    $orderby = !empty($_POST['refresh']) ? 'rand' : 'date';
    $query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => $count,
        'orderby' => $orderby,
        'order' => 'DESC',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
    ));
    $items = array();
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = array(
            'title' => get_the_title(),
            'url' => get_permalink(),
            'date' => get_the_date('n-j'),
        );
    }
    wp_reset_postdata();
    wp_send_json_success(array('items' => $items));
}

/* ===================== 导航详情页设置与AJAX ===================== */

function theme_sites_section_callback() {
    echo '<p>' . __('控制导航详情页（single-sites）的功能开关和第三方服务地址', '115theme') . '</p>';
}

function theme_sites_checkbox_callback($args) {
    $options = get_option('theme_settings');
    $key = $args['key'];
    $val = $options[$key] ?? 1;
    echo '<label><input type="checkbox" name="theme_settings[' . esc_attr($key) . ']" value="1" ' . checked($val, 1, false) . '> ' . esc_html($args['label']) . '</label>';
}

function theme_sites_text_callback($args) {
    $options = get_option('theme_settings');
    $key = $args['key'];
    $defaults = array(
        'theme_screenshot_url' => 'https://s0.wordpress.com/mshots/v1/{domain}?w=456&h=300',
        'theme_favicon_url' => 'https://www.google.com/s2/favicons?domain={domain}&sz=32',
        'theme_qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={url}',
    );
    $val = $options[$key] ?? $defaults[$key];
    echo '<input type="text" name="theme_settings[' . esc_attr($key) . ']" value="' . esc_attr($val) . '" class="regular-text">';
    if (isset($args['desc'])) echo '<p class="description">' . esc_html($args['desc']) . '</p>';
}

/* 图标本地化设置 + 批量刷新按钮 */
function theme_favicon_localize_callback() {
    $options = get_option('theme_settings');
    $enabled = $options['theme_favicon_localize'] ?? 1;
    echo '<label><input type="checkbox" name="theme_settings[theme_favicon_localize]" value="1"' . checked($enabled, 1, false) . '> 启用图标本地化（首次加载自动下载到 uploads/websiteico/，后续直接使用本地文件）</label>';
    $total = wp_count_posts('sites')->publish;
    $cached = count(get_posts(array('post_type' => 'sites', 'posts_per_page' => -1, 'meta_key' => '_local_favicon', 'fields' => 'ids', 'no_found_rows' => true)));
    echo '<p class="description">已本地化 ' . (int) $cached . ' / ' . (int) $total . ' 个网址图标</p>';
    echo '<p><button type="button" class="button button-primary" id="theme-batch-fav-btn" data-offset="0">批量下载/刷新图标</button> ';
    echo '<span id="theme-batch-fav-status" class="text-muted"></span></p>';
    echo '<script>
    jQuery(function($){
        var running = false;
        $("#theme-batch-fav-btn").on("click", function(){
            if (running) return; running = true;
            var $btn = $(this), $s = $("#theme-batch-fav-status");
            var favNonce = ' . wp_json_encode(wp_create_nonce('theme_ajax_nonce')) . ';
            function batch(offset){
                $s.text("正在处理第 " + (offset + 1) + " 批…");
                $.post(ajaxurl, {
                    action: "theme_refresh_favicons",
                    nonce: favNonce,
                    offset: offset,
                    limit: 10
                }, function(res){
                    if (res && res.success) {
                        $s.text(res.data.msg);
                        if (res.data.offset < res.data.total) {
                            batch(res.data.offset);
                        } else {
                            $s.text("✓ 全部完成：" + res.data.msg);
                            $btn.prop("disabled", false);
                            running = false;
                        }
                    } else {
                        $s.text("✗ " + (res.data && res.data.msg ? res.data.msg : "失败"));
                        $btn.prop("disabled", false);
                        running = false;
                    }
                }, "json").fail(function(xhr){
                    $s.text("✗ 请求失败（HTTP " + (xhr && xhr.status ? xhr.status : "0") + "），已暂停，请刷新页面后重试");
                    $btn.prop("disabled", false);
                    running = false;
                });
            }
            $btn.prop("disabled", true);
            batch(0);
        });
    });
    </script>';
}

/* go 跳转（dl_id 参数用于软件下载计数） */
add_action('init', 'theme_go_redirect');
function theme_go_redirect() {
    if (isset($_GET['url']) && strpos($_SERVER['REQUEST_URI'], '/go/') !== false) {
        $url = base64_decode($_GET['url']);
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            if (!empty($_GET['dl_id'])) {
                $dl_post_id = (int) $_GET['dl_id'];
                if ($dl_post_id && get_post_type($dl_post_id) === 'app') {
                    $count = (int) get_post_meta($dl_post_id, 'download_count', true);
                    update_post_meta($dl_post_id, 'download_count', $count + 1);
                }
            }
            wp_redirect($url);
            exit;
        }
    }
}
add_rewrite_rule('^go/?$', 'index.php?theme_go=1', 'top');

/* 点赞/收藏 AJAX */
add_action('wp_ajax_theme_site_like', 'theme_ajax_site_like');
add_action('wp_ajax_nopriv_theme_site_like', 'theme_ajax_site_like');
function theme_ajax_site_like() {
    if (!isset($_POST['post_id']) || !isset($_POST['type'])) {
        wp_send_json_error(array('msg' => '参数错误'));
    }
    $post_id = intval($_POST['post_id']);
    $type = sanitize_text_field($_POST['type']);
    $meta_key = $type === 'favorite' ? '_site_favorites' : '_site_likes';
    $current = intval(get_post_meta($post_id, $meta_key, true));
    $current++;
    update_post_meta($post_id, $meta_key, $current);
    wp_send_json_success(array('count' => $current, 'action' => 'added'));
}

/**
 * 统一收藏/点赞接口（main.min.js 的 .io-posts-like 发送 action=posts_like）
 * 返回结构与 OneNav 前端约定一致：{type:1, data:{action:add|del, count:N}, status:1, msg}
 * 用 cookie 记录访客的点赞/收藏状态，刷新后可正确回显 liked 样式。
 */
function theme_like_cookie_key($post_id, $act_type) {
    return 'theme_ulike_' . $act_type . '_' . (int) $post_id;
}
function theme_is_liked($post_id, $act_type = 'favorite') {
    return !empty($_COOKIE[theme_like_cookie_key($post_id, $act_type)]);
}
add_action('wp_ajax_posts_like', 'theme_ajax_posts_like');
add_action('wp_ajax_nopriv_posts_like', 'theme_ajax_posts_like');
function theme_ajax_posts_like() {
    $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
    $post_type = isset($_POST['post_type']) ? sanitize_key($_POST['post_type']) : '';
    $act_type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'like';
    if (!$post_id || !get_post($post_id) || !in_array($act_type, array('favorite', 'like'), true)) {
        wp_send_json_error(array('msg' => '参数错误'));
    }
    // app 收藏数用 app_favorites，其余类型统一用 _site_favorites / _site_likes
    if ($act_type === 'favorite') {
        $meta_key = $post_type === 'app' ? 'app_favorites' : '_site_favorites';
    } else {
        $meta_key = '_site_likes';
    }
    $cookie_key = theme_like_cookie_key($post_id, $act_type);
    $count = (int) get_post_meta($post_id, $meta_key, true);
    if (!empty($_COOKIE[$cookie_key])) {
        $count = max(0, $count - 1);
        // 立即过期（带路径/域与设置时保持一致）
        setcookie($cookie_key, '', time() - 3600, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN);
        $_COOKIE[$cookie_key] = '';
        $res_action = 'del';
        $msg = $act_type === 'favorite' ? '已取消收藏' : '已取消点赞';
    } else {
        $count++;
        setcookie($cookie_key, '1', time() + 30 * DAY_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN);
        $_COOKIE[$cookie_key] = '1';
        $res_action = 'add';
        $msg = $act_type === 'favorite' ? '收藏成功' : '点赞成功';
    }
    update_post_meta($post_id, $meta_key, $count);
    wp_send_json_success(array(
        'type'   => 1,
        'data'   => array('action' => $res_action, 'count' => $count),
        'status' => 1,
        'msg'    => $msg,
    ));
}

/* SEO权重 AJAX */
add_action('wp_ajax_theme_site_seo', 'theme_ajax_site_seo');
add_action('wp_ajax_nopriv_theme_site_seo', 'theme_ajax_site_seo');
function theme_ajax_site_seo() {
    $domain = isset($_POST['domain']) ? sanitize_text_field($_POST['domain']) : '';
    if (!$domain) wp_send_json_error();
    $cache_key = 'theme_seo_' . md5($domain);
    $data = get_transient($cache_key);
    if ($data === false) {
        $data = array(
            'baidu' => 0,
            'pr' => 0,
            'out_links' => 0,
            'in_links' => 0,
            'index' => 0,
        );
        // 尝试获取百度收录数
        $resp = wp_remote_get('https://www.baidu.com/s?wd=site:' . $domain, array('timeout' => 8, 'user-agent' => 'Mozilla/5.0'));
        if (!is_wp_error($resp)) {
            $body = wp_remote_retrieve_body($resp);
            if (preg_match('/找到相关结果约([\d,]+)个/', $body, $m)) {
                $data['index'] = (int)str_replace(',', '', $m[1]);
            }
        }
        set_transient($cache_key, $data, 6 * HOUR_IN_SECONDS);
    }
    wp_send_json_success($data);
}

/* 反馈 AJAX */
add_action('wp_ajax_theme_site_report', 'theme_ajax_site_report');
add_action('wp_ajax_nopriv_theme_site_report', 'theme_ajax_site_report');
function theme_ajax_site_report() {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    $content = isset($_POST['content']) ? sanitize_textarea_field($_POST['content']) : '';
    if (!$post_id || !$type) wp_send_json_error();
    $reports = get_option('theme_site_reports', array());
    $reports[] = array(
        'post_id' => $post_id,
        'type' => $type,
        'content' => $content,
        'time' => current_time('mysql'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    );
    update_option('theme_site_reports', array_slice($reports, -200));
    wp_send_json_success();
}

/* 热门网址排序/加载更多 AJAX */
add_action('wp_ajax_theme_big_posts', 'theme_ajax_big_posts');
add_action('wp_ajax_nopriv_theme_big_posts', 'theme_ajax_big_posts');
function theme_ajax_big_posts() {
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'views';
    $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
    $per_page = 12;

    $args = array(
        'post_type' => 'sites',
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'paged' => $page,
    );
    switch ($type) {
        case 'date': $args['orderby'] = 'date'; $args['order'] = 'DESC'; break;
        case 'modified': $args['orderby'] = 'modified'; $args['order'] = 'DESC'; break;
        case 'like': $args['orderby'] = 'meta_value_num'; $args['meta_key'] = '_site_likes'; $args['order'] = 'DESC'; break;
        case 'favorite': $args['orderby'] = 'meta_value_num'; $args['meta_key'] = '_site_favorites'; $args['order'] = 'DESC'; break;
        case 'comment': $args['orderby'] = 'comment_count'; $args['order'] = 'DESC'; break;
        default: $args['orderby'] = 'meta_value_num'; $args['meta_key'] = '_site_views'; $args['order'] = 'DESC'; break;
    }
    $query = new WP_Query($args);
    $items = '';
    while ($query->have_posts()) {
        $query->the_post();
        $site_url = theme_get_site_url(get_the_ID());
        $views = theme_get_site_views(get_the_ID());
        $likes = theme_get_site_likes(get_the_ID());
        $fav = function_exists('theme_get_local_favicon') ? theme_get_local_favicon(get_the_ID()) : '';
        ob_start();
        ?>
        <article class="posts-item sites-item d-flex style-sites-max">
            <a href="<?php the_permalink(); ?>" data-id="<?php the_ID(); ?>" data-url="<?php echo esc_url($site_url); ?>" class="sites-body" title="<?php the_title(); ?>">
                <div class="item-header"><div class="item-media">
                    <div class="blur-img-bg lazy-bg"<?php if ($fav) echo ' data-bg="' . esc_url($fav) . '"'; ?>></div>
                    <div class="item-image">
                        <?php if (has_post_thumbnail()) : ?>
                        <img class="fill-cover sites-icon lazy unfancybox" src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title(); ?>">
                        <?php elseif ($fav) : ?>
                        <img class="fill-cover sites-icon lazy unfancybox" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0OCIgaGVpZ2h0PSI0OCI+PHJlY3Qgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiBmaWxsPSIjZjBmMGYwIi8+PC9zdmc+" data-src="<?php echo esc_url($fav); ?>" alt="<?php the_title(); ?>">
                        <?php else : ?>
                        <img class="fill-cover sites-icon lazy unfancybox" src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_the_title()); ?>&background=random&color=fff" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                </div></div>
                <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                    <h3 class="item-title line1"><b><?php the_title(); ?></b></h3>
                    <div class="line1 text-muted text-xs"><?php echo wp_trim_words(get_the_content(), 15); ?></div>
                </div>
            </a>
            <div class="meta-ico text-muted text-xs">
                <span class="meta-view"><i class="iconfont icon-chakan-line"></i><?php echo $views; ?></span>
                <span class="meta-like d-none d-md-inline-block"><i class="iconfont icon-like-line"></i><?php echo $likes; ?></span>
            </div>
            <div class="sites-tags"><div class="item-tags overflow-x-auto no-scrollbar">
                <?php $terms = get_the_terms(get_the_ID(), 'favorites');
                if ($terms && !is_wp_error($terms)) : foreach ($terms as $term) : ?>
                <a href="<?php echo get_term_link($term); ?>" class="badge vc-l-theme text-ss mr-1" rel="tag"><i class="iconfont icon-folder mr-1"></i><?php echo $term->name; ?></a>
                <?php endforeach; endif; ?>
            </div>
            <?php if ($site_url) : ?>
            <a href="<?php echo esc_url($site_url); ?>" target="_blank" rel="external nofollow noopener" class="togo ml-auto text-center text-muted is-views" data-id="<?php the_ID(); ?>" title="直达"><i class="iconfont icon-goto"></i></a>
            <?php endif; ?>
            </div>
        </article>
        <?php
        $items .= ob_get_clean();
    }
    wp_reset_postdata();
    $has_more = $page * $per_page < $query->found_posts;
    wp_send_json_success(array('items' => $items, 'has_more' => $has_more));
}

/* ===================== 主题在线更新（官方 115.la 检测源 + WP 原生一键升级） =====================
 * 主题分发后会自动连接官方接口检测新版本，并通过 WordPress 原生机制
 * （仪表盘→更新 / 外观→主题）完成一键升级，无需任何额外插件。
 *
 * 服务端文件 theme-update.php 仅部署在官方站点 WordPress 根目录，不随主题分发：
 *   - 自动扫描更新包目录，输出版本信息 JSON
 *   - 提供更新 zip 包下载（WP 升级程序直接拉取）
 *
 * 接口返回：{version, download_url, requires, tested, requires_php, details_url, description}
 * 成功结果缓存 12 小时，失败缓存 30 分钟，避免频繁请求拖慢后台。
 * ================================================================================== */

if (!defined('THEME_UPDATE_API')) {
    define('THEME_UPDATE_API', 'https://www.115.la/theme-update.php');
}

/**
 * 获取官方更新信息（带缓存）
 * @param bool $force true=跳过缓存强制请求
 * @return array|false
 */
function theme_get_remote_update($force = false) {
    $cache_key = 'theme_remote_update_info';

    if ($force) {
        delete_transient($cache_key);
        delete_site_transient('update_themes');
    } else {
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return is_array($cached) ? $cached : false;
        }
    }

    $url = add_query_arg(array(
        'site'    => home_url(),
        'version' => THEME_VERSION,
    ), THEME_UPDATE_API);

    $response = wp_remote_get($url, array(
        'timeout'    => 12,
        'user-agent' => 'WordPress/115theme/' . THEME_VERSION . '; ' . home_url(),
        'headers'    => array('Accept' => 'application/json'),
    ));

    if (is_wp_error($response) || (int) wp_remote_retrieve_response_code($response) !== 200) {
        set_transient($cache_key, '', 30 * MINUTE_IN_SECONDS);
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($body) || empty($body['version']) || empty($body['download_url'])) {
        set_transient($cache_key, '', 30 * MINUTE_IN_SECONDS);
        return false;
    }

    set_transient($cache_key, $body, 12 * HOUR_IN_SECONDS);
    return $body;
}

/* 注入 WordPress 原生更新列表：仪表盘→更新、外观→主题 出现"更新可用"并支持一键升级 */
add_filter('pre_set_site_transient_update_themes', 'theme_update_inject');
function theme_update_inject($transient) {
    if (empty($transient->checked)) return $transient;

    $stylesheet = get_stylesheet();
    if (!isset($transient->checked[$stylesheet])) return $transient;

    $info = theme_get_remote_update(false);
    if ($info && version_compare(THEME_VERSION, $info['version'], '<')) {
        $transient->response[$stylesheet] = array(
            'theme'        => $stylesheet,
            'new_version'  => $info['version'],
            'url'          => $info['details_url'] ?? '',
            'package'      => $info['download_url'],
            'requires'     => $info['requires'] ?? '5.0',
            'tested'       => $info['tested'] ?? '',
            'requires_php' => $info['requires_php'] ?? '',
        );
    }
    return $transient;
}

/* "查看版本详情"弹窗（外观→主题→查看详情）展示官方更新日志 */
add_filter('themes_api', 'theme_update_details', 10, 3);
function theme_update_details($result, $action, $args) {
    if ($action !== 'theme_information') return $result;
    if (empty($args->slug) || $args->slug !== get_stylesheet()) return $result;

    $info = theme_get_remote_update(false);
    if (!$info) return $result;

    $sections = array();
    if (!empty($info['description'])) $sections['description'] = $info['description'];
    if (!empty($info['changelog'])) $sections['changelog'] = $info['changelog'];

    return (object) array(
        'name'          => wp_get_theme()->get('Name'),
        'slug'          => get_stylesheet(),
        'version'       => $info['version'],
        'download_link' => $info['download_url'],
        'requires'      => $info['requires'] ?? '5.0',
        'tested'        => $info['tested'] ?? '',
        'requires_php'  => $info['requires_php'] ?? '',
        'homepage'      => 'https://www.115.la/',
        'sections'      => $sections,
        'banners'       => array(),
    );
}

/* 后台顶部"发现新版本"提示（可按版本关闭） */
add_action('admin_init', 'theme_update_handle_dismiss');
function theme_update_handle_dismiss() {
    if (!current_user_can('manage_options')) return;
    if (!isset($_GET['theme_update_dismiss'])) return;
    $ver = sanitize_text_field(wp_unslash($_GET['theme_update_dismiss']));
    check_admin_referer('theme_update_dismiss_' . $ver);
    update_user_meta(get_current_user_id(), '_theme_update_dismissed', $ver);
    $referer = wp_get_referer();
    wp_safe_redirect($referer ? $referer : admin_url());
    exit;
}

add_action('admin_notices', 'theme_update_admin_notice');
function theme_update_admin_notice() {
    if (!current_user_can('manage_options')) return;
    if (isset($_GET['page']) && $_GET['page'] === 'theme_update_check') return;

    $info = theme_get_remote_update(false);
    if (!$info || empty($info['version'])) return;
    if (!version_compare(THEME_VERSION, $info['version'], '<')) return; // 已是最新或更高
    if (get_user_meta(get_current_user_id(), '_theme_update_dismissed', true) === $info['version']) return;

    $dismiss_url = wp_nonce_url(
        add_query_arg('theme_update_dismiss', $info['version'], admin_url()),
        'theme_update_dismiss_' . $info['version']
    );
    ?>
    <div class="notice notice-warning">
        <p>
            <strong>115theme 有新版本可用：v<?php echo esc_html($info['version']); ?></strong>
            （当前 v<?php echo esc_html(THEME_VERSION); ?>）
            <a class="button button-primary" style="margin-left:8px" href="<?php echo esc_url(admin_url('update-core.php')); ?>?force-check=1">立即更新</a>
            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=theme_update_check')); ?>">查看更新日志</a>
            <a href="<?php echo esc_url($dismiss_url); ?>" class="button" style="float:right">不再提醒此版本</a>
        </p>
    </div>
    <?php
}

/* 主题更新检测页 */
function theme_update_check_page() {
    $info = theme_get_remote_update(false);
    $has_update = ($info && !empty($info['version']) && version_compare(THEME_VERSION, $info['version'], '<'));
    ?>
    <div class="wrap">
        <h1>主题更新</h1>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">当前版本</th>
                <td><strong style="font-size:15px;line-height:1.4">v<?php echo esc_html(THEME_VERSION); ?></strong></td>
            </tr>
            <tr>
                <th scope="row">最新版本</th>
                <td>
                    <?php if ($info && !empty($info['version'])) : ?>
                        <strong style="font-size:15px;color:<?php echo $has_update ? '#d63638' : '#008a20'; ?>">v<?php echo esc_html($info['version']); ?></strong>
                        <?php if ($has_update) : ?>
                            <span class="description" style="margin-left:8px">发现新版本</span>
                        <?php else : ?>
                            <span class="description" style="margin-left:8px">已是最新版本</span>
                        <?php endif; ?>
                    <?php else : ?>
                        <span class="description">暂时无法连接官方更新服务器（结果每 12 小时自动刷新，可点下方按钮立即检查）</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <p>
            <button type="button" class="button button-primary" id="theme-update-check-now">立即检查更新</button>
            <span id="theme-update-check-result" class="description" style="margin-left:8px"></span>
        </p>

        <div id="theme-update-detail" class="card" style="max-width:820px;display:none;margin-top:16px;padding:16px 20px"></div>

        <?php if ($has_update) : ?>
        <div class="card" style="max-width:820px;margin-top:16px;padding:16px 20px">
            <h2 style="margin-top:6px">新版本 v<?php echo esc_html($info['version']); ?></h2>
            <p class="description" style="margin:4px 0 10px">
                <?php if (!empty($info['requires'])) echo '要求 WordPress ' . esc_html($info['requires']) . ' 或更高版本　'; ?>
                <?php if (!empty($info['tested'])) echo '已兼容测试至 WordPress ' . esc_html($info['tested']) . '　'; ?>
                <?php if (!empty($info['requires_php'])) echo '要求 PHP ' . esc_html($info['requires_php']) . '+'; ?>
            </p>
            <?php if (!empty($info['description'])) : ?>
                <h3 style="margin-bottom:6px">更新日志</h3>
                <div><?php echo wp_kses_post($info['description']); ?></div>
            <?php endif; ?>
            <p style="margin-top:16px">
                <a class="button button-primary" href="<?php echo esc_url(admin_url('update-core.php')); ?>?force-check=1">前往「仪表盘 → 更新」一键升级</a>
                <?php if (!empty($info['details_url'])) : ?>
                    <a class="button" target="_blank" rel="noopener" href="<?php echo esc_url($info['details_url']); ?>">查看官方详情</a>
                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
    <script>
    jQuery(function($){
        $('#theme-update-check-now').on('click', function(){
            var $btn = $(this), $s = $('#theme-update-check-result'), $d = $('#theme-update-detail').hide().empty();
            $btn.prop('disabled', true);
            $s.css('color', '').text('正在连接官方服务器检查…');
            $.post(ajaxurl, {
                action: 'theme_check_update',
                nonce: '<?php echo wp_create_nonce('theme_ajax_nonce'); ?>'
            }, function(res){
                $btn.prop('disabled', false);
                if (!res || !res.success) {
                    $s.css('color', '#d63638').text('检查失败，请稍后重试');
                    return;
                }
                var d = res.data;
                if (d.status === 'unavailable') {
                    $s.css('color', '#d63638').text('无法连接官方更新服务器，请稍后再试');
                } else if (d.status === 'latest') {
                    $s.css('color', '#008a20').text('✓ 当前已是最新版本（v' + d.current + '）');
                } else if (d.status === 'update') {
                    $s.css('color', '#d63638').text('发现新版本 v' + d.latest + '（当前 v' + d.current + '），正在展示更新内容…');
                    var html = '<h2 style="margin-top:6px">新版本 v' + d.latest + '</h2>';
                    html += '<p class="description" style="margin:4px 0 10px">';
                    if (d.requires) html += '要求 WordPress ' + d.requires + ' 或更高版本　';
                    if (d.tested) html += '已兼容测试至 WordPress ' + d.tested + '　';
                    if (d.requires_php) html += '要求 PHP ' + d.requires_php + '+';
                    html += '</p>';
                    if (d.description) html += '<h3 style="margin-bottom:6px">更新日志</h3><div>' + d.description + '</div>';
                    html += '<p style="margin-top:16px"><a class="button button-primary" href="<?php echo esc_url(admin_url('update-core.php')); ?>?force-check=1">前往「仪表盘 → 更新」一键升级</a>';
                    if (d.details_url) html += ' <a class="button" target="_blank" rel="noopener" href="' + d.details_url + '">查看官方详情</a>';
                    html += '</p>';
                    $d.html(html).show();
                }
            }, 'json').fail(function(xhr){
                $btn.prop('disabled', false);
                $s.css('color', '#d63638').text('请求失败（HTTP ' + (xhr && xhr.status ? xhr.status : '0') + '），请刷新页面后重试');
            });
        });
    });
    </script>
    <?php
}

/* 立即检查更新 AJAX（强制跳过缓存；下载与安装由 WP 原生升级程序完成） */
add_action('wp_ajax_theme_check_update', 'theme_ajax_check_update');
function theme_ajax_check_update() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('msg' => '无权限'));
    }
    check_ajax_referer('theme_ajax_nonce', 'nonce');

    $info = theme_get_remote_update(true);
    if (!is_array($info) || empty($info['version'])) {
        wp_send_json_success(array('status' => 'unavailable', 'current' => THEME_VERSION));
    }

    $has_update = version_compare(THEME_VERSION, $info['version'], '<');
    wp_send_json_success(array(
        'status'       => $has_update ? 'update' : 'latest',
        'current'      => THEME_VERSION,
        'latest'       => $info['version'],
        'requires'     => isset($info['requires']) ? sanitize_text_field($info['requires']) : '',
        'tested'       => isset($info['tested']) ? sanitize_text_field($info['tested']) : '',
        'requires_php' => isset($info['requires_php']) ? sanitize_text_field($info['requires_php']) : '',
        'description'  => wp_kses_post((string) ($info['description'] ?? '')),
        'details_url'  => !empty($info['details_url']) ? esc_url_raw($info['details_url']) : '',
    ));
}

/* ===================== 入驻广告（首页付费广告位） ===================== */

function theme_ad_section_callback() {
    echo '<p>' . __('首页"热门"区域的"立即入驻"付费广告位：配置套餐价格与收款二维码，用户下单付款后在"入驻订单"中确认收款，广告即在首页热门区展示至到期。', '115theme') . '</p>';
}

function theme_ad_price_callback($args) {
    $options = get_option('theme_settings');
    $key = $args['key'];
    $val = (is_array($options) && isset($options[$key]) && $options[$key] !== '') ? $options[$key] : $args['default'];
    $suffix = isset($args['suffix']) ? $args['suffix'] : '元';
    echo '<input type="number" step="0.01" min="0" name="theme_settings[' . esc_attr($key) . ']" value="' . esc_attr($val) . '" class="small-text"> ' . esc_html($suffix);
}

function theme_ad_qr_callback($args) {
    $options = get_option('theme_settings');
    $val = is_array($options) ? ($options[$args['key']] ?? '') : '';
    theme_logo_field_html($args['key'], $val, $args['label'] . '；建议上传 300x300 以上的收款二维码图片，用户扫码付款后需在"入驻订单"页确认收款');
}

function theme_ad_notice_callback() {
    $options = get_option('theme_settings');
    $val = is_array($options) ? ($options['theme_ad_notice'] ?? '') : '';
    echo '<textarea name="theme_settings[theme_ad_notice]" rows="3" class="large-text" placeholder="例如：付款后请点击“我已完成支付”，广告将在10分钟内上架；客服微信：xxx">' . esc_textarea($val) . '</textarea>';
    echo '<p class="description">显示在支付弹窗底部，可填写客服微信/QQ、上架时效等说明</p>';
}

function theme_get_ad_settings() {
    $o = get_option('theme_settings');
    $o = is_array($o) ? $o : array();
    return array(
        'enable'       => !empty($o['theme_ad_enable']),
        'price_week'     => floatval($o['theme_ad_price_week'] ?? 68),
        'price_month'    => floatval($o['theme_ad_price_month'] ?? 198),
        'price_quarter'  => floatval($o['theme_ad_price_quarter'] ?? 498),
        'price_halfyear' => floatval($o['theme_ad_price_halfyear'] ?? 888),
        'price_custom' => floatval($o['theme_ad_price_custom'] ?? 0.8),
        'hours_min'    => max(1, intval($o['theme_ad_hours_min'] ?? 1)),
        'hours_max'    => max(1, intval($o['theme_ad_hours_max'] ?? 240)),
        'wechat_qr'    => trim($o['theme_ad_wechat_qr'] ?? ''),
        'alipay_qr'    => trim($o['theme_ad_alipay_qr'] ?? ''),
        'notice'       => trim($o['theme_ad_notice'] ?? ''),
    );
}

function theme_ad_calc_price($hours, $cfg) {
    $hours = intval($hours);
    switch ($hours) {
        case 168:  return round($cfg['price_week'], 2);
        case 720:  return round($cfg['price_month'], 2);
        case 2160: return round($cfg['price_quarter'], 2);
        case 4320: return round($cfg['price_halfyear'], 2);
        default: return round($hours * $cfg['price_custom'], 2);
    }
}

function theme_ad_valid_hours($hours, $cfg) {
    $hours = intval($hours);
    if (in_array($hours, array(168, 720, 2160, 4320), true)) return $hours;
    if ($hours >= $cfg['hours_min'] && $hours <= $cfg['hours_max']) return $hours;
    return 0;
}

/* 时长 → 套餐名称（订单页展示用） */
function theme_ad_hours_label($hours) {
    $map = array(168 => '周付（7天）', 720 => '月付（30天）', 2160 => '季付（90天）', 4320 => '半年付（180天）');
    return isset($map[$hours]) ? $map[$hours] : $hours . ' 小时（自定义）';
}

/* 前台首页有效广告（已上架且未过期），按上架时间倒序 */
function theme_get_active_ads($limit = 6) {
    $orders = get_option('theme_ad_orders', array());
    $now = current_time('timestamp');
    $active = array();
    foreach ($orders as $o) {
        if (($o['status'] ?? '') !== 'approved' || empty($o['expire_at'])) continue;
        if (strtotime($o['expire_at']) > $now) $active[] = $o;
    }
    usort($active, function($a, $b) { return strcmp($b['start_at'] ?? '', $a['start_at'] ?? ''); });
    return array_slice($active, 0, $limit);
}

/* AJAX：提交入驻订单 */
add_action('wp_ajax_theme_ad_submit', 'theme_ajax_ad_submit');
add_action('wp_ajax_nopriv_theme_ad_submit', 'theme_ajax_ad_submit');
function theme_ajax_ad_submit() {
    check_ajax_referer('theme_ajax_nonce', 'nonce');
    $cfg = theme_get_ad_settings();
    if (!$cfg['enable']) wp_send_json_error(array('msg' => '入驻功能暂未开放'));

    $name = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $url = esc_url_raw(wp_unslash($_POST['url'] ?? ''));
    $contact = sanitize_text_field(wp_unslash($_POST['contact'] ?? ''));
    $hours = theme_ad_valid_hours(isset($_POST['hours']) ? wp_unslash($_POST['hours']) : 0, $cfg);

    if ($name === '' || mb_strlen($name) > 30) wp_send_json_error(array('msg' => '请填写网站名称（30字以内）'));
    if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url)) wp_send_json_error(array('msg' => '请填写正确的网站URL（http/https开头）'));
    if (!filter_var($contact, FILTER_VALIDATE_EMAIL)) wp_send_json_error(array('msg' => '请填写正确的联系邮箱'));
    if (!$hours) wp_send_json_error(array('msg' => '请选择有效的入驻时长'));
    if (!$cfg['wechat_qr'] && !$cfg['alipay_qr']) wp_send_json_error(array('msg' => '站长暂未配置收款方式，请稍后再试'));

    $amount = theme_ad_calc_price($hours, $cfg);
    $orders = get_option('theme_ad_orders', array());
    $order = array(
        'id'         => date('YmdHis') . wp_rand(100, 999),
        'name'       => $name,
        'url'        => $url,
        'contact'    => $contact,
        'hours'      => $hours,
        'amount'     => $amount,
        'pay_method' => '',
        'status'     => 'pending',
        'created_at' => current_time('mysql'),
        'paid_at'    => '',
        'start_at'   => '',
        'expire_at'  => '',
        'ip'         => $_SERVER['REMOTE_ADDR'] ?? '',
    );
    $orders[] = $order;
    update_option('theme_ad_orders', array_slice($orders, -500));

    wp_send_json_success(array(
        'order_id' => $order['id'],
        'amount'   => number_format($amount, 2, '.', ''),
        'hours'    => $hours,
        'wechat'   => $cfg['wechat_qr'],
        'alipay'   => $cfg['alipay_qr'],
        'notice'   => $cfg['notice'],
    ));
}

/* AJAX：用户确认已扫码支付 */
add_action('wp_ajax_theme_ad_paid', 'theme_ajax_ad_paid');
add_action('wp_ajax_nopriv_theme_ad_paid', 'theme_ajax_ad_paid');
function theme_ajax_ad_paid() {
    check_ajax_referer('theme_ajax_nonce', 'nonce');
    $order_id = sanitize_text_field(wp_unslash($_POST['order_id'] ?? ''));
    $pay_method = sanitize_text_field(wp_unslash($_POST['pay_method'] ?? ''));
    if (!in_array($pay_method, array('wechat', 'alipay'), true)) $pay_method = 'wechat';

    $orders = get_option('theme_ad_orders', array());
    $found = false;
    foreach ($orders as $i => $o) {
        if (($o['id'] ?? '') === $order_id && ($o['status'] ?? '') === 'pending') {
            $orders[$i]['pay_method'] = $pay_method;
            $orders[$i]['paid_at'] = current_time('mysql');
            $found = true;
            break;
        }
    }
    if (!$found) wp_send_json_error(array('msg' => '订单不存在或已处理'));
    update_option('theme_ad_orders', $orders);
    wp_send_json_success();
}

/* 后台：入驻订单管理页 */
function theme_ad_orders_page() {
    if (!current_user_can('manage_options')) return;

    if (isset($_GET['action'], $_GET['id'], $_GET['_wpnonce']) && wp_verify_nonce(wp_unslash($_GET['_wpnonce']), 'ad_order_action')) {
        $action = sanitize_key(wp_unslash($_GET['action']));
        $id = sanitize_text_field(wp_unslash($_GET['id']));
        $orders = get_option('theme_ad_orders', array());
        foreach ($orders as $i => $o) {
            if (($o['id'] ?? '') !== $id) continue;
            if ($action === 'approve') {
                $start = current_time('timestamp');
                $orders[$i]['status'] = 'approved';
                $orders[$i]['start_at'] = date('Y-m-d H:i:s', $start);
                $orders[$i]['expire_at'] = date('Y-m-d H:i:s', $start + intval($o['hours']) * HOUR_IN_SECONDS);
                update_option('theme_ad_orders', $orders);
                echo '<div class="updated"><p>已确认收款，广告上架展示至 ' . esc_html($orders[$i]['expire_at']) . '</p></div>';
            } elseif ($action === 'reject') {
                $orders[$i]['status'] = 'rejected';
                update_option('theme_ad_orders', $orders);
                echo '<div class="updated"><p>订单已拒绝</p></div>';
            } elseif ($action === 'delete') {
                array_splice($orders, $i, 1);
                update_option('theme_ad_orders', $orders);
                echo '<div class="updated"><p>订单已删除</p></div>';
            }
            break;
        }
    }

    $orders = array_reverse(get_option('theme_ad_orders', array()));
    $status_map = array(
        'pending'  => '<span style="color:#996600">待确认收款</span>',
        'approved' => '<span style="color:#008000">已上架</span>',
        'rejected' => '<span style="color:#999">已拒绝</span>',
    );
    $now = current_time('timestamp');
    ?>
    <div class="wrap">
        <h1><?php _e('入驻订单', '115theme'); ?></h1>
        <p>用户在首页点击"立即入驻"下单并扫码付款后，订单会出现在这里。确认收到款项后点击"确认收款并上架"，广告将在首页热门区展示至到期时间。</p>
        <?php if (empty($orders)) : ?>
        <p>暂无入驻订单。</p>
        <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>订单号</th>
                    <th>网站名称</th>
                    <th>网址</th>
                    <th>联系邮箱</th>
                    <th>时长</th>
                    <th>金额</th>
                    <th>支付方式</th>
                    <th>状态</th>
                    <th>下单时间</th>
                    <th>上架/到期</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o) :
                    $expired = (($o['status'] ?? '') === 'approved' && !empty($o['expire_at']) && strtotime($o['expire_at']) <= $now);
                    $base_url = '?page=theme_ad_orders&action=%s&id=' . urlencode($o['id'] ?? '') . '&_wpnonce=' . wp_create_nonce('ad_order_action');
                ?>
                <tr>
                    <td><?php echo esc_html($o['id'] ?? ''); ?></td>
                    <td><?php echo esc_html($o['name'] ?? ''); ?></td>
                    <td><a href="<?php echo esc_url($o['url'] ?? '#'); ?>" target="_blank" rel="noopener"><?php echo esc_html($o['url'] ?? ''); ?></a></td>
                    <td><?php echo esc_html($o['contact'] ?? ''); ?></td>
                    <td><?php echo esc_html(theme_ad_hours_label(intval($o['hours'] ?? 0))); ?></td>
                    <td>￥<?php echo esc_html(number_format(floatval($o['amount'] ?? 0), 2)); ?></td>
                    <td><?php $pm = $o['pay_method'] ?? ''; echo $pm === 'alipay' ? '支付宝' : ($pm === 'wechat' ? '微信' : '—'); ?></td>
                    <td><?php echo $expired ? '<span style="color:#999">已过期</span>' : ($status_map[$o['status'] ?? ''] ?? esc_html($o['status'] ?? '')); ?></td>
                    <td><?php echo esc_html($o['created_at'] ?? ''); ?><br><small><?php echo esc_html($o['ip'] ?? ''); ?></small></td>
                    <td><?php if (!empty($o['start_at'])) : ?><?php echo esc_html($o['start_at']); ?><br>至 <?php echo esc_html($o['expire_at']); ?><?php else : ?>—<?php endif; ?></td>
                    <td>
                        <?php if (($o['status'] ?? '') !== 'approved' || $expired) : ?>
                        <a href="<?php echo esc_url(sprintf($base_url, 'approve')); ?>" class="button button-primary button-small">确认收款并上架</a>
                        <?php endif; ?>
                        <?php if (($o['status'] ?? '') === 'pending') : ?>
                        <a href="<?php echo esc_url(sprintf($base_url, 'reject')); ?>" class="button button-small">拒绝</a>
                        <?php endif; ?>
                        <a href="<?php echo esc_url(sprintf($base_url, 'delete')); ?>" class="button button-small" onclick="return confirm('确定删除该订单？');">删除</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php
}

/* ===================== 官方友链保护（免费使用授权） ===================== */

/* 判断某个 URL 是否为官方友链 115啦（host 严格匹配防伪造） */
function theme_link_is_official($url) {
    $host = strtolower((string) parse_url(trim((string) $url), PHP_URL_HOST));
    if (!$host) return false;
    return $host === '115.la' || $host === 'www.115.la' || substr($host, -7) === '.115.la';
}

/* 检测友情链接中是否保留官方友链 115啦（https://www.115.la） */
function theme_official_link_ok() {
    $friendlinks = get_option('theme_friendlinks', array());
    if (!is_array($friendlinks)) return false;
    foreach ($friendlinks as $link) {
        $url = is_array($link) ? ($link['url'] ?? '') : '';
        if ($url && theme_link_is_official($url)) {
            return true;
        }
    }
    return false;
}

/* 幂等写入官方友链，返回写入后检测是否通过 */
function theme_ensure_official_link() {
    $friendlinks = get_option('theme_friendlinks', array());
    if (!is_array($friendlinks)) $friendlinks = array();
    foreach ($friendlinks as $link) {
        $url = is_array($link) ? ($link['url'] ?? '') : '';
        if ($url && theme_link_is_official($url)) {
            return true;
        }
    }
    $friendlinks[] = array('name' => '115啦', 'url' => 'https://115.la', 'description' => '115啦导航');
    update_option('theme_friendlinks', $friendlinks, true);
    return theme_official_link_ok();
}

/* 后台主题设置相关页面：官方友链被删除时全局遮盖并提示恢复 */
add_action('admin_footer', 'theme_official_link_lock');
function theme_official_link_lock() {
    if (!current_user_can('manage_options')) return;
    // 主题设置三个后台页均为 admin.php?page=theme_settings / theme_friendlinks / theme_ad_orders
    global $pagenow;
    $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
    if ($pagenow !== 'admin.php' || !in_array($page, array('theme_settings', 'theme_friendlinks', 'theme_ad_orders'), true)) return;
    if (theme_official_link_ok()) return;
    $restore_url = wp_nonce_url(
        add_query_arg(array('page' => $page, 'tll_restore' => '1', 'tll_from' => $page), admin_url('admin.php')),
        'theme_restore_official_link',
        'tll_nonce'
    );
    ?>
    <style>
    #theme-link-lock{position:fixed;inset:0;z-index:999999;background:rgba(17,24,39,.82);backdrop-filter:blur(3px);display:flex;align-items:center;justify-content:center;padding:20px}
    #theme-link-lock .tll-card{background:#fff;border-radius:14px;max-width:520px;width:100%;padding:42px 36px 34px;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.35)}
    #theme-link-lock .tll-icon{width:72px;height:72px;margin:0 auto 18px;border-radius:50%;background:#fff7f0;display:flex;align-items:center;justify-content:center;color:#ff6d00}
    #theme-link-lock .tll-icon .dashicons{font-size:38px;width:38px;height:38px;line-height:38px}
    #theme-link-lock h2{font-size:21px;margin:0 0 14px;color:#1f2937}
    #theme-link-lock p{color:#6b7280;font-size:14px;line-height:1.9;margin:0 0 10px}
    #theme-link-lock .tll-url{display:inline-block;background:#f3f4f6;border-radius:8px;padding:8px 16px;font-size:13px;color:#374151;margin:6px 0 18px}
    #theme-link-lock #tll-restore{margin-top:4px;text-decoration:none}
    #theme-link-lock .tll-tip{font-size:12px;color:#9ca3af;margin-top:14px;margin-bottom:0}
    #theme-link-lock .tll-fail{color:#d63638;font-weight:600}
    </style>
    <div id="theme-link-lock">
        <div class="tll-card">
            <div class="tll-icon"><span class="dashicons dashicons-lock"></span></div>
            <h2>请恢复官方友情链接</h2>
            <p>检测到官方友情链接 <strong style="color:#ff6d00">115啦</strong> 已被删除。<br>本主题免费使用并提供技术支持的前提是保留该友链，恢复后即可正常使用主题设置全部功能。</p>
            <div class="tll-url">名称：115啦　链接：https://www.115.la</div>
            <?php if (isset($_GET['tll_fail'])) : ?><p class="tll-fail">自动写入失败，请在下方"友链管理"中手动添加该友链后刷新页面</p><?php endif; ?>
            <p>
                <a href="<?php echo esc_url($restore_url); ?>" id="tll-restore" class="button button-primary button-hero">一键恢复官方友链</a>
            </p>
            <p class="tll-tip">点击后将自动恢复并刷新页面；也可在"友链管理"中手动添加上述友链</p>
        </div>
    </div>
    <script>
    (function () {
        var a = document.getElementById('tll-restore');
        if (a) a.addEventListener('click', function () { a.textContent = '恢复中，正在刷新页面...'; });
    })();
    </script>
    <?php
}

/* 一键恢复官方友链：整页 GET 请求处理（admin_init 阶段，任何后台环境都可用，不依赖 admin-ajax） */
add_action('admin_init', 'theme_handle_restore_official_link');
function theme_handle_restore_official_link() {
    if (!current_user_can('manage_options')) return;
    if (empty($_GET['tll_restore'])) return;
    check_admin_referer('theme_restore_official_link', 'tll_nonce');
    $ok = theme_ensure_official_link();
    $from = isset($_GET['tll_from']) ? sanitize_key(wp_unslash($_GET['tll_from'])) : 'theme_settings';
    if (!in_array($from, array('theme_settings', 'theme_friendlinks', 'theme_ad_orders'), true)) {
        $from = 'theme_settings';
    }
    $args = array('page' => $from);
    if ($ok) {
        $args['tll_done'] = '1';
    } else {
        $args['tll_fail'] = '1';
    }
    wp_safe_redirect(add_query_arg($args, admin_url('admin.php')));
    exit;
}

/* ===================== 详情页：评论 / 下载 / 内页热门条 ===================== */

/**
 * 前台内容类型（文章/软件/网址/书籍）评论一律开放，
 * 解决演示数据 comment_status=closed 导致评论框不显示的问题。
 * admin-ajax 请求也需放行（AJAX 提交评论时 is_admin() 为 true）。
 */
add_filter('comments_open', function ($open, $post_id) {
    if (is_admin() && !wp_doing_ajax()) {
        return $open;
    }
    if (in_array(get_post_type($post_id), array('post', 'app', 'sites', 'book'), true)) {
        return true;
    }
    return $open;
}, 20, 2);

/**
 * 递归渲染评论树（结构风格与 OneNav 评论模块一致）
 */
function theme_render_comment_level($by_parent, $parent, $depth) {
    if (empty($by_parent[$parent])) {
        return;
    }
    foreach ($by_parent[$parent] as $c) {
        $avatar_url = get_avatar_url($c->comment_author_email, array(
            'size'    => 96,
            'default' => get_template_directory_uri() . '/assets/images/gravatar.jpg',
        ));
        $is_author = $c->user_id && $c->user_id == get_post_field('post_author', $c->comment_post_ID);
        ?>
        <li id="comment-<?php echo (int) $c->comment_ID; ?>" class="comment depth-<?php echo (int) $depth; ?>">
            <div class="comment-body d-flex py-1">
                <div class="comment-avatar mr-3">
                    <img class="avatar rounded-circle" src="<?php echo esc_url($avatar_url); ?>" width="44" height="44" alt="<?php echo esc_attr($c->comment_author); ?>">
                </div>
                <div class="comment-content flex-fill text-sm">
                    <div class="comment-head d-flex align-items-center flex-wrap">
                        <b class="comment-author"><?php echo esc_html($c->comment_author); ?><?php if ($is_author) : ?><span class="badge vc-l-theme text-ss ml-1">作者</span><?php endif; ?></b>
                        <span class="text-muted text-xs ml-2"><?php echo esc_html(get_comment_date('Y年n月j日 H:i', $c)); ?></span>
                        <a href="javascript:;" class="comment-reply-link btn btn-sm vc-l-gray py-0 px-2 ml-auto text-xs" data-id="<?php echo (int) $c->comment_ID; ?>" data-name="<?php echo esc_attr($c->comment_author); ?>"><i class="iconfont icon-comment mr-1"></i>回复</a>
                    </div>
                    <div class="comment-text mt-1"><?php echo wp_kses(make_clickable(nl2br(esc_html($c->comment_content))), array('a' => array('href' => array(), 'rel' => array(), 'target' => array()), 'br' => array())); ?></div>
                </div>
            </div>
            <?php if (!empty($by_parent[$c->comment_ID])) : ?>
            <ol class="children">
                <?php theme_render_comment_level($by_parent, $c->comment_ID, $depth + 1); ?>
            </ol>
            <?php endif; ?>
        </li>
        <?php
    }
}

function theme_render_comment_tree($post_id) {
    $comments = get_comments(array(
        'post_id'       => $post_id,
        'status'        => 'approve',
        'orderby'       => 'comment_date_gmt',
        'order'         => 'ASC',
        'no_found_rows' => true,
        'update_comment_meta_cache' => false,
    ));
    if (empty($comments)) {
        return '';
    }
    $by_parent = array();
    foreach ($comments as $c) {
        $pid = (int) $c->comment_parent;
        if ($pid && !array_key_exists($pid, $comments) && !wp_get_comment($pid)) {
            // 父评论不存在或未通过时挂到顶层
            $pid = 0;
        }
        $by_parent[$pid][] = $c;
    }
    ob_start();
    echo '<ol class="comment-list">';
    theme_render_comment_level($by_parent, 0, 1);
    echo '</ol>';
    return ob_get_clean();
}

/* AJAX：加载评论列表 */
add_action('wp_ajax_theme_load_comments', 'theme_ajax_load_comments');
add_action('wp_ajax_nopriv_theme_load_comments', 'theme_ajax_load_comments');
function theme_ajax_load_comments() {
    $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
    if (!$post_id || !get_post($post_id)) {
        wp_send_json_error(array('msg' => '参数错误'));
    }
    wp_send_json_success(array(
        'count' => (int) get_comments_number($post_id),
        'html'  => theme_render_comment_tree($post_id),
    ));
}

/* AJAX：提交评论（对应目标站 action=ajax_comment 的自实现版） */
add_action('wp_ajax_theme_ajax_comment', 'theme_ajax_submit_comment');
add_action('wp_ajax_nopriv_theme_ajax_comment', 'theme_ajax_submit_comment');
function theme_ajax_submit_comment() {
    check_ajax_referer('theme_comment_nonce', '_wpnonce');

    $post_id = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;
    $post = $post_id ? get_post($post_id) : null;
    if (!$post || !in_array($post->post_type, array('post', 'app', 'sites', 'book'), true)) {
        wp_send_json_error(array('msg' => '内容不存在'));
    }
    if (!comments_open($post_id)) {
        wp_send_json_error(array('msg' => '评论已关闭'));
    }

    $content = trim(wp_unslash($_POST['comment'] ?? ''));
    $content = preg_replace('/\s{3,}/u', '  ', $content);
    if ($content === '' || mb_strlen($content) < 2) {
        wp_send_json_error(array('msg' => '请输入至少 2 个字的评论内容'));
    }
    if (mb_strlen($content) > 2000) {
        wp_send_json_error(array('msg' => '评论内容不能超过 2000 字'));
    }

    $parent = isset($_POST['comment_parent']) ? (int) $_POST['comment_parent'] : 0;
    if ($parent) {
        $parent_comment = get_comment($parent);
        if (!$parent_comment || (int) $parent_comment->comment_post_ID !== $post_id) {
            $parent = 0;
        }
    }

    $user = wp_get_current_user();
    if ($user->exists()) {
        $author = $user->display_name;
        $email = $user->user_email;
    } else {
        $author = sanitize_text_field(wp_unslash($_POST['author'] ?? ''));
        $email = sanitize_text_field(wp_unslash($_POST['email'] ?? ''));
        if (mb_strlen($author) < 1 || mb_strlen($author) > 30) {
            wp_send_json_error(array('msg' => '请填写昵称（30字以内）'));
        }
        if (!is_email($email)) {
            wp_send_json_error(array('msg' => '请填写正确的邮箱地址'));
        }
        // 邮箱仅用于头像，不允许伪造他人管理员身份展示由 WP 评论逻辑自然处理
    }

    // 简易防灌水：同 IP 同内容 60 秒内不可重复提交
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $flood_key = 'theme_cmf_' . md5($ip . '|' . mb_substr($content, 0, 60));
    if (get_transient($flood_key)) {
        wp_send_json_error(array('msg' => '您评论得太快了，请稍后再试'));
    }
    set_transient($flood_key, 1, 60);

    $comment_id = wp_insert_comment(array(
        'comment_post_ID'      => $post_id,
        'comment_content'      => $content,
        'comment_parent'       => $parent,
        'comment_approved'     => 1,
        'comment_author'       => $author,
        'comment_author_email' => $email,
        'comment_author_url'   => '',
        'comment_author_IP'    => $ip,
        'comment_agent'        => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 254),
        'comment_date'         => current_time('mysql'),
        'comment_type'         => '',
        'user_id'              => get_current_user_id(),
    ));
    if (!$comment_id) {
        delete_transient($flood_key);
        wp_send_json_error(array('msg' => '评论发表失败，请稍后重试'));
    }

    wp_send_json_success(array(
        'count'        => (int) get_comments_number($post_id),
        'html'         => theme_render_comment_tree($post_id),
        'comment_id'   => (int) $comment_id,
    ));
}

/**
 * 下载地址字段解析：支持 "URL"、"URL|提取码"、"URL 提取码: abcd"
 */
function theme_parse_down_raw($raw) {
    $raw = trim(html_entity_decode((string) $raw, ENT_QUOTES));
    $url = '';
    $pwd = '';
    if (preg_match('/https?:\/\/[^\s|，,]+/i', $raw, $m)) {
        $url = rtrim($m[0], '.。;；');
        $rest = trim(substr($raw, strpos($raw, $m[0]) + strlen($m[0])));
        if ($rest !== '' && preg_match('/([a-zA-Z0-9]{2,12})/', $rest, $pm)) {
            $pwd = $pm[1];
        }
    } elseif (filter_var($raw, FILTER_VALIDATE_URL)) {
        $url = $raw;
    }
    return array('url' => $url, 'pwd' => $pwd);
}

/* AJAX：软件下载弹窗（按钮 data-action=get_app_down_btn，由 main.min.js 的 io-ajax-modal 调用） */
add_action('wp_ajax_get_app_down_btn', 'theme_ajax_get_app_down_btn');
add_action('wp_ajax_nopriv_get_app_down_btn', 'theme_ajax_get_app_down_btn');
function theme_ajax_get_app_down_btn() {
    $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
    if (!$post_id || get_post_type($post_id) !== 'app') {
        wp_die('参数错误');
    }
    $version = theme_get_app_version($post_id);
    $items = array();
    foreach (array(
        array('meta' => 'app_down_pc', 'label' => 'PC 版本'),
        array('meta' => 'app_down_mac', 'label' => 'MAC 版本'),
        array('meta' => 'app_down_android', 'label' => '安卓版本'),
        array('meta' => 'app_down_ios', 'label' => 'IOS 版本'),
    ) as $f) {
        $raw = trim((string) get_post_meta($post_id, $f['meta'], true));
        if ($raw === '') {
            continue;
        }
        $parsed = theme_parse_down_raw($raw);
        if ($parsed['url']) {
            $items[] = array_merge($parsed, array('label' => $f['label']));
        }
    }
    // 兼容旧的单一下载地址 download_url
    if (!$items) {
        $parsed = theme_parse_down_raw(theme_get_download_url($post_id));
        if ($parsed['url']) {
            $items[] = array_merge($parsed, array('label' => '官方下载'));
        }
    }
    ?>
<div class="modal-header py-2 modal-header-simple vc-blue"><span></span><div class="text-md"><i class="iconfont icon-down mr-2"></i><span class="text-sm"><?php echo esc_html(get_the_title($post_id)); ?><?php if ($version) : ?> - <?php echo esc_html($version); ?><?php endif; ?></span></div><button type="button" class="close io-close" data-dismiss="modal" aria-label="Close"><i class="iconfont icon-close text-lg" aria-hidden="true"></i></button></div>
<div class="p-4">
    <?php if ($items) : ?>
    <div class="row">
        <div class="col-6 col-md-7">描述</div>
        <div class="col-2 col-md-2" style="white-space:nowrap;">提取码</div>
        <div class="col-4 col-md-3 text-right">下载</div>
    </div>
    <div class="col-12 -line- my-2" style="height:1px;background:rgba(136,136,136,.4)"></div>
    <div class="down_btn_list mb-4">
        <?php foreach ($items as $idx => $item) :
            $go = home_url('/go/?url=' . urlencode(base64_encode($item['url'])) . '&dl_id=' . $post_id);
        ?>
        <div class="row">
            <div class="col-6 col-md-7"><?php echo esc_html($item['label']); ?></div>
            <div class="col-2 col-md-2" style="white-space:nowrap;"><?php echo $item['pwd'] ? esc_html($item['pwd']) : '无'; ?></div>
            <div class="col-4 col-md-3 text-right"><a class="btn vc-l-theme py-0 px-1 mx-auto copy-data text-sm" href="<?php echo esc_url($go); ?>" target="_blank" rel="external nofollow noopener" data-id="<?php echo (int) $post_id; ?>" data-mmid="down-mm-<?php echo (int) $idx; ?>"><?php echo esc_html($item['label']); ?></a></div>
        </div>
        <?php if ($idx < count($items) - 1) : ?>
        <div class="col-12 -line- my-2" style="height:1px;background:rgba(136,136,136,.2)"></div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <div class="tips-box vc-l-yellow w-100 text-left text-xs mb-3 py-2"><i class="iconfont icon-tishi mr-1"></i>安全提示：请通过官方渠道下载软件，安装时注意辨别捆绑与钓鱼链接</div>
    <?php else : ?>
    <div class="col-1a-i nothing-box nothing-type-none">
        <div class="nothing">
            <svg class="nothing-svg" viewBox="0 0 220 170" xmlns="http://www.w3.org/2000/svg" style="max-width:160px">
                <ellipse cx="110" cy="148" rx="82" ry="10" fill="#eef1f6"/>
                <circle cx="110" cy="78" r="52" fill="#fff" stroke="#d9deea" stroke-width="3"/>
                <text x="110" y="96" font-size="48" text-anchor="middle" fill="#9aa6bf" font-weight="bold">···</text>
            </svg>
            <div class="nothing-msg text-sm text-muted">暂未提供下载地址</div>
        </div>
    </div>
    <?php endif; ?>
    <div class="tips-box vc-l-blue text-left text-sm" role="alert"><i class="iconfont icon-statement mr-2"></i><strong>声明：</strong>本站大部分下载资源收集于网络，只做学习和交流使用，版权归原作者所有。若您需要使用非免费的软件或服务，请购买正版授权并合法使用。本站发布的内容若侵犯到您的权益，请联系站长删除，我们将及时处理。</div>
</div>
    <?php
    wp_die();
}

/**
 * 热门广告/网址条（首页与内页共用，对应目标站 .auto-ad-url）
 * 付费入驻广告优先，热门网址补位，空位用 auto-list-null 占位
 */
function theme_render_hot_ad_bar($wrap_class = 'my-3 my-md-5') {
    $favicon_service = (get_option('theme_settings'))['theme_favicon_url'] ?? 'https://www.google.com/s2/favicons?domain={domain}&sz=32';
    ?>
<div class="auto-ad-url <?php echo esc_attr($wrap_class); ?>">
    <div class="card my-0 mx-auto">
        <div class="card-head d-flex align-items-center pb-0 px-2 pt-2">
            <div class="text-sm"><i class="iconfont icon-hot mr-2"></i>热门</div>
            <?php if (!empty(theme_get_ad_settings()['enable'])) : ?>
            <a href="javascript:;" class="btn vc-yellow btn-outline btn-sm py-0 ml-auto" id="ad-join-btn"><i class="iconfont icon-ad-copy mr-2"></i>立即入驻</a>
            <?php endif; ?>
        </div>
        <div class="card-body pt-1 pb-1 px-2 posts-row row-col-3a row-col-md-6a">
            <?php
            $slot_index = 0;
            foreach (theme_get_active_ads(6) as $ad) :
                $slot_index++;
                $ad_domain = wp_parse_url($ad['url'], PHP_URL_HOST);
                $ad_icon = $ad_domain ? str_replace('{domain}', rawurlencode($ad_domain), $favicon_service) : '';
                $ad_go = home_url('/go/?url=' . urlencode(base64_encode($ad['url'])));
            ?>
            <div class="auto-list auto-list-<?php echo $slot_index; ?>">
                <a href="<?php echo esc_url($ad_go); ?>" class="d-flex btn on-border align-items-center auto-url-list px-2 py-1" target="_blank" rel="external nofollow sponsored" title="<?php echo esc_attr($ad['name']); ?>">
                    <div class="auto-ad-img rounded-circle overflow-hidden">
                        <img src="<?php echo esc_url($ad_icon); ?>" height="21" width="21" alt="<?php echo esc_attr($ad['name']); ?>" loading="lazy">
                    </div>
                    <div class="auto-ad-name text-sm ml-1 ml-md-2 line1"><?php echo esc_html($ad['name']); ?><span class="ad-badge">Ad</span></div>
                </a>
            </div>
            <?php endforeach; ?>
            <?php
            $remain = 6 - $slot_index;
            if ($remain > 0) :
                $hot_sites = theme_get_top_sites($remain);
                while ($hot_sites->have_posts()) : $hot_sites->the_post();
                    $slot_index++;
            ?>
            <div class="auto-list auto-list-<?php echo $slot_index; ?>">
                <a href="<?php the_permalink(); ?>" class="d-flex btn on-border align-items-center auto-url-list px-2 py-1" target="_blank" rel="external" title="<?php the_title_attribute(); ?>">
                    <div class="auto-ad-img rounded-circle overflow-hidden">
                        <?php
                        $hot_fav = function_exists('theme_get_local_favicon') ? theme_get_local_favicon(get_the_ID()) : '';
                        if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" height="21" width="21" alt="<?php the_title_attribute(); ?>">
                        <?php elseif ($hot_fav) : ?>
                            <img src="<?php echo esc_url($hot_fav); ?>" height="21" width="21" alt="<?php the_title_attribute(); ?>" loading="lazy">
                        <?php else : ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(get_the_title()); ?>&background=random&color=fff&size=42" height="21" width="21" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="auto-ad-name text-sm ml-1 ml-md-2 line1"><?php the_title(); ?></div>
                </a>
            </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
            <?php endif; ?>
            <?php for ($i = $slot_index + 1; $i <= 6; $i++) : ?>
            <div class="auto-list-null">
                <div class="d-flex align-items-center auto-url-list px-2 py-1">
                    <i class="iconfont icon-ad-copy text-muted"></i>
                    <div class="auto-ad-name ml-2"></div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>
    <?php
}

/* ===================== 默认页面与导航（5.86） ===================== */

/**
 * 主题功能页面定义：slug => array(标题, 页面模板, 默认内容)
 * 模板为空字符串时使用默认页面模板
 */
function theme_default_page_defs() {
    $site_name = get_bloginfo('name');
    return array(
        'rankings'   => array('排行榜', 'page-rankings.php', ''),
        'hotnews'    => array('今日热点', 'page-hotnews.php', ''),
        'iotags'     => array('快速筛选', 'page-iotags.php', ''),
        'bookmark'   => array('新标签页', 'page-bookmark.php', ''),
        'secondary'  => array('次级导航', 'page-secondary.php', ''),
        'contribute' => array('投稿收录', 'page-contribute.php', "把你的发现记录下来，让每一份灵感与收获都成为你成长的基石。\n\n欢迎向{$site_name}投递优质网址、文章、软件与书籍资源，收录后将在对应分类展示。"),
        'jitang'     => array('金句', 'page-jitang.php', ''),
        'about'      => array('关于我们', '', "<h2>关于{$site_name}</h2><p>{$site_name}是一个专注于收集和分享优质互联网资源的网址导航站，内容涵盖常用工具、设计开发、社区资讯、软件下载、书籍期刊等。</p><p>如有任何问题或合作意向，欢迎通过页面底部的联系方式与我们取得联系。</p>"),
        'links'      => array('友链申请', '', "<h2>友链申请</h2><p>欢迎内容健康、长期稳定更新的网站与本站交换友情链接。</p><p><strong>申请要求：</strong></p><ul><li>网站内容合法合规，无恶意广告与跳转；</li><li>正常访问、持续更新，原则上已收录一定数量内容；</li><li>先将本站加入贵站友链后再提交申请。</li></ul><p><strong>本站信息：</strong>名称：{$site_name}；地址：" . home_url() . "</p><p>申请请发送邮件至站长邮箱，注明网站名称、地址与简介，审核通过后我们会尽快上线。</p>"),
        'disclaimer' => array('免责声明', '', "<h2>免责声明</h2><p>1. 本站仅收集整理互联网公开的网站与资源链接，所有资源版权均归原网站及权利人所有。</p><p>2. 本站不对第三方网站内容的真实性、合法性负责，通过本站链接访问外部网站产生的任何后果与本站无关。</p><p>3. 如发现收录内容侵犯了您的权益，请联系站长，我们将在核实后第一时间删除相关链接。</p><p>4. 本站部分内容来自网络投稿与转载，如有侵权请及时告知，我们会在 24 小时内处理。</p>"),
        'ad'         => array('广告合作', '', "<h2>广告合作</h2><p>本站提供首页热门推荐广告位等多种合作形式，支持按周/月/季等时长投放。</p><p>广告内容须合法合规，具体价格与投放流程请查看首页「立即入驻」入口，或直接联系站长洽谈。</p>"),
    );
}

/**
 * 幂等创建主题功能页面（已存在的同名/同别名页面不重复创建）
 */
function theme_create_default_pages() {
    foreach (theme_default_page_defs() as $slug => $def) {
        list($title, $template, $content) = $def;
        $existing = get_posts(array(
            'post_type'      => 'page',
            'name'           => $slug,
            'post_status'    => array('publish', 'draft', 'pending', 'trash'),
            'posts_per_page' => 1,
            'no_found_rows'  => true,
        ));
        if (!empty($existing)) {
            if ($template && get_post_meta($existing[0]->ID, '_wp_page_template', true) !== $template) {
                update_post_meta($existing[0]->ID, '_wp_page_template', $template);
            }
            continue;
        }
        $page_id = wp_insert_post(array(
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ));
        if ($page_id && !is_wp_error($page_id) && $template) {
            update_post_meta($page_id, '_wp_page_template', $template);
        }
    }
}

/**
 * 未分配顶部菜单时，创建并绑定默认菜单（已分配或菜单已有内容时不动用户数据）
 */
function theme_create_default_menu() {
    $locations = get_nav_menu_locations();
    if (!empty($locations['header'])) {
        return;
    }
    $menu = wp_get_nav_menu_object('顶部导航');
    if ($menu) {
        $menu_id = $menu->term_id;
        if (wp_get_nav_menu_items($menu_id)) {
            set_theme_mod('nav_menu_locations', array_merge($locations, array('header' => $menu_id)));
            return;
        }
    } else {
        $menu_id = wp_create_nav_menu('顶部导航');
        if (is_wp_error($menu_id)) {
            return;
        }
    }
    wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title'  => '首页',
        'menu-item-url'    => home_url('/'),
        'menu-item-status' => 'publish',
    ));
    $menu_pages = array('rankings' => '排行榜', 'hotnews' => '今日热点', 'iotags' => '快速筛选', 'contribute' => '投稿收录', 'about' => '关于我们');
    foreach ($menu_pages as $slug => $label) {
        $page = get_page_by_path($slug);
        if ($page) {
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title'     => $label,
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page->ID,
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ));
        }
    }
    set_theme_mod('nav_menu_locations', array_merge($locations, array('header' => $menu_id)));
}

/**
 * 顶部菜单兜底：后台未分配菜单时输出默认导航，避免导航空白
 */
function theme_default_header_menu($args = array()) {
    $items = array(array(home_url('/'), '首页'));
    foreach (array('rankings' => '排行榜', 'hotnews' => '今日热点', 'iotags' => '快速筛选', 'contribute' => '投稿收录', 'about' => '关于我们') as $slug => $label) {
        $items[] = array(home_url('/' . $slug . '/'), $label);
    }
    $menu_class = isset($args->menu_class) ? $args->menu_class : 'nav navbar-nav';
    echo '<ul class="' . esc_attr($menu_class) . '">';
    foreach ($items as $item) {
        echo '<li class="menu-item"><a href="' . esc_url($item[0]) . '">' . esc_html($item[1]) . '</a></li>';
    }
    echo '</ul>';
}

/* ===================== 排行榜 ===================== */

function theme_ranking_types() {
    return array(
        'sites' => array('post_type' => 'sites', 'views_key' => 'site_views', 'label' => '网址排行榜'),
        'post'  => array('post_type' => 'post',  'views_key' => 'post_views', 'label' => '文章排行榜'),
        'book'  => array('post_type' => 'book',  'views_key' => 'post_views', 'label' => '书籍排行榜'),
        'app'   => array('post_type' => 'app',   'views_key' => 'post_views', 'label' => '软件排行榜'),
    );
}

/**
 * 取排行榜数据
 *
 * @return array posts(WP_Post[])、found、page、per_page、views_map(post_id=>区间浏览量)
 */
function theme_get_ranking_items($type = 'sites', $range = 'today', $page = 1, $per_page = 20) {
    $types = theme_ranking_types();
    if (!isset($types[$type])) {
        $type = 'sites';
    }
    if (!in_array($range, array('today', 'week', 'month', 'all'), true)) {
        $range = 'today';
    }
    $page = max(1, (int) $page);
    $per_page = max(1, min(50, (int) $per_page));
    $post_type = $types[$type]['post_type'];
    $views_key = $types[$type]['views_key'];

    if ($range === 'all') {
        $q = new WP_Query(array(
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'meta_value_num',
            'meta_key'       => $views_key,
            'order'          => 'DESC',
            'ignore_sticky_posts' => 1,
        ));
        $views_map = array();
        foreach ($q->posts as $p) {
            $views_map[$p->ID] = (int) get_post_meta($p->ID, $views_key, true);
        }
        return array(
            'posts'    => $q->posts,
            'found'    => (int) $q->found_posts,
            'page'     => $page,
            'per_page' => $per_page,
            'views_map' => $views_map,
        );
    }

    // 日/周/月榜：按每日浏览量日志汇总后排序
    $today = current_time('Y-m-d');
    $days_back = ($range === 'today') ? 0 : (($range === 'week') ? 6 : 29);
    $dates = array();
    for ($i = 0; $i <= $days_back; $i++) {
        $dates[] = date('Y-m-d', strtotime($today . " -{$i} days"));
    }
    $candidate_ids = get_posts(array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => 2000,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'meta_key'       => '_theme_daily_views',
    ));
    $scores = array();
    foreach ($candidate_ids as $cid) {
        $log = get_post_meta($cid, '_theme_daily_views', true);
        if (!is_array($log)) {
            continue;
        }
        $sum = 0;
        foreach ($dates as $d) {
            if (!empty($log[$d])) {
                $sum += (int) $log[$d];
            }
        }
        if ($sum > 0) {
            $scores[$cid] = $sum;
        }
    }
    arsort($scores);
    // 日志缺失时（新站）退化为总榜，保证页面有内容
    if (empty($scores)) {
        $fallback = get_posts(array(
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => 2000,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'orderby'        => 'meta_value_num',
            'meta_key'       => $views_key,
            'order'          => 'DESC',
        ));
        foreach ($fallback as $fid) {
            $scores[$fid] = (int) get_post_meta($fid, $views_key, true);
        }
    }
    $found = count($scores);
    $offset = ($page - 1) * $per_page;
    $page_ids = array_slice(array_keys($scores), $offset, $per_page);
    $posts = array_map('get_post', $page_ids);
    return array(
        'posts'    => $posts,
        'found'    => $found,
        'page'     => $page,
        'per_page' => $per_page,
        'views_map' => array_intersect_key($scores, array_flip($page_ids)),
    );
}

/**
 * 排行榜单条条目（与目标站 .posts-item.sites-item.style-sites-default 结构一致）
 */
function theme_render_ranking_row($post, $rank, $type, $views = 0) {
    $pid = $post->ID;
    $fav_url = ($type === 'sites' && function_exists('theme_get_local_favicon')) ? theme_get_local_favicon($pid) : '';
    if (has_post_thumbnail($pid)) {
        $icon = get_the_post_thumbnail_url($pid, 'thumbnail');
    } elseif ($fav_url) {
        $icon = $fav_url;
    } else {
        $icon = 'https://ui-avatars.com/api/?name=' . urlencode($post->post_title) . '&background=random&color=fff&size=64';
    }
    $rank_class = $rank === 1 ? ' vc-l-red' : ($rank === 2 ? ' vc-l-yellow' : ($rank === 3 ? ' vc-l-purple' : ''));
    $permalink = get_permalink($pid);
    $site_url = ($type === 'sites') ? theme_get_site_url($pid) : '';
    ?>
    <div class="posts-item sites-item d-flex style-sites-default post-<?php echo (int) $pid; ?>">
        <span class="hotapi-rank rank-num <?php echo esc_attr($rank_class); ?> d-flex align-items-center justify-content-center mr-2"><?php echo (int) $rank; ?></span>
        <a href="<?php echo esc_url($permalink); ?>" data-id="<?php echo (int) $pid; ?>"<?php echo $site_url ? ' data-url="' . esc_url($site_url) . '"' : ''; ?> class="sites-body flex-fill" title="<?php echo esc_attr($post->post_title); ?>">
            <div class="item-header">
                <div class="item-media">
                    <div class="blur-img-bg lazy-bg"<?php echo $fav_url ? ' data-bg="' . esc_url($fav_url) . '"' : ''; ?>></div>
                    <div class="item-image">
                        <img class="fill-cover sites-icon lazy unfancybox" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiPjwvc3ZnPg==" data-src="<?php echo esc_url($icon); ?>" height="auto" width="auto" alt="<?php echo esc_attr($post->post_title); ?>">
                    </div>
                </div>
            </div>
            <div class="item-body overflow-hidden d-flex flex-column flex-fill">
                <h3 class="item-title line1"><b><?php echo esc_html($post->post_title); ?></b></h3>
                <div class="line1 text-muted text-xs"><?php echo esc_html(wp_trim_words($post->post_content, 16)); ?></div>
                <div class="meta-ico text-muted text-xs">
                    <span class="meta-view"><i class="iconfont icon-chakan-line"></i><?php echo esc_html(theme_format_num($views)); ?></span>
                </div>
            </div>
        </a>
    </div>
    <?php
}

add_action('wp_ajax_theme_ranking_list', 'theme_ajax_ranking_list');
add_action('wp_ajax_nopriv_theme_ranking_list', 'theme_ajax_ranking_list');
function theme_ajax_ranking_list() {
    $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'sites';
    $range = isset($_POST['range']) ? sanitize_key($_POST['range']) : 'today';
    $page = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;
    $data = theme_get_ranking_items($type, $range, $page, 20);
    ob_start();
    $rank = ($data['page'] - 1) * $data['per_page'] + 1;
    foreach ($data['posts'] as $p) {
        theme_render_ranking_row($p, $rank++, $type, isset($data['views_map'][$p->ID]) ? $data['views_map'][$p->ID] : 0);
    }
    $html = ob_get_clean();
    wp_send_json_success(array(
        'html'     => $html,
        'has_more' => ($data['page'] * $data['per_page']) < $data['found'],
        'page'     => $data['page'],
        'found'    => $data['found'],
    ));
}

/* ===================== AI 助手 ===================== */

add_action('wp_ajax_theme_ai_chat', 'theme_ajax_ai_chat');
add_action('wp_ajax_nopriv_theme_ai_chat', 'theme_ajax_ai_chat');
function theme_ajax_ai_chat() {
    check_ajax_referer('theme_ajax_nonce', 'nonce');
    $options = get_option('theme_settings');
    if (empty($options['theme_ai_on'])) {
        wp_send_json_error(array('msg' => 'AI助手暂未开启'));
    }
    $api_url = isset($options['theme_ai_api_url']) ? trim($options['theme_ai_api_url']) : '';
    $api_key = isset($options['theme_ai_api_key']) ? trim($options['theme_ai_api_key']) : '';
    $model   = isset($options['theme_ai_model']) ? trim($options['theme_ai_model']) : '';
    if ($api_url === '') {
        wp_send_json_error(array('msg' => 'AI接口地址未配置'));
    }
    $raw_messages = isset($_POST['messages']) ? wp_unslash($_POST['messages']) : array();
    if (!is_array($raw_messages)) {
        $raw_messages = array();
    }
    $messages = array();
    foreach (array_slice($raw_messages, -20) as $msg) {
        if (!is_array($msg)) {
            continue;
        }
        $role = isset($msg['role']) && in_array($msg['role'], array('user', 'assistant'), true) ? $msg['role'] : 'user';
        $content = isset($msg['content']) ? wp_strip_all_tags((string) $msg['content']) : '';
        $content = trim(preg_replace('/\s+/u', ' ', $content));
        if ($content !== '') {
            $messages[] = array('role' => $role, 'content' => function_exists('mb_substr') ? mb_substr($content, 0, 1000) : substr($content, 0, 1000));
        }
    }
    if (empty($messages)) {
        wp_send_json_error(array('msg' => '请输入内容'));
    }
    array_unshift($messages, array(
        'role'    => 'system',
        'content' => '你是「' . get_bloginfo('name') . '」网址导航站的AI助手，用简洁友好的中文回答用户问题，可解答疑问、提供建议、协助创作。',
    ));
    $body = array(
        'model'       => $model ?: 'gpt-4o-mini',
        'messages'    => $messages,
        'temperature' => 0.7,
        'stream'      => false,
    );
    $headers = array('Content-Type' => 'application/json', 'Timeout' => 60);
    if ($api_key !== '') {
        $headers['Authorization'] = 'Bearer ' . $api_key;
    }
    $response = wp_remote_post($api_url, array(
        'headers' => $headers,
        'body'    => wp_json_encode($body),
        'timeout' => 60,
    ));
    if (is_wp_error($response)) {
        wp_send_json_error(array('msg' => 'AI服务连接失败：' . $response->get_error_message()));
    }
    $code = (int) wp_remote_retrieve_response_code($response);
    $result = json_decode(wp_remote_retrieve_body($response), true);
    if ($code !== 200 || !is_array($result)) {
        $err_msg = isset($result['error']['message']) ? $result['error']['message'] : ('AI服务异常（HTTP ' . $code . '）');
        wp_send_json_error(array('msg' => $err_msg));
    }
    $content = isset($result['choices'][0]['message']['content']) ? trim($result['choices'][0]['message']['content']) : '';
    if ($content === '') {
        wp_send_json_error(array('msg' => 'AI未返回有效内容，请稍后再试'));
    }
    wp_send_json_success(array('content' => $content));
}
