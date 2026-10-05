<!DOCTYPE html>
<html lang="zh-Hans" class="">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta name="force-rendering" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=0.0, viewport-fit=cover">
    <title><?php wp_title('-', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <meta name="theme-color" content="#f9f9f9">
    <meta name="keywords" content="UI设计,UI设计素材,设计导航,网址导航,设计资源,创意导航,创意网站导航,设计师网址大全,设计素材大全,设计师导航,UI设计资源,优秀UI设计欣赏">
    <meta name="description" content="115啦导航 - 收集国内外优秀设计网站、UI设计资源网站、灵感创意网站、素材资源网站，定时更新分享优质产品设计书签。">
    <script>
    var __default_c = "io-grey-mode";
    var __night = document.cookie.replace(/(?:(?:^|.*;\s*)io_night_mode\s*\=\s*([^;]*).*$)|^.*$/, "$1"); 
    try {
        if (__night === "0" || (!__night && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
            document.documentElement.classList.add("io-black-mode");
        }
    } catch (_) {}
    </script>
    <?php wp_head(); ?>
    <style>:root{--main-aside-basis-width:140px;--home-max-width:1620px;--main-radius:12px;--main-max-width:1620px;}</style>
</head>
<body <?php body_class(); ?>>
<?php if (function_exists('wp_body_open')) wp_body_open(); ?>
<header class="main-header header-fixed">
    <div class="header-nav blur-bg">
        <nav class="switch-container container-header nav-top header-center d-flex align-items-center h-100 container">
            <div class="navbar-logo d-flex mr-4">
                <h1 class="text-hide position-absolute"><?php bloginfo('name'); ?></h1>
                <?php
                $options = get_option('theme_settings');
                $logo_url = $options['theme_logo'] ?? '';
                $logo_night_url = $options['theme_logo_night'] ?? '';
                $logo_switch = $options['theme_logo_switch'] ?? 1;
                $use_dual_logo = $logo_switch && $logo_night_url;
                ?>
                <a href="<?php echo home_url(); ?>" class="logo-expanded">
                    <?php if ($logo_url) : ?>
                        <?php if ($use_dual_logo) : ?>
                        <img src="<?php echo esc_url($logo_url); ?>" height="36" switch-src="<?php echo esc_url($logo_night_url); ?>" is-dark="false" alt="<?php bloginfo('name'); ?>">
                        <?php else : ?>
                        <img src="<?php echo esc_url($logo_url); ?>" height="36" alt="<?php bloginfo('name'); ?>">
                        <?php endif; ?>
                    <?php else : ?>
                        <span style="font-size:20px;font-weight:700;color:var(--theme-color,#8618db)"><?php bloginfo('name'); ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($use_dual_logo) : ?>
                <script>(function(){var img=document.querySelector('.navbar-logo img[switch-src]');if(!img)return;var dark=document.documentElement.classList.contains('io-black-mode');if(dark&&img.getAttribute('is-dark')!=='true'){var cur=img.getAttribute('src');img.setAttribute('src',img.getAttribute('switch-src'));img.setAttribute('switch-src',cur);img.setAttribute('is-dark','true');}})();</script>
                <?php endif; ?>
                <div class="more-menu-logo">
                    <div class="more-menu-list">
                        <i></i><i></i><i></i><i></i>
                    </div>
                    <div class="sub-menu">
                        <a class="menu-item" href="<?php echo home_url('/secondary'); ?>">
                            <span class="tips-box tips-icon vc-j-blue"><i class="iconfont icon-home-config"></i></span>
                            <span class="line1 text-center w-100">次级导航演示</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="navbar-header-menu">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'header',
                    'container' => false,
                    'menu_class' => 'nav navbar-header d-none d-md-flex mr-3',
                    'walker' => new Theme_Nav_Walker(),
                    'fallback_cb' => 'theme_default_header_menu',
                ));
                ?>
                <li class="menu-item io-menu-fold hide"><a href="javascript:void(0);"><i class="iconfont icon-dian"></i></a><ul class="sub-menu"></ul></li>
            </div>
            <div class="flex-fill"></div>
            <ul class="nav header-tools position-relative">
                <li class="nav-item mr-2 d-none d-xxl-block">
                    <div class="text-sm line1">
                        <div class="text-sm overflowClip_1">
                            <a href="<?php echo home_url('/jitang'); ?>" target="_blank" rel="external nofollow">
                                <span id="hitokoto">人生苦短，我用Python。</span>
                            </a>
                        </div>
                    </div>
                </li>
                <?php
                $lang_switch_on = $options['theme_lang_switch'] ?? 1;
                $lang_list = function_exists('theme_lang_list') ? theme_lang_list() : array();
                ?>
                <?php if ($lang_switch_on && $lang_list) : ?>
                <?php $lang_codes = array_keys($lang_list); ?>
                <li class="header-icon-btn nav-lang d-none d-md-block ignore">
                    <a href="javascript:;" class="lang-switch-current" data-lang="<?php echo esc_attr($lang_codes[0]); ?>" title="<?php echo esc_attr($lang_list[$lang_codes[0]]); ?>">
                        <?php echo theme_lang_flag_html($lang_codes[0]); ?>
                    </a>
                    <ul class="sub-menu mt-1 lang-switch-list">
                        <?php foreach ($lang_list as $code => $name) : ?>
                        <li><a href="javascript:;" class="lang-switch-item" data-lang="<?php echo esc_attr($code); ?>">
                            <?php echo theme_lang_flag_html($code); ?><span><?php echo esc_html($name); ?></span>
                        </a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <li class="header-icon-btn nav-login d-none d-md-block">
                    <a href="<?php echo wp_login_url(); ?>"><i class="iconfont icon-user icon-lg"></i></a>
                    <ul class="sub-menu mt-5">
                        <div class="menu-user-box">
                            <div class="nav-user-box br-lg mt-n5 fx-bg fx-shadow px-3 py-2" js-href="<?php echo wp_login_url(); ?>">
                                <div class="user-info d-flex align-items-center position-relative">
                                    <div class="avatar-img">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/gravatar.jpg" class="avatar avatar-96 photo" height="96" width="96">
                                    </div>
                                    <div class="user-right flex-fill overflow-hidden ml-2">
                                        <b>未登录</b>
                                        <div class="text-xs line1">登录后即可体验更多功能</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center user-btn">
                                <div class="d-flex justify-content-around mt-2">
                                    <button js-href="<?php echo wp_login_url(); ?>" class="btn menu-user-btn text-xs flex-fill vc-l-blue" target="_blank" rel="nofollow">
                                        <i class="iconfont icon-user"></i><span class="white-nowrap">登录</span>
                                    </button>
                                    <button js-href="<?php echo wp_registration_url(); ?>" class="btn menu-user-btn text-xs flex-fill vc-l-green" target="_blank" rel="nofollow">
                                        <i class="iconfont icon-register"></i><span class="white-nowrap">注册</span>
                                    </button>
                                    <button js-href="<?php echo wp_lostpassword_url(); ?>" class="btn menu-user-btn text-xs flex-fill vc-l-yellow" target="_blank" rel="nofollow">
                                        <i class="iconfont icon-password"></i><span class="white-nowrap">找回密码</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </ul>
                </li>
                <li class="header-icon-btn nav-search">
                    <a href="javascript:" class="search-ico-btn nav-search-icon" data-toggle-div="" data-target="#search-modal" data-z-index="101">
                        <i class="search-bar"></i>
                    </a>
                </li>
            </ul>
            <div class="d-block d-md-none menu-btn" data-toggle-div="" data-target=".mobile-nav" data-class="is-mobile" aria-expanded="false">
                <span class="menu-bar"></span><span class="menu-bar"></span><span class="menu-bar"></span>
            </div>
        </nav>
    </div>
</header>
<div class="mobile-header">
    <nav class="mobile-nav">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'header',
            'container' => false,
            'menu_class' => 'menu-nav mb-4',
            'walker' => new Theme_Nav_Walker(),
            'fallback_cb' => 'theme_default_header_menu',
        ));
        ?>
        <?php
        $mobile_lang_on = $options['theme_lang_switch'] ?? 1;
        if ($mobile_lang_on && !empty($lang_list)) :
        ?>
        <div class="mobile-lang-box mb-4 px-2">
            <select class="form-control" onchange="if(this.value&&typeof translate==='object'){translate.changeLanguage(this.value);this.value='';}">
                <option value="">🌐 切换语言 / Language</option>
                <?php foreach ($lang_list as $code => $name) : ?>
                <option value="<?php echo esc_attr($code); ?>"><?php echo esc_html($name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="menu-user-box mb-4">
            <div class="nav-user-box br-lg mt-n5 fx-bg fx-shadow px-3 py-2" js-href="<?php echo wp_login_url(); ?>">
                <div class="user-info d-flex align-items-center position-relative">
                    <div class="avatar-img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/gravatar.jpg" class="avatar avatar-96 photo" height="96" width="96">
                    </div>
                    <div class="user-right flex-fill overflow-hidden ml-2">
                        <b>未登录</b>
                        <div class="text-xs line1">登录后即可体验更多功能</div>
                    </div>
                </div>
            </div>
            <div class="text-center user-btn">
                <div class="d-flex justify-content-around mt-2">
                    <button js-href="<?php echo wp_login_url(); ?>" class="btn menu-user-btn text-xs flex-fill vc-l-blue" target="_blank" rel="nofollow">
                        <i class="iconfont icon-user"></i><span class="white-nowrap">登录</span>
                    </button>
                    <button js-href="<?php echo wp_registration_url(); ?>" class="btn menu-user-btn text-xs flex-fill vc-l-green" target="_blank" rel="nofollow">
                        <i class="iconfont icon-register"></i><span class="white-nowrap">注册</span>
                    </button>
                    <button js-href="<?php echo wp_lostpassword_url(); ?>" class="btn menu-user-btn text-xs flex-fill vc-l-yellow" target="_blank" rel="nofollow">
                        <i class="iconfont icon-password"></i><span class="white-nowrap">找回密码</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</div>