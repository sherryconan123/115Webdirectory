<footer class="main-footer footer-stick">
    <div class="switch-container container-footer container">
        <div class="footer row pt-5 text-center text-md-left">
            <div class="col-12 col-md-5 my-4 my-md-0">
                <p class="footer-links text-sm mb-3">
                    <a href="<?php echo home_url('/links'); ?>">友链申请</a>
                    <a href="<?php echo home_url('/disclaimer'); ?>">免责声明</a>
                    <a href="<?php echo home_url('/ad'); ?>">广告合作</a>
                    <a href="<?php echo home_url('/about'); ?>">关于我们</a>
                </p>
                <div id="friendlink" class="mt-4 mb-3 text-left">
                    <h4 class="text-sm mb-2">友情链接</h4>
                    <div class="friend-link text-sm">
                        <?php
                        $friendlinks = get_option('theme_friendlinks', array());
                        if (empty($friendlinks)) :
                        ?>
                        <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>" target="_blank"><?php bloginfo('name'); ?></a>
                        <?php else : ?>
                        <?php foreach ($friendlinks as $link) : ?>
                        <a href="<?php echo esc_url($link['url']); ?>" title="<?php echo esc_attr($link['description']); ?>" target="_blank"><?php echo esc_html($link['name']); ?></a>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="footer-copyright m-3 text-xs">
                Copyright © <?php echo date('Y'); ?> <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>" class="" rel="home"><?php bloginfo('name'); ?></a>&nbsp;由<strong> 115theme </strong>强力驱动&nbsp;
            </div>
        </div>
    </div>
</footer>
<div class="tools-left io-footer-tools">
    <div class="btn-tools btn-show-side my-1" data-toggle-div="" data-target="#layout_aside" data-class="is-mobile"><i class="iconfont icon-expand"></i></div>
</div>
<div id="footer-tools" class="tools-right io-footer-tools d-flex flex-column">
    <a href="javascript:" class="btn-tools go-to-up go-up my-1" rel="go-up" style="display: none">
        <i class="iconfont icon-to-up"></i>
    </a>
    <a class="btn-tools custom-tool0 my-1" href="<?php echo home_url('/bookmark'); ?>" target="_blank" data-toggle="tooltip" data-placement="left" title="bookmark" rel="external noopener nofollow">
        <i class="iconfont icon-minipanel"></i>
    </a>
    <a class="btn-tools custom-tool1 my-1 qr-img" href="javascript:;" data-toggle="tooltip" data-html="true" data-placement="left" title="<img src='<?php echo get_template_directory_uri(); ?>/assets/images/qr.png' height='100' width='100'>">
        <i class="iconfont icon-wechat"></i>
    </a>
    <?php $theme_footer_options = get_option('theme_settings'); ?>
    <?php if (!empty($theme_footer_options['theme_qq_service'])) : ?>
    <a class="btn-tools custom-tool2 my-1 qq-service" href="<?php echo esc_url($theme_footer_options['theme_qq_service']); ?>" target="_blank" rel="external noopener nofollow" data-toggle="tooltip" data-placement="left" title="QQ客服">
        <i class="iconfont icon-qq"></i>
    </a>
    <?php endif; ?>
    <?php if (!empty($theme_footer_options['theme_weather_on'])) : ?>
    <div class="btn-tools btn-weather weather my-1" data-toggle="tooltip" data-placement="left" title="天气">
        <div id="io_weather_widget" class="io-weather-widget" data-locale="zh-chs" data-token="<?php echo esc_attr($theme_footer_options['theme_weather_token'] ?? 'faeb43c3-de83-4ce0-ac30-c997d962d388'); ?>"></div>
    </div>
    <?php endif; ?>
    <a href="javascript:" class="btn-tools switch-dark-mode my-1" data-toggle="tooltip" data-placement="left" title="夜间模式">
        <i class="mode-ico iconfont icon-light"></i>
    </a>
</div>
<?php if (!empty($theme_footer_options['theme_weather_on'])) : ?>
<script>(function(){var s=document.createElement('script');s.src='https://cdn.sencdn.com/widget2/static/js/bundle.js';s.defer=true;document.body.appendChild(s);})();</script>
<?php endif; ?>
<?php if (!empty($theme_footer_options['theme_ai_on'])) : ?>
<!-- AI助手 -->
<style>
.io-ai-assistant{position:fixed;right:24px;bottom:24px;z-index:1030}
.io-ai-toggle{position:relative;width:58px;height:58px;border-radius:50%;border:0;padding:0;cursor:pointer;background:transparent;outline:none}
.io-ai-siri{position:absolute;inset:0;border-radius:50%;background:linear-gradient(135deg,#6a5cff,#a14bff 55%,#ff5ca8);box-shadow:0 8px 24px rgba(122,75,255,.4)}
.io-ai-siri-spin{position:absolute;inset:-4px;border-radius:50%;border:2px dashed rgba(122,75,255,.35);animation:ioai-spin 6s linear infinite}
.io-ai-siri-blob{position:absolute;border-radius:50%;filter:blur(6px);opacity:.75;mix-blend-mode:screen}
.io-ai-siri-blob--a{width:16px;height:16px;background:#54e0ff;top:2px;left:14px;animation:ioai-blob 3s ease-in-out infinite}
.io-ai-siri-blob--b{width:12px;height:12px;background:#ff8ac4;right:4px;bottom:10px;animation:ioai-blob 3.4s ease-in-out infinite .6s}
.io-ai-siri-blob--c{width:10px;height:10px;background:#ffe27a;left:6px;bottom:6px;animation:ioai-blob 2.8s ease-in-out infinite 1.1s}
.io-ai-siri-blob--d{width:9px;height:9px;background:#9dffb0;top:12px;right:8px;animation:ioai-blob 3.8s ease-in-out infinite .3s}
.io-ai-siri-core{position:absolute;inset:15px;border-radius:50%;background:rgba(255,255,255,.92);display:flex;align-items:center;justify-content:center;color:#7a4bff;font-size:20px;box-shadow:inset 0 0 8px rgba(122,75,255,.25)}
.io-ai-siri-shine{position:absolute;top:6px;left:10px;width:16px;height:9px;background:rgba(255,255,255,.55);border-radius:50%;filter:blur(2px)}
@keyframes ioai-spin{to{transform:rotate(360deg)}}
@keyframes ioai-blob{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(4px,-5px) scale(1.25)}}
.io-ai-peek{position:absolute;right:66px;top:50%;transform:translateY(-50%);background:rgba(30,30,40,.88);color:#fff;font-size:12px;padding:6px 12px;border-radius:16px;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .25s}
.io-ai-assistant:hover .io-ai-peek{opacity:1}
.io-ai-panel{position:absolute;right:0;bottom:72px;width:360px;max-width:calc(100vw - 32px);height:520px;max-height:calc(100vh - 120px);display:none;flex-direction:column;border-radius:16px;overflow:hidden;background:var(--card-bg,#fff);box-shadow:0 16px 48px rgba(0,0,0,.18);border:1px solid var(--border-color,rgba(0,0,0,.06))}
.io-ai-assistant.is-open .io-ai-panel{display:flex}
.io-ai-assistant.is-open .io-ai-toggle{opacity:0;pointer-events:none}
.io-ai-panel-header{display:flex;align-items:center;gap:8px;padding:12px 14px;background:linear-gradient(135deg,#6a5cff,#a14bff);color:#fff}
.io-ai-panel-title{font-size:15px;font-weight:600;flex:1;display:flex;align-items:center;gap:6px}
.io-ai-panel-actions{display:flex;gap:4px}
.io-ai-panel-actions button{border:0;background:transparent;color:#fff;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:15px;opacity:.85}
.io-ai-panel-actions button:hover{background:rgba(255,255,255,.2);opacity:1}
.io-ai-panel-body{flex:1;overflow-y:auto;padding:14px;background:var(--ioai-body,#f7f7fb)}
.io-ai-welcome{font-size:14px;line-height:1.9;color:var(--text-color,#333);background:var(--card-bg,#fff);border-radius:10px;padding:12px 14px;margin-bottom:10px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
.io-ai-msg{display:flex;margin-bottom:12px;font-size:14px;line-height:1.7}
.io-ai-msg.user{justify-content:flex-end}
.io-ai-msg .bubble{max-width:82%;padding:9px 13px;border-radius:12px;word-break:break-word;white-space:pre-wrap}
.io-ai-msg.assistant .bubble{background:var(--card-bg,#fff);color:var(--text-color,#333);border-top-left-radius:3px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
.io-ai-msg.user .bubble{background:linear-gradient(135deg,#6a5cff,#a14bff);color:#fff;border-top-right-radius:3px}
.io-ai-typing{display:inline-flex;gap:4px;align-items:center;padding:4px 0}
.io-ai-typing i{width:7px;height:7px;border-radius:50%;background:#b3b3c0;animation:ioai-typing 1s infinite}
.io-ai-typing i:nth-child(2){animation-delay:.2s}.io-ai-typing i:nth-child(3){animation-delay:.4s}
@keyframes ioai-typing{0%,60%,100%{transform:translateY(0);opacity:.5}30%{transform:translateY(-4px);opacity:1}}
.io-ai-panel-footer{padding:10px 12px;border-top:1px solid var(--border-color,rgba(0,0,0,.06));background:var(--card-bg,#fff)}
.io-ai-input-wrap{display:flex;align-items:flex-end;gap:8px;background:var(--ioai-body,#f7f7fb);border-radius:18px;padding:6px 8px 6px 14px}
.io-ai-input{flex:1;border:0;outline:0;resize:none;background:transparent;max-height:96px;font-size:14px;line-height:1.5;padding:5px 0;color:var(--text-color,#333)}
.io-ai-send{border:0;width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#6a5cff,#a14bff);color:#fff;cursor:pointer;font-size:15px;flex:0 0 auto}
.io-ai-send:disabled{opacity:.4;cursor:not-allowed}
.io-ai-error{font-size:13px;color:#f56c6c}
@media(max-width:576px){.io-ai-assistant{right:16px;bottom:16px}.io-ai-panel{right:-4px}}
</style>
<div class="io-ai-assistant is-positioned pos-br" id="io-ai-assistant">
    <button type="button" class="io-ai-toggle io-ai-toggle--siri" style="width:58px;height:58px" aria-label="AI助手">
        <span class="io-ai-siri" aria-hidden="true">
            <span class="io-ai-siri-spin"></span>
            <span class="io-ai-siri-blob io-ai-siri-blob--a"></span>
            <span class="io-ai-siri-blob io-ai-siri-blob--b"></span>
            <span class="io-ai-siri-blob io-ai-siri-blob--c"></span>
            <span class="io-ai-siri-blob io-ai-siri-blob--d"></span>
            <span class="io-ai-siri-shine"></span>
            <span class="io-ai-siri-core"><i class="iconfont icon-quanzi"></i></span>
        </span>
        <span class="io-ai-peek">AI助手</span>
    </button>
    <div class="io-ai-panel" role="dialog" aria-label="AI助手">
        <div class="io-ai-panel-header">
            <div class="io-ai-panel-title"><i class="iconfont icon-quanzi"></i>AI助手</div>
            <div class="io-ai-panel-actions">
                <button type="button" class="io-ai-clear" title="清空对话"><i class="iconfont icon-delete"></i></button>
                <button type="button" class="io-ai-close" title="关闭"><i class="iconfont icon-close"></i></button>
            </div>
        </div>
        <div class="io-ai-panel-body">
            <div class="io-ai-welcome">👋 欢迎使用AI助手！我可以帮您：<br>· 解答问题<br>· 提供建议<br>· 协助创作</div>
            <div class="io-ai-messages"></div>
        </div>
        <div class="io-ai-panel-footer">
            <div class="io-ai-input-wrap">
                <textarea class="io-ai-input" rows="1" placeholder="输入问题，回车发送（Shift+回车换行）"></textarea>
                <button type="button" class="io-ai-send" disabled><i class="iconfont icon-fasong"></i></button>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) {
    var $assistant = $('#io-ai-assistant');
    var $body = $assistant.find('.io-ai-panel-body');
    var $messages = $assistant.find('.io-ai-messages');
    var $input = $assistant.find('.io-ai-input');
    var $send = $assistant.find('.io-ai-send');
    var STORE_KEY = 'theme_ai_messages';
    var history = [];
    try { history = JSON.parse(localStorage.getItem(STORE_KEY) || '[]'); } catch (e) { history = []; }
    if (!Array.isArray(history)) history = [];

    function scrollBottom() { $body.scrollTop($body[0].scrollHeight); }
    function escapeHtml(s) { return $('<i>').text(s).html(); }
    function saveHistory() {
        try { localStorage.setItem(STORE_KEY, JSON.stringify(history.slice(-20))); } catch (e) {}
    }
    function addMsg(role, content, isError) {
        var $m = $('<div class="io-ai-msg ' + role + '"></div>');
        var $b = $('<div class="bubble"></div>').text(content);
        if (isError) $b.addClass('io-ai-error');
        $m.append($b);
        $messages.append($m);
        scrollBottom();
    }
    function renderHistory() {
        $messages.empty();
        history.forEach(function(m) { addMsg(m.role, m.content); });
    }
    function setTyping(on) {
        $messages.find('.io-ai-typing-row').remove();
        if (on) {
            $messages.append('<div class="io-ai-msg assistant io-ai-typing-row"><div class="bubble"><span class="io-ai-typing"><i></i><i></i><i></i></span></div></div>');
            scrollBottom();
        }
    }
    function send() {
        var text = $input.val().trim();
        if (!text || $send.prop('disabled')) return;
        addMsg('user', text);
        history.push({role: 'user', content: text});
        saveHistory();
        $input.val('').css('height', '');
        $send.prop('disabled', true);
        setTyping(true);
        $.post((typeof theme_data !== 'undefined' ? theme_data.ajaxurl : ''), {
            action: 'theme_ai_chat',
            nonce: (typeof theme_data !== 'undefined' ? theme_data.nonce : ''),
            messages: history
        }, function(res) {
            setTyping(false);
            if (res && res.success) {
                addMsg('assistant', res.data.content);
                history.push({role: 'assistant', content: res.data.content});
                saveHistory();
            } else {
                addMsg('assistant', (res && res.data && res.data.msg) ? res.data.msg : '出了点小问题，请稍后再试', true);
            }
        }, 'json').fail(function() {
            setTyping(false);
            addMsg('assistant', '网络异常，请稍后再试', true);
        });
    }

    $assistant.find('.io-ai-toggle').on('click', function() {
        $assistant.addClass('is-open');
        setTimeout(function() { $input.focus(); }, 200);
        scrollBottom();
    });
    $assistant.find('.io-ai-close').on('click', function() {
        $assistant.removeClass('is-open');
    });
    $assistant.find('.io-ai-clear').on('click', function() {
        history = [];
        saveHistory();
        $messages.empty();
    });
    $input.on('input', function() {
        $send.prop('disabled', !$(this).val().trim());
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 96) + 'px';
    });
    $input.on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            send();
        }
    });
    $send.on('click', send);
    renderHistory();
});
</script>
<?php endif; ?>
<?php wp_footer(); ?>

<!-- 搜索弹窗 -->
<div id="search-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:rgba(0,0,0,.5);backdrop-filter:blur(4px)">
    <div class="search-modal-body" style="max-width:600px;margin:80px auto 0;padding:0 16px">
        <div class="search-modal-header text-center mb-3">
            <i class="iconfont icon-search text-xl text-white"></i>
            <button type="button" class="close-search-modal" style="position:absolute;top:20px;right:20px;color:#fff;font-size:24px;background:none;border:none;cursor:pointer">&times;</button>
        </div>
        <form action="<?php echo home_url(); ?>/" method="get" class="search-modal-form">
            <div class="d-flex">
                <input type="text" name="s" class="form-control search-modal-input" placeholder="你想了解些什么" style="border-radius:8px 0 0 8px;border:0;height:48px;font-size:16px" autocomplete="off">
                <button type="submit" class="btn vc-theme search-modal-submit" style="border-radius:0 8px 8px 0;height:48px;padding:0 24px"><i class="iconfont icon-search"></i></button>
            </div>
        </form>
        <div class="search-modal-hot text-center mt-3">
            <span class="text-white text-sm mr-2">热门搜索：</span>
            <a href="<?php echo home_url('/?s=导航'); ?>" class="badge vc-l-theme text-sm mr-1">导航</a>
            <a href="<?php echo home_url('/?s=设计'); ?>" class="badge vc-l-theme text-sm mr-1">设计</a>
            <a href="<?php echo home_url('/?s=工具'); ?>" class="badge vc-l-theme text-sm mr-1">工具</a>
        </div>
    </div>
</div>

<!-- 入驻广告弹窗 -->
<style>
.ad-badge{display:inline-block;background:#ff6d00;color:#fff;font-size:9px;line-height:1;padding:2px 3px;border-radius:3px;margin-left:4px;vertical-align:middle;font-style:normal;font-weight:400}
.ad-label{font-size:13px;font-weight:600;color:#333;margin:14px 0 8px}
.ad-loc-tag{display:inline-block;background:#fff7f0;color:#ff6d00;border:1px dashed #ffb066;border-radius:6px;padding:4px 12px;font-size:13px}
.ad-packages{display:flex;gap:8px;flex-wrap:wrap}
.ad-pkg{position:relative;flex:1;min-width:90px;cursor:pointer}
.ad-pkg input{position:absolute;opacity:0;width:0;height:0}
.ad-pkg-body{display:block;text-align:center;border:1px solid #e5e7eb;border-radius:8px;padding:10px 4px;font-size:13px;color:#666;transition:all .2s}
.ad-pkg-body b{display:block;margin-top:4px;font-size:15px;color:#ff6d00;font-weight:600}
.ad-pkg input:checked + .ad-pkg-body{border-color:#ff6d00;background:#fff7f0;color:#ff6d00;box-shadow:inset 0 0 0 1px #ff6d00}
.ad-pkg-tag{position:absolute;top:-9px;right:-4px;background:#ff6d00;color:#fff;font-size:10px;padding:1px 5px;border-radius:8px;line-height:1.4}
.ad-pkg-tag.green{background:#52c41a}
.ad-custom-hours{width:56px!important;height:26px!important;padding:2px 4px!important;text-align:center;border:1px solid #ddd;border-radius:4px;font-size:14px;display:inline-block!important;margin:0 2px}
.ad-amount{text-align:right;margin:16px 0 6px;font-size:14px;color:#333}
.ad-amount strong{color:#ff3b30;font-size:22px}
.ad-tips{font-size:12px;color:#999;margin:10px 0 14px;line-height:1.6}
#ad-modal .form-control{margin-bottom:10px;height:42px;font-size:14px}
.ad-pay-amount{font-size:15px;color:#333}
.ad-pay-amount strong{color:#ff3b30;font-size:26px}
.ad-pay-tabs{display:flex;width:240px;margin:16px auto 0;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden}
.ad-pay-tab{flex:1;padding:9px 0;border:none;background:#f7f7f7;font-size:14px;cursor:pointer;color:#666}
.ad-pay-tab.active{background:#ff6d00;color:#fff}
.ad-qr-wrap{margin:14px 0 6px;min-height:220px}
.ad-qr-wrap img{width:220px;height:220px;border:1px solid #eee;border-radius:8px}
.ad-notice{color:#ff6d00;background:#fff7f0;border-radius:6px;padding:8px 12px;display:inline-block;margin:8px 0}
.ad-modal-card{background:#fff;border-radius:12px;overflow:hidden}
.ad-modal-head{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:linear-gradient(135deg,#ff9500,#ff6d00);color:#fff}
.ad-modal-head span{font-size:16px;font-weight:600}
.ad-modal-head .close-ad-modal{color:#fff;font-size:26px;background:none;border:none;cursor:pointer;line-height:1;padding:0}
/* 首页侧栏：首屏（banner区域）隐藏，滚动越过banner后淡入显示（!important 覆盖 main.min.js 内联样式） */
@media (min-width: 768px){
  body.have-banner.has-home-banner:not(.full-container) .aside-body{opacity:0 !important}
  body.have-banner.has-home-banner.aside-pinned:not(.full-container) .aside-body{opacity:1 !important}
}
</style>
<div id="ad-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);overflow-y:auto">
    <div style="max-width:560px;margin:50px auto 40px;padding:0 16px">
        <div class="ad-modal-card">
            <!-- 视图一：填写信息 -->
            <div class="ad-panel ad-panel-form">
                <div class="ad-modal-head">
                    <span><i class="iconfont icon-ad-copy mr-2"></i>加入队列</span>
                    <button type="button" class="close-ad-modal">&times;</button>
                </div>
                <div style="padding:18px 20px 22px">
                    <div class="ad-label">入驻位置</div>
                    <span class="ad-loc-tag"><i class="iconfont icon-hot mr-1"></i>首页 · 热门推荐广告位</span>
                    <div class="ad-label">选择套餐</div>
                    <div class="ad-packages">
                        <label class="ad-pkg"><input type="radio" name="ad_hours" value="168" checked><span class="ad-pkg-body">周付<b>￥<em class="ad-pkg-price" data-hours="168">--</em></b></span></label>
                        <label class="ad-pkg"><span class="ad-pkg-tag">推荐</span><input type="radio" name="ad_hours" value="720"><span class="ad-pkg-body">月付<b>￥<em class="ad-pkg-price" data-hours="720">--</em></b></span></label>
                        <label class="ad-pkg"><input type="radio" name="ad_hours" value="2160"><span class="ad-pkg-body">季付<b>￥<em class="ad-pkg-price" data-hours="2160">--</em></b></span></label>
                        <label class="ad-pkg"><span class="ad-pkg-tag green">特惠</span><input type="radio" name="ad_hours" value="4320"><span class="ad-pkg-body">半年付<b>￥<em class="ad-pkg-price" data-hours="4320">--</em></b></span></label>
                        <label class="ad-pkg"><input type="radio" name="ad_hours" value="custom"><span class="ad-pkg-body">自定义<b><input type="number" id="ad_custom_hours" class="ad-custom-hours" value="24">小时</b></span></label>
                    </div>
                    <div class="ad-amount">应付金额：<strong>￥<span id="ad_amount">0.00</span></strong></div>
                    <input type="text" id="ad_name" class="form-control" placeholder="请输名称（网站名称）" maxlength="30">
                    <input type="url" id="ad_url" class="form-control" placeholder="请输URL（https://）">
                    <input type="email" id="ad_contact" class="form-control" placeholder="联系邮箱">
                    <p class="ad-tips"><i class="iconfont icon-notice mr-1"></i>请不要提交中华人民共和国法律所不允许的内容，发现后将立即删除，且不予退款！</p>
                    <button type="button" class="btn vc-yellow" id="ad-submit-btn" style="width:100%;height:44px;border-radius:8px;font-size:15px">立即支付</button>
                </div>
            </div>
            <!-- 视图二：扫码支付 -->
            <div class="ad-panel ad-panel-pay" style="display:none">
                <div class="ad-modal-head">
                    <span><i class="iconfont icon-ad-copy mr-2"></i>扫码支付</span>
                    <button type="button" class="close-ad-modal">&times;</button>
                </div>
                <div style="padding:22px 20px;text-align:center">
                    <div class="ad-pay-amount">支付金额：<strong>￥<span id="ad_pay_amount">0.00</span></strong></div>
                    <div class="ad-pay-tabs">
                        <button type="button" class="ad-pay-tab" data-method="wechat">微信支付</button>
                        <button type="button" class="ad-pay-tab" data-method="alipay">支付宝</button>
                    </div>
                    <div class="ad-qr-wrap">
                        <img id="ad_qr_img" src="" alt="收款二维码">
                        <p id="ad_qr_empty" class="text-muted text-sm" style="display:none;padding-top:80px">站长暂未配置该收款方式，<br>请切换其他支付方式</p>
                    </div>
                    <p class="text-sm text-muted mb-1">订单号：<span id="ad_order_id"></span></p>
                    <p class="ad-notice text-sm" id="ad_notice" style="display:none"></p>
                    <p class="text-sm text-muted">请扫码完成支付，支付后点击下方按钮</p>
                    <button type="button" class="btn vc-theme" id="ad-paid-btn" style="width:100%;height:44px;border-radius:8px;font-size:15px">我已完成支付</button>
                    <p style="margin-top:10px"><a href="javascript:;" class="ad-back-form text-sm">返回修改信息</a></p>
                </div>
            </div>
            <!-- 视图三：提交完成 -->
            <div class="ad-panel ad-panel-done" style="display:none">
                <div class="ad-modal-head">
                    <span><i class="iconfont icon-ad-copy mr-2"></i>提交成功</span>
                    <button type="button" class="close-ad-modal">&times;</button>
                </div>
                <div style="padding:40px 20px;text-align:center">
                    <div style="width:72px;height:72px;border-radius:50%;background:#f6ffed;border:3px solid #52c41a;color:#52c41a;font-size:40px;line-height:66px;margin:0 auto">✓</div>
                    <h3 style="margin:16px 0 8px;font-size:18px">支付凭证已提交</h3>
                    <p class="text-muted text-sm" style="line-height:1.8">站长确认收款后广告将自动上架展示，<br>请保持联系邮箱畅通</p>
                    <button type="button" class="btn vc-theme close-ad-modal" style="height:40px;border-radius:8px;padding:0 36px">完成</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var AD_CFG = <?php echo wp_json_encode(theme_get_ad_settings()); ?>;
</script>
<script>
jQuery(function($) {
    // 搜索弹窗
    $('.nav-search-icon[data-target="#search-modal"]').on('click', function(e) {
        e.preventDefault();
        $('#search-modal').show();
        setTimeout(function() { $('.search-modal-input').focus(); }, 100);
    });
    $('.close-search-modal').on('click', function() { $('#search-modal').hide(); });
    $('#search-modal').on('click', function(e) { if (e.target === this) $(this).hide(); });
    $(document).on('keydown', function(e) { if (e.key === 'Escape') $('#search-modal').hide(); });

    // 首页侧栏：首屏（banner区域）隐藏，滚动越过banner后淡入
    (function() {
        var banner = document.querySelector('.header-calculate');
        if (!banner || !document.querySelector('.aside-body')) { return; }
        document.body.classList.add('has-home-banner');
        function updateAsidePin() {
            var navH = 80;
            var threshold = banner.offsetHeight - navH - 30;
            if (window.scrollY > Math.max(threshold, 120)) {
                document.body.classList.add('aside-pinned');
            } else {
                document.body.classList.remove('aside-pinned');
            }
        }
        window.addEventListener('scroll', updateAsidePin, { passive: true });
        window.addEventListener('resize', updateAsidePin, { passive: true });
        updateAsidePin();
    })();

    // 金句API
    if ($('#hitokoto').length) {
        $.ajax({
            url: 'https://v1.hitokoto.cn/?c=d&c=i&c=k&encode=json',
            dataType: 'json',
            timeout: 5000,
            success: function(res) {
                if (res && res.hitokoto) {
                    $('#hitokoto').text(res.hitokoto);
                }
            }
        });
    }

    // ===== 立即入驻（付费广告位） =====
    if (AD_CFG && AD_CFG.enable) {
        var $adModal = $('#ad-modal');
        var adOrder = null;

        function adFmt(n) { return parseFloat(n).toFixed(2); }
        function adShowPanel(name) { $adModal.find('.ad-panel').hide(); $adModal.find('.ad-panel-' + name).show(); }

        // 渲染套餐价格
        var pkgMap = {168: AD_CFG.price_week, 720: AD_CFG.price_month, 2160: AD_CFG.price_quarter, 4320: AD_CFG.price_halfyear};
        $('.ad-pkg-price').each(function() {
            $(this).text(adFmt(pkgMap[$(this).data('hours')]));
        });
        $('#ad_custom_hours').attr('min', AD_CFG.hours_min).attr('max', AD_CFG.hours_max);

        function adSelectedHours() {
            var v = $('input[name=ad_hours]:checked').val();
            if (v === 'custom') {
                var h = parseInt($('#ad_custom_hours').val(), 10) || 0;
                return Math.max(AD_CFG.hours_min, Math.min(AD_CFG.hours_max, h));
            }
            return parseInt(v, 10);
        }
        function adRefreshAmount() {
            var v = $('input[name=ad_hours]:checked').val();
            var amount = (v === 'custom') ? adSelectedHours() * AD_CFG.price_custom : pkgMap[v];
            $('#ad_amount').text(adFmt(amount));
        }
        $('input[name=ad_hours]').on('change', adRefreshAmount);
        $('#ad_custom_hours').on('focus input', function() {
            $('input[name=ad_hours][value="custom"]').prop('checked', true);
            adRefreshAmount();
        });
        adRefreshAmount();

        // 打开/关闭
        $(document).on('click', '#ad-join-btn', function(e) {
            e.preventDefault();
            adShowPanel('form');
            $adModal.show();
        });
        $adModal.on('click', function(e) { if (e.target === this) $adModal.hide(); });
        $(document).on('click', '.close-ad-modal', function() { $adModal.hide(); });
        $(document).on('keydown', function(e) { if (e.key === 'Escape') $adModal.hide(); });

        // 提交订单
        $('#ad-submit-btn').on('click', function() {
            var name = $('#ad_name').val().trim();
            var url = $.trim($('#ad_url').val());
            var contact = $('#ad_contact').val().trim();
            var hours = adSelectedHours();
            var $btn = $(this);
            if (!name) { alert('请填写网站名称'); return; }
            if (!/^https?:\/\/.+/i.test(url)) { alert('请填写正确的网站URL（http/https开头）'); return; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contact)) { alert('请填写正确的联系邮箱'); return; }
            $btn.prop('disabled', true).text('提交中...');
            $.post(theme_data.ajaxurl, {
                action: 'theme_ad_submit',
                nonce: theme_data.nonce,
                name: name, url: url, contact: contact, hours: hours
            }, function(res) {
                $btn.prop('disabled', false).text('立即支付');
                if (res && res.success) {
                    adOrder = res.data;
                    $('#ad_pay_amount').text(res.data.amount);
                    $('#ad_order_id').text(res.data.order_id);
                    if (res.data.notice) { $('#ad_notice').text(res.data.notice).show(); } else { $('#ad_notice').hide(); }
                    var method = res.data.wechat ? 'wechat' : 'alipay';
                    $('.ad-pay-tab').removeClass('active');
                    $('.ad-pay-tab[data-method="' + method + '"]').addClass('active');
                    adSwitchQr(method);
                    adShowPanel('pay');
                } else {
                    alert((res && res.data && res.data.msg) || '提交失败，请稍后重试');
                }
            }).fail(function() {
                $btn.prop('disabled', false).text('立即支付');
                alert('网络错误，请稍后重试');
            });
        });

        // 支付方式切换
        function adSwitchQr(method) {
            var src = adOrder ? (method === 'wechat' ? adOrder.wechat : adOrder.alipay) : '';
            if (src) {
                $('#ad_qr_img').attr('src', src).show();
                $('#ad_qr_empty').hide();
            } else {
                $('#ad_qr_img').hide();
                $('#ad_qr_empty').show();
            }
        }
        $('.ad-pay-tab').on('click', function() {
            $('.ad-pay-tab').removeClass('active');
            $(this).addClass('active');
            adSwitchQr($(this).data('method'));
        });

        // 确认已支付
        $('#ad-paid-btn').on('click', function() {
            if (!adOrder) return;
            var method = $('.ad-pay-tab.active').data('method');
            var $btn = $(this);
            $btn.prop('disabled', true).text('提交中...');
            $.post(theme_data.ajaxurl, {
                action: 'theme_ad_paid',
                nonce: theme_data.nonce,
                order_id: adOrder.order_id,
                pay_method: method
            }, function(res) {
                $btn.prop('disabled', false).text('我已完成支付');
                if (res && res.success) {
                    adShowPanel('done');
                } else {
                    alert((res && res.data && res.data.msg) || '提交失败，请稍后重试');
                }
            }).fail(function() {
                $btn.prop('disabled', false).text('我已完成支付');
                alert('网络错误，请稍后重试');
            });
        });

        $(document).on('click', '.ad-back-form', function() { adShowPanel('form'); });
    }
});
</script>
</body>
</html>
