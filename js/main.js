jQuery(document).ready(function($) {
    'use strict';

    $('.search-menu').click(function() {
        $('.search-menu').removeClass('active');
        $(this).addClass('active');
        var target = $(this).data('target');
        $('.search-group').removeClass('active');
        $(target).addClass('active');
    });

    $('.search-term').click(function() {
        var value = $(this).data('value');
        var placeholder = $(this).data('placeholder');
        $('#search-text').attr('placeholder', placeholder);
        $('.search-form').attr('action', value);
        $('.search-term').removeClass('active');
        $(this).addClass('active');
    });

    $('.search-group a').click(function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        $('#search-text').val($(this).text());
        $('.search-form').attr('action', href);
        $('.search-form').submit();
    });

    $('.togo').click(function(e) {
        e.preventDefault();
        var post_id = $(this).data('id');
        var href = $(this).attr('href');
        $.ajax({
            url: theme_data.ajaxurl,
            type: 'POST',
            data: {
                action: 'increment_views',
                post_id: post_id
            },
            success: function(response) {}
        });
        if (href && href !== '#') {
            window.open(href, '_blank');
        }
    });

    $('.sites-body').click(function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var post_id = $(this).data('id');
        $.ajax({
            url: theme_data.ajaxurl,
            type: 'POST',
            data: {
                action: 'increment_views',
                post_id: post_id
            },
            success: function(response) {
                if (url) {
                    window.open(url, '_blank');
                }
            }
        });
    });

    $('a.smooth').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        if (target && target !== '#') {
            var $target = $(target);
            if ($target.length) {
                var offset = $target.offset().top - 60;
                $('html, body').animate({
                    scrollTop: offset
                }, 500);
            }
        }
    });

    $('.aside-btn.btn-outdent').click(function(e) {
        e.preventDefault();
        var $aside = $('.ioui-aside');
        var $content = $('.ioui-content');
        var $footer = $('.main-footer');
        var $icon = $(this).find('i');
        var $text = $(this).find('span');
        
        if ($aside.hasClass('aside-hide')) {
            $aside.removeClass('aside-hide');
            $content.css('margin-left', '140px');
            $footer.css('margin-left', '140px');
            $icon.removeClass('icon-expand').addClass('icon-shrink');
            $text.text('收起');
        } else {
            $aside.addClass('aside-hide');
            $content.css('margin-left', '0');
            $footer.css('margin-left', '0');
            $icon.removeClass('icon-shrink').addClass('icon-expand');
            $text.text('展开');
        }
    });

    $('.menu-btn').click(function(e) {
        e.preventDefault();
        $('.mobile-nav').toggleClass('is-mobile');
    });

    $('.header-icon-btn a').click(function(e) {
        e.stopPropagation();
    });

    $('.header-tools').click(function(e) {
        e.stopPropagation();
    });

    $(document).click(function() {
        $('.mobile-nav').removeClass('is-mobile');
    });

    $('.ioui-aside').theiaStickySidebar({
        additionalMarginTop: 80,
        additionalMarginBottom: 20
    });

    $('.sidebar-tools').theiaStickySidebar({
        additionalMarginTop: 80,
        additionalMarginBottom: 20
    });

    $(window).scroll(function() {
        if ($(window).scrollTop() > 100) {
            $('.main-header').addClass('header-scrolled');
        } else {
            $('.main-header').removeClass('header-scrolled');
        }
    });

    $('.ajax-page-post').click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var target = $this.data('target');
        var action = $this.data('action');
        var page = parseInt($this.data('page'));
        var orderby = $this.data('orderby');
        
        $.ajax({
            url: theme_data.ajaxurl,
            type: 'POST',
            data: {
                action: action,
                page: page,
                orderby: orderby
            },
            beforeSend: function() {
                $this.text('加载中...');
            },
            success: function(response) {
                $(target).append(response);
                $this.data('page', page + 1);
                $this.text('加载更多');
                new LazyLoad();
            },
            error: function() {
                $this.text('加载失败');
            }
        });
    });

    $('.list-ajax-by').click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var target = $this.data('target');
        var type = $this.data('type');
        
        $('.list-ajax-by').removeClass('active');
        $this.addClass('active');
        
        $.ajax({
            url: theme_data.ajaxurl,
            type: 'POST',
            data: {
                action: 'load_big_posts',
                page: 1,
                orderby: type
            },
            success: function(response) {
                $(target + ' .ajax-panel').html(response);
                new LazyLoad();
            }
        });
    });

    new LazyLoad();

    $(document).on('click', '.tab-title', function() {
        var term_id = $(this).closest('[id^="term-"]').attr('id').replace('term-', '');
        var url = theme_data.home_url + '/favorites/' + term_id;
        window.location.href = url;
    });

    $(document).on('click', '.btn-more', function(e) {
        e.stopPropagation();
    });

    $('.go-to-up').click(function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 300);
    });

    $(window).scroll(function() {
        if ($(window).scrollTop() > 300) {
            $('.go-to-up').addClass('show').css('display', 'flex');
        } else {
            $('.go-to-up').removeClass('show').css('display', 'none');
        }
    });

    if ($(window).scrollTop() > 300) {
        $('.go-to-up').addClass('show').css('display', 'flex');
    }

    $('.switch-dark-mode').click(function(e) {
        e.preventDefault();
        var $html = $('html');
        var $ico = $(this).find('.mode-ico');
        if ($html.hasClass('io-black-mode')) {
            $html.removeClass('io-black-mode');
            $ico.removeClass('icon-moon').addClass('icon-light');
            document.cookie = 'io_night_mode=1; path=/; max-age=' + (3600 * 24 * 30);
        } else {
            $html.addClass('io-black-mode');
            $ico.removeClass('icon-light').addClass('icon-moon');
            document.cookie = 'io_night_mode=0; path=/; max-age=' + (3600 * 24 * 30);
        }
    });

    function updateDarkModeIcon() {
        var $html = $('html');
        var $ico = $('.switch-dark-mode .mode-ico');
        if ($html.hasClass('io-black-mode')) {
            $ico.removeClass('icon-light').addClass('icon-moon');
        } else {
            $ico.removeClass('icon-moon').addClass('icon-light');
        }
    }
    updateDarkModeIcon();

    $('.btn-show-side').click(function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        var cls = $(this).data('class');
        $(target).toggleClass(cls);
    });

    if (typeof $.fn.theiaStickySidebar === 'function') {
        $('.sidebar-tools-widget').theiaStickySidebar({
            additionalMarginTop: 80,
            additionalMarginBottom: 20
        });
    }
});